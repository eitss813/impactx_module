<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Contact.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Contact extends Engine_Form {

  public function init() {

    $this->setTitle('Key Contact Form');
    $this->setMethod('post');

    $this->addElement('Text', 'contactName', array(
        'label' => 'Enter the contact name.',
        'allowEmpty' => false,
        'required' => true,
    ));
        $this->addElement('Hidden', 'toValues', array(
      'label' => 'Send To',
      'required' => true,
      'allowEmpty' => false,
      'order' => 2,
      'validators' => array(
        'NotEmpty'
      ),
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);
      $this->addElement('Text', 'designation', array(
        'label' => 'Designation',
        'allowEmpty' => false,
        'required' => true,
    ));
    

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}
