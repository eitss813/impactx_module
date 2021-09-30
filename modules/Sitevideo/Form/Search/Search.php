<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Search_Search extends Sitevideo_Form_Searchfields {

    protected $_fieldType = 'sitevideo_channel';
    protected $_searchForm;
    protected $_searchFormSettings;
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
        $this
                ->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => 'sitechannels_browse_filters field_search_criteria',
                    'method' => 'GET'
        ));

        parent::init();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators();

        $this->getChannelTypeElement();

        //GET SEARCH FORM SETTINGS
        $this->_searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitevideo_channel');

        $this->getAdditionalOptionsElement();

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();

        switch ($action) {
            case 'pinboard':
                $this->setAction($view->url(array('action' => 'pinboard'), "sitevideo_channel_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesitevideos_criteria');
                break;
            default:
                $this->setAction($view->url(array('action' => 'browse'), 'sitevideo_general', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitevideos_criteria');
                break;
        }
    }

    public function getChannelTypeElement() {

        $multiOptions = array('' => ' ');
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
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

        $i = 99980;
        $this->addElement('Hidden', 'page', array(
            'order' => $i++,
        ));
        $this->addElement('Hidden', 'tag', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'tag_id', array(
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

        $this->addElement('Hidden', 'viewFormat', array(
            'order' => $i++,
        ));
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!empty($this->_searchFormSettings['search']) && !empty($this->_searchFormSettings['search']['display'])) {
            $this->addElement('Text', 'search', array(
                'label' => 'Name / Keyword',
                'order' => $this->_searchFormSettings['search']['order'],
                'autocomplete' => 'off',
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label', array('tag' => 'div')),
                    array('HtmlTag2', array('tag' => 'li'))
                ),
            ));

            if (isset($_GET['search'])) {
                $this->search->setValue($_GET['search']);
            } elseif (isset($_GET['titleAjax'])) {
                $this->search->setValue($_GET['titleAjax']);
            }
        }

        if (!empty($this->_searchFormSettings['view']) && !empty($this->_searchFormSettings['view']['display'])) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (!empty($viewer_id)) {
                $show_multiOptions = array();
                $show_multiOptions["0"] = 'Everyone\'s Channels';
                $show_multiOptions["1"] = 'Only My Friends\' Channels';
                $value_deault = 0;
                $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.network', 0);
                if (empty($enableNetwork)) {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
                    if (!empty($viewerNetwork) || Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
                        $show_multiOptions["3"] = 'Only My Networks';
                        $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.default.show', 0);

                        if (!isset($_GET['view_view']) && !empty($browseDefaulNetwork)) {
                            $value_deault = 3;
                        } elseif (isset($_GET['view_view'])) {
                            $value_deault = $_GET['view_view'];
                        }
                    }
                }
                $this->addElement('Select', 'view_view', array(
                    'label' => 'View',
                    'order' => $this->_searchFormSettings['view']['order'],
                    'multiOptions' => $show_multiOptions,
                    'onchange' => 'searchSitevideos();',
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                    'value' => $value_deault,
                ));
            }
        }


        if (!empty($this->_searchFormSettings['orderby']) && !empty($this->_searchFormSettings['orderby']['display'])) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Recently Created',
                'modified_date' => 'Recently Updated',
                'view_count' => 'Most Popular',
                'like_count' => 'Most Liked',
                'comment_count' => 'Most Commented',
                'videos_count' => 'Most Videos',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );
            //GET API

            $enableRating = $settings->getSetting('sitevideo.rating', 1);

            if ($enableRating) {
                $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
            }
            $this->addElement('Select', 'orderby', array(
                'label' => 'Browse By',
                'order' => $this->_searchFormSettings['orderby']['order'],
                'multiOptions' => $multiOPtionsOrderBy,
                'onchange' => 'searchSitevideos();',
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label', array('tag' => 'div')),
                    array('HtmlTag2', array('tag' => 'li'))
                ),
            ));
        }


        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            if (!empty($this->_searchFormSettings['category_id']) && !empty($this->_searchFormSettings['category_id']['display'])) {
                $translate = Zend_Registry::get('Zend_Translate');
                if (!$this->_widgetSettings['showAllCategories']) {
                    $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'limit' => 0, 'orderBy' => 'category_name', 'havingChannels' => 1));
                } else {
                    $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
                }

                if (count($categories) != 0) {
                    $categories_prepared[0] = "";
                    foreach ($categories as $category) {
                        $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                    }

                    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                        $onChangeEvent = "showFields(this.value, 1); addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);";
                        $categoryFiles = 'application/modules/Sitevideo/views/scripts/_subCategory.tpl';
                    } else {
                        $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
                        $categoryFiles = 'application/modules/Sitevideo/views/sitemobile/scripts/_subCategory.tpl';
                    }
                    $this->addElement('Select', 'category_id', array(
                        'label' => 'Category',
                        'order' => $this->_searchFormSettings['category_id']['order'],
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
                        'order' => $this->_searchFormSettings['category_id']['order'] + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => $this->_widgetSettings['showAllCategories'],
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));

                    $this->addElement('Select', 'subsubcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $this->_searchFormSettings['category_id']['order'] + 2,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => $this->_widgetSettings['showAllCategories'],
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));
                }
            }
        }
        $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'onclick' => 'searchSitevideos();',
            'ignore' => true,
            'order' => 999999999,
            'decorators' => array(
                'ViewHelper',
                //array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
        ));
    }

}
