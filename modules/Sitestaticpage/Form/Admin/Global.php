<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );
  
  public function init() {

    $this->setTitle("Global Settings")
            ->setDescription("These Settings affect all members in your community.");

    // SETTINGS API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // ELEMENT FOR LICENSE KEY
    $this->addElement('Text', 'sitestaticpage_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $settings->getSetting('sitestaticpage.lsettings'),
    ));

    if( APPLICATION_ENV == 'production' ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    //Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));
    
    $this->addElement('Radio', 'sitestaticpage_formsetting', array(
        'label' => 'Non-logged-in Visitors',
        'description' => 'Do you want the Custom Field Forms on Static Pages to be shown to non-logged-in visitors? (If yes, then non-logged-in visitors will be able to see the Forms on the Static Pages and submit the Forms created by Siteadmin.)
',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.formsetting', 1),
    ));

    $this->addElement('Radio', 'sitestaticpage_saveformdata', array(
        'label' => 'Save Forms Data',
        'description' => ' Do you want to save the Form data that the users will fill in the Custom Field Forms on Static pages. (Form data will not be saved in case of non-logged-in visitors.)

',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.saveformdata', 0),
    ));

    // ELEMENT FOR DEFAULT URL
    $this->addElement('Text', 'sitestaticpage_manifestUrl', array(
        'label' => 'Alternate Text for “static” in URLs of Static Pages',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'In the URLs of content from this plugin, you can change the default term: “static” to something of your choice, thus allowing you to use this plugin efficiently for a functionality different from just static pages. Please enter your desired text if you want to change the term: "static" in content URLs from this plugin.',
        'value' => $settings->getSetting('sitestaticpage.manifestUrl', "static"),
    ));

    // ELEMENT FOR MULTILANGUAGE
    $this->addElement('Radio', 'sitestaticpage_multilanguage', array(
        'label' => 'Multiple Languages for Static Pages & HTML Blocks',
        'description' => "Do you want to enable multiple languages for Static Pages and HTML Blocks at the time of their creation? (Select 'Yes', only if you have installed multiple language packs from the 'Language Manager' section of the Admin Panel. Selecting 'Yes' over here will enable creation of Static Pages and HTML Blocks in the multiple languages installed on your site. If you select 'No' over here, then the pack that you've marked as your 'default' pack will be the language displayed for creation of Static Pages and HTML Blocks.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestaticpage.multilanguage', 0),
    ));

    //GET EXISTING LANGUAGES ARRAY
    $localeMultiOptions = Engine_Api::_()->sitestaticpage()->getLanguageArray();
    $this->addElement('MultiCheckbox', 'sitestaticpage_languages', array(
        'label' => 'Languages',
        'description' => 'Select the languages for which you want users to be able to create Static Pages.',
        'multiOptions' => $localeMultiOptions,
        'value' => $settings->getSetting('sitestaticpage.languages'),
    ));

    // ADDING BUTTON.
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}