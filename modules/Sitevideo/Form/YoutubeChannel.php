<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Channel.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_YoutubeChannel extends Engine_Form {

    public function init() {

        $hiddenOrderCount=76548;
        $user = Engine_Api::_()->user()->getViewer();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        // Init form
        $this
                ->setTitle("Upload Youtube Channel's Video")
                ->setAttrib('id', 'form-channel-upload')
                ->setAttrib('name', 'channels_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this->addElement('Text', 'youtube_channel_keyword', array(
            'label' => 'Channel Keyword',
            'description' => 'Enter the keywords to find the associated YouTube channels.',
            'maxlength' => '150',
            'filters' => array(
                'StripTags',
            )
        ));
        $this->youtube_channel_keyword->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
        $this->addElement('Text', 'youtube_channel_url', array(
            'label' => 'Channel URL',
            'description' => 'Enter the YouTube channel’s URL to get the videos from that channel.',
            'maxlength' => '150',
            'filters' => array(
                'StripTags',
            )
        ));
        $this->youtube_channel_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
        
        $this->addElement('Hidden', 'youtube_channel_id', array( 'order' => $hiddenOrderCount++,));
        $this->addElement('Hidden', 'pending_video', array( 'order' => $hiddenOrderCount++,));

        // Init submit
        $this->addElement('Button', 'channelsubmit', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

}
