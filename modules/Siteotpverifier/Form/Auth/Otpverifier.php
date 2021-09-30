<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Otpverifier.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Auth_Otpverifier extends Engine_Form
{
  public function formdata($mobileno)
  {
    $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    $string = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);

    $description = sprintf($this->getTranslator()->translate('Enter the verification code you have received on ******%s.<br/> <b>Note:</b> OTP Code is valid for %s.'), substr($mobileno, -4), $string);
    $this->setDescription($description);
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
  }

  public function init()
  {
    $this->setAttrib('id', 'siteotpverifier_form_verify')
      ->setTitle('Validate OTP (One Time Password)');

    // init password
    $this->addElement('Text', 'code', array(
      'label' => 'OTP',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'tabindex' => 1,
    ));

    // Init submit
    $this->addElement('Button', 'verify_submit', array(
      'label' => 'Verify',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 3,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Button', 'resend', array(
      'label' => 'Resend',
      'onClick' => 'resendotpCode();',
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array(
      'verify_submit',
      'resend'
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    if( $service == "testmode" ) {
      $content = Zend_Registry::get('Zend_Translate')->_("Currently, we have enabled 'Virtual SMS Client' to receive OTP code. So, you will not get the OTP code on your registered mobile number. To view the received OTP code, please <a href='javascript:void()' onclick='window.open(%s)'>click here</a>.");
      $content = sprintf($content, '"' . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'siteotpverifier', 'controller' => 'mobile', 'action' => 'index'), 'siteotpverifier_extended', true) . '","Ratting","width=360,height=600,toolbar=0,status=0,scrollbars=1"');
      $this->addElement('Dummy', 'linkfor_testmode_mobile', array(
        'content' => '<div class="tip"><span>' . $content . '</span></div>',
        'order' => 9999,
      ));
    }
  }

}
