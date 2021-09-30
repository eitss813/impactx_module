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
class Sitealbum_Widget_ListAlbumsTabsViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if (!$this->_getParam('detactLocation', 0))
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if ($this->_getParam('detactLocation', 0))
        $this->getElement()->removeDecorator('Title');
      if (empty($this->view->is_ajax))
        $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', false);
      else
        $this->view->is_ajax_load = true;
    }

    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    $param = array();
    $this->view->showContent = $param['show_content'] = $this->_getParam('show_content', 1); 
    $this->view->columnHeight = $param['columnHeight'] = $this->_getParam('columnHeight', 270);
    $this->view->showViewMore = $param['showViewMore'] = $this->_getParam('showViewMore', 1);
    $this->view->titleLink = $param['titleLink'] = $this->_getParam('titleLink', '');
    $this->view->infoOnHover = $param['infoOnHover'] = $this->_getParam('infoOnHover', 1);
    if (empty($is_ajax)) {
      $tabs = $param['ajaxTabs'] = $this->_getParam('ajaxTabs', array("recentalbums", "most_likedalbums", "most_viewedalbums", "most_commentedalbums", "featuredalbums", "randomalbums", "most_ratedalbums",));

      if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1) && in_array("mostZZZratedalbums", $tabs)) {
        unset($tabs[array_search('mostZZZratedalbums', $tabs)]);
      }

      if ($tabs) {
        foreach ($tabs as $key => $value)
          $tabs[$key] = str_replace("ZZZ", "_", $value);
      } else {
        $tabs = array();
      }

      $this->view->tabs = $tabs;

      $this->view->tabCount = $count_tabs = count($tabs);
      if (empty($count_tabs)) {
        return $this->setNoRender();
      }

      $this->view->tabs = $tabs = $this->setTabsOrder($tabs);
      $activeTabName = $tabs[0];
    }

    if (!$coreApi->getSetting('sitealbum.viewerphoto', 0)) {
      return $this->setNoRender();
    }

    $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
    if (empty($getLightBox)) {
      return;
    }

    $this->view->detactLocation = $param['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $param['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $param['latitude'] = $this->_getParam('latitude', 0);
      $param['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->albumTitleTruncation = $param['albumTitleTruncation'] = $this->_getParam('albumTitleTruncation', 100);
    $this->view->orderBy = $param['orderBy'] = $this->_getParam('orderBy', 'creation_date');
    $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->marginPhoto = $param['margin_photo'] = $this->_getParam('margin_photo', 3);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 270);
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 220);
    $this->view->albumInfo = $param['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle"));
    $param['category_id'] = $this->_getParam('category_id');
    $param['subcategory_id'] = $this->_getParam('subcategory_id');
    $param['limit'] = $limit = $this->_getParam('limit', 9);
    $this->view->param = $param;

    if (!$this->view->is_ajax_load)
      return;

    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');
    $paramTabName = $this->_getParam('tabName', '');

    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $this->view->activTab = $activeTabName;

    switch ($activeTabName) {
      case 'recentalbums':
        $param['orderby'] = 'creation_date';
        break;
      case 'most_likedalbums':
        $param['orderby'] = 'like_count';
        break;
      case 'most_viewedalbums':
        $param['orderby'] = 'view_count';
        break;
      case 'most_commentedalbums':
        $param['orderby'] = 'comment_count';
        break;
      case 'most_ratedalbums':
        $param['orderby'] = 'rating';
        break;
      case 'featuredalbums':
        $param['orderby'] = 'featured';
        break;
      case 'randomalbums':
        $param['orderby'] = 'random';
        break;
    }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->albumBySettings($param);
    $paginator->setItemCountPerPage($param['limit']);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    $viewerType = $coreApi->getSetting('sitealbum.viewertype', null);
    if (empty($viewerType)) {
      return $this->setNoRender();
    }
  }

  public function setTabsOrder($tabs) {

    $tabsOrder['recentalbums'] = $this->_getParam('recentalbums', 5);
    $tabsOrder['most_likedalbums'] = $this->_getParam('most_likedalbums', 2);
    $tabsOrder['most_viewedalbums'] = $this->_getParam('most_viewedalbums', 3);
    $tabsOrder['most_commentedalbums'] = $this->_getParam('most_commentedalbums', 4);
    $tabsOrder['featuredalbums'] = $this->_getParam('featuredalbums', 1);
    $tabsOrder['randomalbums'] = $this->_getParam('randomalbums', 6);
    $tabsOrder['most_ratedalbums'] = $this->_getParam('most_ratedalbums', 7);

    $tempTabs = array();
    foreach ($tabs as $tab) {
      $order = $tabsOrder[$tab];
      if (isset($tempTabs[$order]))
        $order++;
      $tempTabs[$order] = $tab;
    }

    ksort($tempTabs);
    $orderTabs = array();
    $i = 0;
    foreach ($tempTabs as $tab)
      $orderTabs[$i++] = $tab;

    return $orderTabs;
  }

}