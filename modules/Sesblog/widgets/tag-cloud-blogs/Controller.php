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
class Sesblog_Widget_tagCloudBlogsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->height =  $this->_getParam('height', '300');
    $this->view->color =  $this->_getParam('color', '#00f');
    $this->view->textHeight =  $this->_getParam('text_height', '15');
    $this->view->type =  $this->_getParam('type', 'tab');
    
    $this->view->paginator = $paginator = Engine_Api::_()->sesblog()->tagCloudItemCore();
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', '25'));
    if($paginator->getTotalItemCount() <= 0) 
      return $this->setNoRender();
  }
}
