<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AlbumController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if (!$sitepagealbumEnabled) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();
    $page_id = $this->_getParam('page_id', $this->_getParam('id', null));

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);      
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        } else {
          $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
          if (empty($isPageOwnerAllow)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $page_id = $album->page_id;
      }
    }
  }

  //ACTION FOR EDIT THE ALBUM
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitepage_page')->isValid())
      return;

    //GET ALBUM ID
    $album_id = $this->_getParam('album_id');

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $ownerList = $sitepage->getPageOwnerList();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      Engine_Api::_()->core()->setSubject($album);
    }

    //MAKE FORM
    $this->view->form = $form = new Sitepage_Form_Album_Edit();

    //START PHOTO PRIVACY WORK
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if ($sitepagealbumEnabled) {
      $auth = Engine_Api::_()->authorization()->context;
     // $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      	$sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
			if (!empty($sitepagememberEnabled)) {
				$roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}
//       foreach ($roles as $role) {
//         if ($form->auth_tag) {
//           if (1 == $auth->isAllowed($album, $role, 'tag')) {
//             $form->auth_tag->setValue($role);
//           }
//         }
//       }

			foreach ($roles as $roleString) {
				$role = $roleString;
				if ($role === 'like_member') {
					$role = $ownerList;
				}
				if ($form->auth_tag) {
					if (1 == $auth->isAllowed($album, $role, 'tag')) {
						$form->auth_tag->setValue($roleString);
					}
				}
			}
    }
    //END PHOTO PRIVACY WORK
    //COMMENT PRIVACY
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $commentMax = array_search("everyone", $roles);
    foreach ($roles as $i => $role) {
      $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
    }
    //END PHOTO PRIVACY	WORK
    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($album->toArray());
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //PROCESS
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET FORM VALUES
      $values = $form->getValues();
      $album->setFromArray($values);
      $album->save();

      //CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
			if (!empty($sitepagememberEnabled)) {
				$roles = array('owner', 'member', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}

      //REBUILD PRIVACY
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      //START TAG PRIVACY
      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'registered';
        }
      }
      $tagMax = array_search($values['auth_tag'], $roles);
			foreach ($roles as $i => $role) {
				if ($role === 'like_member') {
					$role = $ownerList;
				}
				$auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}

//       foreach ($roles as $i => $role) {
//         $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
//       }
      //END TAG PRIVACY
      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved.')),
        'smoothboxClose' => 300,
        'parentRefresh' => 300,
    ));
  }

  //ACTION FOR VIEW THE ALBUM
  public function viewAction() {

   //GET PAGE ID
    $page_id = $this->_getParam('page_id');
    
    
    $album_id = $this->_getParam('album_id');
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
    if (!$album) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    
    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    
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
    if (empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0'))) {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }    

  }

  //ACTION FOR DELETE THE ALBUM
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitepage_page')->isValid())
      return;

    //GET PAGE ID
    $page_id = $this->_getParam('page_id', null);

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ID
    $album_id = $this->_getParam('album_id', $this->_getParam('album_id', null));

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

    //GET DELETE FORM
    $this->view->form = $form = new Sitepage_Form_Album_Delete();

    //CHECK ALBUM EXIST OR NOT TO DELETE
    if (!$album) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exist or not authorized to delete.");
      return;
    }

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //GET DB
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {

      //DELETE ALBUM
      $album->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->_getParam('tab')), 'sitepage_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => Zend_Registry::get('Zend_Translate')->_("Album has been deleted.")
    ));
  }

  //ACTION FOR EDIT PHOTOS TO THE ALBUM
  public function editPhotosAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitepage_page')->isValid())
      return;

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ID
    $album_id = $this->view->album_id = $this->_getParam('album_id');

    //GET PAGE ID
    $page_id = $this->view->page_id = $this->_getParam('page_id');

    //GET REQUEST ISAJAX OR NOT
    $isajax = $this->_getParam('is_ajax');

    //GET ITEM ALBUM
    $this->view->album = $album = $sitepage->getSingletonAlbum($album_id);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //SEND CURRENT PAGE NUMBER TO THE TPL
    $this->view->currentPageNumbers = $currentPageNumbers = $this->_getParam('pages', 1);

    //SEND PHOTOS PER PAGE TO THE TPL
    $this->view->photos_per_page = $photos_per_page = 20;

    //SET PAGE PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['page_id'] = $page_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
     $paramsPhoto['viewPage'] = 1;
    //GET TOTAL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
    
    $page_vars = Engine_Api::_()->sitepage()->makePage($total_photo, $photos_per_page, $currentPageNumbers);
    $page_array = Array();
    for ($x = 0; $x <= $page_vars[2] - 1; $x++) {
      if ($x + 1 == $page_vars[1]) {
        $link = "1";
      } else {
        $link = "0";
      }
      $page_array[$x] = Array('page' => $x + 1,
          'link' => $link);
    }
    $this->view->pagearray = $page_array;
    $this->view->maxpage = $page_vars[2];
    $this->view->pstart = 1;
    $this->view->total_images = $total_photo;

    //SET LIMIT PARAMS
    $paramsPhoto['start'] = $photos_per_page;
    $paramsPhoto['end'] = $page_vars[0];
    $paramsPhoto['viewPage'] = 1;
    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $this->view->photos = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);

    //MAKE EDIT PHOTOS FORM
    $this->view->form = $form = new Sitepage_Form_Album_Photos();
    foreach ($this->view->photos as $photo) {
      $subform = new Sitepage_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->file_id, $photo->file_id);

