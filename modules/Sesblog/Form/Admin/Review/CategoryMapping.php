<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: CategoryMapping.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_Review_CategoryMapping extends Engine_Form {

  public function init() {

    $this->setTitle("Choose a Profile Type for Mapping")
            ->setDescription('')
            ->setMethod('POST');

    $profiletype = array();
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sesblog_review');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $options = $profileTypeField->getElementParams('sesblog_review');
      unset($options['options']['order']);
      unset($options['options']['multiOptions']['0']);
      $profiletype = $options['options']['multiOptions'];
    }

    $this->addElement('Select', 'profile_type', array(
        'label' => 'Map Profile Type',
        'description' => 'Map this category with the profile type, so that questions belonging to the mapped profile type will appear to users while creating / editing their blogs when they choose the associated Category.',
        'allowEmpty' => true,
        'required' => false,
        'multiOptions' => $profiletype
    ));

    $this->addElement('Button', 'execute', array(
        'label' => 'Save',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
        'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
        'prependText' => ' or ',
        'label' => 'cancel',
        'link' => true,
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        ),
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
  }
}
