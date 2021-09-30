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
class Sitealbum_Widget_PinboardAlbumsSitealbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->params = $this->_getAllParams();
    $this->view->params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
    if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
      $this->view->params['noOfTimes'] = 1000;

    if ($this->_getParam('autoload', true)) {
      $this->view->autoload = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->autoload = false;
        if ($this->_getParam('contentpage', 1) > 1)
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
        $this->getElement()->removeDecorator('Title');
        //return;
      }
    } else {
      $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
      if ($this->_getParam('contentpage', 1) > 1) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
    }

    //CORE API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = array();
    $params['orderby'] = $this->view->popularity = $this->_getParam('popularity', 'comment_count');
    switch ($params['orderby']) {
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
      case 'modified':
        $params['orderby'] = 'modified_date';
        break;
      case 'photos':
        $params['orderby'] = 'photos_count';
        break;
      case 'random':
        $params['orderby'] = 'random';
        break;
    }
    $params['limit'] = $this->_getParam('itemCount', 12);
    $this->view->userComment = $this->_getParam('userComment', 1);
    $this->view->statistics = $this->_getParam('albumInfo', array("likeCount", "memberCount"));
    $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
    $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
    $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
    $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['paginator'] = 1;

    $sitealbum_pinboard_view = Zend_Registry::isRegistered('sitealbum_pinboard_view') ? Zend_Registry::get('sitealbum_pinboard_view') : null;
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $settings->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->events = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumPaginator($params);
    $this->view->totalCount = $paginator->getTotalItemCount();

    $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
    $paginator->setItemCountPerPage($params['limit']);
    //DON'T RENDER IF RESULTS IS ZERO
    if (empty($sitealbum_pinboard_view) || $this->view->totalCount <= 0) {
      return $this->setNoRender();
    }
    
    $this->view->countPage = $paginator->count();
    $this->view->normalPhotoWidth = $settings->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $settings->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $settings->getSetting('sitealbum.last.photoid');
    if ($this->view->params['noOfTimes'] > $this->view->countPage)
      $this->view->params['noOfTimes'] = $this->view->countPage;

    $this->view->show_buttons = $this->_getParam('show_buttons', array("comment", "like", 'facebook', 'twitter', 'pinit'));
  }

}
