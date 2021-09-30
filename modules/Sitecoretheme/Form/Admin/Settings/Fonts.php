<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fonts.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Fonts extends Engine_Form
{

  protected $_fontType;

  public function getFontType()
  {
    return $this->_fontType;
  }

  public function setFontType($fontType)
  {
    $this->_fontType = $fontType;
    return $this;
  }

  public function init()
  {

    $this->setTitle("Manage Fonts");
    $this->setDescription("Here you can manage fonts for your website. [Note: This set of settings will affect the fonts of your entire website.]");

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $fontsOptions = array('0' => 'Google Fonts', '1' => 'Web Safe');
    $webSafeFonts = array(
      'Georgia, serif' => 'Georgia, serif',
      '"Palatino Linotype", "Book Antiqua", Palatino' => '"Palatino Linotype", "Book Antiqua", Palatino',
      '"Times New Roman", Times, serif' => '"Times New Roman", Times, serif',
      'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
      '"Arial Black", Gadget, sans-serif' => '"Arial Black", Gadget, sans-serif',
      '"Comic Sans MS", cursive, sans-serif' => '"Comic Sans MS", cursive, sans-serif',
      'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
      '"Lucida Sans Unicode", "Lucida Grande", sans-serif' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
      'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
      '"Trebuchet MS", Helvetica, sans-serif' => '"Trebuchet MS", Helvetica, sans-serif',
      'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
      '"Courier New", Courier, monospace' => '"Courier New", Courier, monospace',
      '"Lucida Console", Monaco, monospace' => '"Lucida Console", Monaco, monospace'
    );

    $googleFontFamilies = array();
    if( !$this->_fontType ) {
      $url = "https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyB8n5LK2n_9aUoqKulzWrkcltkTbqGhyXY";
      $raw = file_get_contents( $url, 0, null, null );
      $data = json_decode( $raw );
      foreach ( $data->items as $font ) { 
        $googleFontFamilies['"' . $font->family . '", ' . $font->category] = $font->family;         
      }
    }

    $this->addElement('Select', 'sitecoretheme_fonts_selected_font', array(
      'label' => 'Font Type',
      'description' => 'Select font type which you want to implement on your website.',
      'multiOptions' => $fontsOptions,
      'onchange' => 'fetchFontSettings(this.value)',
      'value' => $this->getFontType(),
    ));

    if( $this->_fontType ) {
      $this->addElement('Select', 'sitecoretheme_fonts_body_font_family', array(
        'label' => 'Font Family for Body Tag',
        'description' => 'Select font family for text added in body tag.',
        'multiOptions' => $webSafeFonts,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.body.font.family', ''),
      ));
    } else {
      $this->addElement('Select', 'sitecoretheme_fonts_body_font_family_google', array(
        'description' => 'Select font family for text added in body tag.',
        'label' => 'Font Family for Body Tag',
        'required' => true,
        'multiOptions' => $googleFontFamilies,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.body.font.family.google', '"Roboto", sans-serif'),
      ));
      $this->sitecoretheme_fonts_body_font_family_google->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    }

    $this->addElement('Text', 'sitecoretheme_fonts_body_font_size', array(
      'label' => 'Font Size for Body Tag',
      'description' => 'Enter font size for text added in body tag.',
      'required' => true,
      'value' => $coreSettings->getSetting('sitecoretheme.fonts.body.font.size', 13),
    ));
    if( $this->_fontType ) {
      $this->addElement('Select', 'sitecoretheme_fonts_heading_font_family', array(
        'label' => 'Font Family for Headings',
        'description' => 'Select the font family for headings.',
        'multiOptions' => $webSafeFonts,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.heading.font.family', ''),
      ));
    } else {
      $this->addElement('Select', 'sitecoretheme_fonts_heading_font_family_google', array(
        'description' => 'Select the font family for headings.',
        'label' => 'Font Family for Headings',
        'required' => true,
        'multiOptions' => $googleFontFamilies,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.heading.font.family.google', '"Roboto", sans-serif'),
      ));
      $this->sitecoretheme_fonts_heading_font_family_google->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    }

    $this->addElement('Text', 'sitecoretheme_fonts_heading_fontsize', array(
      'label' => 'Font Size for Headings',
      'description' => 'Enter font size for headings.',
      'required' => true,
      'value' => $coreSettings->getSetting('sitecoretheme.fonts.heading.fontsize', 15),
    ));

    if( $this->_fontType ) {
      $this->addElement('Select', 'sitecoretheme_fonts_mainmenu_font_family', array(
        'label' => 'Font Family for Main Menu',
        'description' => 'Select font family for main menu.',
        'multiOptions' => $webSafeFonts,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.mainmenu.font.family', ''),
      ));
    } else {
      $this->addElement('Select', 'sitecoretheme_fonts_mainmenu_font_family_google', array(
        'description' => 'Select font family for main menu.',
        'label' => 'Font Family for Main Menu',
        'required' => true,
        'multiOptions' => $googleFontFamilies,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.mainmenu.font.family.google', '"Roboto", sans-serif'),
      ));
      $this->sitecoretheme_fonts_mainmenu_font_family_google->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    }

    $this->addElement('Text', 'sitecoretheme_fonts_mainmenu_font_size', array(
      'label' => 'Font Size for Main Menu',
      'description' => 'Enter font size for main menu.',
      'required' => true,
      'value' => $coreSettings->getSetting('sitecoretheme.fonts.mainmenu.font.size', 14),
    ));

    if( $this->_fontType ) {
      $this->addElement('Select', 'sitecoretheme_fonts_tab_font_family', array(
        'label' => 'Font Family for Tabs',
        'description' => 'Select font family for menu tabs.',
        'multiOptions' => $webSafeFonts,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.tab.font.family', ''),
      ));
    } else {
      $this->addElement('Select', 'sitecoretheme_fonts_tab_font_family_google', array(
        'description' => 'Select font family for menu tabs.',
        'label' => 'Font Family for Tabs',
        'required' => true,
        'multiOptions' => $googleFontFamilies,
        'value' => $coreSettings->getSetting('sitecoretheme.fonts.tab.font.family.google', '"Roboto", sans-serif'),
      ));
      $this->sitecoretheme_fonts_tab_font_family_google->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    }
    $this->addElement('Text', 'sitecoretheme_fonts_tab_font_size', array(
      'label' => 'Font Size for Tabs under Menu',
      'description' => 'Enter font size for tabs under menu.',
      'required' => true,
      'value' => $coreSettings->getSetting('sitecoretheme.fonts.tab.font.size', 13),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}