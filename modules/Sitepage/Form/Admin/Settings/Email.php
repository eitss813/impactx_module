<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Email.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Settings_Email extends Engine_Form {

  public function init() {
    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //check if Sitemailtemplates Plugin is enabled
    $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

    $this
            ->setTitle('Email Settings for Insights')
            ->setDescription('This page contains settings for the Automatic email containing Page Insights. Below, you can enable / disable this email and configure the design of its template. You can also send sample email to yourself to see the template.');

    $this->addElement('Radio', 'sitepage_insightemail', array(
        'label' => 'Automatic Email Notifications for Page Insights',
        'description' => "Do you want the site to send automatic page insights to the page owners? (The design of the email template for this can be customized below, and the content of this email template can be customized from the Mail Templates section in Settings.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.insightemail', 1),
    ));

    $this->addElement('Select', 'sitepage_insightmail_options', array(
        'label' => 'Periodic Interval',
        'description' => 'Select the periodic interval between email notifications.',
        'multiOptions' => array(
            // '0' => '1 day',
            '1' => '1 week',
            '2' => '1 month',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.insightmail.time', 1),
    ));

    if(!$sitemailtemplates) {
			$this->addElement('Text', 'sitepage_site_title', array(
					'label' => 'Site Title',
					'filters' => array(
							new Engine_Filter_Censor(),
							'StripTags',
							new Engine_Filter_StringLength(array('max' => '63'))
					),
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1))
			));

			$this->addElement('Text', 'sitepage_header_color', array(
					'decorators' => array(array('ViewScript', array(
											'viewScript' => '_formImagerainbowHeader.tpl',
											'class' => 'form element'
							)))
			));

			$this->addElement('Text', 'sitepage_title_color', array(
					'decorators' => array(array('ViewScript', array(
											'viewScript' => '_formImagerainbowTitle.tpl',
											'class' => 'form element'
							)))
			));

			$this->addElement('Text', 'sitepage_bg_color', array(
					'decorators' => array(array('ViewScript', array(
											'viewScript' => '_formImagerainbowBgcolor.tpl',
											'class' => 'form element'
							)))
			));
    
    }
    $this->addElement('Checkbox', 'sitepage_demo', array(
        'label' => 'Send me a test email for page insights to check the above settings.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.demo', 1),
    ));

    $this->addElement('Text', 'sitepage_admin', array(
        'label' => 'Email ID for Testing',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
            array('EmailAddress', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admin', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
    ));
    $this->sitepage_admin->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
    $this->sitepage_admin->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>