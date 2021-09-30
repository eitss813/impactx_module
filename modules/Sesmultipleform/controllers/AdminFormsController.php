<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminFormsController.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_AdminFormsController extends Core_Controller_Action_Admin {
  public function indexAction() {
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm();
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $form = Engine_Api::_()->getItem('sesmultipleform_form', $value)->delete();
        }
      }
    }
    $this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }
  public function orderAction() {
    if (!$this->getRequest()->isPost())
      return;
    $formsTable = Engine_Api::_()->getDbtable('forms', 'sesmultipleform');
    $forms = $formsTable->fetchAll($formsTable->select());
    foreach ($forms as $form) {
      $order = $this->getRequest()->getParam('form_' . $form->form_id);
      if (!$order)
        $order = 999;
      $form->order = $order;
      $form->save();
    }
    return;
  }
  public function manageAction() {
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
            // Setup
     $viewer = Engine_Api::_()->user()->getViewer();
     if($this->_getParam('id',false))
      $this->view->formobj = $formObj = Engine_Api::_()->getItem('sesmultipleform_form', $this->_getParam('id'));
     $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesmultipleform')->profileFieldId();
     $this->view->form = $form = new Sesmultipleform_Form_Admin_Manageform(array('defaultProfileId' => $defaultProfileId, 'formId'=>$formObj->form_id));     
     $form->populate($formObj->toArray());
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
     // return;
    }
    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
	      $values = $form->getValues();
			$formObj->setFromArray($values);
      $formObj->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
       if (isset($_POST['submitsave'])){
				$formUrl = rtrim($this->view->baseUrl(), '/') . '/admin/sesmultipleform/forms';
	    // Redirect
		    return $this->_helper->redirector->gotoUrl($formUrl, array('prependBase' => false));
      }    
  }
    public function emailConfirmationAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
    $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form_id = $id = $this->_getParam('id');
		$this->view->formset = $formset = Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getSetting(array('id'=> $id));
     $this->view->form = $form = new Sesmultipleform_Form_Admin_Confirmationform();     
     if(($formset)){
    	 $itemArray = $formset->toArray();
	     $form->populate($itemArray);	     
     }
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
     // return;
    }
   // Process
    $db = Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getAdapter();
    $db->beginTransaction();
    try {
			if(!$formset){	
				$table = Engine_Api::_()->getDbtable('settings', 'sesmultipleform');
				$formset = $table->createRow();
			}
			$values = $form->getValues();
			$values['form_id'] = $id;
			$formset->setFromArray($values);
			$formset->save();
				$db->commit();
			} catch( Exception $e ) {
				$db->rollBack();
				throw $e;
		}
  }
  public function activeAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form_id = $id = $this->_getParam('id');
    $this->view->active = $active = $this->_getParam('active');   
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->update(array(
          'active' => $active,
              ), array(
          "form_id = ?" => $id,
      ));
      $db->commit();	
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    header('location:' . $_SERVER['HTTP_REFERER']);die;
  }
  public function createFormAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form = $form = new Sesmultipleform_Form_Admin_Form();
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $db = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('forms', 'sesmultipleform');
        $values = $form->getValues();
        $forms = $table->createRow();
        $forms->setFromArray($values);
        $forms->creation_date = date('Y-m-d h:i:s');
          $forms->page_id=7;
        $forms->save();
        $forms->order=$forms->form_id;
				$forms->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
			if($this->_getParam('category',false))
				$redirect = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'index','id'=>$forms->getIdentity()),'admin_default',true);
			else
				$redirect = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesmultipleform', 'controller' => 'settings', 'action' => 'advance-setting','id'=>$forms->getIdentity()),'admin_default',true);
      $this->_forward('success', 'utility', 'core', array(
          'parentRedirect' => $redirect,
          'parentRefresh' => 10,
          'messages' => array('Form created successfully.')
      ));
    }
  }
  public function entryAction(){
		$id = $this->_getParam('id',false);	
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
		 $this->view->formFilter = $formFilter = new Sesmultipleform_Form_Admin_Filter(array('formId'=>$id));
		  $this->view->formObj = Engine_Api::_()->getItem('sesmultipleform_form', $id);
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $entry = Engine_Api::_()->getItem('sesmultipleform_entry', $value);
          $entry->delete();
        }
      }
    }
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    if (isset($_GET) && !empty($_GET['category_id'])) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
      $category_select = $categoryTable->select()
              ->from($categoryTable->info('name'))
              ->where('subcat_id = ?', $_GET['category_id'])
							->where('form_id =?',$id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      $data = '';
      if ($subcategory && $count_subcat) {
        $data = array();
        $data[0] = 'Select';
        foreach ($subcategory as $category) {
          $data[$category['category_id']] = $category['title'];
        }
        if (!empty($data) && $formFilter->getElement('subcat_id'))
          $formFilter->getElement('subcat_id')->addMultiOptions($data);
      }
    }
		if (isset($_GET) && !empty($_GET['subcat_id'])) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
      $category_select = $categoryTable->select()
              ->from($categoryTable->info('name'))
              ->where('subsubcat_id = ?', $_GET['subcat_id'])
							->where('form_id =?',$id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      $data = '';
      if ($subcategory && $count_subcat) {
        $data = array();
        $data[0] = 'Select';
        foreach ($subcategory as $category) {
          $data[$category['category_id']] = $category['title'];
        }
        if (!empty($data) && $formFilter->getElement('subsubcat_id'))
          $formFilter->getElement('subsubcat_id')->addMultiOptions($data);
      }
    }
    $this->view->assign($values);
    $entryTable = Engine_Api::_()->getDbTable('entries', 'sesmultipleform');
    $entryTableName = $entryTable->info('name');
    $select = $entryTable->select()->order('entry_id DESC')->where('form_id =?',$id);
    if (!empty($values['name']))
      $select->where('name LIKE ?', '%' . $values['name'] . '%');
    if (!empty($values['email']))
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    if (!empty($values['creation_date']))
      $select->where('creation_date =?', $values['creation_date']);
    if (!empty($_GET['category_id']))
      $select->where('category_id =?', $_GET['category_id']);
    if (!empty($_GET['subcat_id']))
      $select->where('subcat_id =?', $_GET['subcat_id']);
		if (!empty($_GET['subsubcat_id']))
      $select->where('subsubcat_id =?', $_GET['subsubcat_id']);
    if (!empty($values['description']))
      $select->where('description LIKE ?', '%' . $values['body'] . '%');
    $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator;
    $paginator->setItemCountPerPage(100);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}
   public function deleteEntryAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete This Entry?');
    $form->setDescription('Are you sure that you want to delete this Entry? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');
    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $deleteform = Engine_Api::_()->getItem('sesmultipleform_entry', $id)->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Entry Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-forms/delete-entry.tpl');
  }
	public function viewAction(){
		$this->_helper->layout->setLayout('admin-simple');
    $this->view->entry = $entry = Engine_Api::_()->getItem('sesmultipleform_entry', $this->_getParam('id'));
   	$db = Engine_Db_Table::getDefaultAdapter();
    $this->view->profilefields = $db->query("SELECT GROUP_CONCAT(value) AS `valuesMeta`,IFNULL(TRIM(TRAILING ', ' FROM GROUP_CONCAT(DISTINCT(engine4_sesmultipleform_entry_fields_options.label) SEPARATOR ', ')),engine4_sesmultipleform_entry_fields_values.value) AS `value`, `engine4_sesmultipleform_entry_fields_meta`.`label`, `engine4_sesmultipleform_entry_fields_meta`.`type` FROM `engine4_sesmultipleform_entry_fields_values` LEFT JOIN `engine4_sesmultipleform_entry_fields_meta` ON engine4_sesmultipleform_entry_fields_meta.field_id = engine4_sesmultipleform_entry_fields_values.field_id LEFT JOIN `engine4_sesmultipleform_entry_fields_options` ON engine4_sesmultipleform_entry_fields_values.value = engine4_sesmultipleform_entry_fields_options.option_id  WHERE (engine4_sesmultipleform_entry_fields_values.item_id = ".$entry->entry_id.") AND (engine4_sesmultipleform_entry_fields_values.field_id != 1) GROUP BY `engine4_sesmultipleform_entry_fields_meta`.`field_id`,`engine4_sesmultipleform_entry_fields_options`.`field_id`")->fetchAll();
	}
	public function replyAction(){
		 $this->_helper->layout->setLayout('admin-simple');
    $entry = Engine_Api::_()->getItem('sesmultipleform_entry', $this->_getParam('id'));
    $this->view->form = $form = new Sesmultipleform_Form_Admin_Mail();
    if (!$this->getRequest()->isPost())
      return;
    if (!$form->isValid($this->getRequest()->getPost()))
      return;
    $values = $form->getValues();
    unset($values['body_text']);
    $core_mail = Engine_Api::_()->getApi('mail', 'core');
    if ($entry->owner_id) {
      $core_mail->sendSystem($entry->email, 'sesmultipleform_admin_reply', array(
          'subject' => $values['subject'],
          'body' => $values['body'],
      ));
    } else {
      $core_mail->sendSystem($entry->email, 'sesmultipleform_admin_reply_nonlogged', array(
          'subject' => $values['subject'],
          'body' => $values['body'],
      ));
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('You have successfully reply to member.')
    ));	
	}
  public function deleteFormAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete This Form?');
    $form->setDescription('Are you sure that you want to delete this Form? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');
    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $deleteform = Engine_Api::_()->getItem('sesmultipleform_form', $id)->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Form Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-forms/delete-form.tpl');
  }
	public function downloadCsvAction() {
		$form = $this->_getParam('form_id',false);
    $entryTable = Engine_Api::_()->getDbTable('entries', 'sesmultipleform');
    $select = $entryTable->select()->order('entry_id DESC');
    $entries = $entryTable->fetchAll($select);
    $file = '';
		$form = Engine_Api::_()->getItem('sesmultipleform_form', $form);
		if($form)
			$filename = str_replace(' ','_',$form->title.'_'.$form->form_id);
		else
			$filename = 'sesmultipleform_entries';
		// Submission from
		$filename = $filename . ".csv";		 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Expires: 0");
		$this->exportCSVFile($entries,$filename);
		exit();
  }
	protected function exportCSVFile($records,$filename) {
	// create a file pointer connected to the output stream
	$fh = fopen( 'php://output', 'w' );
	$heading = false;
	$counter = 1;
		if(!empty($records)){
		  foreach($records as $row) {
				$valueVal[$this->view->translate('S.No')] = $counter;
				$valueVal[$this->view->translate('Name')] = $row['first_name'];
				$valueVal[$this->view->translate('email')] = $row['email'];
				$category = Engine_Api::_()->getItem('sesmultipleform_category', $row['category_id']);
				$valueVal[$this->view->translate('category')] = $category ? $category->getTitle() : '-';
				$subcategory = Engine_Api::_()->getItem('sesmultipleform_category', $row['subcat_id']);
				$valueVal[$this->view->translate('2nd-Level Category')] = $subcategory ? $subcategory->getTitle() : '-';
				$subsubcategory = Engine_Api::_()->getItem('sesmultipleform_category', $row['subsubcat_id']);
				$valueVal[$this->view->translate('3rd-Level Category')] = $subsubcategory ? $subsubcategory->getTitle() : '-';				
				$valueVal[$this->view->translate('Message')] = $row['description'];
				$valueVal[$this->view->translate('Creation Date')] = date('Y-m-d H:i:s',strtotime($row['creation_date']));
				$counter++;
				if(!$heading) {
					// output the column headings
					fputcsv($fh, array_keys($valueVal));
					$heading = true;
				}
				// loop over the rows, outputting them
				 fputcsv($fh, array_values($valueVal)); 
		  }
		}
		  fclose($fh);
	}

  public function downloadAttachedFileAction() {
    $storage = Engine_Api::_()->storage()->get($this->_getParam('file_id', ''));
    $path = APPLICATION_PATH . '/' . $storage->storage_path;
    header("Content-Disposition: attachment; filename=" . urlencode(basename($storage->name)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($path), true);
    readfile("$path");
    exit();
  }
}