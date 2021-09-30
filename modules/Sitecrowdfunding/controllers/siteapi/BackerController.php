<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BackerController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_BackerController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        //LOGGED IN USER VALIDATON 
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //SET SUBJECT
        $project_id = $this->_getParam('project_id', null);
        if ($project_id) {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            if ($project && !Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
                Engine_Api::_()->core()->setSubject($project);
            }
        }
        //END - SET SUBJECT 
    }

    /*
     * Get All Rewards
     * Return Json
     */

    public function rewardSelectionAction() {

        $jsonRewardData = array();
        $response = array();
        $this->validateRequestMethod();
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError('no_record');
        }
        //IF THE BACKING IS DONATION TYPE THEN THIS PARAMETER WILL COME TRUE
        $donationType = $this->_getParam('donationType', false);
        $project = Engine_Api::_()->core()->getSubject();
        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));


        if ($project->isExpired()) {
            $this->respondWithError('unauthorized', "Project is closed");
        } elseif (empty($project->is_gateway_configured)) {
            $this->respondWithError('unauthorized', "Please configure Payment method first");
        } elseif ($project->status != 'active') {
            $this->respondWithError('unauthorized', "Project is not publish right now.");
        } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
            $this->respondWithError('unauthorized', "Project is not started yet.");
        }

        $project_id = $project->project_id;
        try {
            $rewards = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewards($project_id);
            $rewardCount = count($rewards);
            $jsonRewardData['donationType'] = $donationType;
            if ($rewardCount > 0) {
                $response['rewards_form'][] = array(
                    "name" => "reward_0",
                    "type" => "Radio",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Back this project without a reward."),
                );

                $response['rewards_form'][] = array(
                    "name" => "reward_1",
                    "type" => "Radio",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Back this project with a reward."),
                );
            } else {
                $response['rewards_form'][] = array(
                    "name" => "reward_0",
                    "type" => "Radio",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Back this project without a reward."),
                );
            }
            foreach ($rewards as $reward) {
                $browseReward = $reward->toArray();
                $shippingCharge = Engine_Api::_()->getDbTable('rewardshippinglocations', 'sitecrowdfunding')->findShippingLocation($project->project_id, $reward->reward_id);
                $browseReward['shipping_amt'] = $pledge_amount = $reward->pledge_amount + $shippingCharge;

                $browseReward['pledge_amount'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($pledge_amount);
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($reward);
                $browseReward['delivery_date'] = date('F Y', strtotime($reward->delivery_date));
                if ($reward->quantity) {
                    $quantity = $reward->quantity;
                    $browseReward['remaining_reward'] = $quantity - $reward->spendRewardQuantity();
                } else {
                    $browseReward['remaining_reward'] = "Unlimited";
                }

                if (!empty($getContentImages))
                    $browseReward = array_merge($browseReward, $getContentImages);

                $countries = array();
                foreach ($reward->getAllCountries() as $country) {
                    $name = empty($country->country) ? $this->translate('Rest of the World') : $country->country_name;
                    $regionId = empty($country->region_id) ? 0 : $country->region_id;
                    $shipping_label = $name . "(+$country->amount)";
                    $countries[$regionId] = $shipping_label;
                    $browseReward['region_id'][$regionId] = $reward->pledge_amount + $country->amount;
                    $browseReward['shipping_amt'] = $reward->pledge_amount;
                    $default_region = $regionId;
                }
                if (count($countries) > 0) {
                    $browseReward['form'][] = array(
                        "name" => "region_id",
                        "type" => "Select",
                        "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Country"),
                        'multiOptions' => $countries,
                        'value' => $default_region
                    );

                    $browseReward['form'][] = array(
                        "name" => "shipping_amt",
                        "type" => "Text",
                        "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Back Amount"),
                        'value' => $browseReward['shipping_amt']
                    );
                }



                $response['reward_1'][] = $browseReward;
            }

            $response['reward_0'][] = array(
                "name" => "shipping_amt",
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Back Amount"),
            );
            $response['totalItemCount'] = $rewardCount;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    //and of select reward..........................................

    /*
     * Validate Reward
     * Return Json
     */
    
    public function checkRewardSelection($params = array()) {

        $project = Engine_Api::_()->core()->getSubject();
        if (!$project) {
            $this->respondWithError('unauthorized', "Please select the project");
        }
        $project_id = $project->project_id;
        if (!isset($params['region_id'])) {
            $region_id = "";
        } else {
            $region_id = $params['region_id'];
        }

        if (!isset($params['reward_id'])) {
            $rewardId = 0;
        } else {
            $rewardId = $params['reward_id'];
        }


        $pledgeAmount = $params['shipping_amt'];
        $rewardModel = Engine_Api::_()->getItem('sitecrowdfunding_reward', $rewardId);
        if (!is_numeric($pledgeAmount) || $pledgeAmount <= 0) {
            $this->respondWithError('unauthorized', 'Please enter a valid Back amount.');
        }
        $totalA = 0;
        if ($rewardModel) {

            foreach ($rewardModel->getAllCountries() as $country) {
                $regionId = empty($country->region_id) ? 0 : $country->region_id;
                if ($regionId == $region_id) {
                    $totalA = $rewardModel->pledge_amount + $country->amount;
                    break;
                }
            }


            if ($pledgeAmount < $totalA) {
                $error_message = "Please enter the Back amount greater than or equal to Reward’s Back amount i.e. $totalA";
                $this->respondWithError('unauthorized', $error_message);
            }
        }

        $cartData = array();
        $cartData['region_id'] = isset($region_id) ?$region_id:"" ;
        $cartData['pledge_amount'] = $pledgeAmount;
        $cartData['reward_id'] = $rewardId;
        $cartData['project_id'] = $project_id;
        return $cartData;
    }

    /*
     * Get All payment method which configure with this project.
     * Return Json
     */

    public function checkoutAction() {
        $values = $_REQUEST;
        try {
            $data = $this->checkRewardSelection($values);
            $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $data['reward_id']);
            $project = Engine_Api::_()->core()->getSubject();
            $project_id = $project->project_id;
            $currentDate = date('Y-m-d');
            $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
            if ($project->isExpired()) {
                $this->respondWithError('unauthorized', "Project is closed");
            } elseif (empty($project->is_gateway_configured)) {
                $this->respondWithError('unauthorized', "Please configure Payment method first");
            } elseif ($project->status != 'active') {
                $this->respondWithError('unauthorized', "Project is not publish right now.");
            } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
                $this->respondWithError('unauthorized', "Project is not started yet.");
            }
            // MANAGE COMPLETE CHECKOUT PROCESS
            $checkout_process = array();
            $finalProjectEnableGateway = array();
            $projectEnabledgateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getEnabledGateways($project_id);
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
            $allowedPaymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
            $isPaymentToSite = false;
            if ($allowedPaymentMethod == 'split') {
                $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
            } elseif ($allowedPaymentMethod == 'escrow') {
                $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
            } else {
                if (empty($isPaymentToSiteEnable)) {
                    $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
                } else {
                    $isPaymentToSite = true;
                }
            }
            if ($isPaymentToSite == true) {
                $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                $enable_gateway = $gateway_table->select()
                        ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                        ->where('enabled = 1')
                        ->where('plugin not in (?)', array('Sitegateway_Plugin_Gateway_PayPalAdaptive', 'Sitegateway_Plugin_Gateway_MangoPay'))
                        ->query()
                        ->fetchAll();

                // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
                if (empty($enable_gateway)) {
                    $no_payment_gateway_enable = true;
                }
                $payment_gateway = $enable_gateway;
            } else {
                if (!empty($projectEnabledgateway)) {
                    foreach ($projectEnabledgateway as $enbGatewayName) {
                        if (in_array(strtolower($enbGatewayName->title), $siteAdminEnablePaymentGateway)) {
                            $finalProjectEnableGateway[] = $enbGatewayName;
                        }
                    }
                }
                $payment_gateway = $finalProjectEnableGateway;
                // IF NO PAYMENT GATEWAY ENABLE
                if (empty($projectEnabledgateway) || empty($finalProjectEnableGateway)) {
                    $no_payment_gateway_enable = true;
                }
            }

            if (!empty($no_payment_gateway_enable)) {
                $error_message = $this->translate("Site admin has not configured or enabled the payment gateways yet. Please, contact site admin to configure and enable payment gateways.");
                $this->respondWithError('unauthorized', $error_message);
            }
            $multiOptions = array();
            foreach ($payment_gateway as $payment_method) {
                if (!isset($payment_method['plugin'])) {
                    continue;
                }
                $pluginName = $payment_method['plugin'];
                $paymentGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway($pluginName);
                if (!$paymentGateway) {
                    continue;
                }
                $multiOptions[$paymentGateway->gateway_id] = ucfirst($paymentGateway->title);
                $otherPaymentGateways[] = $paymentGatewayId = $paymentGateway->gateway_id;
            }
            $form = array();
            if (!empty($reward))
                $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->shippingAdressForm($data);

            $form[] = array(
                "name" => "payment_method",
                "type" => "Dummy",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Payment Method'),
            );

            $form[] = array(
                'type' => 'radio',
                'label' => $this->translate('Select Payment Gateway'),
                'name' => 'payment_gateway',
                'multiOptions' => $multiOptions,
            );

            $form[] = array(
                'name' => 'isPrivateBacking',
                'label' => $this->translate(" Make my purchase private. "),
                'type' => 'Checkbox',
                'value0' => 0
            );

            $form[] = array(
                'type' => 'Button',
                'name' => 'Continue',
                'label' => $this->translate("Continue"),
            );

            $response = array();
            $response['form'] = $form;
            $response['response']['data'] = Zend_Json::encode($data);
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Place order and redirected to Payment gatway .
     * Return Json
     */
    public function placeOrderAction() {

        $directPayment = 0;
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        if (empty($isPaymentToSiteEnable)) {
            $directPayment = 1;
        }
        try {


            $data = $this->_getParam('data', null);
            $paymentGatewayId = $this->_getParam('payment_gateway');
            if (empty($data)) {
                $this->respondWithError('unauthorized', "Back amount not found");
            }
            $data = preg_replace('/\\\"/', "\"", $data);
            if (!empty($data))
                $data = Zend_Json::decode($data);

            // GET VIEWER
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            $project_id = $this->_getParam('project_id', null);

            $backer_table = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
        // PROCESS
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        // GET IP ADDRESS
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        try {
            //check if value of grandtotal is 0
            if (empty($data['pledge_amount'])) {
                $this->respondWithError('unauthorized', "Back amount not found");
            }
            //SAVE Backers DETAILS IN Backer TABLE.
            $shippingDetail = $_REQUEST;
            $table = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
            $backer = $table->createRow();
            $backer->user_id = $viewer_id;
            $backer->project_id = $project_id;
            $backer->order_status = 1;
            $backer->payment_status = 'initial';
            $backer->creation_date = date('Y-m-d H:i:s');
            $backer->gateway_id = $paymentGatewayId;
            $backer->ip_address = $ipExpr;
            $backer->gateway_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
            $backer->amount = $data['pledge_amount'];
            $backer->direct_payment = $directPayment;
            $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $data['reward_id']);
            //CHECKING FOR REWARD SHIPPING ADDRESS
            if (!empty($reward)) {
                $backer->shipping_country = $regionId = $data['region_id'];
                $backer->shipping_address1 = $shippingDetail['address1'];
                $backer->shipping_address2 = $shippingDetail['address2'];
                $backer->shipping_city = $shippingDetail['city'];
                $backer->shipping_zip = $shippingDetail['postal_code'];

                //FIND SHIPPING LOCATION
                if (!empty($regionId)) {
                    $shippingLocation = Engine_Api::_()->getDbTable('rewardshippinglocations', 'sitecrowdfunding')->findShippingLocation($reward->reward_id, $regionId);
                    if ($shippingLocation) {
                       // $this->respondWithError('unauthorized', "Shipping location is not found");
                        $backer->shipping_price = $shippingLocation['amount'];
                    }
                }
                //Set shipping price
                //$backer->shipping_price = $shippingLocation['amount'];
            }

            $backer->is_private_backing = empty($_POST['isPrivateBacking']) ? 0 : 1;

            if (!empty($reward)) {
                $backer->delivery_date = $reward->delivery_date;
            }
            //COMMISSION WORK
            $commission = Engine_Api::_()->sitecrowdfunding()->getCommission($project_id);
            $commission_type = $commission[0];
            $commission_rate = $commission[1];
            // IF COMMISSION VALUE IS FIX.
            if ($commission_type == 0)
                $commission_value = $commission_rate;
            else
                $commission_value = (@round($data['pledge_amount'], 2) * $commission_rate) / 100;
            $backer->commission_type = $commission_type;
            $backer->commission_value = $commission_value;
            $backer->commission_rate = $commission_rate;
            $backer->save();
            // COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $backer_id = Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($backer->backer_id);

        $gateway_id = $paymentGatewayId;

        $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");
        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
        $slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.slugplural', 'projects');

        $url = $getHost . '/' . $baseUrl . '/' . $slug_plural . '/backer/payment';

        $url .= "?project_id=" . $project_id . "&gateway_id=" . $paymentGatewayId . "&backer_id=" . $backer_id;
        $this->respondWithSuccess(array('payment_url' => $url), false);
    }

    public function backerFaqAction() {
        $coreSetting = Engine_Api::_()->getApi('settings', 'core');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.projectownerfaq.enabled', 1)) {
            $this->respondWithError('unauthorized');
        }
        $response[] = array(
            "question" => "1. How can I found interesting projects as per my preference? \n",
            "answer" => "To find interesting projects as per your preference follow below steps:\na) Go to ‘Browse Projects’ page.\nb) Enter the criteria in the search form as per your preference.\nc) You can go through the projects as per the searched criteria."
        );
        $response[] = array(
            "question" => "2. How can I back a project? \n",
            "answer" => "You can back a project in two ways:\na)  Back Button: Click on back button and you will be re-directed to the page with all the list of rewards and an option to back any amount to the project.\nb)Reward Selection: choose the reward listed on the project profile page and you will be redirected to the page where that reward is pre-selected.\n\nNext step is to fill your delivery address and pay for the back amount using available payment options."
        );

        $response[] = array(
            "question" => "3. Can I back a project more than once? \n",
            "answer" => "Yes, you can back a project more than once."
        );
        $response[] = array(
            "question" => "4. How can I contact Project Owner for any queries related to his project? \n",
            "answer" => "Amount backed can be refunded back or not is entirely dependent on the project owner and site admin. So, in case of any refund, please contact project owner or site admin."
        );
        $response[] = array(
            "question" => "5. Is it possible to get refund of the amount I have backed for a project?\n",
            "answer" => "Amount backed can be refunded back or not is entirely dependent on the project owner and site admin. So, in case of any refund, please contact project owner or site admin."
        );
        $response[] = array(
            "question" => "6. Do I get notified if a project I have backed succeeds or not?\n",
            "answer" => "Yes, you will be notified about the success and failure of the project which you have backed."
        );
        $response[] = array(
            "question" => "7. Is my pledge amount publicly displayed?\n",
            "answer" => "This depends entirely on you. You can make your contribution anonymous while backing the project."
        );
        $response[] = array(
            "question" => "8.How can I know in detail about the project owner?\n",
            "answer" => "You can see the full biography of the project owner by clicking on the ‘Full Bio’ button present on the project profile page. Here, you can also the link of other social media profile of the project owner like: Facebook, Twitter, LinkedIn, Google Plus etc."
        );

        $response[] = array(
            "question" => "9. Where can I keep track of my backed details related to various projects? \n",
            "answer" => "You can keep track of your backed details related to various projects from ‘My Projects’ section. You can also print invoice of the backing details from here.
"
        );

        $response[] = array(
            "question" => "10. Will I receive the invoice for my backed amount? \n",
            "answer" => "Yes, you will receive the invoice for you backed amount on your registered email address. You can also print invoice of the backing details from ‘My Projects’ section."
        );
        $response[] = array(
            "question" => "11. How do I know when rewards for a project will be delivered? \n",
            "answer" => "Projects have an Estimated Delivery Date under each reward on the project page. You can view the Estimated Delivery Date either on the project profile page. This date is entered by project owners as their best guess for delivery to backers."
        );

        $response[] = array(
            "question" => "12. I haven't gotten my reward yet. What do I do? \n",
            "answer" => "The first step is checking the Estimated Delivery Date on the project page. Backing a project is a lot different than simply ordering a product online, and sometimes projects are in very early stages when they are funded.\nIf the Estimated Delivery Date has passed, check for project updates that may explain what happened. Sometimes project owners hit unexpected roadblocks, or simply underestimate how much work it takes to complete a project. PRoject owners are expected to communicate these setbacks when they happen.\nIf the project owner hasn’t posted any update, send them a direct message to request more information about their progress, or post a public comment on their project asking for a status update."
        );

        $body = array();
        $body['response'] = $response;

        $body['title'] = $coreSetting->getSetting('sitecrowdfunding.projectownerfaq.title', 'FAQs for Project Owner');
    }
    
    
    public function viewBackedDetailsAction() {
		$project_id = $this->_getParam('project_id');
		if (empty($project_id)) {
			$this->respondWithError('no_record');
		}

		$project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
		$viewer = Engine_Api::_()->user()->getViewer();

		$results = $project->getBackingDetailForLoginUser();
                foreach($results as $backer){
                     $backInfo=array();
                     $user = Engine_Api::_()->user()->getUser($backer->user_id);
                    $backInfo['Backer ID'] = $backer->backer_id;
                    $backInfo['ownerTitle'] = $user->getTitle();
                    if((((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '3.5') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '2.6.1'))){
                        $backInfo['Backed Amount'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($backer->amount,1);
                    }
                    else {
                        $backInfo['Backed Amount'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); 
                    }  
                    $backInfo['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                    $backInfo['Backing Date'] = $backer->creation_date;
                    $date = new DateTime($backInfo['Backing Date']);
                   $backInfo['Backing Date'] =  $date->format('F, d Y H:i:s');
                    if ($backer->reward_id && ($reward->shipping_method == 2 || $reward->shipping_method == 3)){
                      $backInfo['Backed Amount'] =  $backInfo['Backed Amount']. " ".$this->translate("(Shipping Cost Included)"); 
                    }
                    if ($backer->reward_id) {
                $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);
            }
            if (!empty($reward)){
                 $backInfo['Reward Selected'] = $this->translate($reward->getTitle());
            }
            else{
                $backInfo['Reward Selected'] = $this->translate("No Reward Selected");
            }
            $response['response'][] = $backInfo;
           
                    
                }
                
                 $this->respondWithSuccess($response, true);
	}

}

?>