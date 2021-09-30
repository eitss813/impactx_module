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
class Sitecrowdfunding_Model_DbTable_Initiativeanswers extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Initiativeanswer';

    public function getAllInitiativesAnswersByIds($project_id,$initiative_id){

        $select = $this->select()
            ->where('project_id = ?', $project_id)
            ->where('initiative_id = ?', $initiative_id)
            ->order('creation_date DESC')
            ->order('updated_date DESC');

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getInitiativeAnswerRow($project_id,$initiative_id,$initiative_question_id){
        $select = $this->select();
        $select->where('project_id = ?', $project_id);
        $select->where('initiative_id = ?', $initiative_id);
        $select->where('initiativequestion_id = ?', $initiative_question_id);
        return $this->fetchRow($select);
    }

    public function getProjectInitiativeAnswers($project_id){
        $select = $this->select()
            ->where('project_id = ?', $project_id)
            ->order('creation_date DESC')
            ->order('updated_date DESC');

        $result =  $select->query()->fetchAll();

        return $result;
    }

}
