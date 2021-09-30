<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitecrowdfunding_MemberController extends Seaocore_Controller_Action_Standard {

    protected $_TEMPDATEVALUE = 75633;

    //ACTION FOR MEMBER JOIN THE PAGE.
    public function joinAction() {

        //CHECK AUTH
        if( !$this->_helper->requireUser()->isValid() ) return;

        //SOMMTHBOX
        $this->_helper->layout->setLayout('default-simple');

        //MAKE FORM
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->form = $form = new Sitecrowdfunding_Form_Project_Join();
        } else {
            $this->view->form = $form = new Sitecrowdfunding_Form_Project_SitemobileJoin();
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $owner = $project->getOwner();

        /*$action_notification = array();
        $notificationSettings = Engine_Api::_()->getDbTable('memberships', 'sitecrowdfunding')->notificationSettings(array('user_id' => $project->owner_id, 'project_id' => $project_id, 'columnName' => array('action_notification')));
        if($notificationSettings){
            $action_notification = Zend_Json_Decoder::decode($notificationSettings);
        }*/

        //IF MEMBER IS ALREADY PART OF THE PROJECT
        $hasMembers = Engine_Api::_()->getDbTable('memberships', 'sitecrowdfunding')->hasMembers($viewer_id, $project_id);
        if(!empty($hasMembers)) {
            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this project.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }

        //PROCESS FORM
        if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

            // Sent notification to project owner
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $project, 'project_member_joined');

            //ADD ACTIVITY
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $project, 'project_member_joined');
            if ( $action ) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
            }

            //GET VALUE FROM THE FORM.
            $values = $this->getRequest()->getPost();

            $membersTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
            $row = $membersTable->createRow();
            $row->resource_id = $project_id;
            $row->project_id = $project_id;
            $row->user_id = $viewer_id;
            $row->resource_approved = 1;
            $row->active = 1;
            $row->user_approved = 1;

            //FOR CATEGORY WORK.
            if (isset($values['role_id'])) {
                $roleName = array();
                foreach($values['role_id'] as $role_id) {
                    $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->getRoleName($role_id);
                }
                $roleTitle = json_encode($roleName);
                $roleIDs = json_encode($values['role_id']);
                if ($roleTitle && $roleIDs) {
                    $row->title = $roleTitle;
                    $row->role_id = $roleIDs;
                }
            }

            //IF MEMBER IS ALREADY FEATURED THEN AUTOMATICALLY FEATURED WHEN MEMBER JOIN ANY PAGE.
            $projectmember = Engine_Api::_()->getDbTable('memberships', 'sitecrowdfunding')->hasMembers($viewer_id);
            if(!empty($projectmember->featured) && $projectmember->featured == 1) {
                $row->featured = 1;
            }

            $row->save();



            //START DISCUSSION WORK WHEN MEMBER JOIN THE PAGE THEN ALL DISCUSSION IS WATCHABLE FOR JOINED MEMBERS.
            //END DISCUSSION WORK WHEN MEMBER JOIN THE PAGE THEN ALL DISCUSSION IS WATCHABLE FOR JOINED MEMBERS.

            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this project.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));

        }
    }

    public function removeExternalMemberAction(){
        //CHECK AUTH
        if( !$this->_helper->requireUser()->isValid()) return;

        //GET PAGE ID.
        $project_id = $this->_getParam('project_id');
        $invite_id = $this->_getParam('invite_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Member();
        $form->setTitle('Remove Member Project');
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

    //ACTION FOR LEAVE THE PAGE.
    public function leaveAction() {

        //CHECK AUTH
        if( !$this->_helper->requireUser()->isValid()) return;

        //GET PAGE ID.
        $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $membership_id = $this->_getParam('membership_id');
        $list_id = $this->_getParam('list_id');
        $member_email = $this->_getParam('member_email');
        if(!empty($project_id) && $membership_id ) {
            $owner = $project->getOwner();
        }
        $user_id = '';
        // if user id is passed, then it is someone or else logged in person
        if(!empty($membership_id)) {
            $user_id = $this->_getParam('user_id');
        }
        elseif(!$this->_getParam('user_id')){
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        }else{
            $user_id = $this->_getParam('user_id');
        }

        $user = Engine_Api::_()->user()->getUser($user_id);

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Member();

        if($this->_getParam('user_id') || !empty($membership_id)){
            $type = 'REMOVED';
            $msg = 'You have successfully remove this member.';
            $form->setTitle('Remove Member Project');
            $form->setDescription('Are you sure you want to remove this member ?');
            $form->submit->setLabel('Yes');
            $form->cancel->setLabel('No');
        }else{
            $type = 'LEAVE';
            $msg = 'You have successfully left this project.';
            $form->setTitle('Leave Project');
            $form->setDescription('Are you sure you want to leave this project ?');
            $form->submit->setLabel('Leave Project');
        }


        //IF THE MODE IS APP MODE THEN
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            $this->view->sitemapPageHeaderTitle = "Leave Project";
            $form->setTitle('');
        }

        //PROCESS FORM
        if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{
            if(!empty($project_id) && $membership_id ) {

                $membersTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
                $membersTable->delete(array('membership_id = ?' => $membership_id));
            }
            elseif (!empty($project_id)) {

                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->delete(array('resource_id =?' => $project_id, 'user_id = ?' => $user_id));

                if($type == 'LEAVE'){

                    // Sent notification to project owner
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $owner, $project, 'project_member_leave');

                    // add activity to user
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $project, 'project_member_leave');
                    if ( $action ) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                    }

                }

                if($type == 'REMOVED'){

                    // Sent notification to user
                 //   Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner,$owner, $project, 'project_member_removed');

                    // add activity to project owner
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $project, 'project_member_removed');
                    if ( $action ) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                    }

                }

            }
            // external admin member remove
            if($member_email && $list_id) {
                //DELETE THE EXTERNAL PROJECT ADMIN FROM THE TABLE.
                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');

                //fetch the external project admin - invited mebers - custom code
                $member_email_listtable = $listItemTable->select()
                    ->from($listItemTableName)
                    ->where('member_email = ?', $member_email)->query()->fetchColumn();

                Engine_Api::_()->getDbtable('listitems', 'sitecrowdfunding')->delete(array(
                    'listitem_id = ?' => $member_email_listtable,
                ));

            }

            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_($msg)),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));

        }
    }

    //ACTION FOR USER AUTO SUGGEST.
    public function getMembersAction() {

        $data = array();

        //GET COUPON ID.
        $project_id = $this->_getParam('project_id', null);

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info('name');

        $select = $membershipTable->select()
            ->from($membershipTableName, 'user_id')
            ->where('project_id = ?', $project_id);
        $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $user_count = count($user_ids);
        $autoRequest = '';

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $autoRequest = $this->_getParam('user_ids', null);
        } else {
            $autoRequest = $this->_getParam('text', null);
        }

        if($user_count > 0){
            $select = $usersTable->select()
                ->where($usersTableName . ".displayname LIKE ? OR " . $usersTableName . ".username LIKE ? OR " . $usersTableName . ".email LIKE ?", '%' . $autoRequest . '%')
                ->where($usersTableName . '.user_id NOT IN (?)', (array) $user_ids)
                ->order('displayname ASC')
                ->limit('40');
        } else {
            $select = $usersTable->select()
                ->where($usersTableName . ".displayname LIKE ? OR " . $usersTableName . ".username LIKE ? OR " . $usersTableName . ".email LIKE ?", '%' . $autoRequest . '%')
                ->order('displayname ASC')
                ->limit('40');
        }

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

        return $this->_helper->json($data);
    }

    //ACTION FOR THE INVITE MEMBER.
    public function inviteMembersAction() {

        if( !$this->_helper->requireUser()->isValid() ) return;

        //GET PAGE ID.
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $owner = $project->getOwner();

        $viewer = Engine_Api::_()->user()->getViewer();

        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        $member_approval = $project->member_approval;

        //PREPARE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_InviteMembers(array(
           'project_id' => $project_id
        ));

        $this->view->externalform = $externalform = new Sitecrowdfunding_Form_Project_InviteExternal(array(
            'project_id' => $project_id
        ));

        $externalform->populate(array(
            'project_id' => $project_id,
            'message' => 'You are being invited to join our ImpactX and Join to the Project - '. $project->getTitle()
        ));

       // $externalform->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitecrowdfunding', 'controller' => 'member' ,'action' => 'invite-external' , 'project_id' => $project_id ),'sitecrowdfunding_extended', true));

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        $values = $form->getValues();
        $user_id=null;
        if( $values['toValues'] && strpos($values['toValues'], '@') !== false && strpos($values['toValues'], '.') !== false) {
            $userTable = Engine_Api::_()->getItemTable('user');
            $userTableName = $userTable->info('name');

            $user_id=null;

            if(strpos($values['toValues'], ',') !== false){
                $getMail = explode(",", $values['toValues']);

                $members_ids =array();
                foreach ($getMail as $val) {
                    if(strpos($val, '@') !== false)
                        $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $values['toValues'])->query()->fetchColumn();
                    else {
                        $user_id = $val;
                    }
                    array_push($members_ids,$user_id);
                }

            } else{
                $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $values['toValues'])->query()->fetchColumn();
                $members_ids =  (array) $user_id;
                $memberEmail = $values['toValues'] ? $values['toValues'] :null;
            }
            array_unique($members_ids);



        }
        else {
             $members_ids = explode(",", $values['toValues']);
        }

        if(!empty($memberEmail) && !$user_id){

            $viewer = Engine_Api::_()->user()->getViewer();
            $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
            $db = $inviteTable->getAdapter();
            $db->beginTransaction();
            $msg = 'You are being invited to join our ImpactX and Join to the Project - '.$project->getTitle();
            try {
                $emailsSent = $inviteTable->sendCustomInvites($viewer, $memberEmail, $msg,1, $project_id, $memberEmail, 1);

                /***
                 *
                 * send notification and email to all project admins
                 *
                 ***/
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                $host = $_SERVER['HTTP_HOST'];
                $list = $project->getLeaderList();
                $list_id = $list['list_id'];
                $project_link = $view->htmlLink($host . $project->getHref(), $project->title);

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

                $selectUsers = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                    ->order('displayname ASC');

                $adminMembers = $userTable->fetchAll($selectUsers);

                foreach($adminMembers as $adminMember){
                    $admin_link = $view->htmlLink($host . $adminMember->getHref(), $adminMember->getTitle());

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, "notify_admin_invite_project", array(
                        'admin_link' => $admin_link,
                        'project_link' => $project_link,
                        'user_name' => $memberEmail,
                        'queue' => false,
                    ));
                }

                $db->commit();
            } catch( Exception $e ) {
                $db->rollBack();
                if( APPLICATION_ENV == 'development' ) {
                    throw $e;
                }
            }
        }

        if(empty($values['toValues'])) {
            $form->addError('This is an invalid user name. Please select a valid user name from the autosuggest.');
            return;
        }

        $membersTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membersTableName = $membersTable->info('name');

        if (!empty($members_ids)) {

            foreach($members_ids as $members_id) {

               if($members_id){

                    $row = $membersTable->createRow();
                    $row->resource_id = $project_id;
                    $row->project_id = $project_id;
                    $row->user_id = $members_id;
//                    if(!$members_id && $memberEmail) {
//                        $row->member_email = $memberEmail;
//                    }
                    $row->resource_approved = 1;

                    if ($isProjectAdmin == 1) {
                        $row->active = 1;
                        $row->user_approved = 1;
                        $row->save();
                    } elseif ( $member_approval == 1 && $isProjectAdmin == 0) {
                        $row->active = 1;
                        $row->user_approved = 1;
                        $row->save();
                    }
                    else {
                        $row->active = 0;
                        $row->user_approved = 0;
                    }

                    //FOR CATEGORY WORK.
                    if (isset($values['role_id'])) {
                        $roleName = array();
                        foreach($values['role_id'] as $role_id) {
                            $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->getRoleName($role_id);
                        }
                        $roleTitle = json_encode($roleName);
                        $roleIDs = json_encode($values['role_id']);
                        if ($roleTitle && $roleIDs) {
                            $row->title = $roleTitle;
                            $row->role_id = $roleIDs;
                        }
                    }

                    $row->save();

                    if ($member_approval==1) {

                        $user_subject = Engine_Api::_()->user()->getUser($members_id);

                        // send notification to project owner
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $user_subject, $project, 'project_member_joined');

                        // send notification to invited user
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $project, 'project_member_person_invite');

                        // add activity
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user_subject, 'project_member_invited');
                        if ( $action ) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                        }

                    }
                    else {
                        $user_subject = Engine_Api::_()->user()->getUser($members_id);

                        // send notification to project owner
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $user_subject, $project, 'project_member_invite_pending');

                        // send notification to invited user
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $project, 'project_member_person_invite');

                        // add activity
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user_subject, 'project_member_invited');
                        if ( $action ) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                        }

                    }

                }
            }

            $user_names = array();

            foreach($members_ids as $members_id) {
               if($members_id) {
                    //   send invite
                    $user = Engine_Api::_()->getItem('user', $members_id);
                    $memberEmail = $user->email;

                    $viewer = Engine_Api::_()->user()->getViewer();
                    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
                    $db = $inviteTable->getAdapter();
                    $db->beginTransaction();

                    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                    $host = $_SERVER['HTTP_HOST'];
                    $project_link = $view->htmlLink($host . $project->getHref(), $project->title);
                    $viewer_user_name = $view->htmlLink($host . $viewer->getHref(), $viewer->getTitle());

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($memberEmail, "invite_project", array(
                        'project_link' => $project_link,
                        'user_name' => $user->displayname,
                        'viewer_user_name' => $viewer_user_name,
                        'queue' => false,
                    ));

                   $user_names = $view->htmlLink($host . $user->getHref(), $user->getTitle());
                   /***
                    *
                    * send notification and email to all project admins
                    *
                    ***/
                   $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                   $host = $_SERVER['HTTP_HOST'];
                   $list = $project->getLeaderList();
                   $list_id = $list['list_id'];
                   $project_link = $view->htmlLink($host . $project->getHref(), $project->title);

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

                   $selectUsers = $userTable->select()
                       ->from($userTableName)
                       ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                       ->order('displayname ASC');

                   $adminMembers = $userTable->fetchAll($selectUsers);

                   foreach($adminMembers as $adminMember){
                       $admin_link = $view->htmlLink($host . $adminMember->getHref(), $adminMember->getTitle());

                       Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, "notify_admin_invite_project", array(
                           'admin_link' => $admin_link,
                           'project_link' => $project_link,
                           'user_name' => $user_names,
                           'queue' => false,
                       ));
                   }
                }
            }
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully added to this project.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

    public function inviteExternalAction(){

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        $values = $_POST;

        if(!empty($values['recipients'])){

            $viewer = Engine_Api::_()->user()->getViewer();
            $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
            $db = $inviteTable->getAdapter();
            $db->beginTransaction();

            try {
                $emailsSent = $inviteTable->sendCustomInvites($viewer, $values['recipients'], @$values['message'],$values['friendship'], $values['project_id'], $values['recipient_name'], $values['role_id']);
                $db->commit();
            } catch( Exception $e ) {
                $db->rollBack();
                if( APPLICATION_ENV == 'development' ) {
                    throw $e;
                }
            }

            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Invited.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }

    }

    //ACTION FOR THE LIST MEMBER.
    public function listMembersAction(){

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // GET PROJECT.
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);

        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomPendingInvites($project_id);

    }

    //ACTION FOR ACCEPT THE PAGE.
    public function acceptMemberAction() {

        //CHECK AUTH
        if( !$this->_helper->requireUser()->isValid()) return;

        //GET PAGE ID.
        $project_id = $this->_getParam('project_id');
        $user_id = $this->_getParam('user_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $owner = $project->getOwner();

        $viewer = Engine_Api::_()->user()->getViewer();

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Member();
        $form->setTitle('Accept Member');
        $form->setDescription('Are you sure you want to accept this member?');
        $form->submit->setLabel('Yes');
        $form->cancel->setLabel('No');

        //PROCESS FORM
        if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

            if (!empty($project_id)) {

                //UPDATE
                Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->update(array(
                    'user_approved' => 1,
                    'active' => 1
                ), array(
                    'resource_id =? ' => $project_id,
                    'user_id = ?' => $user_id
                ));

                //ADD NOTIFICATION TO USER,THAT THEY HAVE JOINED
                $user = Engine_Api::_()->user()->getUser($user_id);
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $project, 'project_member_invite_accept');

                // add activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'project_member_invite_accept');
                if ( $action ) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                }

            }

            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully accepted this member.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }
    }

    //ACTION FOR LEAVE THE PAGE.
    public function rejectMemberAction() {

        //CHECK AUTH
        if( !$this->_helper->requireUser()->isValid()) return;

        //GET PAGE ID.
        $project_id = $this->_getParam('project_id');
        $user_id = $this->_getParam('user_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $owner = $project->getOwner();

        $viewer = Engine_Api::_()->user()->getViewer();

        //MAKE FORM
        $this->view->form = $form = new Sitepagemember_Form_Member();
        $form->setTitle('Reject Member');
        $form->setDescription('Are you sure you want to reject this member?');
        $form->submit->setLabel('Yes');
        $form->cancel->setLabel('No');

        //PROCESS FORM
        if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

            if (!empty($project_id)) {

                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->delete(array('resource_id =?' => $project_id, 'user_id = ?' => $user_id));

                $user = Engine_Api::_()->user()->getUser($user_id);
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $project, 'project_member_invite_reject');

                // add activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'project_member_invite_reject');
                if ( $action ) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $project ) ;
                }

            }

            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully rejected this member.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }
    }


}
