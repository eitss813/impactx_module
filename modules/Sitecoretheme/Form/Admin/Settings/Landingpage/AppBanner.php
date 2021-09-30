<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AppBanner.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Landingpage_AppBanner extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can manage Promotional Banner section. <a title='Preview - Promotion Banner' href='application/modules/Sitecoretheme/externals/images/screenshots/app-store.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));
    $this->setTitle("Manage Promotion Banner");
    $this->setDescription("$description");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttrib('id', 'form-upload'); 
    $this->addElement('Text', 'sitecoretheme_landing_appbanner_title', array(
      'label' => 'Title Text',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.title', 'Download our latest app'),
    ));

    $this->addElement('Textarea', 'sitecoretheme_landing_appbanner_description', array(
      'label' => 'Description',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.description', 'Enabling Business with a new perspective using Mobile apps.Get your community in the hands of your customers with our beautiful mobile solutions.'),
    ));

    $this->addElement('radio', 'sitecoretheme_landing_appbanner_buttons', array(
      'label' => "Type of Promotion",
      'description' => "Select the type of promotion you want to do using this section.",
      'multiOptions' => array(
        '0' => 'App Promotion',
        '1' => 'Other Promotion via Link'
      ),
      'onclick' => 'changeActionButtons(this.value)',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.buttons', 0),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_appbanner_appstoreUrl', array(
      'label' => 'App Store URL',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.appstoreUrl', '#'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_appbanner_playstoreUrl', array(
      'label' => 'Play Store URL',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.playstoreUrl', '#'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_appbanner_actionUrl', array(
      'label' => 'Other Promotional URL',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.actionUrl', '#'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_appbanner_actionText', array(
      'label' => 'Other Promotional Text',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.actionText', 'Action Button'),
    )); 

    $this->addElement('radio', 'sitecoretheme_landing_appbanner_newtab', array(
      'label' => "Open URL in New Tab?",
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.appbanner.newtab', 1),
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
?>