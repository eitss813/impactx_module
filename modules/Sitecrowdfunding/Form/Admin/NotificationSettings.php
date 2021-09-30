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
class Sitecrowdfunding_Form_Admin_NotificationSettings extends Engine_Form {

    public function init() {
        // create an object for view
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators();
        $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This page contains settings for notification/activity settings for projects."));

        $this->setTitle('Notification/Activity settings');
        $this->setDescription($description);
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->getDecorator('Description')->setOption('escape', false);


        // project approval
        $this->addElement('Radio', 'sitecrowdfunding_notification_project_approval', array(
            'label' => 'Project Approval',
            'description' => "Do you want the site to send notification to project owner once its has been approved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.notification.project.approval', 0)
        ));
        // project approval

        // project disapproval
        $this->addElement('Radio', 'sitecrowdfunding_notification_project_disapproval', array(
            'label' => 'Project Disapproval',
            'description' => "Do you want the site to send notification to project owner once its has been disapproved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.notification.project.disapproval', 0)
        ));
        // project disapproval

        // project funding approval
        $this->addElement('Radio', 'sitecrowdfunding_notification_project_funding_approval', array(
            'label' => 'Project Funding Approval',
            'description' => "Do you want the site to send notification to project owner once funding has been approved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.notification.project.funding.approval', 0)
        ));
        // project funding approval

        // project funding disapproval
        $this->addElement('Radio', 'sitecrowdfunding_notification_project_funding_disapproval', array(
            'label' => 'Project Funding Disapproval',
            'description' => "Do you want the site to send notification to project owner once funding has been disapproved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.notification.project.funding.disapproval', 0)
        ));
        // project funding disapproval


        // project approval
        $this->addElement('Radio', 'sitecrowdfunding_activity_project_approval', array(
            'label' => 'Project Approval Activity Feed',
            'description' => "Do you want the site to send activity post in the public feed once its has been approved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.activity.project.approval', 0)
        ));
        // project approval

        // project funding approval
        $this->addElement('Radio', 'sitecrowdfunding_activity_project_funding_approval', array(
            'label' => 'Project Funding Approval Activity Feed',
            'description' => "Do you want the site to send activity post in the public feed once funding has been approved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.activity.project.funding.approval', 0)
        ));
        // project funding approval

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}