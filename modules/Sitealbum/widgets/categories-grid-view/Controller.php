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
class Sitealbum_Widget_CategoriesGridViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1))
      return $this->setNoRender();
    
    $showSubCategoryCount = $this->_getParam('showSubCategoriesCount', 5);
    $this->view->count = $count = $this->_getParam('showCount', 0);

    $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitealbum');
    $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $this->view->columnWidth = $this->_getParam('columnWidth', 268);
    $this->view->columnHeight = $this->_getParam('columnHeight', 260);
    $showAllCategories = $this->_getParam('showAllCategories', 0);
    $orderBy = $this->_getParam('orderBy', 'cat_order');
    $this->view->storage = Engine_Api::_()->storage();

    // GET ALL CATEGORIES
    $sitealbum_gridview = Zend_Registry::isRegistered('sitealbum_gridview') ? Zend_Registry::get('sitealbum_gridview') : null;
    $categories = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'cat_dependency', 'category_name', 'category_slug', 'cat_order', 'photo_id'), 'sponsored' => 0, 'cat_depandancy' => 1, 'havingAlbums' => $showAllCategories, 'orderBy' => $orderBy));

    if (empty($sitealbum_gridview))
      return $this->setNoRender();

    if (count($categories) == 0)
      return $this->setNoRender();

    $categoryParams = array();
    foreach ($categories as $category) {
      $subcategory_info2 = $tableCategory->getSubcategories(array('category_id' => $category->category_id, 'havingAlbums' => $showAllCategories, 'fetchColumns' => array('category_id', 'category_name', 'cat_order')));
      $SubCategoryArray = $tempSubCategoryArray = array();
      if (!empty($subcategory_info2)) {
        $tempFlag = 0;
        foreach ($subcategory_info2 as $subCategory) {
          if ($tempFlag == $showSubCategoryCount)
            break;
          else
            $tempFlag++;
          $tempSubCategoryArray['sub_category_id'] = $subCategory->getIdentity();
          $tempSubCategoryArray['title'] = $subCategory->getTitle();
          $tempSubCategoryArray['order'] = $subCategory->cat_order;
          $tempSubCategoryArray['count'] = !empty($count) ? $tableSitealbum->getAlbumsCount(array('category_id' => $subCategory->category_id, 'columnName' => 'subcategory_id', 'foruser' => 1)) : false;

          $SubCategoryArray[] = $tempSubCategoryArray;
        }
      }
      $tempCategoryParams['category_id'] = $category->getIdentity();
      $tempCategoryParams['title'] = $category->getTitle();
      $tempCategoryParams['order'] = $category->cat_order;
      $tempCategoryParams['photo_id'] = $category->photo_id;
      $tempCategoryParams['subCategories'] = $SubCategoryArray;
      $tempCategoryParams['count'] = !empty($count) ? $tableSitealbum->getAlbumsCount(array('category_id' => $category->category_id, 'columnName' => 'category_id', 'foruser' => 1)) : false;
      $categoryParams[] = $tempCategoryParams;
    }
    $this->view->categoryParams = $categoryParams;
  }

}
