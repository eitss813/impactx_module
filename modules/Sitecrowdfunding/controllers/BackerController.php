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

class Sitecrowdfunding_BackerController extends Core_Controller_Action_Standard {

    /**
     * @var User_Model_User
     */
    protected $_user;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Payment_Model_Order
     */
    protected $_order;

    /**
     * @var Payment_Model_Gateway
     */
    protected $_gateway;

    /**
     * @var Sitecrowdfunding_Model_Project
     */
    protected $_project;

    /**
     * @var Payment_Model_Package
     */
    protected $_package;
    protected $_success;

	public function init() {

		//LOGGED IN USER VALIDATON
		//if (!$this->_helper->requireUser()->isValid()) {
		//	return;
	//	}

		//SET SUBJECT
		$project_id = $this->_getParam('project_id', null);
		if ($project_id) {
			$this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
			if ($project && !Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
				Engine_Api::_()->core()->setSubject($project);
			}
		}
		//END - SET SUBJECT
	}

    public function checkRewardSelectionAction() {
        $data = array('return' => 0, 'message' => 'Invalid action Performed');
        if (!$this->getRequest()->isPost()) {
            return $this->_helper->json($data);
        }
        $project = Engine_Api::_()->core()->getSubject();
        if (!$project) {
            $data['message'] = "Please select the project";
            return $this->_helper->json($data);
        }
        $this->view->project_id = $project_id = $project->project_id;
        $rewardId = $this->_getParam('reward_id', null);
        $country = $this->_getParam('country');
        $message = $this->_getParam('message');
        $this->view->reward_id = $rewardId = is_null($rewardId) ? $rewardId : (int) $rewardId;
        $this->view->pledge_amount = $pledgeAmount = $this->_getParam('pledge_amount', 0);
        $shippingAmount = $this->_getParam('shipping_amt', 0);
        $this->view->message = "";
        $rewardModel = Engine_Api::_()->getItem('sitecrowdfunding_reward', $rewardId);
        $is_error = false;
        if (!is_numeric($pledgeAmount) || $pledgeAmount <= 0) {
            $data['message'] = $this->view->translate('Please enter a valid Back amount.');
            $is_error = true;
        }
        if ($rewardModel) {
            $totalA = $rewardModel->pledge_amount + $shippingAmount;
            if ($pledgeAmount < $totalA) {
                $data['message'] = $this->view->translate('Please enter the Back amount greater than or equal to Reward’s Back amount i.e. %s', $totalA);
                $is_error = true;
            }
        }
        if ($is_error) {
            return $this->_helper->json($data);
        }
        $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
        $session->country = $country;
        $session->pledge_amount = $pledgeAmount;
        $session->reward_id = $rewardId;
        $session->project_id = $project_id;
        $session->message = $message;
        $data['return'] = 1;
        $data['message'] = "Successful";
        return $this->_helper->json($data);
    }

    /*
             * Checkout Process
             * Step1 : Select the reward
             * Step2 : Enter the Billing Address
             * Step3 : Go for Checkout
    */

