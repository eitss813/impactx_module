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
class Sitealbum_Widget_PhotoOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->photoOfDay = $photoOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum')->getPhotoOfDay();

    if (empty($photoOfDay)) {
      return $this->setNoRender();
    }
    $parent = $photoOfDay->getParent();
    $canView = $parent->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'view');
    if (empty($canView)) {
      return $this->setNoRender();
    }

    //GET PARAMS
    $this->view->photoHeight = $this->_getParam('photoHeight', 200);
    $this->view->photoWidth = $this->_getParam('photoWidth', 200);
    $this->view->photoTitleTruncation = $this->_getParam('photoTitleTruncation', 22);
    $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
    $this->view->photoInfo = $this->_getParam('photoInfo', array("photoTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->normalPhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.last.photoid');

    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
  }

}