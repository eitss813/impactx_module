<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Categories.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Categories extends Engine_Db_Table {

  protected $_name = 'album_categories';
  protected $_rowClass = 'Sitealbum_Model_Category';
  protected $_categories = array();

  /**
   * Return categories
   *
   * @param array $category_ids
   * @return all categories
   */
  public function getCategories($params = array()) {

    //MAKE QUERY
    $select = $this->select();

    //GET CATEGORY TABLE NAME
    $categoryTableName = $this->info('name');

    if (isset($params['orderBy']) && $params['orderBy'] == 'category_name') {
      $select->order('category_name');
    } else {
      $select->order('cat_order');
    }

    if (isset($params['cat_depandancy']) && !empty($params['cat_depandancy'])) {
      $select->where('cat_dependency = ?', 0);
    }

    if (isset($params['sponsored']) && !empty($params['sponsored'])) {
      $select->where('sponsored = ?', 1);
    }

    if (isset($params['fetchColumns']) && !empty($params['fetchColumns'])) {
      $select->setIntegrityCheck(false)->from($categoryTableName, $params['fetchColumns']);
    } else {
      $select->setIntegrityCheck(false)->from($categoryTableName);
    }

    if (isset($params['havingAlbums']) && !$params['havingAlbums']) {
      $tableAlbum = Engine_Api::_()->getDbTable('albums', 'sitealbum');
      $tableAlbumName = $tableAlbum->info('name');
      $select->join($tableAlbumName, "$tableAlbumName.category_id = $categoryTableName.category_id", null);
      $select->group(array("$tableAlbumName.category_id"));
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select->limit($params['limit']);
    }


    //RETURN DATA
    return $this->fetchAll($select);
  }

  /**
   * Return subcaregories
   *
   * @param int category_id
   * @return all sub categories
   */
  public function getSubCategories($params = array()) {

    $categoryTableName = $this->info('name');

    //RETURN IF CATEGORY ID IS EMPTY
    if (empty($params['category_id'])) {
      return;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($categoryTableName, $params['fetchColumns'])
            ->where('cat_dependency = ?', $params['category_id'])
            ->order('cat_order');

    if (isset($params['havingAlbums']) && empty($params['havingAlbums'])) {
      $tableAlbum = Engine_Api::_()->getDbTable('albums', 'sitealbum');
      $tableAlbumName = $tableAlbum->info('name');
      $select->join($tableAlbumName, "$tableAlbumName.subcategory_id = $categoryTableName.category_id", null)
              ->where($tableAlbumName . '.category_id = ?', $params['category_id'])
              ->group($categoryTableName . '.category_id');
    }

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  public function getCategoryName($category_id) {

    //RETURN IF CATEGORY ID IS EMPTY
    if (empty($category_id)) {
      return;
    }

    //MAKE QUERY
    $categoryName = $this->select()
            ->from($this->info('name'), array('category_name'))
            ->where('category_id = ?', $category_id)
            ->query()
            ->fetchColumn();
    //RETURN RESULTS
    return $categoryName;
  }

  public function getCategoriesHavingNoChield($arrayLevels = array(), $showAllCategories = 0) {

    $categoryTableName = $this->info('name');
    $select = $this->select()
            ->from($categoryTableName, array('category_id', 'category_name', 'cat_dependency'))
            ->order('cat_order');

    if (!$showAllCategories) {
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');
      $tableAlbumName = $tableAlbum->info('name');
      $select = $this->select()->setIntegrityCheck(false)->from($categoryTableName);
    }

    $addedEventJoin = 0;
    if (!empty($arrayLevels) && Count($arrayLevels) < 2) {

      if (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableAlbumName, "$tableAlbumName.subcategory_id=$categoryTableName.category_id", null);
          $addedEventJoin = 1;
        }

        $select->where("cat_dependency != 0");
      } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableAlbumName, "$tableAlbumName.category_id=$categoryTableName.category_id", null);
          $addedEventJoin = 1;
        }

        $select->where("(cat_dependency = 0)");
      } elseif (in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableAlbumName, "$tableAlbumName.category_id=$categoryTableName.category_id OR $tableAlbumName.subcategory_id=$categoryTableName.category_id", null);
          $addedEventJoin = 1;
        }
      }
    }

    if (!$addedEventJoin && !$showAllCategories) {
      $select->join($tableAlbumName, "$tableAlbumName.category_id=$categoryTableName.category_id", null);
    }

    if (!$showAllCategories) {
      $select->where($tableAlbumName . '.search = ?', 1);
    }

    //RETURN DATA
    return $this->fetchAll($select);
  }

  public function getCategoriesDetails($arrayLevels) {

    $categories = $this->getCategoriesHavingNoChield($arrayLevels);

    $categories_prepared = array();
    foreach ($categories as $category) {
      $categoryArray = array();
      if ($category->cat_dependency == 0) {
        $categoryArray['category_id'] = $category->category_id;
        $categoryArray['categoryname'] = $category->category_name;
        $categoryArray['subcategory_id'] = 0;
        $categoryArray['subcategoryname'] = '';
      } elseif ($category->cat_dependency != 0) {
        $categoryMain = Engine_Api::_()->getItem('album_category', $category->cat_dependency);
        $categoryArray['category_id'] = $categoryMain->category_id;
        $categoryArray['categoryname'] = $categoryMain->category_name;
        $categoryArray['subcategory_id'] = $category->category_id;
        $categoryArray['subcategoryname'] = $category->category_name;
      }

      $categories_prepared[$category->category_id] = $categoryArray;
    }

    //RETURN DATA
    return $categories_prepared;
  }

  /**
   * Get category object
   * @param int $category_id : category id
   * @return category object
   */
  public function getCategory($category_id) {
    if (empty($category_id))
      return;
    if (!array_key_exists($category_id, $this->_categories)) {
      $this->_categories[$category_id] = $this->find($category_id)->current();
    }
    return $this->_categories[$category_id];
  }

  public function getChildMapping($category_id) {

    return $this->select()
                    ->from($this->info('name'), 'category_id')
                    ->where("profile_type != ?", 0)
                    ->where("cat_dependency = $category_id")
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
    ;
  }

  public function getChilds($params = array()) {

    if (empty($params['category_id']))
      return;

    $cat_dependency = Engine_Api::_()->getItem('album_category', $params['category_id'])->cat_dependency;

    //IF CATEGORY THEN FETCH SUB-CATEGORY
    if ($cat_dependency != 0) {
      return array();
    }

    $select = $this->select()
            ->from($this->info('name'), $params['fetchcolumns'])
            ->where("cat_dependency = ?", $params['category_id']);
    return $this->fetchAll($select);
  }

  /**
   * Get Mapping array
   *
   */
  public function getMapping($params = array()) {

    //MAKE QUERY
    $select = $this->select()->from($this->info('name'), $params);

    //FETCH DATA
    $mapping = $this->fetchAll($select);

    //RETURN DATA
    if (!empty($mapping)) {
      return $mapping->toArray();
    }

    return null;
  }

  public function getCatDependancyArray() {

    return $this->select()
                    ->from($this->info('name'), 'cat_dependency')
                    ->where('cat_dependency <>?', 0)
                    ->group('cat_dependency')
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  /**
   * Get profile_type corresponding to category_id
   *
   * @param int category_id
   */
  public function getProfileType($params) {

    if (!empty($params['categoryIds'])) {
      $profile_type = 0;
      foreach ($params['categoryIds'] as $value) {
        $profile_type = $this->select()
                ->from($this->info('name'), array("profile_type"))
                ->where("category_id = ?", $value)
                ->query()
                ->fetchColumn();

        if (!empty($profile_type)) {
          return $profile_type;
        }
      }

      return $profile_type;
    } elseif (!empty($params['category_id'])) {

      //FETCH DATA
      $profile_type = $this->select()
              ->from($this->info('name'), array("profile_type"))
              ->where("category_id = ?", $params['category_id'])
              ->query()
              ->fetchColumn();

      return $profile_type;
    }

    return 0;
  }

  public function getCategoriesByLevel($level = null) {

    $select = $this->select()->order('cat_order');
    switch ($level) {
      case 'category':
        $select->where('cat_dependency =?', 0);
        break;
      case 'subcategory':
        $select->where('cat_dependency !=?', 0);
        break;
    }

    return $this->fetchAll($select);
  }
  
    /**
   * Return slug
   *
   * @param int $categoryname
   * @return categoryname
   */
  public function getCategorySlug($categoryname) {
    $slug = $categoryname;
    return Engine_Api::_()->seaocore()->getSlug($slug, 225);
  }
  
}