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

class Sesblog_Widget_BlogInfoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    if (!Engine_Api::_()->core()->hasSubject())
      return $this->setNoRender();
    
    $this->view->subject = Engine_Api::_()->core()->getSubject();
    
		$customMetaFields = $this->view->customMetaFields = Engine_Api::_()->sesblog()->getCustomFieldMapDataBlog($this->view->subject);

    if (!count($customMetaFields))
      return $this->setNoRender();
  }
}
