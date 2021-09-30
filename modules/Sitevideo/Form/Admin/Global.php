<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );

    public function init() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
                ->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'sitevideo_lsettings', array(
            'label' => 'Enter License key For Advanced Videos / Channels / Playlists Plugin',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $coreSettings->getSetting('sitevideo.lsettings'),
        ));

        if (Engine_Api::_()->hasModuleBootstrap('sitevideointegration')) {
            $this->addElement('Text', 'sitevideointegration_lsettings', array(
                'label' => 'Enter License key For Advanced Videos Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('sitevideointegration.lsettings'),
            ));
        }

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
            'onclick' => 'showlightbox();',
            'ignore' => true
        ));

        $this->addElement('Radio', 'sitevideo_channel_allow', array(
            'label' => 'Channels',
            'description' => "Do you want to enable the Channels feature on your website?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.channel.allow', 1),
        ));
        $this->addElement('Radio', 'sitevideo_playlist_allow', array(
            'label' => 'Playlists',
            'description' => "Do you want to enable Playlists feature on your website?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.playlist.allow', 1),
        ));
        $this->addElement('Radio', 'sitevideo_automatic_videouploadallow', array(
            'label' => 'Automatic Upload Youtube Videos',
            'description' => "Do you want to enable automatic upload of youtube videos on your website?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'selectAutoUpload(this.value)',
            'value' => $coreSettings->getSetting('sitevideo.automatic.videouploadallow', 0),
        ));
        $this->addElement('Text', 'sitevideo_automatic_uploadvideoscount', array(
            'label' => 'Count of Automatic Videos',
            'description' => "Please enter the count of youtube videos which you want to be uploaded automatically.",
            'value' => $coreSettings->getSetting('sitevideo.automatic.uploadvideoscount', 20),
        ));
        $this->addElement('Text', 'sitevideo_automatic_uploadvideoindays', array(
            'label' => 'Time-interval for Automatic Videos Upload',
            'description' => "Please enter the time-interval (in days) to upload youtube videos automatically.",
            'value' => $coreSettings->getSetting('sitevideo.automatic.uploadvideoindays', 3),
        ));
        $this->addElement('Text', 'normal_video_height', array(
            'label' => 'Small Video Thumbnail Height (in pixels)',
            'description' => "Note: Changes will be reflected only for the newly uploaded videos.",
            'value' => $coreSettings->getSetting('normal.video.height', 375),
        ));
        $this->normal_video_height->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Text', 'normal_video_width', array(
            'label' => 'Small Video Thumbnail Width (in pixels)',
            'description' => "Note: Changes will be reflected only for the newly uploaded videos.",
            'value' => $coreSettings->getSetting('normal.video.width', 375),
        ));
        $this->normal_video_width->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Text', 'normallarge_video_height', array(
            'label' => 'Medium Video Thumbnail Height (in pixels)',
            'description' => "Note: Changes will be reflected only for the newly uploaded videos.",
            'value' => $coreSettings->getSetting('normallarge.video.height', 720),
        ));
        $this->normallarge_video_height->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Text', 'normallarge_video_width', array(
            'label' => 'Medium Video Thumbnail Width (in pixels)',
            'description' => "Note: Changes will be reflected only for the newly uploaded videos.",
            'value' => $coreSettings->getSetting('normallarge.video.width', 720),
        ));
        $this->normallarge_video_width->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Text', 'main_video_height', array(
            'label' => 'Large Video Thumbnail Height (in pixels)',
            'description' => "Note: Changes will be reflected only for the newly uploaded videos.",
            'value' => $coreSettings->getSetting('main.video.height', 1600),
        ));
        $this->main_video_height->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Text', 'main_video_width', array(
            'label' => 'Large Video Thumbnail Width (in pixels)',
            'description' => "Note: Changes will be reflected only for the newly uploaded videos.",
            'value' => $coreSettings->getSetting('main.video.width', 1600),
        ));
        $this->main_video_width->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Radio', 'sitevideo_video_badge', array(
            'label' => 'Videos Badge',
            'description' => 'Do you want users to be able to create their Video Badges? Video badges will enable users to show off their videos on external blogs or websites. Multiple configuration options will enable them to create attractive badges.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.video.badge', 1),
        ));

        $this->addElement('Radio', 'sitevideo_autoplay_mycomputer_video', array(
            'label' => 'Allow Autoplay of Videos',
            'description' => "Do you want to allow autoplay videos uploaded by my computer?",
            'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.autoplay.mycomputer.video', 1),
        ));
        
        $this->addElement('Radio', 'sitevideo_rating', array(
            'label' => 'Allow Ratings',
            'description' => "Do you want to allow ratings for Channels and Videos?",
            'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.rating', 1),
            'onclick' => 'showUpdateratingSetting(this.value)'
        ));

        $this->addElement('Radio', 'sitevideorating_update', array(
            'label' => 'Allow Updating of Rating?',
            'description' => "Do you want to let members to update their rating for Channels / Videos?",
            'MultiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideorating.update', 1),
        ));


        $this->addElement('Text', 'sitevideoshow_navigation_tabs', array(
            'label' => 'Tabs in Videos / Channels Navigation bar',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'How many tabs do you want to show on Videos / Channels main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Layout" > "Menu Editor")',
            'value' => $coreSettings->getSetting('sitevideoshow.navigation.tabs', 7),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $description = sprintf(Zend_Registry::get('Zend_Translate')->_('The settings for the Advanced Lightbox Viewer have been moved to the SocialEngineAddOns Core Plugin. Please %1svisit here%2s to see and configure these settings.'), "<a href='" . $view->baseUrl() . "/admin/seaocore/settings/lightbox" . "' target='_blank'>", "</a>");
        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
