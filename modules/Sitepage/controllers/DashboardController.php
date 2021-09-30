<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_DashboardController extends Core_Controller_Action_Standard {

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
            return;

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

        $page_url = $this->_getParam('page_url', $this->_getParam('page_url', null));
        $page_id = $this->_getParam('page_id', $this->_getParam('page_id', null));

        if ($page_url) {
            $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
        }

        if ($page_id) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            if ($sitepage) {
                if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
                    Engine_Api::_()->core()->setSubject($sitepage);
                }
            }
        }

        //FOR UPDATE EXPIRATION
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time()) {
            Engine_Api::_()->sitepage()->updateExpiredPages();
        }
    }

    //ACTION FOR SHOWING THE APPS AT DASHBOARD
    public function appAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET THE LOGGEDIN USER INFORMATION
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET THE SITEPAGE ID FROM THE URL
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //SET THE SUBJECT OF SITEPAGE
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        //VERSION CHECK APPLIED FOR - PACKAGE WORK
        $this->view->siteeventVersion = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {

            if (Engine_Api::_()->sitepage()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version, '4.8.8')) {
                $this->view->siteeventVersion = true;
            }
        }

        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

        //PACKAGE BASE PRIYACY START
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
            if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagealbum")) {
                $this->view->allowed_upload_photo = 1;
            }
        } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'spcreate');
            if (!empty($isPageOwnerAllow)) {
                $this->view->allowed_upload_photo = 1;
            }
        }
        //START THE PAGE ALBUM WORK
        $this->view->default_album_id = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($page_id)->album_id;
        //END THE PAGE ALBUM WORK

        $this->view->albumtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $page_id, $layout);


        //PASS THE PAGE ID IN THE CORRESPONDING TPL FILE
        $this->view->sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->sitepages_view_menu = 16;

        //START THE PAGE POLL WORK
        $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
        if ($sitepagePollEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagepoll")) {
                    $this->view->can_create_poll = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'splcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_poll = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->polltab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagepoll.profile-sitepagepolls', $page_id, $layout);
        }
        //END THE PAGE POLL WORK
        //START THE PAGE DOCUMENT WORK
        $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
        if ($sitepageDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagedocument")) {
                    $this->view->can_create_doc = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sdcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_doc = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitepageDocumentEnabled) {
                $this->view->documenttab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $page_id, $layout);
            } else {
                $this->view->documenttab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('document.contenttype-documents', $page_id, $layout);
            }
        }
        //END THE PAGE DOCUMENT WORK
        //START THE PAGE INVITE WORK
        $this->view->can_invite = 0;
        $sitepageInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite');
        if ($sitepageInviteEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_invite = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'invite');
            //END MANAGE-ADMIN CHECK
        }
        //END THE PAGE INVITE WORK
        //START THE PAGE VIDEO WORK
        $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
        if ($sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagevideo")) {
                    $this->view->can_create_video = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'svcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_video = 1;
                }
            }
            //PACKAGE BASE PRIYACY END


            if ($sitepageVideoEnabled) {
                // todo: Page Video: naaziya: used the new widget with simple ui, so commented out the old widget
                //$this->view->videotab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $page_id, $layout);
                $this->view->videotab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.page-videos', $page_id, $layout);
            } else {
                $this->view->videotab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitevideo.contenttype-videos', $page_id, $layout);
            }
        }
        //END THE PAGE VIDEO WORK

        $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
        //START THE PAGE EVENT WORK
        if ($sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepageevent")) {
                    $this->view->can_create_event = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'secreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_event = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitepageeventEnabled) {
                $this->view->eventtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $page_id, $layout);
            } else {
                $this->view->eventtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('siteevent.contenttype-events', $page_id, $layout);
            }
        }
        //END THE PAGE EVENT WORK
        //START THE PAGE NOTE WORK
        $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
        if ($sitepageNoteEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagenote")) {
                    $this->view->can_create_notes = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sncreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_notes = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->notetab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $page_id, $layout);
        }
        //END THE PAGE NOTE WORK
        //START THE PAGE REVEIW WORK
        $sitepageReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
        if ($sitepageReviewEnabled) {
            $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitepagereview')->canPostReview($subject->page_id, $viewer_id);
            if (empty($hasPosted) && !empty($viewer_id)) {
                $this->view->can_create_review = 1;
            } else {
                $this->view->can_create_review = 0;
            }

            $this->view->reviewtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagereview.profile-sitepagereviews', $page_id, $layout);
        }
        //END THE PAGE REVEIW WORK
        //START THE PAGE DISCUSSION WORK
        $sitepageDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
        if ($sitepageDiscussionEnabled) {

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'sdicreate');
            if (!empty($isManageAdmin)) {
                $this->view->can_create_discussion = 1;
            }
            //END MANAGE-ADMIN CHECK
            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagediscussion")) {
                    $this->view->can_create_discussion = 0;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->discussiontab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.discussion-sitepage', $page_id, $layout);
        }
        //END THE PAGE DISCUSSION WORK
        //START THE PAGE FORM WORK
        $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
        if ($sitepageFormEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_form = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'form');
            //END MANAGE-ADMIN CHECK

            $page_id = $this->_getParam('page_id');
            $quetion = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
            $select_quetion = $quetion->select()->where('page_id = ?', $page_id);
            $result_quetion = $quetion->fetchRow($select_quetion);
            $this->view->option_id = $result_quetion->option_id;

            $this->view->formtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageform.sitepage-viewform', $page_id, $layout);
        }
        //END THE PAGE FORM WORK
        //START THE PAGE OFFER WORK
        $this->view->moduleEnable = $sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
        if ($sitepageOfferEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_offer = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'offer');
            //END MANAGE-ADMIN CHECK

            $this->view->offertab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.profile-sitepageoffers', $page_id, $layout);
        }
        //END THE PAGE OFFER WORK
        //START THE PAGE MUSIC WORK
        $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
        if ($sitepageMusicEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagemusic")) {
                    $this->view->can_create_musics = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'smcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_musics = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->musictab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagemusic.profile-sitepagemusic', $page_id, $layout);
        }
        //END THE PAGE MUSIC WORK
        //START THE PAGE CROWDFUNDING WORK
        $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
        if ($sitecrowdfundingEnabled && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitecrowdfunding")) {
                    $this->view->can_create_project = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sprcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_project = 1;
                }
            }
            //PACKAGE BASE PRIYACY END
            $this->view->projecttab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitecrowdfunding.contenttype-projects', $page_id, $layout);

        }
        //END THE PAGE CROWDFUNDING WORK
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }

    //ACTION FOR CONTACT INFORMATION
    public function announcementsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SHOW SELECTED TAB
        $this->view->sitepages_view_menu = 30;

        $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitepage')->announcements(array('page_id' => $page_id, 'hideExpired' => 0), array('announcement_id', 'title', 'body', 'startdate', 'expirydate', 'status'));
    }

    //ACTION FOR CONTACT INFORMATION
    public function notificationSettingsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET THE LOGGEDIN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SHOW SELECTED TAB
        $this->view->sitepages_view_menu = 31;

        //SET FORM
        $this->view->form = $form = new Sitepage_Form_NotificationSettings();

        $ManageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $ManageAdminsTableName = $ManageAdminsTable->info('name');

        $select = $ManageAdminsTable->select()
            ->from($ManageAdminsTableName)
            ->where($ManageAdminsTableName . '.page_id = ?', $page_id)
            ->where($ManageAdminsTableName . '.user_id = ?', $viewer_id);
        $results = $ManageAdminsTable->fetchRow($select);


        //POPULATE FORM
        $this->view->email = $value['email'] = $results["email"];
        $value['action_email'] = json_decode($results['action_email']);

        $this->view->notification = $value['notification'] = $results["notification"];
        $value['action_notification'] = unserialize($results['action_notification']);

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();
            if (isset($values['email'])) {

                $ManageAdminsTable->update(array('email' => $values['email'], 'action_email' => json_encode($values['action_email'])), array('page_id =?' => $page_id, 'user_id =?' => $viewer_id));

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {

                    if (in_array('posted', $values['action_email']))
                        $email['emailposted'] = 1;
                    else
                        $email['emailposted'] = 0;

                    if (in_array('created', $values['action_email']))
                        $email['emailcreated'] = 1;
                    else
                        $email['emailcreated'] = 0;

                    Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('email' => $values['email'], 'action_email' => json_encode($email)), array('page_id =?' => $page_id, 'resource_id =?' => $page_id, 'user_id =?' => $viewer_id));
                }
            }

            if (isset($values['notification'])) {

                $ManageAdminsTable->update(array('notification' => $values['notification'], 'action_notification' => serialize($values['action_notification'])), array('page_id =?' => $page_id, 'user_id =?' => $viewer_id));


                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {

                    if (in_array('posted', $values['action_notification']))
                        $notification['notificationposted'] = 1;
                    else
                        $notification['notificationposted'] = 0;

                    if (in_array('created', $values['action_notification']))
                        $notification['notificationcreated'] = 1;
                    else
                        $notification['notificationcreated'] = 0;

                    if (in_array('follow', $values['action_notification']))
                        $notification['notificationfollow'] = 1;
                    else
                        $notification['notificationfollow'] = 0;

                    if (in_array('like', $values['action_notification']))
                        $notification['notificationlike'] = 1;
                    else
                        $notification['notificationlike'] = 0;

                    if (in_array('comment', $values['action_notification']))
                        $notification['notificationcomment'] = 1;
                    else
                        $notification['notificationcomment'] = 0;

                    if (in_array('join', $values['action_notification']))
                        $notification['notificationjoin'] = 1;
                    else
                        $notification['notificationjoin'] = 0;

                    Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('notification' => $values['notification'], 'action_notification' => json_encode($notification)), array('page_id =?' => $page_id, 'resource_id =?' => $page_id, 'user_id =?' => $viewer_id));
                }
            }

            if (empty($results) && $viewer == 1) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Your changes will not be reflected as you are not an admin of this page. Please go to the Admin Panel >> Manage >> Members to login to the Page Admin’s account to save your changes.'));
                return;
            } else {
                //SHOW SUCCESS MESSAGE
                $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
            }
        } else {
            $this->view->is_ajax = $this->_getParam('is_ajax', '');
        }
    }

    //ACTION FOR CONTACT INFORMATION
    public function contactAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'contact');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SHOW SELECTED TAB
        $this->view->sitepages_view_menu = 20;

        //SET FORM
        $this->view->form = $form = new Sitepage_Form_Contactinfo(array('pageowner' => Engine_Api::_()->user()->getUser($sitepage->owner_id)));

        //POPULATE FORM
        $value['email'] = $sitepage->email;
        $value['phone'] = $sitepage->phone;
        $value['website'] = $sitepage->website;
        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            if (isset($values['email'])) {
                $email_id = $values['email'];

                //CHECK EMAIL VALIDATION
                $validator = new Zend_Validate_EmailAddress();
                $validator->getHostnameValidator()->setValidateTld(false);
                if (!empty($email_id)) {
                    if (!$validator->isValid($email_id)) {
                        $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'));
                        return;
                    } else {
                        $sitepage->email = $email_id;
                    }
                } else {
                    $sitepage->email = $email_id;
                }
            }

            //CHECK PHONE OPTION IS THERE OR NOT
            if (isset($values['phone'])) {
                $sitepage->phone = $values['phone'];
            }

            //CHECK WEBSITE OPTION IS THERE OR NOT
            if (isset($values['website'])) {
                $sitepage->website = $values['website'];
            }

            //SAVE VALUES
            $sitepage->save();

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        } else {
            $this->view->is_ajax = $this->_getParam('is_ajax', '');
        }
    }

    //ACTION FOR Services
    public function serviceAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        $servicePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'service');
        if (empty($servicePrivacy)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $page=$this->_getParam('page',1);

        //SHOW SELECTED TAB
        $this->view->sitepages_view_menu = 20;

        $this->view->services=Engine_Api::_()->getDbTable('services', 'sitepage')->getListing(array('page_id'=>$page_id, 'page' => $page, 'count' => 5));

    }

    //ACTION FOR Services
    public function linkpagesAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //FOR SHOW ADD FAVOURITE LINK ON THE PAGE PROFILE PAGE
        $show_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addfavourite.show', 0);
        if (empty($show_link)) {
            return false;
        }
        $page=$this->_getParam('page',1);

        //SHOW SELECTED TAB
        $this->view->sitepages_view_menu = 20;

        //GET THE SUBJECT OF PAGE.
        $sitepage_id = $sitepage->page_id;

        //FUNCTION CALL FORM THE DBTABLE AND PASS PAGE ID OR LIMIT OF PAGES TO SHOW ON THE WIDGET.
        $this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitepage')->linkedPages($sitepage_id, 10,$params);
    }

    //ACTION FOR OPERATING HOURS
    public function timingAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //SHOW SELECTED TAB
        $this->view->sitepages_view_menu = 20;

        // Store value to populate
        $totalDays = array('monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday');
        $timings = Engine_Api::_()->getItemTable('sitepage_timing')->getPageTimings($page_id);
        $this->view->form = $form = new Sitepage_Form_Timing(array('timings' => $timings));
        $form->populate($sitepage->toArray());
    }
    // ACTION FOR SAVE TIMINGS
    public function saveTimingAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $page_id = $this->_getParam('page_id');

        // If not post or form not valid, return
        if( !$this->getRequest()->isPost() ) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'timing', 'page_id' => $sitepage->page_id), 'sitepage_dashboard', true);
        }
        // Process
        $page = Engine_Api::_()->getItem('sitepage_page', $page_id);
        Engine_Api::_()->getItemTable('sitepage_timing')->deletePageTimings($page_id);
        try {
            // Create timing
            $viewer = Engine_Api::_()->user()->getViewer();
            $timer = $this->getRequest()->getParams();
            $page->setFromArray($timer);
            $page->modified_date = new Zend_Db_Expr('NOW()');
            $page->save();
        } catch( Exception $e ) {
            return $this->exceptionWrapper($e, $form, $db);
        }

        if($timer['days'] == 0) {
            $totalDays = array('monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday');
            $table = Engine_Api::_()->getItemTable('sitepage_timing');
            $hours = array();
            $hours['page_id'] = $page_id;
            foreach ($totalDays as $key => $value) {
                if($timer[$key] == '1') {
                    if($timer[$key.'start'] > $timer[$key.'end']){
                        return $this->_helper->redirector->gotoRoute(array('action' => 'timing', 'page_id' => $page_id), 'sitepage_dashboard', true);
                    }else {
                        $hours['day'] = $key;
                        $hours['start'] = $timer[$key.'start'];
                        $hours['end'] = $timer[$key.'end'];
                    }
                    $data = $table->createRow();
                    $data->setFromArray($hours);
                    $data->save();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'timing', 'page_id' => $page_id), 'sitepage_dashboard', true);
    }
    //ACTION FOR CREATE SERVICE
    public function createServiceAction() {

        $this->_helper->layout->setLayout('default-simple');
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Process
        $table = Engine_Api::_()->getItemTable('sitepage_service');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            //create service
            $this->view->page_id = $page_id = $this->_getParam('page_id');
            $table = Engine_Api::_()->getDbTable('services', 'sitepage');
            $this->view->form = $form = new Sitepage_Form_Service();
            if( !$this->getRequest()->isPost() ) {
                return;
            }
            if( !$form->isValid($this->getRequest()->getPost()) ) {
                return ;
            }
            if( $table->checKService($form->getvalue('title'),$page_id) ) {
                return $form->addError("Service Already Exists");
            }
            $value=$form->getValues();
            $row=$table->createRow();
            $value = array_merge($value, array(
                'page_id' => $page_id,
            ));
            $row->setFromArray($value);
            $row->save();
            if( !empty($value['photo']) ) {
                $row->setPhoto($form->photo);
            }
            // Commit
            $db->commit();
        } catch( Exception $e ) {
            return $this->exceptionWrapper($e, $form, $db);
        }
        //SUCCESS
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRefresh' => 500,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Service successfully created'))
        ));
    }

    //ACTION FOR EDIT SERVICE
    public function editServiceAction() {
        $this->_helper->layout->setLayout('default-simple');
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Process
        $table = Engine_Api::_()->getItemTable('sitepage_service');
        $this->view->service_id = $service_id = $this->_getParam('service_id');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            //edit service
            $this->view->form = $form = new Sitepage_Form_Editservice();
            $select=$table->select()->where('service_id=?',$service_id);
            $row=$table->fetchRow($select);
            $form->populate($row->toArray());
            $table = Engine_Api::_()->getDbTable('services', 'sitepage');
            if( !$this->getRequest()->isPost() ) {
                return;
            }
            if( !$form->isValid($this->getRequest()->getPost()) ) {
                return ;
            }
            $value=$form->getValues();
            $row->setFromArray($value);
            $row->save();
            if( !empty($value['photo']) ) {
                $row->setPhoto($form->photo);
            }
            // Commit
            $db->commit();
        } catch( Exception $e ) {
            return $this->exceptionWrapper($e, $form, $db);
        }
        //SUCCESS
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRefresh' => 500,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Service successfully Saved'))
        ));
    }

    // ACTION FOE DELETE SERVICE
    public function deleteServiceAction() {
        $this->_helper->layout->setLayout('default-simple');
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Process
        $table = Engine_Api::_()->getItemTable('sitepage_service');
        $this->view->service_id = $service_id = $this->_getParam('service_id');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $this->view->form = $form = new Sitepage_Form_Deleteservice();
            $select=$table->select()->where('service_id=?',$service_id);
            $row=$table->fetchRow($select);
            if( !$this->getRequest()->isPost() ) {
                return;
            }
            $row->delete();
            $db->commit();
        } catch( Exception $e ) {
            return $this->exceptionWrapper($e, $form, $db);
        }
        //SUCCESS
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRefresh' => 500,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Service successfully Deleted'))
        ));
    }
    // ACTION FOE UNLINK PAGE
    public function unlinkPageAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING THE VIEWER ID.
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->form = $form = new Sitepage_Form_Unlinkpage();

        //CHECK POST.
        if ($this->getRequest()->isPost()) {

            //DELETE THE RESULT FORM THE TABLE.
            $sitepageTable = Engine_Api::_()->getDbtable('favourites', 'sitepage');
            $sitepageTable->delete(array('page_id =?' => $this->_getParam('page_id'), 'page_id_for =?' => $this->_getParam('page_id_for')));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 100,
                'parentRefresh' => 100,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Page has been successfully unlinked.'))
            ));
        }
    }
    //ACTION FOR EDIT STYLE
    public function editStyleAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET PAGE ID AND PAGE OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->sitepages_view_menu = 3;
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //GET FORM
        $this->view->form = $form = new Sitepage_Form_Style();

        //GET CURRENT ROW
        $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');

        $row = $tableStyle->fetchRow(array('type = ?' => 'sitepage_page', 'id = ? ' => $page_id));
        $style = $sitepage->getPageStyle();

        //POPULATE
        if (!$this->getRequest()->isPost()) {
            $form->populate(array(
                'style' => ( null == $row ? '' : $row->style )
            ));
            return;
        }

        //Whoops, form was not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET STYLE
        $style = $form->getValue('style');
        $style = strip_tags($style);

        $forbiddenStuff = array(
            '-moz-binding',
            'expression',
            'javascript:',
            'behaviour:',
            'vbscript:',
            'mocha:',
            'livescript:',
        );

        $style = str_replace($forbiddenStuff, '', $style);

        //SAVE IN DATABASE
        if (null == $row) {
            $row = $tableStyle->createRow();
            $row->type = 'sitepage_page';
            $row->id = $page_id;
        }
        $row->style = $style;
        $row->save();
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }

    //ACTION FOR ALL LOCATION
    public function allLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LOCAITON ENABLE OR NOT
        if (!Engine_Api::_()->sitepage()->enableLocation()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        $this->view->sitepages_view_menu = 4;

        //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //$location_id = $this->_getParam('location_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        if (!empty($sitepage->location)) {
            $mainLocationId = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location);
            $this->view->mainLocationObject = Engine_Api::_()->getItem('sitepage_location', $mainLocationId);
            $value['mainlocationId'] = $mainLocationId;
        }
        $value['id'] = $sitepage->getIdentity();
        $value['mapshow'] = 'Map Tab';
        $page = $this->_getParam('page');

        $this->view->location = $paginator = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);

        $paginator->setItemCountPerPage(10);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
    }

    //ACTION FOR EDIT LOCATION
    public function editLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LOCAITON ENABLE OR NOT
        if (!Engine_Api::_()->sitepage()->enableLocation()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        $this->view->sitepages_view_menu = 4;

        //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $location_id = $this->_getParam('location_id');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');

//    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);

        $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);

        if (empty($location_id) && !empty($sitepage->location)) {
            $location_id = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocationId($page_id, $sitepage->location);
        }
        if ($locationFieldEnable && $location_id) {
            $params['location_id'] = $location_id;
            $params['id'] = $page_id;
            $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($params);
        }

        //Get form
        if (!empty($location)) {
            $this->view->form = $form = new Sitepage_Form_Location(array(
                'item' => $sitepage,
                'location' => $location->location
            ));

            if (!$this->getRequest()->isPost()) {
                $form->populate($location->toarray());
                return;
            }

            //FORM VALIDAITON
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            //FORM VALIDAITON
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                unset($values['submit']);
                unset($values['location']);
                unset($values['locationParams']);
                $locationTable->update($values, array('page_id =?' => $page_id, 'location_id =?' => $location_id));
            }
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
        }

        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($params);
    }

    //ACTION FOR EDIT ADDRESS
    public function addLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
        $tab_selected_id = $this->_getParam('tab');
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->form = $form = new Sitepage_Form_Address(array('item' => $sitepage));
        $form->setTitle('Add Location');
        $form->setDescription('Add your location below, then click "Save Location" to save your location.');

        //POPULATE FORM
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values['location'])) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please enter the location.");
            $form->addError($itemError);
            return;
        }

        if (empty($values['locationParams'])) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please select location from the auto-suggest.");
            $form->addError($itemError);
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if (!empty($values['main_location'])) {
                $sitepage->location = $values['location'];
                $sitepage->save();
            }

            $location = array();
            unset($values['submit']);
            $location = $values['location'];
            $locationName = $values['locationname'];
            if (!empty($location)) {
                $sitepage->setLocation($location, $locationName);
            }
            $db->commit();
            if (!empty($tab_selected_id)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page location has been added successfully.'))
                ));
            } else {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page location has been added successfully.'))
                ));
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR LEAVE THE JOIN MEMBER.
    public function deleteLocationAction() {

        $page_id = $this->_getParam('page_id');
        $tab_selected_id = $this->_getParam('tab');
        $location_id = $this->_getParam('location_id');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $location = Engine_Api::_()->getItem('sitepage_location', $location_id);
        if ($this->getRequest()->isPost()) {
            if ($location->location == $sitepage->location) {
                $sitepage->location = '';
                $sitepage->save();
            }

            if (!empty($page_id)) {
                Engine_Api::_()->getDbtable('locations', 'sitepage')->delete(array('location_id =?' => $location_id, 'page_id =?' => $page_id));
            }

            if (!empty($tab_selected_id)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete location for this page.'))
                ));
            } else {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete location for this page.'))
                ));
            }
        }
    }

    //ACTION FOR EDIT ADDRESS
    public function editAddressAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
        $tab_selected_id = $this->_getParam('tab');
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $location_id = $this->_getParam('location_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);

        $location = Engine_Api::_()->getItem('sitepage_location', $location_id);

        if (empty($sitepage)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->form = $form = new Sitepage_Form_Address(array('item' => $sitepage));
        $form->setTitle('Edit Location');
        $form->setDescription('Edit your location below, then click "Save Location" to save your location.');

        if (!empty($multipleLocation) && $location->location == $sitepage->location) {
            $form->main_location->setValue(1);
        }

        //POPULATE FORM
        if (!$this->getRequest()->isPost()) {
            if (!empty($multipleLocation)) {
                $form->populate($location->toArray());
            } else {
                $form->populate($sitepage->toArray());
            }
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();

        if (empty($values['location']) && !empty($multipleLocation)) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please enter the location.");
            $form->addError($itemError);
            return;
        }
        if (empty($values['locationParams'])) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please select location from the auto-suggest.");
            $form->addError($itemError);
            return;
        }

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if (!empty($multipleLocation)) {
                if (!empty($values['main_location'])) {
                    $sitepage->location = $values['location'];
                    $sitepage->save();
                } elseif ($sitepage->location == $location->location) {
                    $sitepage->location = '';
                    $sitepage->save();
                }
                if ($location->location != $values['location']) {
                    $locationTable->delete(array('location_id =?' => $location_id));
                    $sitepage->setLocation($values['location']);
                }
            } else {
                if (!empty($values['location']) && $values['location'] != $sitepage->location) {
                    $locationTable->delete(array('location_id =?' => $location_id));
                    $sitepage->location = $values['location'];
                    $sitepage->setLocation($values['location']);
                    $sitepage->save();
                }
            }

            $location = '';
            $locationName = '';
            unset($values['submit']);
            $location = $values['location'];

            if (isset($values['locationname']))
                $locationName = $values['locationname'];


            if (!empty($location)) {
                // $sitepage->setLocation();

                if (!empty($multipleLocation)) {
                    $locationTable->update(array('location' => $location, 'locationname' => $locationName), array('page_id =?' => $page_id, 'location_id =?' => $location_id));
                } else {
                    $locationTable->update(array('location' => $location), array('page_id =?' => $page_id, 'location_id =?' => $location_id));
                }
            } else {
                $locationTable->delete(array('page_id =?' => $page_id, 'location_id =?' => $location_id));
            }

            $db->commit();
            if (!empty($tab_selected_id)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page location has been modified successfully.'))
                ));
            } elseif (!empty($multipleLocation)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('action' => 'all-location', 'page_id' => $page_id), 'sitepage_dashboard', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page location has been modified successfully.'))
                ));
            } else {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page location has been modified successfully.'))
                ));
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR FEATURED OWNER
    public function featuredOwnersAction() {

        //USER VALIDAITON
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        $this->view->sitepages_view_menu = 17;

        //GET PAGE ID AND PAGE OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //EDIT PRIVACY
        $editPrivacy = 0;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (!empty($isManageAdmin)) {
            $editPrivacy = 1;
        }

        $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
        if (empty($editPrivacy) || empty($manageAdminAllowed)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET FEATURED ADMINS
        $this->view->featuredhistories = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->featuredAdmins($page_id);

        $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }

    //ACTION FOR MAKING FAVOURITE
    public function favouriteAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET THE SUBJECT
        $getPageId = $this->_getParam('page_id', $this->_getParam('id', null));
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject();
        $page_id = $sitepage->page_id;

        //CALLING THE FUNCTION AND PASS THE VALUES OF PAGE ID AND USER ID.
        $this->view->userListings = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPages($page_id, $viewer_id);

        //CHECK POST.
        if ($this->getRequest()->isPost()) {

            //GET VALUE FROM THE FORM.
            $values = $this->getRequest()->getPost();
            $selected_page_id = $values['page_id'];
            if (!empty($selected_page_id)) {

                $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'sitepage');
                $row = $favouritesTable->createRow();
                $row->page_id = $selected_page_id;
                $row->page_id_for = $getPageId;
                $row->owner_id = $viewer_id;
                $row->save();

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 100,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Page has been successfully linked.'))
                ));
            }
        }
        //RENDER THE SCRIPT.
        $this->renderScript('dashboard/favourite.tpl');
    }

    //ACTION FOR DELETING THE FAVOURITE
    public function favouriteDeleteAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING THE VIEWER ID.
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET THE SUBJECT AND CHECK AUTH.
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject();
        $page_id = $sitepage->page_id;

        //CALLING THE FUNCTION.
        $this->view->userListings = Engine_Api::_()->getDbtable('favourites', 'sitepage')->deleteLink($page_id, $viewer_id);

        //CHECK POST.
        if ($this->getRequest()->isPost()) {

            $values = $this->getRequest()->getPost();
            $page_id_for = $page_id;
            $page_id = $values['page_id'];
            if (!empty($page_id)) {

                //DELETE THE RESULT FORM THE TABLE.
                $sitepageTable = Engine_Api::_()->getDbtable('favourites', 'sitepage');
                $sitepageTable->delete(array('page_id =?' => $page_id, 'page_id_for =?' => $page_id_for));

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 100,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Page has been successfully unlinked.'))
                ));
            }
        }
        //RENDER THE SCRIPT.
        $this->renderScript('dashboard/favourite-delete.tpl');
    }

    //ACTION FOR FOURSQUARE CODE
    public function foursquareAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET PAGE ID AND SITEPAGE OBJECT
        $siteapage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));

        //GENERATE FORM
        $this->view->form = $form = new Sitepage_Form_Foursquare();

        //POPULATE THE FORM
        $form->populate($siteapage->toArray());

        if (!$this->getRequest()->isPost())
            return;

        //SAVE THE FOURSQUARE CODE IN DATABASE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbtable('pages', 'sitepage')->getAdapter();
            $db->beginTransaction();
            try {
                $siteapage->foursquare_text = $_POST['foursquare_text'];
                $siteapage->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => false,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Text saved successfully.'))
            ));
        }
    }

    //ACTION: GET-STARTED
    public function getStartedAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //VIEWER INFORMATION
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET PAGE ID AND PAGE SUBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

        //GET PHOTO ID
        $this->view->photo_id = $subject->photo_id;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //VERSION CHECK APPLIED FOR - PACKAGE WORK
        $this->view->siteeventVersion = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $siteeventModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent');
            $siteeventVersion = $siteeventModule->version;
            if (!empty(Engine_Api::_()->seaocore()->checkVersion($siteeventVersion, '4.8.8p1'))) {
                $this->view->siteeventVersion = true;
            }
        }

        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //OVERVIEW PRIVACY
        $this->view->overviewPrivacy = 0;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'overview');
        if (!empty($isManageAdmin)) {
            $this->view->overviewPrivacy = 1;
        }

        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

        //START PAGE ALBUM WORK
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagealbum")) {
                    $this->view->allowed_upload_photo = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'spcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->allowed_upload_photo = 1;
                }
            }
            //START THE PAGE ALBUM WORK
            $this->view->default_album_id = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($page_id)->album_id;
            //END THE PAGE ALBUM WORK

            $this->view->albumtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $page_id, $layout);
        }
        //END PAGE ALBUM WORK
        //GET PAGE OBJECT
        $this->view->sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->sitepages_view_menu = 12;

        //START THE PAGE POLL WORK
        $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
        if ($sitepagePollEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagepoll")) {
                    $this->view->can_create_poll = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'splcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_poll = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->polltab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagepoll.profile-sitepagepolls', $page_id, $layout);
        }
        //END THE PAGE POLL WORK
        //START THE PAGE DOCUMENT WORK
        $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
        if ($sitepageDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagedocument")) {
                    $this->view->can_create_doc = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sdcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_doc = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitepageDocumentEnabled) {
                $this->view->documenttab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $page_id, $layout);
            } else {
                $this->view->documenttab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('document.contenttype-documents', $page_id, $layout);
            }
        }
        //END THE PAGE DOCUMENT WORK
        //START THE PAGE INVITE WORK
        $this->view->can_invite = 0;
        $sitepageInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite');
        if ($sitepageInviteEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_invite = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'invite');
            //END MANAGE-ADMIN CHECK
        }
        //END THE PAGE INVITE WORK
        //START THE PAGE VIDEO WORK
        $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
        if ($sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagevideo")) {
                    $this->view->can_create_video = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'svcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_video = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitepageVideoEnabled) {
                // todo: Page Video: naaziya: used the new widget with simple ui, so commented out the old widget
                //$this->view->videotab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $page_id, $layout);
                $this->view->videotab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.page-videos', $page_id, $layout);
            } else {
                $this->view->videotab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitevideo.contenttype-videos', $page_id, $layout);
            }
        }
        //END THE PAGE VIDEO WORK

        $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
        //START THE PAGE EVENT WORK
        if ($sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepageevent")) {
                    $this->view->can_create_event = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'secreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_event = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitepageeventEnabled) {
                $this->view->eventtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $page_id, $layout);
            } else {
                $this->view->eventtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('siteevent.contenttype-events', $page_id, $layout);
            }
        }

        //END THE PAGE EVENT WORK
        //START THE PAGE NOTE WORK
        $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
        if ($sitepageNoteEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagenote")) {
                    $this->view->can_create_notes = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sncreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_notes = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->notetab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $page_id, $layout);
        }
        //END THE PAGE NOTE WORK
        //START THE PAGE REVEIW WORK
        $sitepageReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
        if ($sitepageReviewEnabled) {
            $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitepagereview')->canPostReview($subject->page_id, $viewer_id);
            if (empty($hasPosted) && !empty($viewer_id)) {
                $this->view->can_create_review = 1;
            } else {
                $this->view->can_create_review = 0;
            }

            $this->view->reviewtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagereview.profile-sitepagereviews', $page_id, $layout);
        }
        //END THE PAGE REVEIW WORK
        //START THE PAGE DISCUSSION WORK
        $sitepageDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
        if ($sitepageDiscussionEnabled) {

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'sdicreate');
            if (!empty($isManageAdmin)) {
                $this->view->can_create_discussion = 1;
            }
            //END MANAGE-ADMIN CHECK
            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagediscussion")) {
                    $this->view->can_create_discussion = 0;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->discussiontab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.discussion-sitepage', $page_id, $layout);
        }
        //END THE PAGE DISCUSSION WORK
        //START THE PAGE OFFER WORK
        $this->view->moduleEnable = $sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
        if ($sitepageOfferEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_offer = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'offer');
            //END MANAGE-ADMIN CHECK

            $this->view->offertab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.profile-sitepageoffers', $page_id, $layout);
        }
        //END THE PAGE OFFER WORK
        //START THE PAGE FORM WORK
        $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
        if ($sitepageFormEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_form = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'form');
            //END MANAGE-ADMIN CHECK

            $page_id = $this->_getParam('page_id');
            $quetion = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
            $select_quetion = $quetion->select()->where('page_id = ?', $page_id);
            $result_quetion = $quetion->fetchRow($select_quetion);
            $this->view->option_id = $result_quetion->option_id;

            $this->view->formtab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageform.sitepage-viewform', $page_id, $layout);
        }
        //END THE PAGE FORM WORK
        //START THE PAGE MUSIC WORK
        $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
        if ($sitepageMusicEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagemusic")) {
                    $this->view->can_create_musics = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'smcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_musics = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->musictab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagemusic.profile-sitepagemusic', $page_id, $layout);
        }
        //END THE PAGE MUSIC WORK

        //START THE PAGE CROWDFUNDING WORK
        $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
        if ($sitecrowdfundingEnabled && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitecrowdfunding")) {
                    $this->view->can_create_project = 1;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sprcreate');
                if (!empty($isPageOwnerAllow)) {
                    $this->view->can_create_project = 1;
                }
            }
            //PACKAGE BASE PRIYACY END
            $this->view->projecttab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitecrowdfunding.contenttype-projects', $page_id, $layout);

        }
        //END THE PAGE CROWDFUNDING WORK
