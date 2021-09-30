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
class Sitepage_Widget_SponsoredSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();

    $this->view->limit = $params['limit'] = $this->_getParam('itemCount', 4);
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id', 0);
    $this->view->interval = $this->_getParam('interval', 300);
    $this->view->titletruncation = $this->_getParam('truncation', 18); 
    
    //GET SPONSERED PAGES
    $totalSitepage = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Total Sponsored Sitepage',$params, null, null, array('page_id'));

    //NO RENDER IF SPONSERED PAGES ARE ZERO
    $this->view->totalCount = $totalSitepage->count();
    if ( !($this->view->totalCount > 0) ) {
      return $this->setNoRender();
    }

    //SEND PAGE DATA TO TPL
    $columnsArray = array('page_id', 'title', 'page_url', 'owner_id', 'category_id', 'photo_id', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count','show_name');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }    
    $this->view->sitepages = $sitepages = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Sponsored Sitepage',$params, null, null, $columnsArray);

    $this->view->count = $sitepages->count();

  }

}
?>