<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ManageCategorySettings.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Form_Admin_ManageCategorySettings extends Engine_Form {

  public function init() {

    $this->setTitle('Manage Member Roles')
        ->setDescription('Here, you can add and manage the various Member Roles for the members of pages on your site. Below, you can also choose who all will be able to create these Member Roles.');

    // Element : restriction
    $this->addElement('Radio', 'sitepagemember_category_settings', array(
			'label' => 'Addition of Member Roles',
			'description' => 'Select below that who should be able to add member roles for the pages on your site.',
			'multiOptions' => array(
				'1' => 'Only Site Admin',
				'2' => 'Only Page Admins',
				'3' => 'Both Site Admin and Page Admins',
			),
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1),
    ));
    
    $this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
    ));
  }
}