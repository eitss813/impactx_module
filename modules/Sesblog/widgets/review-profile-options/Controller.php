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
class Sesblog_Widget_ReviewProfileOptionsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->viewType = $t= $this->_getParam('viewType', 'vertical');
    $coreMenuApi = Engine_Api::_()->getApi('menus', 'core');
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sesblog_review') || !$viewerId)
     return $this->setNoRender();
    
    $review = Engine_Api::_()->core()->getSubject('sesblog_review');
    $this->view->content_item = Engine_Api::_()->getItem('sesblog_blog', $review->blog_id);
    $this->view->navigation = $coreMenuApi->getNavigation('sesblogreview_profile');
  }

}
