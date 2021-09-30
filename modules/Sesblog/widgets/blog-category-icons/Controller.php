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

class Sesblog_Widget_BlogCategoryIconsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->allParams = $allParams = $this->_getAllParams();

    if (in_array('countBlogs', $allParams['show_criteria']) || $allParams['criteria'] == 'most_blog')
      $allParams['countBlogs'] = true;
    
    $allParams['column_name'] = array('category_name', 'cat_icon', 'thumbnail', 'color', 'slug');
    
    $this->view->paginator = Engine_Api::_()->getDbTable('categories', 'sesblog')->getCategory($allParams);
    if (count($this->view->paginator) == 0)
      return;
  }
}
