<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Reset.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Auth_Reset extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Reset Password?')
      ->setDescription('Enter a new password for your account.');

    // init password
    $this->addElement('Password', 'password', array(
      'label' => 'New Password',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', true, array(6, 32)),
      ),
      'tabindex' => 1,
    ));

    // init password_confirm
    $this->addElement('Password', 'password_confirm', array(
      'label' => 'Confirm New Password',
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => 2,
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Reset Password',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 3,
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