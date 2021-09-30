<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AnnouncementController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AnnouncementController extends Core_Controller_Action_Standard {

    public function init() {

        $project_id = $this->_getParam('project_id');

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $viewer = Engine_Api::_()->user()->getViewer();

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
            return;

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.announcement', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
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

        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "announcements";
        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($project);
        $fetchColumns = array('announcement_id', 'title', 'body', 'startdate', 'expirydate', 'status');
        $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitecrowdfunding')->announcements($project_id, 1, 0, $fetchColumns);
    }

    public function createAction() {

        //GETTING THE OBJECT AND GROUP ID AND RESOURCE TYPE.
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $this->view->project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_announcements';

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "announcements";
        $announcementsTable = Engine_Api::_()->getDbTable('announcements', 'sitecrowdfunding');

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Announcement_Create();
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values['project_id'] = $project_id;
                $announcement = $announcementsTable->createRow();
                $announcement->setFromArray($values);
                $announcement->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'announcement', 'action' => 'manage', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }
    }

    public function editAction() {

        $announcement_id = $this->_getParam('announcement_id', null);
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $this->view->project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "announcements";
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_announcements';

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Announcement_Edit();
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        //SHOW PRE-FIELD FORM 
        $announcement = Engine_Api::_()->getItem('sitecrowdfunding_announcement', $announcement_id);
        $resultArray = $announcement->toArray();

        $resultArray['startdate'] = $resultArray['startdate'] . ' 00:00:00';
        $resultArray['expirydate'] = $resultArray['expirydate'] . ' 00:00:00';

        //IF NOT POST OR FORM NOT VALID THAN RETURN AND POPULATE THE FROM.
        if (!$this->getRequest()->isPost()) {
            $form->populate($resultArray);
            return;
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $announcement->setFromArray($values);
            $announcement->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array('controller' => 'announcement', 'action' => 'manage', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
    } 
    public function deleteAction() { 
        $this->view->announcement_id = $announcement_id = $this->_getParam('announcement_id');

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('sitecrowdfunding_announcement', $announcement_id)->delete();
            $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Succesfully.')),
            ));
        } 
    }

}