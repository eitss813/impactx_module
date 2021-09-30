<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BackerFilter.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
class Sitepage_Form_Admin_NotificationSettings extends Engine_Form {

    public function init() {
        // create an object for view
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators();
        $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This page contains settings for notification/activity settings for organization."));

        $this->setTitle('Notifications/Email settings');
        $this->setDescription($description);
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->getDecorator('Description')->setOption('escape', false);


        // page approval notification
        $this->addElement('Radio', 'sitepage_notification_page_approval', array(
            'label' => 'Organization Approval',
            'description' => "Do you want the site to send notification to organization owner once its has been approved by super admin?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitepage.notification.page.approval', 0)
        ));
        // page approval notification

        // page disapproval notification
        $this->addElement('Radio', 'sitepage_notification_page_disapproval', array(
            'label' => 'Organization Disapproval',
            'description' => "Do you want the site to send notification to organization owner once its has been disapproved by super admin?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitepage.notification.page.disapproval', 0)
        ));
        // page disapproval notification

        // approval email
        $this->addElement('Radio', 'sitepage_email_page_approval', array(
            'label' => 'Organization Approval Email',
            'description' => "Do you want the site to send email to organization owner once its has been approved by super admin?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitepage.email.page.approval', 0)
        ));
        // approval email

        // disapproval email
        $this->addElement('Radio', 'sitepage_email_page_disapproval', array(
            'label' => 'Organization Disapproval Email',
            'description' => "Do you want the site to send email to organization owner once its has been disapproved by super admin?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitepage.email.page.disapproval', 0)
        ));
        // disapproval email


        // page approval -> activity
        $this->addElement('Radio', 'sitepage_activity_page_approval', array(
            'label' => 'Organization Approval Activity Feed',
            'description' => "Do you want the site to send activity post in the public feed once its has been approved by super admin?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitepage.activity.page.approval', 0)
        ));
        // page approval -> activity


        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}