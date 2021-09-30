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

class Sesblog_Widget_CategoryBrowseMenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $limit = $this->_getParam('categoryShow', 0) ? $this->_getParam('categoryShow', 0) : 1000;
    $this->view->categories = $categories = Engine_Api::_()->getDbTable('categories', 'sesblog')->getCategoriesAssoc(array('limit' => $limit));

    if(count($categories) == 0)
      return $this->setNoRender();
  }
}
