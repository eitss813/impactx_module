<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Categories.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_DbTable_Categories extends Engine_Db_Table {
  protected $_rowClass = 'Sesmultipleform_Model_Category';
  protected $_name = 'sesmultipleform_categories';
  public function deleteCategory($params = array()) {
    $isValid = false;
    if (count($params) > 0) {
      if ($params->subcat_id != 0) {
        $Subcategory = $this->getModuleSubsubcategory(array('column_name' => '*', 'category_id' => $params->category_id));
        if (count($Subcategory) > 0)
          $isValid = false;
        else
          $isValid = true;
      }else if ($params->subsubcat_id != 0) {
        $isValid = true;
      } else {
        $category = $this->getModuleSubcategory(array('column_name' => '*', 'category_id' => $params->category_id));
        if (count($category) > 0)
          $isValid = false;
        else
          $isValid = true;
      }
    }
    return $isValid;
  }
  public function order($categoryType = 'category_id', $categoryTypeId) {
    // Get a list of all corresponding category, by order
    $table = Engine_Api::_()->getItemTable('sesmultipleform_category');
    $currentOrder = $table->select()
            ->from($table, 'category_id')
            ->order('order DESC');
    if ($categoryType != 'category_id')
      $currentOrder = $currentOrder->where($categoryType . ' = ?', $categoryTypeId);
    else
      $currentOrder = $currentOrder->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
    return $currentOrder->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  public function orderNext($params = array()) {
    $category_select = $this->select()
            ->from($this->info('name'), '*')
            ->limit(1)
            ->order('order DESC');
    if (isset($params['category_id'])) {
      $category_select = $category_select->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
    } else if (isset($params['subsubcat_id'])) {
      $category_select = $category_select->where('subsubcat_id = ?', $params['subsubcat_id']);
    } else if (isset($params['subcat_id'])) {
      $category_select = $category_select->where('subcat_id = ?', $params['subcat_id']);
    }
    $category_select = $this->fetchRow($category_select);
    if (empty($category_select))
      $order = 1;
    else
      $order = $category_select['order'] + 1;
    return $order;
  }
  public function getCategory($params = array(), $customParams = array(), $searchParams = array()) {
    if (isset($params['column_name'])) {
      $column = $params['column_name'];
    } else
      $column = '*';
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $column)
            ->where($tableName . '.subcat_id = ?', 0)
            ->where($tableName . '.subsubcat_id = ?', 0);
    if (isset($params['id']) && !empty($params['id']))
      $category_select = $category_select->where($tableName . '.form_id = ?', $params['id']);			
    if (count($customParams)) {
      return Zend_Paginator::factory($category_select);
    }
		$category_select->order('order DESC');
		if(isset($params['limit']) && $params['limit'])
			$category_select->limit($params['limit']);
    return $this->fetchAll($category_select);
  }
  public function getMapping($params = array()) {
    $select = $this->select()->from($this->info('name'), $params);
    $mapping = $this->fetchAll($select);
    if (!empty($mapping)) {
      return $mapping->toArray();
    }
    return null;
  }
  public function getMapId($categoryId = '') {
    $tableName = $this->info('name');
    if ($categoryId) {
      $category_map_id = $this->select()
              ->from($tableName, 'profile_type')
              ->where('category_id = ?', $categoryId)
							->order('order DESC');
      $category_map_id = $this->fetchAll($category_map_id);
      if (isset($category_map_id[0]->profile_type)) {
        return $category_map_id[0]->profile_type;
      } else
        return 0;
    }
  }
  public function getSubCatMapId($subcategoryId = '') {
    $tableName = $this->info('name');
    if ($subcategoryId) {
      $category_map_id = $this->select()
              ->from($tableName, 'profile_type')
              ->where('category_id = ?', $subcategoryId)
							->order('order DESC');
      $category_map_id = $this->fetchAll($category_map_id);
      if (isset($category_map_id[0]->profile_type)) {
        return $category_map_id[0]->profile_type;
      } else
        return 0;
    }
  }
  public function getSubSubCatMapId($subsubcategoryId = '') {
    $tableName = $this->info('name');
    if ($subsubcategoryId) {
      $category_map_id = $this->select()
              ->from($tableName, 'profile_type')
              ->where('category_id = ?', $subsubcategoryId)
							->order('order DESC');
      $category_map_id = $this->fetchAll($category_map_id);
      if (isset($category_map_id[0]->profile_type)) {
        return $category_map_id[0]->profile_type;
      } else
        return 0;
    }
  }
  public function getCategoriesAssoc($params = array()) {
    $stmt = $this->select()
            ->from($this, array('category_id', 'category_name'))
            ->where('subcat_id = ?', 0)
            ->where('subsubcat_id = ?', 0);
    if (isset($params['module'])) {
      $stmt = $stmt->where('resource_type = ?', $params['module']);
    }
    $stmt = $stmt->order('order DESC')
            ->query()
            ->fetchAll();
    $data = array();
    if (isset($params['module']) && $params['module'] == 'group') {
      $data[] = '';
    }
    foreach ($stmt as $category) {
      $data[$category['category_id']] = $category['category_name'];
    }
    return $data;
  }
  public function getColumnName($params = array()) {
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where('category_id = ?', $params['category_id']);
    if (isset($params['subcat_id']))
      $category_select = $category_select->where('subcat_id = ?', $params['subcat_id']);
    return $category_select = $category_select->query()->fetchColumn();
  }
  public function getModuleSubcategory($params = array()) {

    $tableName = $this->info('name');

    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);

    if (isset($params['category_id']))
      $category_select = $category_select->where($tableName . '.subcat_id = ?', $params['category_id']);

if (isset($params['id']) && !empty($params['id']))
      $category_select = $category_select->where($tableName . '.form_id = ?', $params['id']);
    $category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
  public function getModuleCategory($params = array()) {
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where('category_id = ?', $params['category_id']);
    $category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
  public function getModuleSubsubcategory($params = array()) {
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where($tableName . '.subsubcat_id = ?', $params['category_id']);

if (isset($params['id']) && !empty($params['id']))
      $category_select = $category_select->where($tableName . '.form_id = ?', $params['id']);
    $category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
}