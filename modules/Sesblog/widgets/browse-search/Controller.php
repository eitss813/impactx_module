<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Controller.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->view_type = $this-> _getParam('view_type', 'horizontal');
    $this->view->search_for = $search_for = $this-> _getParam('search_for', 'blog');
    $default_search_type = $this-> _getParam('default_search_type', 'mostSPliked');
	
    if($this->_getParam('location','yes') == 'yes' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog_enable_location', 1))
      $location = 'yes';	
    else
      $location = 'no';
    
    $sesblog_browssesblogs = Zend_Registry::isRegistered('sesblog_browssesblogs') ? Zend_Registry::get('sesblog_browssesblogs') : null;
    if (empty($sesblog_browssesblogs))
      return $this->setNoRender();
      
    $searchForm = $this->view->form = new Sesblog_Form_Search(array('searchTitle' => $this->_getParam('search_title', 'yes'),'browseBy' => $this->_getParam('browse_by', 'yes'),'categoriesSearch' => $this->_getParam('categories', 'yes'),'searchFor'=>$search_for,'FriendsSearch'=>$this->_getParam('friend_show', 'yes'),'defaultSearchtype'=>$default_search_type,'locationSearch' => $location,'kilometerMiles' => $this->_getParam('kilometer_miles', 'yes'),'hasPhoto' => $this->_getParam('has_photo', 'yes')));

    $filterOptions = (array)$this->_getParam('search_type', array('recentlySPcreated' => 'Recently Created','mostSPviewed' => 'Most Viewed','mostSPliked' => 'Most Liked', 'mostSPcommented' => 'Most Commented','mostSPfavourite' => 'Most Favourite','featured' => 'Featured','sponsored' => 'Sponsored','verified' => 'Verified','mostSPrated'=>'Most Rated'));
    
		if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1))
			unset($filterOptions['mostSPfavourite']);
			
    if($this->_getParam('search_type','blog') !== null && $this->_getParam('browse_by', 'yes') == 'yes') {
      $arrayOptions = $filterOptions;
      $filterOptions = array();
      foreach ($arrayOptions as $key=>$filterOption) {
        if(is_numeric($key))
        $columnValue = $filterOption;
        else
        $columnValue = $key;
				$value = str_replace(array('SP',''), array(' ',' '), $columnValue);
				$filterOptions[$columnValue] = ucwords($value);
      }
      $filterOptions = array(''=>'')+$filterOptions;
      $searchForm->sort->setMultiOptions($filterOptions);
      $searchForm->sort->setValue($default_search_type);
    }
    
    $this->view->request = $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->controllerName = $request->getControllerName();
    $this->view->actionName = $request->getActionName();
    
    $searchForm->setMethod('get')->populate($request->getParams());
    
    // Browse page work
    $page_id = Engine_Api::_()->sesblog()->getWidgetPageId($this->view->identity);
    if($page_id) {
      $pageName = Engine_Db_Table::getDefaultAdapter()->select()
              ->from('engine4_core_pages', 'name')
              ->where('page_id = ?', $page_id)
              ->limit(1)
              ->query()
              ->fetchColumn();
      if($pageName) {
        $this->view->pageName = $pageName;
        $explode = explode('sesblog_index_', $pageName);
        if(is_numeric($explode[1])) {
          $this->view->page_id = $explode[1];
        }
      }
    }
    // Browse page work
  }
}
