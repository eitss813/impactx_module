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

class Sesblog_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->createButton = $this->_getParam('createButton', 1);
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_main');
    if(count($this->view->navigation) == 1)
      $this->view->navigation = null;
    
    $sesblog_browssesblogs = Zend_Registry::isRegistered('sesblog_browssesblogs') ? Zend_Registry::get('sesblog_browssesblogs') : null;
    if (empty($sesblog_browssesblogs))
      return $this->setNoRender();
      
    $this->view->createBlog = Engine_Api::_()->authorization()->isAllowed('sesblog_blog', Engine_Api::_()->user()->getViewer(), 'create');
      
    $this->view->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.taboptions', 6);
  }
}
