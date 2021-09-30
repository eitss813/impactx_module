<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_SearchSitephotoController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->params = $params = $request->getParams();

    if (!empty($params) && isset($params['category_id']) && isset($params['subcategory_id'])) {
      $this->view->category_id = $category_id = $params['category_id'];
      $this->view->subcategory_id = $subcategory_id = $params['subcategory_id'];
    }

    //FORM CREATION
    $this->view->viewType = $this->_getParam('viewType', 'vertical');
    $this->view->whatWhereWithinmile = $this->_getParam('whatWhereWithinmile', 0);
    $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
    $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);
    $this->view->locationDetection = $this->_getParam('locationDetection', 0);

    $widgetSettings = array(
        'viewType' => $this->view->viewType,
        'whatWhereWithinmile' => $this->view->whatWhereWithinmile,
        'advancedSearch' => $this->view->advancedSearch,
        'showAllCategories' => $this->view->showAllCategories,
        'locationDetection' => $this->view->locationDetection,
    );

    $this->view->form = $form = new Sitealbum_Form_Search_PhotoSearch(array('widgetSettings' => $widgetSettings));

    $orderBy = $request->getParam('orderby', null);
    if (empty($orderBy)) {
      $order = Engine_Api::_()->sitealbum()->showSelectedBrowseBy($this->view->identity);
      if(isset($form->orderby))
      $form->orderby->setValue("$order");
    }

    if (isset($params['tag']) && !empty($params['tag'])) {
      $tag = $params['tag'];
      if (isset($params['tag_id']) && !empty($params['tag_id'])) {
        $tag_id = $params['tag_id'];
      }
      $page = 1;
      if (isset($params['page']) && !empty($params['page'])) {
        $page = $params['page'];
      }
      $params['page'] = $page;
      $params['search'] = $params['tag'] = $tag;
      if (isset($params['tag_id']) && !empty($params['tag_id'])) {
        $params['tag_id'] = $tag_id;
      }
    }

    $orderBy = $request->getParam('orderby', null);

    if (!empty($orderBy)) {
      $params['orderby'] = $orderBy;
    }

    if (!isset($params['profile_type']) && !empty($category_id)) {
      $categoryIds = array();
      $categoryIds[] = $category_id;
      $categoryIds[] = $subcategory_id;

      $profile_type = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
      if (!empty($profile_type)) {
        $params['profile_type'] = $profile_type;
      }
    }

    if (!empty($params))
      $form->populate($params);

    //SHOW PROFILE FIELDS ON DOME READY
    if (!empty($category_id)) {
      $categoryIds = array();
      $categoryIds[] = $category_id;
      $categoryIds[] = $subcategory_id;

      //GET PROFILE MAPPING ID
      $this->view->profileType = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
    }

    $categories = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'sponsored' => 0, 'cat_depandancy' => 1));
    $categories_slug[0] = "";
    if (count($categories) != 0) {
      foreach ($categories as $category) {
        $categories_slug[$category->category_id] = $category->getCategorySlug();
      }
    }
    $this->view->categories_slug = $categories_slug;
  }

}

