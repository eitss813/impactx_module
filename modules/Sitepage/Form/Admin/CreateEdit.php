<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_CreateEdit extends Engine_Form {

    public function init() {

        $this->setTitle('Miscellaneous Settings')
                ->setDescription('The below settings govern various properties for pages on your website. By choosing fewer fields for page creation, you can make creating pages quicker on your website (Remaining fields can be configured from Page Dashboard).')
                ->setName('sitepage_global');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Radio', 'sitepage_description_allow', array(
            'label' => 'Allow Description',
            'description' => 'Do you want to allow page owners to write description for their pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.description.allow', 1),
            'onclick' => 'showDescription(this.value)'
        ));

        //VALUE FOR DESCRIPTION
        $this->addElement('Radio', 'sitepage_requried_description', array(
            'label' => 'Description Required',
            'description' => 'Do you want to make Description a mandatory field for directory items / pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.requried.description', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitepage_requried_photo', array(
            'label' => 'Profile Photo Required',
            'description' => 'Do you want to make Profile Photo a mandatory field for directory items / pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.requried.photo', 0),
        ));
        $this->addElement('Radio', 'sitepage_status_show', array(
            'label' => 'Open / Closed status in Search',
            'description' => 'Do you want the Status field (Open / Closed) in the search form widget? (This widget appears on the "Pages Home", "My Pages" and "Browse Pages" pages, and enables users to search and filter pages.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.status.show', 1),
        ));

        $this->addElement('Radio', 'sitepage_profile_fields', array(
            'label' => 'Profile Information Fields',
            'description' => 'Do you want to display Profile Information Fields associated with the selected category while creation of directory items / pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.profile.fields', 1),
        ));

        //VALUE FOR ENABLE /DISABLE PRICE FIELD
        $this->addElement('Radio', 'sitepage_price_field', array(
            'label' => 'Price Field',
            'description' => 'Do you want the Price field to be enabled for directory items / pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.price.field', 1),
        ));

        $createFormFields = array(
            'location' => 'Location',
            'tags' => 'Tags',
            'photo' => 'Photo [Note: This setting will only work if photo is not required.]',
            'description' => 'Description [Note: This setting will only work if description is not required.]',
            'price' => 'Price',
            'viewPrivacy' => 'View Privacy',
            'commentPrivacy' => 'Comment Privacy',
            'allPostPrivacy' => 'Post Updates'
        );

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
            $createFormFields = array_merge($createFormFields, array('discussionPrivacy' => 'Discussion Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
            $createFormFields = array_merge($createFormFields, array('photoPrivacy' => 'Photo Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $createFormFields = array_merge($createFormFields, array('videoPrivacy' => 'Video Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
            $createFormFields = array_merge($createFormFields, array('documentPrivacy' => 'Document Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
            $createFormFields = array_merge($createFormFields, array('pollPrivacy' => 'Poll Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
            $createFormFields = array_merge($createFormFields, array('notePrivacy' => 'Note Privacy'));
        }

        if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy' => 'Event Privacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy' => 'Event Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
            $createFormFields = array_merge($createFormFields, array('musicPrivacy' => 'Music Privacy'));
        }
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $createFormFields = array_merge($createFormFields, array('memberTitle' => 'What will members be called setting?'));
            $createFormFields = array_merge($createFormFields, array('memberInvite' => 'Invite member'));
            $createFormFields = array_merge($createFormFields, array('memberApproval' => 'Approve members?'));
        }
        
        $createFormFields = array_merge($createFormFields, array(
            'subPagePrivacy' => 'Sub Page Creation Privacy',
            'claimThisPage' => 'Claim this Page',
            'status' => 'Status',
            'search' => 'Show this page on browse page and in various blocks',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options'
        ));

        $this->addElement('MultiCheckbox', 'sitepage_createFormFields', array(
            'label' => 'Page Creation Fields',
            'description' => 'Choose the fields that you want to be available on the Page Creation page. Choosing less fields here could mean quicker page creation. Other fields that are enabled for pages but not chosen here will appear in Page Dashboard.',
            'multiOptions' => $createFormFields,
            'value' => $coreSettings->getSetting('sitepage.createFormFields', array_keys($createFormFields)),
        ));

        $this->addElement('Hidden', 'is_remove_note', array('value' => 0, 'order' => 999));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}