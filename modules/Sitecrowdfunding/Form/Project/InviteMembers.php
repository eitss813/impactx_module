<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InviteMembers.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitecrowdfunding_Form_Project_InviteMembers extends Engine_Form {

  public function init() {
  
		/*$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		$memberSettings = $coreSettings->getSetting( 'sitecrowdfunding.automatically.addmember' , 1);
		if (!empty($memberSettings)) {
			$this->setTitle('Add People to this Project');
			$this->setDescription('Select the people you want to add to this project.')
			->setAttrib('id', 'messages_compose');
			$Button = 'Add People';
		} else {
			$this->setTitle('Invite People to this Project');
			$this->setDescription('Select the members you want to invite to this project.')
			->setAttrib('id', 'messages_compose');;
			$Button = 'Invite People';
		}*/

  $this->setTitle('Add People to this Project');
  $this->setDescription('Select the people you want to add to this project.')
      ->setAttrib('id', 'messages_compose');
  $Button = 'Add People';

  $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id');
  $roles = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->getRolesAssoc($project_id);
  if (!empty($roles)) {
      $roleKey = array();
      foreach ($roles as $k => $role) {
          $role_name[$k] = $role;
          $roleKey[] = $k;
      }
      reset($role_name);

      $this->addElement('Multiselect', 'role_id', array(
          'label' => 'ROLE',
          'multiOptions' => $role_name,
          'value' => $roleKey,
      ));
  }
    // init to
    $this->addElement('Text', 'user_ids',array(
			'description'=>'Start typing the name of the member...',
			'autocomplete'=>'off',
      'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
    ));
    Engine_Form::addDefaultDecorators($this->user_ids);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'order' => '7',
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    $this->addElement('Button', 'submit', array(
      'label' => $Button,
      'ignore' => true,
      'order' => '8',
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'order' => '9',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
  }
}