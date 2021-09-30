<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Widget_FormsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
		$formtype = $this->_getParam('formtype',$this->_getParam('formid',$this->_getParam('form_id',null)));
		//return if no form id associated with this widget
		$this->view->formObj = $formObj = Engine_Api::_()->getItem('sesmultipleform_form',$formtype);
		if(!$formtype || !$formObj->active)
			$this->setNoRender();
		$this->view->redirect = $this->_getParam('redirect',false);
		$this->view->formsettings = $formsettings =  Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getSetting(array('id'=>$formtype));
		$this->view->hideform = $this->_getParam('hideform',1);
		$this->view->closepopup = $this->_getParam('closepopup',1);
		//global banned code
    $captcha = '';
    if(isset($_POST['g-recaptcha-response'])){
      $captcha=$_POST['g-recaptcha-response'];
      if(!$captcha){
       echo "captcha";die;
      }
    }
		$bannedips = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.ipaddressban', "");
		if (in_array($_SERVER['REMOTE_ADDR'], explode(",", $bannedips)) || in_array($_SERVER['REMOTE_ADDR'], explode(",", $formsettings->ipaddress_ban)))
		 $this->setNoRender();
		$isSmoothbox = $this->view->isSmoothbox = $this->_getParam('typesmoothbox',false);
		$is_ajax = $this->view->is_ajax = $this->_getParam('is_ajax',false);   
    $this->view->formtype = $formtype;
		$this->view->identity = $id = $this->view->identity ? $this->view->identity : $this->_getParam('identity',null);
	  $sesmultipleform_forms = Zend_Registry::isRegistered('sesmultipleform_forms') ? Zend_Registry::get('sesmultipleform_forms') : null;
    if(empty($sesmultipleform_forms)) {
	    return $this->setNoRender();
    }
		$this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesmultipleform')->profileFieldId();
 	  $this->view->form = $form = new Sesmultipleform_Form_User_Form(array('formId' => $formtype,'widgetId'=> $id,'defaultProfileId'=>$defaultProfileId,'formSettings'=>$formsettings));
		if(isset($_POST)){
			$form->populate($_POST);
		}
		if(!$is_ajax){
			$categories = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getCategory(array('column_name' => '*','id'=>$formObj->form_id));
			if(count($categories) == 1){
				$this->view->isSingleCategory = 1;
				$categories = $categories->toArray();
				if(isset($categories[0]['category_id']) && $categories[0]['category_id'] != 0) 
					$this->view->category_id =$categories[0]['category_id'];
				else
					$this->view->category_id = 0;
					$this->view->subcat_id = 0;
					$this->view->subsubcat_id = 0;
			}
		}
		if($isSmoothbox || $this->_getParam('removeElem',null)){
			$this->getElement()->removeDecorator('Container');  	
			$this->getElement()->removeDecorator('Title');  	
			return;	
		}
    if (empty($_POST)) {
      return;
    }
		//first  emailed to the SuperAdmin by default
		$settings = Engine_Api::_()->getApi('settings', 'core');
		//check form lavel settind of email.
		if($formsettings->message_reciver_email)
			$adminEmail = $formsettings->message_reciver_email;
		else
			$adminEmail = $settings->getSetting('core.mail.contact');
		$adminTitle = $settings->getSetting('core.mail.name');
		if (!$adminEmail) {
			$users_table = Engine_Api::_()->getDbtable('users', 'user');
			$users_select = $users_table->select()
							->where('level_id = ?', 1)
							->where('enabled >= ?', 1);
			$super_admin = $users_table->fetchRow($users_select);
			$adminEmail = $super_admin->email;
			$adminTitle = $super_admin->getTitle();
		}
		//save values
		$db = Engine_Api::_()->getDbtable('entries', 'sesmultipleform')->getAdapter();
    $db->beginTransaction();
    try {
		// Create entry
		$table = Engine_Api::_()->getDbtable('entries', 'sesmultipleform');
		$entry = $table->createRow();
		$values = $form->getValues();
		$values['form_id'] = $formtype;
		$newValues = $values;
		$values = array();
		foreach($newValues as $key=>$value){
			$values[str_replace('_'.$id,'',$key)] = $value;
		}
		unset($values['captcha']);
		if(empty($values['category_id']))
			$values['category_id'] = 0;
		if(empty($values['subsubcat_id']))
			$values['subsubcat_id'] = 0;
		if(empty($values['subcat_id']))
			$values['subcat_id'] = 0;
		$values['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$values['owner_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
		$entry->setFromArray($values);
    $entry->save();		
		$customfieldform = $form->getSubForm('fields');
		$customfieldform->setItem($entry);
		$customfieldform->saveValues(array('widget'=>$id));
		//upload attachment work
      $file_id = 0;
      if (isset($_FILES['file_'.$id]['name']) && !empty($_FILES['file_'.$id]['name'])) {
        $storage = $entry->setAttachment($form->{'file_'.$id}, $formtype);
        $file_id = $storage->file_id;
        $service_id = Engine_Api::_()->getItem('storage_file', $file_id)->service_id;
        if ($service_id == 1) {
          $path = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Engine_Api::_()->getItem('storage_file', $file_id)->map();
        } else {
          $path = Engine_Api::_()->getItem('storage_file', $file_id)->map();
        }
        if ($file_id) {
          $entry->file_id = $file_id;
          $entry->save();
        }
      }
			//email work
			$senderName = '';
			if(isset($values['first_name'])){
				$senderName = $values['first_name'];
			}
			if(isset($values['last_name'])){
				$senderName = $senderName.' '. $values['last_name'];
			}
			if(!$senderName)
				$senderName = $this->view->translate('Anonymous');
      $mail_settings = array(
          'sender_title' => $senderName,
      );
      if (isset($values['category_id']) && $values['category_id'])
      	$sescategory_name =  Engine_Api::_()->getItem('sesmultipleform_category', $values['category_id'])->title;

      if (isset($values['subcat_id']) && $values['subcat_id'])
        $sessubcategory_name = Engine_Api::_()->getItem('sesmultipleform_category', $values['subcat_id'])->title;
			
			if (isset($values['subsubcat_id']) && $values['subsubcat_id'])
        $sessubsubcategory_name = Engine_Api::_()->getItem('sesmultipleform_category', $values['subsubcat_id'])->title;
			
			//fetch custom field
			$db = Engine_Db_Table::getDefaultAdapter();
      $profilefielddata = $db->query("SELECT GROUP_CONCAT(value) AS `valuesMeta`,IFNULL(TRIM(TRAILING ', ' FROM GROUP_CONCAT(DISTINCT(engine4_sesmultipleform_entry_fields_options.label) SEPARATOR ', ')),engine4_sesmultipleform_entry_fields_values.value) AS `value`, `engine4_sesmultipleform_entry_fields_meta`.`label`, `engine4_sesmultipleform_entry_fields_meta`.`type` FROM `engine4_sesmultipleform_entry_fields_values` LEFT JOIN `engine4_sesmultipleform_entry_fields_meta` ON engine4_sesmultipleform_entry_fields_meta.field_id = engine4_sesmultipleform_entry_fields_values.field_id LEFT JOIN `engine4_sesmultipleform_entry_fields_options` ON engine4_sesmultipleform_entry_fields_values.value = engine4_sesmultipleform_entry_fields_options.option_id  WHERE (engine4_sesmultipleform_entry_fields_values.item_id = ".$entry->entry_id.") AND (engine4_sesmultipleform_entry_fields_values.field_id != 1) GROUP BY `engine4_sesmultipleform_entry_fields_meta`.`field_id`,`engine4_sesmultipleform_entry_fields_options`.`field_id`")->fetchAll();
			$profile_field_value = '';
			if(count($profilefielddata)){
					foreach($profilefielddata as $valData){
						$profile_field_value .= $this->view->translate($valData['label'].": %s", $valData['value']) . '<br />';	
					}
			}
      			
			$body = '';
      if (empty($_FILES['file_'.$id]['name'])) {
        $body .= $this->view->translate("Email: %s", $values['email']) . '<br />';
        if (isset($sescategory_name) && !empty($sescategory_name))
          $body .= $this->view->translate("Category Name: %s", $sescategory_name) . '<br />';
        if (isset($sessubcategory_name) && !empty($sessubcategory_name))
          $body .= $this->view->translate("2nd-Level Category Name: %s", $sessubcategory_name) . '<br />';
				if (isset($sessubsubcategory_name) && !empty($sessubsubcategory_name))
          $body .= $this->view->translate("3rd-Level Category Name: %s", $sessubsubcategory_name) . '<br />';				
				
				if (isset($profile_field_value) && !empty($profile_field_value))
          $body .= $profile_field_value . '<br /><br />';
				
				if(isset($values['description']))
        	$body .= $this->view->translate("Message: %s", $values['description']) . '<br /><br />';
        $mail_settings['message'] = $body;
        //Simple mail to admin
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminEmail, 'sesmultipleform_email_simple', $mail_settings);
        //Start Copy mail to sender
        if (isset($_POST['sesmultipleform_copymain_'.$id]) && !empty($_POST['sesmultipleform_copymain_'.$id]))
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['email'], 'sesmultipleform_email_simple', $mail_settings);
      }//End Copy mail to sender
      else {
        // Work of attachement
        $body = $this->view->translate("Hello %s", $adminTitle) . ',' . '<br /><br />';
        $body .= $this->view->translate("Email: %s", $values['email']) . '<br />';
        if (isset($sescategory_name) && !empty($sescategory_name))
          $body .= $this->view->translate("Category Name: %s", $sescategory_name) . '<br />';
         if (isset($sessubcategory_name) && !empty($sessubcategory_name))
          $body .= $this->view->translate("2nd-Level Category Name: %s", $sessubcategory_name) . '<br />';
				if (isset($sessubsubcategory_name) && !empty($sessubsubcategory_name))
          $body .= $this->view->translate("3rd-Level Category Name: %s", $sessubsubcategory_name) . '<br />';		
        if (isset($profile_field_value) && !empty($profile_field_value))
          $body .= $profile_field_value . '<br /><br />';
				if(isset($values['description']))
        	$body .= $this->view->translate("Message: %s", $values['description']) . '<br /><br />';

        $subject = $this->view->translate("%s has sent you a message using the %s form.", $senderName,$formObj->title);
        $mailApi = Engine_Api::_()->getApi('mail', 'core');
        $mail = $mailApi->create();
        $mail->setFrom($adminEmail, $adminTitle)
                ->setSubject($subject)
                ->setBodyHtml($body);

        $mail->addTo($adminEmail);
        $handle = @fopen($path, "r");
				$content = '';
        while (($buffer = fgets($handle)) !== false) {
          $content .= $buffer;
        }
        $attachment = $mail->createAttachment($content);
        $attachment->filename = $_FILES['file_'.$id]['name'];
        $mailApi->send($mail);

        //Start Copy mail to sender
        if (isset($_POST['sesmultipleform_copymain_'.$id]) && !empty($_POST['sesmultipleform_copymain_'.$id])) {
          $body = $this->view->translate("Hello %s", $senderName) . ',' . '<br /><br />';
          $body .= $this->view->translate("Email: %s", $values['email']) . '<br /><br />';
          if (isset($sescategory_name) && !empty($sescategory_name))
            $body .= $this->view->translate("Category Name: %s", $sescategory_name) . '<br /><br />';
          if (isset($sessubcategory_name) && !empty($sessubcategory_name))
          $body .= $this->view->translate("2nd-Level Category Name: %s", $sessubcategory_name) . '<br />';
				if (isset($sessubsubcategory_name) && !empty($sessubsubcategory_name))
          $body .= $this->view->translate("3rd-Level Category Name: %s", $sessubsubcategory_name) . '<br />';		
          if (isset($profile_field_value) && !empty($profile_field_value))
            $body .= $profile_field_value . '<br /><br />';
					if(isset($values['description']))
        	$body .= $this->view->translate("Message: %s", $values['description']) . '<br /><br />';

          $sitetitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
          $subject = $this->view->translate("This is copy of the mail which you sent using the %s form of %s.", $sitetitle,$formObj->title);
          $mailApi = Engine_Api::_()->getApi('mail', 'core');
          $mail = $mailApi->create();
          $mail->setFrom($values['email'], $senderName)
                  ->setSubject($subject)
                  ->setBodyHtml($body);

          $mail->addTo($values['email']);
          $handle = @fopen($path, "r");
          while (($buffer = fgets($handle)) !== false) {
            $content .= $buffer;
          }
          $attachment = $mail->createAttachment($content);
          $attachment->filename = $_FILES['file_'.$id]['name'];
          $mailApi->send($mail);
        }
      }
      // End work of attachment
      // Confirmation email work.
      if ($formsettings->email_confirmation) {
        $conf_msg = $formsettings->confirmation_subject;
        $conf_subject = $formsettings->confirmation_subject;
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['email'], 'sesmultipleform_admin_confirmation', array(
            'subject' => $conf_subject,
            'body' => $conf_msg,
        ));
      }
			$db->commit();
			$message = $formsettings->success_message ? $formsettings->success_message : $this->view->translate('Thank you for contacting us! We will get back to you soon.');
			$successMessage = '<div class="sesmultipleform_form_'.$id.'"><div class="sesmultipleform_success_message"><i class="fa fa-check"></i><span>'.$this->view->translate($message).'</span></div></div>';
			echo json_encode(array('status'=>true,'message'=>$successMessage));die;
		} catch (Engine_Image_Exception $e) {
      $db->rollBack();
			$errorMessage = '<div class="sesmultipleform_form_'.$id.'"><div class="sesmultipleform_error_message"><i class="fa fa-times-circle"></i><span>'.$this->view->translate("Something wrong happened,please try again later").'</span></div></div>';
      echo json_encode(array('status'=>false,'message'=>$errorMessage));die;
    }
  }
}