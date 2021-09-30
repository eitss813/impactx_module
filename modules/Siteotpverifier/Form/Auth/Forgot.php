<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Forgot.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Auth_Forgot extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Lost Password')
      ->setAttrib('id', 'Siteotpverifier_form_auth_forgot')
      ;

    // init email
    $this->addElement('Text', 'email', array(
      'description' => 'Please enter your email address or mobile number whose password you want to reset.',
      'required' => true,
      'allowEmpty' => false,
      'placeholder'=> 'Email or Mobile Number',  
      'validators' => array(
                array('NotEmpty',true),
                array('Regex', true, "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})|([1-9][0-9]{4,15})$/"),
                ),
      'tabindex' => 1,
    ));
    $this->email->getValidator('Regex')->setMessage('Email Address / Phone number is not valid, Please provide a valid Email or phone number.', 'regexNotMatch');
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 2,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'default', true),
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}