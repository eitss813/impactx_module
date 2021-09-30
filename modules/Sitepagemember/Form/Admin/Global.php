<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      'sitepagemember_lsettings', 'environment_mode', 'submit_lsetting', 'include_in_package', 'language_phrases_pages', 'language_phrases_page', 'sitepagemember_group_settings', 'sitepagemember_settings', 'sitepagemember_settingsforlayout', 'sitepagemember_enabled_group_layout'
  );
    
  public function init() {
    $this->setTitle('Global Settings')
        ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitepagemember_lsettings', array(
			'label' => 'Enter License key',
			'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.lsettings'),
    ));

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }

//     	$this->addElement('Checkbox', 'sitepagemember_group_settings', array(
// 				'description' => 'Use as Groups (Setting to make this system work as Groups)',
// 				'label' => 'Do you want use this system as SocialEngineAddOns Demo Groups? (Note: If you select this option, then you will be able to configure additional settings to use this system as Groups on your site. This is a one time setting and to revert this later, you will have to manually configure from Layout Editor, Global Settings, Manage Packages, etc.)',
// 				'value' => 0,
// 				'onclick' => 'showGroupSettings(this.value)'
// 		));
//     
//     $LayoutSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
//     
//     $Group_setting_array = array("packagedisable" => "Enable Packages and disable Member Level Settings.", "reportinsight" => "Enable Insights and Reports.");
//     $this->addElement( 'MultiCheckbox' , 'sitepagemember_settings' , array (
// 			'multiOptions' => $Group_setting_array,
// 		)) ;
// 
// 		
// 		
// 		
// 		$Group_settingforLayout = array("layoutsetasdemo" => "Retain the layout of Pages Home (Now Groups Home) like what it is now and do not make it like SocialEngineAddOns Demo Groups Home.", 'layoutbrowsepage' => "Retain the layout of Browse Pages (Now Browse Groups) like what it is now and do not make it like SocialEngineAddOns Demo Browse Groups.", 'layoutpinboard' => 'Retain the layout of Pinboard (Now Pinboard of Groups) like what it is now and do not make it like SocialEngineAddOns Demo Pinboard.', 'layoutprofilepage' => 'Retain the layout of Page Profile (Now Group Profile) like what it is now and do not make it like SocialEngineAddOns Demo Group Profile.');
// 
// 		$this->addElement( 'MultiCheckbox' , 'sitepagemember_settingsforlayout' , array (
// 		   'label' => 'Content Layout',
// 			'multiOptions' => $Group_settingforLayout,
// 		)) ;

		
    $this->addElement('Text', 'language_phrases_page', array(
        'label' => 'Singular Page Title',
        'description' => 'Please enter the Singular Title for page. This text will come in places like feeds generated, widgets etc.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.page", "page"),

    ));
    
    $this->addElement('Text', 'language_phrases_pages', array(
        'label' => 'Plural Page Title',
        'description' => 'Please enter the Plural Title for pages. This text will come in places like Main Navigation Menu, Page Navigation Menu, widgets etc.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        ),
      'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.pages", "pages"),
    ));

