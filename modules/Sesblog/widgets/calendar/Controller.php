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
class Sesblog_Widget_CalendarController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $date = $this->_getParam('date',false);
		if(strtotime($date) <= time() && $date){
    	$this->view->blogdate = $date;
		}else
			$this->view->blogdate = false;
  }
}
