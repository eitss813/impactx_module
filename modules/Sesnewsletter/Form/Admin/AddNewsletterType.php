<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AddNewsletterType.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_AddNewsletterType extends Engine_Form {

  public function init() {

    $this->setTitle('Create New Newsletter Type')->setAttrib('id', 'form-newslettertype');
    $this->setMethod('post');

    $this->addElement('Text', 'title', array(
        'label' => 'Enter the title for this newsletter type. Subscribers will be able to choose to receive newsletter of this type or not.',
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Select', 'singupuser', array(
       'label' => 'Do you want newly signed-up members to auto subscribe this newsletter type?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => 1,
    ));

    $this->addElement('Select', 'existinguser', array(
       'label' => 'Do you want existing site members to auto subscribe this newsletter type?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => 0,
    ));

    $this->addElement('Select', 'guestuser', array(
       'label' => 'Do you want guest members to auto subscribe this newsletter type?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => 1,
    ));

	$this->addElement('Select', 'enabled', array(
       'label' => 'Do you want to enable this newsletter type?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => 1,
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
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
