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

class Sesblog_Widget_ProfileTagsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    if(!Engine_Api::_()->core()->hasSubject())  
      return $this->setNoRender();
    
    $subject = Engine_Api::_()->core()->getSubject();
    
    $this->view->paginator = Engine_Api::_()->sesblog()->tagCloudItemCore('', $subject->getIdentity());
    //$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', '25'));
    
    if(count($this->view->paginator) == 0) 
		return $this->setNoRender();
  }
}
