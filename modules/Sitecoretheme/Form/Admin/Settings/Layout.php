<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layout.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Layout extends Engine_Form
{

  public function init()
  {
    $this->setTitle("Layout Settings");
    $this->setDescription("Here you can manage the layout of this theme.");

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'sitecoretheme_layout_theme_width', array(
      'label' => 'Theme Width',
      'description' => 'Enter total width of theme [Note: Minimum width should be - 1200px]',
      'required' => true,
      'value' => $coreSettings->getSetting('sitecoretheme.layout.theme.width', '1200'),
    ));

    $this->addElement('Text', 'sitecoretheme_layout_left_column_width', array(
      'label' => 'Left Column Width',
      'description' => 'Enter width for left column [Note: Width should be in range from 220 - 300 px. Width of center column will be set after deducting the width of left and right column.]',
      'required' => true,
      'allowEmpty' => false,
      'value' => $coreSettings->getSetting('sitecoretheme.layout.left.column.width', '250'),
    ));

    $this->addElement('Text', 'sitecoretheme_layout_right_column_width', array(
      'label' => 'Right Column Width',
      'description' => 'Enter width for right column [Note: Width should be in range from 220 - 300 px. Width of center column will be set after deducting the width of left and right column.]',
      'required' => true,
      'allowEmpty' => false,
      'value' => $coreSettings->getSetting('sitecoretheme.layout.right.column.width', '250'),
    ));

    $this->addElement('Select', 'sitecoretheme_layout_container_design', array(
      'label' => 'Layout Container Style',
      'description' => 'Select the style which you want to apply on container of layout?',
      'multiOptions' => array(
        1 => 'Style 1',
        2 => 'Style 2',
        3 => 'Style 3'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.layout.container.design', 2),
    ));
    
    $this->addElement('Select', 'sitecoretheme_layout_container_headding_style', array(
      'label' => 'Heading Style',
      'description' => 'Select the style which you want to apply on block heading?',
      'onchange' => "showOption(this.value);",
      'multiOptions' => array(
        1 => 'Style 1',
        2 => 'Style 2',
        3 => 'Style 3',
        4 => 'Style 4',
        5 => 'Style 5 (With Icon)',
        6 => 'Style 6',
        7 => 'Style 7 (With Icon)',
        8 => 'Style 8 (With Icon)',
        9 => 'Style 9 (With Icon)',
        10 => 'Style 10',
        11 => 'Style 11 (With Icon)',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.layout.container.headding.style', 1),
    ));
    
    $this->addElement('Select', 'sitecoretheme_button_design', array(
      'label' => 'Button Style',
      'description' => 'Select the style which you want to apply on button?',
      'multiOptions' => array(
        1 => 'Style 1',
        2 => 'Style 2',
        3 => 'Style 3',
        4 => 'Style 4',
        5 => 'Style 5',
        6 => 'Style 6',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.button.design', 1),
    ));
    
    $this->addElement('Select', 'sitecoretheme_navtab_button_design_button', array(
      'label' => 'Navigation Menu Style',
      'description' => 'Select the style which you want to apply on navigation menu?',
      'multiOptions' => array(
        1 => 'Style 1',
        2 => 'Style 2',
        3 => 'Style 3',
        4 => 'Style 4',
        5 => 'Style 5',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.navtab.button.design.button', 1),
    ));

    $this->addElement('Select', 'sitecoretheme_landing_heading_icon', array(
      'label' => 'Icon below Heading',
      'description' => 'Select the icons which you want to display below the heading on Landing page?',
      'multiOptions' => array(
        "f10b" => 'Mobile',
        "f004" => 'Heart',
        "f0c8" => "Square",
        "f111" => "Circle",
        "f192" => "Dot Circle",
//        "f7c6" => "Sketch",
//        "f5cb" => "Vector Square",
        "f1ad" => "Building",
        "f2dc" => "Snowflake",
//        "f7d0" => "Snowman",
        "f005" => "Star",
        "f185" => 'Sun',
        "f225" => "Transgender Alt",
//        "f55f" => "Cannabis",
//        "f789" => "Centos",
//        "f445" => "Chess Queen",
        "f1cb" => "Codepen",
//        "f6d1" => "Dice D6",
//        "f6cf" => "Dice D20",
//        "f50c" => "Galactic Republic",
        "f001" => "Music",
        "f18c" => "Pagelines",
//        "f41b" => "React"
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.heading.icon', 'none'),
    ));
		$this->addElement('Radio', 'sitecoretheme_landingpage_width_type', array(
			'label' => 'Full Width Landing Page',
			'description' => 'Do you want to show landing page in full width?',
			'multiOptions' => array(
        '2' => 'Yes',
        '1' => 'No'
      ),
			'value' => $coreSettings->getSetting('sitecoretheme.landingpage.width.type', '2'),
		));
		$this->addElement('Radio', 'sitecoretheme_layout_hide_left_column', array(
      'label' => 'Hide Left Column in Mobile Device',
      'description' => 'Do you want to hide left column in Mobile devices?',
      'multiOptions' => array(
        'none' => 'Yes',
        'block' => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.layout.hide.left.column', 'none'),
    ));

    $this->addElement('Radio', 'sitecoretheme_layout_hide_right_column', array(
      'label' => 'Hide Right Column in Mobile Device',
      'description' => 'Do you want to hide right column in Mobile devices?',
      'multiOptions' => array(
        'none' => 'Yes',
        'block' => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.layout.hide.right.column', 'block'),
    ));
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}