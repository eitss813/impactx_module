<?php

class Siteotpverifier_Plugin_Signup_Account
{

  protected $_plugin;

  public function setPlugin($plugin)
  {
    $this->_plugin = $plugin;
  }

  public function addFields($form)
  {
    if( !$this->hasEnableOtp() ) {
      return $form;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
    $countrycodes = Engine_Api::_()->getApi('core', 'siteotpverifier')->countryCode();
    $countrycodes = array_keys($countrycodes);
    $countrycodes = array_combine($countrycodes, $countrycodes);
    $reqphoneno = !empty($showBothPhoneAndEmail) && $settings->getSetting('siteotpverifier.singupRequirePhone', 1);

    $emailFiledsName = $form->getEmailElementFieldName();
    $emailFiled = $form->getElement($emailFiledsName);
    $emailOrder = ($emailFiled && $emailFiled->getOrder()) ? $emailFiled->getOrder() : 1;
    $form->addElement('Text', 'phoneno', array(
      'label' => 'Mobile Number',
      'description' => 'Enter mobile number',
      'required' => empty($reqphoneno) ? false : true,
      'allowEmpty' => empty($reqphoneno) ? true : false,
      'order' => ++$emailOrder,
      'tabindex' => 2,
      'validators' => array(
        array('NotEmpty', empty($reqphoneno) ? false : true),
        array('Regex', true, array('/^[1-9][0-9]{4,15}$/')),
        array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'siteotpverifier_users', 'phoneno'))
      ),
     // 'placeholder' => 'Mobile Number',
    ));
    $form->phoneno->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
//    $form->phoneno->getValidator('NotEmpty')->setMessage('Please enter a valid phone number.', 'isEmpty');
    $form->phoneno->getValidator('Db_NoRecordExists')->setMessage('Someone has already registered this phone number, please use another one.', 'recordFound');
    $form->phoneno->getValidator('Regex')->setMessage('Please enter a valid phone number.', 'regexNotMatch');

    ksort($countrycodes);
    $form->addElement('Select', 'country_code', array(
      'label' => 'Country/Region',
      'multiOptions' => $countrycodes,
      'value' => Engine_Api::_()->siteotpverifier()->getDefaultCountry(),
      'style' => 'display:none;',
      'order' => ++$emailOrder,
      'decorators' => array(array('ViewScript', array(
            'viewScript' => 'application/modules/Siteotpverifier/views/scripts/_formPhoneCountryCodes.tpl',
            'class' => 'form element',
            'countryCodes' => $countrycodes,
            'emailFieldName' => $emailFiledsName,
          )))
    ));
    $form->addElement('Hidden', 'signup_otp_type', array(
      'value' => 'email',
      'order' => 100999
    ));
    return $form;
  }

  public function addPhoneNumberValidators()
  {
    if( $this->hasEnableOtp() ) {
      $this->_plugin->getForm()->phoneno->setAllowEmpty(false)->removeValidator('NotEmpty')->addValidator('NotEmpty');
    }
  }

  public function onSubmitBefore($request)
  {
    $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
    $otpverifySession->unsetAll();
    if( $request->isPost() && $this->hasEnableOtp() ) {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
      $emailElementFieldName = $this->_plugin->getForm()->getEmailElementFieldName();
      $signup_otp_type = $request->getParam("signup_otp_type");
      $this->_plugin->getForm()->signup_otp_type->setValue($signup_otp_type);
      if( $signup_otp_type == 'phone' && (empty($showBothPhoneAndEmail)) ) {
        $phoneno = $request->getParam("phoneno") ?: time();
        $this->addPhoneNumberValidators();
        $autoEmailTemplate = $settings->getSetting('siteotpverifier.signupAutoEmailTemplate', 'se[PHONE_NO]@semail.com');
        $email = str_replace('[PHONE_NO]', $phoneno, $autoEmailTemplate);
        $request->setPost($emailElementFieldName, $email);
        $request->setPost('signup_otp_type', 'phone');
      }
    }
  }

  public function onSubmitAfter($request)
  {
    if( $this->_plugin->getForm()->isValid($request->getPost()) && $this->hasEnableOtp() ) {
      $phone_no = $request->getParam("phoneno");
      $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
      $otpverifySession->unsetAll();
      if( $phone_no ) {
        $country_code = $request->getParam("country_code");
        $otpverifySession->otp_code = null;
        $otpverifySession->phoneno = $country_code . $phone_no;
        $otpverifySession->code_for = 'signup';
      } else {
        $otpverifySession->unsetAll();
      }
      $this->_plugin->getSession()->data['signup_otp_type'] = $request->getParam("signup_otp_type");
    }
  }

  public function onProcess()
  {
    if( !$this->hasEnableOtp() ) {
      return;
    }

    $data = $this->_plugin->getSession()->data;
    $user = $this->_plugin->getRegistry()->user;
    if(!empty($data['phoneno'])) {
      $otpusertable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
      $userRow=$otpusertable->createRow();
      $userRow->user_id=$user['user_id'];
      $userRow->phoneno=$data['phoneno'];
      $userRow->country_code=$data['country_code'];
      $userRow->enable_verification=1;
      $userRow->save();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
    if( $showBothPhoneAndEmail ) {
      return;
    }
    if( $user->verified || empty($data['signup_otp_type']) || $data['signup_otp_type'] !== 'phone' ) {
      return;
    }

    $user->verified = 1;
    $user->enabled = (int) ( $user->approved && $user->verified );
    $user->save();


    if( $user->verified && $user->enabled ) {
      // Create activity for them
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
      // Set user as logged in if not have to verify email
      Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
    }
    if( $this->_plugin->getRegistry()->mailType === 'core_verification' ) {
      $this->_plugin->getRegistry()->mailType = null;
      $this->_plugin->getRegistry()->mailParams = array();
    }
  }

  public function hasEnableOtp()
  {
    $enabledOTPClient = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $showphoneSignup = $settings->getSetting('siteotpverifier.singupUserPhone', 1);
    return $enabledOTPClient && !empty($showphoneSignup);
  }

}

?>