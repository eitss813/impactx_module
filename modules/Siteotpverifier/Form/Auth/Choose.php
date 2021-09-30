<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Choose.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Auth_Choose extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Password Reset Options')
//      ->setDescription('Forgotten your password?')
      ->setAttrib('id', 'Siteotpverifier_form_auth_choose')
      ;

    // init email
    $this->addElement('Radio', 'option', array(
      'description' => 'Choose the option to get code to reset password of your account.',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
                1 => 'Get via Email',
                0 => 'Get via SMS',
            ),
      'tabindex' => 1,
    ));
    //$this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
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