<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_IndexController extends Seaocore_Controller_Action_Standard {


  public function init() {

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END    
    else {
      if ($this->_getParam('video_id') != null) {
        $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $this->_getParam('video_id'));
        $page_id = $sitepagevideo->page_id;
      }
    }

    //GET VIDEO ID
    $video_id = $this->_getParam('video_id');
    if ($video_id) {
      $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
      if ($sitepagevideo) {
        Engine_Api::_()->core()->setSubject($sitepagevideo);
      }
    }
  }
  
  //ACTION FOR CREATE VIDEO
  public function createAction() {

    if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //UPLOAD VIDEO
      if( isset($_GET['ul'])){
       return $this->_forward('upload-video', null, null, array('format' => 'json'));
      }
      if( isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['name'])){ 
        $_POST['id'] = $this->uploadVideoAction();
      }
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    $getPackagevideoCreate = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');
    $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    //GET SITEPAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
   
    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
      if (empty($isPageOwnerAllow) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    // PACKAGE BASE PRIYACY END
    //VIDEO UPLOAD PROCESS
    $this->view->imageUpload = Engine_Api::_()->sitepagevideo()->isUpload();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

     $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
    if (empty($isManageAdmin) && empty($can_edit)) {

      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //FORM GENERATON

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$this->view->form = $form = new Sitepagevideo_Form_Video();
    } else {
      $this->view->form = $form = new Sitepagevideo_Form_SitemobileVideo();
    }

    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue( $this->_getParam('type') );
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues('url');
      return;
    }

    // todo: Page Video: naaziya: Not allowing to create video, so commented out
//    if (empty($getPackagevideoCreate)) {
//      return;
//    }

    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagevideo.utility.type', convert_uuencode($sitepageModHostName));
    }

    //GET FORM VALUES
    $values = $form->getValues();

    $values['owner_id'] = $viewer->getIdentity();

    //VIDEO CREATION PROCESS
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');

    $insert_action = false;
      
    $db = $videoTable->getAdapter();
    $db->beginTransaction();
    try {

      if ($values['type'] == 3) { 
        $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $this->_getParam('id'));
        
        if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          if (!$this->_helper->requireUser()->checkRequire()) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
              return;
            }

            if (!$this->getRequest()->isPost()) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
              return;
            }

            $values = $this->getRequest()->getPost();

            $this->view->clear_cache = true;
            if( empty($_FILES['Filedata']) ){
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
              return;
            }

            if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
              return;
            }

            $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
            if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
              return;
            }

              $viewer = Engine_Api::_()->user()->getViewer();
              $values['owner_id'] = $viewer->getIdentity();

              $params = array(
                  'owner_id' => $viewer->getIdentity()
              );
              $sitepagevideo = Engine_Api::_()->sitepagevideo()->createSitepagevideo($params, $_FILES['Filedata'], $values);
              $sitepagevideo->title = $_FILES['Filedata']['name'];
              $sitepagevideo->owner_id = $viewer->getIdentity();
