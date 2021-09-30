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
class Sitealbum_Widget_FeaturedAlbumsSlideshowController extends Engine_Content_Widget_Abstract {

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

    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    $values = array();
    $this->view->albumInfo = $values['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->titleLink = $values['titleLink'] = $this->_getParam('titleLink', '');
    $this->view->albumTitleTruncation = $values['albumTitleTruncation'] = $this->_getParam('albumTitleTruncation', 16);
    $this->view->truncationLocation = $values['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->truncationDescription = $values['truncationDescription'] = $this->_getParam('truncationDescription', 150);
    $values['limit'] = $this->_getParam('itemCountPerPage', 10);
    $values['featured'] = $this->_getParam('featured', 0);
    $values['popularType'] = $this->_getParam('popularType', 'comment_count');
    $values['interval'] = $interval = $this->_getParam('interval', 'overall');
    $values['category_id'] = $this->_getParam('category_id');
    $values['subcategory_id'] = $this->_getParam('subcategory_id');

    $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $values['latitude'] = $this->_getParam('latitude', 0);
      $values['longitude'] = $this->_getParam('longitude', 0);
    }

    switch ($values['popularType']) {
      case 'view':
        $values['orderby'] = 'view_count';
        break;
      case 'comment':
        $values['orderby'] = 'comment_count';
        break;
      case 'like':
        $values['orderby'] = 'like_count';
        break;
      case 'rating':
        $values['orderby'] = 'rating';
        break;
      case 'creation':
        $values['orderby'] = 'creation_date';
        break;
      case 'modified':
        $values['orderby'] = 'modified_date';
        break;
      case 'photos':
        $values['orderby'] = 'photos_count';
        break;
      case 'random':
        $values['orderby'] = 'random';
        break;
    }

    $this->view->params = $values;

    if (!$this->view->is_ajax_load)
      return;

    // Get paginator
    $this->view->show_slideshow_object = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->albumBySettings($values);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($values['limit']);
    $featuredAlbum = $coreApi->getSetting('sitealbum.featuredalbum', null);
    // Count Featured Albums
    $this->view->num_of_slideshow = $paginator->getTotalItemCount() > $values['limit'] ? $values['limit'] : count($this->view->show_slideshow_object);

    // Number of the result.
    if (empty($this->view->num_of_slideshow) || empty($featuredAlbum)) {
      return $this->setNoRender();
    }
  }

}