<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminComplimentController.php 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_AdminComplimentController extends Core_Controller_Action_Admin {
  public function init() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_compliments');
  }

  public function indexAction() {
    $table = Engine_Api::_()->getDbtable('complimentCategories', 'sitemember');
    // GET PAGE LIST.
    $select = $table->select()
      ->order('order ASC');
    $this->view->complimentIcons = $table->fetchAll($select);
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main_compliments', array(), 'sitemember_admin_main_compliments_icon');

  }

  public function addAction() {
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main_compliments', array(), 'sitemember_admin_main_compliments_icon');
    $this->view->form = $form = new Sitemember_Form_Admin_Compliment_Create();
    $form->photo->setAttrib('required', true);
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $table = Engine_Api::_()->getItemTable('sitemember_compliment_category');
      $row = $table->createRow();
      $row->setFromArray($values);
     
      $row->save();
      
      // Set photo
      if (!empty($values['photo'])) {
        $row->setPhoto($form->photo);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitemember/compliment');
  }

  public function editAction() {
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main_compliments', array(), 'sitemember_admin_main_compliments_icon');
    $this->view->form = $form = new Sitemember_Form_Admin_Compliment_Create();
    $form->photo->setAttrib('required', false);
    $reaction_id = $this->_getParam('complimentcategory_id');
    $row = Engine_Api::_()->getItem('sitemember_compliment_category', $reaction_id);
    $form->populate($row->toArray());
    
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();
    $table = Engine_Api::_()->getItemTable('sitemember_compliment_category');
    

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $row->setFromArray($values);
      $row->save();
      
      // Set photo
      if (!empty($values['photo'])) {
        $row->setPhoto($form->photo);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitemember/compliment');
  }

  public function deleteAction() {
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->complimentcategory_id = $complimentcategory_id = $this->_getParam('complimentcategory_id');

    if (!$this->getRequest()->isPost()) {
      return;
    }
    $values = $this->getRequest()->getPost();

    if ($values['confirm'] != $complimentcategory_id) {
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
            $row = Engine_Api::_()->getItem('sitemember_compliment_category', $complimentcategory_id);
            $row->delete();
      
      $db->commit();
    } catch (Exception $ex) {
      $db->rollBack();
      throw $ex;
    }
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted'))
    ));
  }

//ACTION FOR UPDATE ORDER 
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
                foreach ($values['order'] as $key => $value) {
          $row = Engine_Api::_()->getItem('sitemember_compliment_category', (int) $value);
          if (!empty($row)) {
            $row->order = $key + 1;
            $row->save();
         }
        }
        $db->commit();
        $this->_redirect('admin/sitemember/compliment');
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }
  public function levelAction()
  {
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main_compliments', array(), 'sitemember_admin_main_compliments_member_level');
    $this->view->form = $form = new Sitemember_Form_Admin_MemberLevel();
    
       // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;
    $form->level_id->setValue($id);
    if(($level->type == 'public'))
    {
      $form->removeElement('compliment');
      $form->removeElement('submit');
      $form->addNotice('No settings are available for this member level.');
    }
 // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('user', $id, array_keys($form->getValues())));

 // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
    $permissionsTable->setAllowed('user', $id, $values);
      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');

  }
 
}
