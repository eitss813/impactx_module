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

class Sesblog_Widget_LabelsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    if(!Engine_Api::_()->core()->hasSubject('sesblog_blog'))
      return $this->setNoRender();
      
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if(empty($subject->featured) && empty($subject->sponsored) && empty($subject->verified))
      return $this->setNoRender();
  }
}
