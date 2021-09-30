<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_PhotoController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
            return;

        //SET SUBJECT
        if (!Engine_Api::_()->core()->hasSubject()) {

            if (0 != ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null != ($photo = Engine_Api::_()->getItem('sitecrowdfunding_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 != ($project_id = (int) $this->_getParam('project_id')) &&
                    null != ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id))) {
                Engine_Api::_()->core()->setSubject($project);
            }
        }
        $this->_helper->requireUser->addActionRequires(array(
            'upload',
            'upload-photo',
            'edit',
        ));

        $this->_helper->requireSubject->setActionRequireTypes(array(
            'project' => 'sitecrowdfunding_project',
            'upload' => 'sitecrowdfunding_project',
            'view' => 'sitecrowdfunding_photo',
            'edit' => 'sitecrowdfunding_photo',
        ));
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET CHANNEL
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->can_edit = $project->authorization()->isAllowed($viewer, "edit");

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitecrowdfunding_main");

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $photoCount = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id)->photo_count;
            $paginator = $project->getSingletonAlbum()->getCollectiblesPaginator();
            if (Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo")) {
                $this->view->allowed_upload_photo = 1;
                if (empty($photoCount))
                    $this->view->allowed_upload_photo = 1;
                elseif ($photoCount <= $paginator->getTotalItemCount())
                    $this->view->allowed_upload_photo = 0;
            } else {
                $this->view->allowed_upload_photo = 0;
            }
        } else {//AUTHORIZATION CHECK
            $this->view->allowed_upload_photo = $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
        }

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //SELECTED TAB
        $this->view->TabActive = "photo";
        if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
            return $this->_forwardCustom('upload-photo', null, null, array('format' => 'json', 'project_id' => (int) $project->getIdentity()));
        }
        //GET ALBUM
        $album = $project->getSingletonAlbum();

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Photo_Upload();
        //   $form->file->setAttrib('data', array('project_id' => $project->getIdentity()));
        $this->view->tab_id = $content_id = $this->_getParam('content_id');

//CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!isset($_FILES['filedata'])) {
            $form->removeElement('filedata');
        }