    public function rewardSelectionAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('notfound', 'error', 'core');
        }
        //IF THE BACKING IS DONATION TYPE THEN THIS PARAMETER WILL COME TRUE
        $this->view->donationType = $donationType = $this->_getParam('donationType', false);
        $request = $this->getRequest();
        //PREVIOUS PAGE URL
        $sourceUrl = $request->getHeader('referer');
        $project = Engine_Api::_()->core()->getSubject();
        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
        if ($project->isExpired()) {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif (empty($project->is_gateway_configured)) {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif ($project->status != 'active') {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        $this->view->project_id = $project_id = $project->project_id;
        $this->view->rewards = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewards($project_id);
        $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
        $session->donationType = $donationType;
        $session->sourceUrl = $sourceUrl;
        if (isset($session->reward_id) || $this->_getParam('reward_id', null)) {
            $this->view->reward_id = $session->reward_id;
            $this->view->pledge_amount = $session->pledge_amount;
            $this->view->country_selected = $session->country;
            $this->view->message = $session->message;
            if ($this->_getParam('reward_id', null)) {
                $reward_id = $this->_getParam('reward_id', null);
                $this->view->reward_id = $this->getParam('reward_id');
                //WE GIVES FIRST COUNTRY SHIPPING ADDED IN PLEDGE AMOUNT
                $shippingCharge = Engine_Api::_()->getDbTable('rewardshippinglocations', 'sitecrowdfunding')->findShippingLocation($project->project_id, $reward_id);
                $this->view->pledge_amount = $reward->pledge_amount + $shippingCharge;
            }
        }
        $this->_helper->content
            //->setNoRender()
            ->setEnabled();
    }

    // created only for donating to project
    public function donateToProjectAction() {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        //  $id = $this->_getParam('user_id');
        $email = $this->_getParam('email',null);
        $username = $this->_getParam('username',null);
        $oauth_consumer_key = $this->_getParam('oauth_consumer_key',null);
        $oauth_consumer_secret = $this->_getParam('oauth_consumer_secret',null);
        $baseUrl = _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        if($email)
            $id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $email)->query()->fetchColumn();

        if(!$id) {

            //curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $post = array(
                'email'=>$email,
                'password'=>'Welcome123_',
                'passconf'=>'Welcome123_',
                'username'=>$username,
                'timezone'=>'US/Mountain',
                'language'=>'en',
                'terms'=>'1',
                'ip'=>'103.53.52.50'

            );
            $headers = array(
                'Content-Type: multipart/form-data',
                'oauth_consumer_key: '.$oauth_consumer_key,
                'oauth_consumer_secret: '.$oauth_consumer_secret);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$baseUrl."/api/rest/signup");

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);

            curl_close ($ch);


            $server_output_json = json_decode($server_output);
            if($server_output_json->status_code == '200') {
                $server_output_user = $server_output_json->body;
                $user_details = json_decode(json_encode($server_output_user->user));
                $user_id= json_decode(json_encode($user_details->user_id));
                $id = $user_id;
                //print_r($user_id);
            }


        }

        $user = Engine_Api::_()->getItem('user', $id);
        if($id && $user && $user->getIdentity()) {
            // Login
            Zend_Auth::getInstance()->getStorage()->write($user->getIdentity());
            header("Cache-Control: no-cache");
        }

        $this->view->status = true;
        // Redirect
        if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
            //  return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            //  $this->view->status = true;
            //   return;
        }


        // check project
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //IF THE BACKING IS DONATION TYPE THEN THIS PARAMETER WILL COME TRUE
        $this->view->donationType = $donationType = $this->_getParam('donationType', false);

        $request = $this->getRequest();

        //PREVIOUS PAGE URL
        $this->view->sourceUrl = $sourceUrl = $request->getHeader('referer');
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $this->view->owner = $owner = Engine_Api::_()->user()->getUser($project->owner_id);
        $this->view->project_id = $project_id = $project->project_id;

        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));

        if ($project->isExpired()) {
        //    return $this->_forward('requireauth', 'error', 'core');
        } elseif (empty($project->is_gateway_configured)) {
          //  return $this->_forward('requireauth', 'error', 'core');
        } elseif ($project->status != 'active') {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        /****** organisation ******/
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if(empty($parentOrganization)){
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }
        $this->view->parentOrganization = $parentOrganization;

        /****** initiative ******/
        $projectTags = $project->tags()->getTagMaps();
        $tagString =  array();
        foreach ($projectTags as $tagmap) {
            $tagString[]= $tagmap->getTag()->getTitle();
        }
        if(!empty($project->initiative_id)){
            $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id);
        }else{
            if(count($tagString) > 0){
                $initiatives = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'],$tagString);
                if(count($initiatives) > 0){
                    $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiatives[0]['initiative_id']);
                }else{
                    $this->view->initiative = null;
                }
            }else{
                $this->view->initiative = null;
            }
        }

        /****** get payment gateway ******/
        $checkout_process = array();
        $finalProjectEnableGateway = array();
        $projectEnabledgateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getEnabledGateways($project_id);
        $this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
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
                ->where('plugin not in (?)', array('Sitegateway_Plugin_Gateway_MangoPay'))
                ->query()
                ->fetchAll();

            // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
            if (empty($enable_gateway)) {
                $no_payment_gateway_enable = true;
            }
            $this->view->payment_gateway = $enable_gateway;
        } else {
            if (!empty($projectEnabledgateway)) {
                foreach ($projectEnabledgateway as $enbGatewayName) {
                    if (in_array(strtolower($enbGatewayName->title), $siteAdminEnablePaymentGateway)) {
                        $finalProjectEnableGateway[] = $enbGatewayName;
                    }
                }
            }
            $this->view->payment_gateway = $finalProjectEnableGateway;
            // IF NO PAYMENT GATEWAY ENABLE
            if (empty($projectEnabledgateway) || empty($finalProjectEnableGateway)) {
                $no_payment_gateway_enable = true;
            }
        }

        if (!empty($no_payment_gateway_enable)) {
            $this->view->sitecrowdfunding_checkout_no_payment_gateway_enable = true;
            return;
        }

        // show action label
        $donateLabel = null;

        if ($parentOrganization['page_id']) {

            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);

            if ($project->initiative_id) {

                $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id);

                /** payment_action_label **/
                // if all present, then use project
                if ($initiative->payment_action_label && $project->payment_action_label && $sitepage->payment_action_label) {
                    $donateLabel = $project->payment_action_label;
                } // if initiative not present and project present, then use project label
                elseif (!$initiative->payment_action_label && $project->payment_action_label && ($sitepage->payment_action_label || !$sitepage->payment_action_label)) {
                    $donateLabel = $project->payment_action_label;
                } // if initiative present and project not present, then update initiative into project
                elseif ($initiative->payment_action_label && !$project->payment_action_label && ($sitepage->payment_action_label || !$sitepage->payment_action_label)) {
                    $donateLabel = $initiative->payment_action_label;
                } // if both initiative and project not present, then use sitepage label
                elseif (!$initiative->payment_action_label && !$project->payment_action_label && ($sitepage->payment_action_label || !$sitepage->payment_action_label)) {
                    $donateLabel = $sitepage->payment_action_label;
                } // use sitepage
                else {
                    $donateLabel = $sitepage->payment_action_label;
                }

            } else {

                /** payment_action_label **/
                // if sitepage present and project present, then use project label
                if ($sitepage->payment_action_label && $project->payment_action_label) {
                    // print_r(208);
                    $donateLabel = $project->payment_action_label;
                } // if initiative not present and project present, then use project label
                elseif (!$sitepage->payment_action_label && $project->payment_action_label) {
                    // print_r(213);
                    $donateLabel = $project->payment_action_label;
                } // if sitepage present and project not present, then update sitepage into project
                elseif ($sitepage->payment_action_label && !$project->payment_action_label) {
                    // print_r(218);
                    $donateLabel = $sitepage->payment_action_label;
                } // use sitepage
                else {
                    // print_r(223);
                    $donateLabel = $sitepage->payment_action_label;
                }
            }
        } else {
            $donateLabel = $project->payment_action_label;
        }

        if ($project->payment_action_label != $donateLabel) {
            $this->view->payment_action_label = $donateLabel;
        } else {
            $this->view->payment_action_label = $project->payment_action_label;
        }

    }

    public function saveCartDataAsSessionAction() {
        $data = array('return' => 0, 'message' => 'Invalid action Performed');

        $project_id = $this->_getParam('project_id');
        $rewardId = $this->_getParam('reward_id');
        $country = $this->_getParam('country');
        $message = $this->_getParam('message');
        $pledgeAmount = $this->_getParam('pledge_amount');
        $donationType = $this->_getParam('donationType');
        $shippingAmount = $this->_getParam('shipping_amt');

        $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
        $session->country = $country;
        $session->pledge_amount = $pledgeAmount;
        $session->donationType = $donationType;
        $session->reward_id = $rewardId;
        $session->project_id = $project_id;
        $session->message = $message;

        $data['return'] = 1;
        $data['message'] = "Successful";

        return $this->_helper->json($data);
    }

    //ACTION TO DISPLAY CHECKOUT PAGE (BACK ON PROJECT)
    public function checkoutAction() {

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
        $this->view->session = $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
        if (!$session) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $session->reward_id);
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $this->view->project_id = $project_id = $project->project_id;
        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
        if ($project->isExpired()) {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif (empty($project->is_gateway_configured)) {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif ($project->status != 'active') {
            return $this->_forward('requireauth', 'error', 'core');
        } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // MANAGE COMPLETE CHECKOUT PROCESS
        $checkout_process = array();
        $finalProjectEnableGateway = array();
        $projectEnabledgateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getEnabledGateways($project_id);
        $this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
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
                ->where('plugin not in (?)', array('Sitegateway_Plugin_Gateway_MangoPay'))
                ->query()
                ->fetchAll();

            // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
            if (empty($enable_gateway)) {
                $no_payment_gateway_enable = true;
            }
            $this->view->payment_gateway = $enable_gateway;
        } else {
            if (!empty($projectEnabledgateway)) {
                foreach ($projectEnabledgateway as $enbGatewayName) {
                    if (in_array(strtolower($enbGatewayName->title), $siteAdminEnablePaymentGateway)) {
                        $finalProjectEnableGateway[] = $enbGatewayName;
                    }
                }
            }
            $this->view->payment_gateway = $finalProjectEnableGateway;
            // IF NO PAYMENT GATEWAY ENABLE
            if (empty($projectEnabledgateway) || empty($finalProjectEnableGateway)) {
                $no_payment_gateway_enable = true;
            }
        }

        if (!empty($no_payment_gateway_enable)) {
            $this->view->sitecrowdfunding_checkout_no_payment_gateway_enable = true;
            return;
        }
    }

    public function placeOrderAction() {

        $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
        if (!$session) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $directPayment = 0;
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        if (empty($isPaymentToSiteEnable)) {
            $directPayment = 1;
        }
        $total = $session->pledge_amount;
        $message = $session->message;
        // GET VIEWER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $checkout_process = @unserialize($_POST['checkout_process']);
        if (!empty($_POST['param'])) {
            $payment_info = array();
            $payment_information = array();
            $payment_info = @explode(',', $_POST['param']);

            if (($payment_info[0] != 3)) {
                $payment_information['method'] = $payment_info[0];
            }
            $checkout_process['payment_information'] = $payment_information;
        }
        $backer_table = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');

        // PROCESS
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // GET IP ADDRESS
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        try {
            //check if value of grandtotal is 0
            if (empty($total)) {
                return $this->_forward('notfound', 'error', 'core');
            }
            //SAVE Backers DETAILS IN Backer TABLE.

            $table = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
            $backer = $table->createRow();
            $backer->user_id = $viewer_id;
            $backer->project_id = $project_id;
            $backer->order_status = 1;
            $backer->payment_status = 'initial';
            $backer->creation_date = date('Y-m-d H:i:s');
            $backer->gateway_id = $checkout_process['payment_information']['method'];
            $backer->ip_address = $ipExpr;
            $backer->gateway_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
            $backer->amount = $total;
            $backer->custom_message = $message;
            $backer->direct_payment = $directPayment;
            $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $session->reward_id);
            //CHECKING FOR REWARD SHIPPING ADDRESS
            if (isset($_POST['formValues']) && !empty($_POST['formValues']) && !empty($reward)) {

                @parse_str($_POST['formValues'], $shippingDetail);
                $backer->shipping_country = $regionId = $shippingDetail['regionId'];
                $backer->shipping_address1 = $shippingDetail['address1'];
                $backer->shipping_address2 = $shippingDetail['address2'];
                $backer->shipping_city = $shippingDetail['city'];
                $backer->shipping_zip = $shippingDetail['postal_code'];

                //FIND SHIPPING LOCATION
                $shippingLocation = Engine_Api::_()->getDbTable('rewardshippinglocations', 'sitecrowdfunding')->findShippingLocation($reward->reward_id, $regionId);
                if (!$shippingLocation) {
                    return $this->_forward('notfound', 'error', 'core');
                }
                //Set shipping price
                $backer->shipping_price = $shippingLocation['amount'];
            }

            $backer->is_private_backing = $_POST['isPrivateBacking'];

            if (!empty($reward)) {
                $backer->delivery_date = $reward->delivery_date;
            }
            //COMMISSION WORK
            $commission = Engine_Api::_()->sitecrowdfunding()->getCommission($project_id);
            $commission_type = $commission[0];
            $commission_rate = $commission[1];

            // IF COMMISSION VALUE IS FIX.
            if ($commission_type == 0):
                $commission_value = $commission_rate;
            else:
                $commission_value = (@round($total, 2) * $commission_rate) / 100;
            endif;
            $backer->commission_type = $commission_type;
            $backer->commission_value = $commission_value;
            $backer->commission_rate = $commission_rate;
            $backer->save();
            // COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->backer_id = Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($backer->backer_id);

        $this->view->gateway_id = $checkout_process['payment_information']['method'];
    }

    public function paymentAction() {
        $gateway_id = $this->_getParam('gateway_id');
        //PAYMENT FLOW CHECK
        $project_id = $this->_getParam('project_id', null);
        $backer_id = (int) Engine_Api::_()->sitecrowdfunding()->getEncodeToDecode($this->_getParam('backer_id'));
        if (empty($gateway_id) || empty($backer_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->_session = new Zend_Session_Namespace('Payment_Sitecrowdfunding');
        $this->_session->unsetAll();
        $this->_session->user_order_id = $backer_id;
        $this->_session->checkout_project_id = $project_id;
        return $this->_forward('process', 'backer-payment', 'sitecrowdfunding', array());
    }

    public function successAction() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $session = new Zend_Session_Namespace('Sitecrowdfunding_Backer_Payment_Detail');
        if ($this->_getParam('success_id')) {
            $backer_id = (int) Engine_Api::_()->sitecrowdfunding()->getEncodeToDecode($this->_getParam('success_id'));
            $state = $error = '';
        } else {
            if (empty($session->sitecrowdfundingBackerPaymentDetail['success_id'])) {
                return $this->_forward('notfound', 'error', 'core');
            }

            $backer_id = $session->sitecrowdfundingBackerPaymentDetail['success_id'];
            $this->view->state = $state = $session->sitecrowdfundingBackerPaymentDetail['state'];
            $this->view->error = $error = $session->sitecrowdfundingBackerPaymentDetail['errorMessage'];
        }
        $this->view->donationType = $donationType = $session->sitecrowdfundingBackerPaymentDetail['donationType'];
        $this->view->sourceUrl = $sourceUrl = $session->sitecrowdfundingBackerPaymentDetail['sourceUrl'];
        $this->view->backer_id = $backer_id;
        $this->view->paymentMethod = $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        $backer_obj = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);

        //PREAPPROVAL WILL BE THERE IF THIS IS PAYPALADAPTIVE
        $gateway = Engine_Api::_()->getItem('payment_gateway', $backer_obj->gateway_id);
        $this->view->gatewayName = $gatewayName = $gateway->title;

        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $backer_obj->project_id);


        if (empty($backer_id) || empty($backer_obj)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $success_message = '<h2>' . $this->view->translate("Thanks for backing the project!") . '</h2>';
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyUser = $project->getOwner();
        $subject = $project;

        //Add parameters for email notification
        $params = array();
        $params['project_name'] = $project_name = $project->title;
        $params['member_name'] = $member_name = $notifyUser->getTitle();
        $params['user_name'] = $user_name = $viewer->getTitle();
        $host = $_SERVER['HTTP_HOST'];
        $params['profile_link'] = $profile_link = $view->htmlLink($host . $viewer->getHref(), $viewer->getTitle());
        $params['project_link'] = $project_link = $view->htmlLink($host . $project->getHref(), $project->title);
        $params['goal_amount'] = $goal_amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($project->goal_amount);
        $profile_name = $viewer->displayname;

        if ($backer_obj->payment_status == 'active' && empty($error)) {
            $paymentStatus = true;
            $backerEmailType = 'SITECROWDFUNDING_BACKER_PAYMENT_CONFIRMATION_EMAIL';
            $type = "sitecrowdfunding_back";
        } else {
            $paymentStatus = false;
        }

        // IF PAYMENT IS SUCCESSFULLY DONE FOR THE ORDER
        if ($paymentStatus && $session->sitecrowdfundingBackerPaymentDetail['incrementBacker']) {
            $project->backer_count += 1;
            $project->save();
            $session->sitecrowdfundingBackerPaymentDetail['incrementBacker'] = false;

            $backer_obj->order_status = 2;
            $backer_obj->save();
            //$notifyApi->addNotification($notifyUser, $viewer, $subject, $type, $params);
            $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);
            $priceStr = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount);

            if (!$backer_obj->is_private_backing) {
                //CREATE FEED FOR THE BACKED PROJECT
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $project, 'sitecrowdfunding_project_back', null, array('amount' => $priceStr, 'customMessage' => $backer->custom_message));
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                }
            }


            // Send email to backer for successful payment
            $amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer_obj->amount);
            $delivery_date = date("d M Y", strtotime($backer_obj->delivery_date));
            $end_date = date("d M Y", strtotime($project->funding_end_date));
            $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer_obj->reward_id);
            $rewardTitle = ($reward) ? $reward->getTitle() : 'No';
            $delivery_date = ($reward) ? $delivery_date : 'N/A';

            $settings = Engine_Api::_()->getApi('settings', 'core');
            if($settings->getSetting('sitecrowdfunding.reminder.project.backer.success', 0)) {

                // output: localhost
                $hostName = $_SERVER['HTTP_HOST'];
                // output: http://
                $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
                $site_url = $protocol.'://'.$hostName;

                if($_SERVER['SERVER_NAME'] == 'stage.impactx.co'){
                    $logoUrl = $site_url.'/network/public/admin/Impact-Network-transparent-logo.png';
                    $noPhotoUrl = $site_url .'/'.'network/application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png';
                    $locationUrl = $site_url ."/".'network/application/modules/Sitepage/externals/images/location.png';
                }else{
                    $logoUrl = $site_url.'/net/public/admin/Impact-Network-transparent-logo.png';
                    $noPhotoUrl = $site_url .'/'.'net/application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png';
                    $locationUrl = $site_url ."/".'net/application/modules/Sitepage/externals/images/location.png';
                }

                /* receipt related datas */
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($viewer->email, "$backerEmailType", array(
                    'project_name' => $project_name,
                    'member_name' => $viewer->getTitle(),
                    'amount' => $amount,
                    'end_date' => $end_date,
                    'reward' => $rewardTitle,
                    'delivery_date' => $delivery_date,
                    'project_link' => $project_link,
                    // dont queue send it immediately as it is payment
                    'html_template' => $this->view->partial('backer/_print-invoice.tpl',array(
                        "backer_id" => $backer_id,
                        'site_url' => $site_url,
                        'logoUrl' => $logoUrl,
                        'noPhotoUrl' => $noPhotoUrl,
                        'locationUrl' => $locationUrl
                    )),
                    'queue' => false,
                    'invoice_link' => $view->htmlLink($host . '/sitecrowdfunding/backer/print-invoice/backer_id/'.Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($backer_id), 'Download Invoice')
                ));


                try{
                  //  if($project->is_user_followed_after_donate_yn) {
                        $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
                        $favouriteTable->addFavourite($project, $viewer);
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $project, 'project_follow');
                        if ( $action ) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                        }
                  //  }

                }catch (Exception $exception){
                    // do nothing if already followed
                }

                //customised code to send notification for team members,admins,owner
                $user_ids = array();
                $project_id =$project->project_id;

                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

                // $project_id = $subject->getIdentity();
                if($project->notify_project_donate || $project->notify_project_donate==0 || $project->notify_project_donate=='' || $project->notify_project_donate==null) {
                    $level = $project->notify_project_donate;
                    $levelArr = explode(" ",$level);
                    $idsArr = explode(",",$levelArr[0]);
                    if(!$project->notify_project_donate) {
                        array_push($idsArr,0);
                    }
                    //customised code to fetch users to send notification for team members,admins,owner
                    foreach ($idsArr as $level_id) {

                        // Project Members
                        if($level_id == 1) {
                            $team_members = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);
                            foreach ($team_members as $id) {
                                array_push($user_ids,$id['user_id']);
                            }
                        }

                        // Project Followers
                        if($level_id == 2) {
                            $this->view->resource_id = $resource_id = $project->getIdentity();
                            $this->view->resource_type = $resource_type = $project->getType();
                            $resource = Engine_Api::_()->getItem( $resource_type , $resource_id ) ;
                            $projectFollowers = $this->getFavouriteFollowers($resource);
                            foreach ($projectFollowers as $id) {
                                array_push($user_ids,$id['poster_id']);
                            }
                        }

                        // add by default
                        //Admins or Owner
                        //if($level_id == 3 || $level_id == 0) {
                            //get admin members of the project start
                            /** List Project Admins */
                            //if(!empty($project)) {

                                $this->view->list = $list = $project->getLeaderList();

                                $list_id = $list['list_id'];

                                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                                $listItemTableName = $listItemTable->info('name');

                                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                                $userTableName = $userTable->info('name');
                                $selectLeaders = $listItemTable->select()
                                    ->from($listItemTableName, array('child_id'))
                                    ->where("list_id = ?", $list_id)
                                    ->query()
                                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                                $selectLeaders[] = $project->owner_id;

                                $select = $userTable->select()
                                    ->from($userTableName)
                                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                                    ->where("$userTableName.email != ? ",$viewer->email)
                                    ->order('displayname ASC');

                                $adminMembers = $userTable->fetchAll($select);

                                foreach ($adminMembers as $id) {
                                    array_push($user_ids,$id['user_id']);
                                }
                            //}
                        //}

                        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
                        if(!empty($parentOrganization['page_id']) && ($level_id == 5 || $level_id == 6 || $level_id == 4 || $level_id == 7 )){

                            $org_id = $parentOrganization['page_id'];
                            $sitepage = Engine_Api::_()->getItem('sitepage_page', $org_id);
                            $owner_id = $sitepage->owner_id;

                            //get organization owner
                            if($level_id == 4) { //level 4
                                array_push($user_ids,$owner_id);
                            }

                            //get organization team members
                            if($level_id == 5) { //level 5
                                $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                                $sitepagemember = $membershipTable->getallsitepagemembersSelect($org_id);
                                foreach ($sitepagemember as $item) {
                                    array_push($user_ids,$item->user_id);
                                }
                            }
                            //get organization admins
                            if($level_id == 7) {  //level 7
                                $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
                                $sitepageadmins = $manageadminsTable->getManageAdminUser($org_id);

                                foreach ($sitepageadmins as $item) {
                                    if($owner_id != $item->user_id) {
                                        array_push($user_ids,$item->user_id );
                                    }
                                }
                            }
                            // get organization followers
                            if($level_id == 6) { //level 6
                                $followerUserId=array();
                                $followerUserId = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowerUserIds('sitepage_page', $org_id);
                                $user_ids =  array_merge($user_ids,$followerUserId);
                            }
                        }

                    }

                    // unique users  from team member , followers and admin
                    $user_ids =  array_unique($user_ids);

                    foreach ($user_ids as $user_id) {
                        $user_subject = Engine_Api::_()->user()->getUser($user_id);
                        $Email = $user_subject->email;
                        $viewer_user_name = $viewer->getTitle();
                        $profile_name = $viewer_user_name;
                        // to avoid sending duplicate email to user who is already a admin and donated user too
                        if($viewer->email != $Email) {
                            $type = "sitecrowdfunding_back";
                            $notifyApi->addNotification($user_subject, $viewer, $subject, $type, $params);
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($Email, "NOTIFY_SITECROWDFUNDING_BACK", array(
                                'project_name' => $project_name,
                                'project_link' => $project_link,
                                'user_name' => $profile_name,
                                'profile_link' => $profile_link,
                                'queue' => false,
                            ));
                        }
                    }
                }

            }
        }

        // SUCCESS MESSAGE
        $this->view->backer_id = $backer_id;
        $this->view->back_details = $tempViewUrl = $this->view->url(array('action' => 'view', 'backer_id' => $backer_id, 'project_id' => $backer_obj->project_id), 'sitecrowdfunding_backer', true);

        $success_message .= '<span class="payment_backing_id">Your backing ID #' . $backer_id . '. </span><br><br> ';

        if (!empty($viewer_id)) {
            $success_message .= $this->view->translate('You will soon receive your backing details on your registered mail id. You can also %1$sclick here%2$s to view your backing details.', "<a class='smoothbox' href='$tempViewUrl'>", "</a>");
        }

        $this->view->success_message = $success_message;

        // get organisation
        if(!empty($project->project_id)){
            $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project->project_id);
            if(empty($parentOrganization)){
                $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project->project_id);
            }
            $this->view->parentOrganization = $parentOrganization;
            if(!empty($parentOrganization['page_id']) ){
                $this->view->page_id = $page_id = $parentOrganization['page_id'];
            }
        }

        //IF BACKING IS DONATION TYPE THAN REDIRECT CONTROL TO THE RESPECTIVE SOURCE PAGE(where the from where the back link was clicked)
        /*if ($donationType) {
            header("refresh:5;url=$sourceUrl");
        }*/
    }
    //get followers of the projects
    public function getFavouriteFollowers(Core_Model_Item_Abstract $resource)
    {
        $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
        $favouritesTableName = $favouritesTable->info('name');

        $select = new Zend_Db_Select($favouritesTable->getAdapter());
        $select->from($favouritesTableName, 'poster_id')
            ->where('resource_id = ?', $resource->getIdentity());

        $data = $select->query()->fetchAll();
        return $data;
    }
    public function backersReportAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $params = array();
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $params['project_id'] = $this->view->project_id = $project->project_id;
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['page'] = $this->view->page = $this->_getParam('page', 1);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }
        $multiOptions = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getPaymentStates();
        $this->view->payment_status = array_merge(array('' => 'Payment Status: Any'), $multiOptions);
        $this->view->message = 'There are no backers for this project yet.';
        $this->view->searchUser = '';
        $this->view->searchOption = 0;
        if (isset($_POST['searchByRewards'])) {
            $this->view->searchOption = $params['searchByRewards'] = $_POST['searchByRewards'];
            $this->view->message = 'There are no backers related to this criteria.';
        }
        if (isset($_POST['search'])) {
            $this->view->searchUser = $params['username'] = $_POST['username'];
            $params['user_id'] = $user_id = $_POST['user_id'];
            $this->view->selectedStatus = $params['payment_status'] = $_POST['payment_status'];
            $this->view->message = 'There are no backers related to this criteria.';
        }
        if (isset($user_id) && !empty($user_id)) {
            $this->view->searchUser = $user = Engine_Api::_()->user()->getUser($user_id)->getTitle();
        }

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getBackersPaginator($params);

        $this->view->total_item = $paginator->getTotalItemCount();
    }

    public function exportBackersAction() {

        $this->_helper->layout->setLayout('default-simple');
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $params = array();
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $this->view->project_id = $params['project_id'] = $project->project_id;
        $viewer = Engine_Api::_()->user()->getViewer();


        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project->project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }




        if (isset($_POST['searchByRewards'])) {
            $params['searchByRewards'] = $_POST['searchByRewards'];
        }
        if (isset($_POST['search'])) {
            $params['username'] = $_POST['username'];
        }
        $params['export_backers'] = true;
        $this->view->paginator = $paginator = $paginator = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getBackersPaginator($params);
        $this->view->total_item = $paginator->getTotalItemCount();
    }

    public function viewAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->backer_id = $backer_id = $this->_getParam('backer_id');
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->backer = $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);
        $this->view->user = $user = Engine_Api::_()->user()->getUser($backer->user_id);
        if ($this->view->project_id != $backer->project_id) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->reward = 0;
        $this->view->shipping_included = 0;
        if ($backer->reward_id) {
            $this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);
            if ($reward->shipping_method == 2 || $reward->shipping_method == 3) {
                $this->view->shipping_included = 1;
            }

        }
    }

    public function viewBackedDetailsAction() {
        $project_id = $this->_getParam('project_id');
        if (empty($project_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->results = $project->getBackingDetailForLoginUser();
    }

    public function printInvoiceAction() {
        //ONLY LOGGED IN USER
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->backer_id = $backer_id = Engine_Api::_()->sitecrowdfunding()->getEncodeToDecode($this->_getParam('backer_id', null));
        if (empty($backer_id)) {
            $this->view->sitecrowdfunding_print_invoice_no_permission = true;
            return;
        }
        $this->view->backer = $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);
        $this->view->project_id = $project_id = $backer->project_id;

        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        $this->view->sitepage =  $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($sitepage->donate_receipt_logo, 'thumb.cover');
        $this->view->donate_receipt_logo = $donate_receipt_logo = $file ?  $file->map() :null;

        if ($this->view->project_id != $backer->project_id) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $backer->project_id);
        $this->view->reward = 0;
        $this->view->shipping_included = 0;
        if ($backer->reward_id) {
            $this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);
            if ($reward->shipping_method == 2 || $reward->shipping_method == 3) {
                $this->view->shipping_included = 1;
            }

        }
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->_helper->layout->setLayout('default-simple');
        if (!empty($backer->user_id)) {
            $user_table = Engine_Api::_()->getDbtable('users', 'user');
            $select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $backer->user_id);
            $this->view->user_detail = $user_table->fetchRow($select);
        }

        // FETCH SITE LOGO OR TITLE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                ->where('page_id = ?', $page_id)
                ->where("name LIKE '%core.menu-logo%'")
                ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params->logo)) {
                $this->view->logo = $params->logo;
            }

        }
    }

    /* Commented printInvoiceAction
     public function printInvoiceAction() {
		//ONLY LOGGED IN USER
		if (!$this->_helper->requireUser()->isValid()) {
			return;
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->backer_id = $backer_id = Engine_Api::_()->sitecrowdfunding()->getEncodeToDecode($this->_getParam('backer_id', null));
		if (empty($backer_id)) {
			$this->view->sitecrowdfunding_print_invoice_no_permission = true;
			return;
		}
		$this->view->backer = $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);
		$this->view->project_id = $project_id = $backer->project_id;

		if ($this->view->project_id != $backer->project_id) {
			return $this->_forward('notfound', 'error', 'core');
		}

		$this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $backer->project_id);
		$this->view->reward = 0;
		$this->view->shipping_included = 0;
		if ($backer->reward_id) {
			$this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);
			if ($reward->shipping_method == 2 || $reward->shipping_method == 3) {
				$this->view->shipping_included = 1;
			}

		}
		$this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
		$this->_helper->layout->setLayout('default-simple');
		if (!empty($backer->user_id)) {
			$user_table = Engine_Api::_()->getDbtable('users', 'user');
			$select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $backer->user_id);
			$this->view->user_detail = $user_table->fetchRow($select);
		}

		// FETCH SITE LOGO OR TITLE
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

		$info = $select->query()->fetch();
		if (!empty($info)) {
			$page_id = $info['page_id'];

			$select = new Zend_Db_Select($db);
			$select->from('engine4_core_content', array("params"))
				->where('page_id = ?', $page_id)
				->where("name LIKE '%core.menu-logo%'")
				->limit(1);
			$info = $select->query()->fetch();
			$params = json_decode($info['params']);

			if (!empty($params->logo)) {
				$this->view->logo = $params->logo;
			}

		}
	}
    */
    public function sendRewardAction() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->view->backer_id = $backer_id = $this->_getParam('backer_id');
        $this->view->backer = $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $backer->project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        if ($this->getRequest()->isPost()) {

            $where = array(
                '`backer_id` = ?' => $backer_id,
                '`reward_id` > ?' => 0,
            );

            Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->update(array('reward_status' => 1), $where);

            //Send Mail to backers whose reward has been shipped
            $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);
            $notifyUser = Engine_Api::_()->user()->getUser($backer->user_id);
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $backer->project_id);
            $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);
            $emailType = 'SITECROWDFUNDING_BACKER_REWARD_SHIPPED_EMAIL';

            $project_name = $project->title;
            $host = $_SERVER['HTTP_HOST'];
            $project_link = $view->htmlLink($host . $project->getHref(), $project->title);
            $delivery_date = date("d M Y", strtotime($backer->delivery_date));
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($notifyUser->email, "$emailType", array(
                'project_name' => $project_name,
                'member_name' => $notifyUser->getTitle(),
                'reward' => $reward->title,
                'delivery_date' => $delivery_date,
                'project_link' => $project_link,
            ));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Sent Succesfully.'),
            ));
        }
        $this->renderScript('backer/send-reward.tpl');
    }

    //PAYMENT REQUEST
    public function paymentToMeAction() {

        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $project = $this->view->project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->minimum_requested_amount = $minimum_requested_amount = @round(Engine_Api::_()->sitecrowdfunding()->getTransferThreshold($project_id), 2);
        $total_project_amount = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getTotalAmount($project_id);
        if (empty($total_project_amount['backed_amount']) && empty($total_project_amount['backer_count'])) {
            $total_amount = 0;
        } else {
            $total_amount = $total_project_amount['backed_amount'] - $total_project_amount['commission_value'];
        }
        $this->view->total_amount = @round($total_amount, 2);
        $this->view->backer_count = $total_project_amount['backer_count'];
        $this->view->threshold_amount = Engine_Api::_()->sitecrowdfunding()->getTransferThreshold($project_id);

        $remaining_amount_table = Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding');
        $remaining_amount_obj = $remaining_amount_table->fetchRow(array('project_id = ?' => $project_id));
        $paymentRequestTable = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding');
        $requested_amount = $paymentRequestTable->getRequestedAmount($project_id);

        if (empty($remaining_amount_obj->project_id)) {
            $remaining_amount_table->insert(array('project_id' => $project_id, 'remaining_amount' => 0));
            $remaining_amount = 0;
        } else {
            $remaining_amount = $remaining_amount_obj->remaining_amount;
        }

        $this->view->remaining_amount = @round($remaining_amount, 2);
        $this->view->requesting_amount = empty($requested_amount) ? 0 : @round($requested_amount, 2);

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['project_id'] = $project_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        if (isset($_POST['search'])) {
            $this->_helper->layout->disableLayout(true);
            $params['search'] = 1;
            $params['request_date'] = $_POST['request_date'];
            $params['response_date'] = $_POST['response_date'];
            $params['request_min_amount'] = $_POST['request_min_amount'];
            $params['request_max_amount'] = $_POST['request_max_amount'];
            $params['response_min_amount'] = $_POST['response_min_amount'];
            $params['response_max_amount'] = $_POST['response_max_amount'];
            $params['request_status'] = $_POST['request_status'];
            $this->view->only_list_content = true;
        }

//MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding')->getProjectPaymentRequestPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function paymentRequestAction() {

        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $minimum_requested_amount = @round(Engine_Api::_()->sitecrowdfunding()->getTransferThreshold($project_id), 2);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $remaining_amount = Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id))->remaining_amount;
        $total_project_amount = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getTotalAmount($project_id);
        $total_amount = empty($total_project_amount['backed_amount']) ? 0 : $total_project_amount['backed_amount'] - $total_project_amount['commission_value'];
        $this->view->user_max_requested_amount = $user_requested_amount = @round(($remaining_amount + $total_amount), 2);
        $backer_count = $this->_getParam('backer_count');
        $gateway_id = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getProjectGateway($project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($gateway_id)) {
            if ($viewer_id == $project->owner_id) {
                $this->view->req_page_owner = true;
            }

            $this->view->gateway_disable = 1;
        } else if ($minimum_requested_amount > $user_requested_amount) {
            $this->view->not_allowed_for_payment_request = 1;
            $this->view->minimun_requested_amount = $minimum_requested_amount;
            $this->view->gross_amount = $user_requested_amount;
        } else {
            $this->view->form = $form = new Sitecrowdfunding_Form_Paymentrequest(array('requestedAmount' => $user_requested_amount, 'totalAmount' => $total_amount, 'remainingAmount' => $remaining_amount, 'amounttobeRequested' => $user_requested_amount));

            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

            $form->total_amount->setLabel($this->view->translate('New Backers <br /> (%s)', $currencyName));
            $form->total_amount->getDecorator('Label')->setOption('escape', false);

            $form->remaining_amount->setLabel($this->view->translate('Remaining Amount <br /> (%s)', $currencyName));
            $form->remaining_amount->getDecorator('Label')->setOption('escape', false);

            $form->amount_to_be_requested->setLabel($this->view->translate('Balance Amount <br /> (%s)', $currencyName));
            $form->amount_to_be_requested->getDecorator('Label')->setOption('escape', false);

            $form->amount->setLabel($this->view->translate('Amount to be Requested <br /> (%s)', $currencyName));
            $form->amount->getDecorator('Label')->setOption('escape', false);

            $form->removeElement('last_requested_amount');
            $this->view->user_requested_amount = $user_requested_amount;

            if (!$this->getRequest()->isPost()) {
                return;
            }
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            $values = array('total_amount' => @round($total_amount, 2), 'remaining_amount' => @round($remaining_amount, 2), 'amount_to_be_requested' => @round($user_requested_amount, 2));
            $temp_values = $form->getValues();
            $values['amount'] = $temp_values['amount'];
            $values['message'] = $temp_values['message'];

            $form->populate($values);

            if ($values['amount'] < $minimum_requested_amount && $values['amount'] > 0) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting for a less amount (%s) than the minimun request payment amount (%s) set by site administrator. Please request for an amount equal or greater than (%s)');
                $error = sprintf($error, Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($values['amount']), Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($minimum_requested_amount), Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($minimum_requested_amount));
                $form->addError($error);
                return;
            }

            if ($values['amount'] > $user_requested_amount) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting a amount for which you are not able. Please request for a amount equal or less than %s');
                $error = sprintf($error, Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($user_requested_amount));
                $form->addError($error);
                return;
            }

            $remaining_amount = @round($user_requested_amount - $values['amount'], 2);
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $payment_req_table = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding');
                $payment_req_table->insert(array(
                    'project_id' => $project_id,
                    'backer_count' => $backer_count,
                    'request_amount' => @round($values['amount'], 2),
                    'request_date' => date('Y-m-d H:i:s'),
                    'request_message' => $values['message'],
                    'remaining_amount' => $remaining_amount,
                    'request_status' => '0',
                ));

                $request_id = $payment_req_table->getAdapter()->lastInsertId();
                $payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);

