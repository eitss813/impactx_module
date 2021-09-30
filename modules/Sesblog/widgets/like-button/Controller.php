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
class Sesblog_Widget_LikeButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if (empty($viewer_id))
      return $this->setNoRender();
      
		if (!Engine_Api::_()->core()->hasSubject('sesblog_blog'))
      return $this->setNoRender();
      
		$subject = Engine_Api::_()->core()->getSubject('sesblog_blog');
		$this->view->subject_id = $subject->getIdentity();
		
		$likeUser = Engine_Api::_()->sesbasic()->getLikeStatus($this->view->subject_id, $subject->getType());
    $this->view->likeClass = (!$likeUser) ? 'fa-thumbs-up' : 'fa-thumbs-down' ;
    $this->view->likeText = ($likeUser) ?  $this->view->translate('Unlike') : $this->view->translate('Like') ;
  }
}
