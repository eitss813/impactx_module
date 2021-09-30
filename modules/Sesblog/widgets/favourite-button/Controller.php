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
class Sesblog_Widget_FavouriteButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if (empty($this->view->viewer_id))
      return $this->setNoRender();
      
		if (!Engine_Api::_()->core()->hasSubject('sesblog_blog') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1))
      return $this->setNoRender();
      
		$subject = Engine_Api::_()->core()->getSubject('sesblog_blog');
		
		$this->view->subject_id = $subject->getIdentity();
		
		$this->view->favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type' => $subject->getType(),'resource_id' => $this->view->subject_id));
  }
}
