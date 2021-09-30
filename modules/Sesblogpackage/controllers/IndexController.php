<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: IndexController.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_IndexController extends Core_Controller_Action_Standard {

  public function indexAction() {
    $this->view->someVar = 'someVal';
  }

  public function cancelAction() {
    $packageId = $this->_getParam('package_id', 0);

    $this->view->form = $form = new Sesblogpackage_Form_Cancel();

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    
    Engine_Api::_()->getDbTable('packages','sesblogpackage')->cancelSubscription(array('package_id' => $packageId));

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Package Subscription has been Deleted Successfully.'))
    ));
  }

  public function blogAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->package = $packageMemberLevel = Engine_Api::_()->getDbTable('packages', 'sesblogpackage')->getPackage(array('member_level' => $viewer->level_id, 'enabled' => 0));
    if (!count($packageMemberLevel) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 0))
      return $this->_helper->redirector->gotoRoute(array('action' => 'create'), 'sesblog_general', true);

    $this->view->existingleftpackages = $existingleftpackages = Engine_Api::_()->getDbTable('orderspackages', 'sesblogpackage')->getLeftPackages(array('owner_id' => $viewer->getIdentity()));

    $this->_helper->content->setEnabled();
  }

  public function confirmUpgradeAction() {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $blog_id = $this->_getParam('blog_id', false);
    $package_id = $this->_getParam('package_id', false);
    if (!$blog_id || !$package_id)
      return $this->_forward('notfound', 'error', 'core');
    $sesblog = Engine_Api::_()->getItem('sesblog_blog', $blog_id);
    $package = Engine_Api::_()->getItem('sesblogpackage_package', $package_id);
    if (!$sesblog || !$package || $sesblog->package_id == $package_id)
      return $this->_forward('notfound', 'error', 'core');

    $this->view->form = $form = new Sesblogpackage_Form_Confirm();

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $validPackage = Engine_Api::_()->getDbTable('packages', 'sesblogpackage')->getPackage(array('member_level' => $viewer->level_id, 'show_upgrade' => 1, 'package_id' => $package_id));
    if (empty($package->enabled) || !$validPackage)
      return $this->_forward('notfound', 'error', 'core');
    $tableBlog = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    if ($this->getRequest()->getPost()) {
      $orderpackage_id = $sesblog->orderspackage_id;
      if (empty($sesblog->transaction_id)) {
        //get transaction id
        $select = $tableBlog->select()->where('transaction_id !=?', '')->where('orderspackage_id =?', $sesblog->orderspackage_id);
        $transactionBlog = $tableBlog->fetchRow($select);
        if ($transactionBlog)
          $transaction = Engine_Api::_()->getItem('sesblogpackage_transaction', $transactionBlog->transaction_id);
        else
          $transaction = '';
      }else {
        $transaction = Engine_Api::_()->getItem('sesblogpackage_transaction', $sesblog->transaction_id);
        $transactionBlog = $sesblog;
      }
      if ($transactionBlog) {
        $transactionBlog->cancel();
        $tableBlog->update(array('transaction_id' => '', 'package_id' => $package_id), array('orderspackage_id' => $orderpackage_id));
        if ($transaction) {
          $tableBlog->update(array('transaction_id' => '', 'package_id' => $package_id), array('orderspackage_id' => $orderpackage_id));
          $transaction->delete();
        }
      }
      if ($package->isFree()) {
        if (!empty($orderpackage_id))
          $tableBlog->update(array('transaction_id' => '', 'package_id' => $package_id, 'is_approved' => 1), array('orderspackage_id' => $orderpackage_id));
        else {
          $sesblog->is_approved = 1;
          $sesblog->package_id = $package_id;
          $sesblog->save();
        }
      } else {
        if (!empty($orderpackage_id))
          $tableBlog->update(array('transaction_id' => '', 'package_id' => $package_id, 'is_approved' => 0), array('orderspackage_id' => $orderpackage_id));
        else {
          $sesblog->is_approved = 0;
          $sesblog->package_id = $package_id;
          $sesblog->save();
        }
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Blog Package changed successfully.'))
      ));
    }
  }

}
