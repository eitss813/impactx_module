<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: sitepage.php 10072 2013-07-24 22:38:42Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitepage_Form_Unlinkpage extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Unlink Page')
      ->setDescription('Are you sure you want to unlink this page?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    //$this->addElement('Hash', 'token');
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'UNLINK',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}