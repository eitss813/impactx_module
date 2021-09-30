<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_SearchboxVideo extends Engine_Form {

    protected $_widgetSettings;

    public function getSettings() {
        return $this->_params;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function init() {
        $hiddenOrderCount=89740;
        $this
                ->setAttribs(array(
                    'method' => 'GET',
                    'id' => 'searchBoxVideo'
        ));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->setAction($view->url(array('action' => 'browse'), "sitevideo_video_general", true))->getDecorator('HtmlTag');
        if (!empty($this->_widgetSettings['formElements']) && in_array('textElement', $this->_widgetSettings['formElements'])) {
            $textWidth = $this->_widgetSettings['textWidth'];
            $this->addElement('Text', 'titleAjax', array(
                'label' => '',
                'placeholder' => $view->translate('Search...'),
                'autocomplete' => 'off',
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                'style' => "width:$textWidth" . "px;",
            ));

            if (isset($_GET['titleAjax'])) {
                $this->titleAjax->setValue($_GET['titleAjax']);
            } elseif (isset($_GET['search'])) {
                $this->titleAjax->setValue($_GET['search']);
            }
        }

        if (!empty($this->_widgetSettings['formElements']) && in_array('locationElement', $this->_widgetSettings['formElements']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 1)) {
            $locationWidth = $this->_widgetSettings['locationWidth'];
            $this->addElement('Text', 'locationSearch', array(
                'label' => '',
                'placeholder' => $view->translate('Location...'),
                //'autocomplete' => 'off',
                'style' => "width:$locationWidth" . "px;",
                'filters' => array(
                     new Engine_Filter_Censor(),
                     'StripTags',
                 ),
                    //'value' => $location,
            ));

            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            if (!isset($_GET['location']) && !isset($_GET['locationSearch']) && isset($myLocationDetails['location'])) {
                $this->locationSearch->setValue($myLocationDetails['location']);
            }

            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            if (isset($_GET['locationSearch'])) {
                $this->locationSearch->setValue($_GET['locationSearch']);
            } elseif (isset($_GET['location'])) {
                $this->locationSearch->setValue($_GET['location']);
            } elseif (isset($myLocationDetails['location'])) {
                $this->locationSearch->setValue($myLocationDetails['location']);
            }

            if (isset($_GET['location']) || isset($_GET['locationSearch'])) {
                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
            }

            if (!isset($_GET['location']) && !isset($_GET['locationSearch']) && empty($this->_widgetSettings['locationDetection'])) {
                $this->locationSearch->setValue('');
            }

            if (in_array('locationmilesSearch', $this->_widgetSettings['formElements'])) {

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0)) {
                    $locationLable = "Within Kilometers";
                    $locationOption = array(
                        '0' => $locationLable,
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
                        '0' => $locationLable,
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
                $locationmilesWidth = $this->_widgetSettings['locationmilesWidth'];
                $this->addElement('Select', 'locationmilesSearch', array(
                    'label' => $locationLable,
                    'multiOptions' => $locationOption,
                    //'placeholder' => $locationLable,
                    'value' => 0,
                    'style' => "width:$locationmilesWidth" . "px;",
                ));

                if (isset($_GET['locationmilesSearch'])) {
                    $this->locationmilesSearch->setValue($_GET['locationmilesSearch']);
                } elseif (isset($_GET['locationmiles'])) {
                    $this->locationmilesSearch->setValue($_GET['locationmiles']);
                } elseif (isset($myLocationDetails['locationmiles'])) {
                    $this->locationmilesSearch->setValue($myLocationDetails['locationmiles']);
                }
            }
        }

        $this->addElement('Hidden', 'video_id', array('order' => $hiddenOrderCount++));

        if (!empty($this->_widgetSettings['formElements']) && in_array('categoryElement', $this->_widgetSettings['formElements']) && !empty($this->_widgetSettings['categoriesLevel'])) {
            $categories = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getCategoriesHavingNoChield($this->_widgetSettings['categoriesLevel'], $this->_widgetSettings['showAllCategories']);
            if (count($categories) != 0) {
                $categories_prepared[0] = "All Categories";
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

                $this->addElement('Hidden', 'category_id', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'subcategory_id', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'subsubcategory_id', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'categoryname', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'subcategoryname', array(
                    'order' => $hiddenOrderCount++,
                ));

                $this->addElement('Hidden', 'subsubcategoryname', array(
                    'order' => $hiddenOrderCount++,
                ));
            }
        }

        if (!empty($this->_widgetSettings['formElements']) && in_array('linkElement', $this->_widgetSettings['formElements'])) {

            $this->addElement('Button', 'submitButton', array(
                'label' => 'Search',
                //'type' => 'submit',
                'ignore' => true,
                'onClick' => 'doSearching()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $url = $view->url(array('action' => 'browse'), "sitevideo_video_general");
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'Advanced',
                'link' => true,
                'title' => 'Advanced Search',
                'href' => $url,
                'prependText' => '  ',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addDisplayGroup(array('submitButton', 'cancel'), 'buttons', array(
                'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper',
                ),
            ));
        } else {
            $this->addElement('Button', 'submitButton', array(
                'label' => 'Search',
                //'type' => 'submit',
                'onClick' => 'doSearching()',
                'ignore' => true,
            ));
        }
    }

}
