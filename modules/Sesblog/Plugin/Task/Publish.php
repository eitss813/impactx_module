<?php
/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Publish.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblog_Plugin_Task_Publish extends Core_Plugin_Task_Abstract {
  public function execute() {
    Engine_Api::_()->sesblog()->checkBlogStatus();
  }
}
