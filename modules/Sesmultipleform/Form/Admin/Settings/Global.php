<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
		  $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $supportTicket = '<a href="https://socialnetworking.solutions/tickets" target="_blank">Support Ticket</a>';
    $sesSite = '<a href="https://socialnetworking.solutions" target="_blank">SocialNetworking.Solutions website</a>';
    $descriptionLicense = sprintf('Enter your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);

    $this->addElement('Text', "sesmultipleform_licensekey", array(
        'label' => 'Enter License key',
        'description' => $descriptionLicense,
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesmultipleform.licensekey'),
    ));
    $this->getElement('sesmultipleform_licensekey')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    if ($settings->getSetting('sesmultipleform.pluginactivated')) {
	    $this->addElement('textarea', 'sesmultipleform_ipaddressban', array(
		      'label' => 'IP Address Ban From Viewing Forms',
					'description' => 'Enter the IP addresses (comma separated) to which Forms created using this plugin will not be shown. This setting will affect all the forms created using this plugin.',
		      'filters' => array(
	        new Engine_Filter_Censor(),
		      ),
		       'value' => $settings->getSetting('sesmultipleform.ipaddressban'),
		  ));
	    // Add submit button
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save Changes',
	      'type' => 'submit',
	      'ignore' => true
	    ));
    } else {

	    $this->addElement('MultiCheckbox', 'sesmultipleform_footer_widgets', array(
				'label' => "Enable Default Buttons",
				'description'=>'Do you want to enable "Feedback" & "Contact Us" buttons in the right side of your website? (Note: This is a one time setting and will not be shown after plugin is activated.)',
				'multiOptions' => array(
					'contactus' => 'Contact Us',
					'feedback' => 'Feedback'
				),
				'value' => array('contactus','feedback'),
			 ));

    	if (APPLICATION_ENV == 'production') {
				$this->addElement('Checkbox', 'system_mode', array(
				'label' => 'Please make sure that you change the mode of your website from "Production Mode" to "Development Mode" before activating the plugin and again to "Production Mode" after successfully activating the plugin to reflect CSS changes from this plugin on user side.',
				'description' => 'System Mode',
				'value' => 0,
				));
			}

      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate your plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }
}
