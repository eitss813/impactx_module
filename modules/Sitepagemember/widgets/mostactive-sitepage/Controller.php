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
class Sitepagemember_Widget_MostactiveSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $params = array();
    $params['active_pages'] = $this->_getParam('active_pages', 'member_count');

    //GET SITEPAGE FOR MOST LIKE
    $this->view->sitepages = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Most Active Pages',$params,'', '', array('page_id', 'photo_id','title', 'body', 'page_url', 'owner_id', 'view_count', 'like_count', 'comment_count', 'member_count', 'member_title'));
		$this->view->statistics =$this->_getParam('statistics', 'members');
    //NOT RENDER IF SITEPAGE COUNT ZERO
    if (!(count($this->view->sitepages) > 0)) {
      return $this->setNoRender();
    }
  }
}