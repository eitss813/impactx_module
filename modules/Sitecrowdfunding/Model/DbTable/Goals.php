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
class Sitecrowdfunding_Model_DbTable_Goals extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Goal';

    public function getAllGoalsByProjectId($project_id){

        $select = $this->select()
            ->where('project_id = ?', $project_id)
            ->order('sdg_goal_id ASC');
            //->group('sdg_goal_id');

        $result =   $select->query()->fetchAll();

        $newresult = [];

        foreach ($result as $key => $value){
            $temp = $value;
            $goal =  Engine_Api::_()->getItem('sitecrowdfunding_sdggoal', $value['sdg_goal_id']);
            $target = Engine_Api::_()->getItem('sitecrowdfunding_sdgtarget', $value['sdg_target_id']);
            $temp['goal'] = $goal['title'];
            $temp['target'] = $target['sdg_target_sno'].'. '.$target['title'];
            array_push($newresult, $temp);
        }

        return $newresult;

    }

    public function getAllGoalsByProjectIds($project_ids){

        $select = $this->select()
            ->where('project_id in(?)', $project_ids)
            ->order('sdg_goal_id ASC');

        $result =   $select->query()->fetchAll();

        $newresult = [];

        foreach ($result as $key => $value){
            $temp = $value;
            $goal =  Engine_Api::_()->getItem('sitecrowdfunding_sdggoal', $value['sdg_goal_id']);

            $targetTable = Engine_Api::_()->getDbtable('sdgtargets', 'sitecrowdfunding');
            $targetSelect = $targetTable->select()
                ->where('sdg_goal_id = ? ', $value['sdg_goal_id'])
                ->where('sdg_target_id = ? ', $value['sdg_target_id'])
                ->limit(1);
            $target = $targetTable->fetchRow($targetSelect);

            $temp['goal'] = $goal['title'];
            $temp['target'] = $target['sdg_target_sno'].'. '.$target['title'];
            array_push($newresult, $temp);
        }

        return $newresult;

    }

    public function getGoalTable(){
        return $this;
    }

    public function getAllGoalsCountByProjectId($project_id){
            $select = new Zend_Db_Select($this->getGoalTable()->getAdapter());
            $select
                ->from($this->getGoalTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

            $select->where('project_id = ?', $project_id);

            $data = $select->query()->fetchAll();
            return (int) $data[0]['count'];
    }
}
