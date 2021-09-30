<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TextBanner.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Landingpage_TextBanner extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can manage various settings for the tagline displaying on the Banner. <a title='Preview - Text Banner' href='application/modules/Sitecoretheme/externals/images/screenshots/banner_tagline.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));
    $this->setTitle("Manage Banner Tagline");
    $this->setDescription("$description");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttrib('id', 'form-upload');

     $this->addElement('Textarea', 'sitecoretheme_landing_tbanner_text', array(
        'label' => 'Tagline',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.tbanner.text', 'Get our plugins to let your community move one step ahead.'),
      ));

      $this->addElement('Text', 'sitecoretheme_landing_tbanner_ctatext', array(
        'label' => 'Text on Button',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.tbanner.ctatext', 'Purchase Now'),
      ));

      $this->addElement('Text', 'sitecoretheme_landing_tbanner_url', array(
        'label' => 'URL for Button',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.tbanner.url', '#'),
      ));

      $this->addElement('radio', 'sitecoretheme_landing_tbanner_newtab', array(
        'label' => "Open URL in New Tab",
        'description' => "Do you want to open the link in new tab?",
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'value' => $coreSettings->getSetting('sitecoretheme.landing.tbanner.newtab', 1),
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