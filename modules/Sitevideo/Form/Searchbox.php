<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Searchbox extends Engine_Form {

    protected $_widgetSettings;

    public function getSettings() {
        return $this->_params;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function init() {
        $hiddenOrderCount=543246;

        $this
                ->setAttribs(array(
                    'method' => 'GET',
                    'id' => 'searchBox'
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->setAction($view->url(array('action' => 'browse'), "sitevideo_general", true))->getDecorator('HtmlTag');

        if (!empty($this->_widgetSettings['formElements']) && in_array('textElement', $this->_widgetSettings['formElements'])) {
            $textWidth = $this->_widgetSettings['textWidth'];
            $this->addElement('Text', 'search', array(
                'label' => '',
                'placeholder' => $view->translate('Search...'),
                'autocomplete' => 'off',
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                'style' => "width:$textWidth" . "px;",
            ));

            if (isset($_GET['search'])) {
                $this->search->setValue($_GET['search']);
            } elseif (isset($_GET['search'])) {
                $this->search->setValue($_GET['search']);
            }
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');



        $this->addElement('Hidden', 'channel_id', array('order' => $hiddenOrderCount++));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
            if (!empty($this->_widgetSettings['formElements']) && in_array('categoryElement', $this->_widgetSettings['formElements']) && !empty($this->_widgetSettings['categoriesLevel'])) {
                $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoriesHavingNoChield($this->_widgetSettings['categoriesLevel'], $this->_widgetSettings['showAllCategories']);
                if (count($categories) != 0) {
                    $categories_prepared[0] = $view->translate("All Categories");
                    foreach ($categories as $category) {
                        $categories_prepared[$category->category_id] = $category->category_name;
                    }
                    $categoryWidth = $this->_widgetSettings['categoryWidth'];
                    $this->addElement('Select', 'ajaxcategory_id', array(
                        'allowEmpty' => false,
                        'required' => true,
                        'multiOptions' => $categories_prepared,
                        'style' => "width:$categoryWidth" . "px;",
                    ));
                }
                $this->addElement('Hidden', 'category_id', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'subcategory_id', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'subsubcategory_id', array(
                    'order' => $hiddenOrderCount++,
                ));
            }
        }

        $this->addElement('Button', 'submitButton', array(
            'label' => 'Search',
            //'type' => 'submit',
            'onClick' => 'doSearching()',
            'ignore' => true,
        ));
    }

}
