<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChannelSettings.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_ChannelSettings extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
                ->setTitle('Channel Settings')
                ->setDescription('These settings affect all members in your community.');


        $this->addElement('Text', 'sitevideo_channel_manifestUrlP', array(
            'label' => 'URL Alternate Text for "Channels"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "channels" in the URLs of this plugin.',
            'value' => $coreSettings->getSetting('sitevideo.channel.manifestUrlP', "channels"),
        ));



        $this->addElement('Radio', 'sitevideo_subscriptions_enabled', array(
            'label' => 'Subscribe',
            'description' => 'Do you want to enable Subscribe feature for channels?',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.subscriptions.enabled', 1)
        ));
        $this->addElement('Radio', 'sitevideo_category_enabled', array(
            'label' => 'Allow Category',
            'description' => 'Do you want the Category field to be enabled for Channels?',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.category.enabled', 1)
        ));

        $this->addElement('Radio', 'sitevideo_tags_enabled', array(
            'label' => 'Allow Tags',
            'description' => 'Do you want the Tags field to be enabled for Channels?',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => $coreSettings->getSetting('sitevideo.tags.enabled', 1)
        ));
        $this->addElement('Radio', 'sitevideo_overview', array(
            'label' => 'Allow Overview',
            'description' => 'Do you want to allow channel’s owner to write overview for their channels?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.overview', 1),
        ));
        $this->addElement('Radio', "sitevideo_metakeyword", array(
            'label' => 'Meta Tags / Keywords',
            'description' => 'Do you want to enable channel owners to add Meta Tags / Keywords for their channels? (If enabled, then channel owners will be able to add them from "Meta Keyword" section of their Channel Dashboard.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.metakeyword', 1),
        ));

        $this->addElement('Text', 'sitevideo_sponsoredcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowSponsred.tpl',
                        'class' => 'form element'
                    )))
        ));
        $this->addElement('Radio', 'sitevideo_channel_addvideo_other_channel', array(
            'label' => 'Adding Same Video to Multiple Channels',
            'description' => 'Do you want to allow the same video can be added to multiple channels? <br/> [Note: If set to ‘No’, videos uploaded by you cannot be added into other channels.]',
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'onclick' => 'addVideoOption()',
            'value' => $coreSettings->getSetting('sitevideo.channel.addvideo.other.channel', 1),
        ));
        $this->addElement('Radio', 'sitevideo_channel_add_othermember_video', array(
            'label' => "Adding Member's Video to Channels",
            'description' => "Do you want to allow member's video can be added to channels? <br/> [Note: If set to ‘No’, videos of other members that you have liked / favourited / rated cannot be added into your channels. These videos are added as a collection, which means if the owner deletes those videos, then they will be automatically deleted from your channels.]",
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'onclick' => 'addVideoOption()',
            'value' => $coreSettings->getSetting('sitevideo.channel.add.othermember.video', 1)
        ));
        $this->addElement('MultiCheckbox', 'sitevideo_add_videos_options', array(
            'label' => 'Add More Videos Options',
            'description' => "Select the options that you want to enable at channel dashboard while adding more videos to a channel.",
            'multiOptions' => array(
                1 => 'My Uploads ',
                2 => 'My Likes',
                3 => 'My Favorites',
                4 => 'My Rated'
            ),
            'value' => $coreSettings->getSetting('sitevideo.add.videos.options', array(0, 1, 2, 3))
        ));

        $this->addElement('Radio', 'sitevideo_channel_tinymceditor', array(
            'label' => 'TinyMCE Editor for Discussion',
            'description' => 'Allow TinyMCE editor for creation / edition Discussion in channels.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.channel.tinymceditor', 1),
        ));

        $this->addElement('Radio', 'sitevideo_channel_creation', array(
            'label' => 'Default Channel Creation',
            'description' => 'Do you want to allow default Channel creation on new sign up?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitevideo.channel.creation', 0),
        ));

        //NETWORK BASE CHANNEL
        $this->addElement('Radio', 'sitevideo_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show channels according to viewer's network if he has selected any? (If set to no, all the channels will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $coreSettings->getSetting('sitevideo.network', 0),
        ));
        //$this->getElement('sitevideo_network')->getDecorator('Label')->setOption('class', 'incomplete');
        //VALUE FOR Page Dispute Link.
        $this->addElement('Radio', 'sitevideo_default_show', array(
            'label' => 'Set Only My Networks as Default in Search',
            'description' => 'Do you want to set "Only My Network" option as default for Show field in the search form widget? (This widget appears on the Browse Channels to enable users to search and filter channels.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetworkType(this.value)',
            'value' => $coreSettings->getSetting('sitevideo.default.show', 0),
        ));
        // $this->getElement('sitevideo_default_show')->getDecorator('Label')->setOption('class', 'incomplete');
        $this->addElement('Radio', 'sitevideo_networks_type', array(
            'label' => 'Network selection for Channels',
            'description' => "You have chosen that viewers should only see Channels of their network(s). How should a Channel's network(s) be decided?",
            'multiOptions' => array(
                0 => "Channel Owner's network(s) [If selected, only members belonging to channel owner's network(s) will see the Channels.]",
                1 => "Selected Networks [If selected, channel owner will be able to choose the networks of which members will be able to see their Channel.]"
            ),
            'value' => $coreSettings->getSetting('sitevideo.networks.type', 0),
        ));
        // $this->getElement('sitevideo_networks_type')->getDecorator('Label')->setOption('class', 'incomplete');
        $this->addElement('Radio', 'sitevideo_networkprofile_privacy', array(
            'label' => 'Display Profile Channel only to Network Users',
            'description' => "Do you want to show the Channel Profile page only to users of the same network. (If set to yes and \"Browse By Networks\" is enabled then users would not be able to view the profile page of those channels which does not belong to their networks.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            // 'onclick' => 'showviewablewarning(this.value);',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.networkprofile.privacy', 0),
        ));

        //   $this->getElement('sitevideo_networkprofile_privacy')->getDecorator('Label')->setOption('class', 'incomplete');
        $this->addElement('Radio', 'sitevideo_privacybase', array(
            'label' => ' Displays All Channels in Widgets.',
            'description' => "Do you want to show all the channels to the users in the Widgets and Browse Channels of this plugin irrespective of privacy? [Note: If you select 'No', then only those channels will be shown in the Widgets and Browse Channels which are viewable to the current logged-in users. But this may slightly affect the loading speed of your website. To avoid such loading delay to the best possible extent, we are also using caching based display.)",
            'multiOptions' => array(
                0 => 'Yes',
                1 => 'No'
            ),
            // 'onclick' => 'showviewablewarning(this.value);',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.privacybase', 0),
        ));

        //$this->getElement('sitevideo_privacybase')->getDecorator('Label')->setOption('class', 'incomplete');
        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
