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
class Sitepage_Model_DbTable_Initiativemetrics extends Engine_Db_Table {

    protected $_rowClass = 'Sitepage_Model_Initiativemetric';

    public function getAllInitiativesMetricByIdPage($page_id,$initiativeId,$index){

        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('initiative_id = ?', $initiativeId)
            ->order('updated_date DESC')
            ->limit(2,$index);

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getAllInitiativesMetricById($page_id,$initiativeId){

        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('initiative_id = ?', $initiativeId)
            ->order('updated_date DESC');

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getAllInitiativesMetricByIdPaginator($page_id,$initiativeId){
        return Zend_Paginator::factory($this->getAllInitiativesMetricByIdSelect($page_id,$initiativeId));
    }

    public function getAllInitiativesMetricByIdSelect($page_id,$initiativeId){
        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('initiative_id = ?', $initiativeId)
            ->order('updated_date DESC');
        return $select;
    }


}
