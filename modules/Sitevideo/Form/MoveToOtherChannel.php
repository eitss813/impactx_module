<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MoveToOtherChannel.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_MoveToOtherChannel extends Engine_Form {

    protected $_item;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {
        $this
                ->setTitle('Move video to another channel?')
                ->setDescription('')
        ;

        // Get channels
        $channelTable = Engine_Api::_()->getItemTable('sitevideo_channel');
        $myChannels = $channelTable->select()
                ->from($channelTable, array('channel_id', 'title', 'type'))
                ->where('owner_type = ?', 'user')
                ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                ->query()
                ->fetchAll();

        $channelOptions = array('' => '');
        foreach ($myChannels as $myChannel) {
            if ($this->_item->getIdentity() == $myChannel['channel_id'] || ($myChannel['type'] != null))
                continue;
            $channelOptions[$myChannel['channel_id']] = $myChannel['title'];
        }
        if (count($channelOptions) == 1) {
            $channelOptions = array();
        }

        $this->addElement('Select', 'move', array(
            'label' => 'Select another channel for this video:',
            'MultiOptions' => $channelOptions,
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Move Video',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $this->getDisplayGroup('buttons');
    }

}
