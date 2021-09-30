<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_ManageadminController extends Seaocore_Controller_Action_Standard {

    //ACTION FOR SHOWING PAGES FOR WHICH I AM ADMIN
    public function myPagesAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //MANAGE ADMIN IS ALLOWED OR NOT BY ADMIN
        $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
        if (empty($manageAdminEnabled)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //GETTING THE VIEWER AND VIEWER ID AND PASS VALUE .TPL FILE.
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->owner_id = $viewer_id = $viewer->getIdentity();

        //CHEKC FOR MEMBER LEVEL SETTINGS FOR EDIT AND DELETE AND CREATE.
        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->checkRequire();
        $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'edit')->checkRequire();
        $this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'delete')->checkRequire();

        $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main', array(), 'sitepage_main_manage');

        //GET QUICK NAVIGATION
        $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_quick');

        $this->view->form = $form = new Sitepage_Form_Myadminpages();

        //PROCESS FORM
        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        }

        //RATING ENABLE / DISABLE
        $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');

        //GET PAGES
        $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($viewer_id);

        //GET STUFF
        $ids = array();
        foreach ($adminpages as $adminpage) {
            $ids[] = $adminpage->page_id;
        }
        $values['adminpages'] = $ids;
        $values['orderby'] = 'creation_date';
        //$values['notIncludeSelfPages'] = $viewer_id;

        //GET PAGINATOR.
        $this->view->paginator = $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, null);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);

        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        //MAXIMUN ALLOWED PAGES.
        $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();

        if(Engine_Api::_()->seaocore()->isSitemobileApp()) {
            //SET SCROLLING PARAMETTER FOR AUTO LOADING.
            if (!Zend_Registry::isRegistered('scrollAutoloading')) {
                Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
            }
        }
        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
        $this->view->page = $this->_getParam('page', 1);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalCount) /$items_count);
        if (!$isappajax) {
            $this->_helper->content/*->setNoRender()*/->setEnabled();
        }
    }

    //MANAGE ADMINS ACTION FOR THE PAGES.
    public function indexAction() {

        //CHECK PERMISSION FOR VIEW.
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION.
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

        $this->view->sitepages_view_menu = 11;

        //GETTING THE VIEWER AND VIEWER ID.
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //GETTING THE OBJECT AND PAGE ID.
        $this->view->page_id = $page_id = $this->_getParam('page_id', null);
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->owner_id = $sitepage->owner_id;
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //EDIT PRIVACY
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
        if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

        //FETCH DATA
        $this->view->manageAdminUsers = $manageadminsTable->getManageAdminUser($page_id);
        $this->view->manageInvitedUsers = $manageadminsTable->getInvitedAdminUsers($page_id);

        if ($this->getRequest()->isPost()) {

            $values = $this->getRequest()->getPost();
            $selected_user_id = $values['user_id'];
            $selected_user_email = $values['user_email'];

            // declare tables
            $userTable = Engine_Api::_()->getItemTable('user');
            $userTableName = $userTable->info('name');
            $ManageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
            $ManageAdminsTableName = $ManageAdminsTable->info('name');

            // if only email passed and userId is empty
            if( $selected_user_id == 0 && !empty($selected_user_email) ) {

                // check if the given string is an email type
                $find1 = strpos($selected_user_email, '@');
                $find2 = strpos($selected_user_email, '.');
                $isEmailYn =  ($find1 !== false && $find2 !== false && $find2 > $find1);

                if(!$isEmailYn){
                    $this->view->message = $selected_user_email . ' - Enter a valid email address !' ;
                    return false;
                }

                // check if email is present in user table
                $selected_user_id = $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $selected_user_email)->query()->fetchColumn();

                // check if userId is present in admin table
                if(!empty($selected_user_id)){
                    $userIdPresent = true;
                    $data = $ManageAdminsTable->select()
                        ->from($ManageAdminsTableName)
                        ->where($ManageAdminsTableName . '.page_id = ?', $page_id)
                        ->where($ManageAdminsTableName . '.user_id = ?', $selected_user_id)
                        ->where($ManageAdminsTableName . '.member_email is null')
                        ->query()->fetchColumn();
                }else{
                    $userIdPresent = false;
                    // check if email is exists with userId
                    $data = $ManageAdminsTable->select()
                        ->from($ManageAdminsTableName)
                        ->where($ManageAdminsTableName . '.page_id = ?', $page_id)
                        ->where($ManageAdminsTableName . '.member_email = ?', $selected_user_email)
                        ->where($ManageAdminsTableName . '.user_id is null')
                        ->query()->fetchColumn();
                }

                if(!empty($data)){
                    echo '<div class="tip"> <span> Member exist already ! </span>  </div>';
                    return;
                }else{
                    // insert into table
                    if($userIdPresent === true){
                        $row = $manageadminsTable->createRow();
                        $row->user_id = $selected_user_id;
                        $row->page_id = $page_id;
                        $row->save();
                    }else{
                        $row = $manageadminsTable->createRow();
                        $row->user_id = 0;
                        $row->page_id = $page_id;
                        $row->member_email =$values['user_email'];
                        $row->save();
                    }
                }
            }

            // if user id is passed
            else if( $selected_user_id != 0 ) {
                $data = $ManageAdminsTable->select()
                    ->from($ManageAdminsTableName)
                    ->where($ManageAdminsTableName . '.page_id = ?', $page_id)
                    ->where($ManageAdminsTableName . '.user_id = ?', $selected_user_id)
                    ->where($ManageAdminsTableName . '.member_email is null')
                    ->query()->fetchColumn();

                if(!empty($data)){
                    $this->view->message = 'Member exist already !';
                    return false;
                }else{
                    // insert into table
                    $row = $manageadminsTable->createRow();
                    $row->user_id = $selected_user_id;
                    $row->page_id = $page_id;
                    $row->save();
                }
            }

            //START SITEPAGEMEMBER PLUGIN WORK
            $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if ($sitepageMemberEnabled && $selected_user_id && $page_id) {
                $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $membersTableName = $membersTable->info('name');

                $select = $membersTable->select()
                    ->from($membersTableName)
                    ->where('user_id = ?', $selected_user_id)
                    ->where($membersTableName . '.resource_id = ?', $page_id);
                $select = $membersTable->fetchRow($select);

                if(empty($select)) {
                    $row = $membersTable->createRow();
                    $row->resource_id = $page_id;
                    $row->page_id = $page_id;
                    $row->user_id = $selected_user_id;
                    $row->save();
                }
            }
            //END SITEPAGEMEMBER PLUGIN WORK

            // Send emails
            if(!empty($selected_user_id)) {
                $user = Engine_Api::_()->getItem('user', $selected_user_id);
                $sendEmail =  $user->email;
            }else{
                $sendEmail = $selected_user_email;
            }

             $sitepage_title = $sitepage->title;
             $page_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view') . ">$sitepage_title</a>";

             $viewer_title = $viewer->getTitle();
             $user_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . $viewer->getHref() . ">$viewer_title</a>";

             $host = $_SERVER['HTTP_HOST'];
             $page_url = $host . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view');

             Engine_Api::_()->getApi('mail', 'core')->sendSystem($sendEmail, 'SITEPAGE_MANAGEADMIN_EMAIL', array(
                 'page_title_with_link' => $page_title_with_link,
                 'sender' => $user_title_with_link,
                 'page_url' => $page_url,
                 'queue' => true
             ));

            if(!empty($user)){
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($user, $viewer, $sitepage, 'sitepage_manageadmin');
            }

            //INCREMENT MESSAGE COUNTER.
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
        }
    }

    //ACTINO FOR USER AUTO-SUGGEST LIST
    public function manageAutoSuggestAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING THE PAGE ID.
        $page_id = $this->_getParam('page_id', $this->_getParam('id', null));

        //FETCH DATA
        $user_idarray = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->linkedPages($page_id);

        $user_id_array = '';
        if (!empty($user_idarray)) {
            foreach ($user_idarray as $key => $user_ids) {
                $user_id_array = $user_ids['user_id'] . ',' . $user_id_array;
            }
        }
        $user_id_array = $user_id_array . '0';
        $noncreate_owner_level = array();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
            $can_create = 0;
            if ($level->type != "public") {
                $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitepage_page', 'create');
                if (empty($can_create)) {
                    $noncreate_owner_level[] = $level->level_id;
                }
            }
        }

        $usertable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $usertable->info('name');
        $select = $usertable->select()
            ->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ? OR " . $userTableName . ".email LIKE ?", '%' . $this->_getParam('text') . '%')
            ->where($userTableName . '.user_id NOT IN (' . $user_id_array . ')')
            ->order('displayname ASC')
            ->limit($this->_getParam('limit', 40));

        if (!empty($noncreate_owner_level)) {
            $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
            $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
        }

        //FETCH ALL RESULT.
        $userlists = $usertable->fetchAll($select);
        $data = array();

        foreach ($userlists as $userlist) {
            $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
            if($userlist->displayname){
                $labelName = $userlist->displayname;
                $email = $userlist->email;
            }else{
                $labelName = $userlist->email;
                $email = '';
            }
            $data[] = array(
                'id' => $userlist->user_id,
                'label' => $labelName,
                'photo' => $content_photo,
                'email' => $email
            );
        }

        if ($this->_getParam('sendNow', true)) {

            //RETURN TO THE RETRIVE RESULT.
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    //THIS ACTION FOR DELETE MANAGE ADMIN AND CALLING FROM THE CORE.JS FILE.
    public function deleteAction() {

        $manageadmin_id = (int) $this->_getParam('managedelete_id');
        $owner_id = (int) $this->_getParam('owner_id');
        $page_id = (int) $this->_getParam('page_id');
        $manageAdmintable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $manageAdmintable->delete(array('manageadmin_id = ?' => $manageadmin_id,'page_id = ?' => $page_id));

        //STAR WORK SITEPAGE INTREGRATION.
        if(!empty($owner_id)){
            $sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
            if (!empty($sitepageintegrationEnabled)) {
                $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitepageintegration');
                $contentsTable->delete(array('resource_owner_id = ?' => $owner_id, 'page_id = ?' => $page_id));
            }
        }
        //END WORK OF SITEPAGE INTREGRATION.
    }

    //ACTION FOR FEATURED ADMIN
    public function listAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET PAGE ID AND PAGE OBJECT
        $page_id = $this->_getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //EDIT PRIVACY
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

        //FETCH DATA
        $this->view->owners = $manageTable->getManageAdmin($page_id);

        //CHECK POST
        if ($this->getRequest()->isPost()) {

            //GET VALUES FROM FORM
            $values = $this->getRequest()->getPost();
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $manageTable->update(array('featured' => 0), array('page_id = ?' => $page_id));

                foreach ($values as $key => $value) {
                    $manageTable->update(array('featured' => 1), array('user_id = ?' => $key, 'page_id = ?' => $page_id));
                }
                $db->commit();
                $this->_forwardCustom('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('action' => 'featured-owners', 'page_id' => $page_id), 'sitepage_dashboard'),
                    'parentRedirectTime' => '1',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Featured admins of your page have been updated successfully.'))
                ));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
}

?>