<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Listclaimmember.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Listclaimmember extends Engine_Form {

  public function init() {

    $this->setMethod('post');
    $this->setTitle("Add Member")
            ->setDescription('Use the auto-suggest box given below to add a member on whose pages the "Claim this Page" link will appear. (Note that the added member will also have authority to decide whether his page should have this link or not.)');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Start typing the name of the member')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:250px;');

    $this->addElement('Hidden', 'user_id', array( 'order' => 900,));

    $this->addElements(array(
        $label,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Add Member',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>