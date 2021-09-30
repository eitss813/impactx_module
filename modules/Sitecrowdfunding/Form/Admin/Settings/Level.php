<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {

        $isEnabledPackage = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
        parent::init();

        $this->setTitle('Member Level Settings')
                ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

        $view_element = "view";
        $this->addElement('Radio', "$view_element", array(
            'label' => 'Allow Viewing of Projects?',
            'description' => 'Do you want to let members view Projects? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow viewing of all projects, even private ones.',
                1 => 'Yes, allow viewing of projects.',
                0 => 'No, do not allow projects to be viewed.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
            unset($this->$view_element->options[2]);
        }

        if (!$this->isPublic()) {

            $create_element = "create";
            $this->addElement('Radio', "$create_element", array(
                'label' => 'Allow Creation of Projects?',
                'description' => 'Do you want to let members create Projects? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view Projects, but only want certain levels to be able to create Projects.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of projects.',
                    0 => 'No, do not allow projects to be created.'
                ),
                'value' => 1,
            ));

            $edit_element = "edit";
            $this->addElement('Radio', "$edit_element", array(
                'label' => 'Allow Editing of Projects?',
                'description' => 'Do you want to let members edit Projects? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all projects.',
                    1 => 'Yes, allow members to edit their own projects.',
                    0 => 'No, do not allow members to edit their projects.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$edit_element->options[2]);
            }

            $delete_element = "delete";
            $this->addElement('Radio', "$delete_element", array(
                'label' => 'Allow Deletion of Projects?',
                'description' => 'Do you want to let members delete Projects? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all projects.',
                    1 => 'Yes, allow members to delete their own projects.',
                    0 => 'No, do not allow members to delete their projects.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$delete_element->options[2]);
            }

            $comment_element = "comment";
            $this->addElement('Radio', "$comment_element", array(
                'label' => 'Allow Commenting on Projects?',
                'description' => 'Do you want to let members of this level comment on Projects?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all projects, including private ones.',
                    1 => 'Yes, allow members to comment on projects.',
                    0 => 'No, do not allow members to comment on projects.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$comment_element->options[2]);
            }

            $this->addElement('Radio', "reward_create", array(
                'label' => 'Allow Creation of Rewards?',
                'description' => 'Do you want to let members create rewards? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view rewards, but only want certain levels to be able to create rewards.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of rewards.',
                    0 => 'No, do not allow rewards to be created.'
                ),
                'value' => 1,
            ));
            $this->addElement('Radio', "contact", array(
                'label' => 'Allow Contact Details',
                'description' => 'Do you want to let members enter Contact Details for their Projects?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 1)) {
                $overview_element = "overview";
                $this->addElement('Radio', "$overview_element", array(
                    'label' => 'Allow Overview?',
                    'description' => 'Do you want to let members enter rich Overview for their Projects?',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.metakeyword', 1)) {
                $this->addElement('Radio', "metakeyword", array(
                    'label' => 'Meta Tags / Keywords',
                    'description' => 'Do you want to let members enter Meta Tags / Keywords for their Projects?',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }
            $photo_element = "photo";
            $this->addElement('Radio', "$photo_element", array(
                'label' => 'Allow Uploading of Photos?',
                'description' => 'Do you want to let members upload Photos to Projects?',
                'multiOptions' => array(
                    2 => 'Yes, allow photo uploading to projects, including private ones.',
                    1 => 'Yes, allow photo uploading to projects.',
                    0 => 'No, do not allow photo uploading.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$photo_element->options[2]);
            }
            if (empty($isEnabledPackage)) {
                $approved_element = "approved";
                $this->addElement('Radio', "$approved_element", array(
                    'label' => 'Project Approval Moderation',
                    'description' => 'Do you want new Project to be automatically approved?',
                    'multiOptions' => array(
                        1 => 'Yes, automatically approve Project.',
                        0 => 'No, site admin approval will be required for all Project.'
                    ),
                    'value' => 1,
                ));

                $featured_element = "featured";
                $this->addElement('Radio', "$featured_element", array(
                    'label' => 'Project Featured Moderation',
                    'description' => 'Do you want new Project to be automatically made featured?',
                    'multiOptions' => array(
                        1 => 'Yes, automatically make Project featured.',
                        0 => 'No, site admin will be making Project featured.'
                    ),
                    'value' => 1,
                ));

                $sponsored_element = "sponsored";
                $this->addElement('Radio', "$sponsored_element", array(
                    'label' => 'Project Sponsored Moderation',
                    'description' => 'Do you want new Project to be automatically made Sponsored?',
                    'multiOptions' => array(
                        1 => 'Yes, automatically make Project Sponsored.',
                        0 => 'No, site admin will be making Project Sponsored.'
                    ),
                    'value' => 1,
                ));
            }

            $availableLabels = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'parent_member' => "Parent Member (Members / Guests of 'Directory / Pages' or  'Directory / Businesses' or 'Groups / Communities' or 'Advanced Events' plugin) Only",
                'leader' => 'Owner and Leaders Only / Just Me'
            );
            $roles = array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'parent_member', 'leader');

            if ((Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) 
                || (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitebusiness_business", 'item_module' => 'sitebusiness')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) 
                || (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitegroup_group", 'item_module' => 'sitegroup')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember'))
                || (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent'))) {
                //No need to do anything
            } else {
                unset($availableLabels['parent_member']);
            }

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Project View Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their Projects. If you do not check any options, everyone will be allowed to view Projects.',
                'multiOptions' => $availableLabels,
                'value' => $roles,
            ));

            $availableLabels = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'parent_member' => "Parent Member (Members / Guests of 'Directory / Pages' or  'Directory / Businesses' or 'Groups / Communities' or 'Advanced Events' plugin) Only",
                'leader' => 'Owner and Leaders Only / Just Me'
            );
            $roles = array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'parent_member', 'leader');

            if ((Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) 
                || (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitebusiness_business", 'item_module' => 'sitebusiness')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) 
                || (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitegroup_group", 'item_module' => 'sitegroup')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember'))
                || (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent'))) {
                //No need to do anything
            } else {
                unset($availableLabels['parent_member']);
            }  

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Project Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their Projects. If you do not check any options, everyone will be allowed to post comments on Projects.',
                'multiOptions' => $availableLabels,
                'value' => $roles,
            ));

            if (Engine_Api::_()->hasModuleBootstrap('advancedactivity')) {
                $post_element = "post";
                $this->addElement('Radio', "$post_element", array(
                    'label' => 'Allow Posting of Updates?',
                    'description' => 'Do you want to let members to post updates on Projects?',
                    'multiOptions' => array(
                        2 => 'Yes, allow posting updates on projects, including private ones.',
                        1 => 'Yes, allow posting updates on projects.',
                        0 => 'No, do not allow posting updates.'
                    ),
                    'value' => ( $this->isModerator() ? 2 : 1 ),
                ));
                if (!$this->isModerator()) {
                    unset($this->$post_element->options[2]);
                }
                $auth_post_element = "auth_post";
                $this->addElement('MultiCheckbox', "$auth_post_element", array(
                    'label' => 'Posting Updates Options',
                    'description' => 'Your members can choose from any of the options checked below when they decide who can post updates on their Projects. If you do not check any options, everyone will be allowed to post updates on the Projects of this member level.',
                    'multiOptions' => $availableLabels,
                    'value' => $roles
                ));
            }
            $topic_element = "topic";
            $this->addElement('Radio', "$topic_element", array(
                'label' => 'Allow Posting of Discusstion Topics?',
                'description' => 'Do you want to let members post discussion topics to Projects?',
                'multiOptions' => array(
                    2 => 'Yes, allow discussion topic posting to projects, including private ones.',
                    1 => 'Yes, allow discussion topic posting to projects.',
                    0 => 'No, do not allow discussion topic posting.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$topic_element->options[2]);
            }
            $auth_topic_element = "auth_topic";
            $this->addElement('MultiCheckbox', "$auth_topic_element", array(
                'label' => 'Discussion Topic Posting Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post the discussion topics in their Projects. If you do not check any options, everyone will be allowed to post discussion topics in the Projects of this member level.',
                'multiOptions' => $availableLabels,
                'value' => $roles
            ));
            $max_element = "max";
            $this->addElement('Text', "$max_element", array(
                'label' => 'Maximum Allowed Projects',
                'description' => 'Enter the maximum number of allowed Projects. This field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));

            $Note = '';
            if ($isEnabledPackage) {
                $Note = '<br>[Note: This setting will work only if \'Packages\' are disabled from "Packages" -->  "Package Settings".]';
            }
            if (empty($isEnabledPackage)) {
                $this->addElement('Radio', 'lifetime', array(
                    'label' => 'Project Duration (Life Time)',
                    'description' => "Do you want to let members create projects with life time duration? [Note: Limit of life time duration is 5 years.]",
                    'multiOptions' => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'value' => 1,
                ));
            }
            $this->addElement('Select', 'commission_handling', array(
                'label' => 'Commission Type',
                'description' => 'Select the type of commission. This commission will be applied on all backed amount for the Projects of this level.' . $Note,
                'multiOptions' => array(
                    1 => 'Percent',
                    0 => 'Fixed'
                ),
                'value' => 1,
                'onchange' => 'showcommissionType();'
            ));

            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
            $this->addElement('Text', 'commission_fee', array(
                'label' => 'Commission Value (' . $currencyName . ')',
                'description' => 'Enter the value of the commission. (If you do not want to apply any commission, then simply enter 0.)' . $Note,
                'allowEmpty' => false,
                'value' => 1,
            ));

            $this->addElement('Text', 'commission_rate', array(
                'label' => 'Commission Value (%)',
                'description' => 'Enter the value of the commission. (Do not add any symbol. For 10% commission, enter commission value as 10. You can only enter commission percentage between 0 and 100.)' . $Note,
                'validators' => array(
                    array('Between', true, array('min' => 0, 'max' => 100, 'inclusive' => true)),
                ),
                'value' => 1,
            ));

            $this->commission_handling->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
            $this->commission_rate->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
            $this->commission_fee->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
            // Element: transfer_threshold
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', '0')) {
                $this->addElement('Text', 'transfer_threshold', array(
                    'label' => "Payment Threshold Amount ($currencyName)",
                    'description' => 'Enter the payment threshold amount. Project owners of projects of this level will be able to request you for their payments when the total amount of their Project backing becomes more than this threshold amount.',
                    'allowEmpty' => false,
                    'required' => true,
                    'value' => 100,
                ));
            }
        }
    }

}