//
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        //PROCESS
        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $values = $form->getValues();
            $params = array(
                'project_id' => $project->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            //ADD ACTION AND ATTACHMENTS
            if (isset($values['fancyuploadfileids']) && !empty($values['fancyuploadfileids'])) {
                $values['file'] = explode(" ", $values['fancyuploadfileids']);
            } elseif (isset($_FILES['filedata'])) {
                $photo_id = $project->setPhoto($form->filedata, array('setProjectMainPhoto' => false, 'return' => 'photo'))->photo_id;
                $values['file'] = array($photo_id);
            }
            $api = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $count = 0;
            $currentDate = date('Y-m-d H:i:s');
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $project, Engine_Api::_()->sitecrowdfunding()->getActivtyFeedType($project, 'sitecrowdfunding_photo_upload'), null, array('count' => count($values['file']), 'title' => $project->title));

            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem("sitecrowdfunding_photo", $photo_id);

                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                    continue;

                $photo->collection_id = $album->album_id;
                $photo->album_id = $album->album_id;
                $photo->save();
                if ($project->photo_id == 0) {
                    $project->photo_id = $photo->file_id;
                    $project->save();
                }
                // ACTIVITY FEED WILL BE GENERATED ONLY IF PROJECT IS IN PUBLISHED STATE
                if ($project->state == 'published' && $project->approved && $project->start_date <= $currentDate) {
                    if ($action instanceof Activity_Model_Action && $count < 8) {
                        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                    }
                }
                $count++;
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->view->can_edit) {
            //return $this->_gotoRouteCustom(array('action' => 'editphotos', 'project_id' => $album->project_id), "sitecrowdfunding_albumspecific", true);
            return $this->_gotoRouteCustom(array('action' => 'project-settings', 'project_id' => $album->project_id), "sitecrowdfunding_dashboard", true);
        } else {
            return $this->_gotoRouteCustom(array('project_id' => $album->project_id, 'slug' => $project->getSlug(), 'tab' => $content_id), "sitecrowdfunding_entry_view", true);
        }
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadPhotoAction() {

        //GET CHANNEL
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', (int) $this->_getParam('project_id'));

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $photoCount = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id)->photo_count;
            $paginator = $project->getSingletonAlbum()->getCollectiblesPaginator();
            if (Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo")) {
                $this->view->allowed_upload_photo = 1;
                if (empty($photoCount))
                    $this->view->allowed_upload_photo = 1;
                elseif ($photoCount <= $paginator->getTotalItemCount())
                    $this->view->allowed_upload_photo = 0;
            } else {
                $this->view->allowed_upload_photo = 0;
            }
        } else {//AUTHORIZATION CHECK
            $this->view->allowed_upload_photo = $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
        }

        if (empty($this->view->allowed_upload_photo)) {
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Maximum photo upload limit has been exceeded.');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $values = $this->getRequest()->getPost();
        $response = Seaocore_Service_FancyUpload::upload();

        if (!empty($response['error'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        if (empty($response['path']) || empty(file_exists($response['path']))) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }

        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitecrowdfunding');
        $db = $tablePhoto->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $photo_id = $project->setPhoto($response['path'], array('setProjectMainPhoto' => false, 'return' => 'photo'))->photo_id;
            $this->view->status = true;
            // $this->view->name = $_FILES['Filedata']['name'];
            $this->view->photo_id = $photo_id;
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
            return;
        }
        @unlink($response['path']);
    }

    //ACTION FOR PHOTO DELETE
    public function removeAction() {

        //GET PHOTO ID AND ITEM
        $photo_id = (int) $this->_getParam('photo_id');
        $photo = Engine_Api::_()->getItem('sitecrowdfunding_photo', $photo_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $canEdit = $photo->canEdit();
        if (!$canEdit) {
            return;
        }

        //GET CHANNEL
        $project = $photo->getParent('sitecrowdfunding_project');

        $isajax = (int) $this->_getParam('isajax');
        if ($isajax) {
            $db = Engine_Api::_()->getDbTable('photos', 'sitecrowdfunding')->getAdapter();
            $db->beginTransaction();

            try {
                $photo->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Photo_Delete();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->populate($photo->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Api::_()->getDbTable('photos', 'sitecrowdfunding')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
                    'layout' => 'default-simple',
                    'parentRedirect' => $project->getHref(),
                    //'parentRedirect' => $this->_helper->url->url(array('action' => 'project-settings', 'project_id' => $project->getIdentity()), 'sitecrowdfunding_dashboard', true),
                    'closeSmoothbox' => true,
        ));
    }

    //ACTION FOR VIEWING THE PHOTO
    public function viewAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET PHOTOS
        $this->view->image = $photo = Engine_Api::_()->core()->getSubject();
        //GET ALBUM DETAILS
        $this->view->album = $photo->getCollection();
        //GET SETTINGS
        $this->view->canEdit = $photo->canEdit();

        if (!$viewer || !$viewer_id || $photo->user_id != $viewer->getIdentity()) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
    }

    // Upload
    public function uploadMainPhotoAction(){

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
            /*Engine_Api::_()->getDbTable('photos','sitecrowdfunding')->delete(array(
                'project_id = ? ' => $project_id
            ));*/

            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
            $tableOtherinfo->update(array('profile_cover' => 1), array('project_id = ?' => $project_id));

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

            }


            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo uploaded successfully'))
            ));

        }

    }

    public function uploadMainVideoAction(){

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
            /*$table->delete(array(
                'parent_type = ?' => $project->getType(),
                'parent_id = ?' => $project_id
            ));*/

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

    // Upload
    public function uploadMorePhotoAction(){

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

            }


            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo uploaded successfully'))
            ));

        }

    }

    public function uploadMoreVideoAction(){

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
}
