<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SigninPopup.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_SigninPopup extends Engine_Form
{

  public function init()
  {
    $this->setTitle("Manage Sign-in Popup");
    $this->setDescription("Here, you can manage settings relevant to Sign-In / Sign-Up Popup.");

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Radio', 'sitecoretheme_signin_popup_enable', array(
      'label' => 'Pop-up for SignIn / SignUp',
      'description' => "Do you want Sign In and Sign Up form to be opened in a pop-up box?",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.signin.popup.enable', 1),
    ));


    $this->addElement('Radio', 'sitecoretheme_signin_popup_display', array(
      'label' => 'Auto Display of Sign-In / Sign-Up Popup',
      'description' => 'Do you want sign-in /sign-up popup to be displayed automatically when users visit your website?',
      'multiOptions' => array(
        2 => 'Yes, Auto Display Sign-Up Popup',
        1 => 'Yes, Auto Display Sign-In Popup',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.signin.popup.display', 1),
    ));

    $this->addElement('Text', 'sitecoretheme_signin_popup_visibility', array(
      'label' => 'Auto Display Duration',
      'description' => "Enter the time duration in day(s) after which you want the Sign-In popup to show up again(Enter '0' to display every time page loads.)",
      'value' => $coreSettings->getSetting('sitecoretheme.signin.popup.visibility', 0),
    ));

    $this->addElement('Radio', 'sitecoretheme_signin_popup_close', array(
      'label' => 'Allow Sign-In / Sign-Up Popup Closure',
      'description' => 'Do you want to allow users to be able to close sign-in / sign-up popup?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No [Note: User will have to either Sign-In or Sign-Up then only user will be able to access your website.]'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.signin.popup.close', 1),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}