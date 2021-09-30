<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Invite.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_Invite extends Engine_Form {

  public function init() {

    $this->setTitle('Add People to Page')
      ->setDescription('Who do you want to add to the page?')
      ->setAttrib('id', 'group_form_invite');
    
    $this->addElement('Checkbox', 'all', array(
      'id' => 'selectall',
      'label' => 'Choose All Friends',
      'ignore' => true
    ));

    $this->addElement('MultiCheckbox', 'users', array(
      'label' => 'Members',
      'required' => true,
      'allowEmpty' => 'false',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Invites',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}