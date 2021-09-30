<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGeneralController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminGeneralController extends Core_Controller_Action_Admin {

    //ACTION FOR MAKING THE PROJECT FEATURED/UNFEATURED
    public function featuredAction() {
        $project_id = $this->_getParam('project_id');
        if (!empty($project_id)) {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $project->featured = !$project->featured;
            $project->save();
        }
        $this->_redirect('admin/sitecrowdfunding/manage');
    }

    //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
    public function sponsoredAction() {

        $project_id = $this->_getParam('project_id');
        if (!empty($project_id)) {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $project->sponsored = !$project->sponsored;
            $project->save();
        }
        $this->_redirect('admin/sitecrowdfunding/manage');
    }

    //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
    public function sponsoredCategoryAction() {

        $category_id = $this->_getParam('category_id');
        if (!empty($category_id)) {
            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id);
            $category->sponsored = !$category->sponsored;
            $category->save();
        }
        $this->_redirect('admin/sitecrowdfunding/settings/categories');
    }

    //ACTION FOR MAKING THE PROJECT APPROVE/DIS-APPROVE
    public function approvedAction() {

        $project_id = $this->_getParam('project_id');
        $is_funding = $this->_getParam('is_funding');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $owner = $project->getOwner();
            $sender = Engine_Api::_()->user()->getViewer();
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            if($is_funding){
                $project->funding_approved = 1;
                if (empty($project->funding_approved_date))
                    $project->funding_approved_date = date('Y-m-d H:i:s');
                $project->funding_state = 'published';
                $project->funding_status = 'active';
                // if funding is approved means normal project also approved status
                if (empty($project->approved_date))
                    $project->approved_date = date('Y-m-d H:i:s');
                $project->approved = 1;
                $project->state = 'published';
                $project->status = 'active';
                if($settings->getSetting('sitecrowdfunding.reminder.project.funding.approval', 0)){
                    // if funding is approved means normal project also approved status
                    Engine_Api::_()->sitecrowdfunding()->sendMailCustom('FUNDING_APPROVED', $project_id);
                }
                if($settings->getSetting('sitecrowdfunding.notification.project.funding.approval', 0)) {
                    //SEND NOTIFICATION TO PROJECT OWNER
                    $type = 'sitecrowdfunding_project_funding_approved';
                    $notifyApi->addNotification($owner, $sender, $project, $type);
                }
            }else{
                $project->approved = 1;
                if (empty($project->approved_date))
                    $project->approved_date = date('Y-m-d H:i:s');
                $project->state = 'published';
                $project->status = 'active';
                if($settings->getSetting('sitecrowdfunding.reminder.project.approval', 0)) {
                    // if normal project approved means no need to do anything
                   Engine_Api::_()->sitecrowdfunding()->sendMailCustom('APPROVED', $project_id);
                }
                if($settings->getSetting('sitecrowdfunding.notification.project.approval', 0)) {
                    //SEND NOTIFICATION TO PROJECT OWNER
                    $type = 'sitecrowdfunding_project_approved';
                    $notifyApi->addNotification($owner, $sender, $project, $type);
                }
            }
            $project->save();

            if($is_funding){
                if($settings->getSetting('sitecrowdfunding.activity.project.approval', 0)) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_funding');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                    }
                }
            }else{
                if($settings->getSetting('sitecrowdfunding.activity.project.funding.approval', 0)) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                    }
                }
            }

            $currentDate = date('Y-m-d H:i:s');
            if ($project->funding_state == 'published' && $project->funding_approved && $project->is_gateway_configured && $project->funding_start_date <= $currentDate) {
//                $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
//                if (!empty($enable_Facebooksefeed)) {
//                    $sitecrowdfunding_array = array();
//                    $sitecrowdfunding_array['type'] = 'sitecrowdfunding_project_new';
//                    $sitecrowdfunding_array['object'] = $project;
//                    Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitecrowdfunding_array);
//                }

                //SEND MAIL TO PROJECT OWNER
                //Engine_Api::_()->sitecrowdfunding()->sendMail('APPROVED', $project_id);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/sitecrowdfunding/manage');
    }

    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');

        $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');
        $select = $categoriesTable->select()
                ->from($categoriesTable->info('name'), array('category_id', 'category_name'))
                ->where("$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }
        $categoriesData = $categoriesTable->fetchAll($select);
        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $category->category_name;
                $data['category_id'] = $category->category_id;
                $categories[] = $data;
            }
        }
        $this->view->categories = $categories;
    }

    public function refundAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecrowdfunding_project_payments', array('project_id' => $project_id, 'payment_type' => 'refund'));

            $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Refund process has been started.')),
            ));
        }
    }

    public function payoutAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitecrowdfunding_project_payments', array('project_id' => $project_id, 'payment_type' => 'payout'));
            $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payout process has been started.')),
            ));
        }
    }

    public function backerPayoutAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $backer_id = $this->_getParam('backer_id');
        $gateway_id = $this->_getParam('gateway_id');
        $data = '';
        if ($this->getRequest()->isPost()) {
            $gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $gateway_id);
            $params['resource_id'] = $backer_id;
            $params['resource_type'] = 'sitecrowdfunding_backer';
            if (strtolower($gateway->title) == 'mangopay') {
                $data = $gateway->getPlugin()->payoutTransaction($params);
            }
            $message = $data['message'];
            $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_("%s", $message))
            ));
        }
    }

    public function backerRefundAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $backer_id = $this->_getParam('backer_id');
        $gateway_id = $this->_getParam('gateway_id');
        $project_id = $this->_getParam('project_id');
        $data = '';
        if ($this->getRequest()->isPost()) {
            $gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $gateway_id);
            $params['resource_id'] = $backer_id;
            $params['resource_type'] = 'sitecrowdfunding_backer';
            $params['project_id'] = $project_id;
            if (strtolower($gateway->title) == 'mangopay') {
                $data = $gateway->getPlugin()->refundTransaction($params);
            }
            $message = $data['message'];
            $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_($message))
            ));
        }
    }

    //ACTION FOR DELETE THE PROJECT
    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $this->view->project_id = $project_id = $this->_getParam('project_id');

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-general/delete.tpl');
    }

}
