<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ManageCategorySettings.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitecrowdfunding_Form_Admin_Settings_ManageMemberRoles extends Engine_Form {

  public function init() {

    $this->setTitle('Manage Member Roles')
        ->setDescription('Here, you can add and manage the various Member Roles for the members of projects on your site. Below, you can also choose who all will be able to create these Member Roles.');

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    // Element : restriction
    $this->addElement('Radio', 'sitecrowdfunding_member_category_settings', array(
			'label' => 'Addition of Member Roles',
			'description' => 'Select below that who should be able to add member roles for the projects on your site.',
			'multiOptions' => array(
				'1' => 'Only Site Admin',
				'2' => 'Only Project Admins',
				'3' => 'Both Site Admin and Project Admins',
			),
			'value' => $coreSettings->getSetting('sitecrowdfunding.member.category.settings', 1),
    ));

  $this->addElement( 'Radio' , 'sitecrowdfunding_member_title' , array (
      'label' => 'Member Roles',
      'description' => "Do you want project members to be able to select their member roles in the projects ?",
      'multiOptions' => array (
          1 => 'Yes' ,
          0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'sitecrowdfunding.member.title' , 1),
  ));
     
    $this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
    ));
  }
}