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
class Sitealbum_Widget_myAlbumsSitealbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    if (isset($params['page']) && !empty($params['page']))
      $this->view->page = $page = $params['page'];
    else
      $this->view->page = $page = 1;

    if (isset($params['is_ajax_load']))
      unset($params['is_ajax_load']);

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
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
      }
    }

    if (Engine_Api::_()->seaocore()->isSitemobileApp() && $this->_getParam('ajax', false)) {
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    
    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
    $params['limit'] = $this->_getParam('limit', 12);
    $this->view->albumInfo = $params['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle"));
    $this->view->photoWidth = $params['photoWidth'] = $this->_getParam('photoWidth', 195);
    $this->view->photoHeight = $params['photoHeight'] = $this->_getParam('photoHeight', 195);
    $this->view->albumTitleTruncation = $params['albumTitleTruncation'] = $this->_getParam('albumTitleTruncation', 100);
    $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->truncationDescription = $params['truncationDescription'] = $this->_getParam('truncationDescription', 150);
    $this->view->album_view_type = $params['album_view_type'] = $this->_getParam('album_view_type', 1);
    $this->view->margin_photo = $params['margin_photo'] = $this->_getParam('margin_photo', 3);
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', 270);
    $params['category_id'] = $this->_getParam('category_id');
    $params['subcategory_id'] = $this->_getParam('subcategory_id');
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $params['albumType'] = $params['action'];
    $params['defaultAlbumsShow'] = 1;

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }

    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->params = $params;

    if (!$this->view->is_ajax_load)
      return;

    $params['notLocationPage'] = 1;
    $params['paginator'] = 1;
    $paginator = $this->view->paginator =  Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumPaginator($params);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($params['limit']);
    $paginator->setCurrentPageNumber($page);

    $this->view->is_ajax = $this->_getParam('isajax', '');
    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->totalAlbums = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalAlbums) / $params['limit']);
  }

}