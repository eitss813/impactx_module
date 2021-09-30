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

class Sesblog_Widget_tagBlogsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->tagCloudData = Engine_Api::_()->sesblog()->tagCloudItemCore('fetchAll');
    if( count($this->view->tagCloudData) <= 0 )
      return $this->setNoRender();
  }
}
