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
class Sitepage_Widget_SitemobilePopularPagesController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    //SITEMOBILE CODE
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->isajax = $this->_getParam('isajax', false);
    if ($this->view->isajax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->viewmore = $this->_getParam('viewmore', false);
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('contentpage', 1) > 1 || $this->_getParam('page', 1) > 1)
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
    
    $params = array(); 
    //Content display widget setting parameter.
    $params['content_display'] = $this->view->contentDisplayArray = $this->_getParam('content_display', array("ratings","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location","price"));
    $params['columnHeight']  = $this->view->columnHeight = $this->_getParam('columnHeight', 325);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['popularity'] = $popularity = $this->view->popularity = $this->_getParam('popularity', 'Recently Posted');
    
    $params['layouts_views'] = $this->view->layouts_views = $this->_getParam('layouts_views', array("1","2"));
   
    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if($params['popularity'] == "Most Joined" && !$sitepagememberEnabled){
      return $this->setNoRender();
    }
    
    $limit = $this->_getParam('itemCount',5);
    if($limit){
      $params['limit']= $params['itemCount'] = $limit;
    }
   
    $params['page'] = $this->_getParam('page', 1);
    $this->view->isajax = $params['isajax'] = $this->_getParam('isajax', 0);
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);  
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    //location work
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $this->view->enableLocation;
    }
    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }
    //end
    $params['paginator'] = true;
    
    $columnsArray = array('page_id', 'title', 'page_url', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }  
    $this->view->sitepages = $paginator = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings($popularity, $params, null, null, $columnsArray);  
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($limit); 
    $this->view->paginator = $paginator->setCurrentPageNumber($params['page']);
    $params['totalpages'] = $this->view->totalCount;
    
    //SEND ALL PARAMS
    $this->view->params = $params;
    //location work
    $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
    $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);

    if ( !(count($this->view->sitepages) > 0)){
      return $this->setNoRender();
    }
  }
}

?>
