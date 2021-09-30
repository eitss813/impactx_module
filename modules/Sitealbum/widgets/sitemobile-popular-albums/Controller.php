<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitealbum_Widget_SitemobilePopularAlbumsController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    ///SITEMOBILE CODE
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
    $this->view->truncation = $this->_getParam('truncation', 25);
    $params['columnHeight']  = $this->view->columnHeight = $this->_getParam('columnHeight', 325);
    $params['category_id'] = $this->_getParam('category_id');
    $params['subcategory_id'] = $this->_getParam('subcategory_id');
    $params['popularity'] = $popularity = $this->view->popularity = $this->_getParam('popularity', 'recentalbums');
    $params['orderBy'] = $this->view->orderBy = $this->_getParam('orderBy', 'creation_date');
    $ratingEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1);
    if($params['popularity'] == "Most Rated" && !$ratingEnabled){
      return $this->setNoRender();
    }
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    if (!$coreApi->getSetting('sitealbum.viewerphoto', 0)) {
      return $this->setNoRender();
    }
    $this->view->albumInfo = $params['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName"));
    //location work
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }
    
    $limit = $this->_getParam('itemCount',5);
    if($limit){
      $params['limit']= $params['itemCount'] = $limit;
    }

    $params['page'] = $this->_getParam('page', 1);
    $this->view->isajax =  $params['isajax'] = $this->_getParam('isajax', 0);
    $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity); 
    $params['paginator'] = true;

     switch ($popularity) {
      case 'recentalbums':
        $params['orderby'] = 'creation_date';
        break;
      case 'most_likedalbums':
        $params['orderby'] = 'like_count';
        break;
      case 'most_viewedalbums':
        $params['orderby'] = 'view_count';
        break;
      case 'most_commentedalbums':
        $params['orderby'] = 'comment_count';
        break;
      case 'most_ratedalbums':
        $params['orderby'] = 'rating';
        break;
      case 'featuredalbums':
        $params['orderby'] = 'featured';
        break;
      case 'randomalbums':
        $params['orderby'] = 'random';
        break;
    }
    $this->view->sitealbums = $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->albumBySettings($params); 

    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($limit); 
    $this->view->paginator = $paginator->setCurrentPageNumber($params['page']);
    $params['totalpages'] = $this->view->totalCount;
    
    //SEND ALL PARAMS
    $this->view->params = $params;
    //location work
    $this->view->paramsLocation = array_merge($request->getParams(), $this->_getAllParams());
    if ( !(count($this->view->sitealbums) > 0)){
      return $this->setNoRender();
    }
  }
}

?>
