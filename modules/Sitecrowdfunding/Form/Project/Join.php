<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Join.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitecrowdfunding_Form_Project_Join extends Engine_Form	{

  public function init() {
  
    $this->setTitle('Join Project')->setDescription('Would you like to join this Project?');

    $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id',null);

		if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitecrowdfunding.member.title', 1)) {
		
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
		}

    $this->addElement('Button', 'submit', array(
      'label' => 'Join Project',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
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

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');


    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
  }
}