//UPDATE PAYMENT REQUEST ID IN ORDER TABLE
                Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->update(
                    array('payment_request_id' => $request_id), array('project_id =? AND payment_request_id = 0 AND direct_payment = 0' => $project_id));

//UPDATE REMAINING AMOUNT
                Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding')->update(
                    array('remaining_amount' => $remaining_amount), array('project_id =? ' => $project_id));

                $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                $project_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $project->getHref() . '">' . $project->getTitle() . '</a>';
                $project_owner = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $project->getOwner()->getHref() . '">' . $project->getOwner()->getTitle() . '</a>';

//Removed Case: SEND MAIL TO PROJECT OWNER ABOUT PAYMENT REQUEST
                // SEND MAIL TO SITE ADMIN FOR THIS PAYMENT REQUEST
                $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);

                if (!empty($admin_email_id)) {
                    $user = Engine_Api::_()->getItemTable('user')->fetchRow(array('email = ?' => $admin_email_id));
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'sitecrowdfunding_payment_request_to_admin', array(
                        'project_name' => $project_name,
                        'request_amount' => Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($values['amount']),
                        'project_owner' => $project_owner,
                        'project_title' => $project->getTitle(),
                        'project_owner_title' => $project->getOwner()->getTitle(),
                        'member_name' => $user->getTitle(),
                    ));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your payment request has been successfully sent.')),
            ));
        }
    }

    public function editPaymentRequestAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);
        if (empty($request_id) || empty($payment_req_obj)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->project_id = $project_id = $payment_req_obj->project_id;

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $gateway_id = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getProjectGateway($project_id);
        $payment_req_table_obj = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding');

        if ($payment_req_obj->request_status == 1) {
            $this->view->sitecrowdfunding_payment_request_deleted = true;
            return;
        } else if ($payment_req_obj->request_status == 2) {
            $this->view->sitecrowdfunding_payment_request_completed = true;
            return;
        }

        if (!empty($payment_req_obj->payment_flag)) {
            $time_diff = abs(time() - strtotime($payment_req_obj->response_date));
            if ($time_diff > 3600) {
                $payment_req_obj->payment_flag = 0;
                $payment_req_obj->save();
            } else {
                $this->view->sitecrowdfunding_admin_responding_request = true;
                return;
            }
        }

        if (empty($gateway_id)) {
            if ($viewer_id == $project->owner_id) {
                $this->view->req_page_owner = true;
            }
            $this->view->gateway_disable = 1;
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('project_id = ?' => $project_id))->remaining_amount;
        $total_project_amount = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getTotalAmount($project_id);
        $total_amount = empty($total_project_amount['backed_amount']) ? 0 : $total_project_amount['backed_amount'] + $total_project_amount['commission_value'];
        $amount_to_be_requested = $remaining_amount + $total_amount + $payment_req_obj->request_amount;

        $this->view->form = $form = new Sitecrowdfunding_Form_Paymentrequest(array('requestedAmount' => @round($payment_req_obj->request_amount, 2), 'totalAmount' => $total_amount, 'remainingAmount' => $remaining_amount, 'amounttobeRequested' => $amount_to_be_requested));
        $form->last_requested_amount->setValue(@round($payment_req_obj->request_amount, 2));
        $form->message->setValue($payment_req_obj->request_message);

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $form->total_amount->setLabel($this->view->translate('New Backers <br /> (%s)', $currencyName));
        $form->total_amount->getDecorator('Label')->setOption('escape', false);

        $form->remaining_amount->setLabel($this->view->translate('Remaining Amount <br /> (%s)', $currencyName));
        $form->remaining_amount->getDecorator('Label')->setOption('escape', false);

        $form->last_requested_amount->setLabel($this->view->translate('Last Requested Amount <br /> (%s)', $currencyName));
        $form->last_requested_amount->getDecorator('Label')->setOption('escape', false);

        $form->amount_to_be_requested->setLabel($this->view->translate('Balance Amount <br /> (%s)', $currencyName));
        $form->amount_to_be_requested->getDecorator('Label')->setOption('escape', false);

        $form->amount->setLabel($this->view->translate('New Amount to be Requested <br /> (%s)', $currencyName));
        $form->amount->getDecorator('Label')->setOption('escape', false);

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = array('total_amount' => @round($total_amount, 2), 'remaining_amount' => @round($remaining_amount, 2), 'last_requested_amount' => @round($payment_req_obj->request_amount, 2), 'amount_to_be_requested' => @round($amount_to_be_requested, 2));
        $temp_values = $form->getValues();
        $values['amount'] = $temp_values['amount'];
        $values['message'] = $temp_values['message'];

        $form->populate($values);
        $minimum_requested_amount = @round(Engine_Api::_()->sitecrowdfunding()->getTransferThreshold($project_id), 2);

        if (@round($values['amount'], 2) != @round($payment_req_obj->request_amount, 2)) {
            $user_max_requested_amount = @round($payment_req_obj->request_amount, 2)+@round($remaining_amount, 2)+@round($total_amount, 2);

            if ($values['amount'] < $minimum_requested_amount) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting for a less amount (%s) than the minimun request payment amount (%s) set by site administrator. Please request for an amount equal or greater than (%s)');
                $error = sprintf($error, Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($values['amount']), Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($minimum_requested_amount), Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($minimum_requested_amount));
                $form->addError($error);
                return;
            }
            if ($values['amount'] > $user_max_requested_amount) {
                $form->addError('You are requesting a amount for which you are not able. Please request for a amount equal or less than in your shopping account.');
                return;
            }

            $remaining_amount = @round(($user_max_requested_amount - $values['amount']), 2);

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $payment_req_obj->request_amount = @round($values['amount'], 2);
                $payment_req_obj->request_message = $values['message'];
                $payment_req_obj->remaining_amount = $remaining_amount;
                $payment_req_obj->save();

//UPDATE REMAINING AMOUNT
                $remaining_amount_table_obj->update(array('remaining_amount' => $remaining_amount), array('project_id =? ' => $project_id));

// UPDATE ORDERS FOR WHICH PAYMENT REQUEST HAS BEEN SENT
                Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->update(
                    array('payment_request_id' => 1), array("project_id =? AND payment_request_id = 0 AND direct_payment = 0 AND payment_status LIKE 'active' AND order_status = 2" => $project_id)
                );

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else {
            $payment_req_obj->request_message = $values['message'];
            $payment_req_obj->save();
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request edited successfully.')),
        ));
    }

    public function deletePaymentRequestAction() {

        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);
        if (empty($request_id) || empty($payment_req_obj)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $project_id = $payment_req_obj->project_id;

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        if ($payment_req_obj->request_status == 1) {
            $this->view->sitecrowdfunding_payment_request_deleted = true;
            return;
        } else if ($payment_req_obj->request_status == 2) {
            $this->view->sitecrowdfunding_payment_request_completed = true;
            return;
        }

        if (!empty($payment_req_obj->payment_flag)) {
            $time_diff = abs(time() - strtotime($payment_req_obj->response_date));
            if ($time_diff > 3600) {
                $payment_req_obj->payment_flag = 0;
                $payment_req_obj->save();
            } else {
                $this->view->sitecrowdfunding_admin_responding_request = true;
                return;
            }
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('project_id = ?' => $project_id))->remaining_amount;
        $remaining_amount += $payment_req_obj->request_amount;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $payment_req_obj->request_status = 1;
            $payment_req_obj->save();

//UPDATE REMAINING AMOUNT
            $remaining_amount_table_obj->update(array('remaining_amount' => $remaining_amount), array('project_id =? ' => $project_id));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request deleted successfully.')),
        ));
    }

    public function viewPaymentRequestAction() {
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'view')->isValid()) {
            return;
        }

        $this->view->request_id = $request_id = $this->_getParam('request_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $project->owner_id);
        $this->view->payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);
    }

    public function yourBillAction() {

        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        Engine_Api::_()->sitecrowdfunding()->isAllowThresholdNotifications(array('project_id' => $project_id));
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $remainingBillAmount = Engine_Api::_()->sitecrowdfunding()->getRemainingBillAmount($project_id);
        $this->view->paidBillAmount = Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding')->totalPaidBillAmount($project_id);
        $this->view->newBillAmount = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getProjectBillAmount($project_id);
        $this->view->remainingBillAmount = round($remainingBillAmount, 2);
        $this->view->totalBillAmount = round(($this->view->remainingBillAmount + $this->view->newBillAmount), 2);

        $params = array();
        $params['project_id'] = $project_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $this->view->only_list_content = $this->_getParam('only_list_content', false);
        if (isset($_POST['search']) || isset($_POST['showDashboardProjectContent'])) {
            if (isset($_POST['search'])) {
                $params['search'] = 1;
                $params['bill_date'] = $_POST['bill_date'];
                $params['bill_min_amount'] = $_POST['bill_min_amount'];
                $params['bill_max_amount'] = $_POST['bill_max_amount'];
                $params['status'] = $_POST['status'];
            }
            $this->_helper->layout->disableLayout();
            $this->view->only_list_content = true;
        }

//MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getProjectBillPaginator($params);
    }

    public function billPaymentAction() {

        $this->view->project_id = $project_id = $this->_getParam('project_id', null);

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $where = "plugin = 'Payment_Plugin_Gateway_PayPal'";
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $additionalGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.paymentmethod', 'paypal');
            $additionalGateway = ucfirst($additionalGateway);
            $where = "plugin = 'Payment_Plugin_Gateway_PayPal' OR plugin = 'Sitegateway_Plugin_Gateway_$additionalGateway'";
        }

        $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $isPaypalEnabled = $gateway_table->select()
            ->from($gateway_table->info('name'), array('gateway_id'))
            ->where($where)
            ->where('enabled = 1')
            ->query()
            ->fetchColumn();

        if (empty($isPaypalEnabled)) {
            $this->view->noAdminGateway = true;
            return;
        }

        $remainingBillTable = Engine_Api::_()->getDbtable('remainingbills', 'sitecrowdfunding');
        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');

        $remainingBillAmount = $remainingBillTable->fetchRow(array('project_id = ?' => $project_id))->remaining_bill;
        $newBillAmount = $backerTable->getProjectBillAmount($project_id);

        $totalBillAmount = round(($remainingBillAmount + $newBillAmount), 2);

        $this->view->form = $form = new Sitecrowdfunding_Form_BillPayment(array('totalBillAmount' => $totalBillAmount));

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $form->total_bill_amount->setLabel($this->view->translate('Total Bill Amount <br /> (%s)', $currencyName));
        $form->total_bill_amount->getDecorator('Label')->setOption('escape', false);
        $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));
        $form->total_bill_amount->setValue($totalBillAmount);

        $form->bill_amount_pay->setLabel($this->view->translate('Amount to Pay <br /> (%s)', $currencyName));
        $form->bill_amount_pay->getDecorator('Label')->setOption('escape', false);
        $form->bill_amount_pay->setValue($totalBillAmount);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));
        $form->total_bill_amount->setValue($totalBillAmount);

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (round($values['bill_amount_pay'], 2) > $totalBillAmount) {
            $error = Zend_Registry::get('Zend_Translate')->_("You can't pay commission more than your total bill amount. Please enter an amount equal to or less than your total bill amount.");
            $form->addError($error);
            return;
        }

        $newRemainingBillAmount = round($totalBillAmount - $values['bill_amount_pay'], 2);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $projectBillTable = Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding');
            $projectBillTable->insert(array(
                'project_id' => $project_id,
                'amount' => round($values['bill_amount_pay'], 2),
                'remaining_amount' => round($newRemainingBillAmount, 2),
                'message' => $values['message'],
                'creation_date' => new Zend_Db_Expr('NOW()'),
                'status' => 'initial',
            ));

            $projectBillId = $projectBillTable->getAdapter()->lastInsertId();

