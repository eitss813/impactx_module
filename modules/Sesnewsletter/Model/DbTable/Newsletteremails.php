<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Newsletteremails.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Model_DbTable_Newsletteremails extends Engine_Db_Table {

    protected $_rowClass = "Sesnewsletter_Model_Newsletteremail";

    public function getResult($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)->order('newsletteremail_id ASC');
        if(!empty($param['campaign_id']))
            $select->where('campaign_id =?', $param['campaign_id']);
        return $this->fetchAll($select);
    }
}
