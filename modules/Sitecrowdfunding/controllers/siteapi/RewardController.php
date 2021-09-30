<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: RewardController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_RewardController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        //SET THE SUBJECT
        if (0 !== ($reward_id = (int) $this->_getParam('reward_id')) && null !== ($reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($reward);
        }
    }

    //THIS ACTION USED TO CREATE A REWARD
    public function createAction() {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to create Project. You are logged out user");

        $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            $this->respondWithError('no_record');
        }
        if (!$project->isOpen()) {
            $this->respondWithError('unauthorized', "Project is closed.");
        }
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($isCreatePrivacy))
            $this->respondWithError('unauthorized', "You don't have permission to create Reward.");
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized', "You don't have permission to create Reward.");
        }

        if ($this->getRequest()->isGet()) {
            try {

                //MAKE FORM
                $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
                $rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->query()->fetchColumn();
                $rewardCount++;
                $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->createRewardForm($rewardCount);
                $this->respondWithSuccess($form);
            } catch (Exception $ex) {
                
            }
        }

        if ($this->getRequest()->isPost()) {
            $data = $values = $_REQUEST;
            //Data validation.......................
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->rewardFormValidations();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);



            //.............................................................

            $dlvDate = explode("-", $values['delivery_date']);
            $deliveryDate = date('Y-m-d', strtotime($values['delivery_date']));
            if (strtotime(date('Y-m-d')) > strtotime($deliveryDate)) {
                //throw exception
                $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                $validationMessage['delivery_date'] = $this->translate('Delivery date must be greater than current date.');
            }

            $shippingChargeByCountry = array();
            if ($values['shipping_method'] > 1) {
                $shippingChargeByCountry = $this->shippingCharge($values);
                $locationArray = isset($shippingChargeByCountry['locationsArray']) ? $shippingChargeByCountry['locationsArray'] : array();
                $shippingAmount = isset($shippingChargeByCountry['shippingAmountsArray']) ? $shippingChargeByCountry['shippingAmountsArray'] : array();

                if (count($locationArray) == 0 && count($shippingAmount) == 0) {
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['country'] = $this->translate('Please add shipping details. It is required');
                }
            }

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {

                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            if (!isset($quantity) || empty($value['limit'])) {
                $value['quantity'] = 0;
            } else {
                if (empty($value['quantity']) || $value['quantity'] < 0) {
                    $value['quantity'] = 0;
                }
            }
            $value['quantity'] = (int) $value['quantity'];
            $shippingLocationTable = Engine_Api::_()->getDbtable('rewardshippinglocations', 'sitecrowdfunding');
            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_reward');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $rewardModel = $table->createRow();
                $rewardModel->setFromArray($values);
                $rewardModel->title = htmlspecialchars($rewardModel->title);
                $rewardModel->project_id = $project_id;
                $rewardModel->owner_id = $viewer->getIdentity();
                $rewardModel->owner_type = $viewer->getType();
                $rewardModel->save();
                //SET PHOTO
                try {
                    if (!empty($_FILES['photo'])) {
                        $rewardModel->setPhoto($_FILES['photo']);
                    }
                } catch (Exception $ex) {
                    
                }

                if (($rewardModel->shipping_method) > 1) {
                    $locationArray = isset($shippingChargeByCountry['locationsArray']) ? $shippingChargeByCountry['locationsArray'] : array();
                    $shippingAmount = isset($shippingChargeByCountry['shippingAmountsArray']) ? $shippingChargeByCountry['shippingAmountsArray'] : array();

                    if (count($locationArray) > 0 && count($shippingAmount) > 0) {
                        foreach ($locationArray as $key => $location) {
                            //LOCATION AND SHIPPING CHARGE SHOULD NOT BE BLANK
                            if ((empty($location) && count($locationArray) == 1 && empty($shippingAmount[$key])) || (!empty($location) && empty($shippingAmount[$key]))) {
                                //throw exception
                                $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                                $validationMessage['shhipping_charge_' . $location] = $this->translate('Please fill location and shipping charge.');
                                $this->respondWithValidationError('validation_fail', $validationMessage);
                            }

                            //TAKING COUNTRIES OBJECT
                            $locale = Engine_Api::_()->getApi('Core', 'siteapi')->getLocal();
                            $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                            if ($location != "1" && !array_key_exists($location, $countries)) {
                                continue;
                            }
                            if (!array_key_exists($key, $shippingAmount) || is_null($shippingAmount[$key]) || $shippingAmount[$key] == false || $shippingAmount[$key] < 0) {
                                continue;
                            }
                            if ($location != "1") {
                                $regionId = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->isCountryExist($location, true);

                                if (empty($regionId))
                                    continue;
                            }
                            else {
                                $regionId = 0;
                            }
                            $shippingLocationTable->insert(array(
                                'reward_id' => $rewardModel->getIdentity(),
                                'project_id' => $rewardModel->project_id,
                                'region_id' => $regionId,
                                'amount' => $shippingAmount[$key],
                            ));
                        }
                    }
                }
                $db->commit();
                $bodyParams = array();
                if (!empty($rewardModel)) {
                    $bodyParams['response']['reward_id'] = $rewardModel->getIdentity();
                    $bodyParams['response']['project_id'] = $rewardModel->project_id;
                }
                $this->respondWithSuccess($bodyParams, true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to create Project. You are logged out user");

        $project_id = $this->_getParam('project_id');
        $reward_id = $this->_getParam('reward_id');
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);
        if (empty($project) || empty($reward)) {
            $this->respondWithError('no_record');
        }

        $form['formValues'] = $reward->toArray();
        if(!empty($form['formValues']['quantity'])){
            $form['formValues']['limit']=1;
        }
        else{
             $form['formValues']['limit']=0;
        }
        $shippingLocations = array();
        if ($reward->shipping_method > 1)
            $shippingLocations = $reward->findShippingLocations($project_id);
        $selectedCountries = array();
        $countriesAmount = array();
        foreach ($shippingLocations as $location) {
            $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $location['region_id']);
            if (empty($location['region_id']) && $reward->shipping_method == 3) {
                $form['formValues']['rest_world'] = $location['amount'];
            } elseif (!empty($region)) {
                $form['formValues']['shhipping_charge_' . $region['country']] = $location['amount'];
                $countryValue[] = $region['country'];
                 $form['formValues']['country'] =$countryValue;
            }
        }

        $form['formValues']['disableFields'] = ($reward->spendRewardQuantity() > 0) ? 1 : 0;
        //ALLOW ADMINS TO EDIT ALL INFORMATION OF THE REWARDS
        if ($viewer->isAdminOnly()) {
            $form['formValues']['disableFields'] = 0;
        }


        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($isCreatePrivacy))
            $this->respondWithError('unauthorized', "You don't have permission to edit Reward.");

        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized', "You don't have permission to edit Reward.");
        }
        if ($this->getRequest()->isGet()) {
            try {

                $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
                $rewardCount = 0;
                $rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->where('reward_id < ?', $reward->reward_id)->query()->fetchColumn();
                $rewardCount++;
                $respnse = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->createRewardForm($rewardCount, $reward);
                $respnse['formValues'] = $form['formValues'];
                $this->respondWithSuccess($respnse);
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }


        if ($this->getRequest()->isPost()) {

            $data = $values = $_REQUEST;

            //Data validation.......................
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->rewardFormValidations();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);

            $shippingChargeByCountry = array();
            if ($values['shipping_method'] > 1) {
                $shippingChargeByCountry = $this->shippingCharge($values);
                $locationArray = isset($shippingChargeByCountry['locationsArray']) ? $shippingChargeByCountry['locationsArray'] : array();
                $shippingAmount = isset($shippingChargeByCountry['shippingAmountsArray']) ? $shippingChargeByCountry['shippingAmountsArray'] : array();

                if (count($locationArray) == 0 && count($shippingAmount) == 0) {
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['country'] = $this->translate('Please add shipping details. It is required');
                }
            }

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {

                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            //.............................................................




            if (!empty($_FILES['photo'])) {
                $reward->setPhoto($_FILES['photo']);
            }
            if ($reward->spendRewardQuantity() == 0) {
                $deliveryDate = date('Y-m-d', strtotime($values['delivery_date']));
                if (strtotime(date('Y-m-d')) > strtotime($deliveryDate)) {
                    //throw exception
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['delivery_date'] = $this->translate('Delivery date must be greater than current date.');
                }

                if (empty($values['quantity']) || $values['quantity'] < 0) {
                        $values['quantity'] = 0;
                    }
            }

            $values['quantity'] = (int) $values['quantity'];
            $shippingLocationTable = Engine_Api::_()->getDbtable('rewardshippinglocations', 'sitecrowdfunding');
            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_reward');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $rewardModel = $reward;
                if ($reward->spendRewardQuantity() == 0) {
                    $rewardModel->setFromArray($values);
                    $rewardModel->save();
                    $shippingLocationTable->delete(array('project_id = ?' => $rewardModel->project_id, 'reward_id = ?' => $rewardModel->reward_id));

                    if (($rewardModel->shipping_method) > 1) {
                        $locationArray = isset($shippingChargeByCountry['locationsArray']) ? $shippingChargeByCountry['locationsArray'] : array();
                        $shippingAmount = isset($shippingChargeByCountry['shippingAmountsArray']) ? $shippingChargeByCountry['shippingAmountsArray'] : array();
                        if (count($locationArray) > 0 && count($shippingAmount) > 0) {
                            foreach ($locationArray as $key => $location) {
                                //LOCATION AND SHIPPING CHARGE SHOULD NOT BE BLANK

                                if ((empty($location) && count($locationArray) == 1 && empty($shippingAmount[$key])) || (!empty($location) && empty($shippingAmount[$key]))) {
                                    //throw exception
                                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                                    $validationMessage['shhipping_charge_' . $location] = $this->translate('Please fill location and shipping charge.');
                                    $this->respondWithValidationError('validation_fail', $validationMessage);
                                }
                                //TAKING COUNTRIES OBJECT
                                $locale = Engine_Api::_()->getApi('Core', 'siteapi')->getLocal();
                                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                                if ($location != "1" && !array_key_exists($location, $countries)) {
                                    continue;
                                }
                                if (!array_key_exists($key, $shippingAmount) || is_null($shippingAmount[$key]) || $shippingAmount[$key] == false || $shippingAmount[$key] < 0) {
                                    continue;
                                }
                                if ($location != "1") {
                                    $regionId = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->isCountryExist($location, true);

                                    if (empty($regionId))
                                        continue;
                                }
                                else {
                                    $regionId = 0;
                                }

                                $shippingLocationTable->insert(array(
                                    'reward_id' => $rewardModel->getIdentity(),
                                    'project_id' => $rewardModel->project_id,
                                    'region_id' => $regionId,
                                    'amount' => $shippingAmount[$key],
                                ));
                            }
                        }
                    }
                } else {
                    $rewardModel->title = $values['title'];
                    $rewardModel->description = $values['description'];
                    $rewardModel->save();
                }
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function manageAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET PROJECT ID
        $project_id = $this->_getParam('project_id');

        //GET PROJECT ITEM
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($isCreatePrivacy))
            $this->respondWithError('unauthorized', "You don't have permission to edit Reward.");

        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized', "You don't have permission to edit Reward.");
        }

        try {

            //SET PROJECT SUBJECT
            Engine_Api::_()->core()->setSubject($project);
            $rewards = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewards($project_id, 0);

            $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
            $rewardCount = 0;
            $rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->query()->fetchColumn();
            foreach ($rewards as $reward) {
                $browseReward = $reward->toArray();
                if((((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '3.5') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '2.6.1'))){
                    $browseReward['pledge_amount'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($reward->pledge_amount,1);
                }
                else {
                    $browseReward['pledge_amount'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($reward->pledge_amount);
                    $browseReward['pledge_amount'] = $browseReward['pledge_amount']. " or more";
                } 
                $browseReward['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($reward);
                $browseReward['delivery_date'] = date('F Y', strtotime($reward->delivery_date));
                if ($reward->quantity) {
                    $quantity = $reward->quantity;
                    $browseReward['remaining_reward'] = $quantity - $reward->spendRewardQuantity();
                }

                if (!empty($getContentImages))
                    $browseReward = array_merge($browseReward, $getContentImages);

                $menus = array();
                if ($editPrivacy) {
                    $menus[] = array(
                        'label' => $this->translate('Edit Reward'),
                        'name' => 'edit',
                        'url' => 'crowdfunding/reward/edit/' . $project_id,
                        "urlParams" => array(
                            "reward_id" => $reward->getIdentity()
                        ),
                        "actionType" => "edit",
                        "dialogueTitle" => $this->translate("Edit Reward"),
                        "successMessage" => $this->translate("Reward Edited successfuly.")
                    );
                }
                if ($reward->spendRewardQuantity() <= 0) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Reward'),
                        'name' => 'delete',
                        'url' => 'crowdfunding/reward/delete/' . $project_id,
                        "urlParams" => array(
                            "reward_id" => $reward->getIdentity()
                        ),
                        "actionType" => "alertDialog",
                        "dialogueMessage" => $this->translate("Do you want to delete this Reward?"),
                        "dialogueTitle" => $this->translate("Delete Reward"),
                        "dialogueButton" => $this->translate("Delete"),
                        "successMessage" => $this->translate("Reward Deleted successfuly."),
                    );
                }
                
                $browseReward['menu'] = $menus;


                $response['response'][] = $browseReward;
            }
            $response['canCreate'] = $isCreatePrivacy;
            $response['totalItemCount'] = $rewardCount;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function viewShippingLocationsAction() {
        $this->validateRequestMethod();
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET PROJECT ID
        $reward_id = $this->_getParam('reward_id');

        //GET PROJECT ITEM
        $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);
        if (empty($reward))
            $this->respondWithError('no_record');

        if ($reward->shipping_method != 2 && $reward->shipping_method != 3)
            $this->respondWithError('unauthorized', "Shipping details are not available.");

        $viewer = Engine_Api::_()->user()->getViewer();

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $reward->project_id);
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized', "You don't have permission to view.");
        }
        $locations = $reward->findShippingLocations($reward->project_id);
        $response['title']['label'] = $this->translate('Title');
        $response['title']['value'] = $reward->getTitle();

        $response['pledge_amount']['label'] = $this->translate('Backed Amount');
        $response['pledge_amount']['value'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($reward->pledge_amount);


        $quantity = $reward->quantity;
        if ($quantity) {
            $remainingRewards = $quantity - $reward->spendRewardQuantity();
            $response['quantity']['label'] = $this->translate('Limited Rewards');
            $response['quantity']['value'] = $remainingRewards . " left out of " . $quantity;
        }
        $response['delivery_date']['label'] = $this->translate('Estimated Delivery');
        $response['delivery_date']['value'] = date('F Y', strtotime($reward->delivery_date));
        $response['description']['label'] = $this->translate('Description');
        $response['description']['value'] = $reward->description;
        $shippingDetails = array();
        foreach ($locations as $location) {
            if (empty($location['region_id'])) {
                $shippingDetails['shipping_charge']['label'] = $this->translate('Rest of World');
            } else {
                $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $location['region_id']);
                $shippingDetails['shipping_charge']['label'] = $region->country_name;
            }
            $shippingDetails['shipping_charge']['value'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($location['amount']);
            $response['shipping_details'][] = $shippingDetails;
        }
        $bodyresponse['response'] = $response;
        $this->respondWithSuccess($bodyresponse, true);
    }

    public function deleteAction() {
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $reward_id = $this->_getParam('reward_id');
        $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);
        if (empty($reward))
            $this->respondWithError('no_record');

        if ($reward->spendRewardQuantity() <= 0) {
            $reward->delete();
            $this->successResponseNoContent('no_content', true);
        }
    }

    function shippingCharge($values) {
        $countriesArray = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAllRegionsCountryArray();
        $locale = Engine_Api::_()->getApi('Core', 'siteapi')->getLocal();
        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
        $location = array();
        $locationArray = array();
        $shippingAmount = array();
        foreach ($countriesArray as $country) {
            $key = $country['country'];
            if (!array_key_exists($key, $countries)) {
                continue;
            }
            $location[$key] = $countries[$key];
            $val_key = "shhipping_charge_" . $key;
            if (array_key_exists($val_key, $values)) {
                $locationArray[] = $key;
                $shippingAmount[] = $values[$val_key];
            }
        }
        if (isset($values['shipping_method']) && $values['shipping_method'] == 3) {
            if (!empty($values['rest_world'])) {
                $locationArray[] = "1";
                $shippingAmount[] = $values['rest_world'];
            }
        }
        $shippingCharge = array(
            "locationsArray" => $locationArray,
            "shippingAmountsArray" => $shippingAmount
        );

        return $shippingCharge;
    }

}

?>
