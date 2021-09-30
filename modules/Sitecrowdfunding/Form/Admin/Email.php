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
class Sitecrowdfunding_Form_Admin_Email extends Engine_Form {

    public function init() {
        // create an object for view
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators(); 
        $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This page contains settings for automatic reminder emails for projects. You can also send sample emails to yourself to see the content.")); 

        $this->setTitle('Automatic Reminder Email Settings'); 
        $this->setDescription($description);
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->getDecorator('Description')->setOption('escape', false);


        // project approval
        $this->addElement('Radio', 'sitecrowdfunding_reminder_project_approval', array(
            'label' => 'Project Approval',
            'description' => "Do you want the site to send email to project owner once its has been approved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.project.approval', 0)
        ));
        // project approval

        // project disapproval
        $this->addElement('Radio', 'sitecrowdfunding_reminder_project_disapproval', array(
            'label' => 'Project Disapproval',
            'description' => "Do you want the site to send email to project owner once its has been disapproved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.project.disapproval', 0)
        ));
        // project disapproval

        // project funding approval
        $this->addElement('Radio', 'sitecrowdfunding_reminder_project_funding_approval', array(
            'label' => 'Project Funding Approval',
            'description' => "Do you want the site to send email to project owner once funding has been approved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.project.funding.approval', 0)
        ));
        // project funding approval

        // project funding disapproval
        $this->addElement('Radio', 'sitecrowdfunding_reminder_project_funding_disapproval', array(
            'label' => 'Project Funding Disapproval',
            'description' => "Do you want the site to send email to project owner once funding has been disapproved by super admin ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.project.funding.disapproval', 0)
        ));
        // project funding disapproval

        // project backer success
        $this->addElement('Radio', 'sitecrowdfunding_reminder_project_backer_success', array(
            'label' => 'Project Backers Success',
            'description' => "Do you want the site to send email to project backer once successful funding?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.project.backer.success', 0)
        ));
        // project backer success

        $this->addElement('Radio', 'sitecrowdfunding_reminder_before_project_completion', array(
            'label' => 'Before Project Completion',
            'description' => "Do you want the site to send automatic email reminders to project owners and to members who have marked projects as favourites before completion of those projects?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.before.project.completion', 0)
        )); 
        $this->addElement('MultiCheckbox', 'sitecrowdfunding_reminder_project_completion_options', array(
            'label' => 'Email to Members',
            'description' => 'Choose to whom you want to send reminder emails before completion of a project.',
            'multiOptions' => array('project_owners' => 'Project Owners', 'favourites' => 'Members who have marked projects as favourite','project_owners_and_admins' => 'Both project owner and admins'),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.project.completion.options', 'project_owners'),
        )); 

//        $this->addElement('Radio', 'sitecrowdfunding_reminder_for_project_payment', array(
//            'label' => 'For Payment after Project Creation',
//            'description' => "Do you want the site to send automatic email reminders to project owners for pending package payment of their projects.",
//            'multiOptions' => array(
//                1 => 'Yes',
//                0 => 'No'
//            ),
//            'value' => $settings->getSetting('sitecrowdfunding.reminder.for.project.payment', 1)
//        ));


        $this->addElement('Radio', 'sitecrowdfunding_reminder_for_payment_gateway_configuration', array(
            'label' => 'To Configure Payment Gateways',
            'description' => "Do you want the site to send automatic email reminders to project owners to configure payment gateways for their projects.",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.for.payment.gateway.configuration', 1)
        )); 
        $this->addElement('Select', 'sitecrowdfunding_reminder_duration_options', array(
            'label' => 'Duration of Upcoming Reminder Email',
            'description' => 'Select the duration before or after which a reminder email will be sent.',
            'multiOptions' => array(
                '1' => '1 Day',
                '2' => '2 Days',
                '3' => '3 Days',
                '7' => '1 Week',
                '14' => '2 Weeks',
                '30' => '1 Month'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.reminder.duration.options', array('0' => 1))
        )); 

        $this->addElement('Checkbox', 'sitecrowdfunding_reminder_demo', array(
            'label' => 'Send me test emails to check the above settings',
            'value' => $settings->getSetting('sitecrowdfunding.reminder.demo', 1),
        ));

        $this->addElement('Text', 'sitecrowdfunding_admin_mail', array(
            'label' => 'Email ID for Testing',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true),
            ),
            'value' => $settings->getSetting('sitecrowdfunding.admin.mail', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
        ));
        $this->sitecrowdfunding_admin_mail->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
        $this->sitecrowdfunding_admin_mail->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        )); 
    }

}