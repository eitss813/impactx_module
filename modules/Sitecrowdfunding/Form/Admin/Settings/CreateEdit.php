<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CreateEdit.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_CreateEdit extends Engine_Form {

    public function init() {

        $this->setTitle('Miscellaneous Settings')
                ->setDescription('The below settings govern various properties for projects on your website.')
                ->setName('sitecrowdfunding_global');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $redirect_array = array(
            1 => 'Project Profile Page',
            0 => 'Project Dashboard',
        );
        $this->addElement('Radio', 'sitecrowdfunding_create_redirection', array(
            'label' => 'Redirection after Project Creation',
            'description' => 'Where do you want to redirect Project Owners after Project creation?',
            'multiOptions' => $redirect_array,
            'value' => $coreSettings->getSetting('sitecrowdfunding.create.redirection', 0),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_leader', array(
            'label' => 'Allow Multiple Project Admins',
            'description' => 'Do you want to allow multiple leaders for a single Project on your site? (If enabled, then every Project will be able to have multiple leaders who will be able to manage that Project. These leaders will have the authority to add other users as leaders of that Project.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.leader', 1),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_tags', array(
            'label' => 'Allow Tags',
            'description' => 'Do you want to enable Project Owners to add tags for their Projects?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.tags', 1)
        ));

        $this->addElement('Radio', 'sitecrowdfunding_overview', array(
            'label' => 'Allow Overview',
            'description' => 'Do you want to allow Project Owners to write overview for their Projects?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.overview', 1),
        ));

        $this->addElement('MultiCheckbox', "sitecrowdfunding_contactdetail", array(
            'label' => 'Contact Detail Options',
            'description' => 'Choose the contact detail options from below that you want to be enabled for the Projects. (Users will be able to fill below chosen details for their projects from their Project Dashboard. To disable contact details section from Project dashboard, simply uncheck all the options.)',
            'multiOptions' => array(
                'phone' => 'Phone',
                'email' => 'Email',
                'social_media' => 'Social Media Url\'s',
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.contactdetail', array('phone', 'social_media', 'email')),
        ));

        $this->addElement('Radio', "sitecrowdfunding_metakeyword", array(
            'label' => 'Meta Tags / Keywords',
            'description' => 'Do you want to enable Project Owners to add Meta Tags / Keywords for their Projects? (If enabled, then project owners will be able to add them from "Meta Keyword" section of their Project Dashboard.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.metakeyword', 1),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_announcement', array(
            'label' => 'Announcements',
            'description' => 'Do you want announcements to be enabled for Projects? (If enabled, then project owner will be able to post announcements for their projects from ‘Manage Announcements’ section of their Project Dashboard.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.announcement', 1),
            'onclick' => 'showAnnouncements(this.value)'
        ));

        $this->addElement('Radio', 'sitecrowdfunding_announcementeditor', array(
            'label' => 'TinyMCE for Announcements',
            'description' => 'Do you want to allow TinyMCE for the announcements?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.announcementeditor', 1),
        ));

        $createFormFields = array(
            'location' => 'Location',
            'tags' => 'Tags',
            'photo' => 'Photo',
            'viewPrivacy' => 'View Privacy',
            'commentPrivacy' => 'Comment Privacy',
            'postPrivacy' => 'Post Privacy',
            'discussionPrivacy' => 'Discussion Privacy',
            'search' => 'Show this project on browse page and in various blocks',
        );

        $this->addElement('MultiCheckbox', 'sitecrowdfunding_createFormFields', array(
            'label' => 'Project Creation Fields',
            'description' => 'Choose the fields that you want to be available on the Project Creation page. Choosing less fields here could mean quicker Project creation. Other fields that are enabled for Projects but not chosen here will appear in Project Dashboard.',
            'multiOptions' => $createFormFields,
            'value' => $coreSettings->getSetting('sitecrowdfunding.createFormFields', array_keys($createFormFields)),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
