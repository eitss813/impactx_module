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
class Sitealbum_Widget_AlbumOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->albumOfDay = $albumOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum')->getAlbumOfDay();

    if (empty($albumOfDay)) {
      return $this->setNoRender();
    }

    $sitealbum_albumoftheday = Zend_Registry::isRegistered('sitealbum_albumoftheday') ? Zend_Registry::get('sitealbum_albumoftheday') : null;
    $canView = $albumOfDay->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'view');
    if (empty($sitealbum_albumoftheday) || empty($canView)) {
      return $this->setNoRender();
    }
    
    //GET PARAMS
    $this->view->photoHeight = $this->_getParam('photoHeight', 255);
    $this->view->photoWidth = $this->_getParam('photoWidth', 237);
    $this->view->infoOnHover = $this->_getParam('infoOnHover', 1);
    $this->view->albumTitleTruncation = $this->_getParam('albumTitleTruncation', 100);
    $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
    $this->view->normalPhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.last.photoid');
    $this->view->albumInfo = $this->_getParam('albumInfo', array("ratingStar","totalPhotos"));
  }

}