<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Milestones extends Engine_Db_Table
{

    protected $_rowClass = 'Sitecrowdfunding_Model_Milestone';


    public function getMilestoneTable(){
        return $this;
    }

    public function getAllMilestonesByProjectId($project_id){
        //MAKE QUERY
        $select = $this->select()
            ->where('project_id = ?', $project_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();

        $milestones = array();
        foreach ($result as $value){
            $item = Engine_Api::_()->getItem('sitecrowdfunding_milestone', $value['milestone_id']);
            $temp = [];
            $temp['milestone_id'] = $value['milestone_id'];
            $temp['project_id'] = $value['project_id'];
            $temp['title'] = $value['title'];
            $temp['description'] = $value['description'];
            $temp['question'] = $value['question'];
            $temp['logo'] = $item->getLogoUrl('thumb.profile');
            $temp['start_date'] = $value['start_date'];
            $temp['end_date'] = $value['end_date'];
            $temp['status'] = $value['status'];
            array_push($milestones, $temp);
        }
        return $milestones;
    }

    public function getMileStoneTotalCountByProjectId($project_id){

        $select = new Zend_Db_Select($this->getMilestoneTable()->getAdapter());
        $select
            ->from($this->getMilestoneTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

        $select->where('project_id = ?', $project_id);

        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
    }
}