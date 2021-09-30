<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Controller.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_Widget_BlogRenewButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $blog_id = $this->_getParam('blog_id', false);
    if ((!Engine_Api::_()->core()->hasSubject() && !$blog_id ) || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage')) {
      return $this->setNoRender();
    }
    if ($blog_id)
      $blog = $this->view->blog = Engine_Api::_()->getItem('sesblog_blog', $blog_id);
    else
      $blog = $this->view->blog = Engine_Api::_()->core()->getSubject();

    $this->view->transaction = Engine_Api::_()->getDbTable('transactions', 'sesblogpackage')->getItemTransaction(array('order_package_id' => $blog->orderspackage_id, 'blog' => $blog));
    $this->view->package = Engine_Api::_()->getItem('sesblogpackage_package', $blog->package_id);
    if (!$this->view->package)
      return $this->setNoRender();
  }

}
