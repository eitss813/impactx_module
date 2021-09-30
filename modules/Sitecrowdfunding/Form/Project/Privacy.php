<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Privacy extends Engine_Form {

    public $_error = array();
    protected $_defaultProfileId;
    protected $_parentTypeItem;

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function setParentTypeItem($item) {
        $this->_parentTypeItem = $item;
    }

    public function getParentTypeItem() {
        return $this->_parentTypeItem;
    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        //PACKAGE ID
        $package_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        if ($this->_item) {
            $package_id = $this->_item->package_id;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS
        $hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
        $projectTypeOptions = array();
        $isAllowedLifeTimeProject = false;
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($hasPackageEnable) {
            $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $package_id);
            $isAllowedLifeTimeProject = $package->lifetime;
        } else {
            $this->setTitle("Privacy");
            $isAllowedLifeTimeProject = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "lifetime");
        }

        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if($paymentMethod == 'escrow'){
            $isAllowedLifeTimeProject = false;
        }

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Select privacy related information about this project.")))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_privacy')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_privacy_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);

        $createFormFields = array(
            'location',
            'tags',
            'photo',
            'viewPrivacy',
            'commentPrivacy',
            'postPrivacy',
            'discussionPrivacy',
            'search',
        );
        if (empty($project_id) && Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitecrowdfunding.createFormFields')) {
            $createFormFields = $settings->getSetting('sitecrowdfunding.createFormFields', $createFormFields);
        }

        $orderPrivacyHiddenFields = 786590;

        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'leader' => 'Owner and Admins Only'
        );
        if ($this->getParentTypeItem()) {
            $parentType = $this->getParentTypeItem()->getType();
            $shortTypeName = $this->getParentTypeItem()->getShortType(1);
            $explodeParentType = explode('_', $parentType);
            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && (in_array($parentType, array('sitepage_page', 'sitebusiness_business', 'sitegroup_group'))) && (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parentType, 'item_module' => $explodeParentType[0])))) {
                    $view_options['parent_member'] = $shortTypeName . ' Members Only';
                    $availableLabels = array(
                        'everyone' => 'Everyone',
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'parent_member' => $shortTypeName . ' Members Only',
                        'leader' => 'Owner and Admins Only'
                    );
                } elseif (($parentType == 'siteevent_event') && (Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parentType, 'item_module' => $explodeParentType[0])))) {
                    $availableLabels = array(
                        'everyone' => 'Everyone',
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'parent_member' => 'Event Guests Only',
                        'leader' => 'Owner and Admins Only'
                    );
                }
            }
        }

        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecrowdfunding_project', $user, "auth_view");
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields) && count($view_options) > 1) {
            $this->addElement('Select', 'auth_view', array(
                'label' => 'View Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may see this project?"),
                // 'attribs' => array('class' => 'se_quick_advanced'),
                'multiOptions' => $view_options,
                'value' => key($view_options),
            ));
            $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($view_options) == 1) {
            $this->addElement('Hidden', 'auth_view', array(
                'value' => key($view_options),
                'order' => ++$orderPrivacyHiddenFields
            ));
        } else {
            $this->addElement('Hidden', 'auth_view', array(
                'value' => "everyone",
                'order' => ++$orderPrivacyHiddenFields
            ));
        }

        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecrowdfunding_project', $user, "auth_comment");
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
        if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields) && count($comment_options) > 1) {
            $this->addElement('Select', 'auth_comment', array(
                'label' => 'Comment Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may comment on this project?"),
                'multiOptions' => $comment_options,
                'value' => key($comment_options),
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($comment_options) == 1) {
            $this->addElement('Hidden', 'auth_comment', array('value' => key($comment_options),
                'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Hidden', 'auth_comment', array('value' => "registered",
                'order' => ++$orderPrivacyHiddenFields));
        }

        $availableLabels = array(
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'leader' => 'Owner and Admins Only'
        );

        if (Engine_Api::_()->hasModuleBootstrap('advancedactivity')) {
            $post_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecrowdfunding_project', $user, "auth_post");
            $post_options = array_intersect_key($availableLabels, array_flip($post_options));

            if (!empty($createFormFields) && in_array('postPrivacy', $createFormFields) && count($post_options) > 1) {
                $this->addElement('Select', 'auth_post', array(
                    'label' => 'Posting Updates Privacy',
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may post updates on this project?"),
                    'multiOptions' => $post_options,
                    'value' => key($post_options),
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
                $this->auth_post->getDecorator('Description')->setOption('placement', 'append');
            } elseif (count($post_options) == 1) {
                $this->addElement('Hidden', 'auth_post', array('value' => key($post_options),
                    'order' => ++$orderPrivacyHiddenFields));
            } else {
                $this->addElement('Hidden', 'auth_post', array(
                    'value' => 'registered',
                    'order' => ++$orderPrivacyHiddenFields
                ));
            }
        }

        $topic_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecrowdfunding_project', $user, "auth_topic");
        $topic_options = array_intersect_key($availableLabels, array_flip($topic_options));
        if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields) && count($topic_options) > 1) {
            $this->addElement('Select', 'auth_topic', array(
                'label' => 'Discussion Topic Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may post discussion topics for this project?"),
                'multiOptions' => $topic_options,
                'value' => 'registered',
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->auth_topic->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($topic_options) == 1) {
            $this->addElement('Hidden', 'auth_topic', array('value' => key($topic_options),
                'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Hidden', 'auth_topic', array(
                'value' => 'registered',
                'order' => ++$orderPrivacyHiddenFields
            ));
        }

        //NETWORK BASE PAGE VIEW PRIVACY
        if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
            // Make Network List
            $table = Engine_Api::_()->getDbtable('networks', 'network');
            $select = $table->select()
                ->from($table->info('name'), array('network_id', 'title'))
                ->order('title');
            $result = $table->fetchAll($select);

            $networksOptions = array('0' => 'Everyone');
            foreach ($result as $value) {
                $networksOptions[$value->network_id] = $value->title;
            }

            if (count($networksOptions) > 0) {
                $this->addElement('Multiselect', 'networks_privacy', array(
                    'label' => 'Networks Selection',
                    'description' => Zend_Registry::get('Zend_Translate')->_("Select the networks, members of which should be able to see your project. (Press Ctrl and click to select multiple networks. You can also choose to make your project viewable to everyone.)"),
//            'attribs' => array('style' => 'max-height:150px; '),
                    'multiOptions' => $networksOptions,
                    'value' => array(0),
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
            } else {

            }
        }


        // Enable join and approve
        $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.invite.option', 1);
        $memberApproval = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.approval.option', 1);
        if(!empty($memberInvite) ) {
            $this->addElement('Radio', 'member_invite', array(
                'label' => 'Invite member',
                'multiOptions' => array(
                    '0' => 'Yes, members can invite other people.',
                    '1' => 'No, only project admins can invite other people.',
                ),
                'value' => '1',
                'attribs' => array('class' => 'sp_quick_advanced')
            ));
        }
        if(!empty($memberApproval) ) {
            $this->addElement('Radio', 'member_approval', array(
                'label' => 'Approve members?',
                'description' => ' When people try to join this project, should they be allowed ' .
                    'to join immediately, or should they be forced to wait for approval?',
                'multiOptions' => array(
                    '0' => 'New members must be approved.',
                    '1' => 'New members can join immediately.',
                ),
                'value' => '1',
            ));
        }

        if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.show.browse', 1)) {
            $this->addElement('Checkbox', 'search', array(
                'label' => "Show this project on browse page and in various blocks.",
                'value' => 1,
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
        }


        $this->addElement('Hidden', 'return_url', array(
            'order' => 10000000000
        ));

        // notification to users
        $this->addElement('MultiCheckbox', 'notify_project_comment', array(
            'label' => 'Send notification when someone comments',
            'description' => ' will send email and notification to specified users only',
            'multiOptions' => array(
                '0' => 'Project Owner',
                '1' => 'Project Members',
                '2' => 'Project Followers',
                '3' => 'Project Admins',
                '4' => 'Organisation Owner',
                '5' => 'Organisation Member',
                '6' => 'Organisation Followers',
                '7' => 'Organisation Admins',
            ),
            'required' => true,
        ));

        // notification to users
        $this->addElement('MultiCheckbox', 'notify_project_donate', array(
            'label' => 'Send notification when someone funds',
            'description' => ' will send email and notification to specified users only',
            'multiOptions' => array(
                '0' => 'Project Owner',
                '1' => 'Project Members',
                '2' => 'Project Followers',
                '3' => 'Project Admins',
                '4' => 'Organisation Owner',
                '5' => 'Organisation Member',
                '6' => 'Organisation Followers',
                '7' => 'Organisation Admins',
            ),
            'required' => true,
        ));
        $this->addElement('Checkbox', 'is_user_followed_after_comment_yn', array(
            'label' => "Make users as followed when someone donated.",
            'value' => 0,
            'attribs' => array('class' => 'se_quick_advanced'),
        ));
        $this->addElement('Checkbox', 'is_user_followed_after_donate_yn', array(
            'label' => "Make users as followed when someone comments.",
            'value' => 0,
            'attribs' => array('class' => 'se_quick_advanced'),
        ));
//        $this->addElement('Text', 'payment_action_label', array(
//            'label' => "Display name on the Call to Action Button. (e.g. Donate, Contribute)",
//            'allowEmpty' => false,
//            'required' => true,
//            'filters' => array(
//                'StripTags',
//                new Engine_Filter_Censor(),
//            )));

        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), "sitecrowdfunding_general", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
