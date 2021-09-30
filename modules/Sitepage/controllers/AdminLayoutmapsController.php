<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminProfilemapsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminLayoutmapsController extends Core_Controller_Action_Admin {

	//ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
  public function manageAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_layoutedit');
    $layoutSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage_layout_enable', 1);
    if($layoutSetting == 1) {
  		  //FETCH CATEGORIES
      $table = Engine_Api::_()->getDbTable('categories', 'sitepage');
      $rName = $table->info('name');
      $select = $table->select()
      ->from($rName)
      ->where($rName . ".cat_dependency = ?", 0);
      $row = $table->fetchAll($select);
      $data = Engine_Api::_()->sitepage()->getMappedLayout($row);

      $this->view->heading = "Category to Layout Mapping";
      $this->view->columnName = "Category Name";
      $this->view->data = $data;
    } else if ($layoutSetting == 2) {
      $table = Engine_Api::_()->getDbTable('packages', 'sitepage');
      $rName = $table->info('name');
      $select = $table->select()
      ->from($rName);
      $row = $table->fetchAll($select);
      $data = Engine_Api::_()->sitepage()->getMappedLayout($row);

      $this->view->heading = "Package to Layout Mapping";
      $this->view->columnName = "Package Name";
      $this->view->data = $data;
    }
  }

	//ACTION FOR MAP THE PROFILE
  public function mapAction() {

    //GENERATE THE FORM
    $form = $this->view->form = new Sitepage_Form_Admin_Maplayout();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

				//SAVE THE NEW MAPPING
        $tableName = $this->_getParam('name');
        $table = Engine_Api::_()->getDbTable($tableName, 'sitepage');
        if($tableName == "categories") {
          $row = Engine_Api::_()->getDbTable($tableName, 'sitepage')->getCategory($this->_getParam('id'));
        } else {
          $select = $table->select()->where('package_id = ?', $this->_getParam('id'));
          $row = $table->fetchRow($select);
        }
        $row->layout_id = $values['layout_id'];
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('successfully mapped'))
        ));
    }
  }

  //ACTION FOR MAP THE PROFILE
  public function editAction() {

      //GENERATE THE FORM
      $form = $this->view->form = new Sitepage_Form_Admin_Editmaplayout();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        //SAVE THE NEW MAPPING
        $tableName = $this->_getParam('name');
        $table = Engine_Api::_()->getDbTable($tableName, 'sitepage');
        if($tableName == "categories") {
          $row = Engine_Api::_()->getDbTable($tableName, 'sitepage')->getCategory($this->_getParam('id'));
        } else {
          $select = $table->select()->where('package_id = ?', $this->_getParam('id'));
          $row = $table->fetchRow($select);
        }
        $layout['layout_id'] = $row->layout_id;
        $form->populate($layout);
        if( !$this->getRequest()->isPost() ) {
          return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
          return ;
        }
        $values = $form->getValues();
        $row->layout_id = $values['layout_id'];
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('successfully mapped'))
        ));
  }

	//ACTION FOR DELETE MAPPING 
  public function deleteAction() {

		//GET MAPPING ID
    $this->view->id = $this->_getParam('id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $tableName = $this->_getParam('name');
        $table = Engine_Api::_()->getDbTable($tableName, 'sitepage');
        if($tableName == "categories") {
          $row = Engine_Api::_()->getDbTable($tableName, 'sitepage')->getCategory($this->_getParam('id'));
        } else {
          $select = $table->select()->where('package_id = ?', $this->_getParam('id'));
          $row = $table->fetchRow($select);
        }

				//DELETE MAPPING
        $row->layout_id = "";
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping deleted successfully !'))
        ));
    }
  }
}

?>