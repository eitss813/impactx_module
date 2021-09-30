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
class Sitealbum_Widget_FriendsPhotosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    // Check if friendship is allowed in the network
    $eligible = (int) $coreApi->getSetting('user.friends.eligible', 2);
    $photoType = $coreApi->getSetting('sitealbum.phototype', null);
    $friendPhotoView = $coreApi->getSetting('sitealbum.coreview', null);

    if (!$eligible || empty($viewer_id) || empty($photoType) || empty($friendPhotoView)) {
      return $this->setNoRender();
    }

    // get viewer friends
    $friendIds = $viewer->membership()->getMembershipsOfIds();
    if (empty($friendIds)) {
      return $this->setNoRender();
    }

    $params = array();
    $params['itemCountPhoto'] = $this->_getParam('itemCountPhoto', 2);
    $params['category_id'] = $this->_getParam('category_id');
    $params['subcategory_id'] = $this->_getParam('subcategory_id');
    $params['featured'] = $this->_getParam('featured', 0);

    $this->view->friendsPhoto = $friendsPhoto = Engine_Api::_()->getDbTable('photos', 'sitealbum')->getFriendsPhotos($friendIds, $params);
    $count = $friendsPhoto->count();
    if ($count <= 0) {
      return $this->setNoRender();
    }

    $this->view->photoInfo = $params['photoInfo'] = $this->_getParam('photoInfo', array("photoTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->photoHeight = $params['photoHeight'] = $this->_getParam('photoHeight', 200);
    $this->view->photoWidth = $params['photoWidth'] = $this->_getParam('photoWidth', 200);
    $this->view->photoTitleTruncation = $params['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 16);
    $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->params = $params;
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $this->view->count = $count;
    $this->view->is_ajax = $this->_getParam('isajax', '');
    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');
  }

}