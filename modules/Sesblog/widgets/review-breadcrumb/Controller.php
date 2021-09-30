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

class Sesblog_Widget_ReviewBreadcrumbController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreApi = Engine_Api::_()->core();
    if (!$coreApi->hasSubject('sesblog_review'))
      return $this->setNoRender();

    $this->view->review = $review = $coreApi->getSubject('sesblog_review');
    $this->view->content_item = Engine_Api::_()->getItem('sesblog_blog', $review->blog_id);
  }

}
