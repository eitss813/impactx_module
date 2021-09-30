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
class Sitealbum_Widget_ListPopularPhotosController extends Engine_Content_Widget_Abstract {

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

    $param = array();
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 200);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 200);
    $this->view->photoTitleTruncation = $param['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 22);
    $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("photoTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'comment');
    $this->view->showPhotosInJustifiedView = $param['showPhotosInJustifiedView'] = $this->_getParam('showPhotosInJustifiedView', 0); 
    $this->view->maxRowHeight = $param['maxRowHeight'] = $this->_getParam('maxRowHeight',0); 
    $this->view->rowHeight = $param['rowHeight'] = $this->_getParam('rowHeight',205);   
    $this->view->margin = $param['margin'] = $this->_getParam('margin',5);  
    $this->view->lastRow = $param['lastRow'] = $this->_getParam('lastRow', 'nojustify');  
    $param['interval'] = $interval = $this->_getParam('interval', 'overall');
    $param['featured'] = $this->_getParam('featured', 0);
    $param['category_id'] = $this->_getParam('category_id');
    $param['subcategory_id'] = $this->_getParam('subcategory_id');

    $this->view->detactLocation = $param['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }

    if ($this->view->detactLocation) {
      $param['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $param['latitude'] = $this->_getParam('latitude', 0);
      $param['longitude'] = $this->_getParam('longitude', 0);
    }

    switch ($param['popularType']) {
      case 'view':
        $param['orderby'] = 'view_count';
        break;
      case 'comment':
        $param['orderby'] = 'comment_count';
        break;
      case 'like':
        $param['orderby'] = 'like_count';
        break;
      case 'rating':
        $param['orderby'] = 'rating';
        break;
      case 'creation':
        $param['orderby'] = 'creation_date';
        break;
      case 'modified':
        $param['orderby'] = 'modified_date';
        break;
      case 'random':
        $param['orderby'] = 'random';
        break;
      default :
          $param['orderby'] = $param['popularType'];
    }

    $this->view->params = $param;
    if (!$this->view->is_ajax_load)
      return;

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($param);

    $popularPhoto = $coreApi->getSetting('sitealbum.badgeviewer', null);
    $photoType = $coreApi->getSetting('sitealbum.phototype', null);
    $featuredPhoto = $coreApi->getSetting('sitealbum.featuredalbum', null);

    // Do not render if nothing to show
    if (($paginator->getTotalItemCount() <= 0) || (empty($popularPhoto) || empty($photoType) || empty($featuredPhoto))) {
      return $this->setNoRender();
    }

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');
    
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    if ($this->view->showLightBox) {
      if ($param['detactLocation'])
        $this->view->params = $params = array('type' => $param['orderby'], 'count' => $paginator->getTotalItemCount(), 'featured' => $param['featured'], 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'interval' => $param['interval'], 'latitude' => $param['latitude'], 'longitude' => $param['longitude'], 'defaultLocationDistance' => $param['defaultLocationDistance'], 'title' => $this->_getParam('title', null));
      else
        $this->view->params = $params = array('type' => $param['orderby'], 'count' => $paginator->getTotalItemCount(), 'featured' => $param['featured'], 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'interval' => $param['interval'], 'title' => $this->_getParam('title', null));
    }
  }

}