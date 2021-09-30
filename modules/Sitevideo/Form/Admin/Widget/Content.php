<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Widget_Content extends Engine_Form {

    public function init() {
        $this
                ->setAttrib('id', 'form-upload');

        $topNavigationLink = array(
            'video' => 'Videos',
            'channel' => 'Channels',
            'createVideo' => 'Post New Video',
            'createChannel' => 'Create New Channel'
        );
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            $topNavigationLink = array(
                'video' => 'Videos',
                'createVideo' => 'Post New Video',
            );
        }

        $videoOption = array(
            'title' => 'Video Title',
            'creationDate' => 'Creation Date',
            'view' => 'Views',
            'location' => 'Location',
            'like' => 'Likes',
            'comment' => 'Comments',
            'duration' => 'Duration',
            'favourite' => 'Favourite',
        );
        $videoNavigationLink = array(
            'video' => 'Videos');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1)) {
            $videoNavigationLink = array_merge($videoNavigationLink, array('rated' => 'Rated'));
            $videoOption = array_merge($videoOption, array("rating" => "Rating"));
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) {
            $videoNavigationLink = array_merge($videoNavigationLink, array('watchlater' => 'Watch Later'));
            $videoOption = array_merge($videoOption, array("watchlater" => "Add to Watch Later"));
        }
        $videoOption = array_merge($videoOption, array(
            'facebook' => 'Facebook [Social Share Link]',
            'twitter' => 'Twitter [Social Share Link]',
            'linkedin' => 'LinkedIn [Social Share Link]',
            'googleplus' => 'Google+ [Social Share Link]'
        ));
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            $videoNavigationLink = array_merge($videoNavigationLink, array('playlist' => 'Playlists'));
        }
        $videoNavigationLink = array_merge($videoNavigationLink, array('liked' => 'Liked',
            'favourite' => 'Favourites',
                )
        );
        $this->addElement('MultiCheckbox', 'topNavigationLink', array(
            'label' => "Choose the action links that you want to display for the videos in this block.",
            'multiOptions' => $topNavigationLink,
        ));
        $this->addElement('MultiCheckbox', 'videoNavigationLink', array(
            'label' => "Choose the video navigation links that you want to display for the videos on this page.",
            'multiOptions' => $videoNavigationLink,
        ));

        $this->addElement('MultiCheckbox', 'viewType', array(
            'label' => "Choose the view type for videos.",
            'multiOptions' => array(
                'videoView' => 'Card view',
                'gridView' => 'Grid view',
                'listView' => 'List view',
            ),
        ));

        $this->addElement('Radio', 'searchButton', array(
            'label' => "Do you want to show search button ?",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
        ));
        $this->addElement('Select', 'defaultViewType', array(
            'label' => "Select a default view type for videos",
            'multiOptions' => array(
                'videoView' => 'Card view',
                'gridView' => 'Grid view',
                'listView' => 'List view',
            ),
            'value' => 'videoView',
        ));
        $this->addElement('Text', 'videoViewWidth', array(
            'label' => "Column width for Card View",
            'value' => 150,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'videoViewHeight', array(
            'label' => "Column height for Card View",
            'value' => 150,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'gridViewWidth', array(
            'label' => "Column width for Grid View",
            'value' => 150,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'gridViewHeight', array(
            'label' => "Column height for Grid View",
            'value' => 150,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('MultiCheckbox', 'videoOption', array(
            'label' => "Choose the options that you want to display for the videos in this block. (These information would be same for all the tabs available on My Videos page)",
            'multiOptions' => $videoOption,
        ));
        $this->addElement('Select', 'show_content', array(
            'label' => "What do you want for view more content?",
            'multiOptions' => array(
                '2' => 'Show View More Link at Bottom',
                '3' => 'Auto Load Content on Scrolling Down'),
            'value' => 2,
        ));
        $this->addElement('Select', 'orderby', array(
            'label' => "Default ordering of videos.",
            'multiOptions' => array(
                'random' => 'Random Videos',
                'creation_date' => 'Recent Videos'
            ),
            'value' => 'creation_date',
        ));
        $this->addElement('Text', 'titleTruncation', array(
            'label' => "Title truncation limit",
            'value' => 100,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'titleTruncationGridNVideoView', array(
            'label' => "Title truncation limit of Grid View and Card View",
            'value' => 100,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'descriptionTruncation', array(
            'label' => "Description truncation limit",
            'value' => 100,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'itemCountPerPage', array(
            'label' => "Count",
            'description' => '(Number of items to show)',
            'value' => 12,
            'validators' => array(
                array('Int', true),
            ),
        ));
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            $this->addElement('Dummy', 'ad_header', array(
                'label' => 'Playlist Settings',
            ));
            $this->ad_header->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->ad_header->getDecorator('Label')->setOption('style', 'font-weight:bolder;');
            $this->addElement('Text', 'videoShowLinkCount', array(
                'label' => "How many video links do you want to display in one playlist ?",
                'value' => 3,
                'validators' => array(
                    array('Int', true),
                ),
            ));
            $this->addElement('MultiCheckbox', 'playlistViewType', array(
                'label' => "Choose the video navigation links that you want to display for the videos on this page.",
                'multiOptions' => array(
                    'gridView' => 'Grid view',
                    'listView' => 'List view',
                ),
            ));
            $this->addElement('Select', 'playlistDefaultViewType', array(
                'label' => "Select a default view type for playlists",
                'multiOptions' => array(
                    'gridView' => 'Grid view',
                    'listView' => 'List view',
                ),
                'value' => 'listView',
            ));
            $this->addElement('Text', 'playlistGridViewWidth', array(
                'label' => "Column width for Playlist Grid View",
                'value' => 150,
                'validators' => array(
                    array('Int', true),
                ),
            ));
            $this->addElement('Text', 'playlistGridViewHeight', array(
                'label' => "Column height for Playlist Grid View",
                'value' => 150,
                'validators' => array(
                    array('Int', true),
                ),
            ));
            $this->addElement('Select', 'playlistOrder', array(
                'label' => "Default ordering of playlist.",
                'multiOptions' => array(
                    'random' => 'Random Playlists',
                    'creation_date' => 'Recent Playlists'
                ),
                'value' => 'creation_date',
            ));
            $this->addElement('Select', 'playlistVideoOrder', array(
                'label' => "Default ordering of playlist videos.",
                'multiOptions' => array(
                    'random' => 'Random Videos',
                    'creation_date' => 'Recent Videos'
                ),
                'value' => 'creation_date',
            ));
            $this->addElement('Radio', 'showPlayAllOption', array(
                'label' => "Show View All option for playlists ?",
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 1,
            ));
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) {
            $this->addElement('Dummy', 'watchlater_header', array(
                'label' => 'Watch Later Settings:',
            ));

            $this->watchlater_header->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->watchlater_header->getDecorator('Label')->setOption('style', 'font-weight:bolder;');
            $this->addElement('Select', 'watchlaterOrder', array(
                'label' => "Default order of watch later.",
                'multiOptions' => array(
                    'random' => 'Random Watch Later',
                    'creation_date' => 'Recent Watch Later'
                ),
                'value' => 'creation_date',
            ));
        }
        
    }

}

?>
