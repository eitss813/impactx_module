<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CtaButtons.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Landingpage_CtaButtons extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can manage the content for Action Buttons. <a title='Preview - Cta Butotns' href='application/modules/Sitecoretheme/externals/images/screenshots/cta-buttons.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));
    $this->setTitle("Manage Action Buttons");
    $this->setDescription("$description");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttrib('id', 'form-upload');
    $this->addElement('Text', 'sitecoretheme_landing_cta_title1', array(
      'label' => 'Title for Button 1',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.title1', 'Create Events to Socialise'),
    ));

    $this->addElement('File', 'sitecoretheme_landing_cta_icon1', array(
      'label' => 'Upload Button 1 Icon',
    ));
    $this->addElement('File', 'sitecoretheme_landing_cta_hover_icon1', array(
      'label' => 'Upload Button 1 Hover Icon',
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_url1', array(
      'label' => 'URL for Button 1',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.url1', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_body1', array(
      'label' => 'Tagline for Button 1',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.body1', 'Virtual events are highly interactive and involve interacting people sharing a common virtual environment on the web.'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_title2', array(
      'label' => 'Title for Button 2',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.title2', 'Preserve Memorable Moments'),
    ));

    $this->addElement('File', 'sitecoretheme_landing_cta_icon2', array(
      'label' => 'Upload Button 2 Icon',
    ));
    $this->addElement('File', 'sitecoretheme_landing_cta_hover_icon2', array(
      'label' => 'Upload Button 2 Hover Icon',
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_url2', array(
      'label' => 'URL for Button 2',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.url2', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_body2', array(
      'label' => 'Tagline for Button 2',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.body2', 'Photos capture the moment in time and preserve it for generations to come. They make the important events of our lives memorable.'),
    ));
    $this->addElement('Text', 'sitecoretheme_landing_cta_title3', array(
      'label' => 'Title for Button 3',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.title3', 'Improve your Credibility with Blogs'),
    ));

    $this->addElement('File', 'sitecoretheme_landing_cta_icon3', array(
      'label' => 'Upload Button 3 Icon',
    ));

    $this->addElement('File', 'sitecoretheme_landing_cta_hover_icon3', array(
      'label' => 'Upload Button 3 Hover Icon',
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_url3', array(
      'label' => 'URL for Button 3',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.url3', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_cta_body3', array(
      'label' => 'Tagline for Button 3',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.body3', 'Blog gives you the opportunity to create relevant content for your customers and gives them a reason to click through to your website.'),
    ));

    $this->addDisplayGroup(array('sitecoretheme_landing_cta_title1', 'sitecoretheme_landing_cta_url1', 'sitecoretheme_landing_cta_icon1', 'sitecoretheme_landing_cta_hover_icon1', 'sitecoretheme_landing_cta_body1'), 'sitecoretheme_landing_cta_block1');
    $this->addDisplayGroup(array('sitecoretheme_landing_cta_title2', 'sitecoretheme_landing_cta_url2', 'sitecoretheme_landing_cta_icon2', 'sitecoretheme_landing_cta_hover_icon2', 'sitecoretheme_landing_cta_body2'), 'sitecoretheme_landing_cta_block2');
    $this->addDisplayGroup(array('sitecoretheme_landing_cta_title3', 'sitecoretheme_landing_cta_url3', 'sitecoretheme_landing_cta_icon3', 'sitecoretheme_landing_cta_hover_icon3', 'sitecoretheme_landing_cta_body3'), 'sitecoretheme_landing_cta_block3');

    $this->addElement('File', 'sitecoretheme_landing_side-banner', array(
      'label' => 'Upload Side Photo',
      'description' => 'The side photo will display in template of style 1 only.',
    ));

    $this->addElement('Select', 'sitecoretheme_landing_cta_style', array(
        'label' => 'Template',
        'description' => 'Select template for displaying this block',
        'multiOptions' => array(
          'style1' => 'Style 1',
          'style2' => 'Style 2',
          'style3' => 'Style 3'
        ),
        'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.style', 'style1'),
      ));
    
    $this->addElement('Select', 'sitecoretheme_landing_cta_newtab', array(
      'label' => "Open URL in New Tab?",
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.cta.newtab', 0),
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