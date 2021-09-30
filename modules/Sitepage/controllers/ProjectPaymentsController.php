<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_ProjectPaymentsController extends Core_Controller_Action_Standard {

	public function setPaymentAction()
	{
		//GET THE LOGGEDIN USER INFORMATION
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//ONLY LOGGED IN USER CAN CREATE
		if (!$this->_helper->requireUser()->isValid())
			return;

		$this->view->page_id = $page_id = $this->_getParam('page_id');

		//GET PROJECT ITEM
		$this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

		//IF THERE IS NO PAGE.
		if (empty($sitepage)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		// $this->view->form = new Sitepage_Form_ProjectPayment();
		$this->view->projectPaymentForm = $projectPaymentForm = new Sitepage_Form_ProjectPayment();

		$projectPaymentTable = Engine_Api::_()->getDbtable('projectpayments', 'sitepage');
		$projectPayment = $projectPaymentTable->getPaypalProjectPaymentRow($page_id);

		if (!empty($projectPayment) ) {
			$projectPaymentForm->populate($projectPayment->toArray());
		}

		if ($this->getRequest()->isPost() && $projectPaymentForm->isValid($this->getRequest()->getPost())) {
			$values = $projectPaymentForm->getValues();
			if (empty($projectPayment)) {
				$projectPayments = $projectPaymentTable->createRow();
				$inputs = array(
					'payment_email' =>  $values['payment_email'],
					'payment_username' => $values['payment_username'],
					'payment_password' => $values['payment_password'],
					'payment_signature' => $values['payment_signature'],
					'user_id' => $viewer_id,
					'page_id' =>$page_id,
					'payment_type' => 'PAYPAL',
					'payment_secret_key' => null,
					'payment_publishable_key' => null
				);
				$projectPayments->setFromArray($inputs);
				$projectPayments->save();
				$projectPaymentForm->addNotice('Created successfully.');

			}
			else {
				$projectPaymentModel = $projectPayment;
				$inputs = array(
					'payment_email' =>  $values['payment_email'],
					'payment_username' => $values['payment_username'],
					'payment_password' => $values['payment_password'],
					'payment_signature' => $values['payment_signature'],
					'user_id' => $viewer_id,
					'page_id' =>$page_id,
					'payment_type' => 'PAYPAL',
					'payment_secret_key' => null,
					'payment_publishable_key' => null
				);
				$projectPaymentModel->setFromArray($inputs);
				$projectPaymentModel->save();
				$projectPaymentForm->addNotice('Updated successfully.');
			}

		}

	}

	public function approvePaymentAction(){

		if (empty($_POST) || !isset($_POST['project_id'])) {
			return false;
		}

		$values = $_POST;

		if(empty($values)){
			return;
		}
		$project_id = $values['project_id'];
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
		$owner = $project->getOwner();
		$sender = Engine_Api::_()->user()->getViewer();
		$viewer = Engine_Api::_()->user()->getViewer();
		$view = Zend_Registry::get('Zend_View');
		$host = $_SERVER['HTTP_HOST'];

		$project_link = $view->htmlLink($host . $project->getHref(), $project->title);
		$profile_name = $owner->displayname;

		$settings = Engine_Api::_()->getApi('settings', 'core');
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try {

			if ($project->is_payment_details_editable) {
				$project->is_payment_details_editable = 0;
				//SEND NOTIFICATION TO PROJECT OWNER
				$type = 'sitecrowdfunding_project_payment_edit_disapproved';
				$email_type = 'SITECROWDFUNDING_PROJECT_PAYMENT_EDIT_DISAPPROVED';
			}
			else {
				$project->is_payment_details_editable = 1;
				//SEND NOTIFICATION TO PROJECT OWNER
				$type = 'sitecrowdfunding_project_payment_edit_approved';
				$email_type = 'SITECROWDFUNDING_PROJECT_PAYMENT_EDIT_APPROVED';
			}

			/***
			 *
			 * send notification and email to all project admins
			 *
			 ***/
			$project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
			$list = $project->getLeaderList();
			$list_id = $list['list_id'];

			$listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
			$listItemTableName = $listItemTable->info('name');

			$userTable = Engine_Api::_()->getDbtable('users', 'user');
			$userTableName = $userTable->info('name');

			$selectLeaders = $listItemTable->select()
				->from($listItemTableName, array('child_id'))
				->where("list_id = ?", $list_id)
				->query()
				->fetchAll(Zend_Db::FETCH_COLUMN);
			$selectLeaders[] = $project->owner_id;

			$selectUsers = $userTable->select()
				->from($userTableName)
				->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
				->order('displayname ASC');

			$adminMembers = $userTable->fetchAll($selectUsers);

			foreach($adminMembers as $adminMember){
				$notifyApi->addNotification($adminMember, $sender, $project, $type);
				Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, $email_type, array(
					'project_link' => $project_link,
					'member_name' => $profile_name,
					'project_name' => $project->title,
					'queue' => false
				));
			}

			$project->save();
			$db->commit();



		}catch (Exception $e){
			$db->rollBack();
			throw $e;
		}


		return true;
	}

	public function setDonateReceiptAction() {
		//GET THE LOGGEDIN USER INFORMATION

		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//ONLY LOGGED IN USER CAN CREATE
		if (!$this->_helper->requireUser()->isValid())
			return;

		$this->view->page_id = $page_id = $this->_getParam('page_id');
		//GET PROJECT ITEM
		$this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

		//IF THERE IS NO PAGE.
		if (empty($sitepage)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		$this->view->donateReceiptForm = $donateReceiptForm = new Sitepage_Form_DonateReceipt();

		$pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$sitePageData = $pageTable->fetchRow($pageTable->select()->where('page_id = ?', $page_id));

		//after submit post request
		if ($this->getRequest()->isPost() && $donateReceiptForm->isValid($this->getRequest()->getPost())) {

			$logoValue = $donateReceiptForm->donate_receipt_logo->getValue();
			$values = $donateReceiptForm->getValues();
			if (empty($sitePageData->donate_receipt_logo) || strpos( $logoValue, '.' )) {
				$table = Engine_Api::_()->getDbTable('organizations', 'sitecrowdfunding');
				$organization = $table->createRow();
				$file_id =  $organization->setLogo($donateReceiptForm->donate_receipt_logo);
			}
			else if(!empty($sitePageData->donate_receipt_logo)) {
				$file_id = $sitePageData->donate_receipt_logo;
			}
			$file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, 'thumb.cover');
			$this->view->logo = $logo = $file ?  $file->map() :null;

			if(!empty($sitePageData)) {
				$sitePageModel = $sitePageData;
				$inputs = array(
					'donate_receipt_logo' => $file_id,
					'donate_receipt_location' => $values['donate_receipt_location'],
					'donate_receipt_desc' => $values['donate_receipt_desc'],
					'page_id' =>$page_id
				);

				$sitePageModel->setFromArray($inputs);
				$sitePageModel->save();
				$donateReceiptForm->addNotice('Updated successfully.');
			}
		}
		else {
			$donateReceiptForm->populate($sitePageData->toArray());
			$type ='thumb.cover';
			$file_id = null;

			if(!empty($sitePageData->donate_receipt_logo)) {
				$file_id = $sitePageData->donate_receipt_logo;

			}
			if($file_id)
				$file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, $type);

			$this->view->logo = $logo = $file ?  $file->map() :null;

		}
	}

	public function setStripePaymentAction(){

	    //GET THE LOGGEDIN USER INFORMATION
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//ONLY LOGGED IN USER CAN CREATE
		if (!$this->_helper->requireUser()->isValid())
			return;

		$this->view->page_id = $page_id = $this->_getParam('page_id');

		//GET PROJECT ITEM
		$this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

		//IF THERE IS NO PAGE.
		if (empty($sitepage)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		// $this->view->form = new Sitepage_Form_ProjectPayment();
		$this->view->projectPaymentForm = $projectPaymentForm = new Sitepage_Form_ProjectStripePayment();

		$projectPaymentTable = Engine_Api::_()->getDbtable('projectpayments', 'sitepage');
		$projectPayment = $projectPaymentTable->getStripeProjectPaymentRow($page_id);

		if (!empty($projectPayment) ) {
			$projectPaymentForm->populate(array(
				'secret' => $projectPayment['payment_secret_key'],
				'publishable' => $projectPayment['payment_publishable_key']
			));
			$projectPaymentForm->populate($projectPayment->toArray());
		}

		if ($this->getRequest()->isPost() && $projectPaymentForm->isValid($this->getRequest()->getPost())) {
			$values = $projectPaymentForm->getValues();
			if (empty($projectPayment)) {
				$projectPayments = $projectPaymentTable->createRow();
				$inputs = array(
					'payment_type' => 'STRIPE',
					'payment_secret_key' =>  $values['secret'],
					'payment_publishable_key' => $values['publishable'],
					'user_id' => $viewer_id,
					'page_id' =>$page_id
				);
				$projectPayments->setFromArray($inputs);
				$projectPayments->save();
				$projectPaymentForm->addNotice('Created successfully.');

			}
			else {
				$projectPaymentModel = $projectPayment;
				$inputs = array(
					'payment_type' => 'STRIPE',
					'payment_secret_key' =>  $values['secret'],
					'payment_publishable_key' => $values['publishable'],
					'user_id' => $viewer_id,
					'page_id' =>$page_id
				);
				$projectPaymentModel->setFromArray($inputs);
				$projectPaymentModel->save();
				$projectPaymentForm->addNotice('Updated successfully.');
			}

		}

	}

	public function setPaymentSettingsAction(){

		//GET THE LOGGEDIN USER INFORMATION
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//ONLY LOGGED IN USER CAN CREATE
		if (!$this->_helper->requireUser()->isValid())
			return;

		$this->view->page_id = $page_id = $this->_getParam('page_id');

		//GET PROJECT ITEM
		$this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

		//IF THERE IS NO PAGE.
		if (empty($sitepage)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		$this->view->paymentSettingsForm = $paymentSettingsForm = new Sitepage_Form_PaymentSettings();

		$paymentSettingsForm->populate(array(
			'payment_action_label' => $sitepage['payment_action_label'],
			'payment_is_tax_deductible' =>  $sitepage['payment_is_tax_deductible'],
			'payment_tax_deductible_label' => $sitepage['payment_tax_deductible_label']
		));

		if ($this->getRequest()->getPost()) {
			if ($paymentSettingsForm->isValid($this->getRequest()->getPost())) {
				$value = $paymentSettingsForm->getValues();
				$inputs = array(
					'payment_action_label' => $value['payment_action_label'],
					'payment_is_tax_deductible' =>  $value['payment_is_tax_deductible'],
					'payment_tax_deductible_label' => $value['payment_tax_deductible_label']
				);
				$sitepage->setFromArray($inputs);
				$sitepage->save();
				$paymentSettingsForm->addNotice('Updated successfully.');
			}
		}
	}
}
?>
