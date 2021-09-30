<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        parent::init();

        // My stuff
        $this
                ->setTitle('Member Level Settings')
                ->setDescription('CHANNEL_FORM_ADMIN_LEVEL_DESCRIPTION');

        // Element: view
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Channels?',
            'description' => 'Do you want to let members view channels? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow members to view all channels, even private ones.',
                1 => 'Yes, allow viewing of channels.',
                0 => 'No, do not allow channels to be viewed.'
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        //$this->getElement('view')->getDecorator('Label')->setOption('class', 'incomplete');
        if (!$this->isModerator()) {
            unset($this->view->options[2]);
        }

        if (!$this->isPublic()) {

            // Element: create
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Channels?',
                'description' => 'Do you want to let members create channels? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view channels, but only certain levels to be able to create channels.',
                'value' => 1,
                'multiOptions' => array(
                    1 => 'Yes, allow creation of channels.',
                    0 => 'No, do not allow channels to be created.'
                ),
                'value' => 1,
            ));

            // Element: edit
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow Editing of Channels?',
                'description' => 'Do you want to let members of this level edit channels?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all channels.',
                    1 => 'Yes, allow members to edit their own channels.',
                    0 => 'No, do not allow channels to be edited.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
         //   $this->getElement('edit')->getDecorator('Label')->setOption('class', 'incomplete');
            if (!$this->isModerator()) {
                unset($this->edit->options[2]);
            }

            // Element: delete
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Channels?',
                'description' => 'Do you want to let members of this level delete channels?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all channels.',
                    1 => 'Yes, allow members to delete their own channels.',
                    0 => 'No, do not allow members to delete their channels.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            //$this->getElement('delete')->getDecorator('Label')->setOption('class', 'incomplete');
            if (!$this->isModerator()) {
                unset($this->delete->options[2]);
            }

            // Element: comment
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow Commenting on Channels?',
                'description' => 'Do you want to let members of this level comment on channels?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all channels, including private ones.',
                    1 => 'Yes, allow members to comment on channels.',
                    0 => 'No, do not allow members to comment on channels.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
 //           $this->getElement('comment')->getDecorator('Label')->setOption('class', 'incomplete');
            if (!$this->isModerator()) {
                unset($this->comment->options[2]);
            }

            $topic_element = "topic";
            $this->addElement('Radio', "$topic_element", array(
                'label' => 'Allow Posting of Discusstion Topics?',
                'description' => 'Do you want to let members post discussion topics to channels?',
                'multiOptions' => array(
                    2 => 'Yes, allow discussion topic posting to channels, including private ones.',
                    1 => 'Yes, allow discussion topic posting to channels.',
                    0 => 'No, do not allow discussion topic posting.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$topic_element->options[2]);
            }


            // Element: rate
            $this->addElement('Radio', 'rate', array(
                'label' => 'Allow Rating on Channels?',
                'description' => 'Do you want to let members of this level rate on channels?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to rate on channels.',
                    0 => 'No, do not allow members to rate on channels.',
                ),
                'value' => 1,
            ));
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.overview', 1)) {
                $overview_element = "overview";
                $this->addElement('Radio', "$overview_element", array(
                    'label' => 'Allow Overview?',
                    'description' => 'Do you want to let members enter rich Overview for their channels?',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }
            $this->addElement('Radio', "youtube_channel", array(
                    'label' => 'Youtube Channel',
                    'description' => 'Who can associate a Youtube Channel with his own channel on your website?',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 0,
                ));
            
            $photo_element = "photo";
            $this->addElement('Radio', "$photo_element", array(
                'label' => 'Allow Uploading of Photos?',
                'description' => 'Do you want to let members upload Photos to channels?',
                'multiOptions' => array(
                    2 => 'Yes, allow photo uploading to channels, including private ones.',
                    1 => 'Yes, allow photo uploading to channels.',
                    0 => 'No, do not allow photo uploading.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$photo_element->options[2]);
            }
            $this->addElement('Radio', "metakeyword", array(
                'label' => 'Meta Tags / Keywords',
                'description' => 'Do you want to let members to enter Meta Keywords for their channels?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Channel Privacy',
                'description' => 'CHANNEL_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me'
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            // Element: auth_comment
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Channel Comment Options',
                'description' => 'CHANNEL_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me'
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            $auth_topic_element = "auth_topic";
            $this->addElement('MultiCheckbox', "$auth_topic_element", array(
                'label' => 'Discussion Topic Posting Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post the discussion topics in their channels. If you do not check any options, everyone will be allowed to post discussion topics to the channels of this member level.',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me'
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            $this->addElement('Text', 'sitevideo_max_allowed_channel', array(
                'label' => 'Maximum Allowed Channels',
                'description' => 'Enter the maximum number of channels that are allowed to be created. This field must contain an integer, use zero for unlimited channels.',
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.max.allowed.channel', 0),
            ));
        }
    }

}
