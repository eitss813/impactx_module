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

class Sesblog_Widget_GutterPhotoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    if(!Engine_Api::_()->core()->hasSubject('sesblog_blog'))
      return $this->setNoRender();
    
    $this->view->photoviewtype = $this->_getParam('photoviewtype', "circle");
    $this->view->title = $this->_getParam('title', "About Me");
    $this->getElement()->removeDecorator('Title');

    $this->view->owner = Engine_Api::_()->core()->getSubject('sesblog_blog')->getOwner();
  }
}
