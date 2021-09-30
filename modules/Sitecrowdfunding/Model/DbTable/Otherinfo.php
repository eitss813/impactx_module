<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Otherinfo.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Otherinfo extends Engine_Db_Table {

    protected $_rowClass = "Sitecrowdfunding_Model_Otherinfo";
    protected $_serializedColumns = array('main_video');

    public function getOtherinfo($project_id) {

        $rName = $this->info('name');
        $select = $this->select()
                ->where($rName . '.project_id = ?', $project_id);

        $row = $this->fetchRow($select);

        if (empty($row))
            return;

        return $row;
    }

    public function getColumnValue($project_id, $column_name) {

        return $this->select()
                        ->from($this->info('name'), array("$column_name"))
                        ->where('project_id = ?', $project_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }
    
    public function getOtherinfoColumns($params){
      
      $select = $this->select()
                        ->from($this->info('name'), $params['columns'])
                        ->where('project_id = ?', $params['project_id']);
      
      $row = $this->fetchRow($select);

      if (empty($row))
          return;

      return $row;
      
    }

    public function getOtherInfoRow($project_id){
        $select = $this->select();
        $select->where('project_id = ?', $project_id);
        return $this->fetchRow($select);
    }

}