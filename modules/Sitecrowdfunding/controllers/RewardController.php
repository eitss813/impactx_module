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
class Sitecrowdfunding_RewardController extends Seaocore_Controller_Action_Standard {

    public function init() {

        //SET THE SUBJECT
        if (0 !== ($reward_id = (int) $this->_getParam('reward_id')) && null !== ($reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($reward);
        }
    }

    //THIS ACTION USED TO CREATE A REWARD
    public function createAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');

        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_rewards';
        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($isCreatePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //MAKE FORM
        $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
        $rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->query()->fetchColumn();
        $rewardCount++;
        $this->view->form = $form = new Sitecrowdfunding_Form_Reward_Create(array(
            'reward_number' => $rewardCount,
            'layoutType' => $layoutType
        ));
        $countriesArray = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAllRegionsCountryArray();
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
        $location = array();
        foreach ($countriesArray as $country) {
            $key = $country['country'];
            if (!array_key_exists($key, $countries)) {
                continue;
            }
            $location[$key] = $countries[$key];
        }
        $this->view->location = $location;
        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "rewards";
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $dlvDate = explode("-", $values['delivery_date']);
            $deliveryDate = date('Y-m-d', strtotime($values['delivery_date']));
            if (strtotime(date('Y-m-d')) > strtotime($deliveryDate)) {
                //throw exception
                $error = $this->view->translate('Delivery date must be greater than current date.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            } elseif (count($dlvDate) != 3 || empty($dlvDate[0]) || empty($dlvDate[1])) {
                //throw exception
                $error = $this->view->translate('Enter the valid delivery date.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
            if (empty($values['shipping_method'])) {
                //throw exception
                $error = $this->view->translate('Please select the shipping method.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
            if (empty($value['limit'])) {
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
                if (!empty($values['photo'])) {
                    $rewardModel->setPhoto($form->photo);
                }
                if (($rewardModel->shipping_method) > 1) {
                    $locationArray = isset($_POST['locationsArray']) ? $_POST['locationsArray'] : array();
                    $shippingAmount = isset($_POST['shippingAmountsArray']) ? $_POST['shippingAmountsArray'] : array(); 

                    if (count($locationArray) > 0 && count($shippingAmount) > 0) {
                        foreach ($locationArray as $key => $location) {
                            //LOCATION AND SHIPPING CHARGE SHOULD NOT BE BLANK
                            if ($location == 0 && count($locationArray) == 1 && empty($shippingAmount[$key]) || !empty($location) && empty($shippingAmount[$key])) {
                                //throw exception
                                $error = $this->view->translate('Please fill location and shipping charge.');
                                $error = Zend_Registry::get('Zend_Translate')->_($error);
                                $form->getDecorator('errors')->setOption('escape', false);
                                $form->addError($error);
                                return;
                            }

                            //TAKING COUNTRIES OBJECT
                            $locale = Zend_Registry::get('Zend_Translate')->getLocale();
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
            } catch (Exception $ex) {
                $db->rollBack();
                throw $ex;
            }

            if($layoutType == 'fundingDetails'){
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Added Successfully'))
                ));
            }else{
                return $this->_helper->redirector->gotoRoute(array('module' => 'sitecrowdfunding', 'action' => 'manage', 'controller' => 'reward', 'project_id' => $project_id), '', true);
            }

        }
    }

    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->reward_id = $reward_id = $this->_getParam('reward_id');
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_rewards';
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);
        $shippingLocations = array();
        if ($reward->shipping_method > 1)
            $shippingLocations = $reward->findShippingLocations($project_id);
        $selectedCountries = array();
        $countriesAmount = array();
        foreach ($shippingLocations as $location) {
            $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $location['region_id']);
            $selectedCountries[] = $region['country'];
            $countriesAmount[] = $location['amount'];
        }

        $this->view->selectedCountries = $selectedCountries;
        $this->view->countriesAmount = $countriesAmount;
        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        if (empty($reward)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($isCreatePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
        $rewardCount = 0;
        $rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->where('reward_id < ?', $reward->reward_id)->query()->fetchColumn();
        $rewardCount++;
        $this->view->form = $form = new Sitecrowdfunding_Form_Reward_Edit(
                array('reward_number' => $rewardCount, 'item' => $reward));
        $countriesArray = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAllRegionsCountryArray();
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
        $location = array();
        foreach ($countriesArray as $country) {
            $key = $country['country'];
            if (!array_key_exists($key, $countries)) {
                continue;
            }
            $location[$key] = $countries[$key];
        }
        $this->view->location = $location;

        $reward->delivery_date = date('Y-m-d', strtotime($reward->delivery_date));
        $populatedArray = $formpopulate_array = $reward->toArray();
        $form->populate($populatedArray);
        $this->view->disableFields = ($reward->spendRewardQuantity() > 0) ? 1 : 0;
        //ALLOW ADMINS TO EDIT ALL INFORMATION OF THE REWARDS
        if ($viewer->isAdminOnly()) {
            $this->view->disableFields = 0;
        }


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;
            //SET PHOTO
            if (!empty($values['photo'])) {
                $reward->setPhoto($form->photo);
            }
            if ($reward->spendRewardQuantity() == 0) {
                $deliveryDate = date('Y-m-d', strtotime($values['delivery_date']));
                if (strtotime(date('Y-m-d')) > strtotime($deliveryDate)) {
                    //throw exception
                    $error = $this->view->translate('Delivery date must be greater than current date.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
                if (empty($values['shipping_method'])) {
                    //throw exception
                    $error = $this->view->translate('Please select the shipping method.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
                if (empty($value['limit'])) {
                    $value['quantity'] = 0;
                } else {
                    if (empty($value['quantity']) || $value['quantity'] < 0) {
                        $value['quantity'] = 0;
                    }
                }
            }

            $value['quantity'] = (int) $value['quantity'];
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
                        $locationArray = isset($_POST['locationsArray']) ? $_POST['locationsArray'] : array();
                        $shippingAmount = isset($_POST['shippingAmountsArray']) ? $_POST['shippingAmountsArray'] : array(); 
                         if (count($locationArray) > 0 && count($shippingAmount) > 0) {
                            foreach ($locationArray as $key => $location) {
                                //LOCATION AND SHIPPING CHARGE SHOULD NOT BE BLANK

                                if ($location == 0 && count($locationArray) == 1 && empty($shippingAmount[$key]) || !empty($location) && empty($shippingAmount[$key])) {
                                    //throw exception
                                    $error = $this->view->translate('Please fill location and shipping charge.');
                                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                                    $form->getDecorator('errors')->setOption('escape', false);
                                    $form->addError($error);
                                    return;
                                }
                                //TAKING COUNTRIES OBJECT
                                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
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
            } catch (Exception $ex) {
                $db->rollBack();
                throw $ex;
            }
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $url = $view->url(array('controller' => 'reward', 'action' => 'manage', 'project_id' => $project_id), "", true);


            if($layoutType == 'fundingDetails'){
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edited Successfully'))
                ));
            }else{
                return $this->_helper->redirector->gotoRoute(array('module' => 'sitecrowdfunding', 'action' => 'manage', 'controller' => 'reward', 'project_id' => $project_id), '', true);
            }

        }
    }

    public function manageAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID
        $this->view->project_id = $project_id = $this->_getParam('project_id');

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "reward_create");
        if (empty($isCreatePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "rewards";
        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($project);
        $this->view->rewards = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewards($project_id, 0);

        $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
        $this->view->rewardCount = 0;
        $this->view->rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->query()->fetchColumn();
    }

    public function viewShippingLocationsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID
        $this->view->reward_id = $reward_id = $this->_getParam('reward_id');

        //GET PROJECT ITEM
        $this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);

        if ($reward->shipping_method != 2 && $reward->shipping_method != 3)
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $reward->project_id);
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        $this->view->locations = $reward->findShippingLocations($reward->project_id);

        $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
        $this->view->rewardCount = 0;
        $this->view->rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $reward->project_id)->where('reward_id < ?', $reward->reward_id)->query()->fetchColumn();
        $this->view->rewardCount++;
    }

    public function deleteAction() {
        $this->view->reward_id = $reward_id = $this->_getParam('reward_id');



        if ($this->getRequest()->isPost()) {
            $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);
            if ($reward->spendRewardQuantity() <= 0) {
                $reward->delete();
                $this->_forward('success', 'utility', 'core', array(
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Succesfully.')),
                ));
            }
        }
    }

}
