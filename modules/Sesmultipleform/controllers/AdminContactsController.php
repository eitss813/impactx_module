<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminContactsController.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_AdminContactsController extends Core_Controller_Action_Admin {

  public function indexAction() {
  
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_contacts');
    
	  $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('keycontacts', 'sesmultipleform')->getContacts();

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $contact = Engine_Api::_()->getItem('sesmultipleform_keycontact', $value)->delete();
        }
      }
    }
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }
  
    public function createContactAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form = $form = new Sesmultipleform_Form_Admin_Contact();

    if ($this->getRequest()->isPost()) {
          if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $db = Engine_Api::_()->getDbtable('keycontacts', 'sesmultipleform')->getAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('keycontacts', 'sesmultipleform');
        $values = $form->getValues();
				$forms = $table->createRow();
				$forms->setFromArray($values);
				$forms->user_id = $values['toValues'];
				$forms->creation_date = date('Y-m-d h:i:s');
				$forms->save();
				$forms->order=$forms->keycontact_id;
				$forms->save();	
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Contact created successfully.')
      ));
    }
  }
    public function editContactAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    $id = $this->_getParam('id');
    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
 
	  $sesmultipleform = Engine_Api::_()->getItem('sesmultipleform_keycontact', $id);
		$this->view->form = $form = new Sesmultipleform_Form_Admin_EditContact();

    $form->populate($sesmultipleform->toArray());
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
			    $values = $form->getValues();
	        $sesmultipleform->setFromArray($values);
	        $sesmultipleform->save();
		      $sesmultipleform->save();
		      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Edit successfully.')
      ));
  }
  
    public function activeAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->keycontact_id = $id = $this->_getParam('id');
    $this->view->active = $active = $this->_getParam('active');

   
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable('keycontacts', 'sesmultipleform')->update(array(
          'active' => $active,
              ), array(
          "keycontact_id = ?" => $id,
      ));
      $db->commit();	
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    header('location:' . $_SERVER['HTTP_REFERER']);
  }
    public function orderAction() {

    if (!$this->getRequest()->isPost())
      return;

    $contactsTable = Engine_Api::_()->getDbtable('keycontacts', 'sesmultipleform');
    $contacts = $contactsTable->fetchAll($contactsTable->select());
    foreach ($contacts as $contact) {
      $order = $this->getRequest()->getParam('form_' . $contact->keycontact_id);

      if (!$order)
        $order = 999;
      $contact->order = $order;
      $contact->save();
    }
    return;
  }
 
  public function deleteContactAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete This Contact?');
    $form->setDescription('Are you sure that you want to delete this Contact? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');
    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $deletecontact = Engine_Api::_()->getItem('sesmultipleform_keycontact', $id)->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Contact Delete Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-contacts/delete-contact.tpl');
  }
  

    public function searchAction() {  
		    $text = $this->_getParam('text', null);
		    $table = Engine_Api::_()->getDbtable('users', 'user');
		    $select = $table->select()->where('username LIKE ? OR email LIKE ? OR displayname LIKE ?', '%' . $text . '%');
		    $select->limit('10');
		      $ids = array();
		      foreach( $select->getTable()->fetchAll($select) as $user ) {
		        $data[] = array(
		          'type'  => 'user',
		          'id'    => $user->getIdentity(),
		          'guid'  => $user->getGuid(),
		          'label' => $user->getTitle(),
		          'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
		          'url'   => $user->getHref(),
		        );
		      }
		    return $this->_helper->json($data);
	  } 
}
