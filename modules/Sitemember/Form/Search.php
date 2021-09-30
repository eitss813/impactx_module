<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Search extends Fields_Form_Search {

  protected $_fieldType = 'user';
  protected $_searchForm;
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
      
    $this->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'sitmembers_browse_filters field_search_criteria',
        'method' => 'GET'
    ));
    parent::init();

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $this->loadDefaultDecorators();
    $this->getMemberTypeElement();
    $this->getAdditionalOptionsElement();

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($module == 'sitemember' && $controller == 'review' && $action == 'top-rated') {
      $this->setAction($view->url(array('action' => 'top-rated', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-recommended-members') {
      $this->setAction($view->url(array('action' => 'most-recommended-members', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-reviewed-members') {
      $this->setAction($view->url(array('action' => 'most-reviewed-members', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-reviewers') {
      $this->setAction($view->url(array('action' => 'top-reviewers', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-raters') {
      $this->setAction($view->url(array('action' => 'top-raters', 'module' => 'sitemember', 'controller' => 'review'), 'default', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');
    } elseif ($module == 'core' && $controller == 'pages' && ($action == 'member-list' || $action == 'member-grid' || $action == 'member-pinboard' || $action == 'member-map')) {
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    } else {
      $this->setAction($view->url(array('action' => 'userby-locations'), 'sitemember_userbylocation', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');
    }
  }

  public function getMemberTypeElement() {
    
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
      return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

    if(count($options) != 1) {
      $multiOptions = array('' => '');
    }
    
    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }
    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
    $row = $this->_searchForm->getFieldsOptions('sitemember', 'profile_type');
    $orderwhatWhereWithinmile = -1005;
    $this->addElement('select', 'profile_type', array(
        'label' => 'What',
        'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $row->order + 1 : ++$orderwhatWhereWithinmile,
        'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_' . $profileTypeField->field_id . ' ',
        'onchange' => 'changeFields($(this));',
        'multiOptions' => $multiOptions,
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div')),
            array('Label', array('tag' => 'div')),
            array('HtmlTag2', array('tag' => 'li'))
        ),
    ));
    return $this->profile_type;
  }

  public function getAdditionalOptionsElement() {

    $orderwhatWhereWithinmile = -1000;
    $i = 99980;

    $this->addElement('Hidden', 'page', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'city', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'latitude', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'longitude', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'Latitude', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'Longitude', array(
        'order' => $i++,
    ));

    $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();

    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    $row = $this->_searchForm->getFieldsOptions('sitemember', 'search');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Text', 'search', array(
          'label' => empty($this->_widgetSettings['whatWhereWithinmile']) ? 'Name / Keyword' : 'Who',
          'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $row->order : $orderwhatWhereWithinmile,
          'autocomplete' => 'off',
          'decorators' => array(
              'ViewHelper',
              array('HtmlTag', array('tag' => 'div')),
              array('Label', array('tag' => 'div')),
              array('HtmlTag2', array('tag' => 'li'))
          ),
          'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
      ));

      if (isset($_GET['search'])) {
        $this->search->setValue($_GET['search']);
      } elseif (isset($_GET['titleAjax'])) {
        $this->search->setValue($_GET['titleAjax']);
      }
    }

    //GET API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $row = $this->_searchForm->getFieldsOptions('sitemember', 'location');
    if ($settings->getSetting('sitemember.location.enable', 1) && !empty($row) && !empty($row->display)) {

      $advancedSearchOrder = $row->order;
      $this->addElement('Text', 'location', array(
          'label' => empty($this->_widgetSettings['whatWhereWithinmile']) ? 'Location' : 'Where',
          'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $row->order : ++$orderwhatWhereWithinmile,
          'decorators' => array(
              'ViewHelper',
              array('HtmlTag', array('tag' => 'div')),
              array('Label', array('tag' => 'div')),
              array('HtmlTag2', array('tag' => 'li'))
          ),
          'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
              //'value' => $location,
      ));

      $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (isset($_GET['location'])) {
        $this->location->setValue($_GET['location']);
      } elseif (isset($_GET['locationSearch'])) {
        $this->location->setValue($_GET['locationSearch']);
      } elseif (isset($myLocationDetails['location'])) {
        $this->location->setValue($myLocationDetails['location']);
      }

      if (isset($_GET['location']) || isset($_GET['locationSearch'])) {

        Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
      }

      if (!isset($_GET['location']) && !isset($_GET['locationSearch']) && isset($this->_widgetSettings['locationDetection']) && empty($this->_widgetSettings['locationDetection'])) {
        $this->location->setValue('');
      }

      $row = $this->_searchForm->getFieldsOptions('sitemember', 'proximity');
      if (!empty($row) && !empty($row->display)) {

        $flage = $settings->getSetting('sitemember.proximity.search.kilometer', 0);
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
        $advancedSearchOrder = $row->order + 1;
        $this->addElement('Select', 'locationmiles', array(
            'label' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $locationLable : $locationLable,
            'multiOptions' => $locationOption,
            'value' => 0,
            'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $row->order + 1 : ++$orderwhatWhereWithinmile,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
                array('Label', array('tag' => 'div')),
                array('HtmlTag2', array('tag' => 'li'))
            ),
        ));

        if (isset($_GET['locationmiles'])) {
          $this->locationmiles->setValue($_GET['locationmiles']);
        } elseif (isset($_GET['locationmilesSearch'])) {
          $this->locationmiles->setValue($_GET['locationmilesSearch']);
        } elseif (isset($myLocationDetails['locationmiles'])) {
          $this->locationmiles->setValue($myLocationDetails['locationmiles']);
        }
      }

      $rowStreet = $this->_searchForm->getFieldsOptions('sitemember', 'street');
      if (!empty($rowStreet) && !empty($rowStreet->display)) {
        $this->addElement('Text', 'sitemember_street', array(
            'label' => 'Street',
            'order' => $rowStreet->order,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
                array('Label', array('tag' => 'div')),
                array('HtmlTag2', array('tag' => 'li'))
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
        ));
      }

      $rowCity = $this->_searchForm->getFieldsOptions('sitemember', 'city');
      if (!empty($rowCity) && !empty($rowCity->display)) {
        $this->addElement('Text', 'sitemember_city', array(
            'label' => 'City',
            'order' => $rowCity->order,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
                array('Label', array('tag' => 'div')),
                array('HtmlTag2', array('tag' => 'li'))
            ),
        ));
      }
      $rowState = $this->_searchForm->getFieldsOptions('sitemember', 'state');
      if (!empty($rowState) && !empty($rowState->display)) {
        $this->addElement('Text', 'sitemember_state', array(
            'label' => 'State',
            'order' => $rowState->order,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
                array('Label', array('tag' => 'div')),
                array('HtmlTag2', array('tag' => 'li'))
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
        ));
      }
      $rowCountry = $this->_searchForm->getFieldsOptions('sitemember', 'country');
      if (!empty($rowCountry) && !empty($rowCountry->display)) {
        $this->addElement('Text', 'sitemember_country', array(
            'label' => 'Country',
            'order' => $rowCountry->order,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
                array('Label', array('tag' => 'div')),
                array('HtmlTag2', array('tag' => 'li'))
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
        ));
      }
    }

    if ($this->_widgetSettings['viewType'] == 'horizontal' && $this->_widgetSettings['whatWhereWithinmile'] && !$this->_widgetSettings['advancedSearch']) {
      $advancedSearch = $this->_widgetSettings['advancedSearch'];
      $this->addElement('Cancel', 'advances_search', array(
          'label' => 'Advanced search',
          'ignore' => true,
          'link' => true,
          'order' => ++$orderwhatWhereWithinmile,
          'onclick' => "advancedSearchLists($advancedSearch, 0);",
          'decorators' => array('ViewHelper'),
      ));

      $this->addElement('hidden', 'advanced_search', array(
         'order' => $i++,
          'value' => 0
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitemember', 'orderby');
    if (!empty($row) && !empty($row->display)) {

      $multiOPtionsOrderBy = array(
          '' => '',
          'creation_date' => 'Most Recent',
          'view_count' => 'Most Viewed',
          'like_count' => 'Most Liked',
          'member_count' => 'Most Popular',
          'title' => "Alphabetical (A-Z)",
          'title_reverse' => 'Alphabetical (Z-A)',
              //"rating" => "Top Rated"
      );

      if ($settings->getSetting('sitemember.location.enable', 1)) {
        $multiOPtionsOrderBy['distance'] = "Distance";
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify')) {
        $multiOPtionsOrderBy['verify_count'] = 'Most Verified';
      }

      $settings = Engine_Api::_()->getApi('settings', 'core');

      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => $multiOPtionsOrderBy,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitemembers();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('HtmlTag', array('tag' => 'div')),
              array('Label', array('tag' => 'div')),
              array('HtmlTag2', array('tag' => 'li'))
          ),
      ));
    } else {
      $this->addElement('hidden', 'orderby', array(
         'order' => $i++,
      ));
    }

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($module == 'sitemember' && $controller == 'review' && $action == 'top-rated') {
      $this->addElement('hidden', 'viewMembers', array(
         'order' => $i++,
          'value' => 'rating_avg'
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-recommended-members') {
      $this->addElement('hidden', 'viewMembers', array(
         'order' => $i++,
          'value' => 'recommend_count'
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'most-reviewed-members') {
      $this->addElement('hidden', 'viewMembers', array(
         'order' => $i++,
          'value' => 'review_count'
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-reviewers') {
      $this->addElement('hidden', 'viewMembers', array(
         'order' => $i++,
          'value' => 'top_reviewer_count'
      ));
    } elseif ($module == 'sitemember' && $controller == 'review' && $action == 'top-raters') {
      $this->addElement('hidden', 'viewMembers', array(
         'order' => $i++,
          'value' => 'top_raters'
      ));
    }

    $networkshow = $settings->getSetting('sitemember.network.show', 0);
    if (empty($networkshow)) {
      $row = $this->_searchForm->getFieldsOptions('sitemember', 'network_id');
      if (!empty($row) && !empty($row->display)) {
        $networks = Engine_Api::_()->getDbTable('networks', 'network');
        $networksname = $networks->info('name');
        $select = $networks->select()->from($networksname);
        $result = $networks->fetchAll($select);

        if (count($result) != 0) {
          $network_title[0] = "";
          foreach ($result as $results) {
            $network_title[$results->network_id] = $results->title;
          }

          $this->addElement('Select', 'network_id', array(
              'label' => 'Networks',
              'multiOptions' => $network_title,
              'onchange' => $this->gethasMobileMode() ? '' : 'searchSitemembers();',
              'order' => $row->order,
              'decorators' => array(
                  'ViewHelper',
                  array('HtmlTag', array('tag' => 'div')),
                  array('Label', array('tag' => 'div')),
                  array('HtmlTag2', array('tag' => 'li'))
              ),
          ));
        } else {
          $this->addElement('hidden', 'network_id', array(
             'order' => $i++,
          ));
        }
      }
    }

    $complimentCategoryOptions = Engine_Api::_()->getDbtable('complimentCategories', 'sitemember')->getComplimentCategories();
    if(!empty($complimentCategoryOptions)) {
      $row = $this->_searchForm->getFieldsOptions('sitemember', 'complimentcategory_id');
      if (!empty($row) && !empty($row->display)) {
      $this->addElement('Select', 'complimentcategory_id', array(
              'label' => 'Compliment',
              'multiOptions' => array_merge(array('All'),$complimentCategoryOptions),
              'order' => $row->order,
              'decorators' => array(
                  'ViewHelper',
                  array('HtmlTag', array('tag' => 'div')),
                  array('Label', array('tag' => 'div')),
                  array('HtmlTag2', array('tag' => 'li'))
              ),
              'onclick' => 'searchSitemembers()'
          ));
      } else {
          $this->addElement('hidden', 'complimentcategory_id', array(
             'order' => $i++,
          ));
      }
    }
    $row = $this->_searchForm->getFieldsOptions('sitemember', 'show');
    if (!empty($row) && !empty($row->display)) {
      $show_multiOptions = array();
      $show_multiOptions["1"] = 'All Members';
      $show_multiOptions["2"] = 'Only My Friends';
      $show_multiOptions["4"] = "Members I Like";

      $show_multiOptions["5"] = "Only Featured";
      $show_multiOptions["6"] = "Only Sponsored";
      $show_multiOptions["7"] = "Both Featured & Sponsored";
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify')) {
        $show_multiOptions["8"] = "Members I've Verified";
      }

      $value_deault = 1;
      $networkShow = $settings->getSetting('sitemember.network.show', 0);
      if (empty($networkShow)) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
        if (!empty($viewerNetwork))
          $show_multiOptions["3"] = 'Only My Networks';
      }

      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => $show_multiOptions,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitemembers();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('HtmlTag', array('tag' => 'div')),
              array('Label', array('tag' => 'div')),
              array('HtmlTag2', array('tag' => 'li'))
          ),
          'value' => $value_deault,
      ));
    } else {
      $this->addElement('hidden', 'show', array(
         'order' => $i++,
          'value' => 1
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitemember', 'has_photo');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Checkbox', 'has_photo', array(
          'label' => "Only Members With Photos",
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitemember', 'is_online');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Checkbox', 'is_online', array(
          'label' => "Only Online Members",
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'value'=>''
      ));
    }

    if ($this->gethasMobileMode()) {
      $this->addElement('Button', 'done', array(
          'label' => 'Search',
          'type' => 'submit',
          'ignore' => true,
          'order' => 999999999,
          'decorators' => array(
              'ViewHelper',
              //array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    } else {
      $this->addElement('Button', 'done', array(
          'label' => 'Search',
          'onclick' => 'searchSitemembers();',
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

}