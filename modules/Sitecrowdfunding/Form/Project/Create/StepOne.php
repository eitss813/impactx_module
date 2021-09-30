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
class Sitecrowdfunding_Form_Project_Create_StepOne extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();

        $createFormFields = array(
            'viewPrivacy',
            'commentPrivacy',
            'postPrivacy',
            'discussionPrivacy',
            'search',
        );
        $user = Engine_Api::_()->user()->getViewer();

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Funding")))
            //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            //->setAttrib('name', 'sitecrowdfunding_funding')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfunding_project_new_step_one');
        //$this->setAttrib('class', 'global_form sitecrowdfunding_project_new_steps');

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        $this->addElement('Radio', 'is_fund_raisable', array(
            'label' => 'Do you seek funding for the project?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'required' => true,
            'allowEmpty' => false,
            'value' => 1,
            'onchange' => 'checkIsFundable(this.value);'
        ));

        $this->is_fund_raisable->getDecorator('Description')->setOption('placement', 'append');


        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Text', 'goal_amount', array(
            'label' => 'What are the total funds needed including the amount you are contributing?',
            //'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Funding Goal (%s)'), $currencyName),
            'description' => 'Please enter amount in USD($).',
            'attribs' => array('class' => 'se_quick_advanced'),
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Float', false)
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('Text', 'invested_amount', array(
            'label' => 'What is the amount that you are contributing to this project?',
            //'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Funding Goal (%s)'), $currencyName),
            'description' => 'Please enter amount in USD($).',
            'attribs' => array('class' => 'se_quick_advanced'),
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Float', false),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));




        $orderPrivacyHiddenFields = 786590;

        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'leader' => 'Owner and Admins Only'
        );

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

        if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.show.browse', 1)) {
            $this->addElement('Checkbox', 'search', array(
                'label' => "Show this project on browse page and in various blocks.",
                'value' => 1,
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
        }


        $this->addElement('Button', 'execute', array(
            'label' => 'Next',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

    }

}
