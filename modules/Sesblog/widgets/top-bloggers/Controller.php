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

class Sesblog_Widget_TopBloggersController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
		
		$this->view->widgetTitle = $this->_getParam("title", 'Top Contributors');
		if (isset($_POST['params']))
		$params = json_decode($_POST['params'], true);
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$identity = $view->identity;
		$this->view->widgetIdentity = $this->_getParam('content_id', $identity);
		$this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
		$page = isset($_POST['page']) ? $_POST['page'] : 1;

		$this->view->view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'vertical');
		if ($this->_getParam('showLimitData', 1))
		$this->view->widgetName = 'top-bloggers';
		$limit = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', 5);
		$this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', 250);
		$this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', 300);
		$show_criterias = $this->_getParam('show_criteria', array('count','ownername'));
    foreach ($show_criterias as $show_criteria)
    $this->view->{$show_criteria . 'Active'} = $show_criteria;
		$this->view->paginator = $paginator = Engine_Api::_()->sesblog()->getBloggers();
		
		$this->view->params = array('limit_data' => $limit, 'height' => $height, 'width' => $width);
		$paginator->setItemCountPerPage($limit);
		$this->view->page = $page;
		$paginator->setCurrentPageNumber($page);
		
		if ($paginator->getTotalItemCount() <= 0)
      return $this->setNoRender();

		if ($is_ajax) {
			$this->getElement()->removeDecorator('Container');
			$this->getElement()->removeDecorator('Title');
		}
		// Add count to title if configured
		if ($paginator->getTotalItemCount() > 0) {
			$this->_childCount = $paginator->getTotalItemCount();
		}
  }
}
  
