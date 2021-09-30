<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_album_manage');

    //MAKE FORM
    $this->view->formFilter = $formFilter = new Sitealbum_Form_Admin_Manage_Filter();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumTableName = $albumTable->info('name');

    //GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    //GET CATEGORY TABLE
    $tableCategoryName = Engine_Api::_()->getDbtable('categories', 'sitealbum')->info('name');

    $select = $albumTable->select();
    //MAKE QUERY
    $select = $albumTable->select()
            ->setIntegrityCheck(false)
            ->from($albumTableName)
            ->joinLeft($tableUserName, "$albumTableName.owner_id = $tableUserName.user_id", 'username')
            ->joinLeft($tableCategoryName, "$albumTableName.category_id = $tableCategoryName.category_id", 'category_name')
            ->group("$albumTableName.album_id");

    // searching
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->albumbrowse = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';

    if (!empty($_POST['title'])) {
      $this->view->title = $_POST['title'];
      $select->where($albumTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
    }

    if (!empty($_POST['owner'])) {
      $owner = $this->view->owner = $_POST['owner'];
      $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
    }

    if (!empty($_POST['category_id']) && empty($_POST['subcategory_id'])) {
      $this->view->category_id = $_POST['category_id'];
      $select->where($albumTableName . '.category_id = ? ', $_POST['category_id']);
    } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id'])) {
      $this->view->category_id = $_POST['category_id'];
      $this->view->subcategory_id = $_POST['subcategory_id'];

      $select->where($albumTableName . '.category_id = ? ', $_POST['category_id'])
              ->where($albumTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
    }

    if (!empty($_POST['albumbrowse'])) {
      $this->view->albumbrowse = $_POST['albumbrowse'];
      if ($_POST['albumbrowse'] == 1) {
        $select->order($albumTableName . '.album_id DESC');
      } elseif ($_POST['albumbrowse'] == 2) {
        $select->order($albumTableName . '.view_count DESC');
      } elseif ($_POST['albumbrowse'] == 3) {
        $select->order($albumTableName . '.like_count DESC');
      } elseif ($_POST['albumbrowse'] == 4) {
        $select->order($albumTableName . '.comment_count DESC');
      } elseif ($_POST['albumbrowse'] == 5) {
        $select->order($albumTableName . '.rating DESC');
      }
    }

    $values = array_merge(array('order' => 'album_id', 'order_direction' => 'DESC'), $values);

    $select->order((!empty($values['order']) ? $values['order'] : 'album_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->assign($values);

    $valuesCopy = array_filter($values);

    // MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setItemCountPerPage(100);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->formValues = $valuesCopy;
  }

  //ACTION FOR MULTI-DELETE ALBUMS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getItem('album', (int) $value)->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function deleteAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->album_id = $this->_getParam('id');
    // Check post
    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $album = Engine_Api::_()->getItem('album', $this->_getParam('id'));
        $album->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin-manage/delete.tpl');
  }

}