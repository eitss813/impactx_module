<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChannelLocationsearch.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_ChannelLocationsearch extends Fields_Form_Search {

    protected $_fieldType = 'sitevideo_channel';
    protected $_searchForm;
    protected $_widgetSettings;

    public function getWidgetSettings() {
        return $this->_widgetSettings;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function init() {

        //$this->_value = unserialize($this->_value);

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();

        // Add custom elements
        $this->setAttribs(array(
                    'id' => 'channel_filter_form',
                    'class' => '',
                ))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');

        $this->getChannelTypeElement();
        $this->getAdditionalOptionsElement();

        parent::init();

        $this->loadDefaultDecorators();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        if ($module == 'sitevideo' && $controller == 'index' && $action != 'map') {
            $this->setAction($view->url(array('action' => 'map'), 'sitevideo_general', true))->getDecorator('HtmlTag')->setOption('class', '');
        }
    }

    public function getChannelTypeElement() {

        $multiOptions = array('' => ' ');
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('sitevideo_channel', 'profile_type');
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

        $subform = new Zend_Form_SubForm(array(
            'name' => 'extra',
            'order' => 19999999,
            'decorators' => array(
                'FormElements',
            )
        ));
        Engine_Form::enableForm($subform);

        $i = 5079;

        $this->addElement('Text', 'search', array(
            'label' => 'What',
            'autocomplete' => 'off',
            'description' => '(Enter keywords or Channel name)',
            'order' => 2,
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));
        $this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


        $this->addElement('Text', 'channel_location', array(
            'label' => 'Where',
            'autocomplete' => 'off',
            'description' => '(address, city, state or country)',
            'order' => 3,
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
            'onclick' => 'locationChannel();'
        ));

        $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        if (isset($myLocationDetails['location'])) {
            $this->channel_location->setValue($myLocationDetails['location']);
        }

        if (isset($_POST['channel_location'])) {
            if (($_POST['channel_location'])) {
                $myLocationDetails['location'] = $_POST['channel_location'];
                $myLocationDetails['latitude'] = $_POST['Latitude'];
                $myLocationDetails['longitude'] = $_POST['Longitude'];
                $myLocationDetails['locationmiles'] = $_POST['locationmiles'];
            }

            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
        }

        if (!isset($_POST['channel_location']) && empty($this->_widgetSettings['locationDetection'])) {
            $this->channel_location->setValue('');
        }

        $this->channel_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.proximity.search.kilometer', 0);
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
            'order' => 4,
        ));

        if (isset($myLocationDetails['locationmiles'])) {
            $this->locationmiles->setValue($myLocationDetails['locationmiles']);
        }

        $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'type' => 'submit',
            'order' => 5,
            'ignore' => true,
            'onclick' => 'return locationSearch();'
        ));
        $this->addSubForm($subform, $subform->getName());

        // Element: cancel
        $this->addElement('Cancel', 'advances_search', array(
            'label' => 'Advanced search',
            'ignore' => true,
            'link' => true,
            'order' => 6,
            'onclick' => 'advancedSearchEvents();',
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('hidden', 'advanced_search', array(
            'order' => 1001,
            'value' => 0
        ));

        $this->addDisplayGroup(array('advances_search', 'done', 'locationmiles', 'done', 'channel_location', 'search'), 'grp3');
        $button_channel = $this->getDisplayGroup('grp3');
        $button_channel->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'channel3', 'class' => 'grp_field'))
        ));

        $channel2 = array();

        $this->addElement('Text', 'channel_street', array(
            'label' => 'Street',
            'autocomplete' => 'off',
            'order' => 7,
            
        ));
        $channel2[] = 'channel_street';

        $this->addElement('Text', 'channel_city', array(
            'label' => 'City',
            'placeholder' => '',
            'autocomplete' => 'off',
            'order' => 8,
            
        ));
        $channel2[] = 'channel_city';

        $this->addElement('Text', 'channel_state', array(
            'label' => 'State',
            'autocomplete' => 'off',
            'order' => 9,
            
        ));
        $channel2[] = 'channel_state';

        $this->addElement('Text', 'channel_country', array(
            'label' => 'Country',
            'autocomplete' => 'off',
            'order' => 10,
            
        ));
        $channel2[] = 'channel_country';

        if (!empty($channel2)) {
            $this->addDisplayGroup($channel2, 'grp2');
            $button_channel = $this->getDisplayGroup('grp2');
            $button_channel->setDecorators(array(
                'FormElements',
                'Fieldset',
                array('HtmlTag', array('tag' => 'div', 'id' => 'channel2', 'class' => 'grp_field'))
            ));
        }

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
                'label' => 'View:',
                'multiOptions' => $show_multiOptions,
                'value' => $value_deault,
            ));
        }

        $multiOPtionsOrderBy = array(
            '' => '',
            'creation_date' => 'Recently Created',
            'view_count' => 'Most Popular',
            'like_count' => 'Most Liked',
            'comment_count' => 'Most Commented',
            'videos_count' => 'Most Videos',
            'title' => "Alphabetical (A-Z)",
            'title_reverse' => 'Alphabetical (Z-A)'
        );

        $enableRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1);

        if ($enableRating) {
            $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
        }

        $this->addElement('Select', 'orderby', array(
            'label' => 'Browse By:',
            'multiOptions' => $multiOPtionsOrderBy,
        ));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            if (!$this->_widgetSettings['showAllCategories']) {
                $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'limit' => 0, 'orderBy' => 'cat_order', 'havingChannels' => 1));
            } else {
                $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1));
            }

            $categories_prepared = array();

            if (count($categories) != 0) {
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name;
                }

                // category field
                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'multiOptions' => $categories_prepared,
                    'onchange' => "showFields(this.value, 1); subcategories(this.value, '', '');",
                ));

                $this->addElement('Select', 'subcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    'allowEmpty' => true,
                    'required' => false,
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => 'application/modules/Sitevideo/views/scripts/_subCategory.tpl')))
                ));
            }
        }
        $this->addElement('Hidden', 'Latitude', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'Longitude', array(
            'order' => $i++,
        ));

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

        $this->addDisplayGroup(array('orderby', 'view_view', 'category_id', 'subcategory_id'), 'grp1');
        $button_channel = $this->getDisplayGroup('grp1');
        $button_channel->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'channel1', 'class' => 'grp_field'))
        ));

        return $this;
    }

}
