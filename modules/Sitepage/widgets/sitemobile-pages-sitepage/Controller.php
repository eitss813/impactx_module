<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_SitemobilePagesSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    //location related work

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
      }
    }

    if (empty($this->view->is_ajax_load)) {
      $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (isset($cookieLocation['location']) && !empty($cookieLocation['location'])) {
        $this->view->is_ajax_load = 1;
      }
    }
    //end

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->sitepage_browse = 'show';
    $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
    
    if ($this->_getParam('ajax', false)) {
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    //Content display widget setting parameter.
    if(Engine_Api::_()->seaocore()->isSitemobileApp()){
      $this->view->contentDisplayArray = $this->_getParam('content_display', array("featured","sponsored","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location"));
    }else{
      $this->view->contentDisplayArray = $this->_getParam('content_display', array("featured","sponsored","closed","ratings","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location","price"));
    }
    $this->view->columnHeight = $this->_getParam('columnHeight', 325);
    
    $defaultOrder = $this->_getParam('view_selected', 1);

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    

    $this->view->isajax = $this->_getParam('isajax', "0");

    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }

    if ($this->view->defaultView == -1) {
      return $this->setNoRender();
    }
    $customFieldValues = array();
    $values = array();
    $select_category = $this->_getParam('category_id', 0);
    if (!empty($select_category) && empty($_GET['category'])) {
      $category = $select_category;
      $category_id = $select_category;
    } else {
      $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
      $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);
    }
    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $subcategory_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategory_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);
    
    if ($category)
      $this->view->category = $_GET['category'] = $category;
    if ($subcategory)
      $this->view->subcategory = $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $this->view->categoryname = $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $this->view->subcategoryname = $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
      $this->view->subsubcategory = $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
      $this->view->subsubcategoryname = $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $this->view->category = $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $this->view->subcategory = $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
      $this->view->subsubcategory = $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;
    

    $values['tag'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null);
    if (!empty($values['tag'])) {
      $_GET['tag'] = $values['tag'];
      $tagItem = Engine_Api::_()->getItem('core_tag', $values['tag']);
      $this->view->tag_name = $tagItem->text; 
    }

    //location work
    $this->view->sitepage_location = $values['sitepage_location'] = $request->getParam('sitepage_location', null);
    if (!empty($values['sitepage_location'])) {
     $this->view->sitepage_location = $_GET['sitepage_location'] = $values['sitepage_location'];
    }
    
    if (isset($_GET['sitepage_location']) && !empty($_GET['sitepage_location'])) { 
      $this->view->sitepage_location = $sitepage_location = $_GET['sitepage_location'];
      $this->view->sitepage_location = $_GET['sitepage_location'] = $sitepage_location;
    }
    //end
    //FORM GENERATIONlatitu
    $form = new Sitepage_Form_Search(array('type' => 'sitepage_page'));

    if (!empty($_GET))
      $form->populate($_GET);
    $values = $form->getValues();

    //PAGE OFFER IS INSTALLED OR NOT
    $this->view->sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');

    //BADGE CODE
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
      if (isset($_POST['badge_id']) && !empty($_POST['badge_id'])) {
        $values['badge_id'] = $_POST['badge_id'];
      }
    }

    $values['page'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('page', 1);
    //GET LISITNG FPR PUBLIC PAGE SET VALUE
    $values['type'] = 'browse';
    $values['type_location'] = 'browsePage';

    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }

    //GEO-LOCATION WORK
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagegeolocation') && isset($values['has_currentlocation']) && !empty($values['has_currentlocation'])) {

      $session = new Zend_Session_Namespace('Current_location');
      if (!isset($session->latitude) || !isset($session->longitude)) {
        $locationResult = null;
        $apiType = Engine_Api::_()->getApi('core', 'sitepagegeolocation')->getGeoApiType();
        if ($apiType == 1) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindCurrentLocation();
        } elseif ($apiType == 2) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindGeoLiteCountry();
        }
        if (($apiType == 1 || $apiType == 2) && !empty($locationResult)) {
          $this->view->latitude = $values['latitude'] = $session->latitude = $locationResult['latitude'];
          $this->view->longitude = $values['longitude'] = $session->longitude = $locationResult['longitude'];
        }
      } else {
        $this->view->latitude = $values['latitude'] = $session->latitude;
        $this->view->longitude = $values['longitude'] = $session->longitude;
      }
    }
    $this->view->assign($values);

    //PAGE-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

    //DON'T SEND CUSTOM FIELDS ARRAY IF EMPTY
    $has_value = 0;
    foreach ($customFieldValues as $customFieldValue) {
      if (!empty($customFieldValue)) {
        $has_value = 1;
        break;
      }
    }

    if (empty($has_value)) {
      $customFieldValues = null;
    }

    $values['browse_page'] = 1;

    //location work
    $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $values['locationmiles'] = $this->_getParam('defaultLocationDistance', 1000);
      $values['Latitude'] = $values['latitude'] = $this->_getParam('latitude', 0);
      $values['Longitude'] = $values['longitude'] = $this->_getParam('longitude', 0);
      
    }

    if (!$this->view->detactLocation && empty($_GET['sitepage_location']) && isset($values['sitepage_location'])) { 
      unset($values['sitepage_location']);

      if (empty($_GET['latitude']) && isset($values['latitude'])) {
        unset($values['latitude']);
      }

      if (empty($_GET['longitude']) && isset($values['longitude'])) {
        unset($values['longitude']);
      }

      if (empty($_GET['Latitude']) && isset($values['Latitude'])) {
        unset($values['Latitude']);
      }

      if (empty($_GET['Longitude']) && isset($values['Longitude'])) {
        unset($values['Longitude']);
      }
    }  
    //end
    // GET SITEPAGES
    $this->view->paginator = $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, $customFieldValues);

    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);
    $this->view->paginator->setItemCountPerPage(12);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    $this->view->flageSponsored = 0;

    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $sitepage) {
        $id = $sitepage->getIdentity();
        $ids[] = $id;
        $sitepage_temp[$id] = $sitepage;
      }
      $values['page_ids'] = $ids;
      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);

      foreach ($locations as $location) {
        if ($sitepage_temp[$location->page_id]->sponsored) {
          $this->view->flageSponsored = 1;
          break;
        }
      }
      $this->view->sitepage = $sitepage_temp;
    } else {
      $this->view->enableLocation = 0;
    }
    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = 0;
      $_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }

    $this->view->search = 0;
    if (!empty($_GET)) {
      $this->view->search = 1;
    }

    //location work
    $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
    $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);

    if (isset($_GET['search']) || isset($_POST['search'])) {
      $this->view->detactLocation = 0;
    } else {
      $this->view->detactLocation = $this->_getParam('detactLocation', 0);
    }   
    //end
    //CAN CREATE PAGES OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create');
    //AJAX POST VARIABLE SEND FOR SITEMOBILE VIEWS
    $this->view->view_selected  = $this->_getParam('view_selected', "grid");
    $reqview_selected = Zend_Controller_Front::getInstance()->getRequest()->getParam('view_selected');
    if ($reqview_selected && $this->view->list_view && $this->view->grid_view) {
      $this->view->view_selected = $reqview_selected;
    }
    $this->view->formValues = array();
    $this->view->formValues['alphabeticsearch'] =  Zend_Controller_Front::getInstance()->getRequest()->getParam('alphabeticsearch','all');
    if($this->view->formValues['alphabeticsearch']=='all'){
      unset($this->view->formValues['alphabeticsearch']);
    }
    
    //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }    
    }
    //FETCH CATEGORY TITLE FOR APP
      $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitepage');
      if(isset($category) && !empty($category)){
       $this->view->category_title = $categoriesTable->getCategory($category)->category_name;
      }
    
    $this->view->page = $this->_getParam('page', 1);
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalCount) /12);
    //END - SCROLLING WORK
    
    if(isset($_GET))
    $this->view->formValues = $_GET;
  }

}

?>