<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rewards.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Rewards extends Engine_Db_Table {

    protected $_rowClass = "Sitecrowdfunding_Model_Reward";

    public function getRewards($project_id, $limit=0) {

        $rewardTableName = $this->info('name');

        $select = $this->select()->from($rewardTableName, '*');
        $select->where('project_id = ?', $project_id);
        $select->order($rewardTableName . '.pledge_amount ASC');

        if (!empty($limit)) {
            $select->limit($limit);
        }

        return $this->fetchAll($select);
    }

    public function getRewardPaginator($project_id, $limit=0) {
        return Zend_Paginator::factory($this->getRewards($project_id, $limit));
    }

}