//      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
//				$form->page_cover->addMultiOption($photo->file_id, $photo->file_id);
//      }
    }

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      //return;
    }

    //GET DB
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET FORM VALUES
      $values = $form->getValues();
      if (!empty($values['cover'])) {
        $album->photo_id = $values['cover'];
        $album->save();
      }

//			if (isset($values['page_cover'])) {
//				Engine_Api::_()->getDbtable('pages', 'sitepage')->update(array('page_cover' => $values['page_cover']), array('page_id =?' => $page_id));
//			}

      //PROCESS
      foreach ($this->view->photos as $photo) { 
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();
        $values = $values[$photo->getGuid()];

        //UNSET TEH PHOTO ID
        unset($values['photo_id']);

        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();

          //FETCHING ALL PHOTOS
          $count = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
          if (empty($count)) {
            Engine_Api::_()->getItemTable('sitepage_album')->update(array('photo_id' => 0,), array('page_id =?' => $page_id, 'album_id =?' => $album_id));
          }
        } else { 
          $photo->setFromArray($values);
          $photo->save();
        }
      }
      

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if (!$isajax) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'page_id' => $page_id, 'album_id' => $album_id, 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepage_albumphoto_general', true);
    }
  }

  public function viewAlbumAction() {

    //GET PAGE ID
    $page_id = $this->_getParam('page_id', null);

    //SET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $page_id;
    $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1);
    if($albums_order) {
			$paramsAlbum['orderby'] = 'album_id DESC';
    } else {
      $paramsAlbum['orderby'] = 'album_id ASC';
    }

    //FETCH ALBUMS
    $this->view->album = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);
  }

  //ACTION FOR CHANGE THE ORDER OF THE PHOTOS
  public function orderAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET ALBUM ID
    $album_id = $this->_getParam('album_id');

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //GET ORDER
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    //GET CURRENT ORDER
    $currentOrder = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPagePhotosOrder($album_id, $page_id);

    //FIND THE STARTING POINT?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      Engine_Api::_()->getItemTable('sitepage_photo')->update(array('order' => $i), array('photo_id = ?' => $photo_id));
    }
    $this->view->status = true;
  }

  //ACTION FOR CHANGE THE ORDER OF THE ALBUMS
  public function albumOrderAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //GET ORDER
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    //GET CURRENT ORDER OF ALBUM
    $currentOrder = Engine_Api::_()->getDbtable('albums', 'sitepage')->getPageAlbumsOrder($page_id);

    //FIND THE STARTING POINT?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      Engine_Api::_()->getItemTable('sitepage_album')->update(array('order' => $i), array('photo_id = ?' => $photo_id));
    }
    $this->view->status = true;
  }

  public function composeUploadAction() {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $response = Seaocore_Service_FancyUpload::upload();
    if (!empty($response['error'])) {
      $this->view->status = false;
      return;
    }

    if (empty($response['path']) || ( $response['type'] != 'html4' && empty(file_exists($response['path'])) ) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No File');
      return;
    }

    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('albums', 'sitepage');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $type = $this->_getParam('type', 'wall');

      if (empty($type))
        $type = 'wall';
      $page_id = $this->_getParam('page_id', $this->_getParam('id', null));

      //PACKAGE BASE PRIYACY START
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

      $album = $table->getSpecialAlbum($sitepage, $type);

      $photoTable = Engine_Api::_()->getDbtable('photos', 'sitepage');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
          'page_id' => $page_id,
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
      ));
      $photo->save();
      $photo->setPhoto($response['path']);

      if ($type == 'message') {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

      $photo->album_id = $album->album_id;
      $photo->collection_id = $album->album_id;
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->file_id;
        $album->save();
      }

      if ($type != 'message') {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      $this->view->src = $photo->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
      
      $requesttype = $this->_getParam('feedphoto', false);
      if ($requesttype) {
      	echo '<img src="'. $photo->getPhotoUrl() . '" id="compose-photo-preview-image" class="compose-preview-image"><div id="advfeed-photo"><input type="hidden" name="attachment[photo_id]" value="'.$photo->photo_id.'"><input type="hidden" name="attachment[type]" value="sitepagephoto"></div>';
      	exit();
      }
    } catch (Exception $e) {
      $db->rollBack();
      //throw $e;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
      $this->view->status = false;
    }
    @unlink($response['path']);
  }

  public function browseAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if (empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0'))) {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  public function homeAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if (empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0'))) {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  public function featuredAction() {
    
    $this->view->album = $album = Engine_Api::_()->getItem('sitepage_album', $this->_getParam('album_id', $this->_getParam('album_id', null)));
    $album->featured = !$album->featured;
    $album->save();
    exit(0);
  }

  //ACTION FOR ADDING ALBUM OF THE DAY
  public function addAlbumOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitepagealbum_Form_ItemOfDayday();
    $album_id = $this->_getParam('album_id');
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
        $select = $dayItemTime->select()->where('resource_id = ?', $album_id)->where('resource_type = ?', 'sitepage_album');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $album_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepage_album';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  //'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Album of the Day has been added successfully.'))
              ));
    }
  }

}
?>