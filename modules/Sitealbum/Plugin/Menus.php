<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Plugin_Menus {

  public function canCreateAlbums() {

    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    // Must be able to create albums
    if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
      return false;
    }

    return true;
  }

  public function canCreateBadge() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    // Badge is Enable or Not
    $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.badge', 1);
    if (empty($badge_enable)) {
      return false;
    }
    return true;
  }

  public function canViewAlbums() {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be able to view albums
    if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'view')) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_SitealbumProfileAdd() { 

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    $mine = true;
    if (!$subject->getOwner()->isSelf($viewer)) {
      $mine = false;
    }

    if (!$mine && !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => $view->translate('Add Photos'),
        'class' => 'data_SmoothboxSEAOClass seao_smoothbox',
        'route' => 'sitealbum_general',
        'params' => array(
            'action' => 'upload',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileManage() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => $view->translate('Manage Photos'),
        'class' => '',
        'route' => 'sitealbum_specific',
        'params' => array(
            'action' => 'editphotos',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileEdit() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => $view->translate('Edit'),
        'class' => '',
        'route' => 'sitealbum_specific',
        'params' => array(
            'action' => 'edit',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileDelete() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    $mine = true;
    if (!$subject->getOwner()->isSelf($viewer)) {
      $mine = false;
    }

    if (!$mine && !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'delete')) {
      return false;
    }

    return array(
        'label' => $view->translate('Delete Album'),
        'route' => 'sitealbum_specific',
        'class' => 'smoothbox',
        'params' => array(
            'action' => 'delete',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileShare() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    $mine = true;
    if (!$subject->getOwner()->isSelf($viewer)) {
      $mine = false;
    }

    if (!$mine && !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    // Badge is Enable or Not
    $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.badge', 1);
    if (empty($badge_enable)) {
      return false;
    }

    return array(
        'label' => $view->translate('Share via Badge'),
        'route' => 'sitealbum_badge',
        'class' => '',
        'params' => array(
            'action' => 'create',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileMakealbumoftheday() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    if ($viewer->level_id != 1) {
      return false;
    }

    // Must be able to view albums
    if (!Engine_Api::_()->authorization()->isAllowed('album', 'everyone', 'view') || !Engine_Api::_()->authorization()->isAllowed('album', 'registered', 'view')) {
      return false;
    }

    return array(
        'label' => $view->translate('Make Album of the Day'),
        'route' => 'sitealbum_specific',
        'class' => 'smoothbox item_icon_album',
        'params' => array(
            'action' => 'add-album-of-day',
            'album_id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileGetlink() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;


    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => $view->translate('Get Link'),
        'route' => 'sitealbum_extended',
        'class' => 'smoothbox sitealbum_icon_link',
        'params' => array(
            'action' => 'get-link',
            'subject' => $subject->getGuid(),
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileEditlocation() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    if (!$subject->authorization()->isAllowed($viewer, 'edit') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) {
      return false;
    }

    return array(
        'label' => $view->translate('Edit Location'),
        'route' => 'sitealbum_general',
        'class' => 'smoothbox',
        'params' => array(
            'action' => 'edit-location',
            'subject' => $subject->getGuid(),
        )
    );
  }

  public function onMenuInitialize_SitealbumProfileSuggesttofriend() {

    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    $suggestionPluginStatus = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    if (!empty($suggestionPluginStatus)) {
      $flag = false;
      if (!empty($suggestionPluginStatus)) {
        $linkShouldShow = Engine_Api::_()->suggestion()->getModSettings('album', 'link');

        $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
        $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
        if ($versionStatus >= 0) {
          $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('album', $subject->getIdentity(), 1,null);
          if (!empty($modContentObj)) {
            $contentCreatePopup = @COUNT($modContentObj);
          }
        }

        if (!empty($linkShouldShow) && !empty($contentCreatePopup)) {
          $flag = true;
        }
      }
      // END WORK FOR SUGGESTION
    }

    if (empty($flag)) {
      return false;
    }

    return array(
        'label' => $view->translate('Suggest to Friends'),
        'route' => 'default',
        'class' => 'smoothbox icon_page_friend_suggestion',
        'params' => array(
            'module' => 'suggestion',
            'controller' => 'index',
            'action' => 'switch-popup',
            'modName' => 'album',
            'modContentId' => $subject->getIdentity()
        )
    );
  }

  //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_SitealbumPhotoEdit($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'album_extended',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'edit',
           'photo_id' => $subject->photo_id
        )
    );
  }

 //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_SitealbumPhotoDelete($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) {
      return false;
    }

    return array(
        'label' => 'Delete',
        'route' => 'album_extended',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'delete',
           'photo_id' => $subject->photo_id
        )
    );
  }

  public function onMenuInitialize_SitealbumPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
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

  public function onMenuInitialize_SitealbumPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
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

  public function onMenuInitialize_SitealbumPhotoMakeProfilePhoto($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }

    return array(
        'label' => 'Make Profile Photo',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'route' => 'user_extended',
        'params' => array(
            'module' => 'user',
            'controller' => 'edit',
            'action' => 'external-photo',
            'photo' => $subject->getGuid(),
        )
    );
  }
  
  public function onMenuInitialize_SitealbumPhotoLocation() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::isRegistered('Zend_View') ?
            Zend_Registry::get('Zend_View') : null;

    if (!$subject->authorization()->isAllowed($viewer, 'edit') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) {
      return false;
    }

    return array(
        'label' => $view->translate('Edit Location'),
        'route' => 'default',
        'class' => 'smoothbox',
        'params' => array(
            'module' => 'sitealbum',
            'controller' => 'index',
            'action' => 'edit-location',
            'subject' => $subject->getGuid(),
        )
    );
  }
}