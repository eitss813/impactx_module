<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Photos.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
class Sesblog_Form_Album_Photos extends Engine_Form {

  public function init() {
    $this
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;
    $this->addElement('Radio', 'cover', array(
        'label' => 'Album Cover',
    ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
    ));
  }

}
