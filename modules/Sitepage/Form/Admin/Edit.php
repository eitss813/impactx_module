<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Edit extends Engine_Form {

  public function init() {

    $this
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box')
            ->setTitle("Edit Verification")
            ->setdescription("You can edit verification comments from here.");


    $label = new Zend_Form_Element_Textarea('comments');
    $label->setLabel('Comments')
            //->setValue($fetchRow->comments)
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:300px;')
            ->setAttrib('onkeyup', 'textCounter(this, "counter-wrapper", 300);')
            ->setAttrib('maxlength', '300');
    $this->addElements(array(
        $label,));

    $this->addElement('Dummy', 'counter', array(
        'label' => "",
        'description' => ""
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Edit Comments',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
  }

}