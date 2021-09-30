<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rewardshippinglocations.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Rewardshippinglocations extends Engine_Db_Table {

    protected $_rowClass = "Sitecrowdfunding_Model_Rewardshippinglocation";
    public function findShippingLocation($rewardId,$regionId){
        return $this
                ->select()
                ->where('reward_id=?',$rewardId)
                ->where('region_id=?',$regionId)
                ->query()
                ->fetch();
                
        
    }

    public function findShippingCharge($projectId,$rewardId,$regionId = null){
        $select = $this->select()->from($this->info('name'), array("amount"));
            if(!empty($projectId)) {
                $select->where('project_id=?',$projectId);
            }
            if(!empty($rewardId)) {
                $select->where('reward_id=?',$rewardId);
            }
            if(!empty($regionId)) {
                $select->where('region_id=?',$regionId);
            }    
        return $select->limit(1)->query()->fetchColumn(); 
    }
}
