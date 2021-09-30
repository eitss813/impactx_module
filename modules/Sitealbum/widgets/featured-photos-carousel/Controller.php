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
class Sitealbum_Widget_FeaturedPhotosCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('contentpage', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
      }
    }

    $sitealbum_api = Engine_Api::_()->sitealbum();
    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    $param = array();
    $this->view->featured = $param['featured'] = $this->_getParam('featured', 0);
    $this->view->category_id = $param['category_id'] = $this->_getParam('category_id');
    $this->view->subcategory_id = $param['subcategory_id'] = $this->_getParam('subcategory_id');
    $this->view->vertical = $param['viewType'] = $this->_getParam('viewType', 0);
    $this->view->showPagination = $param['showPagination'] = $this->_getParam('showPagination', 1);
    $this->view->interval = $param['interval'] = $this->_getParam('interval', 300);
    $this->view->blockHeight = $param['blockHeight'] = $this->_getParam('blockHeight', 250);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 200);
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 200);
    $this->view->photoTitleTruncation = $param['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 22);
    $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("photoTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'comment');
    $this->view->limit = $param['limit'] = $this->_getParam('itemCount', 5);

    $this->view->orderby = '';
    switch ($param['popularType']) {
      case 'view':
        $this->view->orderby = $param['orderby'] = 'view_count';
        break;
      case 'comment':
        $this->view->orderby = $param['orderby'] = 'comment_count';
        break;
      case 'like':
        $this->view->orderby = $param['orderby'] = 'like_count';
        break;
      case 'rating':
        $this->view->orderby = $param['orderby'] = 'rating';
        break;
      case 'creation':
        $this->view->orderby = $param['orderby'] = 'creation_date';
        break;
      case 'modified':
        $this->view->orderby = $param['orderby'] = 'modified_date';
        break;
      case 'random':
        $this->view->orderby = $param['orderby'] = 'random';
        break;
    }

    $this->view->detactLocation = $param['detactLocation'] = $this->_getParam('detactLocation', 0);

    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }

    $this->view->defaultLocationDistance = 1000;
    $this->view->latitude = 0;
    $this->view->longitude = 0;

    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $param['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $this->view->latitude = $param['latitude'] = $this->_getParam('latitude', 0);
      $this->view->longitude = $param['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->param = $param;
    if (!$this->view->is_ajax_load)
      return;
    // List List featured
    $this->view->photos = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($param);

    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    // CAROUSEL SETTINGS
    $this->view->totalCount = $count = $paginator->getTotalItemCount();

    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');

    $this->view->showLightBox = $sitealbum_api->showLightBoxPhoto();
    if ($this->view->showLightBox) {
      if ($param['detactLocation'])
        $this->view->params = $params = array('type' => $param['orderby'], 'featured' => $param['featured'], 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'latitude' => $param['latitude'], 'longitude' => $param['longitude'], 'defaultLocationDistance' => $param['defaultLocationDistance'], 'count' => $count, 'title' => $this->_getParam('title', 'Featured Photos'));
      else
        $this->view->params = $params = array('type' => $param['orderby'], 'featured' => $param['featured'], 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'count' => $count, 'title' => $this->_getParam('title', 'Featured Photos'));
    }
  }

}