//              $sitepagevideo->save();
              $this->view->status = true;
              $this->view->name = $_FILES['Filedata']['name'];
              $this->view->code = $sitepagevideo->code;
              $this->view->video_id = $sitepagevideo->video_id;
            
          } 
      } else {
        $sitepagevideo = $videoTable->createRow();
      }

      $sitepagevideo->setFromArray($values);
      $sitepagevideo->page_id = $this->_getParam('page_id');
      $sitepagevideo->save();

      //THUMBNAIL CREATION
      $thumbnail = $this->handleThumbnail($sitepagevideo->type, $sitepagevideo->code);
      $ext = ltrim(strrchr($thumbnail, '.'), '.');
      $thumbnail_parsed = @parse_url($thumbnail);

      if (@GetImageSize($thumbnail)) {
        $valid_thumb = true;
      } else {
        $valid_thumb = false;
      }

      if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(120, 240)
                ->write($thumb_file)
                ->destroy();

        try {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
              'parent_type' => $sitepagevideo->getType(),
              'parent_id' => $sitepagevideo->getIdentity()
                  ));

          //REMOVE TEMP FILES
          @unlink($thumb_file);
          @unlink($tmp_file);
        } catch (Exception $e) {
          
        }
        $information = $this->handleInformation($sitepagevideo->type, $sitepagevideo->code);
        $sitepagevideo->duration = $information['duration'];
        $sitepagevideo->photo_id = $thumbFileRow->file_id;
        $sitepagevideo->status = 1;
        $sitepagevideo->featured = 0;
        $sitepagevideo->save();

        //INSERT NEW ACTION ITEM
        $insert_action = true;
      }

      if ($values['ignore'] == true) {
        $sitepagevideo->status = 1;
        $sitepagevideo->save();
        $insert_action = true;
      }

      //COMMENT PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
      $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($sitepagevideo, $role, 'comment', ($i <= $commentMax));
      }

      //TAG WORK
      if (!empty($values['tags'])) {
        $tags = preg_split('/[,]+/', $values['tags']);
        $sitepagevideo->tags()->addTagMaps($viewer, $tags);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      if ($insert_action && $sitepagevideo->search == 1) {
        $owner = $sitepagevideo->getOwner();
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepagevideo_admin_new';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
          $activityFeedType = 'sitepagevideo_new';

        if ($activityFeedType) {
          $action = $actionTable->addActivity($owner, $sitepage, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        }
        if ($action != null) {
          $actionTable->attachActivity($action, $sitepagevideo);
        }

        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

          $video_array = array();
          $video_array['type'] = 'sitepagevideo_new';
          $video_array['object'] = $sitepagevideo;

          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($video_array);
        }

				//PAGE VIDEO CREATE NOTIFICATION AND EMAIL WORK
				$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
				if(!empty($action)) {
					if ($sitepageVersion >= '4.3.0p1') {
						Engine_Api::_()->sitepage()->sendNotificationEmail($sitepagevideo, $action, 'sitepagevideo_create', 'SITEPAGEVIDEO_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');
						$isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
						if (!empty($isPageAdmins)) {
							//NOTIFICATION FOR ALL FOLLWERS.
							Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitepagevideo, $action, 'sitepagevideo_create');
						}
					}
				}
      }

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($sitepagevideo) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true);
  }

  //ACTION FOR EDIT VIDEO
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    //GET PAGE ID AND SUBJECT
    $page_id = $this->_getParam('page_id', 0);
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GET VIDEO OBJECT
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $this->_getParam('video_id'));

    //GET TAB ID
    $this->view->tab_selected_id = $this->_getParam('tab');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, VIDEO OWNER AND PAGE OWNER CAN EDIT VIDEO
    if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //FORM GENERATION
    $this->view->form = $form = new Sitepagevideo_Form_Edit();

    Engine_Api::_()->sitepagevideo()->setVideoPackages();

    //PREPARE TAGS
    $sitepageTags = $sitepagevideo->tags()->getTagMaps();
    $tagString = '';
    foreach ($sitepageTags as $tagmap) {
      if ($tagString !== '') {
        $tagString .= ', ';
      }
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($sitepagevideo->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    //PROCESS
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagevideo->setFromArray($values);

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $sitepagevideo->tags()->setTagMaps($viewer, $tags);
      $sitepagevideo->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $page_id = $this->_getParam('page_id');

    //REDIRECTING TO THE EVENT VIEW PAGE
    return $this->_redirectCustom($sitepagevideo->getHref(), array('prependBase' => false));
  }

  //ACTION FOR DELETE VIDEO
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    //GET VIDEO OBJECT
    $this->view->sitepagevideo = $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $this->getRequest()->getParam('video_id'));

    //GET VIDEO TITLE
    $this->view->title = $sitepagevideo->title;

    //GET PAGE ID
    $page_id = $sitepagevideo->page_id;

    //GET NAVIGATION 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //GET SITEPAGE SUBJECT
    if (!empty($page_id)) {
      $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
      //PACKAGE BASE PRIYACY END
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if (empty($isManageAdmin)) {
        $can_edit = 0;
      } else {
        $can_edit = 1;
      }
      //END MANAGE-ADMIN CHECK
    } else {
      $can_edit = 1;
    }
    if (!$sitepagevideo) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    //VIDEO OWNER AND PAGE OWNER CAN DELETE VIDEO
    if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $sitepagevideo->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->delete(array('video_id =?' => $this->getRequest()->getParam('video_id')));

      $sitepagevideo->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    return $this->_gotoRouteCustom(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true);
  }

  //ACTION FOR VIEW VIDEO
  public function viewAction() {

    //IF SITEPAGEVIDEO SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitepagevideo_video')->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET VIDEO ITEM
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $this->getRequest()->getParam('video_id'));

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE VIDEO OR NOT
    if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1 && ($sitepagevideo->search != 1 || $sitepagevideo->status != 1)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
         Zend_Registry::set('sitemobileNavigationName','setNoRender');
         }
    }
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationFormBack', 'Back');
     }
    
   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR DO RATING
  public function rateAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $rating = $this->_getParam('rating');
    $video_id = $this->_getParam('video_id');

    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitepagevideo');
    $db = $ratingTable->getAdapter();
    $db->beginTransaction();

    try {

      $ratingTable->setRating($video_id, $user_id, $rating);

      $total = $ratingTable->ratingCount($video_id);

      $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

      //UPDATE CURRENT AVERAGE RATING IN VIDEO TABLE
      $rating = $ratingTable->rateVideo($video_id);

      $sitepagevideo->rating = $rating;
      $sitepagevideo->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  //ACTION FOR HANDLES THUMBNAIL
  public function handleThumbnail($type, $code = null) {
    switch ($type) {
      //youtube
      case "1":
        //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
        return "https://i.ytimg.com/vi/$code/default.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
    }
  }

  //ACTION FOR VIDEO COMPOSE UPLOAD
  public function composeUploadAction() {

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composer_type = $this->_getParam('c_type', 'wall');

    // extract code
    //$code = $this->extractCode("http://www.youtube.com/watch?v=5osJ8-NttnU&feature=popt00us08", $video_type);
    //$code = parse_url("http://vimeo.com/3945157/asd243", PHP_URL_PATH);

    $code = $this->extractCode($video_url, $video_type);
    // check if code is valid
    // check which API should be used
    if ($video_type == 1) {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == 2) {
      $valid = $this->checkVimeo($code);
    }


    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    //$values['user_id'] = $viewer->getIdentity();
    //  $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);

    if ($valid) {
      $db = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getAdapter();
      $db->beginTransaction();

      try {
        $information = $this->handleInformation($video_type, $code);
        // create video
        $table = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
        $video = $table->createRow();
        $video->title = $information['title'];
        $video->description = $information['description'];
        $video->duration = $information['duration'];
        $video->owner_id = $viewer->getIdentity();
        $video->code = $code;
        $video->type = $video_type;
        $video->save();

        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);

        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(120, 240)
                ->write($thumb_file)
                ->destroy();

        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
                ));

        // If video is from the composer, keep it hidden until the post is complete
        if ($composer_type)
          $video->search = 0;

        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }


      // make the video public
      if ($composer_type === 'wall') {
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $roles));
          $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
        }
      }

      $this->view->status = true;
      $this->view->video_id = $video->video_id;
      $this->view->photo_id = $video->photo_id;
      $this->view->title = $video->title;
      $this->view->description = $video->description;
      $this->view->src = $video->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
    } else {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
    }
  }

  //ACTION FOR VIDEO VALIDATON
  public function validationAction() {
    global $sitepagevideo_validation;
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    if (!empty($sitepagevideo_validation)) {
      // check which API should be used
      if ($video_type == "youtube") {
        $valid = $this->checkYouTube($code);
      }
      if ($video_type == "vimeo") {
        $valid = $this->checkVimeo($code);
      }

      $this->view->code = $code;
      $this->view->ajax = $ajax;
      $this->view->valid = $valid;
    }
  }

  //HELPER FUNCTIONS
  public function extractCode($url, $type) {
    switch ($type) {
      //youtube
      case "1":
                // change new youtube URL to old one
        $new_code = @pathinfo($url);
        $url = preg_replace("/#!/", "?", $url);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($url);
        if ($arr['host'] === 'youtu.be') {
          $data = explode("?", $new_code['basename']);
          $code = $data[0];
        } else {
          $parameters = $arr["query"];
          parse_str($parameters, $data);
          $code = $data['v'];
          if ($code == "") {
            $code = $new_code['basename'];
          }
        }
        return $code;
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);
        return $code['basename'];
    }
  }

  // YouTube Functions
  public function checkYouTube($code){
    $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) return false;

    $data = Zend_Json::decode($data);
    if (empty($data['items'])) return false;
    return true;
  }

  //VIMEO FUNCTIONS
  public function checkVimeo($code) {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $video_id = count($data->video->id);
    if ($video_id == 0)
      return false;
    return true;
  }

  //ACTION FOR UPLOAD VIDEO
  public function uploadVideoAction() {
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    $response = Seaocore_Service_FancyUpload::upload();

    if(!empty($response['error'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . $response['error'];
      return;
    }

    if(empty($response['path']) || empty(file_exists($response['path']))) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if (in_array(pathinfo($response['path'], PATHINFO_EXTENSION), $illegal_extensions)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
          'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->sitepagevideo()->createSitepagevideo($params, $response['path'], $values);
      $video->title = $response['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();
      $this->view->status = true;
      // $this->view->name = $response['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->video_id;
      $db->commit();
      return $video->video_id;
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
      // throw $e;
      return;
    }
    // @unlink($response['path']);
  }

  //ACTION FOR UPLOAD VIDEO
  public function uploadAction() {
    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forwardCustom('upload-video', null, null, array('format' => 'json'));

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->form = $form = new Sitepagevideo_Form_Video();
    $this->view->navigation = $this->getNavigation();

    if (!$this->getRequest()->isPost()) {
      if (null !== ($video_id = $this->_getParam('video_id'))) {
        $form->populate(array(
            'video' => $video_id
        ));
      }
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $video = $form->saveValues();
  }

  //ACTION FOR HANDLE INFORMATION
  public function handleInformation($type, $code) {
    switch ($type) {
      //youtube
      case "1":
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
        $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
        if (empty($data)) {
          return;
        }
        $data = Zend_Json::decode($data);
        $information = array();
        $youtube_video = $data['items'][0];
        $information['title'] = $youtube_video['snippet']['title'];
        $information['description'] = $youtube_video['snippet']['description'];
        $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
    }
  }

  //ACTION FOR MAKE THE SITEPAGEVIDEO FEATURED/UNFEATURED
  public function featuredAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIDEO ID AND OBJECT
    $video_id = $this->view->video_id = $this->_getParam('video_id');
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

    $this->view->featured = $sitepagevideo->featured;

    //GET PAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);

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

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    //CHECK CAN MAKE FEATURED OR NOT(ONLY SITEPAGE VIDEO CAN MAKE FEATURED/UN-FEATURED)
    if ($viewer_id == $sitepagevideo->owner_id || !empty($this->view->canEdit)) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitepagevideo->featured == 0) {
          $sitepagevideo->featured = 1;
        } else {
          $sitepagevideo->featured = 0;
        }

        $sitepagevideo->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitepagevideo->featured) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made featured.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made un-featured.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagevideo->page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

   //ACTION FOR MAKE THE SITEPAGEVIDEO FEATURED/UNFEATURED
  public function highlightedAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIDEO ID AND OBJECT
    $video_id = $this->view->video_id = $this->_getParam('video_id');
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

    $this->view->highlighted = $sitepagevideo->highlighted;

    //GET PAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);

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

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    //CHECK CAN MAKE FEATURED OR NOT(ONLY SITEPAGE VIDEO CAN MAKE FEATURED/UN-FEATURED)
    if ($viewer_id == $sitepagevideo->owner_id || !empty($this->view->canEdit)) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitepagevideo->highlighted == 0) {
          $sitepagevideo->highlighted = 1;
        } else {
          $sitepagevideo->highlighted = 0;
        }

        $sitepagevideo->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitepagevideo->highlighted) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made highlighted.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made un-highlighted.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagevideo->page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

  public function browseAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  // ACTION FOR FEATURED VIDEOS CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredVideosCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];

    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    $params = array();
    $params['category_id'] = $_GET['category_id'];
    $params['zero_count'] = 'featured';

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $params['start_index'] = $startindex;

    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $params['limit'] = $limit * 2;
    $this->view->featuredVideos = $this->view->featuredVideos = $featuredVideos = Engine_Api::_()->getDbTable('videos', 'sitepagevideo')->widgetVideosData($params);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredVideos);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

  public function homeAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  //ACTION FOR ADDING VIDEO OF THE DAY
  public function addVideoOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitepagevideo_Form_ItemOfDayday();
    $video_id = $this->_getParam('video_id');
   // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $video_id)->where('resource_type = ?', 'sitepagevideo_video');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $video_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepagevideo_video';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Video of the Day has been added successfully.'))
              ));
    }
  }

 //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagsCloudAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //GENERATE TAG-CLOULD HIDDEN FROM
    $this->view->form = $form = new Sitepage_Form_Searchtagcloud();

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getTagCloud();
    $tag_id_array = array();
    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;

      if ($spread == 0) {
        $spread = 1;
      }

      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

}

?>