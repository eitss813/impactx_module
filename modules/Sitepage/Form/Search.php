
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Search extends Fields_Form_Search {

  protected $_hasMobileMode = false;
  protected $_searchFormSettings;
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

    // Add custom elements
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
    ;

    parent::init();
    //GET SEARCH FORM SETTINGS
    $this->_searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitepage');

    //if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.profile.search', 1)) {
    $this->getMemberTypeElement();
    //}
    //$this->getDisplayNameElement();
    $this->getAdditionalOptionsElement();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $this->setAction($view->url(array('action' => 'index'), 'sitepage_general', true))->getDecorator('HtmlTag')->setOption('class', '');
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

    if (count($options) <= 1) {
      if (count($options) == 1) {
        $this->_topLevelId = $profileTypeField->field_id;
        $this->_topLevelValue = $options[0]->option_id;
      }
      return;
    }

    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }

    asort($multiOptions);

    $this->addElement('Hidden', 'profile_type', array(
        //'label' => 'Page Profile Type',
        // 'order' => 99,
        'order' => 999,
        'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_' . $profileTypeField->field_id . ' ',
        'onchange' => 'changeFields($(this));',
//        'decorators' => array(
//            'ViewHelper',
//            array('Label', array('tag' => 'span')),
//            array('HtmlTag', array('tag' => 'li'))
//        ),
        'multiOptions' => $multiOptions,
    ));
    return $this->profile_type;
  }

  public function getDisplayNameElement() {
    $this->addElement('Text', 'displayname', array(
        'label' => 'Name',
        'order' => 999999999,
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => 'span')),
            array('HtmlTag', array('tag' => 'li'))
        ),
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
            //'onkeypress' => 'return submitEnter(event)',
    ));
    return $this->displayname;
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

    //   public function getAdditionalOptionsElement() {
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

    if (!empty($this->_searchFormSettings['price']) && !empty($this->_searchFormSettings['price']['display'])) {

      $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
      if (!empty($enablePrice)) {

        $subformPrice = new Zend_Form_SubForm(array(
            'description' => "Price",
            'order' => $this->_searchFormSettings['price']['order'],
            'decorators' => array(
                'FormElements',
                array('Description', array('placement' => 'PREPEND', 'tag' => 'span')),
                array('HtmlTag', array('tag' => 'li', 'class' => 'browse-range-wrapper'))
            )
        ));
        Fields_Form_Standard::enableForm($subformPrice);
        Engine_Form::enableForm($subformPrice);

        $params['options']['decorators'] = array('ViewHelper');
        $params['options']['placeholder'] = 'min';
        $subformPrice->addElement('text', 'min', $params['options']);
        $params['options']['placeholder'] = 'max';
        $subformPrice->addElement('text', 'max', $params['options']);
        $this->addSubForm($subformPrice, 'sitepage_price');
      }
    }

    if (!empty($this->_searchFormSettings['location']) && !empty($this->_searchFormSettings['location']['display'])) {
      $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
      if (!empty($enableLocation)) {
        if (!empty($this->_searchFormSettings['locationmiles']) && !empty($this->_searchFormSettings['locationmiles']['display'])) {
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
                'order' => $this->_searchFormSettings['locationmiles']['order'],
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
          }
          
          if (isset($_GET['locationmiles'])) {
            $this->locationmiles->setValue($_GET['locationmiles']);
          } elseif (isset($_GET['locationmilesSearch'])) {
            $this->locationmiles->setValue($_GET['locationmilesSearch']);
          } elseif (isset($myLocationDetails['locationmiles'])) {
            $this->locationmiles->setValue($myLocationDetails['locationmiles']);
          }
        }
        
        $this->addElement('Text', 'sitepage_location', array(
            'label' => 'Location',
            'order' => $this->_searchFormSettings['location']['order'],
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
         'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
        ));


        $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        if (isset($_GET['sitepage_location'])) {
          $this->sitepage_location->setValue($_GET['sitepage_location']);
        } elseif (isset($_GET['locationSearch'])) {
          $this->sitepage_location->setValue($_GET['locationSearch']);
        } elseif (isset($myLocationDetails['location'])) {
          $this->sitepage_location->setValue($myLocationDetails['location']);
          if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && empty($myLocationDetails['location']) && isset($_GET['is_ajax_load'])){ 
            $this->sitepage_location->setValue(true);
          }
        }

        if (isset($_GET['sitepage_location']) || isset($_GET['locationSearch'])) {
          Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
        }

        if (!isset($_GET['sitepage_location']) && !isset($_GET['locationSearch']) && isset($this->_widgetSettings['locationDetection']) && empty($this->_widgetSettings['locationDetection'])) {
          $this->sitepage_location->setValue('');
        }

        if (!empty($this->_searchFormSettings['street']) && !empty($this->_searchFormSettings['street']['display'])) {
          $this->addElement('Text', 'sitepage_street', array(
              'label' => 'Street',
              'order' => $this->_searchFormSettings['street']['order'],
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
            'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
          ));
        }

        if (!empty($this->_searchFormSettings['city']) && !empty($this->_searchFormSettings['city']['display'])) {
          $this->addElement('Text', 'sitepage_city', array(
              'label' => 'City',
              'order' => $this->_searchFormSettings['city']['order'],
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
                          'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
          ));
        }

        if (!empty($this->_searchFormSettings['state']) && !empty($this->_searchFormSettings['state']['display'])) {
          $this->addElement('Text', 'sitepage_state', array(
              'label' => 'State',
              'order' => $this->_searchFormSettings['state']['order'],
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
                          'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
          ));
        }

        if (!empty($this->_searchFormSettings['country']) && !empty($this->_searchFormSettings['country']['display'])) {
          $this->addElement('Text', 'sitepage_country', array(
              'label' => 'Country',
              'order' => $this->_searchFormSettings['country']['order'],
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
          ));
        }
      }
    }

    if (!empty($this->_searchFormSettings['search']) && !empty($this->_searchFormSettings['search']['display'])) {
      $this->addElement('Text', 'search', array(
          'label' => 'Search Pages',
          'order' => $this->_searchFormSettings['search']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
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

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagebadge.seaching.bybadge', 1) && !empty($this->_searchFormSettings['badge_id']) && !empty($this->_searchFormSettings['badge_id']['display'])) {

      $params = array();
      $params['search_code'] = 1;
      $badgeData = Engine_Api::_()->getDbTable('badges', 'sitepagebadge')->getBadgesData($params);
      if (!empty($badgeData)) {
        $badgeData = $badgeData->toArray();
        $badgeCount = Count($badgeData);

        if (!empty($badgeCount)) {
          $badge_options = array();
          $badge_options[0] = '';
          foreach ($badgeData as $name) {
            $badge_options[$name['badge_id']] = $name['title'];
          }

          $this->addElement('Select', 'badge_id', array(
              'label' => 'Badge',
              'multiOptions' => $badge_options,
              'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
              'order' => $this->_searchFormSettings['badge_id']['order'],
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
          ));
        }
      }
    }

    $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
    if (!empty($sitepagereviewEnabled) && !empty($this->_searchFormSettings['orderby']) && !empty($this->_searchFormSettings['orderby']['display'])) {
      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => array(
              '' => '',
              'creation_date' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'comment_count' => 'Most Commented',
              'like_count' => 'Most Liked',
              'title' => "Alphabetical",
              'review_count' => "Most Reviewed",
              'rating' => "Highest Rated",
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
          'order' => $this->_searchFormSettings['orderby']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    } elseif (!empty($this->_searchFormSettings['orderby']) && !empty($this->_searchFormSettings['orderby']['display'])) {
      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => array(
              '' => '',
              'creation_date' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'comment_count' => 'Most Commented',
              'like_count' => 'Most Liked',
              'title' => "Alphabetical",
              'follow_count' => "Most Followed",
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
          'order' => $this->_searchFormSettings['orderby']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    } else {
      $this->addElement('hidden', 'orderby', array(
         'order' => $i++,
      ));
    }

    $sitepageofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');

    if (!empty($sitepageofferEnabled) && !empty($this->_searchFormSettings['offer_type']) && !empty($this->_searchFormSettings['offer_type']['display'])) {
      $this->addElement('Select', 'offer_type', array(
          'label' => 'Pages With Offers',
          'multiOptions' => array(
              '' => '',
              'all' => 'All Offers',
              'hot' => 'Hot Offers',
              'featured' => 'Featured Offers',
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
          'order' => $this->_searchFormSettings['offer_type']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    if (!empty($this->_searchFormSettings['show']) && !empty($this->_searchFormSettings['show']['display'])) {
      $show_multiOptions = array();
      $show_multiOptions["1"] = 'Everyone\'s Pages';
      $show_multiOptions["2"] = 'Only My Friends\' Pages';
      $show_multiOptions["4"] = 'Pages I Like';
      $show_multiOptions["5"] = 'Featured Pages';
      $value_deault = 1;
      $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0);
      if (empty($enableNetwork)) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
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

      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => $show_multiOptions,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
          'order' => $this->_searchFormSettings['show']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'value' => $value_deault,
      ));
    } else {
      $this->addElement('hidden', 'show', array(
          'value' => 1,
           'order' => $i++,
      ));
    }

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1) && !empty($this->_searchFormSettings['closed']) && !empty($this->_searchFormSettings['closed']['display'])) {

      $this->addElement('Select', 'closed', array(
          'label' => 'Status',
          'multiOptions' => array(
              '' => 'All Pages',
              '0' => 'Only Open Pages',
              '1' => 'Only Closed Pages',
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
          'order' => $this->_searchFormSettings['closed']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    if (!empty($this->_searchFormSettings['category_id']) && !empty($this->_searchFormSettings['category_id']['display'])) {
      // prepare categories
      $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();

// echo preg_replace($pattern, '&', $category->category_name);
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = preg_replace('/&([#0-9A-Za-z]+);/', '&', $category->category_name); //$category->category_name;
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          $onChangeEvent = "var profile_type = getProfileType($(this).value);
															$('profile_type').value = profile_type;
															changeFields($('profile_type'));
                              subcategoryies(this.value, '', '');";
          $categoryFiles = 'application/modules/Sitepage/views/scripts/_Subcategory.tpl';
        } else {
          $onChangeEvent = "sm4.core.category.set(this.value, 'subcategory');";
          $categoryFiles = 'application/modules/Sitepage/views/sitemobile/scripts/_Subcategory.tpl';
        }

        // category field
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'order' => $this->_searchFormSettings['category_id']['order'],
            'multiOptions' => $categories_prepared,
            'onchange' => $onChangeEvent,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))),
        ));
      }

      $this->addElement('Select', 'subcategory_id', array(
          'RegisterInArrayValidator' => false,
          'order' => $this->_searchFormSettings['category_id']['order'] + 1,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => $categoryFiles,
                      'class' => 'form element')))
      ));

