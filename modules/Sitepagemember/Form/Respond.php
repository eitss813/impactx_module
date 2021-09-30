<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Respond.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_Respond extends Engine_Form {

  public function init() {

    $this->setMethod('POST')
			   ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

//     $this->setTitle('Respond to Membership Request')
//         ->setDescription('Respond to Membership Request.');
        
    $this->addElement('Button', 'accept', array(
      'label' => 'Accept',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit',
    ));
    
    $this->addElement('Button', 'reject', array(
      'label' => 'Reject',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit',
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

    $this->addDisplayGroup(array('accept', 'reject', 'cancel'), 'buttons');
  }
}