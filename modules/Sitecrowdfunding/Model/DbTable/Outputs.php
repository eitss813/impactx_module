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
class Sitecrowdfunding_Model_DbTable_Outputs extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Output';

    public function getOutputTable(){
        return $this;
    }

    public function getAllOutputsByProjectId($project_id){

        $select = $this->select()
            ->where('project_id = ?', $project_id);

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getOutputTotalCountByProjectId($project_id){

        $select = new Zend_Db_Select($this->getOutputTable()->getAdapter());
        $select
            ->from($this->getOutputTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

        $select->where('project_id = ?', $project_id);

        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
    }

}
