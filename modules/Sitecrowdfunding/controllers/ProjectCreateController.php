<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_ProjectCreateController extends Seaocore_Controller_Action_Standard
{

    protected $_hasPackageEnable;

    public function init()
    {
        //SET THE SUBJECT
        if (0 !== ($project_id = (int)$this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    public function uploadPhotoAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $album = $project->getSingletonAlbum();

        $this->view->form = $form =new Sitecrowdfunding_Form_Project_Create_StepSixPhoto(array(
            'project_id' => $project_id
        ));

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;


        //saving the photo
        if (!empty($values['photo'])) {

            // removing the old photo
            Engine_Api::_()->getDbTable('photos','sitecrowdfunding')->delete(array(
                'project_id = ? ' => $project_id
            ));


            $photo_id =  $project->setPhoto($form->photo)->photo_id;
            $photo = Engine_Api::_()->getItem("sitecrowdfunding_photo", $photo_id);
            if($photo) {
                //updating the album id in photo item
                $photo->collection_id = $album->album_id;
                $photo->album_id = $album->album_id;
                $photo->save();
                // saving the photo id in project
                $project->photo_id = $photo->file_id;
                $project->save();

                $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
                $tableOtherinfo->update(array('profile_cover' => 1), array('project_id = ?' => $project_id));

            }


            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo uploaded successfully'))
            ));

        }

    }

    public function uploadVideoAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->form = $form =new Sitecrowdfunding_Form_Project_Create_StepSixVideo(array(
            'project_id' => $project_id
        ));

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        if($values['video_valid'] != true){
            $error = $this->view->translate('Please upload valid youtube url.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }


        //saving the video
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try{
            $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');

            //deleting old video
            $table->delete(array(
                'parent_type = ?' => $project->getType(),
                'parent_id = ?' => $project_id
            ));

            $video = $table->createRow();
            $video->code = $values['video_code'];
            $video->type = $values['video_type'];
            $video->duration = $values['video_duration'];
            $video->title = $values['video_title'];
            $video->description = $values['video_description'];
            $video->owner_id = $viewer_id;
            $video->owner_type = $viewer->getType();
            $video->creation_date = date('Y-m-d H:i:s');
            $video->parent_type = $project->getType();
            $video->parent_id   = $project_id;
            $video->status = 1;
            $video->save();


            // CREATE AUTH STUFF HERE
            $auth  = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if ($values['auth_view']) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = "everyone";
            }

            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if ($values['auth_comment']) {
                $auth_comment = $values['auth_comment'];
            } else {
                $auth_comment = "everyone";
            }

            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }

            $thumbnail = $this->handleThumbnail($video->type, $video->code);
            $video->saveVideoThumbnail($thumbnail);
            $video->save();

            $project->video_id = $video->video_id;
            $project->save();


            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
            $tableOtherinfo->update(array('profile_cover' => 0), array('project_id = ?' => $project_id));


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Video uploaded successfully.'))
        ));
    }

    public function addOrganizationAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->form = $form = new Sitecrowdfunding_Form_Organization_Create(array('project_id'=> $project_id));
        $form->removeDecorator('description');
        $form->cancel->setAttribs(array(
            'href' => 'javascript:void(0)',
            'onclick' => 'parent.Smoothbox.close();'
        ));
        $form->execute1->setLabel('Save');

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if($_POST['is_internal'] == 1){
            $form->title->setRequired(false);
            $form->title->setAllowEmpty(true);

        }else{
            $form->organization_id->setRequired(false);
            $form->organization_id->setAllowEmpty(true);
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->showForm = true;
            return;
        }else{
            $this->view->showForm = false;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        if($values['is_internal'] == 1){

            if(empty($values['organization_id'])){
                $error = $this->view->translate('Please select organization - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $table = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
            $pagerow = $table->createRow();
            $pagerow->project_id = $project_id;
            $pagerow->page_id = $values['organization_id'];
            $pagerow->page_type = $values['organization_type'];
            $pagerow->owner_id = $viewer_id;
            $pagerow->save();

        }else{

            //title IS REQUIRED FIELD
            if (empty($values['title'])) {
                $error = $this->view->translate('Please complete title field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $table = Engine_Api::_()->getDbTable('organizations', 'sitecrowdfunding');
            $organization = $table->createRow();

            $file_id = null;
            if (!empty($values['photo'])) {
                $file_id =  $organization->setLogo($form->photo);
            }
            $organization->title = $values['title'];
            $organization->description = $values['description'];
            $organization->project_id = $project_id;
            $organization->organization_type = $values['organization_type'];
            $organization->others = $values['others'];
            $organization->link = $values['link'];
            $organization->logo = $file_id;
            $organization->save();
        }

        return $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Organization added successfully'))
        ));
        //return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);


    }

    public function addOutcomeOldAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Outcome(array('project_id'=> $project_id));
        $form->removeDecorator('description');
        $form->cancel->setAttribs(array(
            'href' => 'javascript:void(0)',
            'onclick' => 'parent.Smoothbox.close();'
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_outcome');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $outcome = $table->createRow();

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'project_id' => $project_id,
                    'user_id' => $viewer_id
                );
                $outcome->setFromArray($inputs);
                $outcome->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Outcome added successfully.'))
            ));
        }
    }

    public function addMilestoneAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Milestone(array('project_id'=> $project_id));
        $form->setTitle('Add Project Milestones');
        $form->removeDecorator('description');
        $form->cancel->setAttribs(array(
            'href' => 'javascript:void(0)',
            'onclick' => 'parent.Smoothbox.close();'
        ));


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //START DATE AND END DATE ARE REQUIRED
            if (empty($values['starttime'])) {
                $error = $this->view->translate('Please enter Start Date - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));

            if(!empty($values['endtime'])){
                $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
            }else{
                $endDate = null;
            }

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_milestone');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $milestone = $table->createRow();

                $file_id = null;
                if (!empty($values['photo'])) {
                    $file_id = $milestone->setLogo($form->photo);
                }

                if($endDate != null){
                    $endDate =  date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));
                }

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'question' => $values['question'],
                    'start_date' => date('Y-m-d', strtotime($startDate)),
                    'end_date' => $endDate,
                    'status' => $values['status'],
                    'project_id' => $project_id,
                    'logo' => $file_id
                );
                $milestone->setFromArray($inputs);
                $milestone->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Milestone added successfully.'))
            ));
        }

    }

    public function getAdminMembersAction()
    {

        //GET Project ID.
        $project_id = $this->_getParam('project_id', null);

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $list = $project->getLeaderList();

        $list_id = $list['list_id'];
        $text = $this->_getParam('text', null);

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
            ->setIntegrityCheck(false)
            ->from($userTableName);

        $select = $select->where("$userTableName.user_id NOT IN (?)", (array)$selectLeaders);
        $select = $select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ? OR " . $userTableName . ".email LIKE ?", '%' . $text . '%')
            ->where($userTableName . '.user_id != 0')
            ->order('displayname ASC')
            ->limit(20);

        //FETCH ALL RESULT.
        $userlists = $userTable->fetchAll($select);
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

        return $this->_helper->json($data);
    }

    public function addAdminMembersAction()
    {

        // require user
        if (!$this->_helper->requireUser()->isValid()) return;

        // get viewer user
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PROJECT DETAILS
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $list = $project->getLeaderList();

        //PREPARE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepEightAdmin(array(
            'project_id' => $project_id
        ));

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // if form is submitted
        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPost())) {

                $values = $form->getValues();


                //if user enters as email send inivte - custom code
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


                if (empty($values['toValues'])) {
                    $form->addError('Please complete this field - It is requried.');
                    return;
                }

                if (empty($values['toValues'])) {
                    $form->addError('This is an invalid user name. Please select a valid user name from the autosuggest.');
                    return;
                }


                if(!empty($memberEmail) && !$user_id){

                    //Find all the list id using project id
                    $listTable = Engine_Api::_()->getItemTable('sitecrowdfunding_list');

                    $listID = $listTable->select()->from($listTable->info('name'), '*')
                        ->where('owner_id = ?', $project_id)->query()
                        ->fetchColumn();

                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();
                    $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                    $row = $listItemTable->createRow();
                    $row->list_id = $listID;
                    $row->child_id = 0;
                    $row->member_email = $memberEmail;
                    $row->save();
                    $db->commit();

                    //send mail invite to external user
                    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                    $host = $_SERVER['HTTP_HOST'];
                    $project_link = $view->htmlLink($host . $project->getHref(), $project->title);
                    $profile_link = $view->htmlLink($host . $viewer->getHref(), $viewer->getTitle());

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($memberEmail, "invite_project_admin", array(
                        'profile_link' => $profile_link,
                        'project_link' => $project_link,
                        'queue' => false,
                    ));

                    /***
                     *
                     * send notification and email to all project admins
                     *
                     ***/
                    $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                    $list = $project->getLeaderList();
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

                    $selectUsers = $userTable->select()
                        ->from($userTableName)
                        ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                        ->order('displayname ASC');

                    $adminMembers = $userTable->fetchAll($selectUsers);

                    foreach($adminMembers as $adminMember){
                        $admin_link = $view->htmlLink($host . $adminMember->getHref(), $adminMember->getTitle());
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, "notify_admin_invite_project_admin", array(
                            'profile_link' => $memberEmail,
                            'project_link' => $project_link,
                            'admin_link' => $admin_link,
                            'queue' => false,
                        ));
                    }

                    // update the list
//                        $settings = Engine_Api::_()->getApi('settings', 'core');
//                        $db = Engine_Db_Table::getDefaultAdapter();
//                        $db->beginTransaction();
//                        try {
//                            $listss = Engine_Api::_()->getItem('sitecrowdfunding_lists', $listID);
//                            if($_POST['project_order']) {
//                                $listss->child_count =  $listss->child_count + 1;
//                            }
//                            $listss->save();
//                            $db->commit();
//
//                        }catch (Exception $e){
//                            $db->rollBack();
//                            throw $e;
//                        }



                }


                $auth = Engine_Api::_()->authorization()->context;

                if (!empty($members_ids)) {

                    foreach ($members_ids as $members_id) {

                      //  $user = Engine_Api::_()->getItem('user', $members_id);
                        $user = Engine_Api::_()->user()->getUser($members_id);

                        //RETURN IF USER IS NOT ALREADY A LEADER: A CASE WHEN WE CLICK MULTIPLE TIMES
                        if (!$list->has($user)) {

                            $table = $list->getTable();
                            $db = $table->getAdapter();
                            $db->beginTransaction();

                            try {
                                $list->add($user);
                                $leaderList = $project->getLeaderList();

                                // Create some auth stuff for all leaders
                                $auth->setAllowed($project, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($project, $leaderList, 'edit', 1);
                                $auth->setAllowed($project, $leaderList, 'delete', 1);

                                // Add notification
                                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                                $notifyApi->addNotification($user, $viewer, $project, 'sitecrowdfunding_create_leader');

                                //Send email
                                $host = $_SERVER['HTTP_HOST'];
                                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                                $profile_link = $view->htmlLink($host . $viewer->getHref(), $viewer->getTitle());
                                $project_link = $view->htmlLink($host . $project->getHref(), $project->title);


                                if(!empty($memberEmail) && !$user_id) {
                                    $sitecrowdfunding_create_leader_email = $memberEmail;
                                    $sitecrowdfunding_create_leader_email_link = $memberEmail;
                                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($memberEmail, "sitecrowdfunding_create_leader", array(
                                        'profile_link' => $profile_link,
                                        'project_link' => $project_link,
                                        'user_name' => $user->displayname
                                    ));

                                } else{
                                    $sitecrowdfunding_create_leader_email = $user->email;
                                    $sitecrowdfunding_create_leader_email_link = $view->htmlLink($host . $user->getHref(), $user->getTitle());
                                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, "sitecrowdfunding_create_leader", array(
                                        'profile_link' => $profile_link,
                                        'project_link' => $project_link,
                                        'user_name' => $user->displayname
                                    ));
                                 }

                                /***
                                 *
                                 * send notification and email to all project admins
                                 *
                                 ***/
                                $list = $project->getLeaderList();
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

                                $selectUsers = $userTable->select()
                                    ->from($userTableName)
                                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                                    ->where("$userTableName.email != ?",$sitecrowdfunding_create_leader_email)
                                    ->order('displayname ASC');

                                $adminMembers = $userTable->fetchAll($selectUsers);

                                foreach($adminMembers as $adminMember){
                                    $admin_link = $view->htmlLink($host . $adminMember->getHref(), $adminMember->getTitle());
                                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, "notify_admin_sitecrowdfunding_create_leader", array(
                                        'profile_link' => $sitecrowdfunding_create_leader_email_link,
                                        'project_link' => $project_link,
                                        'admin_link' => $admin_link
                                    ));
                                }

                                // Add activity
                                $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
                                $activityApi->addActivity($user, $project, 'sitecrowdfunding_promote');

                                $db->commit();
                            } catch (Exception $e) {
                                $db->rollBack();
                                throw $e;
                            }

                        }

                    }
                }

                return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully added as into this project.')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                ));

            }
        }

    }

    public function stepZeroAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get project-id
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        // get initiative-id
        $this->view->initiative_id= $initiative_id =  $this->_getParam('initiative_id',null);

        // get page-id
        $this->view->page_id= $page_id =  $this->_getParam('page_id',null);
        $this->view->create_new = false;

        if($page_id || $initiative_id){
            $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);
            if(count($initiatives) > 0 || $initiative_id) {
                $this->view->is_initiative_exist = true;


                if ($project_id){
                    $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                }

                $viewer = Engine_Api::_()->user()->getViewer();
                $viewer_id = $viewer->getIdentity();
                $this->view->level_id = $viewer->level_id;

                // populate the values
                if ($project_id){

                    $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
                    if(empty($parentOrganization)){
                        $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
                    }

                    // set page_id and initiative_id based on it
                    if(!empty($parentOrganization['page_id'])){
                        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepZero(array('page_id' => $parentOrganization['page_id']));
                        $form->page_id->setValue($parentOrganization['page_id']);
                        if(!empty($project->initiative_id)){
                            $this->view->initiative_id= $project->initiative_id;
                            $form->initiative_id->setValue($project->initiative_id);
                        }else{
                            $this->view->initiative_id = null;
                            $form->initiative_id->setValue(null);
                            $form->getElement('initiative_id')->setRegisterInArrayValidator(FALSE);
                        }
                    }else{
                        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepZero(array('page_id' => $page_id));
                        if(!empty($page_id)){
                            $form->page_id->setValue($page_id);
                        }
                        $this->view->initiative_id = null;
                        $form->initiative_id->setValue(null);
                        $form->getElement('initiative_id')->setRegisterInArrayValidator(FALSE);
                    }

                }else{
                    //MAKE FORM
                    $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepZero(array('page_id' => $page_id,'initiative_id'=>$initiative_id));
                    if(!empty($page_id)){
                        $form->page_id->setValue($page_id);
                    }
                    if(!empty($initiative_id)){
                        $this->view->initiative_id = $initiative_id;
                        $form->initiative_id->setValue($initiative_id);
                    }else{
                        $this->view->initiative_id = null;
                        $form->initiative_id->setValue(null);
                    }
                    $form->getElement('initiative_id')->setRegisterInArrayValidator(FALSE);
                }

                if (!$this->getRequest()->isPost()){
                    return;
                }

                // if(!$form->isValid($this->getRequest()->getPost())){
                //    return;
                //  }

                $formValues = $form->getValues();

                if (empty($formValues))
                    return;

                $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {

                    // if no project_id, then create it
                    if (empty($project_id)){

                        // get initiative_id
                        if(!empty($formValues['initiative_id'])){
                            $this->view->initiative_id = $initiative_id = $formValues['initiative_id'];
                        }else{
                            $this->view->initiative_id = $initiative_id = null;
                        }

                        if(!empty($formValues['page_id'])){
                            $projectIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($formValues['page_id']);
                            if(count($projectIds)){
                                $projectsCount = count($projectIds) + 1;
                            }else{
                                $projectsCount = 1;
                            }
                        }else{
                            $projectsCount = 1;
                        }

                        // save values
                        $values = array(
                            'owner_type' => $viewer->getType(),
                            'owner_id' => $viewer_id,
                            'parent_type' => $viewer->getType(),
                            'parent_id' => $viewer_id,
                            'featured' => 0,
                            'sponsored' => 0,
                            'approved' => 0,
                            'status' => 'initial',
                            'state' => 'draft',
                            'start_date' => null,
                            'expiration_date' => null,
                            'funding_start_date' => null,
                            'funding_end_date' => null,
                            'is_fund_raisable' => 1,
                            'initiative_id' => $initiative_id,
                            'project_order' => $projectsCount
                        );

                        // get page-id
                        $this->view->page_id= $page_id ;

                        $projectModel = $table->createRow();
                        $projectModel->setFromArray($values);
                        $projectModel->save();

                        // PRIVACY WORK , LEVEL WORK
                        if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                            if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                                if (in_array(0, $values['networks_privacy'])) {
                                    unset($values['networks_privacy']);
                                } else {
                                    $values['networks_privacy'] = implode(',', $values['networks_privacy']);
                                }
                            }
                        }

                        $auth = Engine_Api::_()->authorization()->context;

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                        $leaderList = $projectModel->getLeaderList();

                        if (empty($values['auth_view'])) {
                            $values['auth_view'] = "everyone";
                        }

                        if (empty($values['auth_comment'])) {
                            $values['auth_comment'] = "registered";
                        }

                        $viewMax = array_search($values['auth_view'], $roles);
                        $commentMax = array_search($values['auth_comment'], $roles);

                        foreach ($roles as $i => $role) {

                            if ($role === 'leader') {
                                $role = $leaderList;
                            }
                            $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                            $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
                        }

                        $ownerList = '';

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                        if (empty($values['auth_topic'])) {
                            $values['auth_topic'] = "registered";
                        }
                        if (isset($values['auth_post']) && empty($values['auth_post'])) {
                            $values['auth_post'] = "registered";
                        }

                        $topicMax = array_search($values['auth_topic'], $roles);
                        $postMax = '';
                        if (isset($values['auth_post']) && !empty($values['auth_post']))
                            $postMax = array_search($values['auth_post'], $roles);

                        foreach ($roles as $i => $role) {

                            if ($role === 'leader') {
                                $role = $leaderList;
                            }
                            $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                            if (!is_null($postMax)) {
                                $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                            }
                        }

                        // Create some auth stuff for all leaders
                        $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                        $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                        $auth->setAllowed($projectModel, $leaderList, 'delete', 1);

                        $projectModel->category_id = 14;
                        $projectModel->save();
                        $project_id = $projectModel->getIdentity();

                        Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->insert(array(
                            'project_id' => $project_id,
                            'overview' => ""
                        ));

                        // insert into project's organisation
                        $tablePage = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
                        if(empty($tablePage->getPageRow($project_id , $page_id))){
                            $pagerow = $tablePage->createRow();
                            $pagerow->project_id = $project_id;
                            $pagerow->page_id = $page_id;
                            $pagerow->page_type = 'parent';
                            $pagerow->owner_id = $viewer_id;
                            $pagerow->save();
                        }

                        // update the initiative section in project tags
                        /*if (isset($initiative_id) && !empty($initiative_id)) {
                            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
                            if (isset($initiative['sections']) && !empty($initiative['sections'])) {
                                $sections = preg_split('/[,]+/', $initiative['sections']);
                                $sections = array_filter(array_map("trim", $sections));
                                if (count($sections) >0 ) {
                                    $projectModel->tags()->setTagMaps($viewer, $sections);
                                }
                            }
                        }*/

                        $db->commit();

                    }
                    // if project_id is there, then update it
                    else{

                        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

                        // get initiative_id
                        if (!empty($formValues['initiative_id'])) {
                            $this->view->initiative_id = $initiative_id = $formValues['initiative_id'];
                        } else {
                            $this->view->initiative_id = $initiative_id = null;
                        }

                        // get page_id
                        $this->view->page_id = $page_id = $formValues['page_id'];

                        // update initiative_id
                        $project->initiative_id = $initiative_id;
                        $project->save();

                        // update page_id
                        // insert into project's organisation
                        $tablePage = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
                        $tablePage->delete(array(
                            'project_id = ?' => $project_id
                        ));
                        $pagerow = $tablePage->createRow();
                        $pagerow->project_id = $project_id;
                        $pagerow->page_id = $page_id;
                        $pagerow->page_type = 'parent';
                        $pagerow->owner_id = $viewer_id;
                        $pagerow->save();

                        $db->commit();
                    }

                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }

                return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-one', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);
            }
            else {

                $this->view->is_initiative_exist = false;

                //ONLY LOGGED IN USER CAN CREATE
                if (!$this->_helper->requireUser()->isValid())
                    return;

                $this->view->page_id = $page_id = $this->_getParam('page_id');
                if ($page_id){

                    // get initiative_id
                    $this->view->initiative_id = $initiative_id = null;

                    if(!$page_id){
                        $projectIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);
                        if(count($projectIds)){
                            $projectsCount = count($projectIds) + 1;
                        }else{
                            $projectsCount = 1;
                        }
                    }else{
                        $projectsCount = 1;
                    }
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $viewer_id = $viewer->getIdentity();
                    $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
                    $db = $table->getAdapter();
                    $db->beginTransaction();
                    // save values
                    $values = array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer_id,
                        'parent_type' => $viewer->getType(),
                        'parent_id' => $viewer_id,
                        'featured' => 0,
                        'sponsored' => 0,
                        'approved' => 0,
                        'status' => 'initial',
                        'state' => 'draft',
                        'start_date' => null,
                        'expiration_date' => null,
                        'funding_start_date' => null,
                        'funding_end_date' => null,
                        'is_fund_raisable' => 1,
                        'initiative_id' => $initiative_id,
                        'project_order' => $projectsCount
                    );

                    // get page-id
                    $this->view->page_id= $page_id ;

                    $projectModel = $table->createRow();
                    $projectModel->setFromArray($values);
                    $projectModel->save();

                    // PRIVACY WORK , LEVEL WORK
                    if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                        if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                            if (in_array(0, $values['networks_privacy'])) {
                                unset($values['networks_privacy']);
                            } else {
                                $values['networks_privacy'] = implode(',', $values['networks_privacy']);
                            }
                        }
                    }

                    $auth = Engine_Api::_()->authorization()->context;

                    $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    $leaderList = $projectModel->getLeaderList();

                    if (empty($values['auth_view'])) {
                        $values['auth_view'] = "everyone";
                    }

                    if (empty($values['auth_comment'])) {
                        $values['auth_comment'] = "registered";
                    }

                    $viewMax = array_search($values['auth_view'], $roles);
                    $commentMax = array_search($values['auth_comment'], $roles);

                    foreach ($roles as $i => $role) {

                        if ($role === 'leader') {
                            $role = $leaderList;
                        }
                        $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                        $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
                    }

                    $ownerList = '';

                    $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                    if (empty($values['auth_topic'])) {
                        $values['auth_topic'] = "registered";
                    }
                    if (isset($values['auth_post']) && empty($values['auth_post'])) {
                        $values['auth_post'] = "registered";
                    }

                    $topicMax = array_search($values['auth_topic'], $roles);
                    $postMax = '';
                    if (isset($values['auth_post']) && !empty($values['auth_post']))
                        $postMax = array_search($values['auth_post'], $roles);

                    foreach ($roles as $i => $role) {

                        if ($role === 'leader') {
                            $role = $leaderList;
                        }
                        $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                        if (!is_null($postMax)) {
                            $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                        }
                    }

                    // Create some auth stuff for all leaders
                    $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                    $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                    $auth->setAllowed($projectModel, $leaderList, 'delete', 1);

                    $projectModel->category_id = 14;
                    $projectModel->save();
                    $project_id = $projectModel->getIdentity();

                    Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->insert(array(
                        'project_id' => $project_id,
                        'overview' => ""
                    ));

                    // insert into project's organisation
                    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
                    if(empty($tablePage->getPageRow($project_id , $page_id))){
                        $pagerow = $tablePage->createRow();
                        $pagerow->project_id = $project_id;
                        $pagerow->page_id = $page_id;
                        $pagerow->page_type = 'parent';
                        $pagerow->owner_id = $viewer_id;
                        $pagerow->save();
                    }

                    // update the initiative section in project tags
                    /*if (isset($initiative_id) && !empty($initiative_id)) {
                        $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
                        if (isset($initiative['sections']) && !empty($initiative['sections'])) {
                            $sections = preg_split('/[,]+/', $initiative['sections']);
                            $sections = array_filter(array_map("trim", $sections));
                            if (count($sections) >0 ) {
                                $projectModel->tags()->setTagMaps($viewer, $sections);
                            }
                        }
                    }*/

                    $db->commit();
                    $this->view->project_id = $project_id ;
                }
                else {
                    $this->view->project_id = $project_id = $this->_getParam('project_id');
                }
                if ($project_id){
                    $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                }
                $viewer = Engine_Api::_()->user()->getViewer();
                $viewer_id = $viewer->getIdentity();
                $this->view->level_id = $viewer->level_id;


                //MAKE FORM
                $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepOne();
                $form->removeElement('execute');

                $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-zero',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

                if($project_id && !empty($project)){
                    $form->populate(
                        array(
                            'is_fund_raisable'=> $project->is_fund_raisable,
                            'goal_amount' => $project->goal_amount,
                            'invested_amount' => $project->invested_amount
                        )
                    );
                }

                if (!$this->getRequest()->isPost()){
                    return;
                }

                if($_POST['is_fund_raisable']==0) {
                    $form->goal_amount->setRequired(false);
                    $form->goal_amount->setAllowEmpty(true);
                    $form->invested_amount->setRequired(false);
                    $form->invested_amount->setAllowEmpty(true);
                }

                if(!$form->isValid($this->getRequest()->getPost())){
                    return;
                }


                $values = $form->getValues();
                if (empty($values))
                    return;

                $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {

                    if($project_id){

                        if($values['is_fund_raisable']){
                            if($values['goal_amount'] <= 0){
                                $error = $this->view->translate('What are the total funds needed including the amount you are contributing? Please pick a number greater than 0');
                                $error = Zend_Registry::get('Zend_Translate')->_($error);
                                $form->getDecorator('errors')->setOption('escape', false);
                                $form->addError($error);
                                return;
                            }
                        }

                        // remove comma from amount fields to fix decimal issue
                        $values['goal_amount'] = str_replace( ',', '', $values['goal_amount'] );
                        $values['invested_amount'] = str_replace( ',', '', $values['invested_amount'] );

                        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                        $project->is_fund_raisable = $values['is_fund_raisable'];
                        $project->goal_amount = $values['goal_amount'];
                        $project->invested_amount = $values['invested_amount'];
                        $project->save();
                        $db->commit();
                    }
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }

                return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-two', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);













            }
        }else {
            if(1) {
                $this->view->is_initiative_exist = true;
                $this->view->create_new = true;
                // get initiative-id
                $this->view->initiative_id= $initiative_id =  $this->_getParam('initiative_id');

                if ($project_id){
                    $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                }

                $viewer = Engine_Api::_()->user()->getViewer();
                $viewer_id = $viewer->getIdentity();
                $this->view->level_id = $viewer->level_id;

                // populate the values
                if ($project_id){

                    $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
                    if(empty($parentOrganization)){
                        $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
                    }

                    // set page_id and initiative_id based on it
                    if(!empty($parentOrganization['page_id'])){
                        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepZero(array('page_id' => $parentOrganization['page_id']));
                        $form->page_id->setValue($parentOrganization['page_id']);
                        if(!empty($project->initiative_id)){
                            $this->view->initiative_id= $project->initiative_id;
                            $form->initiative_id->setValue($project->initiative_id);
                        }else{
                            $this->view->initiative_id = null;
                            $form->initiative_id->setValue(null);
                            $form->getElement('initiative_id')->setRegisterInArrayValidator(FALSE);
                        }
                    }else{
                        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepZero(array('page_id' => $page_id));
                        if(!empty($page_id)){
                            $form->page_id->setValue($page_id);
                        }
                        $this->view->initiative_id = null;
                        $form->initiative_id->setValue(null);
                        $form->getElement('initiative_id')->setRegisterInArrayValidator(FALSE);
                    }

                }else{
                    //MAKE FORM
                    $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepZero(array('page_id' => $page_id,'initiative_id'=>$initiative_id));
                    if(!empty($page_id)){
                        $form->page_id->setValue($page_id);
                    }
                    if(!empty($initiative_id)){
                        $this->view->initiative_id = $initiative_id;
                        $form->initiative_id->setValue($initiative_id);
                    }else{
                        $this->view->initiative_id = null;
                        $form->initiative_id->setValue(null);
                    }
                    $form->getElement('initiative_id')->setRegisterInArrayValidator(FALSE);
                }

                if (!$this->getRequest()->isPost()){
                    return;
                }

                if(!$form->isValid($this->getRequest()->getPost())){
                    return;
                }

                $formValues = $form->getValues();

                if (empty($formValues))
                    return;

                $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {

                    // if no project_id, then create it
                    if (empty($project_id)){

                        // get initiative_id
                        if(!empty($formValues['initiative_id'])){
                            $this->view->initiative_id = $initiative_id = $formValues['initiative_id'];
                        }else{
                            $this->view->initiative_id = $initiative_id = null;
                        }

                        if(!empty($formValues['page_id'])){
                            $projectIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($formValues['page_id']);
                            if(count($projectIds)){
                                $projectsCount = count($projectIds) + 1;
                            }else{
                                $projectsCount = 1;
                            }
                        }else{
                            $projectsCount = 1;
                        }

                        // save values
                        $values = array(
                            'owner_type' => $viewer->getType(),
                            'owner_id' => $viewer_id,
                            'parent_type' => $viewer->getType(),
                            'parent_id' => $viewer_id,
                            'featured' => 0,
                            'sponsored' => 0,
                            'approved' => 0,
                            'status' => 'initial',
                            'state' => 'draft',
                            'start_date' => null,
                            'expiration_date' => null,
                            'funding_start_date' => null,
                            'funding_end_date' => null,
                            'is_fund_raisable' => 1,
                            'initiative_id' => $initiative_id,
                            'project_order' => $projectsCount
                        );

                        // get page-id
                        $this->view->page_id= $page_id ;

                        $projectModel = $table->createRow();
                        $projectModel->setFromArray($values);
                        $projectModel->save();

                        // PRIVACY WORK , LEVEL WORK
                        if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                            if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                                if (in_array(0, $values['networks_privacy'])) {
                                    unset($values['networks_privacy']);
                                } else {
                                    $values['networks_privacy'] = implode(',', $values['networks_privacy']);
                                }
                            }
                        }

                        $auth = Engine_Api::_()->authorization()->context;

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                        $leaderList = $projectModel->getLeaderList();

                        if (empty($values['auth_view'])) {
                            $values['auth_view'] = "everyone";
                        }

                        if (empty($values['auth_comment'])) {
                            $values['auth_comment'] = "registered";
                        }

                        $viewMax = array_search($values['auth_view'], $roles);
                        $commentMax = array_search($values['auth_comment'], $roles);

                        foreach ($roles as $i => $role) {

                            if ($role === 'leader') {
                                $role = $leaderList;
                            }
                            $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                            $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
                        }

                        $ownerList = '';

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                        if (empty($values['auth_topic'])) {
                            $values['auth_topic'] = "registered";
                        }
                        if (isset($values['auth_post']) && empty($values['auth_post'])) {
                            $values['auth_post'] = "registered";
                        }

                        $topicMax = array_search($values['auth_topic'], $roles);
                        $postMax = '';
                        if (isset($values['auth_post']) && !empty($values['auth_post']))
                            $postMax = array_search($values['auth_post'], $roles);

                        foreach ($roles as $i => $role) {

                            if ($role === 'leader') {
                                $role = $leaderList;
                            }
                            $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                            if (!is_null($postMax)) {
                                $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                            }
                        }

                        // Create some auth stuff for all leaders
                        $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                        $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                        $auth->setAllowed($projectModel, $leaderList, 'delete', 1);

                        $projectModel->category_id = 14;
                        $projectModel->save();
                        $project_id = $projectModel->getIdentity();

                        Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->insert(array(
                            'project_id' => $project_id,
                            'overview' => ""
                        ));

                        // insert into project's organisation
                        $tablePage = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
                        if(empty($tablePage->getPageRow($project_id , $page_id))){
                            $pagerow = $tablePage->createRow();
                            $pagerow->project_id = $project_id;
                            $pagerow->page_id = $page_id;
                            $pagerow->page_type = 'parent';
                            $pagerow->owner_id = $viewer_id;
                            $pagerow->save();
                        }

                        // update the initiative section in project tags
                        /*if (isset($initiative_id) && !empty($initiative_id)) {
                            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
                            if (isset($initiative['sections']) && !empty($initiative['sections'])) {
                                $sections = preg_split('/[,]+/', $initiative['sections']);
                                $sections = array_filter(array_map("trim", $sections));
                                if (count($sections) >0 ) {
                                    $projectModel->tags()->setTagMaps($viewer, $sections);
                                }
                            }
                        }*/

                        $db->commit();

                    }
                    // if project_id is there, then update it
                    else{

                        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

                        // get initiative_id
                        if (!empty($formValues['initiative_id'])) {
                            $this->view->initiative_id = $initiative_id = $formValues['initiative_id'];
                        } else {
                            $this->view->initiative_id = $initiative_id = null;
                        }

                        // get page_id
                        $this->view->page_id = $page_id = $formValues['page_id'];

                        // update initiative_id
                        $project->initiative_id = $initiative_id;
                        $project->save();

                        // update page_id
                        // insert into project's organisation
                        $tablePage = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
                        $tablePage->delete(array(
                            'project_id = ?' => $project_id
                        ));
                        $pagerow = $tablePage->createRow();
                        $pagerow->project_id = $project_id;
                        $pagerow->page_id = $page_id;
                        $pagerow->page_type = 'parent';
                        $pagerow->owner_id = $viewer_id;
                        $pagerow->save();

                        $db->commit();
                    }

                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }

                return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-one', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);
            }
        }



    }


    public function stepOneAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        if ($project_id){
            $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->level_id = $viewer->level_id;


        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepOne();
        $form->removeElement('execute');

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-zero',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        if($project_id && !empty($project)){
            $form->populate(
                array(
                    'is_fund_raisable'=> $project->is_fund_raisable,
                    'goal_amount' => $project->goal_amount,
                    'invested_amount' => $project->invested_amount
                )
            );
        }

        if (!$this->getRequest()->isPost()){
            return;
        }

        if($_POST['is_fund_raisable']==0) {
            $form->goal_amount->setRequired(false);
            $form->goal_amount->setAllowEmpty(true);
            $form->invested_amount->setRequired(false);
            $form->invested_amount->setAllowEmpty(true);
        }

        if(!$form->isValid($this->getRequest()->getPost())){
            return;
        }


        $values = $form->getValues();
        if (empty($values))
            return;

        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {

            if($project_id){

                if($values['is_fund_raisable']){
                    if($values['goal_amount'] <= 0){
                        $error = $this->view->translate('What are the total funds needed including the amount you are contributing? Please pick a number greater than 0');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }
                }

                // remove comma from amount fields to fix decimal issue
                $values['goal_amount'] = str_replace( ',', '', $values['goal_amount'] );
                $values['invested_amount'] = str_replace( ',', '', $values['invested_amount'] );

                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                $project->is_fund_raisable = $values['is_fund_raisable'];
                $project->goal_amount = $values['goal_amount'];
                $project->invested_amount = $values['invested_amount'];
                $project->save();
                $db->commit();
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-two', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);
    }

    public function stepOneDeleteAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        if ($project_id){
            $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->level_id = $viewer->level_id;


        //$this->view->project_id = $project_id;
        //MAKE FORM

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepOne();

        if($project_id && !empty($project)){
            $form->populate(
                array(
                    'is_fund_raisable'=> $project->is_fund_raisable,
                    'goal_amount' => $project->goal_amount,
                    'invested_amount' => $project->invested_amount
                )
            );
        }

        if (!$this->getRequest()->isPost()){
            return;
        }

        if($_POST['is_fund_raisable']==0) {
            $form->goal_amount->setRequired(false);
            $form->goal_amount->setAllowEmpty(true);
            $form->invested_amount->setRequired(false);
            $form->invested_amount->setAllowEmpty(true);
        }

        if(!$form->isValid($this->getRequest()->getPost())){
            return;
        }


        $values = $form->getValues();
        if (empty($values))
            return;

        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {

            if($project_id){

                if($values['is_fund_raisable']){
                    if($values['goal_amount'] <= 0){
                        $error = $this->view->translate('What are the total funds needed including the amount you are contributing? Please pick a number greater than 0');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }
                }

                // remove comma from amount fields to fix decimal issue
                $values['goal_amount'] = str_replace( ',', '', $values['goal_amount'] );
                $values['invested_amount'] = str_replace( ',', '', $values['invested_amount'] );

                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                $project->is_fund_raisable = $values['is_fund_raisable'];
                $project->goal_amount = $values['goal_amount'];
                $project->invested_amount = $values['invested_amount'];
                $project->save();
                $db->commit();
            }else {
                $values = array_merge($form->getValues(), array(
                    'owner_type' => $viewer->getType(),
                    'owner_id' => $viewer_id,
                    'parent_type' => $viewer->getType(),
                    'parent_id' => $viewer_id,
                    'featured' => 0,
                    'sponsored' => 0,
                    'approved' => 0,
                    'status' => 'initial',
                    'state' => 'draft'
                ));

                if($values['is_fund_raisable']){
                    if($values['goal_amount'] <= 0){
                        $error = $this->view->translate('What are the total funds needed including the amount you are contributing? Please pick a number greater than 0');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }
                }

                // remove comma from amount fields to fix decimal issue
                $values['goal_amount'] = str_replace( ',', '', $values['goal_amount'] );
                $values['invested_amount'] = str_replace( ',', '', $values['invested_amount'] );

                $projectModel = $table->createRow();
                $projectModel->setFromArray($values);
                $projectModel->start_date = null;
                $projectModel->expiration_date = null;
                $projectModel->funding_start_date = null;
                $projectModel->funding_end_date = null;
                $projectModel->save();

                if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            unset($values['networks_privacy']);
                        } else {
                            $values['networks_privacy'] = implode(',', $values['networks_privacy']);
                        }
                    }
                }

                //PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;

                $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $leaderList = $projectModel->getLeaderList();

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = "registered";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }
                    $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                    $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
                }
                $ownerList = '';
                $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                if (empty($values['auth_topic'])) {
                    $values['auth_topic'] = "registered";
                }
                if (isset($values['auth_post']) && empty($values['auth_post'])) {
                    $values['auth_post'] = "registered";
                }

                $topicMax = array_search($values['auth_topic'], $roles);
                $postMax = '';
                if (isset($values['auth_post']) && !empty($values['auth_post']))
                    $postMax = array_search($values['auth_post'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }
                    $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                    if (!is_null($postMax)) {
                        $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                    }
                }
                // Create some auth stuff for all leaders
                $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'delete', 1);

                $projectModel->category_id = 14;
                $projectModel->save();
                $project_id = $projectModel->getIdentity();

                Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->insert(array(
                    'project_id' => $project_id,
                    'overview' => ""
                ));

                $db->commit();
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-two', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);
    }

    public function stepTwoAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');

        //MAKE VALUE ARRAY
        $values = array();
        $value['id'] = $project->project_id;

        //GET LOCATION
        $this->view->location = $location = $locationTable->getLocation($value);

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepThree(array(
            'project_id' => $project_id,
            'location' => $location->location
        ));

        $locationData = array();
        if (!empty($location)) {
            $locationData['location'] = $location->location;

        }
        $overview = $tableOtherinfo->getColumnValue($project_id, 'overview');
        if(!empty($overview)){
            $locationData['overview'] = $overview;
        }

        // Populate records
        $populatedArray = $project->toArray();
        $form->populate(
            array_merge($populatedArray, $locationData)
        );


        if ($project->start_date !== null) {
            $form->populate(array(
                'starttime' => date('Y-m-d', strtotime($project->start_date)),
            ));
        }

        if ($project->expiration_date !== null) {
            $form->populate(array(
                'endtime' => date('Y-m-d', strtotime($project->expiration_date)),
            ));
        }

        // Save
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            // get form values
            $values = $form->getValues();
            if (empty($values))
                return;

