<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CreateCompliment.php 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_Form_CreateCompliment extends Engine_Form {
  public function init() {
    $this->setTitle('')
      ->setDescription('')
      ->setAttrib('name', 'compliment_me');


    $this->addElement('Hidden', 'complimentcategory_id', array(
        'order' => 11
    ));
    $view = Zend_Registry::get('Zend_View');
    $this->addElement('Textarea', 'body', array(
      'label' => '',
      'placeholder'=> $view->translate('Compliment something ...'),
      'allowEmpty' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(), 
      ),
    ));
     // Element: execute
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
      'onclick'=>'SmoothboxSEAO.close()',
      'link' => true,
      'href' =>'javascript:void(0)',
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
