<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Import.php  2018-11-30 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_Import_Import extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Upload CSV file only');

    $this->addElement('File', 'csvfile', array(
        'label' => '',
        'allowEmpty' => false,
        'required' => true,
    ));
    $this->csvfile->addValidator('Extension', false, 'csv');

    $this->addElement('Button', 'submit', array(
        'label' => 'Import',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'link' => true,
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }
}
