<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Widget_MostjoinedSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params =array();
    $params['totalpages'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['featured'] = $this->_getParam('featured',0);
    $params['sponsored'] = $this->_getParam('sponsored',0);

    //GET SITEPAGE FOR MOST LIKE
    $this->view->sitepages = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Most Joined',$params,'', '', array('page_id', 'photo_id','title', 'body', 'page_url', 'owner_id', 'member_count', 'member_title'));
    $this->view->sitepagesCount = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListingsCount('Most Joined',$params,'', '', array('page_id', 'photo_id','title', 'body', 'page_url', 'owner_id', 'member_count', 'member_title'));

    //NOT RENDER IF SITEPAGE COUNT ZERO
    if (!($this->view->sitepagesCount > 0)) {
      return $this->setNoRender();
    }
  }
}