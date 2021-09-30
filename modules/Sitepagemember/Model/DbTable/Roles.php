<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Roles.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Model_DbTable_Roles extends Engine_Db_Table {

  protected $_rowClass = 'Sitepagemember_Model_Role';

  /**
   * Return category
   *
   * @param int $page_id
   */
  public function getRolesAssoc($page_id = null) {

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);

    $select = $this->select()
            ->from($this, array('role_id', 'role_name'));

    if ($manageCategorySettings == 1) {
      $select->where('is_admincreated =?', 1)
            ->where('page_category_id =?', $sitepage->category_id);
    } elseif ($manageCategorySettings == 2) {
      $select->where('page_id =?', $page_id)
            ->where('is_admincreated =?', 0)
            ->where('page_category_id =?', $sitepage->category_id);
    } elseif ($manageCategorySettings == 3) {
      $is_admincreated = array("0" => 0, "1" => 1);
      $page_id = array("0" => 0, "1" => $page_id);
      $select->where($this->info('name') . '.page_id IN (?)', (array) $page_id)
						->where($this->info('name') . '.is_admincreated IN (?)', (array) $is_admincreated)
						->where('page_category_id =?', $sitepage->category_id);
    }

    $select_category = $select->order('role_name ASC')->query();

    $data = array();
    foreach ($select_category->fetchAll() as $category) {
      $data[$category['role_id']] = $category['role_name'];
    }

    return $data;
  }

  /**
   * Return category name
   *
   * @param int $role_id
   */
  public function getRoleName($role_id) {

    $tableMemberCategory = $this->info('name');
    $select = $this->select()
            ->from($tableMemberCategory, array('role_name'))
            ->where('role_id = ?', $role_id);
    return $select->query()->fetchColumn();
  }

  /**
   * Return category data.
   *
   */
  public function getSiteAdminRoles($params=array(), $adminParams = null) {

    $select = $this->select();

    if (isset($params['page_category_id']) && !empty($params['page_category_id'])) {
      $select->from($this->info('name'), 'role_id');
      $select->where('page_category_id =?', $params['page_category_id']);
    } else {
      $select->from($this, array('role_id', 'role_name', 'page_category_id'));
    }
    if (isset($params['role_ids']) && $params['role_ids'])
      $select->where('role_id IN(?)', (array) $params['role_ids']);

    $select->where('is_admincreated =?', 1);
    $select_category = $select->order('page_category_id DESC')->query();

    if (isset($params['page_category_id']) && !empty($params['page_category_id'])) {
      $data = $select_category->fetchAll(Zend_Db::FETCH_COLUMN);
    } else {
      $data = array();
      foreach ($select_category->fetchAll() as $category) {
				if ($adminParams = 'adminParams') {
						//$data[$category['role_id']] = $category['role_name'];
						$tempArray["role_id"] = $category['role_id'];
						$tempArray["role_name"] = $category['role_name'];
						$tempArray["page_category_id"] = $category['page_category_id'];
						$data[] = $tempArray;
				} 
				else {
					$data[$category['role_id']] = $category['role_name'];
				}
      } 
    }
    return $data;
  }

  /**
   * Return pageadmins categories.
   *
   * @param int $page_id
   */
  public function getPageadminsRoles($page_id) {

    $data = $select = $this->select()
            ->from($this->info('name'), 'role_id')
            ->where('page_id =?', $page_id)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
//     foreach( $select_category->fetchAll() as $category ) {
//       $data[] = $category['role_id'];
//     }

    return $data;
  }

  /**
   * Roles parameters
   *
   * @param Array $categoryIdsArray
   * @return Review parameters
   */
  public function rolesParams($categoryIdsArray = array(), $is_admincreated=1, $roleIdsArray = array(), $param = null, $page_id = null) {

    if (empty($categoryIdsArray)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('role_id', 'role_name'));

    if ($roleIdsArray) {
      $select->where("role_id IN (?)", (array) $roleIdsArray);
    }
    if ($categoryIdsArray) {
      $select->where("page_category_id IN (?)", (array) $categoryIdsArray);
    }
    
    if(isset($page_id) && !empty($page_id)) {
    $page_id = array("0" => 0, "1" => $page_id);
    $select->where("page_id IN (?)", (array) $page_id);
    }
    if ($is_admincreated)
      $select->where("is_admincreated =?", 1);
    $select->order("role_id");

    $results = $this->fetchAll($select);

    if (!empty($param)) {
      $data = array();
      foreach ($results as $result) {
        $data[$result['role_id']] = $result['role_name'];
      }
      if (in_array('0', $roleIdsArray)) {
        $data['-1'] = 'Un-categorized';
      }
      return $data;
    } else {
      //RETURN RESULTS
      return $results;
    }
  }

}