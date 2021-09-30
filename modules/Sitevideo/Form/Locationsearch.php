<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locationsearch.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Locationsearch extends Fields_Form_Search {

    protected $_searchForm;
    protected $_fieldType = 'video';
    protected $_hasMobileMode = false;
    protected $_widgetSettings;

    public function getWidgetSettings() {
        return $this->_widgetSettings;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function getHasMobileMode() {
        return $this->_hasMobileMode;
    }

    public function setHasMobileMode($flage) {
        $this->_hasMobileMode = $flage;
        return $this;
    }

    public function init() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();

        // Add custom elements
        $this->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => '',
                ))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');

        $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        $this->getMemberTypeElement();

        $this->getAdditionalOptionsElement();

        parent::init();

        $this->loadDefaultDecorators();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        if ($module == 'sitevideo' && $controller == 'video' && $action != 'map') {
            $this->setAction($view->url(array('action' => 'map'), 'sitevideo_video_general', true))->getDecorator('HtmlTag')->setOption('class', '');
        }
    }

    public function getMemberTypeElement() {

        $multiOptions = array('' => ' ');
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('video', 'profile_type');
        if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
            return;
        $profileTypeField = $profileTypeFields['profile_type'];

        $options = $profileTypeField->getOptions();

        foreach ($options as $option) {
            $multiOptions[$option->option_id] = $option->label;
        }

        $this->addElement('hidden', 'profile_type', array(
            'order' => -1000001,
            'class' =>
            'field_toggle' . ' ' .
            'parent_' . 0 . ' ' .
            'option_' . 0 . ' ' .
            'field_' . $profileTypeField->field_id . ' ',
            'onchange' => 'changeFields($(this));',
            'multiOptions' => $multiOptions,
        ));
        return $this->profile_type;
    }

    public function getAdditionalOptionsElement() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();

        //GET API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $subform = new Zend_Form_SubForm(array(
            'name' => 'extra',
            'order' => 19999999,
            'decorators' => array(
                'FormElements',
            )
        ));
        Engine_Form::enableForm($subform);
        $i = 99980;
        $order = 1;
        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'search');
        if (!empty($row) && !empty($row->display)) {
            $this->addElement('Text', 'search', array(
                'label' => 'What',
                'autocomplete' => 'off',
                'description' => '(Enter keywords or Video name)',
                'order' => $order,
                    //'decorators' => array(
//                    'ViewHelper',
//                    array('HtmlTag', array('tag' => 'div')),
//                    array('Label', array('tag' => 'div')),
//                    array('HtmlTag2', array('tag' => 'li'))
//                ),
            ));
            $this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

            if (isset($_GET['search'])) {
                $this->search->setValue($_GET['search']);
            } elseif (isset($_GET['titleAjax'])) {
                $this->search->setValue($_GET['titleAjax']);
            }
        }

        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'location');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            $this->addElement('Text', 'location', array(
                'label' => 'Where',
                'autocomplete' => 'off',
                'description' => Zend_Registry::get('Zend_Translate')->_('(address, city, state or country)'),
                'order' => ++$order,
                'onclick' => 'locationPage();'
            ));
            $this->location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();

            if (isset($myLocationDetails['location'])) {
                $this->location->setValue($myLocationDetails['location']);
            }

            if (isset($_POST['location'])) {
                if (($_POST['location'])) {
                    $myLocationDetails['location'] = $_POST['location'];
                    $myLocationDetails['latitude'] = $_POST['Latitude'];
                    $myLocationDetails['longitude'] = $_POST['Longitude'];
                    $myLocationDetails['locationmiles'] = $_POST['locationmiles'];
                }

                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
            }

            if (!isset($_POST['location']) && empty($this->_widgetSettings['locationDetection'])) {
                $this->location->setValue('');
            }

            $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'proximity');
            if (!empty($row) && !empty($row->display)) {
                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0);
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
                $this->addElement('Select', 'locationmiles', array(
                    'label' => $locationLable,
                    'multiOptions' => $locationOption,
                    'value' => '0',
                    'order' => ++$order,
                ));

                if (isset($myLocationDetails['locationmiles'])) {
                    $this->locationmiles->setValue($myLocationDetails['locationmiles']);
                }
            }
        }

        //Check for Location browse page.
        if ($module == 'list' && $controller == 'index' && $action != 'map') {
            $subform->addElement('Button', 'done', array(
                'label' => 'Search',
                'type' => 'submit',
                'ignore' => true,
            ));
            $this->addSubForm($subform, $subform->getName());
        } else {
            $subform->addElement('Button', 'done', array(
                'label' => 'Search',
                'type' => 'submit',
                'ignore' => true,
                'onclick' => 'return locationSearch();'
            ));
            $this->addSubForm($subform, $subform->getName());
        }

        // Element: cancel
        $this->addElement('Cancel', 'advances_search', array(
            'label' => 'Advanced search',
            'ignore' => true,
            'link' => true,
            'order' => ++$order,
            'onclick' => 'advancedSearchLists();',
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('hidden', 'advanced_search', array(
            'value' => 0
        ));

        $this->addDisplayGroup(array('advances_search', 'locationmiles', 'search', 'done', 'location'), 'grp3');
        $button_group = $this->getDisplayGroup('grp3');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'li', 'id' => 'group3', 'style' => 'width:100%;'))
        ));

        $group2 = array();

        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'location');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            $rowStreet = $this->_searchForm->getFieldsOptions('sitevideo_video', 'street');
            if (!empty($rowStreet) && !empty($rowStreet->display)) {
                $this->addElement('Text', 'video_street', array(
                    'label' => 'Street',
                    'autocomplete' => 'off',
                    'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                    'order' => ++$order,
                ));
                $group2[] = 'video_street';
            }

            $rowCity = $this->_searchForm->getFieldsOptions('sitevideo_video', 'city');
            if (!empty($rowCity) && !empty($rowCity->display)) {
                $this->addElement('Text', 'video_city', array(
                    'label' => 'City',
                    'placeholder' => '',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                    'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                ));
                $group2[] = 'video_city';
            }

            $rowState = $this->_searchForm->getFieldsOptions('sitevideo_video', 'state');
            if (!empty($rowState) && !empty($rowState->display)) {
                $this->addElement('Text', 'video_state', array(
                    'label' => 'State',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                    'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                ));
                $group2[] = 'video_state';
            }

            $rowCountry = $this->_searchForm->getFieldsOptions('sitevideo_video', 'country');
            if (!empty($rowCountry) && !empty($rowCountry->display)) {
                $this->addElement('Text', 'video_country', array(
                    'label' => 'Country',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                    'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                ));
                $group2[] = 'video_country';
            }
        }

        if (!empty($group2)) {
            $this->addDisplayGroup($group2, 'grp2');
            $button_group = $this->getDisplayGroup('grp2');
            $button_group->setDecorators(array(
                'FormElements',
                'Fieldset',
                array('HtmlTag', array('tag' => 'li', 'id' => 'group2', 'style' => 'width:100%;'))
            ));
        }
        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'state');
        if (!empty($row) && !empty($row->display)) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Recently Created',
                'modified_date' => 'Recently Updated',
                'view_count' => 'Most Popular',
                'like_count' => 'Most Liked',
                'comment_count' => 'Most Commented',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );
            //GET API

            $enableRating = $settings->getSetting('sitevideo.video.rating', 1);

            if ($enableRating) {
                $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
            }
            $this->addElement('Select', 'orderby', array(
                'label' => 'Browse By',
                'order' => ++$order,
                'multiOptions' => $multiOPtionsOrderBy,
                'onchange' => 'searchSitevideos();',
                    //'decorators' => array(
//                    'ViewHelper',
//                    array('HtmlTag', array('tag' => 'div')),
//                    array('Label', array('tag' => 'div')),
//                    array('HtmlTag2', array('tag' => 'li'))
//                ),
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'view');
        if (!empty($row) && !empty($row->display)) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (!empty($viewer_id)) {
                $show_multiOptions = array();
                $show_multiOptions["0"] = 'Everyone\'s Videos';
                $show_multiOptions["1"] = 'Only My Friends\' Videos';
                $value_deault = 0;
                $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0);
                if (empty($enableNetwork)) {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
                    if (!empty($viewerNetwork) || Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                        $show_multiOptions["3"] = 'Only My Networks';
                        $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.default.show', 0);

                        if (!isset($_GET['view_view']) && !empty($browseDefaulNetwork)) {
                            $value_deault = 3;
                        } elseif (isset($_GET['view_view'])) {
                            $value_deault = $_GET['view_view'];
                        }
                    }
                }
                $this->addElement('Select', 'view_view', array(
                    'label' => 'View',
                    'order' => ++$order,
                    'multiOptions' => $show_multiOptions,
                    'onchange' => 'searchSitevideos();',
                    //'decorators' => array(
//                        'ViewHelper',
//                        array('HtmlTag', array('tag' => 'div')),
//                        array('Label', array('tag' => 'div')),
//                        array('HtmlTag2', array('tag' => 'li'))
//                    ),
                    'value' => $value_deault,
                ));
            }
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'category');
            if (!empty($row) && !empty($row->display)) {
                $translate = Zend_Registry::get('Zend_Translate');
                if (!$this->_widgetSettings['showAllCategories']) {
                    $categories = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'limit' => 0, 'orderBy' => 'category_name', 'havingVideos' => 1));
                } else {
                    $categories = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
                }

                if (count($categories) != 0) {
                    $categories_prepared[0] = "";
                    foreach ($categories as $category) {
                        $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                    }

                    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                        $onChangeEvent = "showCustomFields(this.value, 1); addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);";
                        $categoryFiles = 'application/modules/Sitevideo/views/scripts/_videoSubCategory.tpl';
                    } else {
                        $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
                        $categoryFiles = 'application/modules/Sitevideo/views/sitemobile/scripts/_subCategory.tpl';
                    }
                    $this->addElement('Select', 'category_id', array(
                        'label' => 'Category',
                        'order' => $this->_searchForm['category_id']['order'],
                        'multiOptions' => $categories_prepared,
                        'onchange' => $onChangeEvent,
                        'decorators' => array(
                            'ViewHelper',
                            array('HtmlTag', array('tag' => 'div')),
                            array('Label', array('tag' => 'div')),
                            array('HtmlTag2', array('tag' => 'li'))),
                    ));

                    $this->addElement('Select', 'subcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $this->_searchForm['category_id']['order'] + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => $this->_widgetSettings['showAllCategories'],
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));

                    $this->addElement('Select', 'subsubcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $this->_searchForm['category_id']['order'] + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => $this->_widgetSettings['showAllCategories'],
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));
                }
            }
        }
        $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'content_type');
        if (!empty($row) && !empty($row->display)) {
            $contentTypes = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array("enabled" => 1));
            $contentTypeArray = array();
            if (!empty($contentTypes)) {
                $contentTypeArray[] = 'All';
                $moduleTitle = '';
                foreach ($contentTypes as $contentType) {
                    $contentTypeArray['user'] = Zend_Registry::get('Zend_Translate')->translate('Member Videos');
                    $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
                }
            }
            if (!empty($contentTypeArray)) {
                $this->addElement('Select', 'videoType', array(
                    'label' => "Video Type",
                    'multiOptions' => $contentTypeArray,
                    // 'onchange' => 'searchSitevideos();',
                    'order' => $row->order,
//                    'decorators' => array(
//                        'ViewHelper',
//                        array('Label', array('tag' => 'span')),
//                        array('HtmlTag', array('tag' => 'li'))
//                    ),
                    'value' => '',
                ));
            } else {
                $this->addElement('Hidden', 'videoType', array(
                    'label' => "Video Type",
                    'order' => $row->order,
                    'value' => 'All',
                ));
            }
        }
        $this->addElement('Hidden', 'page', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'tag', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'tag_id', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'start_date', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'end_date', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'categoryname', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'subcategoryname', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'subsubcategoryname', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'Latitude', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'Longitude', array(
            'order' => $i++,
        ));

        $this->addDisplayGroup(array('profile_type', 'orderby', 'view_view', 'category_id', 'subcategory_id', 'subsubcategory_id', 'videoType'), 'grp1');
        $button_group = $this->getDisplayGroup('grp1');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'li', 'id' => 'group1', 'style' => 'width:100%;'))
        ));

        return $this;
    }

}
