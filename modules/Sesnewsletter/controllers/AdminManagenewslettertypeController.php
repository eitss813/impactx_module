<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManagenewslettertypeController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_AdminManagenewslettertypeController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_managenewslettertype');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getResult();
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $action = Engine_Api::_()->getItem('sesnewsletter_type', $value)->delete();
          $db->query("DELETE FROM engine4_sesnewsletter_types WHERE type_id = " . $value);
        }
      }
    }
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }

  public function createAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id', 0);
    $this->view->form = $form = new Sesnewsletter_Form_Admin_AddNewsletterType();
    if ($id) {
      $form->setTitle("Edit This Newsletter Type");
      $form->submit->setLabel('Save Changes');
      $row = Engine_Api::_()->getItem('sesnewsletter_type', $id);
      $form->populate($row->toArray());
    }
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $db = Engine_Api::_()->getDbtable('types', 'sesnewsletter')->getAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('types', 'sesnewsletter');
        $values = $form->getValues();
        if (!$id)
            $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Newsletter Type created successfully.')
      ));
    }
  }

  public function singupuserAction() {

    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesnewsletter_type', $id);
      $item->singupuser = !$item->singupuser;
      $item->save();
    }
    $this->_redirect('admin/sesnewsletter/managenewslettertype');
  }

  public function existinguserAction() {

    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesnewsletter_type', $id);
      $item->existinguser = !$item->existinguser;
      $item->save();
    }
    $this->_redirect('admin/sesnewsletter/managenewslettertype');
  }

  public function guestuserAction() {

    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesnewsletter_type', $id);
      $item->guestuser = !$item->guestuser;
      $item->save();
    }
    $this->_redirect('admin/sesnewsletter/managenewslettertype');
  }

  public function enabledAction() {

    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesnewsletter_type', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }
    $this->_redirect('admin/sesnewsletter/managenewslettertype');
  }


  public function deleteAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesnewsletter_Form_Admin_DeleteNewsletterType();
    $form->setTitle('Delete This Newsletter Type?');
    $form->setDescription('Are you sure that you want to delete this newsletter type? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');

    $this->view->item_id = $id = $this->_getParam('id');

    // Check post
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getItem('sesnewsletter_type', $id)->delete();
      $db = Engine_Db_Table::getDefaultAdapter();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Newsletter Type Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }
}
