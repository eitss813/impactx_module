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

class Sesblog_Widget_AlbumBreadcrumbController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    if (!Engine_Api::_()->core()->hasSubject('sesblog_album'))
      return $this->setNoRender();
      
    $this->view->album = Engine_Api::_()->core()->getSubject('sesblog_album');
    $this->view->content_item = Engine_Api::_()->getItem('sesblog_blog', $this->view->album->blog_id);
    $this->view->tab_id = Engine_Api::_()->sesbasic()->getWidgetTabId(array('name' => 'sesblog.profile-photos'));
  }
}
