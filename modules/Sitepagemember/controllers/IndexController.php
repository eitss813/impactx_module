<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_IndexController extends Seaocore_Controller_Action_Standard {

    public function init() {

        //GET PAGE ID
        $page_id = $this->_getParam('page_id');

        //PACKAGE BASE PRIYACY START
        if (!empty($page_id)) {

            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

            $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
            if (empty($allowPage)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }
    }

    //ACTION FOR PAGE MEMBER HOME.
    public function homeAction() {

        //CHECK VIEW PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
            return;

        //CHECK THE VERSION OF THE CORE MODULE
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR MEMBER BROWSE PAGE.
    public function browseAction() {

        //CHECK VIEW PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
            return;

        $sitepagememberBrowse = Zend_Registry::isRegistered('sitepagememberBrowse') ? Zend_Registry::get('sitepagememberBrowse') : null;
        if (empty($sitepagememberBrowse))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        //CHECK THE VERSION OF THE CORE MODULE
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR HIGHLIGHTED MEMBER.
    public function highlightedAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET MEMBER ID AND OBJECT
        $this->view->member_id = $member_id = $this->_getParam('member_id');
        $sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->getMembersObject($member_id);

        $page_id = $sitepagemember->page_id;
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        $this->view->highlighted = $sitepagemember->highlighted;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $tab_selected_id = $this->_getParam('tab');

        if ($viewer_id == $sitepagemember->user_id || !empty($this->view->canEdit)) {

            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('membership', 'sitepage')->getAdapter();
            $db->beginTransaction();

            try {
                if ($sitepagemember->highlighted == 0) {
                    $sitepagemember->highlighted = 1;
                } else {
                    $sitepagemember->highlighted = 0;
                }
                $sitepagemember->save();

                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        if ($sitepagemember->highlighted) {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Member has been successfully made highlighted.'));
        } else {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Member has been successfully made un-highlighted.'));
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirectTime' => '2',
            'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
            'format' => 'smoothbox',
            'messages' => ''
        ));
    }

    //ACTION FOR REMOVE THE MEMBER FROM A PAGE.
    public function removeAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET MEMBER ID AND OBJECT
        $this->view->member_id = $member_id = $this->_getParam('member_id');
        $sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->getMembersObject($member_id);
        $this->view->params = $this->_getParam('params', null);

        $page_id = $sitepagemember->page_id;
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //$this->view->active = $sitepagemember->active;
        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $tab_selected_id = $this->_getParam('tab');

        if ($viewer_id == $sitepagemember->user_id || !empty($this->view->canEdit)) {
            $this->view->permission = true;
            $this->view->success = false;

            if (!empty($member_id)) {

                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('member_id =?' => $member_id, 'page_id =?' => $page_id));
                Engine_Api::_()->sitepage()->updateMemberCount($sitepage);

                //DELETE ACTIVITY FEED OF JOIN PAGE ACCORDING TO USER ID.        
                $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'sitepage_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $page_id));
                if (!empty($action_id)) {
                    $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
                    if ($action instanceof Core_Model_Item_Abstract) {
                        $action->delete();
                    }
                }
            }
        } else {
            $this->view->permission = false;
        }
        return $this->_forwardCustom('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully removed.')),
            'layout' => 'default-simple',

            'parentRefresh' => true,
        ));
