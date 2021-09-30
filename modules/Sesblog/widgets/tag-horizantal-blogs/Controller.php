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
class Sesblog_Widget_TagHorizantalBlogsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->viewtype = $this->_getParam('viewtype', 1);
    $this->view->widgetbgcolor = $this->_getParam('widgetbgcolor', '424242');
    $this->view->buttonbgcolor = $this->_getParam('buttonbgcolor', '000000');
    $this->view->textcolor = $this->_getParam('textcolor', 'ffffff');
    
    $this->view->paginator = $paginator = Engine_Api::_()->sesblog()->tagCloudItemCore();
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', '25'));
    if( $paginator->getTotalItemCount() <= 0 ) 
      return $this->setNoRender();
  }
}
