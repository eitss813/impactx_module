<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Global.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'lifetime', array(
      'label' => 'OTP Code Duration',
      'description' => "Enter the duration of OTP code after that duration the OTP code will be invalid and they need to click on 'Resend' button to receive the verification code again. [Note: duration should be in seconds e.g: 10 mins = 600 secs]",
      'size' => 5,
      'maxlength' => 4,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('Int'),
      ),
      'value' => 600,
    ));

    $this->addElement('Select', 'type', array(
      'label' => 'OTP Code Format',
      'description' => "Select format for the OTP code being generated.",
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        0 => "Numeric",
        1 => "Alphanumeric"
      ),
      'value' => 0,
    ));

    $this->addElement('Select', 'length', array(
      'label' => 'OTP Code Length',
      'description' => "Enter character length for the OTP code being generated.",
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        '4' => '4',
        '6' => '6',
        '8' => '8',
        '10' => '10',
        '12' => '12',
      ),
      'validators' => array(
        array('NotEmpty', true),
      ),
      'value' => 6,
    ));


    $this->addElement('Multiselect', 'allowCountry', array(
      'label' => 'Allow Countries',
      'description' => 'Select the countries whose phone numbers can be used to signup/login on your website.',
      'multiOptions' => $GLOBALS['countryCodes'],
      'value' => $GLOBALS['countryCodes'],
    ));
    $this->allowCountry->setValue(array_keys($GLOBALS['countryCodes']));
    $this->allowCountry->getDecorator('Description')->setOption('placement', 'PREPEND');

    $this->addElement('Select', 'defaultCountry', array(
      'label' => 'Default Country',
      'description' => 'Select the default country which will be shown as pre-selected to users.',
      'multiOptions' => $GLOBALS['countryCodes'],
      'value' => '+1',
    ));

    $this->addElement('Radio', 'autoCountrySelection', array(
      'label' => 'Automatic Country Selection',
      'description' => 'Do you want to select the default country automatically based on user\'s IP [Note: Above Default Country Setting will not work when this setting is enabled. If user\'s IP is from a country that is not in Allow Countries, Default country will be pre selected for that user. ] ',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => 1,
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $URL = $view->baseUrl() . "/admin/siteotpverifier/settings/language-editor";
    $click = '<a href="' . $URL . '" target="_blank">here</a>';

    $this->addElement('Radio', 'nativelangauge', array(
      'label' => 'Native Language',
      'description' => 'Do you want to send OTP verification message to users in their native language?[Note: You can edit the message from ' . $click . '.]',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => 1,
    ));
    $this->nativelangauge->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));


    $this->addElement('Radio', 'allowoption', array(
      'label' => 'Login Options',
      'description' => "Select method you want to enable for users to login on your site.",
      'multiOptions' => array(
        "default" => 'Default',
        "both" => 'Either with Password or OTP',
        "otp" => 'Two Factor Verification (Password & OTP)',
      ),
      'value' => "default",
    ));

    $populatearray = array();

    $this->addElement('Radio', 'singupUserPhone', array(
      'label' => 'Enable Phone Number During Signup?',
      'description' => 'Do you want to provide Phone Number field to the users in signup process?',
      'multiOptions' => array(
        1 => 'Yes, show Phone Number field to the users in signup process.',
        0 => 'No, don’t show Phone Number field to the users in signup process.'
      ),
      'value' => 1,
      'onClick' => 'showPhoneNumberFieldSignupCase()'
    ));

    $this->addElement('Radio', 'singupShowBothPhoneAndEmail', array(
      'label' => 'Choice Between Email or Phone number During Signup',
      'description' => 'Do you want to enable single field for Email Address and Phone Number? [Note: This setting will work only when ‘No, user can signup either with Email Address or Mobile Number.’ option is enabled in above setting.]',
      'multiOptions' => array(
        0 => 'Yes, show single field for Email Address and Phone Number.',
        1 => 'No, show two different fields for Email Address and Phone Number.',
      ),
      'value' => 1,
      'onClick' => 'showSignupFieldsBaseSetting()'
    ));

    $this->addElement('Radio', 'singupRequirePhone', array(
      'label' => 'Phone Number is a Required During Signup?',
      'description' => 'Do you want Phone Number to be a mandatory field for users in signup process?',
      'multiOptions' => array(
        1 => 'Yes, Phone Number is a mandatory field.',
        0 => 'No, Phone Number is not a mandatory field.'
      ),
      'value' => 1,
    ));

    $this->addElement('Text', 'signupAutoEmailTemplate', array(
      'label' => 'Email Address Format',
      'description' => "Set email template for user, this template will use for creation of auto email address when user are signup with mobile number or not insert the email adress.",
      'required' => true,
      'allowEmpty' => false,
      'value' => 'se[PHONE_NO]@semail.com',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
    if( !empty($coreSettings->getSetting('siteotpverifier')) ) {
      $populatearray = $coreSettings->getSetting('siteotpverifier');
    }

    $this->populate($populatearray);
  }

}
