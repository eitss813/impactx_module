<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Advance.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Settings_Advance extends Engine_Form
{
  public function init()
  {
  $this
      ->setTitle('Edit Form Settings')
      ->setDescription('Here, you can edit the settings for the form.');
   $settings = Engine_Api::_()->getApi('settings', 'core');
   $this->addElement('Text', 'title', array(
        'label' => 'Form Name',
				'description'=>'Enter the name of the Form. [This name is for your indication only and will not be shown at user side.]',
        'allowEmpty' => false,
        'required' => true,
    ));
	 $this->addElement('Select', 'description', array(
      'label' => 'Display "Message" Field in Form',
			'description'=>'Do you want to display "Message" field in this form?',
       'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),
		  'onchange' => "showDescriptionSetting(this.value);",
      'value' => 1,
   ));   
   $this->addElement('Select', 'description_required', array(
      'label' => 'Make "Message" Field Required',
			'description'=>'Do you want to make "Message" field required in this form?',
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),   
      'value' => 1,
	 ));
   $this->addElement('Select', 'category_required', array(
      'label' => 'Make "Category" Field Required',
			'description'=>'Do you want to make "Category" field required in this form?',
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),   
      'value' => 1,
	 ));	 
   $this->addElement('text', 'message_reciver_email', array(
      'label' => 'Message Receiver Email Id',
			'description'=>'Enter the email id on which you want the receive the messages send from this form.',
      'maxlength' => 63,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
   ));
	  $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $URL = $view->baseUrl() . "/admin/core/settings/spam#recaptchapublic-label";
    $click = '<a href="' . $URL . '" target="_blank">click here</a>';

    $captchaDesc = sprintf('Do you want to show captcha to the visitors of your website while filling this form? Enabling captcha will prevent spamming on your website.? (If you have not yet configured "ReCaptcha Public Key" and "ReCaptcha Private Key", then %s to configure the keys. This capcha will be shown only to the visitors of your website.)', $click);
   $this->addElement('Select', 'show_captcha', array(
      'label' => "Show Captcha to Visitors",
			'description'=>$captchaDesc,
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),
		     
      'value' => 1,
	 ));
	 $this->getElement('show_captcha')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	 $this->addElement('Select', 'send_copy', array(
	 		'label'=>'Show "Send a copy of this on my email address."',
      'description' => 'Do you want users to be able to send a copy of this form’s email to their emails? (If you choose Yes, then "Send a copy of this on my email address." checkbox will appear in the bottom of the form.)',
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),
		     
      'value' => 1,
	 ));
   $this->addElement('text', 'color_asterisk', array(
      'label' => 'Asterisk Sign Color',
			'description'=>'Choose the color for asterisk (*) sign.',
      'class' => 'SEScolor',
      'maxlength' => 63,
			'value'=>'FF0000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
    ));
    //Form Text Settings
      $this->addElement('Select', 'display_file_upload', array(
      'label' => 'Display "File Upload" Field in Form',
			'description'=>'Do you want to display "File Upload" field in this form? (Note: This option will only be displayed to the Logged-in members on your website and will not be visible to the visitors.)',
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),
		     'onchange' => "showFileUpload(this.value);",
      'value' => 1,
			));
		
		 if(isset($_POST['display_file_upload']) && $_POST['display_file_upload']){
			 $requiredL = true;
			 $allL = false;
			}else{
			 $requiredL = false;
			 $allL = true;	
			}
		 $this->addElement('Text', 'label_file_upload', array(
        'label' => 'Text for Label "File Upload"',
        'description' => 'Enter the text for the "File Upload" label in this form.',
        'required' => $requiredL,
        'notEmpty' => $allL,
        'value' => "File Upload",
    ));
	  $this->addElement('Select', 'display_file_upload_required', array(
      'label' => 'Make "File Upload" Field Required?',
			'description'=>'Do you want to make "File Upload" field required in this form?',
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),
      'value' => 1,
		));
    $this->addElement('Radio', 'file_upload', array(
      'label' => 'Allow Photos or Files',
			'description'=>'Choose from below the File Types which you want to allow users to upload in this form.',
      'multiOptions' => array(
	      1 => "Photos (allowed extensions: 'bmp', 'jpg', 'png', 'psd', 'jpeg').",
        0 => "Files (allowed extensions: 'pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff’).",
        2 => 'Both Photos and Files.',
      ),
      'value' => 1,
    ));
    $this->addElement('Select', 'enable_terms', array(
      'label' => 'Enable "Agree to Terms" Disclaimers',
			'description'=>'Do you want to your users to first confirm the "Agree to Terms" disclaimers to fulfill any legal requirements before filling up this form? [If you choose Yes, then users will have to confirm "I have read and agree to the terms of service" setting shown in this form.]',
      'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),  
      'value' => 1,
		));
		 $this->addElement('textarea', 'ipaddress_ban', array(
	      'label' => 'IP Address Ban From Viewing This Form',
				'style'=>'height:40px;min-height:0',
				'description'=>'Enter the IP addresses (comma separated) to which this form will not be shown.',
	      'filters' => array(
        new Engine_Filter_Censor(),
	      ),
	  ));
		//Form Text Settings
    $this->addElement('Dummy', 'sesmultipleformheading', array(
        'label' => 'Form Text Settings',
    ));
    $this->addElement('Text', 'heading_text', array(
        'label' => 'Form Heading Text',
        'description' => 'Enter the text for the Heading of this Form. (Leave Blank, if you do not want to show heading.)',
        'value' => '',
    ));
    $this->addElement('Textarea', 'heading_description', array(
        'label' => 'Form Description Text',
				'style'=>'height:40px;min-height:0',
        'description' => 'Enter the text for the Description of this Form. (Leave Blank, if you do not want to show description.)',
        'value' => '',
    ));
    $this->addElement('Text', 'label_name', array(
        'label' => 'Text for Label "Name"',
        'description' => 'Enter the text which you want to show in place of "Name" for the name label in this form. (Note: This is a required field.)',
        'required' => true,
        'notEmpty' => true,
        'value' => 'Name',
    ));
    $this->addElement('Text', 'label_email', array(
        'label' => 'Text for Label "Email"',
        'description' => 'Enter the text which you want to show in place of "Email" for the email label in this form. (Note: This is a required field.)',
        'required' => true,
        'notEmpty' => true,
        'value' => 'Email',
    ));
    $this->addElement('Text', 'label_description', array(
        'label' => 'Text for Label "Message"',
        'description' => 'Enter the text which you want to show in place of "Message" for the description label in this form.',
        'required' => true,
        'notEmpty' => true,
        'value' => 'Description',
    ));
		
		$this->addElement('Text', 'label_category', array(
        'label' => 'Text for Label "Category"',
        'description' => 'Enter the text which you want to show in place of "Category" for the category label in this form.',
        'required' => true,
        'notEmpty' => true,
        'value' => 'Category',
    ));
		$this->addElement('Text', 'label_subcategory', array(
        'label' => 'Text for Label "2nd-Level Category',
        'description' => 'Enter the text which you want to show in place of "2nd-Level Category" for the 2nd-level category label in this form.',
        'required' => true,
        'notEmpty' => true,
        'value' => '2nd-level category',
    ));
		$this->addElement('Text', 'label_subsubcategory', array(
        'label' => 'Text for Label "3rd-Level Category',
        'description' => 'Enter the text which you want to show in place of "3rd-Level Category" for the 3rd-level category label in this form.',
        'required' => true,
        'notEmpty' => true,
        'value' => '3rd-level category',
    ));
		
		$this->addElement('Text', 'label_submit', array(
        'label' => 'Form Button Text',
        'description' => 'Enter the text for the submit button in this form.',
        'required' => true,
        'notEmpty' => true,
        'value' => 'Submit',
    ));
		
		$this->addElement('textarea', 'success_message', array(
	      'label' => 'Text for "Success Message"',
				'description'=>'Enter the text for success message which will show as a thank you message after the form is submitted.',
				'required' => true,
        'notEmpty' => true,
				'style'=>'height:40px;min-height:0',
	      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=> array('a'))),
	      ),
	  ));
   
	 $this->addElement('Select', 'active', array(
      'label' => "Enable This Form",
			'description'=>'Do you want to enable this form?',
       'multiOptions' => array(
				      '1' => 'Yes',
				      '0' => 'No',
		    ),
      'value' => 1,
   ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit',
      'ignore' => true
    ));
   $this->addElement('Button', 'submitsave', array(
      'label' => 'Save and Exit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));
   $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'decorators' => array(
        'ViewHelper'
      ),
   ));
   $this->addDisplayGroup(array(
      'submit',
      'submitsave',
      'cancel'      
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}