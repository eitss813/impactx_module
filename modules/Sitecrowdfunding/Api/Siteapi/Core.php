<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Feed.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Api_Siteapi_Core extends Core_Api_Abstract {

    protected $_defaultProfileId;
    protected $_parentTypeItem;
    protected $_searchFormSettings;

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

    public function getForm($defaultProfileId = null, $parentTypeItem = null, $item = null) {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        if (!empty($defaultProfileId))
            $this->setDefaultProfileId($defaultProfileId);
        if (!empty($parentTypeItem))
            $this->setParentTypeItem($parentTypeItem);
        //PACKAGE ID
        $package_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('package_id', null);

        //edit field work
        //ALLOW THE ADMINS TO EDIT ALL INFORMATION OF PROJECT
        $edit_flag = 0;
        if (!empty($item)) {
            $viewerIsAdmin = $user->isAdminOnly();
            $backerCount = $project->backer_count;
            if (!empty($project->backer_count) && !$viewerIsAdmin) {
                $edit_flag = 1;
            }
        }
        //..............................
        if ($item) {
            $package_id = $item->package_id;
        }
        $parentType = "";
        $shortTypeName = "";
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //PACKAGE BASED CHECKS
        $hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
        $projectTypeOptions = array();
        $isAllowedLifeTimeProject = false;
        if ($hasPackageEnable) {
            $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $package_id);
            $isAllowedLifeTimeProject = $package->lifetime;
        } else {
            $isAllowedLifeTimeProject = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "lifetime");
        }

        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'escrow') {
            $isAllowedLifeTimeProject = false;
        }



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

        $projectForm[] = array(
            "name" => "title",
            "type" => "Text",
            "hasValidator" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Project Title")
        );

        if (!empty($createFormFields) && in_array('tags', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tags', 1)) {
            $projectForm[] = array(
                "name" => "tags",
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Tags (Keywords)"),
                "description" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Separate tags with commas."),
            );
        }



        $categories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
        if (count($categories) != 0) {
            $categories_prepared[""] = "";
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);

                if (isset($category->profile_type) && !empty($category->profile_type))
                    $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

                //subcategory..............
                $subCategories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCategories($category->category_id, array('category_id', 'category_name'));
                foreach ($subCategories as $subcategory) {
                    $subsubCategories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCategories($subcategory->category_id, array('category_id', 'category_name'));
                    if (isset($subcategory->profile_type) && !empty($subcategory->profile_type))
                        $categoryProfileTypeMapping[$subcategory->category_id] = $subcategory->profile_type;
                    $getsubCategories[$subcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subcategory->category_name);
                    foreach ($subsubCategories as $subsubcategory) {
                        $subsubCategoryOption[$subsubcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubcategory->category_name);

                        if (isset($subsubcategory->profile_type) && !empty($subsubcategory->profile_type))
                            $categoryProfileTypeMapping[$subsubcategory->category_id] = $subsubcategory->profile_type;
                    }
                    if (isset($subsubCategoryOption) && count($subsubCategoryOption) > 0) {
                        $form["subcategory_id_" . $subcategory->category_id][] = array(
                            'type' => 'Select',
                            'name' => 'subsubcategory_id',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                            'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubCategoryOption)
                        );
                    }
                    $subsubCategoryOption = array();
                }

                if (isset($getsubCategories) && count($getsubCategories) > 0) {
                    $subcategoriesForm = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        "hasSubForm" => true,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getsubCategories),
                    );
                }
                $getsubCategories = array();

                if (isset($subcategoriesForm) && !empty($subcategoriesForm) && count($subcategoriesForm) > 0) {
                    $form["category_id_" . $category->category_id][] = $subcategoriesForm;
                }
                $subcategoriesForm = array();
            }

            $projectForm[] = array(
                "name" => "category_id",
                "type" => "Select",
                "hasSubForm" => true,
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Category"),
                'multiOptions' => $categories_prepared,
            );


            if (!empty($item)) {
                $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCategories($item->category_id);
                $getSubCategories[0] = "";
                foreach ($subCategoriesObj as $subcategory) {
                    $getSubCategories[$subcategory->category_id] = $subcategory->category_name;
                }

                if (isset($getSubCategories) && !empty($getSubCategories) && count($getSubCategories) > 1) {
                    $projectForm[] = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        "hasSubForm" => true,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SubCategory'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getSubCategories),
                    );
                }
                $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCategories($item->subcategory_id);

                $getSubSubCategories[0] = "";
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $getSubSubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                }
                if (isset($getSubSubCategories) && !empty($getSubSubCategories) && count($getSubSubCategories) > 1) {
                    $projectForm[] = array(
                        'type' => 'Select',
                        'name' => 'subsubcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getSubSubCategories),
                    );
                }
            }
        }



        if (!empty($createFormFields) && in_array('location', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $projectForm[] = array(
                "name" => "location",
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Enter a location"),
            );
        }

        $projectForm[] = array(
            "name" => "description",
            "type" => "Textarea",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Description"),
        );

        if ($isAllowedLifeTimeProject) {
            $projectForm[] = array(
                "name" => "lifetime",
                "type" => "Radio",
                "disabled" => $edit_flag,
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Project Duration"),
                'multiOptions' => array(
                    1 => 'Upto 5 years',
                    0 => '1-90 days',
                ),
                "value" => 0
            );
        }

        $projectForm[] = array(
            "name" => "starttime",
            "type" => "Date",
            "disabled" => $edit_flag,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Project Start Date"),
        );

        $projectForm[] = array(
            "name" => "endtime",
            "type" => "Date",
            "disabled" => $edit_flag,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Project End Date"),
        );


        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $projectForm[] = array(
            "name" => "goal_amount",
            "type" => "Text",
            "disabled" => $edit_flag,
            "label" => sprintf(Engine_Api::_()->getApi('Core', 'siteapi')->translate('Funding Goal (%s)'), $currencyName),
            "hasValidator" => true
        );


        if (!empty($createFormFields) && in_array('photo', $createFormFields) && empty($item)) {
            $projectForm[] = array(
                "name" => "photo",
                "type" => "File",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo'),
            );
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

        if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields)) {
            $projectForm[] = array(
                "name" => "auth_view",
                "type" => "Select",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("View Privacy"),
                "description" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Who may see this project?"),
                'multiOptions' => $view_options,
                'value' => key($view_options),
            );
        }


        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecrowdfunding_project', $user, "auth_comment");
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
        if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields)) {
            $projectForm[] = array(
                "name" => "auth_comment",
                "type" => "Select",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Comment Privacy"),
                "description" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Who may comment on this project?"),
                'multiOptions' => $comment_options,
                'value' => key($comment_options),
            );
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

                $projectForm[] = array(
                    "name" => "auth_post",
                    "type" => "Select",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Posting Updates Privacy"),
                    "description" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Who may post updates on this project?"),
                    'multiOptions' => $post_options,
                    'value' => key($post_options),
                );
            }
        }

        $topic_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecrowdfunding_project', $user, "auth_topic");
        $topic_options = array_intersect_key($availableLabels, array_flip($topic_options));
        if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields)) {
            $projectForm[] = array(
                "name" => "auth_topic",
                "type" => "Select",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Discussion Topic Privacy"),
                "description" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Who may post discussion topics for this project?"),
                'multiOptions' => $topic_options,
                'value' => 'registered',
            );
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

                $projectForm[] = array(
                    "name" => "networks_privacy",
                    "type" => "Multiselect",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Networks Selection"),
                    'multiOptions' => $networksOptions,
                    'value' => array(0),
                );
            }
        }

        $projectForm[] = array(
            "name" => "state",
            "type" => "Select",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Networks Selection"),
            'multiOptions' => array("published" => "Published", "submitted"=> "Submit for approval", "draft" => "Saved As Draft"),
            'description' => 'If this entry is published, it cannot be switched back to draft mode.',
        );


        if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.show.browse', 1)) {
            $form['state_published'][] = array(
                "name" => "search",
                "type" => "Checkbox",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Show this project on browse page and in various blocks."),
                'value' => 1,
            );
            if ($item->state == 'published') {
                $projectForm[] = array(
                    "name" => "search",
                    "type" => "Checkbox",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Show this project on browse page and in various blocks."),
                    'value' => 1,
                );
                $response['state'] = array(
                    "child" => 'state_published',
                );
            }
        }

        $projectForm[] = array(
            "name" => "submit",
            "type" => "submit",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Create"),
            'value' => 1,
        );

        $response['form'] = $projectForm;
        $response['fields'] = $form;


        // Get profile fields
        $profileFields = $this->_getProfileTypes(array(), 'sitecrowdfunding_project');
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $createFormFields = $this->_getProfileFields(array(), 'sitecrowdfunding_project');

        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $response['fields'][$key] = $createFormFields[$value];
                }
            }
        }

        return $response;
    }

    private function _getProfileTypes($profileFields = array(), $table = null) {

        if (empty($table))
            return;
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop($table);

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams($table);
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
// Make exist profile fields array.         
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }

        return $profileFields;
    }

    private function _getProfileFields($fieldsForm = array(), $table = null) {
        if (empty($table))
            return;

        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($table);
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();

                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


// Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

// In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
// If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

// Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

// Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
// Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'video')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {

                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[$option_id][] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
// Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

// Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

// Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'video')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[$option_id][] = $fieldForm;
                        }
                    }
                }
            }
        }

        return $fieldsForm;
    }

    public function getInformation($subject, $table) {

        $profileFields = $this->_getProfileTypes(array(), $table);
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        $information = $this->getProfileInfo($subject, $table);

        return $information;
    }

    // Get the Profile Fields Information, which will show on profile page.
    public function getProfileInfo($subject, $table, $setKeyAsResponse = false) {
// Getting the default Profile Type id.

        $getFieldId = $this->getDefaultProfileTypeId($subject);

// Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);
        $fieldValues = array();
// In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {

// Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields(array(), $table);
            foreach ($_getProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

// In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;

                            if (isset($tempValue) && !empty($tempValue))
                                $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields(array(), $table);

            foreach ($_getProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {

                    $key = $value['name'];
                    $label = $value['label'];

                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {
                            if (!empty($fieldRow->value)) {
                                $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                                if (isset($fieldRow->value) && !empty($fieldRow->value))
                                    $fieldValues[$tempKey] = $fieldRow->value;
                            }
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }

    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }

        if (empty($getFieldId)) {
            return;
        }
    }

    public function createRewardForm($rewardcount = null, $reward = null) {
        $user = Engine_Api::_()->user()->getViewer();
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $rewardForm = array();
        $rewardForm[] = array(
            "name" => "title",
            "type" => "Text",
            "hasValidator" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Reward Title")
        );
        $rewardForm[] = array(
            "name" => "pledge_amount",
            "type" => "Text",
            "hasValidator" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Minimum Back Amount ($currencyName)")
        );

        $rewardForm[] = array(
            "name" => "description",
            "type" => "Textarea",
            "hasValidator" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Description")
        );

        if (empty($reward))
            $rewardForm[] = array(
                "name" => "photo",
                "type" => "File",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Main Photo")
            );
        $rewardForm[] = array(
            "name" => "delivery_date",
            "type" => "Date",
            "hasValidator" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Estimated Delivery")
        );

        $rewardForm[] = array(
            "name" => "shipping_method",
            "type" => "Select",
            "hasValidator" => true,
            "hasSubForm" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Shipping Detail"),
            'multiOptions' => array('' => 'Select an option', '1' => 'No shipping involved', '2' => 'Ships only to certain countries', '3' => 'Ships anywhere in the world'),
        );

        //edti form work.......................
        $shippingLocations = array();
        if (!empty($reward)) {
            if ($reward->shipping_method > 1) {
                $shippingLocations = $reward->findShippingLocations($reward->project_id);
                $countriesArray = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAllRegionsCountryArray();
                $locale = Engine_Api::_()->getApi('Core', 'siteapi')->getLocal();
                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                $location = array();
                foreach ($countriesArray as $country) {
                    $key = $country['country'];
                    if (!array_key_exists($key, $countries)) {
                        continue;
                    }
                    $location[$key] = $countries[$key];
                }
                if (count($shippingLocations) > 0 && !empty($location))
                    $rewardForm[] = array(
                        "name" => "country",
                        "type" => "MultiCheckbox",
                        "uppend" => 0,
                        "hasSubForm" => true,
                        "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Select Location"),
                        'multiOptions' => $location,
                    );
            }
            $selectedCountries = array();
            $countriesAmount = array();
            $i = 7;
            foreach ($shippingLocations as $location) {
                if (empty($location['region_id']) && $reward->shipping_method == 3) {
                    $rewardForm[] = array(
                        "name" => "rest_world",
                        "type" => "Text",
                        "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Shipping Charge"),
                    );
                    $response['shipping_method'] = array(
                        "child" => 'shipping_method_3',
                    );
                } elseif (!empty($location['region_id'])) {
                    $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $location['region_id']);
                    if (!empty($region)) {
                        $rewardForm[] = array(
                            "name" => "shhipping_charge_" . $region->country,
                            "type" => "Text",
                            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Shipping Chagre in " . $region->country_name),
                        );
                    }
                    $response['shipping_method'] = array(
                        "child" => 'shipping_method_2',
                    );

                    $multiChild['country_' . $region->country] = $i;
                    $i++;
                    $response['country'] = array(
                        "parent"=>'shipping_method',
                        "child" => '',
                        "multiChild" => $multiChild
                    );
                }
            }
        }
        //.....................................

        $rewardForm[] = array(
            "name" => "limit",
            "hasSubForm" => true,
            "type" => "Checkbox",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Limit quantity"),
        );
        if (!empty($reward) && !empty($reward->quantity)) {
            $rewardForm[] = array(
                "name" => "quantity",
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Quantity"),
            );
            $response['limit'] = array(
                "child" => 'limit_1',
            );
        }

        $rewardForm[] = array(
            "name" => "submit",
            "type" => "submit",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Create Reward"),
        );

        $response['form'] = $rewardForm;

        $response['fields'] = $this->subForm();
        return $response;
    }

    function subForm($country = array()) {
        $finalsubform = array();
        $shippingMethodform=array();
        $countriesArray = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAllRegionsCountryArray();
        $locale = Engine_Api::_()->getApi('Core', 'siteapi')->getLocal();
        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
        $location = array();
        $countryForm = array();
        foreach ($countriesArray as $country) {
            $key = $country['country'];
            if (!array_key_exists($key, $countries)) {
                continue;
            }
            $location[$key] = $countries[$key];
            $finalsubform['country_' . $key][] = array(
                "name" => "shhipping_charge_" . $key,
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Shipping Chagre in " . $location[$key]),
            );
        }

        $subForm = array();
        $subForm[] = array(
            "name" => "quantity",
            "type" => "Text",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Quantity"),
        );
        
        if(count($location) > 0)
        $shippingMethodform[] = array(
            "name" => "country",
            "type" => "MultiCheckbox",
            "uppend" => 0,
            "hasSubForm" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Select Location"),
            'multiOptions' => $location,
        );

        $shippingMethodform3[] = array(
            "name" => "rest_world",
            "type" => "Text",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Shipping Charge"),
        );

        if(count($location) > 0)
        $shippingMethodform3[] = array(
            "name" => "country",
            "uppend" => 0,
            "type" => "MultiCheckbox",
            "hasSubForm" => true,
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Select Location"),
            'multiOptions' => $location,
        );
        
        if(!empty($shippingMethodform))
        $finalsubform['shipping_method_2'] = $shippingMethodform;
        
        $finalsubform['shipping_method_3'] = $shippingMethodform3;

        $finalsubform['limit_1'] = $subForm;
        return $finalsubform;
    }

    public function getSearchForm() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->_searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitecrowdfunding_project');
        $searchForm = array();
        if (!empty($this->_searchFormSettings['search']) && !empty($this->_searchFormSettings['search']['display'])) {
            $searchForm[] = array(
                "name" => 'search',
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Name / Keyword'
                )
            );
        }


        $categories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
        if (count($categories) != 0) {
            $categories_prepared[""] = "";
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);

                if (isset($category->profile_type) && !empty($category->profile_type))
                    $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;

                //subcategory..............
                $subCategories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCategories($category->category_id, array('category_id', 'category_name'));
                foreach ($subCategories as $subcategory) {
                    $subsubCategories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCategories($subcategory->category_id, array('category_id', 'category_name'));
                    if (isset($subcategory->profile_type) && !empty($subcategory->profile_type))
                        $categoryProfileTypeMapping[$subcategory->category_id] = $subcategory->profile_type;
                    $getsubCategories[$subcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subcategory->category_name);
                    foreach ($subsubCategories as $subsubcategory) {
                        $subsubCategoryOption[$subsubcategory->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubcategory->category_name);

                        if (isset($subsubcategory->profile_type) && !empty($subsubcategory->profile_type))
                            $categoryProfileTypeMapping[$subsubcategory->category_id] = $subsubcategory->profile_type;
                    }
                    if (isset($subsubCategoryOption) && count($subsubCategoryOption) > 1) {
                        $form["subcategory_id_" . $subcategory->category_id][] = array(
                            'type' => 'Select',
                            'name' => 'subsubcategory_id',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                            'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($subsubCategoryOption)
                        );
                    }
                    $subsubCategoryOption = array();
                }

                if (isset($getsubCategories) && count($getsubCategories) > 1) {
                    $subcategoriesForm = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                        'multiOptions' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($getsubCategories),
                    );
                }
                $getsubCategories = array();

                if (isset($subcategoriesForm) && !empty($subcategoriesForm) && count($subcategoriesForm) > 0) {
                    $form["category_id_" . $category->category_id][] = $subcategoriesForm;
                }
                $subcategoriesForm = array();
            }

            $searchForm[] = array(
                "name" => "category_id",
                "type" => "Select",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Category"),
                'multiOptions' => $categories_prepared,
            );
        }


        if (!empty($this->_searchFormSettings['orderby']) && !empty($this->_searchFormSettings['orderby']['display'])) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'startDate' => 'Recently Started',
                'modifiedDate' => 'Recently Updated',
                'backerCount' => 'Most Popular',
                'likeCount' => 'Most Liked',
                'title' => "Alphabetical (A-Z)",
                'titleReverse' => 'Alphabetical (Z-A)'
            );
            $searchForm[] = array(
                "name" => 'orderby',
                "type" => "Select",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => $multiOPtionsOrderBy,
            );
        }

        if (!empty($this->_searchFormSettings['location']) && !empty($this->_searchFormSettings['location']['display']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $searchForm[] = array(
                "name" => 'location',
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter a location'),
            );
        }

        if (!empty($this->_searchFormSettings['proximity']) && !empty($this->_searchFormSettings['proximity']['display'])) {
            $flage = $settings->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
            if ($flage) {
                $locationLable = "Within Kilometers";
                $locationOption = array(
                    '0' => '',
                    '1' => '1 Kilometer',
                    '2' => '2 Kilometers',
                    '5' => '5 Kilometers',
                    '10' => '10 Kilometers',
                    '20' => '20 Kilometers',
                    '50' => '50 Kilometers',
                    '100' => '100 Kilometers',
                    '250' => '250 Kilometers',
                    '500' => '500 Kilometers',
                    '750' => '750 Kilometers',
                    '1000' => '1000 Kilometers',
                );
            } else {
                $locationLable = "Within Miles";
                $locationOption = array(
                    '0' => '',
                    '1' => '1 Mile',
                    '2' => '2 Miles',
                    '5' => '5 Miles',
                    '10' => '10 Miles',
                    '20' => '20 Miles',
                    '50' => '50 Miles',
                    '100' => '100 Miles',
                    '250' => '250 Miles',
                    '500' => '500 Miles',
                    '750' => '750 Miles',
                    '1000' => '1000 Miles',
                );
            }
            $label = empty($this->_widgetSettings['whatWhereWithinmile']) ? $locationLable : $locationLable;
            $searchForm[] = array(
                "name" => 'locationmiles',
                "type" => "Select",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate($label),
                'multiOptions' => $locationOption,
                'value' => 0,
            );
        }

        if (!empty($this->_searchFormSettings['street']) && !empty($this->_searchFormSettings['street']['display'])) {
            $searchForm[] = array(
                "name" => 'project_street',
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Street'),
            );
        }

        if (!empty($this->_searchFormSettings['city']) && !empty($this->_searchFormSettings['city']['display'])) {
            $searchForm[] = array(
                "name" => 'project_city',
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
            );
        }

        if (!empty($this->_searchFormSettings['state']) && !empty($this->_searchFormSettings['state']['display'])) {
            $searchForm[] = array(
                "name" => 'project_state',
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('State'),
            );
        }

        if (!empty($this->_searchFormSettings['country']) && !empty($this->_searchFormSettings['country']['display'])) {
            $searchForm[] = array(
                "name" => 'project_country',
                "type" => "Text",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
            );
        }



        if (!empty($this->_searchFormSettings['view']) && !empty($this->_searchFormSettings['view']['display'])) {
            $viewer = Engine_Api::_()->user()->getViewer();
            if (!empty($viewer)) {
                $viewer_id = $viewer->getIdentity();
            } else {
                $viewer_id = 0;
            }
            if (!empty($viewer_id)) {
                $show_multiOptions = array();
                $show_multiOptions["0"] = 'Everyone\'s Projects';
                $show_multiOptions["1"] = 'Only My Friends\' Projects';
                $value_deault = 0;
                $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.network', 0);
                if (empty($enableNetwork)) {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

                    if (!empty($viewerNetwork) || Engine_Api::_()->sitecrowdfunding()->projectBaseNetworkEnable()) {
                        $show_multiOptions["3"] = 'Only My Networks';
                    }
                }

                $searchForm[] = array(
                    "name" => 'view_view',
                    "type" => "Select",
                    "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View'
                    ),
                    'multiOptions' => $show_multiOptions,
                    'value' => 0,
                );
            }
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => 'Submit'
        );

        if (isset($searchForm) && !empty($searchForm))
            $responseForm['form'] = $searchForm;

        if (isset($form) && !empty($form))
            $responseForm['fields'] = $form;

        // Get profile fields
        $profileFields = $this->_getProfileTypes(array(), 'sitecrowdfunding_project');
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $createFormFields = $this->_getProfileFields(array(), 'sitecrowdfunding_project');

        if (is_array($createFormFields) && is_array($categoryProfileTypeMapping)) {
            foreach ($categoryProfileTypeMapping as $key => $value) {
                if (isset($createFormFields[$value]) && !empty($createFormFields[$value])) {
                    $response['fields'][$key] = $createFormFields[$value];
                }
            }
        }

        return $responseForm;
    }

    public function getSearchProfileFields() {
        $this->_validateSearchProfileFields = true;
        $this->_profileFieldsArray = $this->getProfileTypes();

        $_getProfileFields = $this->_getProfileFields(array(), 'sitecrowdfunding_project');
        return $_getProfileFields;
    }

    public function getPayPalForm($mainForm = array(), $enable_method, $isPayPal = 1) {
        $payPalForm = $mainForm;
        if (empty($isPayPal)) {
            $name = "paypaladptiveHeading";
            $label = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Paypal Adaptive");
        } else {
            $name = "payPalHeading";
            $label = Engine_Api::_()->getApi('Core', 'siteapi')->translate("PayPal");
        }
        $payPalForm[] = array(
            'type' => 'Dummy',
            "subType" => 'payment_method',
            'name' => $name,
            "hasSubForm" => true,
            "isActive" => $enable_method,
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($label),
        );
        $payPalForm[] = array(
            "name" => "email",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Paypal Email'),
            "hasValidator" => true
        );
        $payPalForm[] = array(
            "name" => "username",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('API Username'),
            "hasValidator" => true
        );

        $payPalForm[] = array(
            "name" => "password",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('API Password'),
            "hasValidator" => true
        );

        $payPalForm[] = array(
            "name" => "signature",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('API Signature'),
            "hasValidator" => true
        );

        if (empty($isPayPal)) {
            $payPalForm[] = array(
                "name" => "adaptivepaypalEnable",
                "type" => "Checkbox",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("PayPal Adaptive"),
            );
        } else {
            $payPalForm[] = array(
                "name" => "paypalEnable",
                "type" => "Checkbox",
                "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("PayPal"),
            );
        }

        return $payPalForm;
    }

    public function getMangoPayForm($mainForm = array(), $enable_method) {
        $mangopayForm = $mainForm;
        $mangopayForm[] = array(
            'type' => 'Dummy',
            "subType" => 'payment_method',
            'name' => 'mangopayHeading',
            "hasSubForm" => true,
            "isActive" => $enable_method,
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Mango Pay'),
        );

        $mangopayForm[] = array(
            "name" => "dummy_account",
            "type" => "Label",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Personal Details for MangoPay Account'),
        );
        $mangopayForm[] = array(
            "name" => "first_name",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('First Name'),
            "hasValidator" => true
        );
        $mangopayForm[] = array(
            "name" => "last_name",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Last Name'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "mango_pay_email",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "birthday",
            "type" => "Date",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Birthday'),
            "hasValidator" => true
        );

        $countryCodes = array(
            "AD" => "AD", "AE" => "AE", "AF" => "AF", "AG" => "AG", "AI" => "AI",
            "AL" => "AL", "AM" => "AM", "AO" => "AO", "AQ" => "AQ", "AR" => "AR",
            "AS" => "AS", "AT" => "AT", "AU" => "AU", "AW" => "AW", "AX" => "AX",
            "AZ" => "AZ", "BA" => "BA", "BB" => "BB", "BD" => "BD", "BE" => "BE",
            "BF" => "BF", "BG" => "BG", "BH" => "BH", "BI" => "BI", "BJ" => "BJ",
            "BL" => "BL", "BM" => "BM", "BN" => "BN", "BO" => "BO", "BQ" => "BQ",
            "BR" => "BR", "BS" => "BS", "BT" => "BT", "BV" => "BV", "BW" => "BW",
            "BY" => "BY", "BZ" => "BZ", "CA" => "CA", "CC" => "CC", "CD" => "CD",
            "CF" => "CF", "CG" => "CG", "CH" => "CH", "CI" => "CI", "CK" => "CK",
            "CL" => "CL", "CM" => "CM", "CN" => "CN", "CO" => "CO", "CR" => "CR",
            "CU" => "CU", "CV" => "CV", "CW" => "CW", "CX" => "CX", "CY" => "CY",
            "CZ" => "CZ", "DE" => "DE", "DJ" => "DJ", "DK" => "DK", "DM" => "DM",
            "DO" => "DO", "DZ" => "DZ", "EC" => "EC", "EE" => "EE", "EG" => "EG",
            "EH" => "EH", "ER" => "ER", "ES" => "ES", "ET" => "ET", "FI" => "FI",
            "FJ" => "FJ", "FK" => "FK", "FM" => "FM", "FO" => "FO", "FR" => "FR",
            "GA" => "GA", "GB" => "GB", "GD" => "GD", "GE" => "GE", "GF" => "GF",
            "GG" => "GG", "GH" => "GH", "GI" => "GI", "GL" => "GL", "GM" => "GM",
            "GN" => "GN", "GP" => "GP", "GQ" => "GQ", "GR" => "GR", "GS" => "GS",
            "GT" => "GT", "GU" => "GU", "GW" => "GW", "GY" => "GY", "HK" => "HK",
            "HM" => "HM", "HN" => "HN", "HR" => "HR", "HT" => "HT", "HU" => "HU",
            "ID" => "ID", "IE" => "IE", "IL" => "IL", "IM" => "IM", "IN" => "IN",
            "IO" => "IO", "IQ" => "IQ", "IR" => "IR", "IS" => "IS", "IT" => "IT",
            "JE" => "JE", "JM" => "JM", "JO" => "JO", "JP" => "JP", "KE" => "KE",
            "KG" => "KG", "KH" => "KH", "KI" => "KI", "KM" => "KM", "KN" => "KN",
            "KP" => "KP", "KR" => "KR", "KW" => "KW", "KY" => "KY", "KZ" => "KZ",
            "LA" => "LA", "LB" => "LB", "LC" => "LC", "LI" => "LI", "LK" => "LK",
            "LR" => "LR", "LS" => "LS", "LT" => "LT", "LU" => "LU", "LV" => "LV",
            "LY" => "LY", "MA" => "MA", "MC" => "MC", "MD" => "MD", "ME" => "ME",
            "MF" => "MF", "MG" => "MG", "MH" => "MH", "MK" => "MK", "ML" => "ML",
            "MM" => "MM", "MN" => "MN", "MO" => "MO", "MP" => "MP", "MQ" => "MQ",
            "MR" => "MR", "MS" => "MS", "MT" => "MT", "MU" => "MU", "MV" => "MV",
            "MW" => "MW", "MX" => "MX", "MY" => "MY", "MZ" => "MZ", "NA" => "NA",
            "NC" => "NC", "NE" => "NE", "NF" => "NF", "NG" => "NG", "NI" => "NI",
            "NL" => "NL", "NO" => "NO", "NP" => "NP", "NR" => "NR", "NU" => "NU",
            "NZ" => "NZ", "OM" => "OM", "PA" => "PA", "PE" => "PE", "PF" => "PF",
            "PG" => "PG", "PH" => "PH", "PK" => "PK", "PL" => "PL", "PM" => "PM",
            "PN" => "PN", "PR" => "PR", "PS" => "PS", "PT" => "PT", "PW" => "PW",
            "PY" => "PY", "QA" => "QA", "RE" => "RE", "RO" => "RO", "RS" => "RS",
            "RU" => "RU", "RW" => "RW", "SA" => "SA", "SB" => "SB", "SC" => "SC",
            "SD" => "SD", "SE" => "SE", "SG" => "SG", "SH" => "SH", "SI" => "SI",
            "SJ" => "SJ", "SK" => "SK", "SL" => "SL", "SM" => "SM", "SN" => "SN",
            "SO" => "SO", "SR" => "SR", "SS" => "SS", "ST" => "ST", "SV" => "SV",
            "SX" => "SX", "SY" => "SY", "SZ" => "SZ", "TC" => "TC", "TD" => "TD",
            "TF" => "TF", "TG" => "TG", "TH" => "TH", "TJ" => "TJ", "TK" => "TK",
            "TL" => "TL", "TM" => "TM", "TN" => "TN", "TO" => "TO", "TR" => "TR",
            "TT" => "TT", "TV" => "TV", "TW" => "TW", "TZ" => "TZ", "UA" => "UA",
            "UG" => "UG", "UM" => "UM", "US" => "US", "UY" => "UY", "UZ" => "UZ",
            "VA" => "VA", "VC" => "VC", "VE" => "VE", "VG" => "VG", "VI" => "VI",
            "VN" => "VN", "VU" => "VU", "WF" => "WF", "WS" => "WS", "YE" => "YE",
            "YT" => "YT", "ZA" => "ZA", "ZM" => "ZM", "ZW" => "ZW"
        );

        $accountTypes = array('IBAN' => 'IBAN', 'GB' => 'GB', 'US' => 'US', 'CA' => 'CA', 'OTHER' => 'OTHER');

        $mangopayForm[] = array(
            "name" => "nationality",
            "type" => "Select",
            'label' => 'Nationality',
            'multiOptions' => $countryCodes,
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "residence",
            "type" => "Select",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country Residence'),
            'multiOptions' => $countryCodes,
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "dummy_account",
            "type" => "Label",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Personal Details for MangoPay Account'),
        );

        $mangopayForm[] = array(
            "name" => "account_type",
            "type" => "Select",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Bank account type'),
            "multiOptions" => $accountTypes,
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "owner_name",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner name'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "owner_address",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner address Line1'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "owner_address2",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner address Line2'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "city",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "region",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Region'),
            "hasValidator" => true
        );
        $mangopayForm[] = array(
            "name" => "postal_code",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Postal Code'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "country",
            "type" => "Select",
            'multiOptions' => $countryCodes,
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
            "hasValidator" => true
        );
        $accountTypes = array('IBAN' => 'IBAN', 'GB' => 'GB', 'US' => 'US', 'CA' => 'CA', 'OTHER' => 'OTHER');
        $subform['account_type_IBAN'][] = array(
            "name" => "iban",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('IBAN'),
            "hasValidator" => true
        );


        $subform['account_type_IBAN'][] = array(
            "name" => "bic",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('BIC'),
            "hasValidator" => true
        );


        $subform['account_type_GB'][] = array(
            "name" => "sort_code",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sort code'),
            "hasValidator" => true
        );


        $subform['account_type_GB'][] = array(
            "name" => "account_number",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Account number'),
            "hasValidator" => true
        );

        $depositAccountTypeArr = array('CHECKING' => 'CHECKING', 'SAVINGS' => 'SAVINGS');
        $subform['account_type_US'][] = array(
            "name" => "deposit_account_type",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Deposit account type'),
            "hasValidator" => true,
            'multiOptions' => $depositAccountTypeArr,
        );

        $subform['account_type_US'][] = array(
            "name" => "aba",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('ABA'),
            "hasValidator" => true
        );

        $subform['account_type_US'][] = array(
            "name" => "us_account_number",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Account number'),
            "hasValidator" => true
        );

        $subform['account_type_CA'][] = array(
            "name" => "branch_code",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Branch code'),
            "hasValidator" => true
        );

        $subform['account_type_CA'][] = array(
            "name" => "bank_name",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Bank name'),
            "hasValidator" => true
        );

        $subform['account_type_CA'][] = array(
            "name" => "institution_number",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Institution number'),
            "hasValidator" => true
        );

        $subform['account_type_CA'][] = array(
            "name" => "ca_account_number",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Account number'),
            "hasValidator" => true
        );

        $subform['account_type_OTHER'][] = array(
            "name" => "other_bic",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('BIC'),
            "hasValidator" => true
        );

        $subform['account_type_OTHER'][] = array(
            "name" => "other_account_number",
            "type" => "Text",
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Account number'),
            "hasValidator" => true
        );

        $mangopayForm[] = array(
            "name" => "mangopayEnable",
            "type" => "Checkbox",
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("MangoPay"),
        );
        return array(
            "mainForm" => $mangopayForm,
            "subForm" => $subform
        );
    }

    public function shippingAdressForm($data = array()) {
        $shippingAdress = array();
        $rewardModel = Engine_Api::_()->getItem('sitecrowdfunding_reward', $data['reward_id']);
        foreach ($rewardModel->getAllCountries() as $country) {
            $name = empty($country->country) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Shipping Charge') : $country->country_name;
            $regionId = empty($country->region_id) ? 0 : $country->region_id;
            $regions[$regionId] = $name;
        }
        if (count($regions) > 0) {
            $shippingAdress[] = array(
                "name" => "shipping_adress",
                "type" => "Dummy",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Shipping Address'),
            );

            $shippingAdress[] = array(
                "name" => "region_id",
                "type" => "Select",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
                'isDisable' =>true,
                'multiOptions' => $regions,
                'value' => $data['region_id'],
                'hasValidator ' => true,
            );

            $shippingAdress[] = array(
                "name" => "address1",
                "type" => "Text",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Address1'),
                'hasValidator ' => true,
            );

            $shippingAdress[] = array(
                "name" => "address2",
                "type" => "Text",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Address2'),
                'hasValidator ' => true,
            );

            $shippingAdress[] = array(
                "name" => "city",
                "type" => "Text",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
                'hasValidator ' => true,
            );

            $shippingAdress[] = array(
                "name" => "postal_code",
                "type" => "Text",
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Postal Code'),
                'hasValidator ' => true,
            );
        }
        return $shippingAdress;
    }

    public function favourite($resource_id, $resource_type, $isFavourite) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            return 0;
        $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
        $object = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (isset($isFavourite) && empty($isFavourite)) {
            $favouriteTable->delete(array('resource_type = ?' => $resource_type, 'resource_id = ?' => $resource_id, 'poster_id= ?' => $viewer_id));
        } else {

            $fName = $favouriteTable->info('name');
            $select = $favouriteTable->select()
                    ->where('resource_type = ?', $resource_type)
                    ->where('resource_id = ?', $resource_id)
                    ->where('poster_id = ?', $viewer_id)
                    ->limit(1);

            $row = $favouriteTable->fetchAll($select);
            if (count($row) == 0) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $favourite = $favouriteTable->createRow();
                    $favourite->resource_type = $resource_type;
                    $favourite->resource_id = $resource_id;
                    $favourite->poster_type = $viewer->getType();
                    $favourite->poster_id = $viewer_id;
                    $favourite->creation_date = new Zend_Db_Expr('NOW()');

                    $favourite->save();
                    $db->commit();
                } catch (Exception $ex) {
                    $db->rollback();
                }
            }
        }
    }

    public function isFavourite($resource_id, $resource_type, $poster_id) {
        if (empty($resource_id) && empty($poster_id))
            return;

        $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
        $fName = $favouriteTable->info('name');
        $select = $favouriteTable->select()
                ->where('resource_id = ?', $resource_id)
                ->where('poster_id = ?', $poster_id);
        if (!empty($resource_type))
            $select->where('resource_type = ?', $resource_type);
        $select->limit(1);

        $row = $favouriteTable->fetchAll($select);

        if (count($row) == 0)
            return 0;
        else
            return 1;
    }

}

?>
