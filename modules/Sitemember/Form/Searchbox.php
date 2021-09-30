<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Searchbox extends Fields_Form_Search {

  protected $_widgetSettings;
  protected $_fieldType = 'user';

  public function getSettings() {
    return $this->_params;
  }

  public function setWidgetSettings($widgetSettings) {
    $this->_widgetSettings = $widgetSettings;
    return $this;
  }

  public function init() {

    $this
            ->setAttribs(array(
                'method' => 'GET',
                'id' => 'searchBox'
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($module == 'sitemember' && $controller == 'review' && $action == 'top-rated') {
      $this->setAction($view->url(array('action' => 'top-rated', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-recommended-members') {
      $this->setAction($view->url(array('action' => 'most-recommended-members', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-reviewed-members') {
      $this->setAction($view->url(array('action' => 'most-reviewed-members', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-reviewers') {
      $this->setAction($view->url(array('action' => 'top-reviewers', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-raters') {
      $this->setAction($view->url(array('action' => 'top-raters', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag');
    } elseif ($module == 'core' && $controller == 'pages' && isset($action) && ($action == 'member-list' || $action == 'member-grid' || $action == 'member-pinboard' || $action == 'member-map')) {
      //$this->setAction();
    } else {
      $this->setAction($view->url(array('action' => 'userby-locations'), "sitemember_userbylocation", true))->getDecorator('HtmlTag');
    }

    if (!empty($this->_widgetSettings['formElements']) && in_array('textElement', $this->_widgetSettings['formElements'])) {
      $textWidth = $this->_widgetSettings['textWidth'];
      $this->addElement('Text', 'search', array(
          'label' => '',
          'placeholder' => $view->translate('Search...'),
          'autocomplete' => 'off',
          'style' => "width:$textWidth" . "px;",
          'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
      ));
      
      if (isset($_GET['search'])) {
        $this->search->setValue($_GET['search']);
      } elseif (isset($_GET['search'])) {
        $this->search->setValue($_GET['search']);
      }
      $this->search->setAttrib('id', 'sitemember_searchbox');
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    if ($settings->getSetting('sitemember.location.enable', 1) && !empty($this->_widgetSettings['formElements']) && in_array('locationElement', $this->_widgetSettings['formElements'])) {
      $locationWidth = $this->_widgetSettings['locationWidth'];
      $this->addElement('Text', 'locationSearch', array(
          'label' => '',
          'placeholder' => $view->translate('Location...'),
          'style' => "width:$locationWidth" . "px;",
          'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
      ));

      $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (!isset($_GET['location']) && !isset($_GET['locationSearch']) && isset($myLocationDetails['location'])) {
        $this->locationSearch->setValue($myLocationDetails['location']);
      }

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

        if ($settings->getSetting('sitemember.proximity.search.kilometer', 0)) {
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

    $this->addElement('Hidden', 'user_id', array( 'order' => 995,));


    if (!empty($this->_widgetSettings['formElements']) && in_array('profileTypeElement', $this->_widgetSettings['formElements'])) {
      $multiOptions = array('' => '');
      $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
      if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
        return;
      $profileTypeField = $profileTypeFields['profile_type'];

      $options = $profileTypeField->getOptions();

      foreach ($options as $option) {
        $multiOptions[$option->option_id] = $option->label;
      }
      $orderwhatWhereWithinmile = -1005;
      $this->addElement('select', 'profile_type', array(
          'class' =>
          'field_toggle' . ' ' .
          'parent_' . 0 . ' ' .
          'option_' . 0 . ' ' .
          'field_' . $profileTypeField->field_id . ' ',
          'multiOptions' => $multiOptions,
          'decorators' => array(
              'ViewHelper',
              array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
          ),
      ));
    }

    if ($module == 'sitemember' && $controller == 'review' && $action == 'top-rated') {
      $this->addElement('hidden', 'viewMembers', array(
          'value' => 'rating_avg',
          'order' => 9999,
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-recommended-members') {
      $this->addElement('hidden', 'viewMembers', array(
          'value' => 'recommend_count',
          'order' => 9998,
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-reviewed-members') {
      $this->addElement('hidden', 'viewMembers', array(
          'value' => 'review_count',
          'order' => 9997,
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-reviewers') {
      $this->addElement('hidden', 'viewMembers', array(
          'value' => 'top_reviewer_count',
          'order' => 9996,
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-raters') {
      $this->addElement('hidden', 'viewMembers', array(
          'value' => 'top_raters',
          'order' => 9995,
      ));
    }

    $this->addElement('Button', 'submitButton', array(
        'label' => 'Search',
        'onClick' => 'doSearching()',
        'ignore' => true,
    ));
  }

}