//     $this->view->updatestab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('activity.feed', $page_id, $layout);
    }

    //ACTION FOR HIDE THE PHOTO
    public function hidePhotoAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET AJAX VALUE
        $is_ajax = $this->_getParam('isajax', '');

        //IF REQUEST IS NOT AJAX THEN ONLY SHOW FORM
        if (empty($is_ajax)) {
            $this->view->form = $form = new Sitepage_Form_Hidephoto();
        } else {
            Engine_Api::_()->getDbtable('photos', 'sitepage')->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    //ACTION FOR SHOW MARKETING PAGE
    public function marketingAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET VIEWER IDENTITY
        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET PAGE ID AND SITEPAGE OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->enableFoursquare = 1;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'foursquare');
        if (empty($isManageAdmin)) {
            $this->view->enableFoursquare = 0;
        }

        $this->view->enabletwitter = $sitepagetwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter');
        if ($sitepagetwitterEnabled) {
            $this->view->enabletwitter = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'twitter');
        }


        $this->view->enableInvite = 1;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'invite');
        if (empty($isManageAdmin)) {
            $this->view->enableInvite = 0;
        }

        $this->view->enableSendUpdate = 1;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sendupdate');
        if (empty($isManageAdmin)) {
            $this->view->enableSendUpdate = 0;
        }

        $sitepageLikeboxEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagelikebox');
        if (!empty($sitepageLikeboxEnabled))
            $this->view->enableLikeBox = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'likebox');
        //END MANAGE-ADMIN CHECK

        $this->view->sitepages_view_menu = 20;
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //CHECKING IF FACEBOOK PAGE FEED WIDGET IS PLACED ON SITE PROFILE PAGE OR NOT. IF YES ONLY THEN WE WILL SHOW FACEBOOK INTEGRATION FEATURE THERE.



        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
        $this->view->fblikebox_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.fblikebox-sitepage', $page_id, $layout);
    }

    //ACTION FOR CREATING OVERVIEW
    public function overviewAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET PAGE ID, PAGE OBJECT AND PAGE VALIDAITON
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $overview = '';
        if (!empty($sitepage->overview)) {
            $overview = $sitepage->overview;
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Overview();

        if (!$this->getRequest()->isPost()) {

            $saved = $this->_getParam('saved');
            if (!empty($saved))
                $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your page has been successfully created. You can enhance your page from this dashboard by creating other components.');
        }

        if ($this->getRequest()->isPost()) {

            $overview = $_POST['body'];
            $sitepage->overview = $overview;
            $sitepage->save();
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
        }
        $values['body'] = $overview;
        $form->populate($values);
        $this->view->sitepages_view_menu = 2;
    }

    //ACTION FOR CHANGING THE PAGE PROFILE PICTURE
    public function profilePictureAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //GET SELECTED TAB
        $this->view->sitepages_view_menu = 22;

        //GET REQUEST IS ISAJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //GET FORM
        $this->view->form = $form = new Sitepage_Form_Photo();

        //CHECK FORM VALIDATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null) {
            //GET DB
            $db = $sitepage->getTable()->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {

                //SET PHOTO
                $sitepage->setPhoto($form->Filedata);

                //SET ALBUMS PARAMS
                $paramsAlbum = array();
                $paramsAlbum['page_id'] = $page_id;
                $paramsAlbum['default_value'] = 1;
                $paramsAlbum['limit'] = 1;

                //FETCH PHOTO ID
                $photo_id = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($sitepage->page_id)->photo_id;
                if ($photo_id == 0) {
                    Engine_Api::_()->getItemTable('sitepage_album')->update(array('photo_id' => $sitepage->photo_id, 'owner_id' => $sitepage->owner_id), array('page_id = ?' => $sitepage->page_id, 'default_value = ?' => 1));
                }
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();
            $iProfile = $storage->get($sitepage->photo_id, 'thumb.profile');
            $iSquare = $storage->get($sitepage->photo_id, 'thumb.icon');
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);
            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
            $image = Engine_Image::factory();
            $image->open($pName)
                ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                ->write($iName)
                ->destroy();
            $iSquare->store($iName);
            @unlink($iName);
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'profile-picture', 'page_id' => $page_id), 'sitepage_dashboard', true);
    }

    //ACTION FOR FILL THE DATA OF PROFILE TYPE
    public function profileTypeAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET PAGE ID AND SITEPAGE OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject();
        ;

        //GET PROFILE TYPE
        $profile_type_exist = $sitepage->profile_type;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        $this->view->sitepages_view_menu = 10;

        //PROFILE FIELDS FORM DATA
        $aliasedFields = $sitepage->fields()->getFieldsObjectsByAlias();
        $this->view->topLevelId = $topLevelId = 0;
        $this->view->topLevelValue = $topLevelValue = null;
        if (isset($aliasedFields['profile_type'])) {
            $aliasedFieldValue = $aliasedFields['profile_type']->getValue($sitepage);
            $topLevelId = $aliasedFields['profile_type']->field_id;
            $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
            if (!$topLevelId || !$topLevelValue) {
                $topLevelId = null;
                $topLevelValue = null;
            }
            $this->view->topLevelId = $topLevelId;
            $this->view->topLevelValue = $topLevelValue;
        }

        //GET FORM
        $form = $this->view->form = new Fields_Form_Standard(array(
            'item' => Engine_Api::_()->core()->getSubject(),
            'topLevelId' => $topLevelId,
            'topLevelValue' => $topLevelValue,
        ));
        $form->submit->setLabel('Save Info');
        $form->setTitle('Edit Page Profile Info');
        if (empty($profile_type_exist)) {
            $form->setDescription('Profile information enables you to add additional information about your page depending on its category. This non-generic additional information will help others know more specific details about your page. First select a relevant Profile Type for your page, and then fill the corresponding profile information fields.');
        } else {
            $form->setDescription('Profile information enables you to add additional information about your page depending on its category. This non-generic additional information will help others know more specific details about your page.');
        }

        //SAVE DATA IF POSTED
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->saveValues();
            $values = $this->getRequest()->getPost();

            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));

            $page_id = $this->_getParam('page_id', null);

            if (isset($values['0_0_1']) && !empty($values['0_0_1'])) {
                $profile_type = $values['0_0_1'];
                $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
                $sitepage->profile_type = $profile_type;
                $sitepage->save();
            }
        }

        //IF PACKAGE INABLE
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {

            $profileField_level = Engine_Api::_()->sitepage()->getPackageProfileLevel($page_id);
            if (empty($profileField_level)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
            if ($profileField_level == 2) {
                $fieldsProfile = array("0_0_1", "submit");

                //PROFILE SELECT WORK
                if (empty($sitepage->profile_type)) {
                    $profileType = $form->getElement('0_0_1')
                        ->getMultiOptions();

                    $profileTypePackage = Engine_Api::_()->sitepage()->getSelectedProfilePackage($page_id);

                    $profileTypeFinal = array_intersect_key($profileType, $profileTypePackage);

                    //ONLY SET SELECTED PROFILE TYPE
                    $profileType = $form->getElement('0_0_1')
                        ->setMultiOptions($profileTypeFinal);
                    if (count($profileTypeFinal) <= 1) {
                        $form->removeElement("0_0_1");
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }

                $field_id = array();
                $fieldsProfile_2 = Engine_Api::_()->sitepage()->getProfileFields($page_id);
                $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);

                //PROFILE FIELD IS SELECTED BUT THERE ARE NOT ANY PROFILE FIELDS
                if (!empty($sitepage->profile_type)) {
                    $profile_field_flage = true;
                    foreach ($fieldsProfile_2 as $k => $v) {
                        $explodeField = explode("_", $v);
                        if ($explodeField['1'] == $sitepage->profile_type) {
                            $profile_field_flage = false;
                            break;
                        }
                    }

                    if ($profile_field_flage) {
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }

                foreach ($fieldsProfile_2 as $k => $v) {
                    $explodeField = explode("_", $v);
                    $field_id[] = $explodeField['2'];
                }

                $elements = $form->getElements();
                $orderIndex = 1;
                foreach ($elements as $key => $value) {
                    $explode = explode("_", $key);
                    if ($explode['0'] != "1" && $explode['0'] != "submit") {
                        if (in_array($explode['0'], $field_id)) {
                            $field_id[] = $explode['2'];
                            $fieldsProfile[] = $key;
                            continue;
                        }
                    }

                    if (!in_array($key, $fieldsProfile)) {
                        $form->removeElement($key);
                        $form->addElement('Hidden', $key, array(
                            "value" => "",
                            "order" => ++$orderIndex,
                        ));
                    }
                }
            }
        }//END PACKAGE WORK
        else {
            //START LEVEL CHECKS
            $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
            $can_profile = Engine_Api::_()->authorization()->getPermission($page_owner->level_id, "sitepage_page", "profile");
            if (empty($can_profile)) {
                return $this->_forward('requireauth', 'error', 'core');
            }

            if ($can_profile == 2) {
                $fieldsProfile = array("0_0_1", "submit");

                //PROFILE SELECT WORK
                if (empty($sitepage->profile_type)) {
                    $profileType = $form->getElement('0_0_1')
                        ->getMultiOptions();

                    $profileTypePackage = Engine_Api::_()->sitepage()->getSelectedProfileLevel($page_owner->level_id);

                    $profileTypeFinal = array_intersect_key($profileType, $profileTypePackage);

                    //ONLY SET SELECTED PROFILE TYPE
                    $profileType = $form->getElement('0_0_1')
                        ->setMultiOptions($profileTypeFinal);
                    if (count($profileTypeFinal) <= 1) {
                        $form->removeElement("0_0_1");
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }
                $fieldsProfile_2 = Engine_Api::_()->sitepage()->getLevelProfileFields($page_owner->level_id);
                $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);


                //PROFILE FIELD IS SELECTED BUT THERE ARE NOT ANY PROFILE FIELDS
                if (!empty($sitepage->profile_type)) {
                    $profile_field_flage = true;
                    foreach ($fieldsProfile_2 as $k => $v) {
                        $explodeField = explode("_", $v);
                        if ($explodeField['1'] == $sitepage->profile_type) {
                            $profile_field_flage = false;
                            break;
                        }
                    }

                    if ($profile_field_flage) {
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }

                foreach ($fieldsProfile_2 as $k => $v) {
                    $explodeField = explode("_", $v);
                    $field_id[] = $explodeField['2'];
                }
                $elements = $form->getElements();
                $orderIndex = 1;
                foreach ($elements as $key => $value) {

                    $explode = explode("_", $key);
                    if ($explode['0'] != "1" && $explode['0'] != "submit") {
                        if (in_array($explode['0'], $field_id)) {
                            $field_id[] = $explode['2'];
                            $fieldsProfile[] = $key;
                            continue;
                        }
                    }
                    if (!in_array($key, $fieldsProfile)) {
                        $form->removeElement($key);
                        $form->addElement('Hidden', $key, array(
                            "value" => "",
                            "order" => ++$orderIndex,
                        ));
                    }
                }
            }//END LEVEL WORK
        }
    }

    //ACTION FOR REMOVE THE PHOTO
    public function removePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main', array(), 'sitepage_main_manage');

        //GET SITEPAGE ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));

        //CHECK FORM SUBMIT
        if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
            $sitepage->photo_id = 0;
            $sitepage->save();
            return $this->_helper->redirector->gotoRoute(array('action' => 'profile-picture', 'page_id' => $sitepage->page_id), 'sitepage_dashboard', true);
        }
    }

    //ACTION FOR UNHIDE THE PHOTO
    public function unhidePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //UNHIDE PHOTO FORM
        $this->view->form = $form = new Sitepage_Form_Unhidephoto();

        //CHECK FORM VALIDAITON
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            Engine_Api::_()->getDbtable('photos', 'sitepage')->update(array('photo_hide' => 0), array('page_id = ?' => $this->_getParam('page_id', null), 'photo_hide = ?' => 1));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photos have been restored.'))
            ));
        }
    }

    //ACTION FOR UPLOADING THE OVERVIEWS PHOTOS FROM THE EDITOR
    public function uploadPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->disableLayout();
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        //PAGE ID
        $page_id = $this->_getParam('page_id');

        $special = $this->_getParam('special', 'overview');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($special == 'overview') {
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
            if (empty($can_edit)) {
                return $this->_forward('requireauth', 'error', 'core');
            }

            if (empty($isManageAdmin)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        } else {
            $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
            if (empty($can_edit) && empty($photoCreate)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }
        //END MANAGE-ADMIN CHECK
        //END MANAGE-ADMIN CHECK
        //IF NOT POST OR FORM NOT VALID, RETURN
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
        //IF NOT POST OR FORM NOT VALID, RETURN
        if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        //PROCESS
        $db = Engine_Api::_()->getDbtable('photos', 'sitepage')->getAdapter();
        $db->beginTransaction();
        try {
            //CREATE PHOTO
            $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');
            $photo = $tablePhoto->createRow();
            $photo->setFromArray(array(
                'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'page_id' => $page_id
            ));
            $photo->save();
            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
            $album = $tableAlbum->getSpecialAlbum($sitepage, $special);
            $tablePhotoName = $tablePhoto->info('name');
            $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
            $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
            $photo->collection_id = $album->album_id;
            $photo->album_id = $album->album_id;
            $order = 0;
            if (!empty($photo_rowinfo)) {
                $order = $photo_rowinfo->order + 1;
            }
            $photo->order = $order;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->file_id;
                $album->save();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            return;
        }
    }

    //ACTION FOR Twitter CODE
    public function twitterAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET PAGE ID AND SITEPAGE OBJECT
        $siteapage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));

        //GENERATE FORM
        $this->view->form = $form = new Sitepage_Form_Twitter();

        //POPULATE THE FORM
        $form->populate($siteapage->toArray());

        if (!$this->getRequest()->isPost())
            return;

        //SAVE THE Twitter CODE IN DATABASE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbtable('pages', 'sitepage')->getAdapter();
            $db->beginTransaction();
            try {
                $siteapage->twitter_user_name = $_POST['twitter_user_name'];
                $siteapage->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your account has been added successfully! You can view your recent tweets on profile of your page.')),
            'parentRefresh' => false,
            'format' => 'smoothbox',
            'smoothboxClose' => 1500,
        ));
    }

    //ACTION FOR Twitter CODE
    public function facebookAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET PAGE ID AND SITEPAGE OBJECT
        $siteapage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));

        //GENERATE FORM
        $this->view->form = $form = new Sitepage_Form_Facebook();

        //POPULATE THE FORM
        $form->populate($siteapage->toArray());

        if (!$this->getRequest()->isPost())
            return;

        //SAVE THE Twitter CODE IN DATABASE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbtable('pages', 'sitepage')->getAdapter();
            $db->beginTransaction();
            try {
                $siteapage->fbpage_url = $_POST['fbpage_url'];
                $siteapage->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Facebook Page has been successfully linked.')),
            'parentRefresh' => false,
            'format' => 'smoothbox',
            'smoothboxClose' => 1500,
        ));
    }

    public function manageMemberCategoryAction() {

        //CHECK PERMISSION FOR VIEW.
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION.
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        $this->view->sitepages_view_menu = 40;

        //GETTING THE OBJECT AND PAGE ID.
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //EDIT PRIVACY
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
        $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);

        if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($manageMemberSettings == 3) {
            $is_admincreated = array("0" => 0, "1" => 1);
            $page_id = array("0" => 0, "1" => $page_id);
        } elseif ($manageMemberSettings == 2) {
            $is_admincreated = array("0" => 0);
            $page_id = array("1" => $page_id);
        }

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');
        $rolesTableName = $rolesTable->info('name');

        

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $row = $rolesTable->createRow();
            $row->is_admincreated = 0;
            $row->role_name = $values['category_name'];
            $row->page_category_id = $sitepage->category_id;
            $row->page_id = $sitepage->page_id;
            $row->save();

            // role Id
            $roleId = $row->getIdentity();
          
            // We will add the new form [Questwalk]
            $values = array(
                'id' => '',
                'title' => 'Member Joining Form: ' . $values['category_name'],
                'description' => '',
                'photo' => '',
                'enable' => 1,
            );
            
            $user = Engine_Api::_() -> user() -> getViewer();
            $table = Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
          
            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $form = $table -> createRow();

                $form -> setFromArray($values);
                $form -> user_id = $user -> getIdentity();

                $optionId = Engine_Api::_()->getApi('core', 'Yndynamicform')->typeCreate($form->title);
                if (!empty($values['photo'])) {
                    $form->setPhoto($new_form->photo);
                }
                $form->option_id = $optionId;
                $form->privacy = 3; // 3 mean everyone can see this form
                $form->page_id = (int) $sitepage->page_id;

                $form -> save();

                $last_form_id =  $form['form_id'];

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                foreach ($roles as $i => $role)
                {
                    $auth->setAllowed($form, $role, 'view', 1);
                    $auth->setAllowed($form, $role, 'comment', 1);
                    $auth->setAllowed($form, $role, 'submission', 1);
                }

                $formmappingTable = Engine_Api::_()->getDbtable('formmappings', 'impactx');
                $formmappingTableName = $formmappingTable->info('name');

                $row = $formmappingTable->createRow();
               
                $row->page_id = $sitepage->page_id;
                $row->form_id = $last_form_id;
                $row->option_id = $optionId ;
                $row->role_id =  $roleId ;
                $row->save();

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

        }
       // $this->view->addfield = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/organizations/manageforms/fields?option_id='.$optionId.'&id='.$last_form_id.'&page_id='. $sitepage->page_id;
        // $this->view->addformfield  = 'organizations/manageforms/fields?option_id='.$optionId.'&id='.$last_form_id.'&page_id='. $sitepage->page_id)
        //$this->view->url1 = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . 'organizations/manageforms/fields?option_id='.$optionId.'&id='.$last_form_id.'&page_id='. $sitepage->page_id;
        
        $select = $rolesTable->select()
            ->from($rolesTableName)
            ->where($rolesTableName . '.is_admincreated IN (?)', (array) $is_admincreated)
            ->where($rolesTableName . '.page_id IN (?)', (array) $page_id)
            ->where($rolesTableName . '.page_category_id = ? ', $sitepage->category_id)
            ->order('role_id DESC');
        $this->view->manageRolesHistories = $rolesTable->fetchALL($select);
    }

    public function editRoleAction() {

        $role_id = (int) $this->_getParam('role_id');
        $page_id = (int) $this->_getParam('page_id');

        $role = Engine_Api::_()->getItem('sitepagemember_roles', $role_id);

        $this->view->form = $form = new Sitepage_Form_EditRole();
        $form->populate($role->toArray());

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            $role->setFromArray($values);
            $role->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'action' => 'manage-member-category', 'page_id' => $page_id), 'sitepage_dashboard', true),
            'parentRedirectTime' => '2',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Roles has been edited successfully.'))
        ));
    }

    //THIS ACTION FOR DELETE MANAGE ADMIN AND CALLING FROM THE CORE.JS FILE.
    public function deleteMemberCategoryAction() {

        $role_id = (int) $this->_getParam('category_id');
        $page_id = (int) $this->_getParam('page_id');
        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');
        $rolesTable->delete(array('role_id = ?' => $role_id, 'page_id = ?' => $page_id));
    }

    public function resetPositionCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET PAGE ID
        $page_id = $this->_getParam("page_id");
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        ////START MANAGE-ADMIN CHECK
        $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($can_edit))
            return;
        $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
        $album = $tableAlbum->getSpecialAlbum($sitepage, 'cover');
        $album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
        $album->save();
    }

    public function getAlbumsPhotosAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
        if (!$sitepagealbumEnabled) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET PAGE ID
        $page_id = $this->_getParam("page_id");
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        ////START MANAGE-ADMIN CHECK
        $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($can_edit))
            return;
        //FETCH ALBUMS
        $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
        $this->view->album_id = $album_id = $this->_getParam("album_id");
        if ($album_id) {
            $this->view->album = $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
            $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
            $paginator->setItemCountPerPage(10000);
        } elseif ($recentAdded) {
            $paginator = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos(array('page_id' => $page_id, 'orderby' => 'photo_id DESC', 'start' => 0, 'end' => 100));
        } else {
            $paramsAlbum['page_id'] = $page_id;
            $paginator = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);
        }
        $this->view->paginator = $paginator;
    }

    public function uploadCoverPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->setLayout('default-simple');
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        //PAGE ID
        $page_id = $this->_getParam('page_id');

        $special = $this->_getParam('special', 'cover');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET FORM
        $this->view->form = $form = new Sitepage_Form_Photo_Cover();

        //CHECK FORM VALIDATION
        $file = '';
        $notNeedToCreate = false;
        $photo_id = $this->_getParam('photo_id');
        if ($photo_id) {
            $photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id);
            $album = Engine_Api::_()->getItem('sitepage_album', $photo->album_id);
            if ($album && $album->type == 'cover') {
                $notNeedToCreate = true;
            }
            if ($photo->file_id && !$notNeedToCreate)
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
        }

        if (empty($photo_id) || empty($photo)) {
            if (!$this->getRequest()->isPost()) {
                return;
            }

            //CHECK FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
        }
        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null || $photo || ($notNeedToCreate && $file)) {
            //PROCESS
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                //CREATE PHOTO
                $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');
                if (!$notNeedToCreate) {
                    $photo = $tablePhoto->createRow();
                    $photo->setFromArray(array(
                        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                        'page_id' => $page_id
                    ));
                    $photo->save();
                    if ($file) {
                        $photo->setPhoto($file);
                    } else {
                        $photo->setPhoto($form->Filedata, true);
                    }


                    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
                    $album = $tableAlbum->getSpecialAlbum($sitepage, $special);

                    $tablePhotoName = $tablePhoto->info('name');
                    $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
                    $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
                    $photo->collection_id = $album->album_id;
                    $photo->album_id = $album->album_id;
                    $order = 0;
                    if (!empty($photo_rowinfo)) {
                        $order = $photo_rowinfo->order + 1;
                    }
                    $photo->order = $order;
                    $photo->save();
                }

                $album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
                $album->save();
                if (!$album->photo_id) {
                    $album->photo_id = $photo->file_id;
                    $album->save();
                }
                $sitepage->page_cover = $photo->photo_id;
                $sitepage->save();
                //ADD ACTIVITY
                $viewer = Engine_Api::_()->user()->getViewer();
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $activityFeedType = null;
                if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
                    $activityFeedType = 'sitepage_admin_cover_update';
                elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
                    $activityFeedType = 'sitepage_cover_update';


                if ($activityFeedType) {
                    $action = $activityApi->addActivity($viewer, $sitepage, $activityFeedType);
                }
                if ($action) {
                    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
                    if ($photo)
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                }

                $this->view->status = true;
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
                return;
            }
        }
    }

    public function removeCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $page_id = $this->_getParam('page_id');
        if ($this->getRequest()->isPost()) {
            $special = $this->_getParam('special', 'cover');
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            $sitepage->page_cover = 0;
            $sitepage->save();
            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
            $album = $tableAlbum->getSpecialAlbum($sitepage, $special);
            $album->cover_params = array('top' => '0', 'left' => 0);
            $album->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    //ACTION TO CHOOSE THE PROJECT FOR THE DONATION TYPE BACKING
    public function chooseProjectAction() {

        //ONLY LOGGED IN USER CAN EDIT THE STYLE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //DO NOT SHOW THE PROJECTS TAB IF ADMIN HAVE PLACED ON THE PAGE PROFILE PAGE
        $adminSelectedProject = Engine_Api::_()->sitecrowdfunding()->adminSelectedProject('sitepage_index_view');
        if(!empty($adminSelectedProject)) {
            return false;
        }
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET PAGE ID AND OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->parent_type = $sitepage->getType();
        $this->view->owner_id = $sitepage->getOwner()->getIdentity();
        //SET PAGE SUBJECT 
        if (!$sitepage->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        $this->view->form = $form = new Sitecrowdfunding_Form_ChooseProjectContentModule(array('item' => $sitepage));
        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key)) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
            }
            if (is_null($value)) {
                $value = "";
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
    }

    public function privacyAction()
    {

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->sitepages_view_menu = 4;
        $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $ownerList = $sitepage->getPageOwnerList();

        if( empty($sitepage) ) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if( empty($isManageAdmin) ) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->owner_id = $owner_id = $sitepage->owner_id;
        $user_subject = Engine_Api::_()->user()->getUser($owner_id);

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Privacy(array('page_id'=>$page_id,'item' => $sitepage, "packageId" => $sitepage->package_id, "layoutId" => $sitepage->layout_id, "owner" => $user_subject, 'create' => 0));
        $values['user_id'] = $viewer_id;

        //SAVE SITEPAGE ENTRY
        if( !$this->getRequest()->isPost() ) {
 
          
            $form->populate($sitepage->toArray());
       

            $auth = Engine_Api::_()->authorization()->context;
            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if( !empty($sitepagememberEnabled) ) {
                $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            foreach( $roles as $roleString ) {
                $role = $roleString;

                if( $form->auth_view && 1 == $auth->isAllowed($sitepage, $role, 'view') ) {
                    $form->auth_view->setValue($roleString);
                }

                if( $form->auth_comment && 1 == $auth->isAllowed($sitepage, $role, 'comment') ) {
                    $form->auth_comment->setValue($roleString);
                }

                if( $role == 'everyone' )
                    continue;

                if( $role === 'like_member' ) {
                    $role = $ownerList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $sitepageAllow = Engine_Api::_()->getApi('allow', 'sitepage');
                if( $form->auth_sspcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sspcreate') ) {
                    $form->auth_sspcreate->setValue($roleString);
                }
                // PHOTO PRIVACY WORK
                if( $form->spcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'spcreate') ) {
                    $form->spcreate->setValue($roleString);
                }
                // DISCUSSION PRIVACY WORK
                if( $form->sdicreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sdicreate') ) {
                    $form->sdicreate->setValue($roleString);
                }
                //SITEPAGEDOCUMENT PRIVACY WORK
                if( $form->sdcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sdcreate') ) {
                    $form->sdcreate->setValue($roleString);
                }
                // SITEPAGEVIDEO PRIVACY WORK
                if( $form->svcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'svcreate') ) {
                    $form->svcreate->setValue($roleString);
                }
                //START SITEPAGEPOLL PRIVACY WORK
                if( $form->splcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'splcreate') ) {
                    $form->splcreate->setValue($roleString);
                }
                //START SITEPAGENOTE PRIVACY WORK
                if( $form->sncreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sncreate') ) {
                    $form->sncreate->setValue($roleString);
                }
                //START SITEPAGEMUSIC PRIVACY WORK
                if( $form->smcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'smcreate') ) {
                    $form->smcreate->setValue($roleString);
                }
                //START SITEPAGEEVENT PRIVACY WORK
                if( $form->secreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'secreate') ) {
                    $form->secreate->setValue($roleString);
                }
            }

            if( Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable() ) {
                if( !empty($sitepage->networks_privacy) ) {
                    $form->networks_privacy->setValue(explode(',', $sitepage->networks_privacy));
                } else {
                    $form->networks_privacy->setValue(array(0));
                }
            }
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
          //  $values['notify_project_donate'] = explode(', ', $values['notify_project_donate']);
           // $values['notify_project_comment'] = explode(', ', $values['notify_project_comment']);
            $form->populate($values);

            return;
        }

        // handle save for tags
        $values = $form->getValues($values);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

          //  $values['notify_project_donate'] = implode(', ', $values['notify_project_donate']);
          //  $values['notify_project_comment'] = implode(', ', $values['notify_project_comment']);

            if( Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable() ) {

                if( isset($values['networks_privacy']) && !empty($values['networks_privacy']) ) {
                    if( in_array(0, $values['networks_privacy']) ) {
                        $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        $form->networks_privacy->setValue(array(0));
                    } else {
                        $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                    }
                }
            }
            $sitepage->setFromArray($values);
            $sitepage->modified_date = date('Y-m-d H:i:s');
            $sitepage->save();

            //CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if( !empty($sitepagememberEnabled) ) {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            $oldViewPrivacy = 'owner';
            foreach( $roles as $role ) {
                if( 1 == $auth->isAllowed($sitepage, $role, 'view') ) {
                    $oldViewPrivacy = $role;
                }
            }

            $values = $form->getValues();
            if( $values['auth_view'] )
                $auth_view = $values['auth_view'];
            else
                $auth_view = "everyone";
            $viewMax = array_search($auth_view, $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
            }

            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if( !empty($sitepagememberEnabled) ) {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            if( $values['auth_comment'] )
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
            }

            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if( !empty($sitepagememberEnabled) ) {
                $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            //START WORK FOR SUB PAGE.
            if( $values['auth_sspcreate'] )
                $subpage = $values['auth_sspcreate'];
            else
                $subpage = "owner";
            $subpageMax = array_search($subpage, $roles);

            foreach( $roles as $i => $role ) {
                if( $role === 'like_member' ) {
                    $role = $ownerList;
                }
                $auth->setAllowed($sitepage, $role, 'sspcreate', ($i <= $subpageMax));
            }
            //END WORK FOR SUBPAGE
            //START DISCUSSION PRIVACY WORK
            $sitepagediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
            if( $sitepagediscussionEnabled ) {
                if( $values['sdicreate'] )
                    $photo = $values['sdicreate'];
                else
                    $photo = "registered";
                $photoMax = array_search($photo, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $photoMax));
                }
            }
            //END DISCUSSION PRIVACY WORK
            //START PHOTO PRIVACY WORK
            $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
            if( $sitepagealbumEnabled ) {
                if( $values['spcreate'] )
                    $photo = $values['spcreate'];
                else
                    $photo = "registered";
                $photoMax = array_search($photo, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $photoMax));
                }
            }
            //END PHOTO PRIVACY WORK
            //START SITEPAGEDOCUMENT WORK
            $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
            if( $sitepageDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {

                if( $values['sdcreate'] )
                    $sdcreate = $values['sdcreate'];
                else
                    $sdcreate = "registered";

                $sdcreateMax = array_search($sdcreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $sdcreateMax));
                }
            }
            //END SITEPAGEDOCUMENT WORK

            //START SITECROWDFUNDING WORK
            $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
            if( $sitecrowdfundingEnabled && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
                if( $values['sprcreate'] )
                    $sprcreate = $values['sprcreate'];
                else
                    $sprcreate = "registered";
                $sprcreateMax = array_search($sprcreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'sprcreate', ($i <= $sprcreateMax));
                }
            }
            //END SITECROWDFUNDING WORK

            //START SITEMUSIC WORK
            $sitemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic');
            if( $sitemusicEnabled && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
                if( $values['samcreate'] )
                    $samcreate = $values['samcreate'];
                else
                    $samcreate = "registered";
                $samcreateMax = array_search($samcreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'samcreate', ($i <= $samcreateMax));
                }
            }
            //END SITEMUSIC WORK

            //START SITEPAGEVIDEO WORK
            $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
            if( $sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
                if( $values['svcreate'] )
                    $svcreate = $values['svcreate'];
                else
                    $svcreate = "registered";
                $svcreateMax = array_search($svcreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $svcreateMax));
                }
            }
            //END SITECROWDFUNDING WORK

            //START SITEPAGEPOLL WORK
            $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
            if( $sitepagePollEnabled ) {
                if( $values['splcreate'] )
                    $splcreate = $values['splcreate'];
                else
                    $splcreate = "registered";
                $splcreateMax = array_search($splcreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $splcreateMax));
                }
            }
            //END SITEPAGEPOLL WORK

            //START SITEPAGENOTE WORK
            $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
            if( $sitepageNoteEnabled ) {
                $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if( !empty($sitepagememberEnabled) ) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }
                if( $values['sncreate'] )
                    $sncreate = $values['sncreate'];
                else
                    $sncreate = "registered";
                $sncreateMax = array_search($sncreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $sncreateMax));
                }
            }
            //END SITEPAGENOTE WORK

            //START SITEPAGEMUSIC WORK
            $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
            if( $sitepageMusicEnabled ) {
                $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if( !empty($sitepagememberEnabled) ) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                if( $values['smcreate'] )
                    $smcreate = $values['smcreate'];
                else
                    $smcreate = "registered";
                $smcreateMax = array_search($smcreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'smcreate', ($i <= $smcreateMax));
                }
            }
            //END SITEPAGENOTE WORK

            //START SITEPAGEEVENT WORK
            $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
            if( $sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
                $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if( !empty($sitepagememberEnabled) ) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }
                if( $values['secreate'] )
                    $secreate = $values['secreate'];
                else
                    $secreate = "registered";
                $secreateMax = array_search($secreate, $roles);

                foreach( $roles as $i => $role ) {
                    if( $role === 'like_member' ) {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'secreate', ($i <= $secreateMax));
                }
            }
            //END SITEPAGEEVENT WORK

            $db->commit();
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            //$sitepage->notify_project_donate = explode(', ', $sitepage->notify_project_donate);
          //  $sitepage->notify_project_comment = explode(', ', $sitepage->notify_project_comment );
            // REBUILD ACTION PRIVACY ONLY IF OBJECT'S VIEW PRIVACY HAS CHANGED
            if ($oldViewPrivacy !== $values['auth_view']) {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach( $actionTable->getActionsByObject($sitepage) as $action ) {
                    $actionTable->resetActivityBindings($action);
                }
            }

            $db->commit();
        } catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
    }
    public function projectPrivacyAction()
    {

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->sitepages_view_menu = 4;
        $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);


        $ownerList = $sitepage->getPageOwnerList();

        if( empty($sitepage) ) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if( empty($isManageAdmin) ) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->owner_id = $owner_id = $sitepage->owner_id;
        $user_subject = Engine_Api::_()->user()->getUser($owner_id);

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_ProjectPrivacy(array('page_id'=>$page_id,'item' => $sitepage, "packageId" => $sitepage->package_id, "layoutId" => $sitepage->layout_id, "owner" => $user_subject, 'create' => 0));
        $values['user_id'] = $viewer_id;

        //SAVE SITEPAGE ENTRY
        if( !$this->getRequest()->isPost() ) {




            if(!$sitepage->notify_project_comment) {
                $form->notify_project_comment->setValue(null);
            }else {
                $form->notify_project_comment->setValue((explode(",",$sitepage->notify_project_comment)));
            }
            if(!$sitepage->notify_project_donate) {
                $form->notify_project_donate->setValue(null);
            }else {
                $form->notify_project_donate->setValue((explode(",",$sitepage->notify_project_donate)));
            }
            $form->is_user_followed_after_comment_yn->setValue($sitepage->is_user_followed_after_comment_yn);
            $form->is_user_followed_after_donate_yn->setValue($sitepage->is_user_followed_after_donate_yn);
           // $form->populate($sitepage->toArray());

            return;
        }
        if( $form->isValid($this->getRequest()->getPost()) ) {
           // $values['notify_project_comment'] = explode(', ', $values['notify_project_comment']);
           // $values['notify_project_donate'] = explode(', ', $values['notify_project_donate']);
            // handle save for tags
            $values = $form->getValues($values);
            if(!$values['notify_project_donate']) {
                $values['notify_project_donate'] = null;
            }
            if(!$values['notify_project_comment']) {
                $values['notify_project_comment'] = null;
            }
            $form->populate($values);



            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $values['notify_project_donate'] = implode(', ', $values['notify_project_donate']);
                $values['notify_project_comment'] = implode(', ', $values['notify_project_comment']);


                $sitepage->setFromArray($values);
                $sitepage->modified_date = date('Y-m-d H:i:s');
                $sitepage->save();


                $notify_project_donate = $values['notify_project_donate'];
                $notify_project_comment= $values['notify_project_comment'];
                $is_user_followed_after_comment_yn =   $values['is_user_followed_after_comment_yn'];
                $is_user_followed_after_donate_yn  =   $values['is_user_followed_after_donate_yn'];


                $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

                if (count($projectsIds) > 0) {

                    $where = array();
                    $where[] = $db->quoteInto('is_privacy_edited_yn = ?', 0);
                    $where[] = $db->quoteInto('project_id IN (?)', $projectsIds);

                      print_r($is_user_followed_after_comment_yn);
                      print_r($is_user_followed_after_donate_yn);

                    $db->update('engine4_sitecrowdfunding_projects', array(
                        'notify_project_donate' => $notify_project_donate,
                        'notify_project_comment' => $notify_project_comment,
                        'is_user_followed_after_comment_yn' => $is_user_followed_after_comment_yn,
                        'is_user_followed_after_donate_yn' => $is_user_followed_after_donate_yn,
                    ), $where);

                }

                $db->commit();
                $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
                return;
            } catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }

        }



    }
    public function metricsAction()
    {

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->user_id = $viewer_id;

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Metrics();

        //$metricsList = Engine_Api::_()->getApi('metrics', 'sitepage');
        $tableMetricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $this->view->metricsList = $metricsList = $tableMetricsTable->getAllMetricsDataByOrganisationId($page_id);


    }
    public function manageMetricsAction()
    {

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->user_id = $viewer_id;

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Metrics();

        //$metricsList = Engine_Api::_()->getApi('metrics', 'sitepage');
        $tableMetricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $this->view->metricsList = $metricsList = $tableMetricsTable->getMetricsDataByOrganisationId($page_id);


    }
    public function editMetricsAction()
    {

        //USER VALDIATION
        if( !$this->_helper->requireUser()->isValid() )
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->user_id = $viewer_id;
        $this->view->metric_id = $metric_id = $this->_getParam('metric_id',null);



        $tableMetricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Editmetrics();

        if($metric_id)
        {
            $this->view->status =  $status = 'edit';
            $this->view->metricDetails = $metricDetails = $tableMetricsTable->getMetricById($metric_id);
            $form->populate($metricDetails->toArray());
        }
        else
        {
            $this->view->status = $status = 'add';
        }

        if($this->getRequest()->getPost() ) {

            if($status =='add') {
                if( $form->isValid($this->getRequest()->getPost()) ) {
                    //get form values
                    $metricsformValues = $form->getValues();
                        //create metric value
                        $metric = $tableMetricsTable->createRow();
                        $metric->metric_name = $metricsformValues['metric_name'];
                        $metric->metric_description = $metricsformValues['metric_description'];
                        $metric->metric_unit = $metricsformValues['metric_unit'];
                        $metric->page_id = $page_id;
                        $metric->user_id = $viewer_id;
                        $metric->save();

                        $auth = Engine_Api::_()->authorization()->context;

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($metric, $role, "view", 1);
                            $auth->setAllowed($metric, $role, "comment", 1);
                        }

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($metric, $role, "topic", 1);
                            $auth->setAllowed($metric, $role, "post", 1);
                        }

                    $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'format' => 'smoothbox',
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Metric added successfully.'))
                    ));

                }
            }
            else if($status =='edit') {

                if( $form->isValid($this->getRequest()->getPost()) ) {
                    //get form values
                    $metricsformValues = $form->getValues();
                        // Update metric details
                        $res =  $tableMetricsTable->update(array(
                            'metric_name' =>  $metricsformValues['metric_name'],
                            'metric_description' => $metricsformValues['metric_description'],
                            'metric_unit' => $metricsformValues['metric_unit']
                        ), array(
                            'metric_id = ?' => (int)$metric_id
                        ));
                    $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'format' => 'smoothbox',
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Metric updated successfully.'))
                    ));
                }

            }
        }



    }

    //metrics popup
    public function metricsAutoSuggestAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING THE PAGE ID.
        $page_id = $this->_getParam('page_id', null);
        $text = $this->_getParam('text', null);

        if($page_id){
            $metrics_array = Engine_Api::_()->getDbtable('metrics', 'sitepage')->getMetricsDataByOrganisationIdAndText($page_id,$text);

            foreach ($metrics_array as $metric) {
                $data[] = array(
                    'id' => $metric->metric_id,
                    'label' => $metric->metric_name,
                    'metric_name' => $metric->metric_name,
                    'metric_description' => $metric->metric_description,
                    'metric_unit' => $metric->metric_unit,
                    'metric_id' => $metric->metric_id,
                    'photo' => ''
                );
            }
        }else{
            $data = array();
        }

        return $this->_helper->json($data);

    }

    public function searchMetricAction()
    {
        $keyword = $this->_getParam('keyword');
        $index = $this->_getParam('index');
        $tableMetricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
        $metricDetails = $tableMetricsTable->getMetricSearcgByName($keyword);
         $vaaal =[];
         foreach ($metricDetails as $val) {
             array_push($vaaal,$val['metric_name']);
         }
        $res = array("index"=>$index, "metric_name"=>$vaaal);

        print_r(json_encode($res));

      //  return $this->_helper->json($vaaal);
       // return $res;
    }

    public function settingsAction()
    {

        //USER VALDIATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->sitepages_view_menu = 5;

        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Settings(array('page_id'=>$sitepage->page_id , 'package_id' => $sitepage->package_id));
        $this->view->editVideoForm = $editVideoForm = new Sitepage_Form_Video_Editvideo();
        $this->view->albumForm = $albumForm = new Sitepage_Form_Album_Photos();

        /**** POPULATE FORM VALUES ****/

        // Settings
        $form->populate($sitepage->toArray());

        // Videos
        $this->view->videos = $videos = array();
        $this->view->integratedWithVideo = false;
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage'));
        if ($sitevideoEnabled && $isIntegrated) {
            $params = array();
            $params['parent_type'] = $sitepage->getType();
            $params['parent_id'] = $sitepage->getIdentity();
            $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
            $this->view->integratedWithVideo = true;
        }
        $this->view->upload_video = 1;
        $this->view->count = count($videos);
        $editVideoForm->removeDecorator('title');
        $editVideoForm->removeDecorator('description');
        foreach ($videos as $video) {
            $videoSubForm = new Sitepage_Form_Video_SubEditVideo(array('elementsBelongTo' => $video->getGuid()));
            if ($video->status != 1) {
                if ($video->status == 0 || $video->status == 2):
                    $msg = $this->view->translate("Your video is currently being processed - you will be notified when it is ready to be viewed.");
                elseif ($video->status == 3):
                    $msg = $this->view->translate("Video conversion failed. Please try again.");
                elseif ($video->status == 4):
                    $msg = $this->view->translate("Video conversion failed. Video format is not supported by FFMPEG. Please try again.");
                elseif ($video->status == 5):
                    $msg = $this->view->translate("Video conversion failed. Audio files are not supported. Please try again.");
                elseif ($video->status == 7):
                    $msg = $this->view->translate("Video conversion failed. You may be over the site upload limit.  Try  a smaller file, or delete some files to free up space.");
                endif;
                $videoSubForm->addElement('dummy', 'mssg' . $video->video_id, array(
                    'description' => $msg,
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'tip')),
                        array('Description', array('tag' => 'span', 'placement' => 'APPEND')),
                        array('Description', array('placement' => 'APPEND')),
                    ),
                ));
                $t = 'mssg' . $video->video_id;
                $videoSubForm->$t->getDecorator("Description")->setOption("placement", "append");
            }
            $videoSubForm->populate($video->toArray());
            $editVideoForm->addSubForm($videoSubForm, $video->getGuid());
            $editVideoForm->cover->addMultiOption($video->video_id, $video->video_id);
        }

        /**** SAVE THE VALUE *****/
        if ($this->getRequest()->isPost()) {

            // Save Settings
            if ($form->isValid($this->getRequest()->getPost())) {
                $settingValues = $form->getValues();
                $sitePageTable = Engine_Api::_()->getItemTable('sitepage_page');
                $sitePageTable->update(array(
                    'member_invite' => $settingValues['member_invite'],
                    'member_approval' => $settingValues['member_approval'],
                    'modified_date' => date('Y-m-d H:i:s')
                ), array(
                    'page_id = ?' => $page_id
                ));
                $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
            }

            // Save Videos
            if ($editVideoForm->isValid($this->getRequest()->getPost())) {
                $formType = $this->getRequest()->getPost('form_type');
                if($formType == 'edit_videos'){
                    foreach ($videos as $video) {
                        $videoSubForm = $editVideoForm->getSubForm($video->getGuid());
                        $editVideovalues = $videoSubForm->getValues();
                        $editVideovalues = $editVideovalues[$video->getGuid()];
                        if (isset($editVideovalues['delete']) && $editVideovalues['delete'] == '1') {
                            Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
                        } else {
                            $video->setFromArray($editVideovalues);
                            $video->save();
                        }
                    }
                }
            }

            // Save Photos
            /*if ($albumForm->isValid($this->getRequest()->getPost())) {
                $formType = $this->getRequest()->getPost('form_type');
                if($formType == 'edit_photos'){
                    $albumFormValues = $albumForm->getValues();
                    //PROCESS
                    foreach ($paginator as $photo) {
                        $photoSubForm = $albumForm->getSubForm($photo->getGuid());
                        $photoSubFormValues = $photoSubForm->getValues();
                        $photoSubFormValues = $photoSubFormValues[$photo->getGuid()];
                        if (isset($photoSubFormValues['delete']) && $photoSubFormValues['delete'] == '1') {
                            $photo->delete();
                        } else {
                            $photo->setFromArray($photoSubFormValues);
                            $photo->save();
                        }
                    }
                }
            }*/

            return $this->_helper->redirector->gotoRoute(array('action' => 'settings', 'page_id' => $sitepage->page_id), 'sitepage_dashboard', true);

        }
    }

    // ACTION FOR GET LINK WORK
    public function getLinkAction() {

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

        $viewer = Engine_Api::_()->user()->getViewer();

        //GET AN ARRAY OF FRIEND IDS

        $friends = $viewer->membership()->getMembers();
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->user_id;
        }

        // IF THERE ARE NO FRIENDS OF VIEWER THEN DON'T DISPLAY SENDINMESSEGE LINK
        $this->view->noSendMessege = 0;
        if (empty($ids)) {
            $this->view->noSendMessege = 1;
        }
        $encode_subjectId = Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode('' . $subject->getIdentity() . '');

        $this->view->url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref() . '/set/' . $encode_subjectId;
        $this->view->subjectType = $subject->getType();
    }

    public function manageMembersAction(){

        //USER VALDIATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->sitepages_view_menu = 5;

        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomORGPendingInvites($page_id);
    }

    public function removeExternalMemberAction(){
        //CHECK AUTH
        if( !$this->_helper->requireUser()->isValid()) return;

        //GET PAGE ID.
        $page_id = $this->_getParam('page_id');
        $invite_id = $this->_getParam('invite_id');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Member();
        $form->setTitle('Remove Member Organization');
        $form->setDescription('Are you sure you want to remove this member ?');
        $form->submit->setLabel('Yes');
        $form->cancel->setLabel('No');

        //PROCESS FORM
        if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{
            $invite = Engine_Api::_()->getItem('invite', $invite_id);
            $invite->delete();
            $msg = 'You have successfully removed this member.';
            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_($msg)),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));

        }
    }

    protected function _forwardCustom($action, $controller = null, $module = null, array $params = null) {
        // Parent
        $request = $this->getRequest();

        if (null !== $params) {
            $request->setParams($params);
        }

        if (null !== $controller) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (null !== $module) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action);
        if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
            $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
        }
        $request->setDispatched(false);
    }

    public function manageProjectsAction(){
        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        //GET PAGE ID, PAGE OBJECT AND PAGE VALIDAITON
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->searchForm = $searchForm = new Sitepage_Form_ProjectsFilter();
        $searchForm->populate($_POST);

        $this->view->sort_field = $_POST['sort_field'];
        $this->view->sort_direction = $_POST['sort_direction'];

        $this->view->projectsIds = $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        $allProjectParams = array();
        $allProjectParams['page'] = $this->_getParam('page', 1);
        $allProjectParams['project_ids'] = $projectsIds;
        $allProjectParams['project_status'] = array("published","successful","failed","submitted","rejected");

        if(!empty($projectsIds) && count($projectsIds) > 0){

            $values = $searchForm->getValues();
            if (isset($_POST['search'])) {
                $allProjectParams['project_name'] = $values['project_name'];
                $allProjectParams['project_id'] = $values['project_id'];
                $allProjectParams['user_name'] = $values['user_name'];
                $allProjectParams['user_id'] = $values['user_id'];
                if(empty($values['project_status'])){
                    $allProjectParams['project_status'] = array("published","successful","failed","submitted","rejected");
                }else{
                    $allProjectParams['project_status'] = $values['project_status'];
                }
                $allProjectParams['funding_status'] = $values['funding_status'];
                $allProjectParams['is_published_yn'] = $values['is_published_yn'];
                $allProjectParams['is_funding_enabled_yn'] = $values['is_funding_enabled_yn'];
                $allProjectParams['is_payment_edit'] = $values['is_payment_edit'];
                $allProjectParams['goal_amount_min'] = $values['goal_amount_min'];
                $allProjectParams['goal_amount_max'] = $values['goal_amount_max'];
                $allProjectParams['project_order'] = $values['project_order'];
                $allProjectParams['sort_field'] = $values['sort_field'];
                $allProjectParams['sort_direction'] = $values['sort_direction'];
            }

            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageProjectsByParamsPaginator($allProjectParams);

        }
    }

    public function approveProjectAction(){

        $project_id = $this->_getParam('project_id');
        $page = $this->_getParam('page',1);
        $page_id = $this->_getParam('page_id');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $owner = $project->getOwner();
            $sender = Engine_Api::_()->user()->getViewer();
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            if($project->is_fund_raisable){
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

            if($settings->getSetting('sitecrowdfunding.activity.project.approval', 0)) {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_funding');
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                }
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'manage-projects', 'page_id' => $page_id, 'page'=> $page), 'sitepage_dashboard', true);
    }

    public function rejectProjectAction(){

        $project_id = $this->_getParam('project_id');
        $page = $this->_getParam('page', 1);
        $page_id = $this->_getParam('page_id');

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $projectTable = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $db1 = $projectTable->getAdapter();
        $db1->beginTransaction();
        try {
            $owner = $project->getOwner();
            $sender = Engine_Api::_()->user()->getViewer();
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            $project->approved = 0;
            $project->state = 'rejected';
            $project->status = 'initial';
            //if normal project rejected means need to reject funding also
            $project->funding_approved = 0;
            $project->funding_state = 'rejected';
            $project->funding_status = 'initial';
            if($settings->getSetting('sitecrowdfunding.reminder.project.disapproval', 0)) {
                Engine_Api::_()->sitecrowdfunding()->sendMailCustom('DISAPPROVED', $project_id);
            }
            if($settings->getSetting('sitecrowdfunding.notification.project.disapproval', 0)) {
                //SEND NOTIFICATION TO PROJECT OWNER
                $type = 'sitecrowdfunding_project_disapproved';
                $notifyApi->addNotification($owner, $sender, $project, $type);
            }

            $project->save();

            $db1->commit();

        }catch (Exception $e){
            $db1->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'manage-projects', 'page_id' => $page_id, 'page'=> $page), 'sitepage_dashboard', true);

    }

    public function createMetricsAction(){

        if (empty($_POST) && !isset($_POST['metric_name']) && !isset($_POST['metric_description']) && !isset($_POST['metric_unit'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }

        $metric_name = $values['metric_name'];
        $metric_description = $values['metric_description'];
        $metric_unit = $values['metric_unit'];
        $page_id = $values['page_id'];
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $metric_id = null;
        try {

            $tableMetricsTable = Engine_Api::_()->getDbtable('metrics', 'sitepage');
            $metric = $tableMetricsTable->createRow();
            $metric->metric_name = $metric_name;
            $metric->metric_description = $metric_description;
            $metric->metric_unit = $metric_unit;
            $metric->page_id = $page_id;
            $metric->user_id = $viewer_id;
            $metric->save();

            $metric_id = $metric->metric_id;

            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $i => $role) {
                $auth->setAllowed($metric, $role, "view", 1);
                $auth->setAllowed($metric, $role, "comment", 1);
            }

            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            foreach ($roles as $i => $role) {
                $auth->setAllowed($metric, $role, "topic", 1);
                $auth->setAllowed($metric, $role, "post", 1);
            }


            $this->view->metric_id = $metric_id;
            $this->view->metric_name = $metric_name;
            $this->view->metric_description = $metric_description;

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }

    public function visibleMetricAction(){

        if (empty($_POST) || !isset($_POST['metric_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }
        $metric_id = $values['metric_id'];

        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if ($metric->visibility) {
                $metric->visibility = 0;
            }
            else {
                $metric->visibility = 1;
            }
            $metric->save();
            $db->commit();

        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }

        return true;
    }
    
    public function getAvailableMetricsAction() {

        $response = array();
        $result = '';
        $page_id = $_POST['page_id'];
        $selected_metric_id = !empty($_POST['selected_metric_id'])? $_POST['selected_metric_id']: 0;
        $metricsSuggestion = Engine_Api::_()->impactx()->metricsSuggestion($page_id, '');
        
        if( !empty($metricsSuggestion) ){
            $tempMetricsArray = array();
            $metricAutocompletePostVal = Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_autocomplete', '');
            foreach($metricsSuggestion as $metricSuggestion) {
                $metricsID = $metricSuggestion['id'];
                $metricsLabel = $metricSuggestion['label'];
                $metricsMetricName = str_replace("\\'", "'", $metricSuggestion['metric_name']);
                $metricsMetricDescription = str_replace("\\'", "'", $metricSuggestion['metric_description']);
                $metricsMetricUnit = $metricSuggestion['metric_unit'];
                $metricsMetricId = $metricSuggestion['metric_id'];
                $tempMetricsArray[$metricsID] = $metricsLabel;
                
                if( !empty($selected_metric_id) && ($selected_metric_id == $metricSuggestion['id']) ) {
                    $response['is_selected'] = 1;
                    $response['matrics_id'] = $metricsID;
                    $response['matrics_label'] = $metricsLabel;
                    $response['selected_matrics_name'] = $metricsMetricName;
                    $response['metrics_description'] = $metricsMetricDescription;
                    $response['metrics_unit'] = $metricsMetricUnit;
                    $response['selected_metrics_id'] = $metricsMetricId;
                    
                    $result .= "<div class='metric_multiradio_single_div'><input type='radio' checked value='".$metricSuggestion['id']."' class='metric_input' name='metric_autocomplete' id='metric_autocomplete' onclick=\"select_metric('".$metricsID."', '".$metricsLabel."', '".$metricsMetricName."', '".$metricsMetricDescription."', '".$metricsMetricUnit."', '".$metricsMetricId."'); insert_metric();\"><label>".$metricsMetricName."</label></div>";
                }else {
                    if( empty($response['is_selected']) )
                        $response['is_selected'] = 0;
                    
                    $result .= "<div class='metric_multiradio_single_div'><input type='radio' value='".$metricSuggestion['id']."' class='metric_input' name='metric_autocomplete' id='metric_autocomplete' onclick=\"select_metric('".$metricsID."', '".$metricsLabel."', '".$metricsMetricName."', '".$metricsMetricDescription."', '".$metricsMetricUnit."', '".$metricsMetricId."'); insert_metric();\"><label>".$metricsMetricName."</label></div>";
                }
            }
            
            $response['output'] = $result;
        }
        echo Zend_Json::encode($response);
        exit;
    }

}
