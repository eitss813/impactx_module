<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Widget_PeopleLikeItemController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    if (!Engine_Api::_()->core()->hasSubject('sesblog_blog'))
      return $this->setNoRender();

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sesblog_blog');
    
    $this->getElement()->removeDecorator('Container');
		$this->view->title = $this->getElement()->getTitle();
		$this->view->limit_data = $this->_getParam('limit_data','11');
		
		$this->view->paginator = $paginator = Engine_Api::_()->sesblog()->likeItemCore(array('id' => $subject->getIdentity(), 'type' => $subject->getType()));
		$paginator->setItemCountPerPage($this->view->limit_data);    
    if($paginator->getTotalItemCount() <= 0)
      return $this->setNoRender();
  }
}