//        $this->_forwardCustom('success', 'utility', 'core', array(
//            'smoothboxClose' => 500,
//            'parentRedirect' => $this->_helper->url->url(array('action' => 'manage-members', 'page_id' => $page_id), 'sitepage_dashboard', true),
//            'parentRedirectTime' => '2',
//            'format' => 'smoothbox',
//            'messages' => ''
//        ));
    }

    //REMPVE PAGE COVER PHOTO.
    public function removeCoverphotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $page_id = $this->_getParam('page_id');
        if ($this->getRequest()->isPost()) {

            Engine_Api::_()->getDbtable('pages', 'sitepage')->update(array('page_cover' => 0), array('page_id =?' => $page_id));

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    //ACTION FOR APPROVE THE MEMBER WHO SEND REQUEST FOR JOIN PAGE.
    public function approveAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET MEMBER ID AND OBJECT
        $this->view->member_id = $this->_getParam('member_id');
        $sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->getMembersObject($this->_getParam('member_id'));
        $this->view->active = $sitepagemember->active;
        $this->view->user_approved = $sitepagemember->user_approved;

        $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagemember->page_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $tab_selected_id = $this->_getParam('tab');

        if ($viewer_id == $sitepagemember->user_id || !empty($this->view->canEdit)) {

            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('membership', 'sitepage')->getAdapter();
            $db->beginTransaction();

            try {

                if ($sitepagemember->active == 0 && $sitepagemember->user_approved == 0) {
                    $sitepagemember->active = 1;
                    $sitepagemember->user_approved = 1;
                    $sitepagemember->resource_approved = 1;
                }
                $sitepagemember->save();

                $user = Engine_Api::_()->getItem('user', $sitepagemember->user_id);

                //GET PAGE TITLE
                $pagetitle = $sitepage->title;

                //PAGE URL
                $page_url = Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id);

                //GET PAGE URL
                $page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);

                //MAKING PAGE TITLE LINK
                $page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';

                //EMAIL THAT GOES TO NEW OWNER
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEPAGEMEMBER_APPROVE_EMAIL', array(
                    'page_title' => $pagetitle,
                    'page_title_with_link' => $page_title_link,
                    'object_link' => $page_baseurl,
                    'email' => $email,
                    'queue' => true
                ));

                //MEMBER APPROVED NOTIFICATION.
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $sitepage, 'sitepagemember_accepted');

                //ADD ACTIVITY
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($user, $sitepage, 'sitepage_join');
                Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action, true);
                //Member count increase when member join the page.
                Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
                $sitepage->save();

                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }
    }

    //ACTION FOR REJECT PAGE MEMBER REQUEST.
    public function rejectAction() {

        //GET THE PAGE ID AND MEMBER ID AND USER ID
        $page_id = $this->_getParam('page_id');
        $member_id = $this->_getParam('member_id');
        $user_id = $this->_getParam('user_id');

        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //CHECK AUTH
        if (!$this->_helper->requireUser()->isValid())
            return;

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Member();

        $form->submit->setLabel('Reject Invitation');
        $form->setTitle('Reject Page Invitation');
        $form->setDescription('Would you like to reject the invitation to this page?');

        //PROCESS FORM
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
            return;
        }

        // Process
        //$viewer = Engine_Api::_()->user()->getViewer();
        //Set the request as handled
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_type =?' => 'sitepage_page', 'object_id =?' => $page_id, 'subject_id =?' => $user_id));

        if (!empty($page_id)) {
            //DELETE THE RESULT FORM THE TABLE.
            Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('page_id =?' => $page_id, 'member_id =?' => $member_id));

            //Member count decrease when member join the page.
            $sitepage->member_count--;
            $sitepage->save();
        }
    }

    public function createAnnouncementAction() {

        //GETTING THE OBJECT AND PAGE ID AND RESOURCE TYPE.
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->sitepages_view_menu = 30;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitebusiness_main');
        $announcementsTable = Engine_Api::_()->getDbTable('announcements', 'sitepage');

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Announcement_Create();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values['page_id'] = $page_id;
                $announcement = $announcementsTable->createRow();
                $announcement->setFromArray($values);
                $announcement->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('action' => 'announcements', 'page_id' => $page_id), 'sitepage_dashboard', true);
        }
    }

    public function editAnnouncementAction() {

        $announcement_id = $this->_getParam('announcement_id', null);
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->sitepages_view_menu = 30;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitebusiness_main');
        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Announcement_Edit();

        //SHOW PRE-FIELD FORM 
        $announcement = Engine_Api::_()->getItem('sitepage_announcements', $announcement_id);
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
        return $this->_helper->redirector->gotoRoute(array('action' => 'announcements', 'page_id' => $page_id), 'sitepage_dashboard', true);
    }

    public function deleteAnnouncementAction() {

        //GET THE CONTENT ID AND RESOURCE TYPE.
        $announcement_id = (int) $this->_getParam('announcement_id');
        $page_id = $this->_getParam('page_id');
        Engine_Api::_()->getDbtable('announcements', 'sitepage')->delete(array('announcement_id = ?' => $announcement_id, 'page_id = ?' => $page_id));
        exit();
    }

    public function notificationSettingsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');

        //GET PAGE ID
        $member_id = $this->_getParam('member_id');
        $sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->getMembersObject($member_id);
        $page_id = $sitepagemember->page_id;
        $user_id = $sitepagemember->user_id;

        //SET FORM
        $this->view->form = $form = new Sitepagemember_Form_NotificationSettings();

        $results = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($user_id, $page_id);

        //EMAIL NOTIFICATION WORK
        $emailSettings = 0;
        $notificationSettings = 0;
        $this->view->email = $value['email'] = $results["email"];
        $action_email = json_decode($results['action_email']);
        if ($action_email) {
            $value['emailcreated'] = $action_email->emailcreated;
            $value['emailposted'] = $action_email->emailposted;
            if ($value['emailcreated'] == 0 && $value['emailcreated'] == 0) {
                $emailSettings = 1;
            }
        }

        //ONLY NOTIFICATION WORK
        $this->view->notification = $value['notification'] = $results["notification"];
        $action_notification = json_decode($results['action_notification']);
        if ($action_notification) {
            $value['notificationcreated'] = $action_notification->notificationcreated;
            $value['notificationposted'] = $action_notification->notificationposted;
            $value['notificationfollow'] = $action_notification->notificationfollow;
            $value['notificationlike'] = $action_notification->notificationlike;
            $value['notificationcomment'] = $action_notification->notificationcomment;
            $value['notificationjoin'] = $action_notification->notificationjoin;
            if ($value['notificationcreated'] == 0 && $value['notificationposted'] == 0 && $value['notificationfollow'] == 0 && $value['notificationlike'] == 0 && $value['notificationcomment'] == 0 && $value['notificationjoin'] == 0) {
                $notificationSettings = 1;
            }
        }
        $this->view->notificationSettings = $notificationSettings;
        $this->view->emailSettings = $emailSettings;
        //$value['action_notification'] = json_decode($results['action_notification']);

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES

            $values = $form->getValues();

            //EMAIL NOTIFICATION work
            $tempArray['emailposted'] = $values['emailposted'];
            $tempArray['emailcreated'] = $values['emailcreated'];
            $action_email = json_encode($tempArray);

            //$action_notification = json_encode($values['action_notification']);
            //only NOTIFICATION work
            $tempNotificationArray['notificationposted'] = $values['notificationposted'];
            $tempNotificationArray['notificationcreated'] = $values['notificationcreated'];
            $tempNotificationArray['notificationfollow'] = $values['notificationfollow'];
            $tempNotificationArray['notificationlike'] = $values['notificationlike'];
            $tempNotificationArray['notificationcomment'] = $values['notificationcomment'];
            $tempNotificationArray['notificationjoin'] = $values['notificationjoin'];
            $action_notification = json_encode($tempNotificationArray);

            if (isset($values['email'])) {

                //MANAGEADMIN TABLE UPDATE WHEN ANY MEMBER UPDATE EMAIL AND NOTIFICATION SETTINGS FROM THE MEMBER TAB.
                $email = array();
                if ($values['emailposted'] == 1)
                    $email[] = 'posted';
                if ($values['emailcreated'] == 1)
                    $email[] = 'created';
                Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->update(array('email' => $values['email'], 'action_email' => json_encode($email)), array('page_id =?' => $page_id, 'user_id =?' => $user_id));

                //UPDATE WHEN ANY MEMBERSHIP UPDATE EMAIL AND NOTIFICATION SETTINGS FROM THE MEMBER TAB.
                Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('email' => $values['email'], 'action_email' => $action_email), array('resource_id =?' => $page_id, 'user_id =?' => $user_id));
            }

            if (isset($values['notification'])) {
                $notification = array();
                if ($values['notificationposted'] == 1)
                    $notification[] = 'posted';
                if ($values['notificationcreated'] == 1)
                    $notification[] = 'created';
                if ($values['notificationfollow'] == 1)
                    $notification[] = 'follow';
                if ($values['notificationlike'] == 1)
                    $notification[] = 'like';
                if ($values['notificationcomment'] == 1)
                    $notification[] = 'comment';
                if ($values['notificationjoin'] == 1)
                    $notification[] = 'join';
                Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->update(array('notification' => $values['notification'], 'action_notification' => serialize($notification)), array('page_id =?' => $page_id, 'user_id =?' => $user_id));

//         if($values['notification'] == 1) {
//         //, 'action_notification' => $action_notification
// 					//$action_notification = 'a:5:{i:0;s:6:"posted";i:1;s:7:"created";i:2;s:7:"comment";i:3;s:4:"like";i:4;s:6:"follow";}';
// 					Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->update(array('notification'=> '1'), array('page_id =?' => $page_id, 'user_id =?' => $user_id));
// 					
// 					//Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('notification'=> $values['notification'], 'action_notification' => $action_notification), array('page_id =?' => $page_id, 'user_id =?' => $user_id));
//         }

                Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('notification' => $values['notification'], 'action_notification' => $action_notification), array('page_id =?' => $page_id, 'user_id =?' => $user_id));
            }


            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->_getParam('tab')), 'sitepage_entry_view', true),
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Notification settings have been saved successfully.'))
            ));
        }
    }

    //ACTION FOR EDIT TITLE OF PAGE MEMBER.
    public function editAction() {

        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;

        $member_id = $this->_getParam('member_id');
        $page_id = $this->_getParam('page_id');

        $table = Engine_Api::_()->getDbtable('membership', 'sitepage');

        //MAKE FORM
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->form = $form = new Sitepagemember_Form_Edit();
        } else {
            $this->view->form = $form = new Sitepagemember_Form_SitemobileEdit();
        }

        $table = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $tablename = $table->info('name');
        $select = $table->select()
                ->from($table->info('name'), array('title', 'date', 'role_id'))
                ->where($tablename . '.member_id = ?', $member_id);
        $result = $table->fetchRow($select)->toArray();

        if (!$this->getRequest()->isPost()) {
            $form->populate(array(
                'role_id' => json_decode($result['role_id'])
                    //'title' => $result['title'],
            ));
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        //FOR DATE WORK.
        if (!empty($values['year']) || !empty($values['month']) || !empty($values['day'])) {
            $member_date = $values['year'] . '-' . (int) $values['month'] . '-' . (int) $values['day'];
        }

        //BEGIN TRANSACTION
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {

            if (isset($values['role_id'])) {
                $roleName = array();
                foreach ($values['role_id'] as $role_id) {
                    $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRoleName($role_id);
                }
                $roleTitle = json_encode($roleName);
                $roleIDs = json_encode($values['role_id']);
                $table->update(array('title' => $roleTitle, 'role_id' => $roleIDs), array('member_id =?' => $member_id));
            }

            if (!empty($member_date)) {
                $table->update(array('date' => $member_date), array('member_id =?' => $member_id));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->_getParam('tab')), 'sitepage_entry_view', true),
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your membership information has been edited successfully.')),
        ));
    }

    //ACTIO FOR REQUEST MEMBER
    public function requestMemberAction() {

        $values = array();
        $page_id = $values['page_id'] = $this->_getParam('page_id');
        $this->view->tab_selected_id = $this->_getParam('tab');
        $this->view->paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getSitepagemembersPaginator($values, 'request');
    }

    //ACTION FOR JOIN PAGE.
    public function pageJoinAction() {

        $this->view->user_id = $user_id = $this->_getParam('user_id');

        //GET THE FRIEND ID AND OBJECT OF USER.
        $this->view->showViewMore = $this->_getParam('showViewMore', 0);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($user_id, 'pageJoin');

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->count = $paginator->getTotalItemCount();
    }

    //ACTION FOR MEMBER JOIN THE PAGE.
    public function memberJoinAction() {

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->showViewMore = $this->_getParam('showViewMore', 0);
        $memberJoin = $this->_getParam('params', null);
        if ($memberJoin == 'memberJoin') {
            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id, '', '', 0);
        } else {
            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id);
        }

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->count = $paginator->getTotalItemCount();
    }

    //ACTION FOR EDIT TITLE OF PAGE MEMBER.
    public function edittitleAction() {

        $member_id = $this->_getParam('member_id');
        $page_id = $this->_getParam('page_id');
        $str_temp = $this->_getParam('str_temp');
        $table = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $table->update(array('title' => $str_temp), array('member_id =?' => $member_id));
        exit();
    }

    //USE FOR COMPOSE THE MESSAGE.
    public function composeAction() {

        $multi = 'member';
        $multi_ids = '';

        $tab_selected_id = $this->_getParam('tab');

        $this->view->resource_id = $resource_id = $this->_getParam("resource_id");
        
        $temproleParamsArray = $this->_getParam("roleParamsArray");
        
        parse_str($temproleParamsArray , $roleParamsArray);
        
        $this->view->roleParamsArray = $roleParamsArray;

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Sitepagemember_Form_Compose();
        
        $form->roles_id->addMultiOptions($roleParamsArray);

        $form->removeElement('to');
        $form->setDescription('Create your new message with the form below.');

        $friends = Engine_Api::_()->user()->getViewer()->membership()->getMembers();
        $data = array();

        foreach ($friends as $friend) {
            $friend_photo = $this->view->itemPhoto($friend, 'thumb.icon');
            $data[] = array('label' => $friend->getTitle(), 'id' => $friend->getIdentity(), 'photo' => $friend_photo);
        }

        $data = Zend_Json::encode($data);
        $this->view->friends = $data;

        //ASSIGN THE COMPOSING STUFF.
        $composePartials = array();
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {
            if (empty($data['composer']))
                continue;
            foreach ($data['composer'] as $type => $config) {
                $composePartials[] = $config['script'];
            }
        }
        $this->view->composePartials = $composePartials;

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if ($values['coupon_mail'] == 1) {
            $members_ids = explode(",", $values['toValues']);
        } else {
            if (!empty($multi)) {
                $user_id = $viewer->getIdentity();

                $tableMember = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $tableMemberName = $tableMember->info('name');

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');

                $select = $tableMember->select()
                        ->setIntegrityCheck(false)
                        ->from($tableMemberName, array('user_id'))
                        ->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
                        ->where($tableMemberName . '.active = ?', 1)
                        ->where($tableMemberName . '.resource_approved = ?', 1)
                        ->where($tableMemberName . '.user_approved = ?', 1)
                        ->where($tableMemberName . '.user_id != ?', $user_id)
                        ->where($tableMemberName . '.page_id = ?', $resource_id);
                
                if($values['coupon_mail'] == 2) {
                      if (isset($values['roles_id']) && $values['roles_id']) {
                    			$select = $select->where($tableMemberName . '.role_id LIKE ?', '%' . $values['roles_id'] . '%');
                  }
                }
                
                $members_ids = $select->query()->fetchAll();
            }
        }

        if (!empty($members_ids)) {
            foreach ($members_ids as $member_id) {
                $multi_ids .= ',' . $member_id['user_id'];
            }

            $multi_ids = ltrim($multi_ids, ",");
            if ($multi_ids) {
                $this->view->multi = $multi;
                $this->view->multi_name = $viewer->getTitle();
                $this->view->multi_ids = $multi_ids;
                $form->toValues->setValue($multi_ids);
            }
        }

        //PROCESS.
        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {

            $attachment = null;
            $attachmentData = $this->getRequest()->getParam('attachment');
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                $type = $attachmentData['type'];
                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (!empty($data['composer'][$type])) {
                        $config = $data['composer'][$type];
                    }
                }
                if ($config) {
                    $plugin = Engine_Api::_()->loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    $attachment = $plugin->$method($attachmentData);
                    $parent = $attachment->getParent();
                    if ($parent->getType() === 'user') {
                        $attachment->search = 0;
                        $attachment->save();
                    } else {
                        $parent->search = 0;
                        $parent->save();
                    }
                }
            }

            $viewer = Engine_Api::_()->user()->getViewer();

            $values = $form->getValues();
            $recipients = preg_split('/[,. ]+/', $values['toValues']);

            // limit recipients if it is not a special list of members
            if (empty($multi))
                $recipients = array_slice($recipients, 0, 10); // Slice down to 10
                
