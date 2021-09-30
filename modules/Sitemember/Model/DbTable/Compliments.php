<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Compliments.php 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_Model_DbTable_Compliments extends Engine_Db_Table {
  
  public function getCompliments($params = array()) {
    $complimentsTablename = $this->info('name');
    $select = $this->select();
    
                    
     if (!empty($params['resource_type'])){
          $select->where("resource_type =? ",$params['resource_type']);
     }
     if (!empty($params['resource_id'])){
          $select->where("resource_id =? ",$params['resource_id']);
     }
     if (!empty($params['user_id'])){
          $select->where("user_id =? ",$params['user_id']);
     }
     if (!empty($params['complimentcategory_id'])){
          $select->where("complimentcategory_id =? ",$params['complimentcategory_id']);
     }
     if (!empty($params['limit'])){
          $select->limit($params['limit']);
     }
     if (!empty($params['groupby'])){
         $select->from($complimentsTablename, array("$complimentsTablename.*","COUNT(*) as count"));
         $select->group($params['groupby'])
                 ->order("count DESC");
          
     }
    if (!empty($params['orderby'])) {
      $select->order($complimentsTablename . "." . $params['orderby'] . " DESC");
    } else {
       $select->order("date DESC");
    }
    return $select;
  }
  public function getUserIdsByComplimentCategoryId($params) {
      
      $complimentsTablename = $this->info('name');
      $select = $this->select()->from($complimentsTablename, array("$complimentsTablename.resource_id","COUNT(*) as count"));
      if (isset($params['complimentcategory_id']) && !empty($params['complimentcategory_id'])){
          $select->where("complimentcategory_id =? ",$params['complimentcategory_id']);
      }
      $select->group("$complimentsTablename.resource_id")
              ->order("count DESC")
              ->order("date DESC"); 
      $resource_ids = $this->fetchAll($select);
      return Engine_Api::_()->sitemember()->getArrayByColumn($resource_ids, 'resource_id');
     
  }
  public  function getComplimentById($compliment_id=0){
      $compliment = $this->select()
                          ->where("compliment_id =?",$compliment_id)
                          ->query()
                          ->fetchAll();
      return $compliment;
  }
  public  function getComplimentCount($params = array()){
      $select =$this->select()
                    ->from($this->info('name'), array("COUNT(*) as count"));
      if(!empty($params['resource_type'])) {
                    $select->where("resource_type =?",$params['resource_type']);
      }
      if(!empty($params['complimentcategory_id'])) {
         $select->where("complimentcategory_id =?",$params['complimentcategory_id']);
      }
      if(!empty($params['resource_id'])) {
         $select->where("resource_id =?",$params['resource_id']);
      }
      $count = $select->query()->fetchColumn();
      return $count;
  }

}