/*    
		$this->addElement( 'Radio' , 'sitepagemember_enabled_group_layout' , array (
			'label' => 'Group Cover Photo Layout',
			'description' => "Do you want to enable Group Cover Photo Layout for the profiles of directory items / pages on your site?",
			'multiOptions' => array (
				1 => 'Yes' ,
				0 => 'No'
			) ,
			'value' => $coreSettings->getSetting('sitepagemember.enabled.group.layout', 1),
		));*/
    
		if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
			$this->addElement('Checkbox', 'include_in_package', array(
				'label' => ' Enable members module for the default package that was created upon installation of the "Directory / Pages Plugin". If enabled, Members App will also be enabled for the Pages created so far under the default package.',
				'description' => 'Enable Members Module for Default Package',
				'value' => 1,
			));
		}
                
    if ( ( !empty($generalConfig['environment_mode']) ) && ($generalConfig['environment_mode'] != 'development') ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
//          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));
    
    $this->addElement('Text', 'sitepagemember_manifestUrl', array(
			'label' => 'Page Members URL alternate text for "page-members"',
			'allowEmpty' => false,
			'required' => true,
			'description' => 'Please enter the text below which you want to display in place of "pagemembers" in the URLs of this plugin.',
			'value' => $coreSettings->getSetting('sitepagemember.manifestUrl', "page-members"),
    ));
    
		$this->addElement( 'Radio' , 'pagemember_title' , array (
      'label' => 'Member Roles',
      'description' => "Do you want page members to be able to select their member roles in the directory items / pages?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.title' , 1),
    )) ;
    
    $this->addElement( 'Radio' , 'pagemember_member_title' , array (
      'label' => 'Enable Member Title',
      'description' => "Do you want page admins to be able to enter member titles by which members will be called in their directory items / pages?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.member.title' , 1),
    )) ;
    
    $this->addElement('Radio', 'sitepagemember_member_show_menu', array(
			'label' => 'Members Link',
			'description' => 'Do you want to show the Members link on Pages Navigation Menu? (You might want to show this if Members from Pages are an important component on your website. This link will lead to a widgetized page listing all Page Members, with a search form for Page Members and multiple widgets.',
			'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
			),
			'value' => $coreSettings->getSetting('sitepagemember.member.show.menu', 1),
    ));

    $this->addElement( 'Radio' , 'pagemember_date' , array (
      'label' => 'Affiliation / Joining Date',
      'description' => "Do you want page members to be able to select their affiliation / joining date in the directory items / pages?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.date' , 1),
    )) ;
    
    $this->addElement( 'Radio' , 'pagemember_announcement' , array (
      'label' => 'Announcements',
      'description' => 'Do you want announcements to be enabled for directory items / pages? (If enabled, then page admins will be able to post announcements for their pages from ‘Manage Announcements’ section of their Page Dashboard.)',
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.announcement' , 1),
			'onclick' => 'showAnnouncements(this.value)'
    )) ;
    
    $this->addElement('Radio', 'sitepagemember_tinymceditor', array(
        'label' => 'Tinymce Editor',
        'description' => 'Do you want to allow tinymce editor for the announcements.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitepagemember.tinymceditor', 1),
    ));

    $this->addElement( 'Radio' , 'pagemember_automatically_addmember' , array (
      'label' => 'Automatically Add People',
      'description' => "Do you want people to be automatically added to a page when page admins or other members of that page add them? (Note: This setting will not work for the pages which need admin approval when people try to join that page.)",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.automatically.addmember' , 1),
    )) ;

    $this->addElement( 'Radio' , 'pagemember_automatically_like' , array (
      'label' => 'Automatic Like',
      'description' => "Do you want members to automatically Like a page they Join?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.automatically.like' , 0),
    )) ;
    
    $this->addElement( 'Radio' , 'pagemember_automatically_join' , array (
      'label' => 'Automatic Join',
      'description' => "Do you want members to automatically Join a page they Like?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.automatically.join' , 0),
    )) ;

    $this->addElement( 'Radio' , 'pagemember_invite_option' , array (
      'label' => 'Enable “Member Invite Others” Option',
      'description' => "Do you want to enable “Member Invite Others” option in the pages using which page admins will be able to choose who should be able to invite other people to their pages? (If you select ‘No’, then you can choose who would be able to invite other people to the pages on your site.)",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.invite.option' , 1),
      'onclick' => 'showInviteOption(this.value)',
    ));

		$this->addElement('Radio', 'pagemember_invite_automatically', array(
			'label' => 'Member Invite Others',
			'description' => 'Do you want page members to invite other people to the pages they join?',
			'multiOptions' => array(
			'0' => 'Yes, members can invite other people.',
			'1' => 'No, only page admins can invite other people',
			),
			'value' => $coreSettings->getSetting( 'pagemember.invite.automatically' , 1),
		));
    
    $this->addElement( 'Radio' , 'pagemember_member_approval_option' , array (
      'label' => 'Enable “Approve Members” Option',
      'description' => "Do you want to enable “Approve Members” option in the pages using which page admins will be able to choose that when people try to join pages, should they be allowed to join immediately, or should they be forced to wait for approval? (If you select ‘No’, then you can choose to allow members to join immediately or wait for approval.)",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'pagemember.member.approval.option' , 1),
      'onclick' => 'showApprovalOption(this.value)'
    ));
    
    $this->addElement('Radio', 'pagemember_member_approval_automatically', array(
			'label' => 'Approve Members',
			'description' => 'When people try to join the pages on your site, should they be allowed to join immediately, or should they be forced to wait for approval?',
			'multiOptions' => array(
				'1' => 'New members can join immediately.',
				'0' => 'New members must be approved.',
			),
			'value' => $coreSettings->getSetting( 'pagemember.member.approval.automatically' , 1),
		));

		$this->addElement( 'Radio' , 'pagemember_pageasgroup' , array (
			'label' => 'Enable "Invite All Page members" Option',
			'description' => "Do you want to let page event owners invite all page members to their page events? (If enabled, then ‘Invite All Page members’ option will be available while creating a page event.)",
			'multiOptions' => array (
				1 => 'Yes' ,
				0 => 'No'
			) ,
			'value' => $coreSettings->getSetting( 'pagemember.pageasgroup' , 0),
		));

    $this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
    ));
  }
}