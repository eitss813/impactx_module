<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_DashboardController extends Core_Controller_Action_Standard {

    public function init() {

        //SET THE SUBJECT
        if (0 !== ($project_id = (int) $this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
        }
    }

    //ACTION FOR CHANING THE PHOTO
    public function changePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //Check subject 
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET PROJECT ID
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //SELECTED TAB
        $this->view->TabActive = "profilepicture";
        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }
        //GET FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_ChangePhoto();
        $form->removeDecorator('title');
        $form->removeDecorator('description');
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
            $db = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {
                //SET PHOTO
                $project->setPhoto($form->Filedata);
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $currentDate = date('Y-m-d H:i:s');
            if ($project->state == 'published' && $project->approved && $project->start_date <= $currentDate) {
                $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $project, Engine_Api::_()->sitecrowdfunding()->getActivtyFeedType($project, 'sitecrowdfunding_change_photo'));
                $file_id = Engine_Api::_()->getDbtable('photos', 'sitecrowdfunding')->getPhotoId($project_id, $project->photo_id);
                $photo = Engine_Api::_()->getItem('sitecrowdfunding_photo', $file_id);
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                }
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();
            $iProfile = $storage->get($project->photo_id, 'thumb.profile');
            $iSquare = $storage->get($project->photo_id, 'thumb.icon');
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

        if (!empty($project->photo_id)) {
            $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
            $order = $photoTable->select()
                ->from($photoTable->info('name'), array('order'))
                ->where('project_id = ?', $project->project_id)
                ->group('photo_id')
                ->order('order ASC')
                ->limit(1)
                ->query()
                ->fetchColumn();

            $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $project->photo_id));
        }
        $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    }

    //ACTION FOR REMOVE THE PHOTO
    public function removePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        //Check subject 
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        //GET PROJECT ID
        $project_id = $this->_getParam('project_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        //GET FILE ID
        $file_id = Engine_Api::_()->getDbtable('photos', 'sitecrowdfunding')->getPhotoId($project_id, $project->photo_id);

        //DELETE PHOTO
        if (!empty($file_id)) {
            $photo = Engine_Api::_()->getItem('sitecrowdfunding_photo', $file_id);
            $photo->delete();
        }

        //SET PHOTO ID TO ZERO
        $project->photo_id = 0;
        $project->save();

        return $this->_helper->redirector->gotoRoute(array('action' => 'project-settings', 'project_id' => $project_id), 'sitecrowdfunding_dashboard', true);
    }

    //ACTION FOR META DETAIL INFORMATION
    public function metaDetailAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PROJECT ID AND OBJECT
        $project_id = $this->_getParam('project_id');

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy) && empty($editPrivacyOrganization) ) {
                return false;
            }
        }

        //Check subject
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.metakeyword', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$project->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "metakeyword")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "metadetails";

        //SET FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Metainfo();
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');

        //POPULATE FORM
        $value['keywords'] = $tableOtherinfo->getColumnValue($project_id, 'keywords');
        $form->populate($value);
        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            $tableOtherinfo->update(array('keywords' => $values['keywords']), array('project_id = ?' => $project_id));
            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
    }

    //ACTION TO SET OVERVIEW
    public function overviewAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //Check subject
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        //GET PROJECT ID AND OBJECT
        $project_id = $this->_getParam('project_id');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $allowOverview = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "overview") ? 1 : 0;
        } else {
            $allowOverview = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "overview");
        }
        if (empty($allowOverview)) {
            return $this->_forward('requireauth', 'error', 'core');
        }



        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (!$project->authorization()->isAllowed($viewer, 'edit')) {
                return $this->_forward('requireauth', 'error', 'core');
            }

        }

        //SELECTED TAB
        $this->view->TabActive = "overview";

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Overview();
        $form->removeDecorator('title');
        // $form->removeDecorator('description');
        //IF NOT POSTED
        if (!$this->getRequest()->isPost()) {
            $saved = $this->_getParam('saved');
            if (!empty($saved))
                $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your project has been successfully created. You can enhance your project from this Dashboard by creating other components.');
        }
        $project_id = $project->getIdentity();
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        //SAVE THE VALUE
        if ($this->getRequest()->isPost()) {
            $tableOtherinfo->update(array('overview' => $_POST['overview']), array('project_id = ?' => $project_id));
            $project->title = $_POST['title'];
            $project->description = $_POST['description'];

            $project->help_desc = $_POST['help_desc'];
            $project->desire_desc = $_POST['desire_desc'];
            $project->location = $_POST['location'];

            $db = Engine_Db_Table::getDefaultAdapter();

//            $db->update('engine4_sitecrowdfunding_locations', array(
//                'location' => 'ger',
//            ), array(
//                'project_id = ?' => 283
//            ));


            $fieldType = $db->select()->from('engine4_sitecrowdfunding_locations', '*')->where('project_id = ?', $project_id)->query()->fetchColumn();

            $locationParams =  json_decode($_POST['locationParams']);


            if($_POST['location']){
                $data = array('location' => $_POST['location']);

                $where = array(
                    'project_id = ?' => $project_id
                );

                $db->update('engine4_sitecrowdfunding_locations', $data, $where);
            }

            if($locationParams) {

                // tab on profile
                $db->insert('engine4_sitecrowdfunding_locations', array(
                    'project_id'  => $project_id,
                    'location'    => $_POST['location'],
                    'latitude'    => $locationParams->latitude,
                    'longitude'   => $locationParams->longitude,
                    'formatted_address'   => $locationParams->formatted_address,
                    'country'     => $locationParams->country,
                    'state'       => $locationParams->state,
                    'zipcode'     => $locationParams->zipcode,
                    'city'        => $locationParams->city,
                    'address'     => $locationParams->address
                ));
            }





            $project->save();
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        //POPULATE FORM
        $values['overview'] = $tableOtherinfo->getColumnValue($project_id, 'overview');
        $values['title'] = $project->getTitle();
        $values['description'] = $project->getDescription();

        $values['help_desc'] = $project->help_desc;
        $values['desire_desc'] = $project->desire_desc;
        $values['location'] = $project->location;

        $form->populate($values);
    }








    //ACTION TO SET OVERVIEW
    public function additionalAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $project_id = $this->_getParam('project_id');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->form = $form = new Sitecrowdfunding_Form_Additional();

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $res = $select
            ->from('engine4_sitecrowdfunding_projects_additionalsection', '*')
            ->where('project_id = ?', $project_id)
            ->query()->fetchAll();

        if (!$this->getRequest()->isPost()) {
            if ($res && $res[0]) {
                $form->populate($res[0]);
            }
            return;
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        $db = Engine_Db_Table::getDefaultAdapter();
        if (count($res) == 0) {
            $title = $values['title'];
            $description = $values['description'];

            $db->insert('engine4_sitecrowdfunding_projects_additionalsection', array(
                'project_id' => $project_id,
                'title' => $values['title'],
                'description' => $values['description']
            ));

        }
        else {
            $title = $values['title'];
            $description = $values['description'];

            $tableInfo = Engine_Api::_()->getDbtable('projectsAdditionalsection', 'sitecrowdfunding');
            $tableInfo->update(array(
                'description' => $values['description'],
                'title' => $values['title']
            ), array(
                'project_id = ?' => $project_id,
            ));

        }
        $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));

        echo '<script type="text/javascript">
                  window.location.reload();
              </script>';

    }



    /*
     * This action is used to take information of Project owner.
     */

    public function aboutYouAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->form = $form = new Sitecrowdfunding_Form_AboutYou();
        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $user_id = $project->owner_id;
        $select = $tableUserInfo->select()->where('user_id = ?', $user_id);
        $user_info = $tableUserInfo->fetchRow($select);
        if (!$this->getRequest()->isPost()) {
            if ($user_info) {
                $form->populate($user_info->toarray());
            }
            return;
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($user_info)) {
            $user_info = $tableUserInfo->createRow();
            $user_info->user_id = $user_id;
            $user_info->save();
        }
        $user_info->setFromArray($values);
        $user_info->save();
        $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    }

    /*
     * Upload video
     */

    public function uploadVideoAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }
        //SELECTED TAB
        $this->view->TabActive = "video";
        $this->view->videos = $videos = array();
        $this->view->integratedWithVideo = false;
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');

        // RETURN IF SITEVIDEO IS NOT INSTALLED
        if (empty($sitevideoEnabled))
            return $this->_forward('requireauth', 'error', 'core');

        $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitecrowdfunding_project", 'item_module' => 'sitecrowdfunding'));

        // RETURN IF SITEVIDEO IS NOT INTEGRATED
        if (empty($isIntegrated))
            return $this->_forward('requireauth', 'error', 'core');

        if ($sitevideoEnabled && $isIntegrated) {
            $params = array();
            $params['parent_type'] = $project->getType();
            $params['parent_id'] = $project->getIdentity();
            $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
            $this->view->integratedWithVideo = true;
        } else {
            if (Engine_Api::_()->sitecrowdfunding()->enableVideoPlugin()) {
                $this->view->videos = $videos = Engine_Api::_()->sitecrowdfunding()->getProjectVideos($project);
            }
        }
        //PACKAGE BASED CHECKS - AUTHORIZATION CHECK
        $allowed_upload_video = Engine_Api::_()->sitecrowdfunding()->allowVideo($project, $viewer, count($videos));
        $this->view->upload_video = 1;
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $this->view->upload_video = $allowed_upload_video;
        } else {
            if (empty($allowed_upload_video)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }
        $this->view->count = count($videos);

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Video_Editvideo();
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        foreach ($videos as $video) {

            $subform = new Sitecrowdfunding_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));

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

                $subform->addElement('dummy', 'mssg' . $video->video_id, array(
                    'description' => $msg,
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'tip')),
                        array('Description', array('tag' => 'span', 'placement' => 'APPEND')),
                        array('Description', array('placement' => 'APPEND')),
                    ),
                ));
                $t = 'mssg' . $video->video_id;
                $subform->$t->getDecorator("Description")->setOption("placement", "append");
            }
            $subform->populate($video->toArray());
            $form->addSubForm($subform, $video->getGuid());
            $form->cover->addMultiOption($video->video_id, $video->video_id);
        }

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();


        if (!empty($values['cover']) && $project->video_id != $values['cover']) {
            $project->video_id = $values['cover'];
            $project->save();
        }
        //VIDEO SUBFORM PROCESS IN EDITING
        foreach ($videos as $video) {
            $subform = $form->getSubForm($video->getGuid());

            $values = $subform->getValues();
            $values = $values[$video->getGuid()];
            if (isset($values['delete']) && $values['delete'] == '1') {

                if ($sitevideoEnabled) {
                    Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
                } else {
                    if (Engine_Api::_()->sitecrowdfunding()->enableVideoPlugin()) {
                        Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
                    }
                }
            } else {
                $video->setFromArray($values);
                $video->save();
            }
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'upload-video', 'project_id' => $project->project_id), "sitecrowdfunding_dashboard", true);
    }

    public function manageLeadersAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $this->view->project_id = $project->project_id;
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        $leader = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1);
        if (!$leader) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->TabActive = 'leaders';

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

        $select = $userTable->select()
            ->from($userTableName)
            ->where("$userTableName.user_id IN (?)", (array) $selectLeaders)
            ->order('displayname ASC');

        $this->view->members = $userTable->fetchAll($select);
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $values = $this->getRequest()->getPost();
        $user_id = $values['user_id'];

        if (!empty($user_id)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user = Engine_Api::_()->getItem('user', $user_id);
            //RETURN IF USER IS ALREADY A LEADER: A CASE WHEN WE CLICK MULTIPLE TIMES
            if ($list->has($user)) {
                return;
            }

            $table = $list->getTable();
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                $list->add($user);
                $leaderList =  $project->getLeaderList();
                // Create some auth stuff for all leaders
                $auth->setAllowed($project, $leaderList, 'topic.edit', 1);
                $auth->setAllowed($project, $leaderList, 'edit', 1);
                $auth->setAllowed($project, $leaderList, 'delete', 1);

                // Add notification
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($user, $viewer, $project, 'sitecrowdfunding_create_leader');

                // Add activity
                $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
                $activityApi->addActivity($user, $project, 'sitecrowdfunding_promote');

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage-leaders', 'controller' => 'dashboard', 'project_id' => $project->getIdentity()), "sitecrowdfunding_extended", true);
    }

    //ACTINO FOR USER AUTO-SUGGEST LIST
    public function manageAutoSuggestAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        //GET PROJECT SUBJECT
        $project = Engine_Api::_()->core()->getSubject();

        //GETTING THE PAGE ID.
        $project_id = $this->_getParam('project_id', $this->_getParam('id', null));

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

        $select = $select->where("$userTableName.user_id NOT IN (?)", (array) $selectLeaders);
        $select = $select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ? OR " . $userTableName . ".email LIKE ?", '%' . $text . '%')
            ->group("$userTableName.user_id")
            ->order('displayname ASC')
            ->limit(20);

        //FETCH ALL RESULT.
        $userlists = $userTable->fetchAll($select);
        $data = array();

        foreach ($userlists as $userlist) {
            $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
            $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
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

    public function getMembersAction() {

        $data = array();

        //GET PROJECT ID
        $project_id = $this->_getParam('project_id', null);

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backerTable->info('name');

        $autoRequest = '';
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $autoRequest = $this->_getParam('user_ids', null);
        } else {
            $autoRequest = $this->_getParam('text', null);
        }

        $select = $backerTable->select()
            ->from($backerTableName, 'user_id')
            ->where('project_id = ?', $project_id);
        $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $select = $usersTable->select()
            ->where('displayname  LIKE ? ', '%' . $autoRequest . '%')
            ->where($usersTableName . '.user_id IN (?)', (array) $user_ids)
            ->order('displayname ASC')
            ->limit('40');
        $users = $usersTable->fetchAll($select);

        foreach ($users as $user) {
            $user_photo = $this->view->itemPhoto($user, 'thumb.icon', '', array('nolazy' => true));
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }
        return $this->_helper->json($data);
    }

    public function demoteAction() {

        $multipleLeaderSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1);

        if (empty($multipleLeaderSetting)) {
            return;
        }

        // Get user
        if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            return $this->_helper->requireSubject->forward();
        }

        $project = Engine_Api::_()->core()->getSubject();
        $list = $project->getLeaderList();

        $this->view->form = $form = new Sitecrowdfunding_Form_Demote();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $list->remove($user);
            $leaderList = $project->getLeaderList();
            $auth = Engine_Api::_()->authorization()->context;
            // Create some auth stuff for all leaders
            $auth->setAllowed($project, $leaderList, 'topic.edit', 1);
            $auth->setAllowed($project, $leaderList, 'edit', 1);
            $auth->setAllowed($project, $leaderList, 'delete', 1);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Removed as Admin')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

    public function projectTransactionsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $project_id = $project->getIdentity();
        /*  // todo: Allow edit for project admins:
          $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
          if ($isProjectAdmin != 1) {
              //GET PROJECT SUBJECT
              if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                  return;
              }
          }
        */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
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

        $this->view->tab = $tab = $this->_getParam('tab', 0);
        $commission = Engine_Api::_()->sitecrowdfunding()->getOrderCommission($project_id);

        if (empty($commission[1])) {
            $this->view->commissionFreePackage = true;
        }
        if (isset($_POST['is_ajax']) && $_POST['is_ajax']) {
            $this->view->only_list_content = true;
        }
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $this->view->project_id = $params['project_id'] = $project_id;

        if (empty($tab)) {
            $this->view->searchForm = $searchForm = new Sitecrowdfunding_Form_DashboardTransactionFilter(array('projectDate' => $project->funding_start_date));
            $searchForm->removeElement('clear');
            $this->view->message = "There are no transactions to show yet.";
            // POPULATE FORM
            if (isset($_POST['search'])) {
                $searchForm->populate($_POST);
                $values = $searchForm->getValues();
                $start_cal_date = $values['start_cal'];
                $end_cal_date = $values['end_cal'];
                $start_tm = date("Y-m-d", strtotime($start_cal_date));
                $end_tm = date("Y-m-d", strtotime($end_cal_date));
                $params['from'] = $start_tm;
                $params['to'] = $end_tm;
                $params['username'] = $_POST['backer_name'];
                $params['transaction_min_amount'] = $_POST['transaction_min_amount'];
                $params['transaction_max_amount'] = $_POST['transaction_max_amount'];
                $params['commission_min_amount'] = $_POST['commission_min_amount'];
                $params['commission_max_amount'] = $_POST['commission_max_amount'];
                $params['payment_status'] = $_POST['payment_status'];
                $this->view->message = "There are no transactions related to this criteria.";
            }
            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->getBackerTransactionsPaginator($params);
            $this->view->total_item = $paginator->getTotalItemCount();
        } else {
            if (!isset($_POST['search'])) {
                $session = new Zend_Session_Namespace('Sitecrowdfunding_Project_Bill_Payment_Detail');
                if (!empty($session->projectBillPaymentDetail)) {
                    $this->view->isPayment = true;
                    $paymentDetail = $session->projectBillPaymentDetail;
                    if (isset($paymentDetail['errorMessage']) && !empty($paymentDetail['errorMessage'])) {
                        $this->view->errorMessage = $paymentDetail['errorMessage'];
                    }
                    $this->view->state = $paymentDetail['state'];
                    $session->unsetAll();
                }
            }

            if (isset($_POST['search'])) {
                $this->_helper->layout->disableLayout();
                $params['search'] = 1;
                if ($_POST['starttime'] == 'From') {
                    $params['from'] = '';
                } else {
                    $params['from'] = $_POST['starttime'];
                }
                if ($_POST['endtime'] == 'To') {
                    $params['to'] = '';
                } else {
                    $params['to'] = $_POST['endtime'];
                }
                $params['bill_min_amount'] = $_POST['bill_min_amount'];
                $params['bill_max_amount'] = $_POST['bill_max_amount'];
                $params['payment'] = $_POST['payment'];
                $this->view->only_list_content = true;
            }

//MAKE PAGINATOR
            $this->view->paginator = Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding')->getProjectBillPaginator($params);
            $this->view->total_item = $this->view->paginator->getTotalItemCount();
        }
    }

    //GET THE DETAILS FOR A PARTICULAR TRANSACTION
    public function detailAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            return;
        }
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $this->view->transaction_id = $transaction_id = $this->_getParam('transaction_id');
        //MISSING TRANSACTION
        if (empty($transaction_id)) {
            return;
        }
        $this->view->tab = $this->_getParam('tab');
        $this->view->transaction = $transaction = Engine_Api::_()->getItem('sitecrowdfunding_transaction', $transaction_id);
        $this->view->backer = $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $transaction->source_id);

        //GET PROJECT OBJECT
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $backer->project_id);
        $package = $project->getPackage();
        $commissionInfo = @unserialize($package->commission_settings);
        $this->view->commissionRate = $commissionInfo['commission_rate'];
        $this->view->gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
        $this->view->user = Engine_Api::_()->getItem('user', $transaction->user_id);
    }

    public function setSettingsAction() {


        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            return;
        }
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        //Check subject 
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $project_id = $project->getIdentity();
        $this->view->form = $form = new Sitecrowdfunding_Form_Settings();
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $tableProjects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');

        //SAVE THE VALUE
        if ($this->getRequest()->isPost()) {
            $tableOtherinfo->update(array('profile_cover' => $_POST['profile_cover']), array('project_id = ?' => $project_id));
            $tableProjects->update(array('member_invite' => $_POST['member_invite'],'member_approval' => $_POST['member_approval']), array('project_id = ?' => $project_id));
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        //POPULATE FORM
        $values['profile_cover'] = $tableOtherinfo->getColumnValue($project_id, 'profile_cover');
        $values['member_invite'] =  $tableProjects->getColumnValue($project_id, 'member_invite');
        $values['member_approval'] =  $tableProjects->getColumnValue($project_id, 'member_approval');
        $form->populate($values);
    }

    public function manageMemberRoleAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PROJECT SUBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        /*    // todo: Allow edit for project admins:
            $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
            if ($isProjectAdmin != 1) {
                $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
                if (empty($editPrivacy)) {
                    return $this->_forward('requireauth', 'error', 'core');
                }
            }
       */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }


        $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1);

        if ($manageMemberSettings == 3) {
            $is_admincreated = array("0" => 0, "1" => 1);
        } elseif ($manageMemberSettings == 2) {
            $is_admincreated = array("0" => 0);
        }

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding');
        $rolesTableName = $rolesTable->info('name');

        $select = $rolesTable->select()
            ->from($rolesTableName)
            ->where($rolesTableName . '.is_admincreated IN (?)', (array) $is_admincreated)
            ->where($rolesTableName . '.project_id = ? ', $project_id)
            ->order('role_id DESC');

        $this->view->manageRolesHistories = $rolesTable->fetchALL($select);

    }

    public function editMemberRoleAction() {

        $role_id = (int) $this->_getParam('role_id');
        $project_id = (int) $this->_getParam('project_id');

        $role = Engine_Api::_()->getItem('sitecrowdfunding_roles', $role_id);

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_EditMemberRole();
        $form->populate($role->toArray());

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'Sitecrowdfunding');

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

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Roles has been edited successfully')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));

    }

    public function deleteMemberRoleAction() {

        $role_id = (int) $this->_getParam('category_id');
        $project_id = (int) $this->_getParam('page_id');
        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding');
        $rolesTable->delete(array('role_id = ?' => $role_id, 'project_id = ?' => $project_id));

    }

    public function addMemberRoleAction() {

        $project_id = (int) $this->_getParam('project_id');

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_AddMemberRole();

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $values = $form->getValues();

        try {

            $row = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->createRow();
            $row->is_admincreated = 0;
            $row->role_name = $values['role_name'];
            $row->project_id = $project_id;
            $row->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Roles has been Added successfully')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));

    }

    public function projectSettingsAction() {

        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_project_settings';

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid()){
            return;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id)) {
            return;
        }

        //GET PROJECT SUBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        /* // todo: Allow edit for project admins:
         $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
         if ($isProjectAdmin != 1) {
             $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
             if (empty($editPrivacy)) {

             }
         } */

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }




        // get photo
        if(!empty($project->getPhotoUrl())){
            $this->view->photoUrl = $project->getPhotoUrl();
        }

        // get video
        $this->view->item = $item = Engine_Api::_()->getItem('sitevideo_video', $project->video_id);

        // Bind Forms
        $this->view->settingsForm = $settingsForm = new Sitecrowdfunding_Form_Settings();
