<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_RecentlyPopularRandomSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK IF PAGE IS THERE OR NOT
    $sitepagePaginator = Engine_Api::_()->sitepage()->getSitepagesPaginator(array('type' => 'browse_home_zero', 'limit' => 1));
    if ((count($sitepagePaginator) == 0)) {
      return $this->setNoRender();
    }
    
    $this->view->titleLink = $this->_getParam('titleLink', null);
    $this->view->titleLinkPosition = $this->_getParam('titleLinkPosition', 'bottom');
//    $this->view->photoHeight = $this->_getParam('photoHeight', 0);
    $this->view->photoWidth = $this->_getParam('photoWidth', 0);
    
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if (!$this->_getParam('detactLocation', 0) || $this->_getParam('contentpage', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if ($this->_getParam('detactLocation', 0))
        $this->getElement()->removeDecorator('Title');

      $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', false);
    }     
    
    $params = array();
    
    $this->view->category_id = $params['category_id'] =  $category_id = $this->_getParam('category_id',0);
    $pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if (!empty($pagemember)) {
			$showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5', "5" => '6'));
    } else {
			$showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'));
    }

    $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));

    $defaultOrder = $this->_getParam('layouts_oder', 1);
    $this->view->columnWidth = $this->_getParam('columnWidth', 188);
    $this->view->columnHeight = $this->_getParam('columnHeight', 350);
    $this->view->showlikebutton = $this->_getParam('showlikebutton', 1);
    $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $this->_getParam('showlocation', 1);
    $this->view->showgetdirection = $this->_getParam('showgetdirection', 1);
    $this->view->showprice = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $this->_getParam('showdate', 1);
    $this->view->turncation = $this->_getParam('turncation', 15);
    $this->view->listview_turncation = $this->_getParam('listview_turncation', 40);
    $this->view->show_name = $this->_getParam('show_name', 1);
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$statisticsElement['']="memberCount";
		}
    //$this->view->statistics = $this->_getParam('statistics', $statisticsElement);
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);

    if($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    $list_limit = 0;
    $grid_limit = 0;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      $grid_limit = $this->_getParam('grid_limit', 15);
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    $sitepageRecently = array();
    $sitepageViewed = array();
    $sitepageRandom = array();
    $sitepageFeatured = array();
    $sitepageSponosred = array();
    $sitepageJoined = array();
    $pageTable = Engine_Api::_()->getDbTable('pages', 'sitepage');
    
    //PAGE-RATING IS ENABLE OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');    
    $columnsArray = array('page_id', 'title', 'page_url', 'body' ,'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count','show_name');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if ($this->view->ratngShow) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }     
    
    if (in_array("1", $showTabArray)) {
      // GET SITEPAGE SITEPAGE FOR RECENTLY POSTED
      $sitepageRecently = $pageTable->getListings('Recently Posted', $params, null, null, $columnsArray);
    }
    if (in_array("2", $showTabArray)) {
      // GET SITEPAGE SITEPAGE FOR MOST VIEWES
      $sitepageViewed = $pageTable->getListings('Most Viewed', $params, null, null, $columnsArray);
    }
    if (in_array("3", $showTabArray)) {
      $sitepageRandom = $pageTable->getListings('Random', $params, null, null, $columnsArray);
    }

    if (in_array("4", $showTabArray)) {
      $sitepageFeatured = $pageTable->getListings('Featured', $params, null, null, $columnsArray);
    }

    if (!empty($showTabArray) && is_array($showTabArray) && in_array("5", $showTabArray)) {
      $sitepageSponosred = $pageTable->getListings('Sponosred', $params, null, null, $columnsArray);
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			if (is_array($showTabArray) && in_array("6", $showTabArray)) {
				$sitepageJoined = $pageTable->getListings('Most Joined', $params, null, null, $columnsArray);
			}
    }
    
//     if ((!(count($sitepageRecently) > 0) && !(count($sitepageViewed) > 0) && !(count($sitepageRandom) > 0 ) && !(count($sitepageFeatured) > 0 ) && !(count($sitepageSponosred) > 0 )) || ($this->view->defaultView == -1)) {
//       return $this->setNoRender();
//     }
    
    $this->view->paramsLocation = $this->_getAllParams();  

    $tabsOrder = array();
    $tabs = array();
    $menuTabs = array();
    if (count($sitepageRecently) > 0) {
      $tabs['recent'] = array('title' => 'Recent', 'tabShow' => 'Recently Posted');
      $tabsOrder['recent'] = $this->_getParam('recent_order', 1);
    }
    if (count($sitepageViewed) > 0) {
      $tabs['popular'] = array('title' => 'Most Popular', 'tabShow' => 'Most Viewed');
      $tabsOrder['popular'] = $this->_getParam('popular_order', 2);
    }
    if (count($sitepageRandom) > 0) {
      $tabs['random'] = array('title' => 'Random', 'tabShow' => 'Random');
      $tabsOrder['random'] = $this->_getParam('random_order', 3);
    }

    if (count($sitepageFeatured) > 0) {
      $tabs['featured'] = array('title' => 'Featured', 'tabShow' => 'Featured');
      $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
    }
    if (count($sitepageSponosred) > 0) {
      $tabs['sponosred'] = array('title' => 'Sponsored', 'tabShow' => 'Sponosred');
      $tabsOrder['sponosred'] = $this->_getParam('sponosred_order', 5);
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			if (count($sitepageJoined) > 0) {
				$tabs['mostjoined'] = array('title' => 'Most Joined', 'tabShow' => 'Most Joined');
				$tabsOrder['mostjoined'] = $this->_getParam('joined_order', 6);
			}
    }
    
    @asort($tabsOrder);
    $firstIndex = key($tabsOrder);
    foreach ($tabsOrder as $key => $value) {
      $menuTabs[$key] = $tabs[$key];
    }

    $this->view->tabs = $menuTabs;
    $this->view->active_tab_list = $list_limit;
    $this->view->active_tab_image = $grid_limit;
    $params['limit'] = $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;
    $this->view->identity = $this->_getParam('identity', $this->view->identity); 
    $tabShow = isset($menuTabs[$firstIndex]) ? $menuTabs[$firstIndex]['tabShow'] : '';
    $this->view->sitepagesitepage = $sitepage = $pageTable->getListings($tabShow, $params, null, null, $columnsArray);

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    $this->view->sitepage = '';

    if (!empty($this->view->map_view)) {

      $this->view->flageSponsored = 0;

      if (!empty($checkLocation)) {
        $sitepage_temp = $ids = array();
        foreach ($sitepage as $sitepage_page) {
          $id = $sitepage_page->getIdentity();
          $ids[] = $id;
          $sitepage_temp[$id] = $sitepage_page;
        }
        $values['page_ids'] = $ids;

        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);
        foreach ($locations as $location) {
          if ($sitepage_temp[$location->page_id]->sponsored) {
            $this->view->flageSponsored = 1;
            break;
          }
        }
        $this->view->sitepage = $sitepage_temp;
      }
    }
  }

}

?>