// MANAGE REMAINING BILL AMOUNT
            $isProjectRemainingBillExist = $remainingBillTable->isProjectRemainingBillExist($project_id);
            if (empty($isProjectRemainingBillExist)) {
                $remainingBillTable->insert(array(
                    'project_id' => $project_id,
                    'remaining_bill' => $newRemainingBillAmount,
                ));
            } else {
                $remainingBillTable->update(array('remaining_bill' => $newRemainingBillAmount), array('project_id =? ' => $project_id));
            }

//UPDATE PROJECT BILL ID IN BACKER TABLE
            $backerTable->update(array('projectbill_id' => $projectBillId), array('project_id =? AND projectbill_id = 0 AND direct_payment = 1' => $project_id));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefreshTime' => '10',
            'parentRedirect' => $this->view->url(array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'bill-process', 'project_id' => $project_id, 'bill_id' => $projectBillId), '', true),
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('You will be redirected to make payment for your bill.')),
        ));
    }

    public function billProcessAction() {

        $project_id = $this->_getParam('project_id', null);
        $bill_id = $this->_getParam('bill_id', null);

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $this->_session = new Zend_Session_Namespace('Project_Bill_Payment_Sitecrowdfunding');
        if (!empty($this->_session)) {
            $this->_session->unsetAll();
            $this->_session->project_id = $project_id;
            $this->_session->bill_id = $bill_id;
        }
        return $this->_forward('process', 'project-bill-payment', 'sitecrowdfunding', array('project_id' => $project_id, 'bill_id' => $bill_id));
    }

    public function billDetailsAction() {
        $bill_id = $this->_getParam('bill_id', null);
        $project_id = $this->_getParam('project_id', null);
        $this->view->projectBillObj = Engine_Api::_()->getItem('sitecrowdfunding_projectbill', $bill_id);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $project->owner_id);
        $this->view->transaction = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->fetchRow(array('source_id = ?' => $bill_id, 'source_type = ?' => 'sitecrowdfunding_projectbill', 'sender_type =?' => 2, 'gateway_id = ?' => $this->view->projectBillObj->gateway_id));
    }

    public function monthlyBillDetailAction() {

        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $this->view->search = $this->_getParam('search', 0);

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $params = array();
        $this->view->month = $params['month'] = $this->_getParam('month');
        $this->view->year = $params['year'] = $this->_getParam('year');

        $this->view->monthName = date("F", mktime(0, 0, 0, $params['month']));

        $params['project_id'] = $project_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

//MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getProjectMonthlyBillPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function buyerDetailsAction() {

// Render
        $this->_helper->content
//->setNoRender()
            ->setEnabled()
        ;

// GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (empty($_POST)) {
            return;
        }
        $projectBuyProjectSteps = Zend_Registry::isRegistered('projectBuyProjectSteps') ? Zend_Registry::get('projectBuyProjectSteps') : null;
        if (empty($projectBuyProjectSteps)) {
            return;
        }

        $session = new Zend_Session_Namespace('sitecrowdfunding_cart_formvalues');
        $session->formValues = $this->view->formValues = $_POST;

        $this->view->tax_rate = false;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tax.enabled', 0) && Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'is_tax_allow') && Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'tax_rate') > 0) {
            $this->view->tax_rate = true;
        }
    }

// Action to fetch payment request transactions done by the admin
    public function transactionAction() {

//Project ID
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        $this->view->transaction_state = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->getTransactionState(true, $project_id);

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $params['project_id'] = $project_id;

        if (isset($_POST['search'])) {
            $this->_helper->layout->disableLayout();
            $params['search'] = 1;
            $params['date'] = $_POST['date'];
            $params['response_min_amount'] = $_POST['response_min_amount'];
            $params['response_max_amount'] = $_POST['response_max_amount'];
            $params['state'] = $_POST['state'];
            $this->view->only_list_content = true;
        }

//MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding')->getAllAdminTransactionsPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function viewTransactionDetailAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'edit')->isValid()) {
            return;
        }

        $this->view->allParams = $this->_getAllParams();
    }

}
