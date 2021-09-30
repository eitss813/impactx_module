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
class Sitepage_Model_DbTable_Partners extends Engine_Db_Table {

    protected $_rowClass = 'Sitepage_Model_Partner';

    public function getPartnerPages($id){
        $page_select = $this->select()
            ->from($this->info('name'))
            ->where('page_id = ?', $id);
        return $this->fetchAll($page_select);
    }

    public function getJoinedAsPartnerPages($id){
        $page_select = $this->select()
            ->from($this->info('name'))
            ->where('partner_page_id = ?', $id)
            ->where('page_id != ?', $id);
        return $this->fetchAll($page_select);
    }

    public function getPartnerPagesCount($id){
        $count = $this->select()
            ->from($this->info('name'), array('count(*) as count'))
            ->where('page_id = ?', $id)
            ->query()
            ->fetchColumn();
        return $count;
    }

    public function getJoinedAndAddedPartnerPages($page_id){
        $select1 = $this->select()
            ->from($this->info('name'), 'partner_page_id as page_id')
            ->where('page_id = ?', $page_id)
            ->where('rejected = ?',0)
            ->where('accepted = ?',1);

        $select2 = $this->select()
            ->from($this->info('name'), 'page_id')
            ->where('partner_page_id = ?', $page_id)
            ->where('rejected = ?',0)
            ->where('accepted = ?',1);

        $select = $this->select()->union(array($select1, $select2));

        $page_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        return $page_ids;
    }

}
