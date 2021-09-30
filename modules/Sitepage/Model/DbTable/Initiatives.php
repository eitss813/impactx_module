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
class Sitepage_Model_DbTable_Initiatives extends Engine_Db_Table {

    protected $_rowClass = 'Sitepage_Model_Initiative';


    public function getAllInitiativesByPageIdPaginator($page_id){
        return Zend_Paginator::factory($this->getAllInitiativesByPageIdSelect($page_id));
    }

    public function getAllInitiativesByPageIdsPaginator($page_ids){
        return Zend_Paginator::factory($this->getAllInitiativesByPageIdsSelect($page_ids));
    }

    public function getAllInitiativesByPageIdSelect($page_id){
        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->order('initiative_order ASC');
        return $select;
    }

    public function getAllInitiativesByPageIdsSelect($page_ids){
        $select = $this->select()
            ->where('page_id in (?)', $page_ids)
            ->order('initiative_order ASC');
        return $select;
    }

    public function getAllInitiativesByPageId($page_id){

        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->order('initiative_order ASC');

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getAllInitiativesByPageIds($page_ids){

        $select = $this->select()
            ->where('page_id in (?)', $page_ids)
            ->order('initiative_order ASC');

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getInitiativesCountByPageId($page_id){
        $count = $this->select()
            ->from($this->info('name'), new Zend_Db_Expr('COUNT(initiative_id)'))
            ->where('page_id = ?', $page_id)
            ->order('initiative_order ASC')
            ->query()
            ->fetchColumn();
        return $count;
    }


}
