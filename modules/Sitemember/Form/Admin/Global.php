<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );

  public function init() {

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    //GENERAL HEADING
    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');


    // ELEMENT FOR LICENSE KEY
    $this->addElement('Text', 'sitemember_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $coreSettings->getSetting('sitemember.lsettings'),
    ));

    if (APPLICATION_ENV == 'production') {
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

    $this->addElement('Radio', 'sitemember_location_enable', array(
        'label' => 'Location Field',
        'description' => "Do you want the Location field to be enabled for Members?",
        'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitemember.location.enable', 1),
        'onclick' => 'showOtherLocationSetting(this.value)',
    ));
    
     $this->addElement('Radio', 'sitemember_info_location', array(
        'label' => 'Location Info',
        'description' => "Do you want to hide 'Location' type 'Profile Question' when member information gets display on your site.",
        'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitemember.info.location', 0),
    ));
    
    //VALUE FOR ENABLE / DISABLE Proximity Search IN Kilometer
    $this->addElement('Radio', 'sitemember_proximity_search_kilometer', array(
        'label' => 'Location & Proximity Search Metric',
        'description' => 'What metric do you want to be used for location & proximity Search Metric? (This will enable users to search for Members within a certain distance from their current location or any particular location.)',
        'multiOptions' => array(
            0 => 'Miles',
            1 => 'Kilometers'
        ),
        'value' => $coreSettings->getSetting('sitemember.proximity.search.kilometer', 0),
    ));
    
    $this->addElement('Radio', 'sitemember_location_marker', array(
        'label' => 'Location Pointer',
        'description' => "What do you want to show at the location point?",
        'MultiOptions' => array('1' => 'Marker', '0' => 'Member Photo'),
        'value' => $coreSettings->getSetting('sitemember.location.marker', 0),
        
    ));
    $this->addElement('Radio', 'sitemember_ajaxify_search_enable', array(
        'label' => 'Member Search Method',
        'description' => "Which type of member search method you want to have on your site?",
        'MultiOptions' => array('1' => 'Ajaxify Search', '0' => 'Simple Search'),
        'value' => $coreSettings->getSetting('sitemember.ajaxify.search.enable', 1),
    ));
    $this->addElement('Text', 'sitemember_recently_views_reset_days', array(
        'label' => 'Number of Days',
        'description' => "Enter the number of days after which you want to reset the recently viewed members",
        'value' => $coreSettings->getSetting('sitemember.recently.views.reset.days',7),
    ));
    $locationOption = array(
        '0' => '',
        '1' => '1',
        '2' => '2',
        '5' => '5',
        '10' => '10',
        '20' => '20',
        '50' => '50',
        '100' => '100',
        '250' => '250',
        '500' => '500',
        '750' => '750',
        '1000' => '1000',
    );

    $this->addElement('Select', 'seaocore_locationdefaultmiles', array(
        'label' => 'Default Value for Miles / Kilometers',
        'multiOptions' => $locationOption,
        'value' => $coreSettings->getSetting('seaocore.locationdefaultmiles', 0),
        'disableTranslator' => 'true'
    ));

    $this->addElement('Text', 'sitemember_map_city', array(
        'label' => 'Centre Location for Map',
        'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Map. (To show the whole world on the map, enter the word "World" below.)',
        'required' => true,
        'value' => $coreSettings->getSetting('sitemember.map.city', "World"),
    ));

    $this->addElement('Select', 'sitemember_map_zoom', array(
        'label' => "Default Zoom Level for Map at Browse Members' Locations and Member Home",
        'description' => "Select the default zoom level for the map which is shown on Member Home and Browse Members' Locations when Map View is chosen to view members. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)",
        'multiOptions' => array(
            '1' => "1",
            "2" => "2",
            "4" => "4",
            "6" => "6",
            "8" => "8",
            "10" => "10",
            "12" => "12",
            "14" => "14",
            "16" => "16"
        ),
        'value' => $coreSettings->getSetting('sitemember.map.zoom', 1),
        'disableTranslator' => 'true'
    ));



    $this->addElement('Text', 'sitemember_sponsoredcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowSponsred.tpl',
                    'class' => 'form element'
                )))
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $URL = $view->baseUrl() . "/admin/sitemember/settings/manage";
    $click = '<a href="' . $URL . '" target="_parent">click here</a>';
    $customBlocks = sprintf("Locations entered by the members from ‘Edit Profile’ pages and ‘Edit Location’ pages should be synchronized to maintain consistency in ‘Members Location & Proximity Search field’ shown in various widgets. For this, please %s, as you need to map Location related Profile Questions to various Profile Types. You must also sync member locations from ‘Member Locations’ section of this plugin.", $click);
    
    $this->addElement('Dummy', 'sitememberprofile_mapping', array(
        'label' => 'Profile Type - Location Field Mapping',
        'description' => $customBlocks,
    ));
    $this->getElement('sitememberprofile_mapping')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $this->addElement('Radio', 'sitemember_network_show', array(
        'label' => 'Browse by Networks',
        'description' => "Do you want to show members according to viewer's network if he has selected any? (If set to no, all the members will be shown.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitemember.network.show', 0),
    ));

    $browsemembers = "Browse Members" . '<a href="http://demo.socialengineaddons.com/members" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="http://demo.socialengineaddons.com/public/admin/User_Mem_1.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

    $listview = "List View" . '<a href="http://demo.socialengineaddons.com/pages/member-list" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="http://demo.socialengineaddons.com/public/admin/User_Mem_2.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

    $gridview = "Grid View" . '<a href="http://demo.socialengineaddons.com/pages/member-grid" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="http://demo.socialengineaddons.com/public/admin/User_Mem_3.png
" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

    $mapview = "Map View" . '<a href="http://demo.socialengineaddons.com/pages/member-map" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="http://demo.socialengineaddons.com/public/admin/User_Mem_5.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

    $pinboard = "Pinboard View" . '<a href="http://demo.socialengineaddons.com/pages/member-pinboard" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="http://demo.socialengineaddons.com/public/admin/User_Mem_4.png
" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';


    $this->addElement('Radio', 'sitemember_profiletemplate', array(
        'label' => 'Browse Members Page',
        'description' => 'Choose from below the template for Browse Members page of your site.',
        'multiOptions' => array(
            'default' => $browsemembers,
            'listview' => $listview,
            "gridview" => $gridview,
            "pinboardview" => $pinboard,
            "mapview" => $mapview,
        ),
        'escape' => false,
        'value' => $coreSettings->getSetting('sitemember.profiletemplate', 'default'),
    ));

    $this->addElement('Radio', 'sitemember_change_user_location', array(
           'label' => 'Update location in "Change User\'s Location" widget',
            'description' => 'Do you want to update the location of "Change User\'s Location" widget with users actual location entered during signup or from profile?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitemember.change.user.location', 0),
        ));

    $this->addElement('Radio', "sitemember_user_follow_enable", array(
        'label' => 'Allow Members to follow other members',
        'description' => 'Do you want to let members follow other memebrs ? (Note : This setting will work only if "Advanced Activity Feeds / Wall Plugin" is installed on your system and two way friendship is enabled.)',
        'multiOptions' => array(
            1 => 'Yes, allow members to follow other members.',
            0 => 'No, do not allow members to follow other members.',
        ),
        'value' => $coreSettings->getSetting('sitemember.user.follow.enable', 0),
    ));        

    $this->addElement('Button', 'save', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}