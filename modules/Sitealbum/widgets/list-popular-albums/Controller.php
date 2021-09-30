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
class Sitealbum_Widget_ListPopularAlbumsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
      }
    }

    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    $params = array();
    $this->view->description = $this->_getParam('description','');
    $this->view->photoHeight = $params['photoHeight'] = $this->_getParam('photoHeight', 195);
    $this->view->photoWidth = $params['photoWidth'] = $this->_getParam('photoWidth', 195);
    $this->view->infoOnHover = $params['infoOnHover'] = $this->_getParam('infoOnHover', 1);
    $this->view->albumTitleTruncation = $params['albumTitleTruncation'] = $this->_getParam('albumTitleTruncation', 16);
    $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->albumInfo = $params['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $params['category_id'] = $this->_getParam('category_id');
    $params['subcategory_id'] = $this->_getParam('subcategory_id');

    $params['popularType'] = $this->_getParam('popularType', 'comment_count');
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['featured'] = $this->_getParam('featured', 0);

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    switch ($params['popularType']) {
      case 'view':
        $params['orderby'] = 'view_count';
        break;
      case 'comment':
        $params['orderby'] = 'comment_count';
        break;
      case 'like':
        $params['orderby'] = 'like_count';
        break;
      case 'rating':
        $params['orderby'] = 'rating';
        break;
      case 'creation':
        $params['orderby'] = 'creation_date';
        break;
      case 'photos':
        $params['orderby'] = 'photos_count';
        break;
      case 'modified':
        $params['orderby'] = 'modified_date';
        break;
      case 'random':
        $params['orderby'] = 'random';
        break;
    }

    $this->view->params = $params;

    if (!$this->view->is_ajax_load)
      return;

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->albumBySettings($params);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $popularAlbum = $coreApi->getSetting('sitealbum.badgeviewer', null);

    // Do not render if nothing to show
    if (($paginator->getTotalItemCount() <= 0) || empty($popularAlbum)) {
      return $this->setNoRender();
    }

    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');
  }

}