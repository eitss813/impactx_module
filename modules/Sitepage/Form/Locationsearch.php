<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Locationsearch extends Fields_Form_Search {

  protected $_searchFormSettings;
  protected $_value;

  public function getValue($name = null) {
    return $this->_value;
  }

  public function setValue($item) {
    $this->_value = $item;
    return $this;
  }

  public function init() {

    $this->_value = unserialize($this->_value);

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

    //GET SEARCH FORM SETTINGS
    $this->_searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitepage'); 

    $this->getMemberTypeElement();

    $this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($module == 'sitepage' && $controller == 'index' && $action != 'map') {
      $this->setAction($view->url(array('action' => 'map'), 'sitepage_general', true))->getDecorator('HtmlTag')->setOption('class', '');
    }
  }

  public function getMemberTypeElement() {
//    $row = $this->_searchForm->getFieldsOptions('sitepage', 'profile_type');
//    if (empty($row) || empty($row->display)) {
//      return;
//    }
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
      return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

//     if (count($options) <= 1) {
//       if (count($options) == 1) {
//         $this->_topLevelId = $profileTypeField->field_id;
//         $this->_topLevelValue = $options[0]->option_id;
//       }
//       return;
//     }

    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }

    asort($multiOptions);

    $this->addElement('Hidden', 'profile_type', array(
       'order' => 1001,
        //'label' => 'Page Profile Type',
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

    $subform = new Zend_Form_SubForm(array(
        'name' => 'extra',
        'order' => 19999999,
        'decorators' => array(
            'FormElements',
        )
    ));
    Engine_Form::enableForm($subform);

    $i = 5000;
    $this->addElement('Hidden', 'page', array(
         'order' => $i++,
    ));
    $this->addElement('Hidden', 'tag', array(
         'order' => $i++,
    ));

    $this->addElement('Hidden', 'alphabeticsearch', array(
         'order' => $i++,
    ));

    $this->addElement('Hidden', 'start_date', array(
         'order' => $i++,
    ));

    $this->addElement('Hidden', 'end_date', array(
         'order' => $i++,
    ));

    if (!empty($this->_searchFormSettings['search']) && !empty($this->_searchFormSettings['search']['display'])) {
      $this->addElement('Text', 'search', array(
          'label' => 'What',
          'autocomplete' => 'off',
          'description' => '(Enter keywords or Page name)',
          'order' => 1,
          'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
              //'placeholder' => 'Enter keywords or Page name',
      ));
      $this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    }

    if (!empty($this->_searchFormSettings['location']) && !empty($this->_searchFormSettings['location']['display'])) {
      $this->addElement('Text', 'sitepage_location', array(
          'label' => 'Where',
          'autocomplete' => 'off',
          'description' => '(address, city, state or country)',
          'order' => 2,
          'onclick' => 'locationPage();',
          'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
      ));
      $this->sitepage_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    }

    if (!empty($this->_searchFormSettings['locationmiles']) && !empty($this->_searchFormSettings['locationmiles']['display'])) {
      $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
      if (!empty($enableLocation)) {
        $enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximitysearch', 1);

        if (!empty($enableProximitysearch)) {
          $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
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
              'order' => 3,
          ));
        }
      }
    }
    //Check for Location browse page.

    $this->addElement('Button', 'done', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true,
        'order' => 4,
        'onclick' => ($action == 'map') ? 'return locationSearch();' : ''
    ));

    if (!empty($this->_value['advancedsearchLink'])) {
      // Element: cancel
      $this->addElement('Cancel', 'advances_search', array(
          'label' => 'Advanced search',
          'ignore' => true,
          'link' => true,
          'order' => 5,
          'onclick' => 'advancedSearchSitepages();',
          'decorators' => array('ViewHelper'),
      ));
    }

    $this->addElement('hidden', 'advanced_search', array(
       'order' => $i++,
        'value' => 0
    ));

    $this->addDisplayGroup(array('advances_search', 'done', 'locationmiles', 'search', 'done', 'sitepage_location'), 'grp3');
    $button_group = $this->getDisplayGroup('grp3');
    $button_group->setDecorators(array(
        'FormElements',
        'Fieldset',
        array('HtmlTag', array('tag' => 'li', 'id' => 'group3', 'style' => 'width:100%;'))
    ));

    $group2 = array();

    if (!empty($this->_searchFormSettings['street']) && !empty($this->_searchFormSettings['street']['display'])) {
      if (!empty($this->_value['street'])) {
        $this->addElement('Text', 'sitepage_street', array(
            'label' => 'Street',
            'autocomplete' => 'off',
            'order' => 12,
        ));
        $group2[] = 'sitepage_street';
      }
    }

    if (!empty($this->_searchFormSettings['city']) && !empty($this->_searchFormSettings['city']['display'])) {
      if (!empty($this->_value['city'])) {
        $this->addElement('Text', 'sitepage_city', array(
            'label' => 'City',
            'autocomplete' => 'off',
            'order' => 13,
        ));
        $group2[] = 'sitepage_city';
      }
    }

    if (!empty($this->_searchFormSettings['state']) && !empty($this->_searchFormSettings['state']['display'])) {
      if (!empty($this->_value['state'])) {
        $this->addElement('Text', 'sitepage_state', array(
            'label' => 'State',
            'autocomplete' => 'off',
            'order' => 14,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $group2[] = 'sitepage_state';
      }
    }

    if (!empty($this->_searchFormSettings['country']) && !empty($this->_searchFormSettings['country']['display'])) {
      if (!empty($this->_value['country'])) {
        $this->addElement('Text', 'sitepage_country', array(
            'label' => 'Country',
            'autocomplete' => 'off',
            'order' => 15,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $group2[] = 'sitepage_country';
      }
    }

// 		if (!empty($this->_value['postalcode'])) {
// 			$this->addElement('Dummy', 'or', array(
// 				'label' => 'or',
// 				'order' => 15,
// 			));
//       $group2[] = 'or';
//       
// 			//postal code.
// 			$this->addElement('Text', 'sitepage_postalcode', array(
// 				'label' => 'Postal code',
// 				'autocomplete' => 'off',
// 				'order' => 16,
// 			));
// 			$group2[] = 'sitepage_postalcode';
// 		}

    if (!empty($group2)) {
      $this->addDisplayGroup($group2, 'grp2');
      $button_group = $this->getDisplayGroup('grp2');
      $button_group->setDecorators(array(
          'FormElements',
          'Fieldset',
          array('HtmlTag', array('tag' => 'li', 'id' => 'group2', 'style' => 'width:100%;'))
      ));
    }

    if (!empty($this->_searchFormSettings['orderby']) && !empty($this->_searchFormSettings['orderby']['display'])) {
      $multiOPtionsOrderBy = array(
          '' => '',
          'creation_date' => 'Most Recent',
          'view_count' => 'Most Viewed',
          'comment_count' => 'Most Commented',
          'like_count' => 'Most Liked',
          'title' => "Alphabetical"
      );

      $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
      if (!empty($sitepagereviewEnabled)) {
        $multiOPtionsOrderBy['review_count'] = "Most Reviewed";
        $multiOPtionsOrderBy['rating'] = "Highest Rated";
      }

      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => $multiOPtionsOrderBy,
          'order' => 6,
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') && !empty($this->_searchFormSettings['offer_type']) && !empty($this->_searchFormSettings['offer_type']['display'])) {
    $this->addElement('Select', 'offer_type', array(
        'label' => 'Pages With Offers',
        'multiOptions' => array(
            '' => '',
            'all' => 'All Offers',
            'hot' => 'Hot Offers',
            'featured' => 'Featured Offers',
        ),
        'order' => 7,
    ));
    }

    if (!empty($this->_searchFormSettings['show']) && !empty($this->_searchFormSettings['show']['display'])) {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $show_multiOptions = array();
      $show_multiOptions["1"] = 'Everyone\'s Pages';
      $show_multiOptions["2"] = 'Only My Friends\' Pages';
      $show_multiOptions["4"] = 'Pages I Like';
      $show_multiOptions["5"] = 'Featured Pages';
      $value_deault = 1;
      $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0);
      if (empty($enableNetwork)) {
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

        if (!empty($viewerNetwork) || Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
          $show_multiOptions["3"] = 'Only My Networks';
          $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.default.show', 0);

          if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
            $value_deault = 3;
          } elseif (isset($_GET['show'])) {
            $value_deault = $_GET['show'];
          }
        }
      }

      if (!empty($viewer_id)) {
        $this->addElement('Select', 'show', array(
            'label' => 'Show',
            'multiOptions' => $show_multiOptions,
            'order' => 8,
            'value' => $value_deault,
        ));
      }
    }

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1) && !empty($this->_searchFormSettings['closed']) && !empty($this->_searchFormSettings['closed']['display'])) {

        $this->addElement('Select', 'closed', array(
            'label' => 'Status',
            'multiOptions' => array(
                '' => 'All Pages',
                '0' => 'Only Open Pages',
                '1' => 'Only Closed Pages',
            ),
            'order' => 9,
        ));
    }

    if (!empty($this->_searchFormSettings['has_photo']) && !empty($this->_searchFormSettings['has_photo']['display'])) {
      $this->addElement('Checkbox', 'has_photo', array(
          'label' => 'Only Pages With Photos',
          'order' => 10,
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && !empty($this->_searchFormSettings['has_review']) && !empty($this->_searchFormSettings['has_review']['display'])) {
      $this->addElement('Checkbox', 'has_review', array(
          'label' => 'Only Pages With Reviews',
          'order' => 11,
      ));
    }

    if (!empty($this->_searchFormSettings['category_id']) && !empty($this->_searchFormSettings['category_id']['display'])) {
      // prepare categories
      $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }

        // category field
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'order' => 20,
            'multiOptions' => $categories_prepared,
            'onchange' => "var profile_type = getProfileType($(this).value);
															$('profile_type').value = profile_type;
															changeFields($('profile_type'));
                              location_subcategoryies(this.value, '', '', '');",
        ));
      }

      $this->addElement('Select', 'subcategory_id', array(
          'RegisterInArrayValidator' => false,
          'order' => 21,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'application/modules/Sitepage/views/scripts/_Locationsubcategory.tpl',
                      'class' => 'form element')))
      ));
    }

    $this->addElement('Hidden', 'category', array(
         'order' => $i++,
    ));
    $this->addElement('Hidden', 'subcategory', array(
         'order' => $i++,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
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

    $this->addDisplayGroup(array('orderby', 'show', 'closed', 'has_photo', 'has_review', 'offer_type', 'profile_type', 'category_id', 'subcategory_id'), 'grp1');
    $button_group = $this->getDisplayGroup('grp1');
    $button_group->setDecorators(array(
        'FormElements',
        'Fieldset',
        array('HtmlTag', array('tag' => 'li', 'id' => 'group1', 'style' => 'width:100%;'))
    ));

    return $this;
  }

}
