<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Controller.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Widget_ClaimBlogController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->form = $form = new Sesblog_Form_Claim();
    
    if(isset($_POST))
    $form->populate($_POST);

    if (empty($_POST)) 
    return;
    
    $validator = new Zend_Validate_EmailAddress();
    $validator->getHostnameValidator()->setValidateTld(false);
    if (!$validator->isValid($_POST['user_email'])) {
      $errorMessage = '<div class="sesblog_claim_form_tip"><div class="sesblog_error_message"><i class="fa fa-times-circle"></i><span>'.$this->view->translate("Invalid sender email address, which you have put in email field.").'</span></div></div>';
      echo json_encode(array('status'=>false,'message'=>$errorMessage));die;
    }
    
		// Process
		$table = Engine_Api::_()->getDbtable('claims', 'sesblog');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try {
			// Create Claim
			$viewer = Engine_Api::_()->user()->getViewer();
			$sesblogClaim = $table->createRow();
			$sesblogClaim->user_id = $viewer->getIdentity();
			$sesblogClaim->blog_id = $_POST['blog_id'];
			$sesblogClaim->title = $_POST['title'];
			$sesblogClaim->user_email = $_POST['user_email'];
			$sesblogClaim->user_name = $_POST['user_name'];
			$sesblogClaim->contact_number = $_POST['contact_number'];
			$sesblogClaim->description = $_POST['description'];
			$sesblogClaim->save();
			// Commit
			$db->commit();
		}
		catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
    
    $mail_settings = array('sender_title' => $_POST['user_name']);
    $body = '';
    $body .= $this->view->translate("Email: %s", $_POST['user_email']) . '<br />';
    if(isset($_POST['contact_number']) && !empty($_POST['contact_number']))
    $body .= $this->view->translate("Claim Owner Contact Number: %s", $_POST['contact_number']) . '<br />';
    $body .= $this->view->translate("Claim Reason: %s", $_POST['description']) . '<br /><br />';
    $mail_settings['message'] = $body;
    $blogItem = Engine_Api::_()->getItem('sesblog_blog', $_POST['blog_id']);
    $blogOwnerId = $blogItem->owner_id;
    $owner = $blogItem->getOwner();
    $blogOwnerEmail = Engine_Api::_()->getItem('user', $blogOwnerId)->email;
    $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($blogOwnerEmail, 'sesblog_blog_owner_claim', $mail_settings);
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($fromAddress, 'sesblog_site_owner_for_claim', $mail_settings);
    
    //Send notification to blog owner
    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $blogItem, 'sesuser_claim_blog');
    
    //Send notification to all superadmins
    $getAllSuperadmins = Engine_Api::_()->user()->getSuperAdmins();
    foreach($getAllSuperadmins as $getAllSuperadmin) {
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($getAllSuperadmin, $viewer, $blogItem, 'sesuser_claimadmin_blog');    
    }
    
    echo json_encode(array('status'=>true,'message'=>'<div class="sesblog_claim_form_tip"><div class="sesblog_success_message"><i class="fa fa-check"></i><span>'.$this->view->translate('Your request for claim has been sent to site owner. He will contact you soon.').'</span></div></div>'));die;
  }

}
