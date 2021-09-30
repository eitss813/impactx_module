<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AddSubscriber.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_AddSubscriber extends Engine_Form {

  public function init() {

    $this->setTitle('Add New Subscriber')->setDescription("Below, you can also add new subscribers by using the 'Add New Subscriber' link below.")->setAttrib('id', 'form-newslettertype');
    $this->setMethod('post');

    $this->addElement('Select', 'choose_member', array(
      'label' => 'Choose from below the users who will be subscribed to newsletters on your website.',
      'multiOptions' => array(
        '1' => 'Website Members',
        '2' => 'Public Users [Guests]',
        '3' => 'Import Users from CSV',
      ),
      'onchange' =>  'choosemember(this.value);',
    ));

    $this->addElement('Text', "member_name", array(
        'description' => "Enter emails / auto-suggest name of member.",
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Hidden', 'user_id', array());

    $this->addElement('Textarea', 'external_emails', array(
      'label' => 'Enter email seprated by commas.',
      'required' => false,
      'allowEmpty' => true,
    ));

    $this->addElement('File', 'csvfile', array(
        'label' => 'Choose CSV File',
        'allowEmpty' => false,
        'required' => true,
    ));
    $this->csvfile->addValidator('Extension', false, 'csv');


    $types = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getResult(array('fetchAll' => 1));
    $newsTypes = array();
    foreach($types as $type) {
        $newsTypes[$type->type_id]  = $type->title;
    }
    $this->addElement('MultiCheckbox', 'newsletter_types', array(
      'description' => 'Select the type of Newsletters which will be subscribed by the users.',
      'multiOptions' => $newsTypes,
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
