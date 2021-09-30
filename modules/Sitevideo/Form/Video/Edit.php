<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Video_Edit extends Engine_Form {

    protected $_defaultProfileId;
    protected $_item;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init() {

        $hiddenOrderCount=45637;
        $parent_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_type', null);
        $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
        $this->setTitle('Edit Video')
                ->setAttrib('name', 'video_edit');
        $user = Engine_Api::_()->user()->getViewer();

        $this->addElement('Text', 'title', array(
            'label' => 'Video Title',
            'required' => true,
            'notEmpty' => true,
            'validators' => array(
                'NotEmpty',
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '100')),
            )
        ));
        $this->title->getValidator('NotEmpty')->setMessage("Please specify an video title");
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            // prepare categories
            $categories_prepared = array();
            $categories = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
            if (count($categories) != 0) {
                $categories_prepared[""] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name;
                }
            }

            $onChangeEvent = "showFields(this.value, 1); subcategories(this.value, '', '');";
            $categoryFiles = 'application/modules/Sitevideo/views/scripts/_formVideoSubcategory.tpl';
            // category field
            $this->addElement('Select', 'category_id', array(
                'label' => 'Category',
                'multiOptions' => $categories_prepared,
                'onchange' => $onChangeEvent,
                'required' => true,
                'notEmpty' => true,
            ));

            $this->addElement('Select', 'subcategory_id', array(
                'RegisterInArrayValidator' => false,
                'allowEmpty' => true,
                'required' => false,
                'decorators' => array(array('ViewScript', array('viewScript' => $categoryFiles, 'class' => 'form element')))
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
        $defaultProfileId = "0_0_" . $this->getDefaultProfileId();

        $customFields = new Sitevideo_Form_Custom_Standard(array(
            'item' => $this->_item,
            'decorators' => array(
                'FormElements'
        )));

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
        $isEnableTinymce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.description.tinymceditor', 0);
        if ($isEnableTinymce) {
            $editorOptions = array(
                'html' => (bool) true,
                'mode' => "exact",
                'forced_root_block' => false,
                'force_p_newlines' => false,
                'elements' => 'description',
                'plugins' => array(
                    'table', 'fullscreen', 'preview', 'paste',
                    'code', 'textcolor', 'link'
                ),
                'toolbar1' => array(
                    'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
                    'link', 'fullscreen',
                    'preview'
            ));

            $this->addElement('TinyMce', 'description', array(
                'label' => 'Video Description',
                'editorOptions' => $editorOptions,
                'onkeyup' => 'setHiddenVideoDescription();',
                'filters' => array(
                    new Engine_Filter_Censor(),
                ),
            ));
        } else {
            $this->addElement('Textarea', 'description', array(
                'label' => 'Video Description',
                'rows' => 2,
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_Censor(),
                )
            ));
        }
        if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.photo.field.enabled', 1) ) {
          $this->addElement('File', 'art', array(
          'label' => 'Video Artwork',
        ));
        $this->art->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        }
        // init tag
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1)) {
            $this->addElement('Text', 'tags', array(
                'label' => 'Tags (Keywords)',
                'autocomplete' => 'off',
                'filters' => array(
                    new Engine_Filter_Censor(),
                    'StripTags',
                ),
                'description' => 'Separate tags with commas.'
            ));
            $this->tags->getDecorator("Description")->setOption("placement", "append");
        }

        //NETWORK BASE CHANNEL
        if (Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
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
                $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.networkprofile.privacy', 0);
                if ($viewPricavyEnable) {
                    $desc = 'Select the networks, members of which should be able to see your video. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                } else {
                    $desc = 'Select the networks, members of which should be able to see your Channel in browse and search videos. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                }
                $this->addElement('Multiselect', 'networks_privacy', array(
                    'label' => 'Networks Selection',
                    'description' => $desc,
                    'multiOptions' => $networksOptions,
                    'value' => array(0)
                ));
            }
        }

        // Privacy
        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );  
        $explodeParentType = explode('_', $parent_type);
        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $shortTypeName = ucfirst($explodeParentType[1]);
                $viewOptions['parent_member'] = $shortTypeName . ' Members Only';
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'parent_member' => $shortTypeName . ' Members Only',
                    'owner' => 'Just Me'
                ); 
            } elseif (($parent_type == 'siteevent_event') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) { 
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'parent_member' => 'Event Guests Only', 
                    'owner' => 'Just Me'
                );

            }
        }


        // View
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (empty($viewOptions)) {
            $viewOptions = $availableLabels;
        }

        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('order'=>$hiddenOrderCount++,'value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this video?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowPasswordProtected = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'video_password_protected');
        if ($allowPasswordProtected) {
            // Element: password
            $this->addElement('Text', 'password', array(
                'label' => 'Password',
                'description' => "Protect this video with a password. [Leave it blank if you do not want password protection on this video.]",
                'required' => false,
                'allowEmpty' => true,
                'filters' => array(
                    new Engine_Filter_Censor(),
                    'StripTags',
                ),
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(6, 32)),
                )
            ));
            $this->password->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        }

        // Privacy
        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );  
        $explodeParentType = explode('_', $parent_type);
        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $shortTypeName = ucfirst($explodeParentType[1]);
                $commentOptions['parent_member'] = $shortTypeName . ' Members Only';
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'parent_member' => $shortTypeName . ' Members Only',
                    'owner' => 'Just Me'
                ); 
            } elseif (($parent_type == 'siteevent_event') && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) { 
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'parent_member' => 'Event Guests Only', 
                    'owner' => 'Just Me'
                );

            }
        }

        // Comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        if (empty($commentOptions)) {
            $commentOptions = $availableLabels;
        }

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('order'=>$hiddenOrderCount++,'value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this video?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }


        $this->addElement('Checkbox', 'search', array(
            'label' => "Show this video in search results",
        ));

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_video_general', true),
            'onclick' => '',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
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
