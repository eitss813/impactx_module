<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Api_Siteapi_Core extends User_Api_Core {
    /*
     * Flag variable for search form settings.
     */

    protected $_searchForm;

    /*
     * Flag variable of profile fields
     */
    private $_profileFieldsArray = array();

    /*
     * Get the Browse Members search page form
     * 
     * @return array
     */
    public function getSearchForm() {
        $response = array();
        $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
        $widgetSettings = $this->_getBrowseMembersWidgetSettings('sitemember.search-sitemember');
        $widgetSettings = !empty($widgetSettings) ? $widgetSettings : array("whatWhereWithinmile" => 0, "advancedSearch" => 0, "locationDetection" => 0);

        // Set default value of "whatWhereWithinmile"
        $response['whatWhereWithinmile'] = 0;
        if (isset($widgetSettings['whatWhereWithinmile']))
            $response['whatWhereWithinmile'] = (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember')) ? 1 : 0;

        // Form Elements: Profile Types
        $this->_profileFieldsArray = $getProfileTypes = $this->_getProfileTypes();
        if (!empty($getProfileTypes)) {
            $response['form'][] = array(
                'type' => 'Select',
                'name' => 'profile_type',
                'label' => $this->_translate('What'),
                'multiOptions' => $getProfileTypes,
            );
        }

        // Form Elements: Search
        $row = $this->_searchForm->getFieldsOptions('sitemember', 'search');
        if (!empty($row) && !empty($row->display)) {
            $response['form'][] = array(
                'type' => 'Text',
                'name' => 'search',
                'label' => empty($widgetSettings['whatWhereWithinmile']) ? $this->_translate('Name / Keyword') : $this->_translate('Who')
            );
        }

        // Form Elements: Location
        $row = $this->_searchForm->getFieldsOptions('sitemember', 'location');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1) && !empty($row) && !empty($row->display)) {
            $response['form'][] = array(
                'type' => 'Text',
                'name' => 'location',
                'label' => empty($widgetSettings['whatWhereWithinmile']) ? $this->_translate('Location') : $this->_translate('Where')
            );

            // Form Elements: Proximate
            $row = $this->_searchForm->getFieldsOptions('sitemember', 'proximity');
            if (!empty($row) && !empty($row->display)) {
                $response['form'][] = $this->_getProximate();
            }
        }

//        $response['form'][] = array(
//            'type' => 'Hidden',
//            'name' => 'advanced_search',
//            'label' => $this->_translate('Advanced Search'),
//            'value' => $response['whatWhereWithinmile']
//        );
        // Form Elements: Show
        $row = $this->_searchForm->getFieldsOptions('sitemember', 'show');
        if (!empty($row) && !empty($row->display)) {
            $showMultiOptions = array();
            $showMultiOptions["1"] = 'All Members';
            $showMultiOptions["2"] = 'Only My Friends';
            $showMultiOptions["4"] = "Members I Like";
            $showMultiOptions["5"] = "Only Featured";
            $showMultiOptions["6"] = "Only Sponsored";
            $showMultiOptions["7"] = "Both Featured & Sponsored";
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify'))
                $showMultiOptions["8"] = "Members I've Verified";

            $networkShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.network.show', 0);
            if (empty($networkShow)) {
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
                if (!empty($viewerNetwork))
                    $showMultiOptions["3"] = 'Only My Networks';
            }

            $response['form'][] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => $this->_translate('Show'),
                'multiOptions' => $showMultiOptions,
            );
        }

        // Form Elements: Networks
        $networkshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.network.show', 0);
        if (empty($networkshow)) {
            $row = $this->_searchForm->getFieldsOptions('sitemember', 'network_id');
            if (!empty($row) && !empty($row->display)) {
                $networks = Engine_Api::_()->getDbTable('networks', 'network');
                $networksname = $networks->info('name');
                $select = $networks->select()->from($networksname);
                $result = $networks->fetchAll($select);
                if (count($result) != 0) {
                    foreach ($result as $results) {
                        $networkTitle[$results->network_id] = $results->title;
                    }

                    $response['form'][] = array(
                        'type' => 'Select',
                        'name' => 'network_id',
                        'label' => $this->_translate('Networks'),
                        'multiOptions' => $networkTitle,
                    );
                }
            }
        }

        // Form Elements: Order By
        $row = $this->_searchForm->getFieldsOptions('sitemember', 'orderby');
        if (!empty($row) && !empty($row->display)) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Most Recent',
                'view_count' => 'Most Viewed',
                'like_count' => 'Most Liked',
                'member_count' => 'Most Popular',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1))
                $multiOPtionsOrderBy['distance'] = "Distance";

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify'))
                $multiOPtionsOrderBy['verify_count'] = 'Most Verified';

            $response['form'][] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => $this->_translate('Browse By'),
                'multiOptions' => $multiOPtionsOrderBy,
            );
        }

        // Form Elements: Street
        $rowStreet = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitemember', 'street');
        if (!empty($rowStreet) && !empty($rowStreet->display)) {
            $response['form'][] = array(
                'type' => 'Text',
                'name' => 'sitemember_street',
                'label' => $this->_translate('Street')
            );
        }

        // Form Elements: City
        $rowCity = $this->_searchForm->getFieldsOptions('sitemember', 'city');
        if (!empty($rowCity) && !empty($rowCity->display)) {
            $response['form'][] = array(
                'type' => 'Text',
                'name' => 'sitemember_city',
                'label' => $this->_translate('City')
            );
        }

        // Form Elements: State
        $rowState = $this->_searchForm->getFieldsOptions('sitemember', 'state');
        if (!empty($rowState) && !empty($rowState->display)) {
            $response['form'][] = array(
                'type' => 'Text',
                'name' => 'sitemember_state',
                'label' => $this->_translate('State')
            );
        }

        // Form Elements: Country
        $rowCountry = $this->_searchForm->getFieldsOptions('sitemember', 'country');
        if (!empty($rowCountry) && !empty($rowCountry->display)) {
            $response['form'][] = array(
                'type' => 'Text',
                'name' => 'sitemember_country',
                'label' => $this->_translate('Country')
            );
        }

        // Form Elements: Is Online
        $row = $this->_searchForm->getFieldsOptions('sitemember', 'is_online');
        if (!empty($row) && !empty($row->display)) {
            $response['form'][] = array(
                'type' => 'Checkbox',
                'name' => 'is_online',
                'label' => $this->_translate('Only Online Members')
            );
        }

        // Form Elements: Has Photo
        $row = $this->_searchForm->getFieldsOptions('sitemember', 'has_photo');
        if (!empty($row) && !empty($row->display)) {
            $response['form'][] = array(
                'type' => 'Checkbox',
                'name' => 'has_photo',
                'label' => $this->_translate('Only Members With Photos')
            );
        }

        $response['form'][] = array(
            'type' => 'Submit',
            'name' => 'done',
            'label' => $this->_translate('Search')
        );

        $response['fields'] = $this->_getProfileFields(array(), true);
        return $response;
    }

    /*
     * Get user profile type
     * 
     * @param array profileFields
     * @return array
     */
    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('siteevent_event');
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

    /*
     * Translate language
     * 
     * @param string str
     * @return string or array
     */
    protected function _translate($str) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($str);
    }

    /*
     * Get proximate for advanced search form
     */
    protected function _getProximate() {
        $responce = array(
            'type' => 'Select',
            'name' => 'locationmiles'
        );
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proximity.search.kilometer', 0)) {
            $responce['label'] = $this->_translate("Within Kilometers");
            $responce['multiOptions'] = array(
                '0' => '',
                '1' => $this->_translate('1 Kilometer'),
                '2' => $this->_translate('2 Kilometers'),
                '5' => $this->_translate('5 Kilometers'),
                '10' => $this->_translate('10 Kilometers'),
                '20' => $this->_translate('20 Kilometers'),
                '50' => $this->_translate('50 Kilometers'),
                '100' => $this->_translate('100 Kilometers'),
                '250' => $this->_translate('250 Kilometers'),
                '500' => $this->_translate('500 Kilometers'),
                '750' => $this->_translate('750 Kilometers'),
                '1000' => $this->_translate('1000 Kilometers'),
            );
        } else {
            $responce['label'] = $this->_translate("Within Miles");
            $responce['multiOptions'] = array(
                '0' => '',
                '1' => $this->_translate('1 Mile'),
                '2' => $this->_translate('2 Miles'),
                '5' => $this->_translate('5 Miles'),
                '10' => $this->_translate('10 Miles'),
                '20' => $this->_translate('20 Miles'),
                '50' => $this->_translate('50 Miles'),
                '100' => $this->_translate('100 Miles'),
                '250' => $this->_translate('250 Miles'),
                '500' => $this->_translate('500 Miles'),
                '750' => $this->_translate('750 Miles'),
                '1000' => $this->_translate('1000 Miles'),
            );
        }

        return $responce;
    }

    /*
     * Get the array of Profile Types
     * 
     * @return array
     */
    protected function _getProfileTypes() {
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('user', 'profile_type');
        if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
            return;

        $profileTypeField = $profileTypeFields['profile_type'];
        $options = $profileTypeField->getOptions();

        foreach ($options as $option) {
            $multiOptions[$option->option_id] = $option->label;
        }

        return $multiOptions;
    }

    /*
     * Set the fields of Advanced Member plugin
     * 
     * @param $user object
     * @return array
     */
    public function addAdvancedMemberSettings($user) {
        $response = array();
        $getBrowseMembersWidgetSettings = $this->_getBrowseMembersWidgetSettings();
        $getBrowseMembersWidgetSettings = !empty($getBrowseMembersWidgetSettings) ? $getBrowseMembersWidgetSettings : array("memberInfo" => array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField", "distance", "age"));
        if (
                !empty($getBrowseMembersWidgetSettings) &&
                isset($getBrowseMembersWidgetSettings['memberInfo']) &&
                ($memberInfo = $getBrowseMembersWidgetSettings['memberInfo']) &&
                !empty($memberInfo)
        ) {
            if (in_array('memberStatus', $memberInfo))
                $response['memberStatus'] = (int) Engine_Api::_()->sitemember()->isOnline($user->user_id);

            if (in_array('mutualFriend', $memberInfo) && ($user->user_id != Engine_Api::_()->user()->getViewer()->getIdentity()))
                $response['mutualFriendCount'] = Engine_Api::_()->seaocore()->getMutualFriend($user->user_id)->getTotalItemCount();

            if (in_array('age', $memberInfo)) {
                $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
                if (isset($fieldsByAlias['birthdate']) && !empty($fieldsByAlias['birthdate'])) {
                    $optionId = $fieldsByAlias['birthdate']->getValue($user);
                    if ($optionId) {
                        $response['age'] = @floor((time() - strtotime($optionId->value)) / 31556926);
                    }
                }
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1) && !empty($user->location) && in_array('location', $memberInfo)) {
                if (!in_array('directionLink', $memberInfo))
                    $response['member_location']['label'] = $user->location;

                if (in_array('distance', $memberInfo) && isset($user->distance)) {
                    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proximity.search.kilometer')) {
                        $response['member_location']['distance'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("approximately %s miles", round($user->distance, 2));
                    } else {
                        $distance = (1 / 0.621371192) * $user->distance;
                        $response['member_location']['distance'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("approximately %s kilometers", round($distance, 2));
                    }
                }
            }

            // Set the profile field of user
            if (in_array('profileField', $memberInfo)) {
                $response['profileField'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getProfileInfo($user);
            }
        }

        return $response;
    }

    /*
     * Get browse members widget settings
     * 
     * @return array OR false
     */
    private function _getBrowseMembersWidgetSettings($widgetName = 'sitemember.browse-members-sitemember') {
        $getEnabledSettings = array();
        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $browseMemberPageId = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', 'sitemember_location_userby-locations')
                        ->limit(1)
                        ->query()->fetchColumn();

        if (!empty($browseMemberPageId)) {
            $select = new Zend_Db_Select($db);
            $getEnabledSettings = $select
                            ->from('engine4_core_content', 'params')
                            ->where('page_id = ?', $browseMemberPageId)
                            ->where('name = ?', $widgetName)
                            ->limit(1)
                            ->query()->fetchColumn();

            if (!empty($getEnabledSettings))
                $getEnabledSettings = Zend_Json::decode($getEnabledSettings);
        }

        return $getEnabledSettings;
    }

    /*
     * Get profile fields
     * 
     * @param array fieldsForm
     * @return array
     */
    private function _getProfileFields($fieldsForm = array(), $searchForm=false) {
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {
            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('user');
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

                    if (
                            (!isset($meta->search) || empty($meta->search))
//                            ||
//                            (!isset($meta->show) || empty($meta->show))
                    )
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
                            if (isset($meta->alias) && !empty($meta->alias))
                                $fieldForm['name'] = $key . '_alias_' . $meta->alias;
                            else
                                $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // It should be "date" OR "datetime"
                            if (($fieldForm['type'] == 'date') || ($fieldForm['type'] == 'Date'))
                                $fieldForm['format'] = 'date';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required) && empty($searchForm))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'siteevent')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            if (isset($meta->alias) && !empty($meta->alias))
                                $fieldForm['name'] = $key . '_alias_' . $meta->alias;
                            else
                                $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';
                            // It should be "date" OR "datetime"
                            if (($fieldForm['type'] == 'date') || ($fieldForm['type'] == 'Date'))
                                $fieldForm['format'] = 'date';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            if (isset($meta) && isset($meta->type) && $meta->type == 'relationship_status') {
                                $fieldForm['multiOptions'] = array(
                                    'friendship' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friendship'),
                                    'dating' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Dating'),
                                    'relationship' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('A Relationship'),
                                    'networking' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Networking'),
                                );
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required) && empty($searchForm))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'siteevent')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    public function getEditAdressForm($resource) {

        if ($resource->getType() == 'user') {

            $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($resource);
            $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
            $profilemapsTablename = $profilemapsTable->info('name');

            $select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
            if (empty($aliasValues['profile_type'])) {
                $select->where($profilemapsTablename . '.option_id = ?', 1);
            } else {
                $select->where($profilemapsTablename . '.option_id = ?', $aliasValues['profile_type']);
            }

            $option_id = $select->query()->fetchColumn();
            $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
            $metaTableName = $metaTable->info('name');

            $select = $metaTable->select()
                    ->from($metaTableName, array('type', 'label'))
                    ->where($metaTableName . '.field_id = ?', $option_id);
            $valuesResultsLocation = $metaTable->fetchAll($select)->toarray();
            if (isset($valuesResultsLocation[0]['type']) && $valuesResultsLocation[0]['type'] == 'country') {
                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                $country[0] = "";
                foreach ($countries as $keys => $countrie) {
                    $country[$keys] = $countrie;
                }
                $response[] = array(
                    'type' => 'Select',
                    'name' => 'location',
                    'label' => $valuesResultsLocation[0]['label'],
                    'multiOptions' => $country
                );
            } elseif (isset($valuesResultsLocation[0]['type']) && !empty($valuesResultsLocation) && ($valuesResultsLocation[0]['type'] == 'location' || $valuesResultsLocation[0]['type'] == 'city')) {
                $response[] = array(
                    'type' => 'Text',
                    'name' => 'location',
                    'label' => $valuesResultsLocation[0]['label'],
                );
            } else {
                $response[] = array(
                    'type' => 'Text',
                    'name' => 'location',
                    'label' => 'Location',
                );
            }
        } else {
            $response[] = array(
                'type' => 'Text',
                'name' => 'location',
                'label' => 'Location',
            );
        }
        $response[] = array(
            'type' => 'Select',
            'name' => 'location_privacy',
            'label' => 'Privacy',
            'multiOptions' => Fields_Api_Core::getFieldPrivacyOptions(),
        );

        $response[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => 'Submit',
        );

        return $response;
    }

    public function editLocation() {

        $response[] = array(
            'type' => 'Text',
            'name' => 'formatted_address',
            'label' => 'Formatted Address',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'longitude',
            'label' => 'Longitude',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'latitude',
            'label' => 'Latitude',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'address',
            'label' => 'Street Address',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'city',
            'label' => 'City',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'zipcode',
            'label' => 'Zipcode',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'state',
            'label' => 'State',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'country',
            'label' => 'Country',
        );
        $response[] = array(
            'type' => 'Text',
            'name' => 'submit',
            'label' => 'Save Changes',
        );
        return $response;
    }

}
