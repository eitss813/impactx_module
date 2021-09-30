<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_MapViewmembersController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    
    if (Engine_Api::_()->seaocore()->isSitemobileApp() && $this->_getParam('ajax', false)) {
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    $params = array();
    $this->view->allParams = $this->_getAllParams();
    $this->view->identity = $this->view->allParams['identity'] = $this->_getParam('identity', $this->view->identity);
    
    $this->view->customParams = $this->_getParam('customParams', 5);
    $this->view->custom_field_title = $this->_getParam('custom_field_title', 5);
    $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 5);
    $this->view->statistics = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField", "distance"));    
    //$this->view->enableBounce = $this->_getParam('sitemember_map_sponsored', 1);

    $values = array();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->params = $params = $request->getParams();

    $page = 1;
    if (isset($params['page']) && !empty($params['page'])) {
      $page = $params['page'];
    }

    $values['page'] = $page;
    $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
    $values['limit'] = $itemCount = $this->_getParam('itemCount', 100);

    // GET MEMBERS
    $this->view->paginator = $paginator = Engine_Api::_()->sitemember()->getUsersSelect(array('seao_locationid' => 1), array());
    $paginator->setItemCountPerPage($itemCount);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->totalResults = $paginator->getTotalItemCount();
    
    if($this->view->totalResults <= 0) {
        return $this->setNoRender();
    }

    //$this->view->flageSponsored = 0;

    if ($this->view->totalResults > 0) {
      $ids = array();
      foreach ($paginator as $user) {
        $id = $user->seao_locationid;
        $ids[] = $id;
        $user_temp[$id] = $user;
      }
      $values['user_ids'] = $ids;

      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($values);

//      foreach ($locations as $location) {
//        if ($user_temp[$location->locationitem_id]->sponsored) {
//          $this->view->flageSponsored = 1;
//          break;
//        }
//      }
      $this->view->sitemember = $user_temp;
    }

    $this->view->is_ajax = $this->_getParam('is_ajax', 0);    
    $this->view->page = $this->_getParam('page', 1);  
  }
  
}