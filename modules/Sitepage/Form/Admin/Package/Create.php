<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Package_Create extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Create New Directory / Pages Package')
            ->setDescription('Create a new page package over here. Below, you can configure various settings for this package like tell a friend, overview, map, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.');

    // Element: title
    $this->addElement('Text', 'title', array(
        'label' => 'Package Name',
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
    ));

    // Element: description
    $this->addElement('Textarea', 'description', array(
        'label' => 'Package Description',
        'validators' => array(
            array('StringLength', true, array(0, 250)),
        )
    ));

    // Element: level_id
    $multiOptions = array('0' => 'All Levels');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      if ($level->type == 'public') {
        continue;
      }
      $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    $this->addElement('Multiselect', 'level_id', array(
        'label' => 'Member Levels',
        'description' => 'Select the Member Levels to which this Package should be available. Only users belonging to the selected Member Levels will be able to create page of this package.',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => $multiOptions,
        'value' => array('0')
    ));



    // Element: price
     $this->addElement('Text', 'price', array(
        'label' => 'Price',
        'description' => 'The amount to charge from the page owner. Setting this to zero will make this a free package.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('Float', true),
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0.00',
    ));

    // Element: recurrence @ todo

    $this->addElement('Duration', 'recurrence', array(
        'label' => 'Billing Cycle',
        'description' => 'How often should Pages of this package be billed? (You can choose the payment for this package to be one-time or recurring.)',
        'required' => true,
        'allowEmpty' => false,
        //'validators' => array(
        //array('Int', true),
        //array('GreaterThan', true, array(0)),
        //),
        'value' => array(1, 'month'),
    ));
    //unset($this->getElement('recurrence')->options['day']);
    //$this->getElement('recurrence')->options['forever'] = 'One-time';
    // Element: duration
    $this->addElement('Duration', 'duration', array(
        'label' => 'Billing Duration',
        'description' => 'When should this package expire? For one-time packages, the package will expire after the period of time set here. For recurring plans, the user will be billed at the above billing cycle for the period of time specified here.',
        'required' => true,
        'allowEmpty' => false,
        //'validators' => array(
        //  array('Int', true),
        //  array('GreaterThan', true, array(0)),
        //),
        'value' => array('0', 'forever'),
    ));

    // renew
    $this->addElement('Checkbox', 'renew', array(
        'description' => 'Renew Link',
        'label' => 'Page creators will be able to renew their pages of this package before expiry. (Note: Renewal link after expiry will only be shown for pages of paid packages, i.e., packages having a non-zero value of Price above.)',
        'value' => 0,
        'onclick' => 'javascript:setRenewBefore();',
    ));
    $this->addElement('Text', 'renew_before', array(
        'label' => 'Renewal Frame before Page Expiry',
        'description' => 'Show page renewal link these many days before expiry.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('Int', true),
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0',
    ));

    // auto aprove
    $this->addElement('Checkbox', 'approved', array(
        'description' => "Auto-Approve",
        'label' => 'Auto-Approve pages of this package. These pages will not need admin moderation approval before going live.',
        'value' => 0,
    ));

    // Element:sponsored
    $this->addElement('Checkbox', 'sponsored', array(
        'description' => "Sponsored",
        'label' => 'Make pages of this package as Sponsored. Note: A change in this setting later on will only apply on new pages that are created in this package.',
        'value' => 0,
    ));

    // Element:featured
    $this->addElement('Checkbox', 'featured', array(
        'description' => "Featured",
        'label' => 'Make pages of this package as Featured. Note: A change in this setting later on will only apply on new pages that are created in this package.',
        'value' => 0,
    ));
    // check extension installed or not
      $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
      if ($featureExtension) {
        // Element:service
        $this->addElement('Checkbox', 'service', array(
            'description' => "Services",
            'label' => 'Enable Service for pages of this package. (Using this, users will be able to create services for their Pages)',
            'value' => 1,
        ));
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1)) {
        $this->addElement('Checkbox', 'ads', array(
            'description' => "Community Ads Display",
            'label' => "Display Community Ads in pages of this package at various positions as configured in Ad Settings. (With this setting, you can choose to grant privilege of being ad-free to pages of special / paid packages.)",
            'value' => 1,
        ));
      } else {
        $this->addElement('dummy', 'ads', array(
            'label' => "Community Ads Display",
            'description' => 'You cannot configure this setting for packages because you have disabled Community Ads display for pages using "Community Ads in this plugin" field in Ad Settings.',
        ));
      }
    } else {
      $desc_modules = Zend_Registry::get('Zend_Translate')->_('To be able to configure this setting for directory items / pages on your website, please install and enable the <a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">"Advertisements / Community Ads Plugin"</a> which enables rich, interactive and effective advertising on your website. With this setting, you can choose to grant privilege of being ad-free to pages of special / paid packages, while showing community ads on pages of other packages.');
      $this->addElement('dummy', 'ads', array(
          'label' => "Community Ads Display",
          'description' => $desc_modules,
      ));

      $this->ads
              ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    // Element:tellafriend
    $this->addElement('Checkbox', 'tellafriend', array(
        'description' => "Tell a friend",
        'label' => 'Display "Tell a friend" link on profile of pages of this package. (Using this, users will be able to email a page to their friends.)',
        'value' => 0,
    ));

    // Element:print
    $this->addElement('Checkbox', 'print', array(
        'description' => 'Print',
        'label' => 'Display "Print" link on profile of pages of this package. (Using this, users will be able to print the information of pages.)',
        'value' => 0,
    ));

    // Element: overview
    $this->addElement('Checkbox', 'overview', array(
        'description' => "Overview",
        'label' => 'Enable Overview for pages of this package. (Using this, users will be able to create rich profiles for their pages using WYSIWYG editor.)',
        'value' => 0,
    ));

    // Element:map
    $this->addElement('Checkbox', 'map', array(
        'description' => "Location Map",
        'label' => 'Enable Location Map for pages of this package. (Using this, users will be able to specify detailed location for their pages and the corresponding Map will be shown on the Page Profile.)',
        'value' => 0,
    ));

    // Element:insights
    $this->addElement('Checkbox', 'insights', array(
        'description' => "Insights",
        'label' => 'Show Insights for pages of this package to their admins. (Insights for pages show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. Using this, page admins will also be getting periodic, auto-generated emails containing Page insights.)',
        'value' => 0,
    ));

    // Element:contact_details
    $this->addElement('Checkbox', 'contact_details', array(
        'description' => "Contact Details",
        'label' => 'Enable Contact Details for pages of this package. (Using this, page admins will be able to mention contact details for their pages\' entity. These contact details will also be displayed on page profile and browse pages.)',
        'value' => 0,
    ));

//    // Element:foursquare
//    $this->addElement('Checkbox', 'foursquare', array(
//        'description' => "Save To Foursquare Button",
//        'label' => "Enable 'Save to foursquare' buttons for pages of this package. (Using this, 'Save to foursquare' buttons will be shown on profiles of pages having location information. These buttons will enable page visitors to add the page's place or tip to their foursquare To-Do List. Page Admins will get the option for enabling this in the \"Marketing\" section of their Dashboard.)",
//        'value' => 0,
//    ));

    // Element:Twitter
    $sitepagetwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter');
    if ($sitepagetwitterEnabled) {
      $this->addElement('Checkbox', 'twitter', array(
          'description' => "Display Twitter Updates",
          'label' => "Enable displaying of Twitter Updates for pages of this package. (Using this, page admins will be able to display their Twitter Updates on their Page profile. Page Admins will get the option for entering their Twitter username in the \"Marketing\" section of their Dashboard. From the Layout Editor, you can choose to place the Twitter Updates widget either in the Tabs container or in the sidebar on Page Profile.)",
          'value' => 0,
      ));
    }

    // Element:sendupdate
    $this->addElement('Checkbox', 'sendupdate', array(
        'description' => "Send an Update",
        'label' => "Enable 'Send an Update' for pages of this package. (Using this, page admins will be able send updates for their pages' entity to users who Like their page. Page Admins will get this option in the \"Marketing\" section of their Dashboard.",
        'value' => 0,
    ));

    // Element:modules
    $includeModules = Engine_Api::_()->sitepage()->getEnableSubModules('adminPackages');
    if (!empty($includeModules)) {
      $desc_modules = sprintf(Zend_Registry::get('Zend_Translate')->_("Select the modules / apps that should be available to pages of this package. (Modules / apps provide extended functionalities to pages, and are great value additions. To see all the apps that you can have for directory items / pages on your site, please visit %s.)"), "<a href='http://www.socialengineaddons.com/catalog/directory-pages-extensions' target='_blank' >here</a>");
      $this->addElement('MultiCheckbox', 'modules', array(
          'description' => $desc_modules,
          'label' => 'Modules / Apps',
          'multiOptions' => $includeModules,
          'RegisterInArrayValidator' => false,
          'escape' => false,
          'value' => 0,
      ));
      $this->modules
              ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }


    // Element : profile
    $this->addElement('Radio', 'profile', array(
        'label' => 'Profile Information',
        'description' => 'Allow Profile Information for directory items / pages of this package. Below, you can also choose whether all, or restricted, profile information should be available to pages of this package. (Using this, page admins will be able to add additional information about their page depending on its category. This non-generic additional information will help others know more specific details about the page. You can create new Profile Types & fields for directory items / pages on your site, and associate them with the Categories of directory items / pages from the "Profile Fields" and "Category-Page Profile Mapping" sections. Such a mapping will enable you to configure profile fields for pages depending on the category they belong to.)',
        'multiOptions' => array(
            '1' => 'Yes, allow profile information with ALL available fields.',
            '0' => 'No, do not allow profile information for pages of this package.',
            '2' => 'Yes, allow profile information with RESTRICTED fields. (Below, you can choose the profile fields that should be available. With this configuration, you can give access to more profile fields to packages of higher cost.)',
        ),
        'value' => 1,
        'onclick' => 'showprofileOption(this.value)',
    ));

    //Add Dummy element for using the tables
    $this->addElement('Dummy', 'profilefield', array(
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_profilefield.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Checkbox', 'update_list', array(
        'description' => 'Show in "Other available Packages" List',
        'label' => "Show this package in the list of 'Other available Packages' which gets displayed to the users for upgrading the package of a Page at Page dashboard. (This will be useful in case you are creating a free package or a test package and you want it to be used by the users only once for a limited period of time and do not want to show it during package upgrdation.)",
        'value' => 1,
    ));
    // Element: enabled
    $this->addElement('hidden', 'enabled', array(
         'order' => 900,
        'value' => 1,
    ));


    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Create Package',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'ignore' => true,
        'link' => true,
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
        'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        )
    ));
    
  }

}

?>
