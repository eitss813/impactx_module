<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Confirmationform.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Confirmationform extends Engine_Form
{
  public function init()
  {
  $this
      ->setTitle('Send Confirmation Email')
      ->setDescription('Below, you can configure subject and message for the confirmation email which will be sent to the users who submit the associated form on your website.');
      $settings = Engine_Api::_()->getApi('settings', 'core');
			$this->addElement('Select', 'email_confirmation', array(
			'label' => "Send Email Confirmations",
			'description'=>'Do you want to send confirmation email messages to the users who submit the associated form?',
			'multiOptions' => array(
					'1'=> 'Yes',
					'0'=> 'No',
			),
			'onchange' => "showConfirmationFields(this.value);",
			'value' => 1,
			));
			if(isset($_POST['email_confirmation']) && $_POST['email_confirmation']){
				$required = true;
				$empty = false;	
			}else{
				$required = false;
				$empty = true;
			}
       $this->addElement('text', 'confirmation_subject', array(
					'label' => 'Confirmation Subject',
					'description'=>'Enter the Confirmation Email Message Subject.',
					'maxlength' => 63,
					'required' => $required,
        	'notEmpty' => $empty,
					'filters' => array(
						'StripTags',
						new Engine_Filter_Censor(),
						new Engine_Filter_StringLength(array('max' => '63')),
					),
				));
    	 //UPLOAD PHOTO URL
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesbasic', 'controller' => 'manage', 'action' => "upload-image"), 'admin_default', true);

      $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr';

      $editorOptions = array(
          'upload_url' => $upload_url,
          'html' => (bool) $allowed_html,
      );

      if (!empty($upload_url)) {
				$editorOptions['mode'] = 'specific_textareas';
        $editorOptions['plugins'] = array(
            'table', 'fullscreen', 'media', 'preview', 'paste',
            'code', 'image', 'textcolor', 'jbimages', 'link'
        );

        $editorOptions['toolbar1'] = array(
            'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
            'media', 'image', 'jbimages', 'link', 'fullscreen',
            'preview'
        );
      }
       $this->addElement('TinyMce', 'confirmation_message', array(
	      'label' => 'Confirmation Message',
				'description'=>'Enter the Confirmation Email Message in the editor below.',
				 'editorOptions' => $editorOptions,
				'required' => $required,
       	'notEmpty' => $empty,
	      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '400')),
        new Engine_Filter_Html(array('AllowedTags'=> array('a'))),
	      ),
	    ));	
			// Add submit button
			$this->addElement('Button', 'submit', array(
				'label' => 'Save',
				'ignore' => true,
				'decorators' => array('ViewHelper'),
				'type' => 'submit',
				'ignore' => true
			));
       $this->addDisplayGroup(array(
      'submit',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}