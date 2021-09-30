<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Plugin_Menus {

 public function canViewAlbums() {

    $isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 0);
    if (empty($isActive)) {
      return false;
    }
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.album.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('albums', 'sitepage');
    $rName = $table->info('name');
    $table_pages = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName_pages = $table_pages->info('name');
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName_pages, array('photo_id', 'title as sitepage_title'))
            ->join($rName, $rName . '.page_id = ' . $rName_pages . '.page_id')
            ->where($rName . '.search = ?', 1);

    $select = $select
            ->where($rName_pages . '.closed = ?', '0')
            ->where($rName_pages . '.approved = ?', '1')
            ->where($rName_pages . '.search = ?', '1')
            ->where($rName_pages . '.declined = ?', '0')
            ->where($rName_pages . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($rName_pages . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }

  //SITEMOBILE PAGE ALBUM MENUS
  public function onMenuInitialize_SitepagealbumViewAlbums($row) {

    if(Engine_Api::_()->seaocore()->isSitemobileApp()){
     return false; 
    }
    // $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

    if (empty($album))
      return false;

    $page_id = $album->page_id;
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $page_id;
    //GET ALBUM COUNT
    $album_count = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);

    //CHECKS
    if ($album_count <= 1) {
      return false;
    }

    return array(
        'label' => 'View Albums',
        'class' => 'ui-btn-action',
        'route' => 'sitepage_albumphoto_general',
        'params' => array(
            'action' => 'view-album',
            'album_id' => $subject->getIdentity(),
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagealbumAdd($row) {

    //$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $upload_photo = 0;
    $engineApiSitepage = Engine_Api::_()->sitepage();
    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

    if (empty($album))
      return false;

    $page_id = $album->page_id;
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'spcreate');
    if (empty($isManageAdmin)) {
      $canCreatePhoto = 0;
    } else {
      $canCreatePhoto = 1;
    }

    if ($canCreatePhoto == 1 && ($engineApiSitepage->isPageOwner($sitepage) || $album->default_value == 1)) {
      $upload_photo = 1;
    }

    if (empty($upload_photo)) {
      return false;
    }

    return array(
        'label' => 'Add More Photos',
        'class' => 'ui-btn-action',
        'route' => 'sitepage_photoalbumupload',
        'params' => array(
            'action' => 'upload',
            'album_id' => $subject->getIdentity(),
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagealbumEdit() {

    //$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
    if (empty($album))
      return false;
    $page_id = $album->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    $engineApiSitepage = Engine_Api::_()->sitepage();

    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($can_edit)) {
      return false;
    }
    return array(
        'label' => 'Edit Album',        
        'route' => 'sitepage_albumphoto_general',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'action' => 'edit',
            'album_id' => $subject->getIdentity(),
            'page_id' => $page_id
        )
    );
  }

  public function onMenuInitialize_SitepagealbumDelete() {

    // $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
    if (empty($album))
      return false;
    $page_id = $album->page_id;
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    $engineApiSitepage = Engine_Api::_()->sitepage();

    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($can_edit)) {
      return false;
    }

    //SET DEFAULT ALBUM VALUE
    $default_value = $album->default_value;
    if ($default_value == 1) {
      return false;
    }

    return array(
        'label' => 'Delete Album',
        'route' => 'sitepage_albumphoto_general',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'delete',
            'album_id' => $subject->getIdentity(),
            'page_id' => $page_id
        )
    );
  }

  //PHOTO VIEW PAGE OPTIONS

  public function onMenuInitialize_SitepagealbumPhotoEdit($row) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET PAGE ID
    $page_id = $subject->page_id;

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canEdit = 1;
    } else {
      $canEdit = 0;
    }
    //CHECK FOR EDIT
    if (empty($canEdit)) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'sitepage_imagephoto_specific',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'action' => 'photo-edit',
            'photo_id' => $subject->getIdentity(),
            'album_id' => $album_id,
            'page_id' => $page_id,
            //'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagealbumPhotoDelete($row) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET PAGE ID
    $page_id = $subject->page_id;

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canDelete = 1;
    } else {
      $canDelete = 0;
    }
    //CHECK FOR EDIT
    if (empty($canDelete)) {
      return false;
    }
    return array(
        'label' => 'Delete',
        'route' => 'sitepage_imagephoto_specific',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'remove',
            'photo_id' => $subject->getIdentity(),
            'album_id' => $album_id,
            'page_id' => $page_id,
        )
    );
  }

  public function onMenuInitialize_SitepagealbumPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    if (!SEA_PHOTOLIGHTBOX_SHARE) {
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitepagealbumPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    if (!SEA_PHOTOLIGHTBOX_REPORT) {
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

// $this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO
  public function onMenuInitialize_SitepagealbumPhotoProfile($row) {
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET PAGE ID
    $page_id = $subject->page_id;

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canEdit = 1;
    } else {
      $canEdit = 0;
    }
    //CHECK FOR EDIT
    if (empty($canEdit) || !SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO) {
      return false;
    }
    return array(
        'label' => 'Make Page Profile Photo',
        'route' => 'sitepage_imagephoto_specific',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'sitepage',
            'controller' => 'photo',
            'action' => 'make-page-profile-photo',
            'photo' => $subject->getGuid(),
            'page_id' => $page_id,
        )
    );
  }

}
?>