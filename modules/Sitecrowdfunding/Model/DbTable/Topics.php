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
class Sitecrowdfunding_Model_DbTable_Topics extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Topic';

    public function getProjectTopics($lisiibg_id) {

        //MAKE QUERY
        $select = $this->select()
                ->where('project_id = ?', $lisiibg_id)
                ->order('sticky DESC')
                ->order('modified_date DESC');

        //RETURN RESULTS
        return Zend_Paginator::factory($select);
    }

}