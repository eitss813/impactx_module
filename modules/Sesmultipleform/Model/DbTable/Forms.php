<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Forms.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_DbTable_forms extends Engine_Db_Table {
  protected $_rowClass = "Sesmultipleform_Model_Form";
  public function getForm($params = array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->from($tableName)
            ->order('order DESC');
            
     if( isset($params['active']) ) {
       $select->where('active = ?', (int) $params['active']);
     }
		if( isset($params['limit']) ) {
       $select->limit($params['limit']);
     }
    if (isset($params['fetchAll']))
      return $this->fetchAll($select);
    return Zend_Paginator::factory($select);
  }
    public function getFormByPageId($page_id) {
        $tableName = $this->info('name');
        $select = $this->select()
            ->from($tableName)
            ->order('order DESC');

//        if( isset($params['active']) ) {
//            $select->where('active = ?', (int) $params['active']);
//        }
        if($page_id) {
            $select->where('page_id = ?', (int) $page_id);
        }
//        if( isset($params['limit']) ) {
//            $select->limit($params['limit']);
//        }
//        if (isset($params['fetchAll']))
//            return $this->fetchAll($select);
        return Zend_Paginator::factory($select);
    }
}
