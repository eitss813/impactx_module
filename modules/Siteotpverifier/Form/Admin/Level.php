<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2017-02-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
    ->setTitle('Member Level Settings')
    ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

      // Buy credits?
    $this->addElement('Radio', 'login', array(
      'label' => 'Allow OTP Verification',
      'description' => 'Do you want to let members enable/disable OTP verification for the login process?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
        ),
      'value' => 1,
      ));

      // send credits?
      // Element: max
    $this->addElement('Text', 'max_resend', array(
      'label' => 'Number of Resend Attempts',
      'description' => 'Enter the number of times a user can request to resend the verification code.  [Note: Enter zero or leave empty for infinite attempts.]',
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(0),
        ),
      'value'=> 0,  
    ));
    $this->addElement('Text', 'resettime', array(
      'label' => 'Reset Duration of OTP Attempts',
      'description' => 'Enter the time duration (in seconds) after which count for sending the verification code will reset. [Note: Default time duration is 24 hours i.e. 86400 seconds.]',
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(0),
        ),
      'value'=> 86400,  
    ));
    $this->addElement('Text', 'time', array(
      'label' => 'User Blocking Duration',
      'description' => 'Enter the time duration for which user will be blocked after continuous failed attempts. [Note: Default time duration is 24 hours i.e. 86400 seconds.]',
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(0),
        ),
      'value'=> 86400,  
    ));
  }
  
}