<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_PhotoController extends Seaocore_Controller_Action_Standard {

  protected $_set;

  public function init() {

    $this->_set = 0;
    if ($this->_getParam('set')) {
      $photo_id = Engine_Api::_()->sitealbum()->getEncodeToDecode($this->_getParam('set'));
      if (Engine_Api::_()->getItem('album_photo', $photo_id))
        $this->_set = 1;
    }

    if (!$this->_set && !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
      return;

    if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
            null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id)) && !Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($photo);
    }
  }
    //ACTION FOR PHOTO BROWSE PAGE 
  public function browseAction() {

    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
      return;
    }
    //GET PAGE OBJECT
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageSelect = $pageTable->select()->where('name = ?', "sitealbum_photo_browse");
    $pageObject = $pageTable->fetchRow($pageSelect);

    $params = array();
    $album_type_title = '';
    if (!empty($pageObject->title)) {
      $params['default_title'] = $title = $pageObject->title;
    } else {
      $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Browse Photos');
    }

    //GET ALBUM CATEGORY TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $category_id = $request->getParam('category_id', null);

    if (!empty($category_id)) {
      if ($album_type_title)
        $params['album_type_title'] = $title = $album_type_title;
      $meta_title = $tableCategory->getCategory($category_id)->meta_title;
      if (empty($meta_title)) {
        $params['categoryname'] = Engine_Api::_()->getItem('album_category', $category_id)->getCategorySlug();
      } else {
        $params['categoryname'] = $meta_title;
      }
      $meta_description = $tableCategory->getCategory($category_id)->meta_description;
      if (!empty($meta_description))
        $params['description'] = $meta_description;

      $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
      if (empty($meta_keywords)) {
        $params['categoryname_keywords'] = Engine_Api::_()->getItem('album_category', $category_id)->getCategorySlug();
      } else {
        $params['categoryname_keywords'] = $meta_keywords;
      }

      $subcategory_id = $request->getParam('subcategory_id', null);
      if (!empty($subcategory_id)) {
        $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
        if (empty($meta_title)) {
          $params['subcategoryname'] = Engine_Api::_()->getItem('album_category', $subcategory_id)->getCategorySlug();
        } else {
          $params['subcategoryname'] = $meta_title;
        }

        $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
        if (!empty($meta_description))
          $params['description'] = $meta_description;

        $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
        if (empty($meta_keywords)) {
          $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('album_category', $subcategory_id)->getCategorySlug();
        } else {
          $params['subcategoryname_keywords'] = $meta_keywords;
        }
      }
    }

    //SET META TITLE
    Engine_Api::_()->sitealbum()->setMetaTitles($params);

    //SET META TITLE
    Engine_Api::_()->sitealbum()->setMetaDescriptionsBrowse($params);

    //GET LOCATION
    if (isset($_GET['location']) && !empty($_GET['location'])) {
      $params['location'] = $_GET['location'];
    }

    //GET TAG
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $params['tag'] = $_GET['tag'];
    }

    if (isset($_GET['search']) && !empty($_GET['search'])) {
      $params['search'] = $_GET['search'];
    }

    //GET ALBUMS TITLE
    $params['album_type_title'] = $this->view->translate('Albums');

    //SET META KEYWORDS
    Engine_Api::_()->sitealbum()->setMetaKeywords($params);

    $this->_helper->content->setNoRender()->setEnabled();
  }

  // ACTION FOR PHOTO VIEW
  public function viewAction() {

    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;

    $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
    if (empty($getLightBox)) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $this->view->album = $album = $photo->getAlbum();
    } else {
      $this->view->album = $album = $photo->getCollection();
    }
    
    $album_id = $album->getIdentity();
    $sitealbum_password_protected = isset($_COOKIE["sitealbum_password_protected_$album_id"]) ? $_COOKIE["sitealbum_password_protected_$album_id"] : 0;
    if(isset($album->password) && !empty($album->password) && $album->owner_id != $viewer->getIdentity() && !$sitealbum_password_protected) {
     return $this->_forward('requireauth', 'error', 'sitealbum');
    }
    
    if (!$album->isViewableByNetwork()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;

    $sitealbumGetview = Zend_Registry::isRegistered('sitealbum_getview') ? Zend_Registry::get('sitealbum_getview') : null;
    if (empty($sitealbumGetview)) {
      return;
    }

    if (!$this->_set && !$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid())
      return;


    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->album_id) {
        $this->_forward('requiresubject', 'error', 'core');
        return;
      }
    } else {
      if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->collection_id) {
        $this->_forward('requiresubject', 'error', 'core');
        return;
      }
    }

    $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
    if (!empty($enable_facebookse)) {
      $facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
      $fbversion = $facebooksemodule->version;
      if ($fbversion >= '4.2.0') {
        $success_showFBCommentBox = Engine_Api::_()->facebookse()->showFBCommentBox('album');
        if ($success_showFBCommentBox) {
          $curr_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
          Engine_Api::_()->facebookse()->SiteURLTOScrape($curr_url, 'album');
        }
      }
    }

    $this->_helper->content->setNoRender()->setEnabled();
  }

  //ACTION FOR DELETING PHOTO
  public function deleteAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    //FOR LIGHTBOX VIEW
   // if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'delete')->isValid())
      //return;

    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    $album = $photo->getParent();

    $this->view->form = $form = new Sitealbum_Form_Photo_Delete();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $photo->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    
    $isajax = (int) $this->_getParam('isajax', 0);
    if($isajax) {
        $data = array();
        return $this->_helper->json($data);
    } else {
        return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                'layout' => 'default-simple',
                'parentRedirect' => $album->getHref(),
        ));
    }
  }
  
  //ACTION FOR EDITING PHOTO
  public function editAction() {

    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;

    $photo = Engine_Api::_()->core()->getSubject('album_photo');

    $this->view->form = $form = new Sitealbum_Form_Photo_Edit();

    $form->populate($photo->toArray());

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setFromArray($values);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
    ));
  }

  public function ajaxPhotoViewAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    $this->_helper->layout->disableLayout();
    $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
    if (empty($getLightBox)) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $this->view->album = $album = $photo->getAlbum();
    } else {
      $this->view->album = $album = $photo->getCollection();
    }

    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
    $this->view->isajax = (int) $this->_getParam('isajax', 0);
    $this->view->viewDisplayHR = $this->_getParam('viewDisplayHR', 0);
