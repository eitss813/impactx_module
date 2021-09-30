<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      'submit_lsetting', 'environment_mode', 'include_in_package'
  );
    
  public function init() {
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitepagevideo_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.lsettings'),
    ));

    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $this->addElement('Checkbox', 'include_in_package', array(
          'label' => ' Enable videos module for the default package that was created upon installation of the "Directory / Pages Plugin". If enabled, Videos App will also be enabled for the Pages created so far under the default package.',
          'description' => 'Enable Videos Module for Default Package ',
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

    $this->addElement('Radio', 'sitepagevideo_video_show_menu', array(
        'label' => 'Videos Link',
        'description' => 'Do you want to show the Videos link on Pages Navigation Menu? (You might want to show this if Videos from Pages are an important component on your website. This link will lead to a widgetized page listing all Page Videos, with a search form for Page Videos and multiple widgets.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.video.show.menu', 1),
    ));

    $this->addElement('Text', 'sitepagevideo_ffmpeg_path', array(
        'label' => 'Path to FFMPEG',
        'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.ffmpeg.path', ''),
    ));

    $this->addElement('Checkbox', 'sitepagevideo_html5', array(
      'description' => 'HTML5 Video Support',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.html5', false),
    ));
    
    $description = 'While posting videos on your site, users can choose YouTube as a source. This requires a valid YouTube API key.<br>To learn how to create that key with correct permissions, read our <a href="http://support.socialengine.com/php/customer/portal/articles/2018371-create-your-youtube-api-key" target="_blank">KB Article</a>';

    $currentYouTubeApiKey = '******';
    if( !_ENGINE_ADMIN_NEUTER ) {
      $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    }
    $this->addElement('Text', 'video_youtube_apikey', array(
      'label' => 'YouTube API Key',
      'description' => $description,
      'filters' => array(
        'StringTrim',
      ),
      'value' => $currentYouTubeApiKey,
    ));
    $this->video_youtube_apikey->getDecorator('Description')->setOption('escape', false);
        
    $this->addElement('Text', 'sitepagevideo_jobs', array(
        'label' => 'Encoding Jobs',
        'description' => 'How many jobs do you want to allow to run at the same time?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.jobs', 2),
    ));

    $this->addElement('Radio', 'sitepagevideo_embeds', array(
        'label' => 'Allow Embedding of Videos?',
        'description' => 'Enabling this option will give members the ability to embed videos on this site in other pages using an iframe (like YouTube).',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.embeds', 1),
        'multiOptions' => array(
            '1' => 'Yes, allow embedding of videos.',
            '0' => 'No, do not allow embedding of videos.',
        ),
    ));

    // Order of page video page
    $this->addElement('Radio', 'sitepagevideo_order', array(
        'label' => 'Default Ordering in Page Videos listing',
        'description' => 'Select the default ordering of videos in Page Videos listing. (This widgetized page will list all Page Videos. Sponsored videos are videos created by paid Pages.)',
        'multiOptions' => array(
            1 => 'All videos in descending order of creation.',
            2 => 'All videos in alphabetical order.',
            3 => 'Featured videos followed by others in descending order of creation.',
            4 => 'Sponsored videos followed by others in descending order of creation.(If you have enabled packages.)',
            5 => 'Featured videos followed by sponsored videos followed by others in descending order of creation.(If you have enabled packages.)',
            6 => 'Sponsored videos followed by featured videos followed by others in descending order of creation.(If you have enabled packages.)',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.order', 1),
    ));

     $this->addElement('Radio', 'sitepagevideo_featured', array(
        'label' => 'Making Page Videos Highlighted',
        'description' => 'Allow Page Admins to make videos in their Pages as highlighted.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.featured', 1),
    ));

    $this->addElement('Text', 'sitepagevideo_truncation_limit', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'maxlength' => 3,
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.truncation.limit', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

			$this->addElement('Text', 'sitepagevideo_manifestUrl', array(
        'label' => 'Page Videos URL alternate text for "page-videos"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "pagevideos" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.manifestUrl', "page-videos"),
    ));


    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>