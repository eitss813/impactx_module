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
class Sitecrowdfunding_Form_Project_Create extends Engine_Form {

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
            $this->setTitle("Create a Project");
            $isAllowedLifeTimeProject = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "lifetime");
        }

        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if($paymentMethod == 'escrow'){
           $isAllowedLifeTimeProject = false; 
        }

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Start your new project below, then click 'Create' to publish the project.")))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'sitecrowdfundings_create')
                ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_create_form');
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
        $this->addElement('Text', 'title', array(
            'label' => "Project Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        )));

        if (!empty($createFormFields) && in_array('tags', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tags', 1)) {
            $this->addElement('Text', 'tags', array(
                'label' => 'Tags (Keywords)',
                'autocomplete' => 'off',
                'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
                'filters' => array(
                    new Engine_Filter_Censor(),
                ),
            ));
            $this->tags->getDecorator("Description")->setOption("placement", "append");
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $onChangeProject = "showFields($(this).value, 1); subcategories(this.value, '', '');";
            $categoryFiles = 'application/modules/Sitecrowdfunding/views/scripts/_formSubcategory.tpl';
        } else {

            $onChangeProject = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
            $categoryFiles = 'application/modules/Sitecrowdfunding/views/sitemobile/scripts/_subCategory.tpl';
        }
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;

        $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
        $translate = Zend_Registry::get('Zend_Translate');
        if (!$this->_item || (isset($this->_item->category_id) && empty($this->_item->category_id)) || ($this->_item)) {
            $categories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
            if (count($categories) != 0) {
                $categories_prepared[""] = "";
                foreach ($categories as $category) {
                        $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                }

                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'allowEmpty' => false,
                    //'required' => true,
                    'multiOptions' => $categories_prepared,
                    'onchange' => $onChangeProject,
                ));
                
                $this->addElement('Select', 'subcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    'allowEmpty' => true,
                    'required' => false,
                ));

                $this->addElement('Select', 'subsubcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    'allowEmpty' => true,
                    'required' => false,
                ));

                $this->addDisplayGroup(array(
                    'subcategory_id',
                    'subsubcategory_id',
                        ), 'Select', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => $categoryFiles,
                                'class' => 'form element')))
                ));
            }
        }

        if (!$this->_item) {
            $customFields = new Sitecrowdfunding_Form_Custom_Standard(array(
                'item' => 'sitecrowdfunding_project',
                'decorators' => array(
                    'FormElements'
            )));
        } else {
            $customFields = new Sitecrowdfunding_Form_Custom_Standard(array(
                'item' => $this->getItem(),
                'decorators' => array(
                    'FormElements'
            )));
        }

        $customFields->removeElement('submit');
        if ($customFields->getElement($defaultProfileId)) {
            $customFields->getElement($defaultProfileId)
                    ->clearValidators()
                    ->setRequired(false)
                    ->setAllowEmpty(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        if (!empty($createFormFields) && in_array('location', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $this->addElement('Text', 'location', array(
                'label' => 'Location',
                'description' => 'Eg: Fairview Park, Berkeley, CA',
                'placeholder' => $view->translate('Enter a location'),
                'autocomplete' => 'off',
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            )));
            $this->location->getDecorator('Description')->setOption('placement', 'append');
            $this->addElement('Hidden', 'locationParams', array('order' => 800000));


            include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';
        }


        //GET TINYMCE SETTINGS
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitecrowdfunding_general', true);
        }

        $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url);
        $editorOptions['height'] = '250px';
        $editorOptions['width'] = '80%';
        $editorOptions['toolbar1'] = array(
            'undo', 'redo', 'removeformat', 'pastetext', '|', 'code', 'image', 'jbimages', 'link', 'fullscreen',
            'preview'
        );

        $this->addElement('TinyMce', 'description', array(
            'label' => "Backstory",
            'description'=> 'Explain the history behind this project.  Explain who you are and how you got involved.  What have you achieved so far?  Who has helped?  What have been the biggest challenges?',
            'required' => true,
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));


        // Enable join and approve
        $createFormFields = array_merge($createFormFields, array('memberInvite'));
        $createFormFields = array_merge($createFormFields, array('memberApproval'));
        $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.invite.option', 1);
        $memberApproval = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.approval.option', 1);
        if(!empty($memberInvite) ) {
            if (in_array('memberInvite', $createFormFields)) {
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
        }
        if(!empty($memberApproval) ) {
            if (in_array('memberApproval', $createFormFields)) {
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
        }

//        if (!empty($createFormFields) && in_array('photo', $createFormFields)) {
//            $this->addElement('File', 'photo', array(
//                'label' => 'Main Photo',
//                    //'attribs' => array('class' => 'se_quick_advanced'),
//            ));
//            $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
//        }
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
        $this->addElement('Button', 'execute', array(
            'label' => 'Create',
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
