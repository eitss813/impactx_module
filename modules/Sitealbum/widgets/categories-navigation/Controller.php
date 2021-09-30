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
class Sitealbum_Widget_CategoriesNavigationController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
   
    $this->view->viewDisplayHR = $this->_getParam('viewDisplayHR', 0);
    if ($this->view->viewDisplay) {
      $element = $this->getElement();
      $this->view->widgetTitle = $element->getTitle();
      $element->setTitle('');
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) 
      return $this->setNoRender();

    //GET PRODUCT CATEGORY TABLE
    $orderBy = $this->_getParam('orderBy', 'cat_order');
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
    $categoriesArray = array();
    $categories = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => $orderBy));
    foreach ($categories as $category) {
      $subcategoriesArray = $this->getSubCategory($category->category_id);
      $categoriesArray[$category->category_id] = array(
          'category' => $category,
          'subcategories' => $subcategoriesArray,
      );
    }
    $this->view->categoriesArray = $categoriesArray;
    $this->view->requestAllParams = $requestAllParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
  }

  protected function getSubCategory($category_id) {
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
    $subcategoriesArray = array();
    $subcategories = $tableCategory->getSubCategories(array('category_id' => $category_id, 'fetchColumns' => array('category_id', 'category_name', 'cat_order', 'cat_dependency','category_slug')));
    foreach ($subcategories as $subcategory) {
      $subcategoriesArray[$subcategory->category_id] = array(
          'subcategory' => $subcategory,
      );
    }
    return $subcategoriesArray;
  }

}