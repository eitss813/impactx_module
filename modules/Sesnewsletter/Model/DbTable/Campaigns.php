<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Campaigns.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */


class Sesnewsletter_Model_DbTable_Campaigns extends Engine_Db_Table {

    protected $_rowClass = "Sesnewsletter_Model_Campaign";

    public function getResult($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)->order('campaign_id DESC');

        if(!empty($param['taskrun']) && $param['taskrun'] == 1) {
            $select->where('status =?', 1)->where('publish_date <= ?', date('Y-m-d'));
        }

        if (isset($param['fetchAll'])) {
            $select->where('enabled =?', 1);
            return $this->fetchAll($select);
        }
        return Zend_Paginator::factory($select);
    }
}
