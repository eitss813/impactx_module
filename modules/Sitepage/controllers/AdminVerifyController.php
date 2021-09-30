<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminVerifyController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminVerifyController extends Core_Controller_Action_Admin {

 	public function indexAction() {

	  	//GET NAVIGATION
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_verify');
	    //GET NAVIGATION
	    $this->view->subnavigation = $subnavigation = Engine_Api::_()->getApi('menus', 'core')
	            ->getNavigation('sitepage_admin_main_verify', array(), 'sitepage_admin_main_verify_settings');


	    $this->view->form = $form = new Engine_Form();

	    $form->setTitle("General Settings");
	    $form->setDescription("These settings affect all members in your community.");

	    $form->addElement('Radio','sitepage_verify_enabled',array(
	    	'label' => 'Page Verification',
	    	'description' => 'Allow the site members to verify the pages.',
	    	'multiOptions' => array(
	    		1 => 'Yes',
	    		0 => 'No',
	    	),
	    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.enabled', 0),
	    ));

	    $form->addElement('Radio','sitepage_comment_verifypopup',array(
	    	'label' => 'Adding Comments',
	    	'description' => 'Do you want comments to be enabled while verifying a page? (If enabled, then users will be able to enter details while verifying pages, or mention why they are verifying the pages.)',
	    	'multiOptions' => array(
	    		1 => 'Yes',
	    		0 => 'No',
	    	),
	    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.comment.verifypopup', 0),
	    ));

	    $form->addElement('Radio','sitepage_admin_approve',array(
	    	'label' => 'Admin Approved Verification',
	    	'description' => 'Do you want the verifications be approved by the admin? (If enabled, the verifications will be listed under Approve Verifications in admin panel from where admin can approve user verified pages.)',
	    	'multiOptions' => array(
	    		1 => 'Yes',
	    		0 => 'No',
	    	),
	    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admin.approve', 0),
	    ));

	    $form->addElement('Text','sitepage_verify_limit',array(
	    	'label' => 'Verification Threshold',
	    	'description' => 'Enter the threshold limit of verification count after which a page will be marked as "Verified".',
	    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3),
	    	'validators' => array(
	            array('Int', true),
	            array('GreaterThan', true, array(0)),
	        ),
	    ));

	    $form->addElement('Button','submit',array(
	    	'label' => 'Save Changes',
	    	'type' => 'submit',
	        'ignore' => true,
	        'decorators' => array(
	            'ViewHelper',
	        ),
	    ));

	    if ($this->getRequest()->isPost()) {
	    	if ($form->isValid($this->_getAllParams())) {
	    		Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage_verify_enabled',$_POST['sitepage_verify_enabled']);
			   	Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage_comment_verifypopup',$_POST['sitepage_comment_verifypopup']);
			   	Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage_admin_approve',$_POST['sitepage_admin_approve']);
			   	Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage_verify_limit',$_POST['sitepage_verify_limit']);
	    	}
	    	$form->populate($_POST);

	    	$form->addNotice('Your settings have been saved.');
	    }
	}


	public function manageAction() {
		//GET NAVIGATION
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_verify');
	    //GET NAVIGATION
	    $this->view->subnavigation = $subnavigation = Engine_Api::_()->getApi('menus', 'core')
	            ->getNavigation('sitepage_admin_main_verify', array(), 'sitepage_admin_main_verify_manage');

	    // CHECK POST
	    if ($this->getRequest()->isPost()) {
	      $values = $this->getRequest()->getPost();
	      foreach ($values as $value) {
	        $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $value);
	        $verifyObj->delete();
	      }
	    }
	            
	    $this->view->paginator = Engine_Api::_()->getDbTable('verifies','sitepage')->getVerifyPaginator(array('admin_approve' => 1));

	}

	// GET DETAIL OF USER VERIFY ENTRY.
	public function detailAction() {

		//GET RESOURCE ID AND VERIFY LIMIT OF USER WHOM DETAIL WE WANT TO SEE.
		//GET DETAILS OF RESOURCE AND POSTER
		$verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));
		$this->view->comments = $verifyObj->comments;
		$this->view->verify_date = $verifyObj->creation_date;
		$this->view->resourceObj = $resource = Engine_Api::_()->getItem('sitepage_page', $verifyObj->resource_id);
		$this->view->posterObj = $poster = Engine_Api::_()->getItem('user', $verifyObj->poster_id);
		$this->view->resource_title = $resource->getTitle();
		$this->view->poster_title = $poster->getTitle();
		$this->view->verify_count = Engine_Api::_()->getDbtable('verifies', 'sitepage')->getVerifyCount($verifyObj->resource_id);
	}


	//CHANGE SATAUS OF USER VERIFY ENTRY BY ADMIN
	public function statusAction() {
		try {
		  $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));
		  $verifyObj->status = !empty($verifyObj->status) ? 0 : 1;
		  $verifyObj->save();
		} catch (Exception $e) {
		  throw $e;
		}
		$this->_redirect('admin/sitepage/verify/manage');
	}


	//EDIT VERIFY ENTRY
	public function editAction() {
		$this->_helper->layout->setLayout('admin-simple');

		$verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));
		$this->view->form = $form = new Sitepage_Form_Admin_Edit();

		//POPULATE EDIT FORM
		$form->populate($verifyObj->toarray());

		// CHECK POST
		if ($this->getRequest()->isPost()) {
		  $db = Engine_Db_Table::getDefaultAdapter();
		  $db->beginTransaction();

		  try {

		    $verifyObj->comments = $_POST['comments'];
		    // UPDATE THE VERIFY ENTRY FROM DATABASE
		    $verifyObj->save();
		    $db->commit();
		  } catch (Exception $e) {
		    $db->rollBack();
		    throw $e;
		  }
		  // AFTER EDIT FORWARD TO THE SAME PAGE
		  $this->_forward('success', 'utility', 'core', array(
		      'smoothboxClose' => 300,
		      'parentRefresh' => 300,
		      'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have changed comments successfully.'))
		  ));
		}
	}


	// DELETE SINGLE VERIFY ENTRY
	public function deleteAction() {

		// CHECK POST
		if ($this->getRequest()->isPost()) {

		  $db = Engine_Db_Table::getDefaultAdapter();
		  $db->beginTransaction();
		  try {
		    $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));

		    // //DELETE ACTIVITY FEED
		    // $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'siteverify_new', 'subject_id = ?' => $verifyObj->poster_id, 'object_id = ?' => $verifyObj->resource_id));
		    // if (!empty($action_id)) {
		    //   $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
		    //   $action->delete();
		    // }
		    // DELETE THE VERIFY ENTRY FROM DATABASE
		    $verifyObj->delete();

		    $db->commit();
		  } catch (Exception $e) {
		    $db->rollBack();
		    throw $e;
		  }

		  // AFTER DELETE FORWARD TO THE SAME PAGE
		  $this->_forward('success', 'utility', 'core', array(
		      'smoothboxClose' => 300,
		      'parentRefresh' => 300,
		      'messages' => array(Zend_Registry::get('Zend_Translate')->_('This verify entry has been deleted successfully.'))
		  ));
		} 
			// $this->renderScript('/admin-verify/delete.tpl');
	}


	public function approveAction()
	{
		//GET NAVIGATION
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_verify');
	    //GET NAVIGATION
	    $this->view->subnavigation = $subnavigation = Engine_Api::_()->getApi('menus', 'core')
	            ->getNavigation('sitepage_admin_main_verify', array(), 'sitepage_admin_main_verify_approve');

	    // CHECK POST
	    if ($this->getRequest()->isPost()) {
	      $values = $this->getRequest()->getPost();
	      foreach ($values as $value) {
	        $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $value);
	        $verifyObj->delete();
	      }
	    }

	    $this->view->paginator = Engine_Api::_()->getDbTable('verifies','sitepage')->getVerifyPaginator(array('admin_approve' => 0));
	}



	// TO APPROVE PAGE VERIFY ENTRY BY ADMIN
	public function approvePageAction() {

		$this->_helper->layout->setLayout('admin-simple');

		// CHECK POST
		if ($this->getRequest()->isPost()) {
		  $db = Engine_Db_Table::getDefaultAdapter();
		  $db->beginTransaction();

		  try {
		    //  IF ADMIN APPROVES THEN ADMIN APPROVE WILL BE FROM 0 TO 1.
		    $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));
		    $verifyObj->admin_approve = 1;
		    $verifyObj->status = 1;

		    //UPDATE THE VERIFY ENTRY FROM DATABASE
		    $verifyObj->save();

		    // NOTIFICATION AND ACTIVITY FEED WORK
		    $resource = Engine_Api::_()->getItem('sitepage_page', $verifyObj->resource_id);
		    $pageOwner = Engine_Api::_()->getItem('user', $resource->owner_id);
		    $poster = Engine_Api::_()->getItem('user', $verifyObj->poster_id);
		    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($pageOwner, $poster, $verifyObj, 'sitepage_verify_new');
		    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($poster, $resource, $verifyObj, 'sitepage_verify_admin_approve');
		    Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($poster, $resource, 'sitepage_verify_new');

		    //COMMIT
		    $db->commit();
		  } catch (Exception $e) {
		    $db->rollBack();
		    throw $e;
		  }
		  // AFTER APPROVE FORWARD TO THE SAME PAGE
		  $this->_forward('success', 'utility', 'core', array(
		      'smoothboxClose' => 400,
		      'parentRefresh' => 400,
		      'messages' => array(Zend_Registry::get('Zend_Translate')->_('This verification request has been approved successfully.'))
		  ));
		}
		// OUTPUT
		// $this->renderScript('admin-verify/approve-page.tpl');
	}

}