<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Buttons.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_Buttons extends Engine_Form
{

  public function init()
  {
      $this->addElement('Text', 'button_title1', array(
        'label' => 'Button 1 Title',
        'value' => ''
      ));

      $this->addElement('Text', 'icon1', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'url1', array(
        'label' => 'URL to be opened on Button 1 click',
        'value' => ''
      ));

      $this->addElement('Text', 'button_title2', array(
        'label' => 'Button 2 Title',
        'value' => ''
      ));

            $this->addElement('Text', 'icon2', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'url2', array(
        'label' => 'URL to be opened on Button 2 click',
        'value' => ''
      ));
      
      $this->addElement('Text', 'button_title3', array(
        'label' => 'Button 3 Title',
        'value' => ''
      ));

      $this->addElement('Text', 'icon3', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'url3', array(
        'label' => 'URL to be opened on Button 3 click',
        'value' => ''
      ));

      $this->addElement('radio', 'new_tab', array(
        'label' => "Open URL in new tab?",
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'value' => '0'
      ));
  }

}