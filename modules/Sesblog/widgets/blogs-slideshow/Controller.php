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
class Sesblog_Widget_BlogsSlideshowController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->title_truncation = $this->_getParam('title_truncation', '45');
	 	$this->view->autoplay = $this->_getParam('autoplay',1);
		$this->view->speed = $this->_getParam('speed',2000);
		
    $this->view->socialshare_enable_plusicon = $this->_getParam('socialshare_enable_plusicon', 1);
    $this->view->socialshare_icon_limit = $this->_getParam('socialshare_icon_limit', 2);
    
		$this->view->height = $this->_getParam('height', 365);
		$this->view->leftBlog = $this->_getParam('leftBlog', 1);
		$this->view->enableSlideshow = $this->_getParam('enableSlideshow', 1);

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $this->view->show_criteria = $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'title', 'socialSharing', 'view', 'featuredLabel', 'sponsoredLabel', 'verifiedLabel', 'rating', 'ratingStar', 'by', 'favourite','category','favouriteButton','likeButton', 'creationDate'));
    
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
      
    $value['criteria'] = $this->_getParam('criteria', 5);
    $value['info'] = $this->_getParam('info', 'recently_created');
   	$value['order'] = $this->_getParam('order', '');
    $value['fetchAll'] = true;
		$value['limit'] = 3;
    $this->view->paginatorLeft = $paginatorLeft = Engine_Api::_()->getDbTable('blogs', 'sesblog')->getSesblogsSelect($value);

    $limit =  $this->_getParam('limit_data', 3);
    $valueRight['criteria'] = $this->_getParam('criteria_right', 5);
    $valueRight['info'] = $this->_getParam('info_right', 'recently_created');
   	$valueRight['order'] = $this->_getParam('order_right', '');
    $valueRight['fetchAll'] = true;
    if($this->view->enableSlideshow){
      $valueRight['limit'] = $limit;
		} else {
      $valueRight['limit'] = 1;
		}
    $this->view->paginatorRight = $paginatorRight = Engine_Api::_()->getDbTable('blogs', 'sesblog')->getSesblogsSelect($valueRight);
  }
}
