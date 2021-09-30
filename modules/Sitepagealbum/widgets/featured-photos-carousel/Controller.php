<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagealbum_Widget_FeaturedPhotosCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Total List List Sponserd
    $totalFeaturedPhotos = Engine_Api::_()->sitepagealbum()->getFeaturedPhotos();
    // Total Count Sponsored Classiifed
    $this->view->totalCount_photo = $totalCount = $totalFeaturedPhotos->count();
    if (!($this->view->totalCount_photo > 0)) {
      return $this->setNoRender();
    }
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
    $this->view->inOneRow_photo = $inOneRow = $this->_getParam('inOneRow', 3);
    $this->view->noOfRow_photo = $noOfRow = $this->_getParam('noOfRow', 2);
    $this->view->totalItemShow_photo = $totalItemShow = $inOneRow * $noOfRow;
    // List List featured
    $this->view->featuredPhotos = $featuredPhotos = Engine_Api::_()->sitepagealbum()->getFeaturedPhotos(array('limit' => $totalItemShow,'category_id' => $category_id));

    // CAROUSEL SETTINGS  
    $this->view->interval = $interval = $this->_getParam('interval', 250);
    $this->view->count = $count = $featuredPhotos->count();
    $this->view->heightRow = @ceil($count / $inOneRow);
    $this->view->vertical = $this->_getParam('vertical', 0);
    $this->view->showLightBox = Engine_Api::_()->sitepage()->canShowPhotoLightBox();
    if ($this->view->showLightBox) {
      $this->view->params = $params = array('type' => 'featured', 'count' => $totalCount,'title'=>$this->_getParam('title', 'Featured Photos'));
    }
  }

}