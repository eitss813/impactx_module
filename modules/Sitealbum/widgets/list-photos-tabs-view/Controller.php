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
class Sitealbum_Widget_ListPhotosTabsViewController extends Engine_Content_Widget_Abstract {

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
    $this->view->columnHeight = $param['columnHeight'] = $this->_getParam('columnHeight', 250);
    $this->view->showViewMore = $param['showViewMore'] = $this->_getParam('showViewMore', 1);
    $this->view->showPhotosInLightbox = $param['showPhotosInLightbox'] = $this->_getParam('showPhotosInLightbox', 1); 
    if (empty($is_ajax)) {
      $tabs = $param['ajaxTabs'] = $this->_getParam('ajaxTabs', array("recentphotos", "most_likedphotos", "most_viewedphotos", "most_commentedphotos", "featuredphotos", "randomphotos", "most_ratedphotos",));

      if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1) && in_array("mostZZZratedphotos", $tabs)) {
        unset($tabs[array_search('mostZZZratedphotos', $tabs)]);
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
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();  
    $this->view->photoTitleTruncation = $param['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 100);
    $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->marginPhoto = $param['margin_photo'] = $this->_getParam('margin_photo', 3);
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 250);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 270);
    $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("ownerName", "albumTitle"));
    $this->view->orderBy = $param['orderBy'] = $this->_getParam('orderBy', 'creation_date');
    $param['limit'] = $limit = $this->_getParam('limit', 9);
    $this->view->showPhotosInJustifiedView = $param['showPhotosInJustifiedView'] = $this->_getParam('showPhotosInJustifiedView', 0); 
    $this->view->maxRowHeight = $param['maxRowHeight'] = $this->_getParam('maxRowHeight',0); 
    $this->view->rowHeight = $param['rowHeight'] = $this->_getParam('rowHeight',205);   
    $this->view->margin = $param['margin'] = $this->_getParam('margin',5);  
    $this->view->lastRow = $param['lastRow'] = $this->_getParam('lastRow', 'nojustify');  
    $param['category_id'] = $this->_getParam('category_id');
    $param['subcategory_id'] = $this->_getParam('subcategory_id');

    $this->view->param = $param;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($param);
    if (!$this->view->is_ajax_load)
      return;
    if (!$coreApi->getSetting('sitealbum.viewertype', 0)) {
      return $this->setNoRender();
    }
   
    
    $paramTabName = $this->_getParam('tabName', '');
    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $this->view->activTab = $activeTabName;
    switch ($activeTabName) {
      case 'recentphotos':
        $param['orderby'] = 'creation_date';
        break;
      case 'most_likedphotos':
        $param['orderby'] = 'like_count';
        break;
      case 'most_viewedphotos':
        $param['orderby'] = 'view_count';
        break;
      case 'most_commentedphotos':
        $param['orderby'] = 'comment_count';
        break;
      case 'featuredphotos':
        $param['orderby'] = 'featured';
        break;
      case 'most_ratedphotos':
        $param['orderby'] = 'rating';
        break;
      case 'randomphotos':
        $param['orderby'] = 'random';
        break;
    }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($param);
    $this->view->activTab_limit = $param['limit'];
    $paginator->setItemCountPerPage($param['limit']);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->params = $params = array();
    if ($this->view->showLightBox && !empty($param['orderby'])) {
      $pos = strpos($activeTabName, "photos");
      $str = substr($activeTabName, 0, $pos);
      if ($param['detactLocation'])
        $this->view->params = $params = array('type' => $param['orderby'], 'count' => $paginator->getTotalItemCount(), 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'latitude' => $param['latitude'], 'longitude' => $param['longitude'], 'defaultLocationDistance' => $param['defaultLocationDistance'], 'title' => ucwords(str_replace('_', ' ', $str)) . " Photos");
      else
        $this->view->params = $params = array('type' => $param['orderby'], 'count' => $paginator->getTotalItemCount(), 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'title' => ucwords(str_replace('_', ' ', $str)) . " Photos");
    }
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
  }

  public function setTabsOrder($tabs) {

    $tabsOrder['recentphotos'] = $this->_getParam('recentphotos', 1);
    $tabsOrder['most_likedphotos'] = $this->_getParam('most_likedphotos', 2);
    $tabsOrder['most_viewedphotos'] = $this->_getParam('most_viewedphotos', 3);
    $tabsOrder['most_commentedphotos'] = $this->_getParam('most_commentedphotos', 4);
    $tabsOrder['featuredphotos'] = $this->_getParam('featuredphotos', 5);
    $tabsOrder['randomphotos'] = $this->_getParam('randomphotos', 6);
    $tabsOrder['most_ratedphotos'] = $this->_getParam('most_ratedphotos', 7);

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