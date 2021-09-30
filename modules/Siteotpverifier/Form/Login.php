<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Login.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_Form_LoginDefault extends Engine_Form_Email
{

  protected $_mode;

  public function setMode($mode)
  {
    $this->_mode = $mode;
    return $this;
  }

  public function getMode()
  {
    if( null === $this->_mode ) {
      $this->_mode = 'page';
    }
    return $this->_mode;
  }

  public function init()
  {
    $tabindex = rand(100, 9999);
    $this->_emailAntispamEnabled = (Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core.spam.email.antispam.login', 1) == 1);

    // Used to redirect users to the correct page after login with Facebook
    $_SESSION['redirectURL'] = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

    $description = Zend_Registry::get('Zend_Translate')->_("If you already have an account, please enter your details below. If you don't have one yet, please <a href='%s'>sign up</a> first.");
    $description = sprintf($description, Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true));

    // Init form
    $this->setTitle('Member Sign In');
    $this->setDescription($description);
    $this->setAttrib('id', 'user_form_login');
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $email = Zend_Registry::get('Zend_Translate')->_('Email Address');
    // Init email
    $emailElement = $this->addEmailElement(array(
      'label' => $email,
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        'EmailAddress'
      ),
      // Fancy stuff
      'tabindex' => $tabindex++,
      'autofocus' => 'autofocus',
      'inputType' => 'email',
      'class' => 'text',
    ));

    $emailElement->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    $password = Zend_Registry::get('Zend_Translate')->_('Password');
    // Init password
    $this->addElement('Password', 'password', array(
      'label' => $password,
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => $tabindex++,
      'filters' => array(
        'StringTrim',
      ),
    ));

    $this->addElement('Hidden', 'return_url', array(
    ));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    if( $settings->core_spam_login ) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
          'tabindex' => $tabindex++,
          'size' => ($this->getMode() == 'column') ? 'compact' : 'normal',
      )));
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Sign In',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => $tabindex++,
    ));

    $this->addDisplayGroup(array(
      'submit',
      // 'remember'
      ), 'buttons');

    $content = Zend_Registry::get('Zend_Translate')->_("<span><a href='%s'>Forgot Password?</a></span>");
    $content = sprintf($content, Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true));


    // Init forgot password link
    $this->addElement('Dummy', 'forgot', array(
      'content' => $content,
    ));

    // Init facebook login link
    if( 'none' != $settings->getSetting('core_facebook_enable', 'none') && $settings->core_facebook_secret ) {
      $this->addElement('Dummy', 'facebook', array(
        'content' => User_Model_DbTable_Facebook::loginButton(),
      ));
    }

    // Init twitter login link
    if( 'none' != $settings->getSetting('core_twitter_enable', 'none') && $settings->core_twitter_secret ) {
      $this->addElement('Dummy', 'twitter', array(
        'content' => User_Model_DbTable_Twitter::loginButton(),
      ));
    }

    // Init janrain login link
    if( 'none' != $settings->getSetting('core_janrain_enable', 'none') && $settings->core_janrain_key ) {
      $mode = $this->getMode();
      $this->addElement('Dummy', 'janrain', array(
        'content' => User_Model_DbTable_Janrain::loginButton($mode),
      ));
    }

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login'));
  }

}

class User_Form_Login extends User_Form_LoginDefault
{
  public function init()
  {
    parent::init();
    $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
    $emailElementName = $this->getEmailElementFieldName();
    $emailElement = $this->getElement($emailElementName);
    $email = Zend_Registry::get('Zend_Translate')->_('Email or Phone');
    $emailElement->setLabel($email);
    $emailElement->setAttribs(array(
    //  'placeholder' => 'Enter your email or mobile',
      'inputType' => 'text'
    ));
    $emailElement->removeValidator('EmailAddress');
    $emailElement->setValidators(array(
      array('NotEmpty', true),
      array('Regex', true, "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})|([1-9][0-9]{4,15})$/")
    ));
    $emailElement->getValidator('Regex')->setMessage('Email Address / Phone number is not valid, Please provide a valid Email or Phone Number.', 'regexNotMatch');
    if( $loginoption == "both" ) {
      $this->setAttrib('class', 'global_form siteotp_login_global_form');
      $this->addElement('Button', 'login_via_otp', array(
        'label' => 'Signin with OTP',
        'onClick' => 'sendotpCode();',
        'order' => 1,
        'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
        'decorators' => array(array('ViewScript', array(
              'viewScript' => 'application/modules/Siteotpverifier/views/scripts/_loginOTPButtons.tpl',
              'class' => 'form element',
              'emailFieldName' => $emailElementName
            )))
      ));
      $this->addDisplayGroup(array(
        'password',
        'login_via_otp'
        ), 'password_buttons', array(
        'order' => 1,
      ));
      //$this->getDisplayGroup('buttons')->addElement($this->login_via_otp);
    }
  }

}
