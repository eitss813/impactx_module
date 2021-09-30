<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode", "include_in_package"
  );
    
  public function init() {

    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitepagealbum_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.lsettings'),
    ));

    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $this->addElement('Checkbox', 'include_in_package', array(
          'label' => 'Enable albums module for the default package that was created upon installation of the "Directory / Pages Plugin". If enabled, Albums App will also be enabled for the Pages created so far under the default package.',
          'description' => 'Enable Albums Module for Default Package',
          'value' => 1,
      ));
    }

    if ((!empty($generalConfig['environment_mode']) ) && ($generalConfig['environment_mode'] != 'development')) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching pralbums CSS of new plugins to be rendered immediately after installation.)',
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
    
    $this->addElement('Text', 'sitepagealbum_manifestUrl', array(
			'label' => 'Page Albums URL alternate text for "page-albums"',
			'allowEmpty' => false,
			'required' => true,
			'description' => 'Please enter the text below which you want to display in place of "pagealbums" in the URLs of this plugin.',
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.manifestUrl', "page-albums"),
    ));
    
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $URL = $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'lightbox'), 'admin_default', true);
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_('The settings for the Advanced Lightbox Viewer have been moved to the SocialEngineAddOns Core Plugin. Please %1svisit here%2s to see and configure these settings.'),
    "<a href='" . $URL ."' target='_blank'>", "</a>");
    $this->addElement('Dummy', 'sitepagealbum_photolightbox_show', array(
        'label' => 'Photos Lightbox Viewer',
        'description' => $description,
    ));

    $this->getElement('sitepagealbum_photolightbox_show')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    $this->addElement('Radio', 'sitepagealbum_album_show_menu', array(
        'label' => 'Albums Link',
        'description' => 'Do you want to show the Albums link on Pages Navigation Menu? (You might want to show this if Albums from Pages are an important component on your website. This link will lead to a widgetized page listing all Page Albums, with a search form for Page Albums and multiple widgets.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.album.show.menu', 1),
    ));

     // Order of page album page
    $this->addElement('Radio', 'sitepagealbum_order', array(
        'label' => 'Default Ordering in Page Albums listing',
        'description' => 'Select the default ordering of albums in Page Albums listing. (This widgetized page will list all Page Albums. Sponsored albums are albums created by paid Pages.)',
        'multiOptions' => array(
            1 => 'All albums in descending order of creation.',
            2 => 'All albums in alphabetical order.',
            3 => 'Featured albums followed by others in descending order of creation.',
            4 => 'Sponsored albums followed by others in descending order of creation.(If you have enabled packages.)',
            5 => 'Featured albums followed by sponsored albums followed by others in descending order of creation.(If you have enabled packages.)',
            6 => 'Sponsored albums followed by featured albums followed by others in descending order of creation.(If you have enabled packages.)',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.order', 1),
    ));

    $this->addElement('Radio', 'sitepagealbum_hide_autogenerated', array(
        'label' => 'Show / Hide Default Albums',
        'description' => "Do you want to show default albums of pages and their various extensions on your site which are created by the system automatically? (If you select No, then albums like 'Overview Photos', 'Note Photos', 'Discussion Photos', etc will not be displayed anywhere on the site.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1),
    ));

    $this->addElement('Radio', 'sitepagealbum_albumsorder', array(
        'label' => 'Choose Order',
        'description' => "Select the order below to display the albums on your site.",
        'multiOptions' => array(
            1 => 'Newer to older',
            0 => 'Older to newer'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1),
    ));

    $this->addElement('Text', 'sitepagealbum_truncation_limit', array(
        'label' => 'Title Truncation Limit',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'What maximum limit should be applied to the number of characters in the title of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.truncation.limit', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>
