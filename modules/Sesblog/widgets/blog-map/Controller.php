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

class Sesblog_Widget_BlogMapController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1))
      return $this->setNoRender();
      
    if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.location', 1))
      return $this->setNoRender();
    
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    
    $this->view->locationLatLng = $locationLatLng = Engine_Api::_()->getDbTable('locations', 'sesbasic')->getLocationData($subject->getType(), $subject->getIdentity());
		
    if ((!$subject->location && is_null($subject->location)) || !$locationLatLng)
      return $this->setNoRender();

  }
}
