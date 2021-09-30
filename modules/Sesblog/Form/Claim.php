<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Claim.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Claim extends Engine_Form
{
 
  public function init() { 
  
    $this->setTitle('Claim For Blog')
      ->setDescription('')
      ->setAttrib('name', 'sesblog_calim')
      ->setAttrib('id', 'sesblog_claim_create');

    $viewer = Engine_Api::_()->user()->getViewer();
      
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'placeholder' => 'Enter Blog Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
      'autofocus' => 'autofocus',
    ));
    $this->addElement('Hidden', 'blog_id', array());
    
		$this->addElement('Text', 'user_name', array(
			'label' => 'Your Name',
			'allowEmpty' => false,
			'required' => true,
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_HtmlSpecialChars(),
			),
			'value'=>$viewer->displayname,
		));
		$this->addElement('Text', 'user_email', array(
			'label' => 'Your Email',
			'required' => true,
			'allowEmpty' => false,
			'validators' => array(
				'EmailAddress'
			),
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_HtmlSpecialChars(),
			),
			'value'=>$viewer->email,
		));
		
		$this->addElement('Text', 'contact_number', array(
      'label' => 'Contact Number',
    ));
    
    $this->addElement('textarea', 'description', array(
      'label' => 'Reason For Claim',
      'required' => true,
			'allowEmpty' => false,
    ));
    
    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
    ));
  }
}
