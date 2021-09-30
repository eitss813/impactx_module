<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Clone.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Themes_Clone extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Theme Manager')
      ->setDescription('Here, you can create a new color scheme by cloning the existing theme. You can customize various parameters of the cloned theme, like, color, font size, font family etc.')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Theme Title',
      'description' => 'Enter the title for new color scheme.',
      'required' => true
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Theme Description',
    ));

    $this->addElement('Select', 'clonedname', array(
      'label' => 'Base Color Scheme',
      'multiOptions' => array(),
    ));
 
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Clone',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'link' => true,
      'label' => 'Cancel',
      'onclick' => 'history.go(-1); return false;',
      'decorators' => array(
        'ViewHelper'
      )
    ));
  }
}