// if this is sending a message id, the user is being directed from a coversation
// check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;

    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid())
      return;

    $getAjaxView = Zend_Registry::isRegistered('sitealbum_getajaxview') ? Zend_Registry::get('sitealbum_getajaxview') : null;
    if (empty($getAjaxView)) {
      return;
    }
    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->album_id) {
        $this->_forward('requiresubject', 'error', 'core');
        return;
      }
    } else {
      if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->collection_id) {
        $this->_forward('requiresubject', 'error', 'core');
        return;
      }
    }

    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);

    $this->view->allowView = $this->view->canMakeFeatured = false;
    if (!empty($viewer_id) && ($viewer->level_id == 1 || $viewer->level_id == 2)) {
      $this->view->canMakeFeatured = true;
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
    }
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $this->view->nextPhoto = $photo->getNextPhoto();
      $this->view->previousPhoto = $photo->getPreviousPhoto();
      $this->view->getPhotoIndex = $photo->getPhotoIndex();
    } else {
      $this->view->nextPhoto = $photo->getNextCollectible();
      $this->view->previousPhoto = $photo->getPrevCollectible();
      $this->view->getPhotoIndex = $photo->getCollectionIndex();
    }

    $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
  }

  //ACTION FOR LIGHT-BOX VIEW OF PHOTO
  public function lightBoxViewAction() {
    $this->_helper->layout->disableLayout();
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;

    $getLightBox = Zend_Registry::isRegistered('sitealbum_featuredview') ? Zend_Registry::get('sitealbum_featuredview') : null;
    if (empty($getLightBox)) {
      return;
    }

    $this->view->isajax = (int) $this->_getParam('isajax', 0);
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $this->view->album = $album = $photo->getAlbum();
    } else {
      $this->view->album = $album = $photo->getCollection();
    }
    
    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    $getLightbox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
    if (empty($getLightbox)) {
      return;
    }
    $this->view->viewPermission = $viewPermission = $photo->authorization()->isAllowed($viewer, 'view');
    $album_id = $album->getIdentity();
    $sitealbum_password_protected = isset($_COOKIE["sitealbum_password_protected_$album_id"]) ? $_COOKIE["sitealbum_password_protected_$album_id"] : 0;
    if(isset($album->password) && !empty($album->password) && $album->owner_id != $viewer->getIdentity() && !$sitealbum_password_protected) { 
        $this->view->viewPermission = $viewPermission = false;
        $this->view->albumPasswordProtected = 1;
    }
    
    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->album_id) {
        $this->_forward('requiresubject', 'error', 'core');
        return;
      }
    } else {
      if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->collection_id) {
        $this->_forward('requiresubject', 'error', 'core');
        return;
      }
    }

    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $this->view->getPhotoIndex = $photo->getPhotoIndex();
    } else {
      $this->view->getPhotoIndex = $photo->getCollectionIndex();
    }

    $this->view->canComment = $canComment = $album->authorization()->isAllowed($viewer, 'comment');
    $params = array();
    if (null !== ($type = $this->_getParam('type', null)))
      $params['type'] = $type;
    if (0 != ($count = (int) $this->_getParam('count', 0)))
      $params['count'] = $count;
    $this->view->type_count = $count;
    $params['offset'] = 0;
    if (0 != ($offset = (int) $this->_getParam('offset', 0)))
      $params['offset'] = $offset;
    if (0 != ($owner_id = (int) $this->_getParam('owner_id', 0)))
      $params['owner_id'] = $owner_id;
    if (0 != ($featured = (int) $this->_getParam('featured', 0)))
      $params['featured'] = $featured;
    if (0 != ($category_id = (int) $this->_getParam('category_id', 0)))
      $params['category_id'] = $category_id;
    if (0 != ($subcategory_id = (int) $this->_getParam('subcategory_id', 0)))
      $params['subcategory_id'] = $subcategory_id;
    if (null != ($interval = $this->_getParam('interval', null)))
      $params['interval'] = $interval;
    if (0 != ($latitude = $this->_getParam('latitude', 0)))
      $params['latitude'] = $latitude;
    if (0 != ($longitude = $this->_getParam('longitude', 0)))
      $params['longitude'] = $longitude;
    if (0 != ($defaultLocationDistance = (int) $this->_getParam('defaultLocationDistance', 0)))
      $params['defaultLocationDistance'] = $defaultLocationDistance;

    if (!empty($type)) {
      if (empty($offset)) {
        $params['offset'] = $offset = Engine_Api::_()->getDbTable('photos', 'sitealbum')->getCollectibleIndex($photo, $params);
      }

      $display = $this->_getParam('title', null);
      if (empty($display)) {
        $concatePhotos = true;
        switch ($type) {
          case 'featured':
            $display = 'Featured';
            break;
          case 'tagged':
          case 'yourphotos':
          case 'strip_view':
          case 'strip_like':
          case 'strip_comment':
          case 'strip_rating':
          case 'strip_random':
          case 'strip_modified':
          case 'strip_creation':
            $concatePhotos = false;
            if (!empty($owner_id)) {
              $owner = Engine_Api::_()->getItem('user', $owner_id);
              $display = $owner->__toString() . '\'s';
              $display = $this->view->translate('%s Photos', $display);
            }
            break;
          case 'like_count':
            $display = 'Most Liked';
            break;
          case 'view_count':
            $display = 'Most Viewed';
            break;
          case 'rating':
            $display = 'Most Rated';
            break;
          case 'comment_count':
            $display = 'Most Commented';
            break;
          case 'random':
            $display = 'Random';
            break;
          case 'creation_date':
          case 'modified_date':
            $display = 'Recent';
            break;
        }
        if ($concatePhotos)
          $display .=" Photos";
      }
      if ($type !== "strip_modified" && $type !== "strip_view" && $type !== "strip_like" && $type !== "strip_comment" && $type !== "strip_rating" && $type !== "strip_random" && $type !== "strip_creation" && $type !== "tagged" && $type !== "yourphotos")
        $params['title'] = $display;
      $this->view->displayTitle = $display;
    }else {
      $count = $album->photos_count;
      $offset = $this->view->getPhotoIndex;
      if (!empty($offset))
        $params['offset'] = $offset;
    }

    if ($offset >= $count)
      $params['offset'] -=$count;
    elseif ($offset < 0)
      $params['offset'] +=$count;

    if (($params['offset'] - 1) < 0) {
      $this->view->PrevOffset = $count - 1;
    } else {
      $this->view->PrevOffset = $params['offset'] - 1;
    }
    if (($params['offset'] + 1) >= $count) {
      $this->view->NextOffset = 0;
    } else {
      $this->view->NextOffset = $params['offset'] + 1;
    }

    $this->view->params = $params;
    $this->view->prevPhoto = Engine_Api::_()->sitealbum()->getPrevPhoto($photo, $params);
    $this->view->nextPhoto = Engine_Api::_()->sitealbum()->getNextPhoto($photo, $params);

    if($this->view->prevPhoto)
    $this->view->prevPhotoUrl = $this->view->prevPhoto->getPhotoUrl();

    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);

    $this->view->allowView = $this->view->canMakeFeatured = false;
    if (!empty($viewer_id) && ($viewer->level_id == 1 || $viewer->level_id == 2)) {
      $this->view->canMakeFeatured = true;
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
    }
    $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);

    // RATING WORK
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {

      $this->view->update_permission = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbumrating.update', 1);
      if (!empty($viewer_id)) {
        $this->view->level_id = $viewer->level_id;
      } else {
        $this->view->level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
      }

      $allowRating = Engine_Api::_()->authorization()->getPermission($this->view->level_id, 'album', 'rate');
      if (!empty($viewer_id) && !empty($allowRating)) {
        $this->view->canRate = 1;
      } else {
        $this->view->canRate = 0;
      }

      $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitealbum');
      $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $photo->getIdentity(), 'resource_type' => 'album_photo'));
      $this->view->rated = $ratingTable->checkRated(array('resource_id' => $photo->getIdentity(), 'resource_type' => 'album_photo'));
    }

    // Get albums
    $albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
            ->from($albumTable, array('album_id', 'title', 'type'))
            ->where('owner_type = ?', 'user')
            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
            ->query()
            ->fetchAll();

    if ($album->type == null) {
      if (count($myAlbums) > 1)
        $this->view->movetotheralbum = 1;
      if ($album->photo_id != $photo->getIdentity())
        $this->view->makeAlbumCover = 1;
    }

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sea.lightbox.fixedwindow', 1)) {
      $this->renderScript('photo/light-box-view-fix-window.tpl');
    } else {

      $this->renderScript('photo/light-box-view-without-fix-window.tpl');
    }
  }

