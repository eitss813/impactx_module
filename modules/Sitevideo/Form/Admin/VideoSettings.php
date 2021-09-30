<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoSettings.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_VideoSettings extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
                ->setTitle('Video Settings')
                ->setDescription('These settings affect all members in your community.');

        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        $compareVersion = Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.9.0');
        $videoOptions = array(
          4 => 'My Computer',
          1 => 'YouTube',
          2 => 'Vimeo',
          3 => 'Dailymotion',
          5 => 'Embed Code'
        );
        if( $compareVersion > 0 ) {
          $videoOptions[6] = 'External Sites';
        }
        if (Engine_Api::_()->sitevideo()->enableStreamVideo()) {
          $videoOptions[7] = 'Upload For Stream';
        }

    $this->addElement('MultiCheckbox', 'sitevideo_allowed_video', array(
            'label' => 'Allowed Video Sources',
            'description' => "Select type of video source that you want to be available for members while uploading new video. [ Note: You can apply this setting on per member level basis from ‘Member Level Settings’. ]",
            'multiOptions' => $videoOptions,
            'value' => $coreSettings->getSetting('sitevideo.allowed.video', array(0, 1, 2, 3,4,5,6))
        ));
        $this->addElement('Radio', 'sitevideo_video_category_enabled', array(
            'label' => 'Allow Category',
            'description' => 'Do you want the Category field to be enabled for Videos?',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.video.category.enabled', 1)
        ));

        $this->addElement('Radio', 'sitevideo_video_tags_enabled', array(
            'label' => 'Allow Tags',
            'description' => 'Do you want the Tags field to be enabled for Videos?',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.video.tags.enabled', 1)
        ));
        
        $this->addElement('Radio', 'sitevideo_video_photo_field_enabled', array(
            'label' => 'Video Artwork',
            'description' => 'Do you want the Video Artwork field to be enabled for Videos?[Note: In that case user can be able to uplaod and edit the Video Artwork]',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.video.photo.field.enabled', 1)
        ));

        $this->addElement('Radio', 'sitevideo_watchlater_allow', array(
            'label' => 'Watch Later
',
            'description' => "Do you want to enable the Watch Later feature on your website?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.watchlater.allow', 1),
        ));
        $this->addElement('Radio', 'sitevideo_description_tinymceditor', array(
            'label' => 'TinyMC Editor for Description',
            'description' => "Do you want to enable the TinyMC Editor for Video Description on Edit Video page?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.description.tinymceditor', 0),
        ));
        $this->addElement('Radio', 'sitevideo_video_location', array(
            'label' => 'Location Field',
            'description' => "Do you want the Location field to be enabled for Videos?",
            'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.video.location', 0),
            'onclick' => 'showProximitySearchSetting(this.value)',
        ));

        //VALUE FOR ENABLE /DISABLE PROXIMITY SEARCH IN Kilometer
        $this->addElement('Radio', 'sitevideo_video_proximity_search_kilometer', array(
            'label' => 'Location & Proximity Search Metric',
            'description' => 'What metric do you want to be used for location & proximity Search Metric? (This will enable users to search for Videos within a certain distance from their current location or any particular location.)',
            'multiOptions' => array(
                0 => 'Miles',
                1 => 'Kilometers'
            ),
            'value' => $coreSettings->getSetting('sitevideo.video.proximity.search.kilometer', 0),
        ));

        $this->addElement('Text', 'sitevideo_ffmpeg_path', array(
            'label' => 'Path to FFMPEG',
            'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
            'value' => $coreSettings->getSetting('sitevideo.ffmpeg.path', ''),
        ));

        $this->addElement('Checkbox', 'sitevideo_html5', array(
            'description' => 'HTML5 Video Support',
            'value' => $coreSettings->getSetting('sitevideo.html5', true),
        ));

        if( Engine_Api::_()->sitevideo()->enableStreamVideo() ) {
          $this->addElement('Radio', 'sitevideo_video_convert_stream', array(
            'label' => 'Convert into Stream',
            'description' => 'Do you want to convert uplaoded video into stream',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.video.convert.stream', true),
          ));
        }

    $description = 'While posting videos on your site, users can choose YouTube as a source. This requires a valid YouTube API key.<br>To learn how to create that key with correct permissions, read our <a href="http://support.socialengine.com/php/customer/portal/articles/2018371-create-your-youtube-api-key" target="_blank">KB Article</a>';

        $currentYouTubeApiKey = '******';
        if (!_ENGINE_ADMIN_NEUTER) {
            $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
        }
        $this->addElement('Text', 'sitevideo_youtube_apikey', array(
            'label' => 'YouTube API Key',
            'description' => $description,
            'filters' => array(
                'StringTrim',
            ),
            'value' => $currentYouTubeApiKey,
        ));
        $this->sitevideo_youtube_apikey->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'sitevideo_jobs', array(
            'label' => 'Encoding Jobs',
            'description' => 'How many jobs do you want to allow to run at the same time?',
            'value' => $coreSettings->getSetting('sitevideo.jobs', $coreSettings->getSetting('video.jobs', 2)),
        ));

        $this->addElement('Text', 'sitevideo_lightbox_player_width', array(
            'label' => 'Video Player Width',
            'description' => 'Enter  the width (in pixel) of Video Player in lightbox',
            'value' => $coreSettings->getSetting('sitevideo.lightbox.player.width', 0),
        ));

        $this->addElement('Text', 'sitevideo_lightbox_player_height', array(
            'label' => 'Video Player Height',
            'description' => ' Enter the height (in pixel) of Video Player in lighbox',
            'value' => $coreSettings->getSetting('sitevideo.lightbox.player.height', 440),
        ));

        //COLOR VALUE FOR BACKGROUND COLOR
        $this->addElement('Text', 'sitevideo_lightbox_bgcolor', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => 'admin-settings/rainbow-color/_formImagerainbowLightBoxBg.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'sitevideo_video_sponsoredcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowVideoSponsred.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Radio', 'sitevideo_gotovideo', array(
            'label' => '"Go to Video" Button',
            'description' => 'Do you want the button of "Go to Video" to be shown in the Video Lightbox viewer?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.gotovideo', 1),
        ));

        $this->addElement('Radio', 'sitevideo_open_lightbox_upload', array(
            'label' => "Open Lightbox for 'Post New Video'",
            'description' => "Do you want to open lightbox when member click on 'Post New Video' button / link?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.open.lightbox.upload', 1),
        ));

        $this->addElement('Radio', 'sitevideo_embeds', array(
            'label' => 'Allow Embedding of Videos?',
            'description' => 'Enabling this option will give members the ability to embed videos from your site on other pages / sites using an iframe code (like YouTube).',
            'value' => $coreSettings->getSetting('sitevideo.embeds', 1),
            'multiOptions' => array(
                '1' => 'Yes, allow embedding of videos.',
                '0' => 'No, do not allow embedding of videos.',
            ),
        ));

        $this->addElement('Radio', 'sitevideo_video_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show videos according to viewer's network if he has selected any? (If set to no, all the videos will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $coreSettings->getSetting('sitevideo.video.network', 0),
        ));

        $this->addElement('Radio', 'sitevideo_video_default_show', array(
            'label' => 'Set Only My Networks as Default in Search',
            'description' => ' Do you want to set "Only My Network" option as default for Show field in the search form widget? (This widget appears on the Browse Videos to enable users to search and filter videos.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetworkType(this.value)',
            'value' => $coreSettings->getSetting('sitevideo.video.default.show', 0),
        ));

        $this->addElement('Radio', 'sitevideo_video_networks_type', array(
            'label' => 'Network selection for Videos',
            'description' => "You have chosen that viewers should only see Videos of their network(s). How should a Video's network(s) be decided?",
            'multiOptions' => array(
                0 => "Video Owner's network(s) [If selected, only members belonging to video owner's network(s) will see the Videos.]",
                1 => "Selected Networks [If selected, video owner will be able to choose the networks of which members will be able to see their Video.]"
            ),
            'value' => $coreSettings->getSetting('sitevideo.video.networks.type', 0),
        ));

        $this->addElement('Radio', 'sitevideo_video_networkprofile_privacy', array(
            'label' => 'Display Profile Video only to Network Users',
            'description' => "Do you want to show the Video Profile page only to users of the same network. (If set to yes and \"Browse By Networks\" is enabled then users would not be able to view the profile page of those videos which does not belong to their networks.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.networkprofile.privacy', 0),
        ));

        $this->addElement('Radio', 'sitevideo_video_privacybase', array(
            'label' => 'Displays All Videos in Widgets',
            'description' => "Do you want to show all the videos to the users in the Widgets and Browse Videos of this plugin irrespective of privacy? [Note: If you select 'No', then only those videos will be shown in the Widgets and Browse Videos which are viewable to the current logged-in users. But this may slightly affect the loading speed of your website. To avoid such loading delay to the best possible extent, we are also using caching based display.)",
            'multiOptions' => array(
                0 => 'Yes',
                1 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.privacybase', 0),
        ));

        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
