<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectGateways.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Sdggoals  extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Sdggoal';

    public function getSDGGoals(){
        $select = $this->select();
        $result = $this->fetchAll($select);
        if(!empty($result)){
            $result = $result->toArray();
            $goals = array();
            foreach ($result as $value){
                $goals[$value['sdg_goal_id']] = $value['sdg_goal_id'].'. ' .$value['title'];
            }
            return $goals;

        }
        return array();
    }


}
