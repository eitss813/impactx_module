<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SearchboxProject.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_SearchboxProject extends Engine_Form {

    protected $_widgetSettings;

    public function getSettings() {
        return $this->_params;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function init() {

        $this->setAttribs(array(
                    'method' => 'GET',
                    'id' => 'searchBoxProject'
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->setAction($view->url(array('action' => 'browse'), "sitecrowdfunding_project_general", true))->getDecorator('HtmlTag');
        if (!empty($this->_widgetSettings['formElements']) && in_array('textElement', $this->_widgetSettings['formElements'])) {
            $textWidth = $this->_widgetSettings['textWidth'];
            $this->addElement('Text', 'titleAjax', array(
                'label' => '',
                'placeholder' => $view->translate('Search...'),
                'autocomplete' => 'off',
                'style' => "width:$textWidth" . "px;",
            ));

            if (isset($_GET['titleAjax'])) {
                $this->titleAjax->setValue($_GET['titleAjax']);
            } elseif (isset($_GET['search'])) {
                $this->titleAjax->setValue($_GET['search']);
            }
        }

        if (!empty($this->_widgetSettings['formElements']) && in_array('locationElement', $this->_widgetSettings['formElements']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $locationWidth = $this->_widgetSettings['locationWidth'];
            $this->addElement('Text', 'locationSearch', array(
                'label' => '',
                'placeholder' => $view->translate('Location...'),
                //'autocomplete' => 'off',
                'style' => "width:$locationWidth" . "px;",
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

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0)) {
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

        $this->addElement('Hidden', 'project_id', array());
        if (!empty($this->_widgetSettings['formElements']) && in_array('categoryElement', $this->_widgetSettings['formElements']) && !empty($this->_widgetSettings['categoriesLevel'])) {
            $categories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategoriesHavingNoChield($this->_widgetSettings['categoriesLevel'], $this->_widgetSettings['showAllCategories']);
             $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            if (count($categories) != 0) {
                $categories_prepared[0] = "All Categories";
                foreach ($categories as $category) { 
                        if (!(isset($this->_widgetSettings['showAllCategories']) && $this->_widgetSettings['showAllCategories'])) {
                            $count = $tableProject->getProjectsCount($category->category_id, 'category_id');
                            if (empty($count))
                                continue;
                        }
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
                    'order' => 497,
                ));

                $this->addElement('Hidden', 'subcategory_id', array(
                    'order' => 498,
                ));

                $this->addElement('Hidden', 'subsubcategory_id', array(
                    'order' => 499,
                ));

                $this->addElement('Hidden', 'categoryname', array(
                    'order' => 500,
                ));

                $this->addElement('Hidden', 'subcategoryname', array(
                    'order' => 501,
                ));

                $this->addElement('Hidden', 'subsubcategoryname', array(
                    'order' => 502,
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

            $url = $view->url(array('action' => 'browse'), "sitecrowdfunding_project_general");
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
