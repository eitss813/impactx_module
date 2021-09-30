<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contactinfo.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_NotificationSettings extends Engine_Form {
  public function init() {

//       $this->setTitle('Contact Details')
//               ->setDescription('Contact information will be displayed in the Info section of your page profile.')
//               ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
//               ->setAttrib('name', 'contactinfo');

		$this->addElement('Checkbox', 'email', array(
				'description' => 'Email Notifications',
				'label' => 'Send email notifications to me when people post an update, or create various content on this Page.',
				'onclick' => 'showEmailAction()',
				'value' => 1,
		));
		
	  $this->addElement( 'MultiCheckbox' , 'action_email' , array (
			//'label' => 'Select the options that you want to be recive notification when any member post, comment, like, follow and create content.',
			'multiOptions' => array("posted" => "People post updates on this page", "created" => "People create various contents on this page"),
			'value' => array("posted", "created")
		)) ;
		
		
	  $this->addElement('Checkbox', 'notification', array(
				'description' => 'Site Notifications',
				'label' => 'Send notification updates to me when people perform various actions on this Page (Below you can individually activate notifications for the actions).',
				'onclick' => 'showNotificationAction()',
				'value' => 0,
		));
		
		$action_notification = array();
		$action_notification = array("posted" => "People post updates on this page", "created" => "People create various contents on this page", "comment" => "People post comments on this page", "like" => "People like this page", "follow" => "People follow this page");
		if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$action_notification['join'] = 'People join this page';
		}
		
		$this->addElement( 'MultiCheckbox' , 'action_notification' , array (
			'multiOptions' => $action_notification,
			'value' => array("posted", "created", "comment", "like", "follow")
		)) ;
		
		$this->addElement('Button', 'submit', array(
				'label' => 'Save Changes',
				'type' => 'submit',
				'ignore' => true,
		));
    
  }
}