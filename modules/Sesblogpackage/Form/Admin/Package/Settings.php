<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Settings.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_Form_Admin_Package_Settings extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this
        ->setTitle('Blog Creation Packages Settings')
        ->setDescription('From this section you can choose to enable creation of blogs based on the Package selected. You can create multiple packages - both Free and Paid, with various features such that members can choose the packages as per their requirements. Note: When you enable the blog creation based on packages on your website, then the similar settings will not work from the member level settings.')
        ->setAttrib('class', 'global_form_popup');

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $supportTicket = '<a href="https://socialnetworking.solutions/tickets" target="_blank">Support Ticket</a>';
    $sesSite = '<a href="https://socialnetworking.solutions" target="_blank">SocialNetworking.Solutions website</a>';
    $descriptionLicense = sprintf('Enter your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);


    if ($settings->getSetting('sesblogpackage.pluginactivated')) {


      $this->addElement('Select', 'sesblogpackage_enable_package', array(
          'label' => 'Enable Package',
          'description' => 'Do you want to enable packages for creation of Blogs on your website? (If you enable Packages, then users will always redirect to the Package selection page, even you have chosen to open the Blog Creation form in Popup.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onchange' => 'enable_package(this.value)',
          'value' => $settings->getSetting('sesblogpackage.enable.package', 0),
      ));

      $information = array('description' => 'Package Description', 'featured' => 'Featured (You can choose to auto-featured blogs in each package.)', 'sponsored' => 'Sponsored (You can choose to auto-sponsored blogs in each package.)', 'verified' => 'Verified (You can choose to auto-verified blogs in each package.)', 'custom_fields' => 'Custom Profile Fields');

      $this->addElement('MultiCheckbox', 'sesblogpackage_package_info', array(
          'label' => 'Package Inclusions',
          'description' => 'Select from below the features that you want to include in the Packages for creating blogs on your website. If you select any option, then that option will be displayed to your users when they try to create a blog. If you simply do not want to show any feature in the package at user end, then do not select it from here.',
          'multiOptions' => $information,
          'value' => $settings->getSetting('sesblogpackage.package.info', array_keys($information)),
      ));

      $this->addElement('Radio', 'sesblogpackage_payment_mod_enable', array(
          'label' => 'Activate Blogs',
          'description' => "Do you want to enable blogs immediately after payment, before the payment passes the gateways' fraud checks? This may take anywhere from 20 minutes to 4 days, depending on the circumstances and the gateway.",
          'multiOptions' => array(
              'all' => 'Enable Blog immediately.',
              'some' => 'Enable if user has an existing successful transaction, wait if this is their first.',
              'none' => 'Wait until the gateway signals that the payment has completed successfully.'
          ),
          'value' => $settings->getSetting('sesblogpackage.payment.mod.enable', 'all'),
      ));

      $this->addElement('Radio', 'sesblogpackage_package_style', array(
          'label' => 'Package Alignment',
          'description' => "Choose the alignment for packages on Package Selection page. This setting will only effect the new packages. Existing package will show in Horizontal View only.",
          'multiOptions' => array(
              '0' => 'Horizontal',
              '1' => 'Vertical',
          ),
          'value' => $settings->getSetting('sesblogpackage.package.style', 1),
      ));

      // Buttons
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Settings',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper')
      ));
	  } else {
      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate Your Plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}
