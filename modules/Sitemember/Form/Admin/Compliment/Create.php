<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_Form_Admin_Compliment_Create extends Engine_Form {
  public function init() {
    $this->setTitle('Add New Compliment')
      ->setDescription('Below you can add a unique compliment with it\'s name for your community. ')
      ->setAttrib('name', 'compliment_create');

    $this->addElement('Text', 'title', array(
      'label' => 'Compliment Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
    ));
    $this->addElement('File', 'photo', array(
      'label' => 'Compliment Icon',
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'action' =>
        'index')),
      'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      )
    ));
  }

}
