<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Configure.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Configure extends Engine_Form
{

  public function init()
  {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Configure Pages")))
      ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can configure pages and can get previous setup of pages. If you want to get the previous set up of landing page and other sections of your website, you can configure it from here.")));

    $this->addElement('Radio', 'sitecoretheme_landing_page_layout', array(
      'label' => 'Versatile Theme Landing Page',
      'description' => "Do you want Landing Page setup of ".SITECORETHEME_PLUGIN_NAME."? If you choose ‘Yes’ you will get the Landing Page as default setup of Versatile Theme. If chosen ‘No’, you will get the previous setup of Landing Page which was before the installation of ".SITECORETHEME_PLUGIN_NAME.".",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.page.layout', 1),
    ));

    $this->addElement('Radio', 'sitecoretheme_header_page_layout', array(
      'label' => 'Versatile Theme Header',
      'description' => "Do you want Header as ".SITECORETHEME_PLUGIN_NAME."? If you choose ‘Yes’ you will get the header as in default setup of Versatile Theme. If chosen ‘No’, you will get the previous Header which was before the installation of ".SITECORETHEME_PLUGIN_NAME.".",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.header.page.layout', 1),
    ));

    $this->addElement('Radio', 'sitecoretheme_footer_page_layout', array(
      'label' => 'Versatile Theme Footer',
      'description' => "Do you want Footer as ".SITECORETHEME_PLUGIN_NAME."? If you choose ‘Yes’ you will get the Footer as in default setup of Versatile Theme. If chosen ‘No’, you will get the previous Footer which was before the installation of ".SITECORETHEME_PLUGIN_NAME.".",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.footer.page.layout', 1),
    ));

    $this->addElement('Radio', 'sitecoretheme_login_page_layout', array(
      'label' => 'Versatile Theme Login Page',
      'description' => "Do you want Login page setup of ".SITECORETHEME_PLUGIN_NAME."? If you choose ‘Yes’ you will get the Login page as default setup of Versatile Theme. If chosen ‘No’, you will get the previous setup of Login page which was before the installation of ".SITECORETHEME_PLUGIN_NAME.".",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.login.page.layout', 1),
    ));

    $this->addElement('Radio', 'sitecoretheme_login_required_page_layout', array(
      'label' => 'Versatile Theme Login Required Page',
      'description' => "Do you want Login Required Page setup of ".SITECORETHEME_PLUGIN_NAME."? If you choose ‘Yes’ you will get the Login Required Page as default setup of Versatile Theme. If chosen ‘No’, you will get the previous setup of Login Required Page which was before the installation of ".SITECORETHEME_PLUGIN_NAME.".",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.login.required.page.layout', 1),
    ));

    $this->addElement('Radio', 'sitecoretheme_signup_page_layout', array(
      'label' => 'Versatile Theme Sign Up Page',
      'description' => "Do you want Sign Up Page setup of ".SITECORETHEME_PLUGIN_NAME."? If you choose ‘Yes’ you will get the Sign Up Page as default setup of Versatile Theme Theme. If chosen ‘No’, you will get the previous setup of Sign Up Page which was before the installation of ".SITECORETHEME_PLUGIN_NAME.".",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.signup.page.layout', 1),
    )); 
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }

}