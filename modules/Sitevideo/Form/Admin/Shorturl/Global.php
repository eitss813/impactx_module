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
class Sitevideo_Form_Admin_Shorturl_Global extends Engine_Form {

    public function init() {

        $this->setTitle('Shorturl Settings');
        $this->setDescription('These settings affect all members in your community.');

        $this->addElement('Radio', 'sitevideo_channel_showurl_column', array(
            'label' => 'Custom Channel URL',
            'description' => 'Do you want to enable Channel Owners to create their custom Channel URL during Channel creation? (If enabled, a URL field will be available to users at the time of creating a Channel.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showediturl(this.value)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1),
        ));

        $this->addElement('Radio', 'sitevideo_channel_edit_url', array(
            'label' => 'Edit Custom Channel URL',
            'description' => 'Do you want to enable Channel Owners to edit their custom Channel URL?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.edit.url', 0),
        ));

        $this->addElement('Radio', 'sitevideo_channel_change_url', array(
            'label' => 'Automatically Shorten Channel URLs',
            'description' => 'Do you want the Channel URLs to be shortened depending upon the number of Likes. (You can choose the Likes limit below. Selecting “Yes” will change the URLs of those Channels from the form: “/channel/channel_url” to: “/channel_url”.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showurl(this.value)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.change.url', 1),
        ));

        $this->addElement('Text', 'sitevideo_channel_likelimit_forurlblock', array(
            'label' => 'Likes Limit for Active Short URL',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'Please enter the minimum number of Likes after which Channel URLs should be shortened. (Note: It is recommended to enter minimum ‘0’ likes.)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.likelimit.forurlblock', 0),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}

?>