//        $this->view->changePhotoForm = $changePhotoForm = new Sitecrowdfunding_Form_ChangePhoto();
        $this->view->albumForm = $albumForm = new Sitecrowdfunding_Form_Album_Photos();
        $this->view->editVideoForm = $editVideoForm = new Sitecrowdfunding_Form_Video_Editvideo();

        $settingsForm->removeDecorator('title');
        $settingsForm->removeDecorator('description');
        $settingsForm->removeElement('save');
        /*********************************** Settings **********************************/

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $tableProjects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');


        /**** POPULATE FORM VALUES ****/
        // Settings Form
        $settingsFormValues['profile_cover'] = $tableOtherinfo->getColumnValue($project_id, 'profile_cover');
        //$settingsFormValues['member_invite'] =  $tableProjects->getColumnValue($project_id, 'member_invite');
        //$settingsFormValues['member_approval'] =  $tableProjects->getColumnValue($project_id, 'member_approval');
        $settingsForm->populate($settingsFormValues);

        // Photos Form
        $this->view->album = $album = $project->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $this->view->photosCount = count($paginator);
        foreach ($paginator as $photo) {
            $photoSubForm = new Sitecrowdfunding_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
            $photoSubForm->populate($photo->toArray());
            $albumForm->addSubForm($photoSubForm, $photo->getGuid());
            $albumForm->cover->addMultiOption($photo->file_id, $photo->file_id);
        }

        // Videos
        $this->view->videos = $videos = array();
        $this->view->integratedWithVideo = false;
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitecrowdfunding_project", 'item_module' => 'sitecrowdfunding'));
        if ($sitevideoEnabled && $isIntegrated) {
            $params = array();
            $params['parent_type'] = $project->getType();
            $params['parent_id'] = $project->getIdentity();
            $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
            $this->view->integratedWithVideo = true;
        } else {
            if (Engine_Api::_()->sitecrowdfunding()->enableVideoPlugin()) {
                $this->view->videos = $videos = Engine_Api::_()->sitecrowdfunding()->getProjectVideos($project);
            }
        }
        $allowed_upload_video = Engine_Api::_()->sitecrowdfunding()->allowVideo($project, $viewer, count($videos));
        $this->view->upload_video = 1;
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $this->view->upload_video = $allowed_upload_video;
        } else {
            if (empty($allowed_upload_video)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }
        $this->view->count = count($videos);
        $editVideoForm->removeDecorator('title');
        $editVideoForm->removeDecorator('description');
        foreach ($videos as $video) {
            $videoSubForm = new Sitecrowdfunding_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));
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
            /*
            if ($settingsForm->isValid($this->getRequest()->getPost())) {
                $settingValues = $settingsForm->getValues();
                if($settingValues['form_type'] == 'edit_settings'){

                    if ($settingValues['profile_cover'] == 1 && empty($project->photo_id)) {
                        $error = $this->view->translate('Please upload photo.');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $settingsForm->getDecorator('errors')->setOption('escape', false);
                        $settingsForm->addError($error);
                        return;
                    }

                    if($settingValues['profile_cover'] == 0 && empty($project->video_id) ){
                        $error = $this->view->translate('Please upload video.');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $settingsForm->getDecorator('errors')->setOption('escape', false);
                        $settingsForm->addError($error);
                        return;
                    }

                    //saving the settings
                    $tableOtherinfo->update(array('profile_cover' => $settingValues['profile_cover']), array('project_id = ?' => $project_id));
                    //$tableProjects->update(array('member_invite' => $settingValues['member_invite'],'member_approval' => $settingValues['member_approval']), array('project_id = ?' => $project_id));
                }
            }*/

            // Save Profile Pic
            /*
            if ($changePhotoForm->isValid($this->getRequest()->getPost())) {
                $changePhotoValues = $changePhotoForm->getValues();
                if($changePhotoValues['form_type'] == 'edit_profile_pic'){
                    if ($changePhotoForm->Filedata->getValue() !== null) {
                        //GET DB
                        $db = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getAdapter();
                        $db->beginTransaction();

                        //PROCESS
                        try {
                            //SET PHOTO
                            $project->setPhoto($changePhotoForm->Filedata);
                            $db->commit();
                        } catch (Engine_Image_Adapter_Exception $e) {
                            $db->rollBack();
                            $changePhotoForm->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
                        } catch (Exception $e) {
                            $db->rollBack();
                            throw $e;
                        }

                        $currentDate = date('Y-m-d H:i:s');
                        if ($project->state == 'published' && $project->approved && $project->start_date <= $currentDate) {
                            $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $project, Engine_Api::_()->sitecrowdfunding()->getActivtyFeedType($project, 'sitecrowdfunding_change_photo'));
                            $file_id = Engine_Api::_()->getDbtable('photos', 'sitecrowdfunding')->getPhotoId($project_id, $project->photo_id);
                            $photo = Engine_Api::_()->getItem('sitecrowdfunding_photo', $file_id);
                            if ($action != null) {
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                            }
                        }

                    }
                    else if ($changePhotoForm->getValue('coordinates') !== '') {
                        $storage = Engine_Api::_()->storage();
                        $iProfile = $storage->get($project->photo_id, 'thumb.profile');
                        $iSquare = $storage->get($project->photo_id, 'thumb.icon');
                        $pName = $iProfile->getStorageService()->temporary($iProfile);
                        $iName = dirname($pName) . '/nis_' . basename($pName);
                        list($x, $y, $w, $h) = explode(':', $changePhotoForm->getValue('coordinates'));
                        $image = Engine_Image::factory();
                        $image->open($pName)
                            ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                            ->write($iName)
                            ->destroy();
                        $iSquare->store($iName);
                        @unlink($iName);
                    }
                    if (!empty($project->photo_id)) {
                        $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
                        $order = $photoTable->select()
                            ->from($photoTable->info('name'), array('order'))
                            ->where('project_id = ?', $project->project_id)
                            ->group('photo_id')
                            ->order('order ASC')
                            ->limit(1)
                            ->query()
                            ->fetchColumn();

                        $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $project->photo_id));
                    }
                }
            }
            */

            // Save Photos
            if ($albumForm->isValid($this->getRequest()->getPost())) {
                $formType = $this->getRequest()->getPost('form_type');
                if($formType == 'edit_photos'){
                    $albumFormValues = $albumForm->getValues();
                    if (!empty($albumFormValues['cover']) && $project->photo_id != $albumFormValues['cover']) {
                        $album->photo_id = $albumFormValues['cover'];
                        $album->save();
                        $project->photo_id = $albumFormValues['cover'];
                        $project->save();
                        $project->updateAllCoverPhotos();
                    }
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

                    if (!empty($project->photo_id)) {
                        $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
                        $order = $photoTable->select()
                            ->from($photoTable->info('name'), array('order'))
                            ->where('project_id = ?', $project->project_id)
                            ->group('photo_id')
                            ->order('order ASC')
                            ->limit(1)
                            ->query()
                            ->fetchColumn();

                        $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $project->photo_id));
                    }
                }
            }

            // Save Videos
            if ($editVideoForm->isValid($this->getRequest()->getPost())) {
                $formType = $this->getRequest()->getPost('form_type');
                if($formType == 'edit_videos'){
                    if (!empty($editVideovalues['cover']) && $project->video_id != $editVideovalues['cover']) {
                        $project->video_id = $editVideovalues['cover'];
                        $project->save();
                    }
                    foreach ($videos as $video) {
                        $videoSubForm = $editVideoForm->getSubForm($video->getGuid());

                        $editVideovalues = $videoSubForm->getValues();
                        $editVideovalues = $editVideovalues[$video->getGuid()];
                        if (isset($editVideovalues['delete']) && $editVideovalues['delete'] == '1') {

                            if ($sitevideoEnabled) {
                                Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
                            } else {
                                if (Engine_Api::_()->sitecrowdfunding()->enableVideoPlugin()) {
                                    Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
                                }
                            }
                        } else {
                            $video->setFromArray($editVideovalues);
                            $video->save();
                        }
                    }
                }
            }

            return $this->_helper->redirector->gotoRoute(array('action' => 'project-settings', 'project_id' => $project_id), 'sitecrowdfunding_dashboard', true);

        }

    }

}
