<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: NotificationSettings.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_NotificationSettings extends Engine_Form {

    public function init() {

        $this->setTitle('Notification and Email Settings')
                ->setDescription('What do you want to get notified and email about?')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'notifications');

        $this->addElement('Checkbox', 'email', array(
            'description' => 'Email Notifications',
            'label' => "Send email notifications when people perform various actions on this channel (Below you can individually activate emails for the actions).",
            'onclick' => 'showEmailAction()',
            'value' => 1,
        ));
        $optionsArray = array("email.posted" => "People post updates on this channel", "email.created" => "People add various videos on this channel");
        $this->addElement('MultiCheckbox', 'action_email', array(
            'multiOptions' => $optionsArray,
            'value' => array("email.posted", "email.created")
        ));


        $this->addElement('Checkbox', 'notification', array(
            'description' => 'Site Notifications',
            'label' => "Send notification updates when people perform various actions on this channel (Below you can individually activate notifications for the actions).",
            'onclick' => 'showNotificationAction()',
            'value' => 1,
        ));

        $optionsArray = array("notification.posted" => "People post updates on this channel", "notification.created" => "People upload various videos on this channel", "notification.comment" => "People post comments on this channel", "notification.like" => "People like this channel", "notification.discussion" => "People create a discussion on this channel");
        $this->addElement('MultiCheckbox', 'action_notification', array(
            'multiOptions' => $optionsArray,
            'value' => array("notification.posted", "notification.created", "notification.comment", "notification.like", "notification.discussion")
        ));


        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'decorators' => array('ViewHelper'),
            'type' => 'submit',
            'ignore' => true,
        ));
        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel'
                ), 'buttons');
    }

}