//      $this->addElement('Select', 'subsubcategory_id', array(
//          'RegisterInArrayValidator' => false,
//          'order' =>  $this->_searchFormSettings['category_id']['order'] + 1,
//          'decorators' => array(array('ViewScript', array(
//                      'viewScript' => $categoryFiles,
//                      'class' => 'form element')))
//      ));
    } else {
      $this->addElement('Hidden', 'category_id', array(
           'order' => $i++,
      ));

      $this->addElement('Hidden', 'subcategory_id', array(
           'order' => $i++,
      ));

      $this->addElement('Hidden', 'subsubcategory_id', array(
           'order' => $i++,
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

    if (!empty($this->_searchFormSettings['has_photo']) && !empty($this->_searchFormSettings['has_photo']['display'])) {
      $this->addElement('Checkbox', 'has_photo', array(
          'label' => 'Only Pages With Photos',
          'order' => $this->_searchFormSettings['has_photo']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && !empty($this->_searchFormSettings['has_review']) && !empty($this->_searchFormSettings['has_review']['display'])) {
      $this->addElement('Checkbox', 'has_review', array(
          'label' => 'Only Pages With Reviews',
          'order' => $this->_searchFormSettings['has_review']['order'],
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagegeolocation') && !empty($this->_searchFormSettings['has_currentlocation']) && !empty($this->_searchFormSettings['has_currentlocation']['display'])) {
      $this->addElement('Checkbox', 'has_currentlocation', array(
          'label' => 'Only current place and range',
          'order' => $this->_searchFormSettings['has_currentlocation']['order'],
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.default', 1)
      ));
    }

    $subform->addElement('Button', 'done', array(
        'label' => 'Search',
        'type' => 'submit',
        'onclick' => $this->gethasMobileMode() ? '' : 'searchSitepages();',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'li'))
        ),
    ));

    $this->addSubForm($subform, $subform->getName());

    return $this;
  }

}

?>