// clean the recipients for repeating ids
            // this can happen if recipient is selected and then a friend list is selected
            $recipients = array_unique($recipients);
            $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'], $attachment);
            if ($conversation->recipients > 1) {
                $conversation->locked = 1; //DO NOT ADD RESOURCE TYPE AND RESOURCE ID
                $conversation->save();
            }
            foreach ($recipientsUsers as $user) {
                if ($user->getIdentity() == $viewer->getIdentity()) {
                    continue;
                }
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
            }

            //Increment messages counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
            $db->commit();

            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($resource_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR GET USER.
    public function getItemAction() {

        $member_name = $this->_getParam('sitepage_members_search_input_text', null);

        $data = array();

        $UserTable = Engine_Api::_()->getDbtable('users', 'user');

        $select = $UserTable->select()
                ->setIntegrityCheck(false)
                ->from($UserTable->info('name'), array('user_id', 'displayname', 'photo_id'))
                ->where('username  LIKE ? ', '%' . $member_name . '%')
                ->order('displayname ASC');

        //FETCH RESULTS
        $members = $UserTable->fetchAll($select);

        foreach ($members as $member) {
            $member_photo = $this->view->itemPhoto($member, 'thumb.icon');
            $data[] = array(
                'id' => $member->user_id,
                'label' => $member->displayname,
                'photo' => $member_photo
            );
        }

        if (!empty($data)) {
            return $this->_helper->json($data);
        }
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getusersAction() {

        $data = array();

        //GET COUPON ID.
        $page_id = $this->_getParam('page_id', null);

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();

        $tableMember = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $tableMemberName = $tableMember->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        $select = $tableMember->select()
                ->setIntegrityCheck(false)
                ->from($tableMemberName, array('user_id'))
                ->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
                ->where($tableMemberName . '.active = ?', 1)
                ->where($tableMemberName . '.resource_approved = ?', 1)
                ->where($tableMemberName . '.user_approved = ?', 1)
                ->where($tableMemberName . '.user_id != ?', $user_id)
                ->where($tableMemberName . '.page_id = ?', $page_id);
        $select->where($userTableName . '.displayname  LIKE ? ', '%' . $this->_getParam('user_ids', null) . '%')
                ->order($userTableName . '.displayname ASC')->limit('40');

        $users = $tableMember->fetchAll($select);

        foreach ($users as $user) {
            $user_subject = Engine_Api::_()->user()->getUser($user->user_id);
            $user_photo = $this->view->itemPhoto($user_subject, 'thumb.icon');
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }

        return $this->_helper->json($data);
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getmembersAction() {

        $data = array();

        //GET COUPON ID.
        $page_id = $this->_getParam('page_id', null);

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $membershipTableName = $membershipTable->info('name');

        $pageJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);
        $pagePhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);

        $select = $membershipTable->select()
                ->from($membershipTableName, 'user_id')
                ->where('page_id = ?', $page_id);
        $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $autoRequest = '';
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $autoRequest = $this->_getParam('user_ids', null);
        } else {
            $autoRequest = $this->_getParam('text', null);
        }
        $select = $usersTable->select()
                ->where('displayname  LIKE ? || email LIKE ?', '%' . $autoRequest . '%')
                ->where($usersTableName . '.user_id NOT IN (?)', (array) $user_ids)
                ->order('displayname ASC')
                ->limit('40');
        $users = $usersTable->fetchAll($select);

        foreach ($users as $user) {
            $user_photo = $this->view->itemPhoto($user, 'thumb.icon', '', array('nolazy' => true));
            if($user->displayname){
                $labelName = $user->displayname;
                $email = $user->email;
            }else{
                $labelName = $user->email;
                $email = '';
            }
            $data[] = array(
                'id' => $user->user_id,
                'label' => $labelName,
                'photo' => $user_photo,
                'email'=> $email
            );
        }

        if ($pageJoinType == $pagePhraseNum) {
            return $this->_helper->json($data);
        } else {
            return;
        }
    }

}