// ACTION FOR EDIT THE DESCRIPTION OF THE PHOTOS
  public function editDescriptionAction() {
    //GET TEXT
    $text = $this->_getParam('text_string');

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->core()->getSubject();
    // GET DB
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->getDisplayPhotos();
    $db->beginTransaction();
    try {
      //SAVE VALUE
      $value['description'] = $text;
      $photo->setFromArray($value);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR MAKING FEATURED PHOTO
  public function featuredAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    $photo = Engine_Api::_()->core()->getSubject();
    $photo->featured = !$photo->featured;
    $photo->save();
    exit(0);
  }

  //ACTION FOR ADDING PHOTO OF THE DAY
  public function addPhotoOfDayAction() {
    //FORM GENERATION
    $photo = Engine_Api::_()->core()->getSubject();

    // CHECK FOR ONLY ADMIN CAN ADD PHOTO OF THE DAY
    $album = $photo->getAlbum();
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $allowView = $addPhotoOfTheDay = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $addPhotoOfTheDay = true;
      $auth = Engine_Api::_()->authorization()->context;
      $allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
    }

    if (!$addPhotoOfTheDay || !$allowView) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $form = $this->view->form = new Sitealbum_Form_ItemOfDayday();
    $form->setTitle('Photo of the Day')
            ->setDescription('Select a start date and end date below.This photo will be displayed as "Photo of the Day" for this duration.If more than one photos of the day are found for a date then randomly one will be displayed.');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();
      $values["resource_id"] = $photo->getIdentity();
//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum');
        $row = $table->getItem('album', $values["resource_id"]);

        if (empty($row)) {
          $row = $table->createRow();
        }
        $values = array_merge($values, array('resource_type' => 'album_photo'));

        if ($values['start_date'] > $values['end_date'])
          $values['end_date'] = $values['start_date'];
        $row->setFromArray($values);
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                  'layout' => 'default-simple',
                  'smoothboxClose' => true,
      ));
    }
  }

  public function getDisplayPhotos() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $code = $settings->getSetting('sitealbum.photo.display');
    $se_lightbox = $settings->getSetting('seaocore.display.lightbox', 1);
    $value = 0;
    $photoName = $settings->getSetting('sitealbum.photo.name');
    $photoName .=$photoName;
    $photoName .='albumsite';
    for ($i = 0; $i < strlen($photoName); $i++) {
      $value += ord($photoName[$i]);
    }
    $status = (int) ($code == $value);
    $settings->setSetting('sitealbum_viewerphoto', $value);
    $settings->setSetting('sitealbum_viewertype', $status);
    $settings->setSetting('sitealbum_featuredalbum', $status);
    $settings->setSetting('seaocore_display_lightbox', ($status && $se_lightbox));
  }

  //ACTION FOR EDIT THE TITLE OF THE PHOTOS
  public function editTitleAction() {
    //GET TEXT
    $text = $this->_getParam('text_string');
    $photo = Engine_Api::_()->core()->getSubject();
    //GET DB
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUE
      $value['title'] = $text;
      $photo->setFromArray($value);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR DISPLAYING ALL PHOTO ON CLICKING ON VIEW ALL
  public function getAllPhotosAction() {
    $album_id = (int) $this->_getParam('album_id');
    $album = Engine_Api::_()->getItem('album', $album_id);
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
          'album' => $album,
      ));
    } else {
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    }
    $paginator->setItemCountPerPage(10000);
  }

  //ACTION FOR ROTATING PHOTO
  public function rotateAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    //FOR LIGHTBOX VIEW
    //if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
     // return;

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    $photo = Engine_Api::_()->core()->getSubject('album_photo');

    $angle = (int) $this->_getParam('angle', 90);
    if (!$angle || !($angle % 360)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must not be empty');
      return;
    }
    if (!in_array((int) $angle, array(90, 270))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must be 90 or 270');
      return;
    }

    // Get file
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    // Pull photo to a temporary file
    $tmpFile = $file->temporary();

    // Operate on the file
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->rotate($angle)
            ->write()
            ->destroy()
    ;

    // Set the photo
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR FLIPING PHOTO
  public function flipAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    $photo = Engine_Api::_()->core()->getSubject('album_photo');

    $direction = $this->_getParam('direction');
    if (!in_array($direction, array('vertical', 'horizontal'))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid direction');
      return;
    }

    // Get file
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    // Pull photo to a temporary file
    $tmpFile = $file->temporary();

    // Operate on the file
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->flip($direction != 'vertical')
            ->write()
            ->destroy()
    ;

    // Set the photo
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  public function cropAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    $photo = Engine_Api::_()->core()->getSubject('album_photo');

    $x = (int) $this->_getParam('x', 0);
    $y = (int) $this->_getParam('y', 0);
    $w = (int) $this->_getParam('w', 0);
    $h = (int) $this->_getParam('h', 0);

    // Get file
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    // Pull photo to a temporary file
    $tmpFile = $file->temporary();

    // Open the file
    $image = Engine_Image::factory();
    $image->open($tmpFile);

    $curH = $image->getHeight();
    $curW = $image->getWidth();

    // Check the parameters
    if ($x < 0 ||
            $y < 0 ||
            $w < 0 ||
            $h < 0 ||
            $x + $w > $curW ||
            $y + $h > $curH) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid size');
      return;
    }

    $image->open($tmpFile)
            ->crop($x, $y, $w, $h)
            ->write()
            ->destroy()
    ;

    // Set the photo
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR RATING THE PHOTO
  public function rateAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'rate');
    if (empty($viewer_id) || empty($allowRating))
      return;

    $rating = $this->_getParam('rating');
    $photo_id = $this->_getParam('photo_id');

    $table = Engine_Api::_()->getDbtable('ratings', 'sitealbum');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $table->setRating($photo_id, 'album_photo', $rating);

      $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
      $photo->rating = $table->getRating($photo->getIdentity(), 'album_photo');
      $photo->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $total = $table->ratingCount(array('resource_id' => $photo->getIdentity(), 'resource_type' => 'album_photo'));

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  // ACTION FOR GET LINK WORK
  public function getLinkAction() {
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;

    $this->view->subject = $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));

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
    $encode_subjectId = Engine_Api::_()->sitealbum()->getDecodeToEncode('' . $subject->getIdentity() . '');

    $this->view->url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref() . '/set/' . $encode_subjectId;
    $this->view->subjectType = $subject->getType();
  }

  //ACTION FOR COMPOSING A MESSEGE TO SEND A PHOTO
  public function composeAction() {
    $this->_helper->layout->setLayout('default-simple');

    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    // Make form
    $this->view->form = $form = new Sitealbum_Form_Compose();

    //SET URL IN BODY OF MESSAGE
    $this->view->subject = $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
    $this->view->subjectType = $subject->getType();

    $encode_subjectId = Engine_Api::_()->sitealbum()->getDecodeToEncode('' . $subject->getIdentity() . '');

    $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref() . '/set/' . $encode_subjectId;
    // Build
    $isPopulated = false;
    $form->body->setValue($url);
    $this->view->isPopulated = $isPopulated;

    // Get config
    $this->view->maxRecipients = $maxRecipients = 10;


    // Check method/data
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
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
      // clean the recipients for repeating ids
      // this can happen if recipient is selected and then a friend list is selected
      $recipients = array_unique($recipients);
      // Slice down to 10
      $recipients = array_slice($recipients, 0, $maxRecipients);
      // Get user objects
      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
      // Validate friends
      if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
        foreach ($recipientsUsers as &$recipientUser) {
          if (!$viewer->membership()->isMember($recipientUser)) {
            return $form->addError('One of the members specified is not in your friends list.');
          }
        }
      }

      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
              $viewer, $recipients, $values['title'], $values['body'], $attachment
      );

      // Send notifications
      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $user, $viewer, $conversation, 'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($this->getRequest()->getParam('format') == 'smoothbox') {
      return $this->_forward('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
                  'smoothboxClose' => true,
      ));
    } else {
      return $this->_forward('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
                  'smoothboxClose' => true,
                  'redirect' => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
      ));
    }
  }

  //ACTION FOR SENDING A PHOTO BY EMAIL
  public function tellAFriendAction() {
    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET PHOTO ID AND PHOTO OBJECT
    $photo_id = $this->_getParam('photo');
    $photo = Engine_Api::_()->getItem('album_photo', $photo_id);

    if (!$photo->authorization()->isAllowed(null, 'edit')) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!$photo->authorization()->isAllowed(null, 'view')) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $encode_photoId = Engine_Api::_()->sitealbum()->getDecodeToEncode('' . $photo->getIdentity() . '');
    $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $photo->getHref() . '/set/' . $encode_photoId;
    if (empty($photo))
      return $this->_forwardCustom('notfound', 'error', 'core');

    //FORM GENERATION
    $this->view->form = $form = new Sitealbum_Form_TellAFriend();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $form->populate($value);
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = explode(',', $values['sitealbum_reciver_emails']);
      $sender_email = $values['sitealbum_sender_email'];
      $sender_name = $viewer->getTitle();

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);
      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
      }

      $message = $values['sitealbum_message'];
      $heading = ucfirst($photo->getTitle());
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEALBUM_SEND_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'photo_title' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => $url,
          'sender_name' => $sender_name,
          'sender_email' => $sender_email,
          'queue' => true
      ));

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
    }
  }

  // ACTION FOR MAKE ALBUM MAIN PHOTO
  public Function makeAlbumCoverAction() {
    // Get photo
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));
    $album = Engine_Api::_()->getItemByGuid($this->_getParam('album'));

    if (!$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo->photo_id)) {
      return $this->_forward('requiresubject', 'error', 'core');
    }

    if (!$photo->authorization()->isAllowed(null, 'edit')) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Make form
    $this->view->form = $form = new Sitealbum_Form_MakeAlbumCover();
    $this->view->photo = $photo;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $table = $album->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {

      if (!empty($photo)) {
        $album->photo_id = $photo->photo_id;
        $album->save();
      }

      $db->commit();
    }
    // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Set as Album Main Photo')),
                'smoothboxClose' => true,
    ));
  }

  //ACTION FOR MOVING A PHOTO FROM ONE ALBUM INTO ANOTHER ALBUM
  public Function moveToOtherAlbumAction() {
    // Get photo
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));
    $album = Engine_Api::_()->getItemByGuid($this->_getParam('album'));

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo->photo_id)) {
      return $this->_forward('requiresubject', 'error', 'core');
    }

    if (!$photo->authorization()->isAllowed(null, 'edit')) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Make form
    $this->view->form = $form = new Sitealbum_Form_MoveToOtherAlbum(array('item' => $album));
    $this->view->photo = $photo;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = $album->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      if (!empty($values['move'])) {
        $nextPhoto = $photo->getNextPhoto();
        $photo->album_id = $values['move'];
        $photo->save();

        if (($viewer->level_id == 1) && !$photo->getOwner()->isSelf($viewer)) {
          $photo->owner_id = $viewer->getIdentity();
          $photo->save();
        }

        // Change album cover if necessary
        if (($nextPhoto instanceof Sitealbum_Model_Photo) &&
                (int) $album->photo_id == (int) $photo->getIdentity()) {
          $album->photo_id = $nextPhoto->getIdentity();
          $album->save();
        }

        // Update photos_count of both albums
        $album->photos_count = $album->photos_count - 1;
        $album->save();

        $movingIntoAlbum = Engine_Api::_()->getItem('album', $values['move']);
        $movingIntoAlbum->photos_count = $movingIntoAlbum->photos_count + 1;
        $movingIntoAlbum->save();

        // Remove activity attachments for this photo
        Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
      }
      $db->commit();
    }
    // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photos has been successfully moved to %s', $movingIntoAlbum->getTitle())),
                'smoothboxClose' => true,
    ));
  }

}