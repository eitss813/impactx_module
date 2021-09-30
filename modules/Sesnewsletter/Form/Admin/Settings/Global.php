<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_Settings_Global extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this
        ->setTitle('Global Settings')
        ->setDescription('These settings affect all members in your community.');

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $supportTicket = '<a href="https://socialnetworking.solutions/tickets" target="_blank">Support Ticket</a>';
    $sesSite = '<a href="https://socialnetworking.solutions" target="_blank">SocialNetworking.Solutions website</a>';
    $descriptionLicense = sprintf('Enter your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);

    $this->addElement('Text', "sesnewsletter_licensekey", array(
        'label' => 'Enter License key',
        'description' => $descriptionLicense,
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesnewsletter.licensekey'),
    ));
    $this->getElement('sesnewsletter_licensekey')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    if ($settings->getSetting('sesnewsletter.pluginactivated')) {

        $this->addElement('Radio', 'sesnewsletter_enabletestmode', array(
            'label' => 'Enable Testing Mode',
            'description' => 'Do you want to enable the testing mode of this plugin? If you choose Yes, then users will not see any newsletter related options and the newsletters you will send will be sent to the test email ids.',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'onchange' => 'showtestmde(this.value)',
            'value' => $settings->getSetting('sesnewsletter.enabletestmode', 0),
        ));

        $this->addElement('Text', "sesnewsletter_testemail", array(
            'label' => 'Test Email',
            'description' => "Enter any 1 email id on which you will receive the newsletters sent in testing mode.",
            'allowEmpty' => true,
            'required' => false,
            'value' => $settings->getSetting('sesnewsletter.testemail', ''),
        ));


        $this->addElement('Radio', 'sesnewsletter_emailsubsverify', array(
            'label' => 'Email Verification by Subscribers',
            'description' => 'Choose from the below who will receive verification emails before being added to the newsletter subscriber list on your website. If you want to add emails directly to the subscriber list, then select "None of the above" option below.',
            'multiOptions' => array(
                '1' => 'All',
                '2' => 'Only Site Members',
                '3' => "Only Guest Members",
                '4' => "None of the above",
            ),
            'value' => $settings->getSetting('sesnewsletter.emailsubsverify', 4),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    } else {

      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate your plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}