//                // valid start time
//                if (!empty($values['starttime']) && empty($values['endtime'])) {
//                    $error = $this->view->translate('Please enter End Date - it is required.');
//                    $error = Zend_Registry::get('Zend_Translate')->_($error);
//                    $form->getDecorator('errors')->setOption('escape', false);
//                    $form->addError($error);
//                    return;
//                }

            // valid end time
//                if (empty($values['starttime'])) {
//                    $error = $this->view->translate('Please enter Start Date - it is required.');
//                    $error = Zend_Registry::get('Zend_Translate')->_($error);
//                    $form->getDecorator('errors')->setOption('escape', false);
//                    $form->addError($error);
//                    return;
//                }

//                if (!empty($values['starttime']) && !empty($values['endtime']) && $values['starttime'] > $values['endtime']) {
//                    $error = $this->view->translate('Please enter End Date greater than Start Date - it is required.');
//                    $error = Zend_Registry::get('Zend_Translate')->_($error);
//                    $form->getDecorator('errors')->setOption('escape', false);
//                    $form->addError($error);
//                    return;
//                }

            if (!empty($values['starttime'])) {
                $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                $startDate2 = date('Y-m-d', strtotime($startDate));
            }else{
                $startDate2 = date('Y-m-d H:i:s');
            }

            if (!empty($values['endtime'])) {
                $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
                $endDate2 = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));
            }else{
                $endDate2 = null;
            }

            $inputs = array(
                'title' => $values['title'],
                'description' => $values['description'],
                'funding_start_date' => date('Y-m-d H:i:s'),
                'funding_end_date' => date('Y-m-d H:i:s',strtotime('+364 days')),
                'start_date' => $startDate2,
                'expiration_date' => $endDate2,
                'desire_desc' => $values['desire_desc'],
                'help_desc' => $values['help_desc'],
                'location'=>$values['location']
            );
            $project->setFromArray($inputs);
            $project->save();

            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            // dont hardcode organisation, select organisation in step-0
            /*
            $tablePage = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
            if(empty($tablePage->getPageRow($project_id , 7))){
                $pagerow = $tablePage->createRow();
                $pagerow->project_id = $project_id;
                $pagerow->page_id = 7;
                $pagerow->page_type = 'parent';
                $pagerow->owner_id = $viewer_id;
                $pagerow->save();
            }
            */


            // Add viewer as member into table
            $memberShipTablePage = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
            if(!$memberShipTablePage->isMemberJoined($project_id)){
                $memberRow = $memberShipTablePage->createRow();
                $memberRow->resource_id = $project_id;
                $memberRow->project_id = $project_id;
                $memberRow->user_id = $viewer_id;
                $memberRow->resource_approved = 1;
                $memberRow->active = 1;
                $memberRow->user_approved = 1;
                $memberRow->save();
            }

            $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
            // update location
            if (isset($values['locationParams']) && $values['locationParams']) {
                if (is_string($values['locationParams'])) {
                    $locationParams = Zend_Json_Decoder::decode($values['locationParams']);
                    if(!empty($locationTable->getLocationRow($project_id))){
                        $locationTable->update($locationParams, array('project_id = ?' => $project_id));
                    }else{
                        $locationParams['project_id'] = $project_id;
                        $locationParams['zoom'] = 16;
                        $locationTable->insert($locationParams);
                    }
                }
            }
            if(!empty($tableOtherinfo->getOtherInfoRow($project_id))){
                $tableOtherinfo->update(array('overview' => $_POST['overview']), array('project_id = ?' => $project_id));
            }else{
                $tableOtherinfo->insert(array('overview' => $_POST['overview'], 'project_id' => $project_id));
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);

        }

    }

    public function stepThreeAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-two',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);
        $this->view->nextURL = $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        $this->view->externalorganizations =  $externalorganizations = Engine_Api::_()->getDbtable('organizations','sitecrowdfunding')->fetchOrganizationByProjectId($project_id);
        $this->view->internalorganizations =  $internalorganizations = Engine_Api::_()->getDbtable('pages','sitecrowdfunding')->getPagesbyProjectId($project_id);

        if(!empty($externalorganizations) || !empty($internalorganizations)){
            $this->view->show_notice = true;
        }else{
            $this->view->show_notice = false;
        }

        //MAKE FORM
        $this->view->form_dummy = $form_dummy = new Sitecrowdfunding_Form_Project_Create_StepFour(array(
            'project_id' => $project_id
        ));

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if($form_dummy->isValid($this->getRequest()->getPost())){

            $form_dummy_values = $form_dummy->getValues();

            if($form_dummy_values['is_associated_org'] == 1){

                if(empty($externalorganizations) && empty($internalorganizations)){
                    $error = $this->view->translate('Please add organization.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form_dummy->getDecorator('errors')->setOption('escape', false);
                    $form_dummy->addError($error);
                    return;
                }else{
                    return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);
                }

            }else{

                // dont hardcode organisation, select organisation in step-0
                $org_table = Engine_Api::_()->getDbTable('organizations', 'sitecrowdfunding');
                $org_table->delete(array('project_id = ?' => $project_id));

                $page_table = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
                $page_table->delete(array('project_id = ?' => $project_id, 'owner_id = ?' => $viewer_id));

                return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);

            }

        }else{
            return;
        }

    }

    public function stepFourAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        // Bind Forms
        $this->view->settingsForm = $settingsForm =new Sitecrowdfunding_Form_Project_Create_StepSix(array(
            'project_id' => $project_id
        ));

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-two',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        // set next page url
        // check if initiative is added or not, based on that go to step-5 or else go to step-6

        // get project-tags
        $projectTags = $project->tags()->getTagMaps();
        $tagString = array();
        foreach ($projectTags as $tagmap) {
            $tagString[] = $tagmap->getTag()->getTitle();
        }

        // get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if(empty($parentOrganization)){
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        // if initiative_id not present, then take check by project tags
        if(empty($project->initiative_id)) {

            // if both tags and page_id is not empty only, then get initiative_id
            if (!empty($parentOrganization['page_id']) && count($tagString) > 0) {

                // check if initiative_id is there, then go to step-5 else to step-6
                $initiatives = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'], $tagString);

                if (!empty($initiatives)) {
                    if (count($initiatives) > 0) {
                        if (!empty($initiatives[0]['initiative_id'])) {
                            // check if initiative question is there
                            $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsById($parentOrganization['page_id'], $initiatives[0]['initiative_id']);
                            if (count($initiativeQuestions) > 0) {
                                $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-five', 'project_id' => $project_id, 'initiative_id' => $initiatives[0]['initiative_id']), "sitecrowdfunding_createspecific", true);
                            } else {
                                $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                            }
                        } else {
                            $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                        }
                    } else {
                        $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                    }
                } else {
                    $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                }

            } else {
                $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
            }

        } else {

            // if present then take the questions,
            // if no questions, then go to step-6 else go to step-5
            $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsByInitiativeId($project->initiative_id);
            if (count($initiativeQuestions) > 0) {
                $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-five', 'project_id' => $project_id, 'initiative_id' => $project->initiative_id), "sitecrowdfunding_createspecific", true);
            } else {
                $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
            }

        }


        $this->view->nextURL = $nextURL;

        if(!empty($project->photo_id) || !empty($project->video_id)){
            $settingsForm->populate(
                array(
                    'profile_cover' => $tableOtherinfo->getColumnValue($project->getIdentity(), 'profile_cover')
                )
            );
        }

        if(!empty($project->getPhotoUrl())){
            $this->view->photoUrl = $project->getPhotoUrl();
        }

        $this->view->item = $item = Engine_Api::_()->getItem('sitevideo_video', $project->video_id);

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$settingsForm->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $settingsForm->getValues();
        if (empty($values))
            return;

        //saving the photo
        if ($values['profile_cover'] == 1 && empty($project->photo_id)) {
            $error = $this->view->translate('Please upload photo.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $settingsForm->getDecorator('errors')->setOption('escape', false);
            $settingsForm->addError($error);
            return;
        }

        if($values['profile_cover'] == 0 && empty($project->video_id) ){
            $error = $this->view->translate('Please upload video.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $settingsForm->getDecorator('errors')->setOption('escape', false);
            $settingsForm->addError($error);
            return;
        }

        //saving the settings
        $tableOtherinfo->update(array('profile_cover' => $values['profile_cover']), array('project_id = ?' => $project_id));

        return $this->_helper->redirector->gotoUrl($nextURL, array('prependBase' => false));

    }

    // list initiative questions
    public function stepFiveAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        /****** get project details ****/
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        /**** get initiative details ****/
        // get project-tags
        $projectTags = $project->tags()->getTagMaps();
        $tagString = array();
        foreach ($projectTags as $tagmap) {
            $tagString[] = $tagmap->getTag()->getTitle();
        }

        // get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        // if initiative_id not present, then take check by project tags
        if (empty($project->initiative_id)) {

            // if both tags and page_id is not empty only, then get initiative_id
            if (!empty($parentOrganization['page_id']) && count($tagString) > 0) {
                $page_id = $parentOrganization['page_id'];
                // check if initiative_id is there, then go to step-5 else to step-6
                $initiatives = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'], $tagString);
                if (!empty($initiatives)) {
                    if (count($initiatives) > 0) {
                        if (!empty($initiatives[0]['initiative_id'])) {
                            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiatives[0]['initiative_id']);
                            $initiative_id = $initiatives[0]['initiative_id'];
                        } else {
                            return $this->_forwardCustom('notfound', 'error', 'core');
                        }
                    } else {
                        return $this->_forwardCustom('notfound', 'error', 'core');
                    }
                } else {
                    return $this->_forwardCustom('notfound', 'error', 'core');
                }
            } else {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
        } else {
            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id);
            $initiative_id = $project->initiative_id;
        }


        $this->view->initiative = $initiative;

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
        $this->view->nextURL = $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);


        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepInitiativeQuestion(array(
            'initiative_id' => $initiative_id
        ));

        // answer table
        $initiativeAnswerTable = Engine_Api::_()->getDbtable('initiativeanswers', 'sitecrowdfunding');

        // get questions for initiative
        $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsByInitiativeId($initiative_id);

        // populate the values
        foreach ($initiativeQuestions as $key => $initiativeQuestion) {

            // get id
            $question_id = $initiativeQuestion['initiativequestion_id'];

            // get answer
            $initiativeAnswer = $initiativeAnswerTable->getInitiativeAnswerRow($project_id, $initiative_id, $question_id);

            // field names
            $idFieldName = 'id_' . $question_id;
            $titleFieldName = 'title_' . $question_id;
            $answerFieldName = 'answer_' . $question_id;
            $hintFieldName = 'hint_' . $question_id;
            $fieldTypeFieldName = 'fieldtype_' . $question_id;

            // check if id there, then based on it, populate value
            if( ($idFieldValue = $form->getElement($idFieldName)) && !$idFieldValue->getValue() ) {
                $idFieldValue->setValue($question_id);
            }

            // check if question there, then based on it, populate value
            if( ($titleFieldValue = $form->getElement($titleFieldName)) && !$titleFieldValue->getValue() ) {
                $titleFieldValue->setValue($initiativeAnswer->initiative_question);
            }

            // check if question there, then based on it, populate value
            if( ($answerFieldValue = $form->getElement($answerFieldName)) && !$answerFieldValue->getValue() ) {
                $answerFieldValue->setValue($initiativeAnswer->initiative_answer);
            }

            // check if question there, then based on it, populate value
            if( ($hintFieldValue = $form->getElement($hintFieldName)) && !$hintFieldValue->getValue() ) {
                $hintFieldValue->setValue($initiativeAnswer->initiative_question_hint);
            }

            // check if question there, then based on it, populate value
            if( ($fieldTypeFieldValue = $form->getElement($fieldTypeFieldName)) && !$fieldTypeFieldValue->getValue() ) {
                $fieldTypeFieldValue->setValue($initiativeAnswer->initiative_question_fieldtype);
            }

        }


        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            // get answers from questions array
            foreach ($initiativeQuestions as $key => $initiativeQuestion) {

                $question_id = $initiativeQuestion['initiativequestion_id'];
                $id = $question_id;
                $question = $values['title_' . $question_id];
                $answer = $values['answer_' . $question_id];
                $hint = $values['hint_' . $question_id];
                $questionFieldtype = $values['fieldtype_' . $question_id];

                // get answer
                $initiativeAnswer = $initiativeAnswerTable->getInitiativeAnswerRow($project_id, $initiative_id, $question_id);

                // check if answer is already added or not
                if (empty($initiativeAnswer)) {
                    $tablerow = $initiativeAnswerTable->createRow();
                    $tablerow->initiative_question = $question;
                    $tablerow->initiative_question_hint = $hint;
                    $tablerow->initiative_question_fieldtype = $questionFieldtype;
                    $tablerow->initiative_answer = $answer;
                    $tablerow->initiative_id = $initiative_id;
                    $tablerow->initiativequestion_id = $id;
                    $tablerow->project_id = $project_id;
                    $tablerow->user_id = $viewer_id;
                    $tablerow->save();
                }else{
                    $initiativeanswer = Engine_Api::_()->getItem('sitecrowdfunding_initiativeanswer', $initiativeAnswer->initiativeanswer_id);
                    $initiativeanswer->initiative_question = $question;
                    $initiativeanswer->initiative_question_hint = $hint;
                    $initiativeanswer->initiative_question_fieldtype = $questionFieldtype;
                    $initiativeanswer->initiative_answer = $answer;
                    $initiativeanswer->updated_date = new Zend_Db_Expr('NOW()');
                    $initiativeanswer->save();
                }


            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);

    }

    // This step has outcomes, now we need have initiative questions
    // How many jobs will this project create if any ?
    // Paying it Forward ?
    public function stepFiveDeleteAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        //$this->view->outcomes =  $outcomes = Engine_Api::_()->getDbtable('outcomes','sitecrowdfunding')->getAllOutcomesByProjectId($project_id);

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        $this->view->nextURL = $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        // Bind Forms
        $this->view->form = $form =new Sitecrowdfunding_Form_Project_Create_StepSeven(array(
            'project_id' => $project_id
        ));

        $form->populate(
            array(
                'no_of_jobs' =>$project->no_of_jobs,
                'help_desc' => $project->help_desc,
            )
        );

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        try{
            $project->no_of_jobs = $values['no_of_jobs'];
            $project->help_desc = $values['help_desc'];
            $project->save();
        }catch (Exception $e){

        }

        return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);

    }

    public function stepSixAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        // set back url
        // get project-tags
        $projectTags = $project->tags()->getTagMaps();
        $tagString = array();
        foreach ($projectTags as $tagmap) {
            $tagString[] = $tagmap->getTag()->getTitle();
        }

        // get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if(empty($parentOrganization)){
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        // get payment details
        if(!empty($parentOrganization['page_id'])){
            $projectPaymentTable = Engine_Api::_()->getDbtable('projectpayments', 'sitepage');

            // update paypal payments into project profile
            $projectPaypalPayment = $projectPaymentTable->getPaypalProjectPaymentRow($parentOrganization['page_id']);
            if (!empty($projectPaypalPayment) ) {
                if(
                    !empty($projectPaypalPayment['payment_email']) &&
                    !empty($projectPaypalPayment['payment_username']) &&
                    !empty($projectPaypalPayment['payment_password']) &&
                    !empty($projectPaypalPayment['payment_signature'])
                ){
                    $this->view->setPaypalPayment = true;

                    $this->view->payment_email = $projectPaypalPayment['payment_email'];
                    $this->view->payment_username = $projectPaypalPayment['payment_username'];
                    $this->view->payment_password = $projectPaypalPayment['payment_password'];
                    $this->view->payment_signature = $projectPaypalPayment['payment_signature'];

                }else{
                    $this->view->setPaypalPayment = false;
                }
            }else{
                $this->view->setPaypalPayment = false;
            }

            // update stripe payments into project profile
            $projectStripePayment = $projectPaymentTable->getStripeProjectPaymentRow($parentOrganization['page_id']);
            if (!empty($projectStripePayment) ) {
                if(
                    !empty($projectStripePayment['payment_secret_key']) &&
                    !empty($projectStripePayment['payment_publishable_key'])
                ){
                    $this->view->setStripePayment = true;

                    $this->view->payment_secret_key = $projectStripePayment['payment_secret_key'];
                    $this->view->payment_publishable_key = $projectStripePayment['payment_publishable_key'];

                }else{
                    $this->view->setStripePayment = false;
                }
            }else{
                $this->view->setStripePayment = false;
            }
        }

        // if initiative_id not present, then take check by project tags
        if(empty($project->initiative_id)) {

            // if both tags and page_id is not empty only, then get initiative_id
            if (!empty($parentOrganization['page_id']) && count($tagString) > 0) {

                // check if initiative_id is there, then go to step-5 else to step-6
                $initiatives = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'], $tagString);

                if (!empty($initiatives)) {
                    if (count($initiatives) > 0) {
                        if (!empty($initiatives[0]['initiative_id'])) {
                            // check if initiative question is there
                            $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsById($parentOrganization['page_id'], $initiatives[0]['initiative_id']);
                            if (count($initiativeQuestions) > 0) {
                                $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-five', 'project_id' => $project_id, 'initiative_id' => $initiatives[0]['initiative_id']), "sitecrowdfunding_createspecific", true);
                            } else {
                                $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                            }
                        } else {
                            $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                        }
                    } else {
                        $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                    }
                } else {
                    $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
                }
            } else {
                $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
            }

        }else{

            // if present then take the questions,
            // if no questions, then go to step-6 else go to step-5
            $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsByInitiativeId($project->initiative_id);
            if (count($initiativeQuestions) > 0) {
                $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-five', 'project_id' => $project_id, 'initiative_id' => $project->initiative_id), "sitecrowdfunding_createspecific", true);
            } else {
                $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four', 'project_id' => $project_id), "sitecrowdfunding_createspecific", true);
            }

        }

        $this->view->backURL = $backURL;

        if ($project->is_fund_raisable) {
            $project->funding_state = 'submitted';
            $project->funding_status = 'initial';
        }
        /*//enabling the funding
        $project->funding_approved = 1;
        if (empty($project->funding_approved_date))
            $project->funding_approved_date = date('Y-m-d H:i:s');
        $project->funding_state = 'published';
        $project->funding_status = 'active';

        if (empty($project->approved_date))
            $project->approved_date = date('Y-m-d H:i:s');
        $project->approved = 1;
        $project->state = 'published';
        $project->status = 'active';
    } else {
        if (empty($project->approved_date))
            $project->approved_date = date('Y-m-d H:i:s');
        */
        //$project->approved = 1;
        $project->state = 'submitted';
        $project->status = 'initial';
        //}

        //saving the organization privacy details values default to project
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);

        $project->notify_project_donate = $sitepage->notify_project_donate;
        $project->notify_project_comment = $sitepage->notify_project_comment ;
        $project->is_user_followed_after_comment_yn =  $sitepage->is_user_followed_after_comment_yn;
        $project->is_user_followed_after_donate_yn =  $sitepage->is_user_followed_after_donate_yn;

        $project->steps_completed = 1;

        $project->save();

    }

    public function stepFiveDeleteOldAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        // Bind Forms
        $this->view->settingsForm = $settingsForm =new Sitecrowdfunding_Form_Project_Create_StepFive(array(
            'project_id' => $project_id
        ));

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);
        $this->view->nextURL = $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        if(!empty($project->photo_id) || !empty($project->video_id)){
            $settingsForm->populate(
                array(
                    'profile_cover' => $tableOtherinfo->getColumnValue($project->getIdentity(), 'profile_cover')
                )
            );
        }

        if(!empty($project->getPhotoUrl())){
            $this->view->photoUrl = $project->getPhotoUrl();
        }

        $this->view->item = $item = Engine_Api::_()->getItem('sitevideo_video', $project->video_id);

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$settingsForm->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $settingsForm->getValues();
        if (empty($values))
            return;

        //saving the photo
        if ($values['profile_cover'] == 1 && empty($project->photo_id)) {
            $error = $this->view->translate('Please upload photo.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $settingsForm->getDecorator('errors')->setOption('escape', false);
            $settingsForm->addError($error);
            return;
        }

        if($values['profile_cover'] == 0 && empty($project->video_id) ){
            $error = $this->view->translate('Please upload video.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $settingsForm->getDecorator('errors')->setOption('escape', false);
            $settingsForm->addError($error);
            return;
        }

        //saving the settings
        $tableOtherinfo->update(array('profile_cover' => $values['profile_cover']), array('project_id = ?' => $project_id));

        return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-six', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);

    }

    public function handleThumbnail($type, $code = null)
    {
        switch ($type) {

            //youtube
            case "youtube":
                $thumbnail     = "";
                $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
                foreach ($thumbnailSize as $size) {
                    $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
                    $file_headers = @get_headers($thumbnailUrl);
                    if (isset($file_headers[0]) && strpos($file_headers[0], '404 Not Found') == false) {
                        $thumbnail = $thumbnailUrl;
                        break;
                    }
                }
                return $thumbnail;
            //vimeo
            case "vimeo":
                $thumbnail = "";
                $data      = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                if (isset($data->video->thumbnail_large)) {
                    $thumbnail = $data->video->thumbnail_large;
                } else if (isset($data->video->thumbnail_medium)) {
                    $thumbnail = $data->video->thumbnail_medium;
                } else if (isset($data->video->thumbnail_small)) {
                    $thumbnail = $data->video->thumbnail_small;
                }

                return $thumbnail;
            //dailymotion
            case "dailymotion":
                $thumbnail      = "";
                $thumbnailUrl   = 'https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_small_url,thumbnail_large_url,thumbnail_medium_url';
                $json_thumbnail = file_get_contents($thumbnailUrl);
                if ($json_thumbnail) {
                    $thumbnails = json_decode($json_thumbnail);
                    if (isset($thumbnails->thumbnail_large_url)) {
                        $thumbnail = $thumbnails->thumbnail_large_url;
                    } else if (isset($thumbnails->thumbnail_medium_url)) {
                        $thumbnail = $thumbnails->thumbnail_medium_url;
                    } else if (isset($thumbnails->thumbnail_small_url)) {
                        $thumbnail = $thumbnails->thumbnail_small_url;
                    }
                }
                return $thumbnail;
            case "instagram":
                $thumbnail = "";
                $path      = "https://api.instagram.com/oembed/?url=" . $code;
                $data      = @file_get_contents($path);
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $instagramData = Zend_Json::decode($data);
                    $thumbnail     = $instagramData['thumbnail_url'];
                }
                return $thumbnail;
        }
    }

    public function stepSixDeleteAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        $this->view->statusLabels = array("yettostart" => "Yet to start", "inprogress" => "In Progress", 'completed'=> 'Completed');

        $this->view->milestones =  $milestones = Engine_Api::_()->getDbtable('milestones','sitecrowdfunding')->getAllMilestonesByProjectId($project_id);

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-five',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

        if($project->is_fund_raisable){
            $action = 'step-seven';
        }else{
            $action = 'step-eight';
        }
        $this->view->nextURL = $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => $action,  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);

    }

    public function stepSevenDeletedAction()
    {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        } else {
            $this->view->project = $project = Engine_Api::_()->core()->getSubject();
            $this->view->project_id = $project_id = $project->project_id;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            return;
        }
        $this->view->paypalForm = $paypalForm = new Sitecrowdfunding_Form_PayPal();

        $paypalForm->removeDecorator('title');

        //$this->view->paypalEnable = true;
        $this->view->stripeConnected = 0;
        $this->view->paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.paymentmethod', 'paypal');
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'split') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
        } elseif ($paymentMethod == 'escrow') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
        } else {
            if (empty($paymentToSiteadmin)) {
                $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
            }
        }
        $projectEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'project_gateway');
        if (!empty($projectEnabledgateway)) {
            $projectEnabledgateway = Zend_Json_Decoder::decode($projectEnabledgateway);
        }
        $getEnabledGateways = array();
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('plugin' => array('Sitegateway_Plugin_Gateway_Stripe', 'Sitegateway_Plugin_Gateway_MangoPay')));
        }
        foreach ($getEnabledGateways as $getEnabledGateway) {
            $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
            $gatewayKey = strtolower($gatewyPlugin[1]);
            $gatewayKeyUC = ucfirst($gatewyPlugin[1]);
            if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                if (isset($_SESSION['redirect_stripe_connect_oauth_process'])) {
                    $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
                    $session->unsetAll();
                }
                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                if (!empty($projectGatewayObj) && !empty($projectGatewayObj->projectgateway_id)) {
                    if (is_array($projectGatewayObj->config) && !empty($projectGatewayObj->config['stripe_user_id'])) {
                        $this->view->stripeConnected = 1;
                        $this->view->stripeEnabled = true;
                    }
                }
            } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                $this->view->mangopayForm = $mangopayForm = new Sitecrowdfunding_Form_MangoPay();
                $this->view->mangopayBankDetailForm = $mangopayBankDetailForm = new Sitecrowdfunding_Form_MangoPayBankDetail();

                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_MangoPay\''));
                if (!empty($projectGatewayObj)) {
                    // Populate form
                    $mangopayForm->populate($projectGatewayObj->toArray());
                    $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
                    $mode = 'live';
                    if ($adminAPGateway->config['test_mode']) {
                        $mode = 'sandbox';
                    }
                    $config = isset($projectGatewayObj->config[$mode]) ? ($projectGatewayObj->config[$mode]) : null;
                    if (is_array($config)) {
                        $birthday = $projectGatewayObj->config[$mode]['birthday'];
                        $config['birthday'] = date('Y-m-d', $birthday);
                        $mangopayForm->populate($config);
                        $mangopayBankDetailForm->populate($config);
                    }
                    if ($projectGatewayObj->enabled == 1) {
                        $this->view->mangopayEnable = true;
                    }
                } else {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                    $select = $searchTable->select();
                    $select->where('item_id = ?', $viewer->getIdentity());
                    $otherUserRecords = $searchTable->fetchRow($select);
                    if ($otherUserRecords) {
                        $formData['first_name'] = $otherUserRecords->first_name;
                        $formData['last_name'] = $otherUserRecords->last_name;
                        $formData['birthday'] = $otherUserRecords->birthdate;
                    }
                    $formData['email'] = $viewer->email;
                    $mangopayForm->populate($formData);
                }
            } else {
                $formName = "form$gatewayKeyUC";
                $formClass = "Sitegateway_Form_Order_$gatewayKeyUC";
                $this->view->$formName = $gatewayForm = new $formClass();
                $gatewayForm->setName("sitecrowdfunding_payment_info_$gatewayKey");
                if ((!empty($projectEnabledgateway[$gatewayKey]) || !empty($paymentToSiteadmin))) {
                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin = ?' => $getEnabledGateway->plugin));
                    if (!empty($projectGatewayObj)) {
                        $gateway_id = $projectGatewayObj->projectgateway_id;
                        if (!empty($gateway_id)) {
                            $gatewyEnabled = $gatewayKey . 'Enabled';
                            $this->view->$gatewyEnabled = true;
                            $gatewayForm->populate($projectGatewayObj->toArray());
                            if (is_array($projectGatewayObj->config)) {
                                $gatewayForm->populate($projectGatewayObj->config);
                            }
                        }
                    }
                }
            }
        }

        if (!empty($projectEnabledgateway['paypal']) || !empty($paymentToSiteadmin)) {
            $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
            if (!empty($projectGatewayObj)) {
                $gateway_id = $projectGatewayObj->projectgateway_id;
                $this->view->paypalEnable = true;
                $paypalForm->populate($projectGatewayObj->toArray());
                if (is_array($projectGatewayObj->config)) {
                    $paypalForm->populate($projectGatewayObj->config);
                }
            }
        }

        // Show form by default
        $this->view->paypalEnable = true;
    }

    public function stepEightDeletedAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        /**
         * privacy form
         **/
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepEight(array(
            'project_id' => $project_id
        ));

        $form->setTitle('Privacy Settings');
        $form->removeDecorator('description');
        $form->removeElement('cancel');
        $form->execute->setLabel('Next');

        $form->auth_view->setLabel($form->auth_view->getDescription());
        $form->auth_view->setDescription('');

        $form->auth_comment->setLabel($form->auth_comment->getDescription());
        $form->auth_comment->setDescription('');

        $leaderList = $project->getLeaderList();

        /**
         * Set Admin list in UI
         **/
        $this->view->list = $list = $project->getLeaderList();
        $list_id = $list['list_id'];
        $auth = Engine_Api::_()->authorization()->context;

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

        $userSelect = $userTable->select()
            ->from($userTableName)
            ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
            ->order('displayname ASC');

        $this->view->members = $userTable->fetchAll($userSelect);

        /***
         * Set Privacy in UI
         ***/
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $projectTags = $project->tags()->getTagMaps();
            $tagString = '';

            foreach ($projectTags as $tagmap) {
                $temp = $tagmap->getTag();
                if (!empty($temp)) {
                    if ($tagString != '')
                        $tagString .= ', ';
                    $tagString .= $tagmap->getTag()->getTitle();
                }
            }

            $this->view->tagNamePrepared = $tagString;
            if (isset($form->tags))
                $form->tags->setValue($tagString);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($project, $role, "view")) {
                        $form->auth_view->setValue($roleString);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($project, $role, "comment")) {
                        $form->auth_comment->setValue($roleString);
                    }
                }
            }
            $ownerList = '';
            $roles_photo = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

            foreach ($roles_photo as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $sitecrowdfundingAllow = Engine_Api::_()->getApi('allow', 'sitecrowdfunding');
                if ($form->auth_topic && 1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'topic')) {
                    $form->auth_topic->setValue($roleString);
                }

                if (isset($form->auth_post) && $form->auth_post && 1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'post')) {
                    $form->auth_post->setValue($roleString);
                }
            }
            if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                if (empty($project->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                } else {
                    $form->networks_privacy->setValue(explode(",", $project->networks_privacy));
                }
            }
        }


        /****
         * SAVE
         ***/
        if ($this->getRequest()->isPost()) {

            /***
             * SAVE PRIVACY FORM
             **/
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                if (empty($values))
                    return;

                $projectModel = $project;

                //PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;

                $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $leaderList = $projectModel->getLeaderList();

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = "registered";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }

                    $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                    $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
                }
                $ownerList = '';
                $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                if (empty($values['auth_topic'])) {
                    $values['auth_topic'] = "registered";
                }
                if (isset($values['auth_post']) && empty($values['auth_post'])) {
                    $values['auth_post'] = "registered";
                }

                $topicMax = array_search($values['auth_topic'], $roles);
                $postMax = '';
                if (isset($values['auth_post']) && !empty($values['auth_post']))
                    $postMax = array_search($values['auth_post'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }
                    $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                    if (!is_null($postMax)) {
                        $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                    }
                }
                // Create some auth stuff for all leaders
                $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'delete', 1);
                //UPDATE KEYWORDS IN SEARCH TABLE
                if (!empty($keywords)) {
                    Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'sitecrowdfunding_project', 'id = ?' => $projectModel->project_id));
                }
                if (!empty($project_id)) {
                    $projectModel->setLocation();
                }
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($projectModel) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-nine', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);

        }

    }

    public function stepNineDeletedAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $goals_ids = Engine_Api::_()->getDbTable('sdggoals', 'sitecrowdfunding')->getSDGGoals();

        $targets = Engine_Api::_()->getDbTable('sdgtargets', 'sitecrowdfunding')->getSDGTargetsWithActualIDS();

        $this->view->goals_ids = $goals_ids;
        $this->view->target_ids = $targets;

        $this->view->goals = $goals = Engine_Api::_()->getDbtable('goals', 'sitecrowdfunding')->getAllGoalsByProjectId($project_id);

    }

    public function stepTenDeletedAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        if ($project->is_fund_raisable) {
            //enabling the funding
            $project->funding_approved = 1;
            if (empty($project->funding_approved_date))
                $project->funding_approved_date = date('Y-m-d H:i:s');
            $project->funding_state = 'published';
            $project->funding_status = 'active';

            if (empty($project->approved_date))
                $project->approved_date = date('Y-m-d H:i:s');
            $project->approved = 1;
            $project->state = 'published';
            $project->status = 'active';
        } else {
            if (empty($project->approved_date))
                $project->approved_date = date('Y-m-d H:i:s');
            $project->approved = 1;
            $project->state = 'published';
            $project->status = 'active';
        }

        $project->save();

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);
        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomPendingInvites($project_id);
    }

    public function stepTwoDeletedAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepTwo(array(
            'project_id' => $project_id
        ));


        if($project && !empty($project->reason)){
            $string = $project->reason;
            $str_arr = preg_split ("/\,/", $string);
            $form->populate(array(
                'reason' => $str_arr
            ));
        }

        // Save
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // get form values
            $values = $form->getValues();
            if (empty($values))
                return;

            try{
                $reason = $values['reason'];
                if(!empty($reason) && count($reason)){
                    $project->reason =implode (",", $reason);
                    $project->save();
                }
            }catch (Exception $e){

            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-three', 'project_id' => $project_id), 'sitecrowdfunding_createspecific', true);
        }
    }

    public function stepFiveOldAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $this->view->backURL = $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-four',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);
        $this->view->nextURL = $nextURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-six',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);


        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);
        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomPendingInvites($project_id);
    }

    public function stepEightDeleted2Action(){
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        } else {
            $this->view->project = $project = Engine_Api::_()->core()->getSubject();
            $this->view->project_id = $project_id = $project->project_id;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            return;
        }
        $this->view->paypalForm = $paypalForm = new Sitecrowdfunding_Form_PayPal();

        $paypalForm->removeDecorator('title');

        //$this->view->paypalEnable = true;
        $this->view->stripeConnected = 0;
        $this->view->paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.paymentmethod', 'paypal');
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'split') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
        } elseif ($paymentMethod == 'escrow') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
        } else {
            if (empty($paymentToSiteadmin)) {
                $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
            }
        }
        $projectEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'project_gateway');
        if (!empty($projectEnabledgateway)) {
            $projectEnabledgateway = Zend_Json_Decoder::decode($projectEnabledgateway);
        }
        $getEnabledGateways = array();
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('plugin' => array('Sitegateway_Plugin_Gateway_Stripe', 'Sitegateway_Plugin_Gateway_MangoPay')));
        }
        foreach ($getEnabledGateways as $getEnabledGateway) {
            $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
            $gatewayKey = strtolower($gatewyPlugin[1]);
            $gatewayKeyUC = ucfirst($gatewyPlugin[1]);
            if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                if (isset($_SESSION['redirect_stripe_connect_oauth_process'])) {
                    $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
                    $session->unsetAll();
                }
                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                if (!empty($projectGatewayObj) && !empty($projectGatewayObj->projectgateway_id)) {
                    if (is_array($projectGatewayObj->config) && !empty($projectGatewayObj->config['stripe_user_id'])) {
                        $this->view->stripeConnected = 1;
                        $this->view->stripeEnabled = true;
                    }
                }
            } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                $this->view->mangopayForm = $mangopayForm = new Sitecrowdfunding_Form_MangoPay();
                $this->view->mangopayBankDetailForm = $mangopayBankDetailForm = new Sitecrowdfunding_Form_MangoPayBankDetail();

                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_MangoPay\''));
                if (!empty($projectGatewayObj)) {
                    // Populate form
                    $mangopayForm->populate($projectGatewayObj->toArray());
                    $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
                    $mode = 'live';
                    if ($adminAPGateway->config['test_mode']) {
                        $mode = 'sandbox';
                    }
                    $config = isset($projectGatewayObj->config[$mode]) ? ($projectGatewayObj->config[$mode]) : null;
                    if (is_array($config)) {
                        $birthday = $projectGatewayObj->config[$mode]['birthday'];
                        $config['birthday'] = date('Y-m-d', $birthday);
                        $mangopayForm->populate($config);
                        $mangopayBankDetailForm->populate($config);
                    }
                    if ($projectGatewayObj->enabled == 1) {
                        $this->view->mangopayEnable = true;
                    }
                } else {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                    $select = $searchTable->select();
                    $select->where('item_id = ?', $viewer->getIdentity());
                    $otherUserRecords = $searchTable->fetchRow($select);
                    if ($otherUserRecords) {
                        $formData['first_name'] = $otherUserRecords->first_name;
                        $formData['last_name'] = $otherUserRecords->last_name;
                        $formData['birthday'] = $otherUserRecords->birthdate;
                    }
                    $formData['email'] = $viewer->email;
                    $mangopayForm->populate($formData);
                }
            } else {
                $formName = "form$gatewayKeyUC";
                $formClass = "Sitegateway_Form_Order_$gatewayKeyUC";
                $this->view->$formName = $gatewayForm = new $formClass();
                $gatewayForm->setName("sitecrowdfunding_payment_info_$gatewayKey");
                if ((!empty($projectEnabledgateway[$gatewayKey]) || !empty($paymentToSiteadmin))) {
                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin = ?' => $getEnabledGateway->plugin));
                    if (!empty($projectGatewayObj)) {
                        $gateway_id = $projectGatewayObj->projectgateway_id;
                        if (!empty($gateway_id)) {
                            $gatewyEnabled = $gatewayKey . 'Enabled';
                            $this->view->$gatewyEnabled = true;
                            $gatewayForm->populate($projectGatewayObj->toArray());
                            if (is_array($projectGatewayObj->config)) {
                                $gatewayForm->populate($projectGatewayObj->config);
                            }
                        }
                    }
                }
            }
        }

        if (!empty($projectEnabledgateway['paypal']) || !empty($paymentToSiteadmin)) {
            $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
            if (!empty($projectGatewayObj)) {
                $gateway_id = $projectGatewayObj->projectgateway_id;
                $this->view->paypalEnable = true;
                $paypalForm->populate($projectGatewayObj->toArray());
                if (is_array($projectGatewayObj->config)) {
                    $paypalForm->populate($projectGatewayObj->config);
                }
            }
        }

        // Show form by default
        $this->view->paypalEnable = true;

    }

    public function saveCroppedImageAction(){
        if (empty($_POST) || !isset($_POST['project_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }

        $project_id = $values['project_id'];
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $coordinatesInput = $values['coordinates'];
        $photo_id = $values['photo_id'];

        $storage = Engine_Api::_()->storage();
        $iMain = $storage->get($photo_id, 'thumb.main');
        $iCover = $storage->get($photo_id, 'thumb.cover');

        if(empty($iCover)){
            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            $params = array(
                'parent_type' => 'sitecrowdfunding_project',
                'parent_id' => $project_id,
                'user_id' => $project->owner_id,
                'name' => $iMain->name,
            );

            $iCover = $filesTable->createFile($iMain->storage_path, $params);
            $iMain->bridge($iCover, 'thumb.cover');
        }

        $pName = $iMain->getStorageService()->temporary($iMain);
        $iName = dirname($pName) . '/nis_' . basename($pName);
        list($x, $y, $w, $h) = explode(':', $coordinatesInput);
        $image = Engine_Image::factory();
        $image->open($pName)
            ->resample($x + .1, $y + .1, $w - .1, $h - .1, $w, $h)
            ->write($iName)
            ->destroy();
        $iCover->store($iName);
        @unlink($iName);


        return true;
    }

    public function approveProjectAction(){


        if (empty($_POST) || !isset($_POST['project_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }

        $project_id = $values['project_id'];

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $owner = $project->getOwner();
            $sender = Engine_Api::_()->user()->getViewer();
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            if($project->approved){
                $project->approved = 0;
                $project->approved_date = null;
                $project->state = 'rejected';
                $project->status = 'initial';
            }else{
                $project->approved = 1;
                if (empty($project->approved_date))
                    $project->approved_date = date('Y-m-d H:i:s');
                $project->state = 'published';
                $project->status = 'active';
            }

            $project->save();

            if($project->approved){

                if($settings->getSetting('sitecrowdfunding.reminder.project.approval', 0)) {
                    // if normal project approved means no need to do anything
                    Engine_Api::_()->sitecrowdfunding()->sendMailCustom('APPROVED', $project_id);
                }
                if($settings->getSetting('sitecrowdfunding.notification.project.approval', 0)) {
                    //SEND NOTIFICATION TO PROJECT OWNER
                    $type = 'sitecrowdfunding_project_approved';
                    $notifyApi->addNotification($owner, $sender, $project, $type);
                }

                if($settings->getSetting('sitecrowdfunding.activity.project.approval', 0)) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                    }
                }

            }else{

                if($settings->getSetting('sitecrowdfunding.reminder.project.disapproval', 0)) {
                    Engine_Api::_()->sitecrowdfunding()->sendMailCustom('DISAPPROVED', $project_id);
                }
                if($settings->getSetting('sitecrowdfunding.notification.project.disapproval', 0)) {
                    //SEND NOTIFICATION TO PROJECT OWNER
                    $type = 'sitecrowdfunding_project_disapproved';
                    $notifyApi->addNotification($owner, $sender, $project, $type);
                }

            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }

    public function approveFundingAction(){

        if (empty($_POST) || !isset($_POST['project_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }

        $project_id = $values['project_id'];

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            $owner = $project->getOwner();
            $sender = Engine_Api::_()->user()->getViewer();
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            if ($project->funding_approved) {
                $project->funding_approved = 0;
                $project->funding_approved_date = null;
                $project->funding_state = 'rejected';
                $project->funding_status = 'initial';
               // $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $db->update('engine4_sitecrowdfunding_project_gateways', array(
                    'enabled' => 0,
                ), array(
                    'project_id = ?' => $project_id,
                ));
            } else {
                $project->funding_approved = 1;
                if (empty($project->funding_approved_date))
                    $project->funding_approved_date = date('Y-m-d H:i:s');
                $project->funding_state = 'published';
                $project->funding_status = 'active';
                //$projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $db->update('engine4_sitecrowdfunding_project_gateways', array(
                    'enabled' => 1,
                ), array(
                    'project_id = ?' => $project_id,
                ));
            }

            $project->save();
            $db->commit();

            if($project->funding_approved){
                if($settings->getSetting('sitecrowdfunding.activity.project.funding.approval', 0)) {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_funding');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                    }
                }
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
                if($settings->getSetting('sitecrowdfunding.reminder.project.funding.disapproval', 0)) {
                    Engine_Api::_()->sitecrowdfunding()->sendMailCustom('FUNDING_DISAPPROVED', $project_id);
                    //if funding rejected means no need to reject normal project
                }
                if($settings->getSetting('sitecrowdfunding.notification.project.funding.disapproval', 0)) {
                    //SEND NOTIFICATION TO PROJECT OWNER
                    $type = 'sitecrowdfunding_project_funding_disapproved';
                    $notifyApi->addNotification($owner, $sender, $project, $type);
                }
            }

        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }


        return true;
    }

    public function updateOrderAction(){

        if (empty($_POST) || !isset($_POST['project_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }
        $project_id = $values['project_id'];

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
            if($_POST['project_order']) {
                $project->project_order =  $_POST['project_order'];
            }
            $project->save();
            $db->commit();



        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }


        return true;
    }

    public function scrambleProjectOrderAction(){

        if (empty($_POST) || !isset($_POST['page_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }

        $page_id = $values['page_id'];

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            // get project_ids
            $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

            // get projects total count
            $countProjectIds = count($projectsIds);

            // order array
            $orderArray = range(1, $countProjectIds);

            // shuffle the order array
            shuffle($orderArray);

            foreach ($projectsIds as $key => $value) {

                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $value);
                $project->project_order = $orderArray[$key];
                $project->save();

            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }

}
