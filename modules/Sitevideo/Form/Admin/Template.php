<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Template.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Template extends Engine_Form {

    public function init() {

        $this->setTitle('Layout Template Settings')
                ->setDescription('You can choose from the following widgetized pages to reset it\'s present settings to the default settings of the plugin.')
                ->setAttrib('name', 'template');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $templateApi = Engine_Api::_()->getApi('settemplate', 'sitevideo');
        $getCurrentThemeSetting = $templateApi->setLayoutAccordingToTheme();

        $recommendSmall = '[Select this if your activated theme width size is greater than or equal to 950px and less than 1100px.]';
        $recommendMedium = '[Select this if your activated theme width size is greater than or equal to 1100px and less than 1200px.]';
        $recommendFull = '[Select this if your activated theme width size is greater than or equal to 1200px.]';
        if ($getCurrentThemeSetting == 1) {
            $recommendSmall = ' <b class="sitevideo_bold">*</b><b>Recommended</b> [Select this if your activated theme width size is greater than or equal to 950px and less than 1100px.]';
        } else if ($getCurrentThemeSetting == 2) {
            $recommendMedium = ' <b class="sitevideo_bold">*</b><b>Recommended</b> [Select this if your activated theme width size is greater than or equal to 1100px and less than 1200px.]';
        } else if ($getCurrentThemeSetting == 3) {
            $recommendFull = ' <b class="sitevideo_bold">*</b><b>Recommended</b> [Select this if your activated theme width size is greater than or equal to 1200px.]';
        }

        $this->addElement('Radio', 'sitevideo_setlayyoutpages', array(
            'label' => 'Set Pages',
            'description' => '<br />[<b>Note:</b> the above selected width setting will be applicable to all the widgetized pages mentioned below.]',
            'multiOptions' => array(
                1 => '<b>Small Width</b> ' . $recommendSmall,
                2 => '<b>Medium Width</b> ' . $recommendMedium,
                3 => '<b>Full Width</b> ' . $recommendFull,
            ),
            'escape' => false,
            'value' => $coreSettings->getSetting('sitevideo.setlayyoutpages', $getCurrentThemeSetting)
        ));
        $this->sitevideo_setlayyoutpages->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

        $pages = array(
            'videoHome' => 'Advanced Videos - Video Home Page',
            'browseVideo' => 'Advanced Videos - Browse Videos',
            'channelHome' => 'Advanced Videos - Channels Home Page',
            'browseChannel' => 'Advanced Videos - Browse Channels Page',
            'videoManage' => 'Advanced Videos - My Videos Page',
            'setManagePlaylist' => 'Advanced Videos - My Playlists Page',
            'watchLaterManage' => 'Advanced Videos - My Watch Later Page',
            'channelManage' => 'Advanced Videos - My Channels Page',
            'subscriptionManage' => 'Advanced Videos - My Subscriptions Page',
            'postNewVideo' => 'Advanced Videos - Post New Video',
            'editVideo' => 'Advanced Videos - Video Edit Page',
            'videoView' => 'Advanced Videos - Video View Page',
            'channelCreate' => 'Advanced Videos - Create Channel',
            'channelEdit' => 'Advanced Videos - Edit Channel',
            'channelView' => 'Advanced Videos - Channel Profile',
            'setChannelEditVideos' => 'Advanced Videos - Channel - Manage Videos Page',
            'topicView' => 'Advanced Videos - Channel - Discussion Topic View Page',
            'setBadgeCreate' => 'Advanced Videos - Channel Share by Badge Page',
            'playlistCreatePage' => 'Advanced Videos - Create Playlists Page',
            'playlistViewPage' => 'Advanced Videos - View Playlist',
            'playlistBrowsePage' => 'Advanced Videos - Browse Playlists Page',
            'playlistPlayallPage' => 'Advanced Videos - Playlist Playall Page',
            'videoCategories' => 'Advanced Videos - Video Categories Home',
            'setVideoCategories' => 'Advanced Videos - Video Category View Page',
            'channelCategories' => 'Advanced Videos - Channel Categories Home',
            'setChannelCategories' => 'Advanced Videos - Channel Category View Page',
            'pinboardBrowseVideo' => 'Advanced Videos - Browse Video\'s Pinboard View Page',
            'pinboardBrowseChannel' => 'Advanced Videos - Browse Channel\'s Pinboard View Page',
            'tagCloudVideo' => 'Advanced Videos - Video Tags',
            'tagCloudChannel' => 'Advanced Videos - Channel Tags',
            'setVideoLocations' => 'Advanced Videos - Browse Videos\' Locations',
            'memberProfileVideoParameter' => "Member Profile Page - My Videos Widget",
            'memberProfileChannelParameter' => "Member Profile Page - My Channels Widget"
        );
        $this->addElement('MultiCheckbox', 'sitevideo_pagestemplate', array(
            'label' => 'Widgetized Pages',
            'multiOptions' => $pages,
            'escape' => false,
            'value' => $coreSettings->getSetting('sitevideo.pagestemplate'),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
