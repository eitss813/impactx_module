<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Integrateothermodules.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesnewsletter_Model_DbTable_Integrateothersmodules extends Engine_Db_Table {

  protected $_rowClass = 'Sesnewsletter_Model_Integrateothersmodule';

  public function getResults($params = array()) {

    if (isset($params['column_name']))
      $columnName = $params['column_name'];
    else
      $columnName = '*';
    $select = $this->select()
            ->from($this->info('name'), $columnName);

    if (isset($params['integrateothersmodule_id']))
      $select = $select->where('integrateothersmodule_id = ?', $params['integrateothersmodule_id']);

    if (isset($params['content_type']))
      $select = $select->where('content_type = ?', $params['content_type']);

    if (isset($params['module_name']))
      $select = $select->where('module_name = ?', $params['module_name']);

    if (isset($params['content_id']))
      $select = $select->where('content_id = ?', $params['content_id']);

    if (isset($params['enabled']))
      $select = $select->where('enabled = ?', $params['enabled']);

    if (isset($params['type']))
      $select = $select->where('type = ?', $params['type']);

    return $select->query()->fetchAll();
  }
}
