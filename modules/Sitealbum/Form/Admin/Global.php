<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );

  public function init() {

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitealbum_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $coreSettings->getSetting('sitealbum.lsettings'),
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

    // Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    $this->addElement('Text', 'normal_photo_height', array(
        'label' => 'Normal Photo Height (in pixels)',
        'description' => "Note: Changes will be reflected only for the newly uploaded photos.",
        'value' => $coreSettings->getSetting('normal.photo.height', 375),
    ));
    $this->normal_photo_height->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'normal_photo_width', array(
        'label' => 'Normal Photo Width (in pixels)',
        'description' => "Note: Changes will be reflected only for the newly uploaded photos.",
        'value' => $coreSettings->getSetting('normal.photo.width', 375),
    ));
    $this->normal_photo_width->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'normallarge_photo_height', array(
        'label' => 'Normal Large Photo Height (in pixels)',
        'description' => "Note: Changes will be reflected only for the newly uploaded photos.",
        'value' => $coreSettings->getSetting('normallarge.photo.height', 720),
    ));
    $this->normal_photo_height->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'normallarge_photo_width', array(
        'label' => 'Normal Large Photo Width (in pixels)',
        'description' => "Note: Changes will be reflected only for the newly uploaded photos.",
        'value' => $coreSettings->getSetting('normallarge.photo.width', 720),
    ));
    $this->normal_photo_width->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'main_photo_height', array(
        'label' => 'Main Photo Height (in pixels)',
        'description' => "Note: Changes will be reflected only for the newly uploaded photos.",
        'value' => $coreSettings->getSetting('main.photo.height', 1600),
    ));
    $this->main_photo_height->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'main_photo_width', array(
        'label' => 'Main Photo Width (in pixels)',
        'description' => "Note: Changes will be reflected only for the newly uploaded photos.",
        'value' => $coreSettings->getSetting('main.photo.width', 1600),
    ));
    $this->main_photo_width->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Radio', 'sitealbum_photo_badge', array(
        'label' => 'Photos Badge',
        'description' => 'Do you want users to be able to create their Photo Badges? Photo badges will enable users to show off their photos on external blogs or websites. Multiple configuration options will enable them to create attractive badges.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitealbum.photo.badge', 1),
    ));

    $this->addElement('Radio', 'sitealbum_photo_specialalbum', array(
        'label' => 'Show / Hide Default Albums',
        'description' => "Do you want to show default albums of members on your site which are created by the system automatically ? [Note: If you click on 'No' then the albums like 'Profile Photos', 'Wall Photos', 'Blog Photos' and 'Message Photos' etc will not be shown to members on the site.]",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        //    'onclick' => 'showwarning(this.value);',
        'value' => $coreSettings->getSetting('sitealbum.photo.specialalbum', 0),
    ));

    $this->addElement('Radio', 'sitealbum_lightbox_onloadshowthumb', array(
        'label' => 'Opening Loader Image in Lightbox',
        'description' => 'Do you want a loader image to be shown in the lightbox viewer while the complete photo is being loaded? (If you select "No" over here, then users will see the photo while it is being loaded. Note: If the clicked photo is private then also the photo will be partially shown to un-authorized users if you select "No".)',
        'multiOptions' => array(
            0 => 'Yes',
            1 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitealbum.lightbox.onloadshowthumb', 1),
    ));

    $this->addElement('Radio', 'sitealbum_rating', array(
        'label' => 'Allow Ratings',
        'description' => "Do you want to allow ratings for Albums and Photos?",
        'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitealbum.rating', 1),
        'onclick' => 'showUpdateratingSetting(this.value)'
    ));

    $this->addElement('Radio', 'sitealbumrating_update', array(
        'label' => 'Allow Updating of Rating?',
        'description' => "Do you want to let members to update their rating for Albums / Photos?",
        'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitealbumrating.update', 1),
    ));

    $this->addElement('Radio', 'sitealbum_category_enabled', array(
        'label' => 'Allow Category',
        'description' => 'Do you want the Category field to be enabled for Albums?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitealbum.category.enabled', 1)
    ));

    $this->addElement('Radio', 'sitealbum_tags_enabled', array(
        'label' => 'Allow Tags',
        'description' => 'Do you want the Tags field to be enabled for Albums?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitealbum.tags.enabled', 0)
    ));

    $this->addElement('Radio', 'sitealbum_makeprofile_photo', array(
        'label' => 'Make Profile Photo Option',
        'description' => 'Do you want Make Profile Photo option to be visible other than the album owner?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitealbum.makeprofile.photo', 1)
    ));
    
    $this->addElement('Radio', 'sitealbum_redirection', array(
        'label' => 'Redirection of Albums link',
        'description' => 'Please select the redirection page for Albums, when user click on "Albums" link at Main Navigation Menu.',
        "multiOptions" => array(
            'index' => 'Albums Home Page',
            'browse' => 'Albums Browse Page'
        ),
        'value' => $coreSettings->getSetting('sitealbum.redirection', 'index'),
    ));      
    
    $this->addElement('Text', 'sitealbumshow_navigation_tabs', array(
        'label' => 'Tabs in Albums navigation bar',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'How many tabs do you want to show on Albums main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Layout" > "Menu Editor")',
        'value' => $coreSettings->getSetting('sitealbumshow.navigation.tabs', 7),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Radio', 'sitealbum_location', array(
        'label' => 'Location Field',
        'description' => "Do you want the Location field to be enabled for Albums and Photos?",
        'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => $coreSettings->getSetting('sitealbum.location', 1),
        'onclick' => 'showProximitySearchSetting(this.value)',
    ));

    //VALUE FOR ENABLE /DISABLE PROXIMITY SEARCH IN Kilometer
    $this->addElement('Radio', 'sitealbum_proximity_search_kilometer', array(
        'label' => 'Location & Proximity Search Metric',
        'description' => 'What metric do you want to be used for location & proximity Search Metric? (This will enable users to search for Albums within a certain distance from their current location or any particular location.)',
        'multiOptions' => array(
            0 => 'Miles',
            1 => 'Kilometers'
        ),
        'value' => $coreSettings->getSetting('sitealbum.proximity.search.kilometer', 0),
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_('The settings for the Advanced Lightbox Viewer have been moved to the SocialEngineAddOns Core Plugin. Please %1svisit here%2s to see and configure these settings.'), "<a href='" . $view->baseUrl() . "/admin/seaocore/settings/lightbox" . "' target='_blank'>", "</a>");
    $this->addElement('Dummy', 'sitealbum_photolightbox_show', array(
        'label' => 'Photos Lightbox Viewer',
        'description' => $description,
    ));

    $this->getElement('sitealbum_photolightbox_show')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    
    $this->addElement('Radio', 'sitealbum_open_lightbox_upload', array(
        'label' => "Open Lightbox for 'Add New Photos'",
        'description' => "Do you want to open lightbox when member click on 'Add New Photos' button / link?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitealbum.open.lightbox.upload', 1),
    ));
    
    //NETWORK BASE ALBUM
    $this->addElement('Radio', 'sitealbum_network', array(
        'label' => 'Browse by Networks',
        'description' => "Do you want to show albums according to viewer's network if he has selected any? (If set to no, all the albums will be shown.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showDefaultNetwork(this.value)',
        'value' => $coreSettings->getSetting('sitealbum.network', 0),
    ));

    //VALUE FOR Page Dispute Link.
    $this->addElement('Radio', 'sitealbum_default_show', array(
        'label' => 'Set Only My Networks as Default in search',
        'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the albums browse and album home, and enables users to search and filter albums.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showDefaultNetworkType(this.value)',
        'value' => $coreSettings->getSetting('sitealbum.default.show', 0),
    ));
    $this->addElement('Radio', 'album_searchable', array(
      'label' => 'Make default albums searchable?',
      'description' => 'Do you want to make a default album searchable ? (If set to no,'
        . ' albums that get created by default like Blog Photos, Forum Photos, etc will not'
        . ' be displayed in the album search results and widgets, but will still be displayed in'
        . ' the Profile Albums tab on a users profile page.)',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('album.searchable', 0),
    ));
    $this->addElement('Radio', 'sitealbum_networks_type', array(
        'label' => 'Network selection for Albums',
        'description' => "You have chosen that viewers should only see Albums of their network(s). How should a Album's network(s) be decided?",
        'multiOptions' => array(
            0 => "Album Owner's network(s) [If selected, only members belonging to album owner's network(s) will see the Albums.]",
            1 => "Selected Networks [If selected, album owner will be able to choose the networks of which members will be able to see their Album.]"
        ),
        'value' => $coreSettings->getSetting('sitealbum.networks.type', 0),
    ));

    $this->addElement('Radio', 'sitealbum_networkprofile_privacy', array(
        'label' => 'Display Profile Album only to Network Users',
        'description' => "Do you want to show the Album Profile page only to users of the same network. (If set to yes and \"Browse By Networks\" is enabled then users would not be able to view the profile page of those albums which does not belong to their networks.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        // 'onclick' => 'showviewablewarning(this.value);',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.networkprofile.privacy', 0),
    ));
    $this->addElement('Radio', 'sitealbum_privacybase', array(
        'label' => 'Display of All Albums in widgets',
        'description' => "Do you want to show all the albums to the user in the widgets and browse albums of this plugin irrespective of privacy? [Note: If you select 'No', then only those albums will be shown in the widgets and browse albums which are viewable to the current logged-in user. But this may slightly affect the loading speed of your website. To avoid such loading delay to the best possible extent, we are also using caching based display.)",
        'multiOptions' => array(
            0 => 'Yes',
            1 => 'No'
        ),
        // 'onclick' => 'showviewablewarning(this.value);',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.privacybase', 0),
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}