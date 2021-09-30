<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contactinfo.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Service extends Engine_Form {

  public function init() {

    $this->setTitle('Service Details')
    ->setDescription('Service provided to the particular page')
    ->setAttrib('class', 'global_form_popup')
    ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'title', array(
      'label' => 'Service Name:',
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '40')),
      )));

    $this->addElement('Text', 'body', array(
      'label' => 'Description:',
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '150')),
      )));

    $this->addElement('File', 'photo', array(
      'label' => 'Choose Logo',
      'accept' => 'image/*',
      'required' => true,
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    $this->addElement('Integer', 'duration', array(
      'label' => 'Duration',
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '50')),
      )));

    $this->addElement('Select', 'duration_type', array(
      'label' => 'Duration Type',
      'multiOptions' => array('minutes' => 'Minutes','hours' => 'Hours','days' => 'Days'),
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '80')),
      )));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Details',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}

?>