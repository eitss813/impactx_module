<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Emails.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Model_DbTable_Emails extends Engine_Db_Table {

    protected $_rowClass = "Sesnewsletter_Model_Email";

    public function getResult() {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)
                ->where('stop =?', 1)
                ->order('email_id ASC');
        return $this->fetchAll($select);
    }
}
