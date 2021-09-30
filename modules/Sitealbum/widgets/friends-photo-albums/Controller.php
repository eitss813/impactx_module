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
class Sitealbum_Widget_FriendsPhotoAlbumsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    $sitealbum_friendphoto = Zend_Registry::isRegistered('sitealbum_friendphoto') ? Zend_Registry::get('sitealbum_friendphoto') : null;
    $photoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.phototype', null);

    if (empty($sitealbum_friendphoto) || !$eligible || empty($viewer_id) || empty($photoType)) {
      return $this->setNoRender();
    }

    // get viewer friends
    $friendIds = $viewer->membership()->getMembershipsOfIds();
    if (empty($friendIds) || empty($photoType)) {
      return $this->setNoRender();
    }
    $params = array();
    $params['category_id'] = $this->_getParam('category_id');
    $params['subcategory_id'] = $this->_getParam('subcategory_id');
    $params['featured'] = $this->_getParam('featured', 0);
    $params['itemCountAlbum'] = $this->_getParam('itemCountAlbum', 2);
    $params['itemCountPhoto'] = $this->_getParam('itemCountPhoto', 2);

    $this->view->friendsPhoto = $friendsPhoto = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getFriendsPhotoAlbums($friendIds, $params);

    $count = count($friendsPhoto);
    if ($count <= 0) {
      return $this->setNoRender();
    }
    $this->view->albumInfo = $params['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->albumTitleTruncation = $params['albumTitleTruncation'] = $this->_getParam('albumTitleTruncation', 16);
    $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->params = $params;
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $this->view->count = $count;
    $this->view->is_ajax = $this->_getParam('isajax', '');
  }

}