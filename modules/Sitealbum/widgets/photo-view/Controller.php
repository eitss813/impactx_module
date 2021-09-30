<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_PhotoViewController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject('album_photo')) {
      return $this->setNoRender();
    }

    //GET REQUEST ISAJAX OR NOT
    $this->view->isajax = (int) Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax', 0);

    $this->view->showbuttons = $this->_getParam('showbuttons', 1);
    $this->view->viewDisplayHR = $this->_getParam('viewDisplayHR', 0);

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();

    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $this->view->album = $album = $photo->getAlbum();
    } else {
      $this->view->album = $album = $photo->getCollection();
    }

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;
    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canComment = $canComment = $album->authorization()->isAllowed($viewer, 'comment');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);
    $sitealbum_photoview = Zend_Registry::isRegistered('sitealbum_photoview') ? Zend_Registry::get('sitealbum_photoview') : null;

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

    $sitealbumCoreview = $coreSettings->getSetting('sitealbum.coreview', null);
    if (empty($sitealbum_photoview) || empty($sitealbumCoreview)) {
      return $this->setNoRender();
    }

    $this->view->enablePinit = $coreSettings->getSetting('seaocore.photo.pinit', 0);

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
  }

}