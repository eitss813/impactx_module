<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Topics extends Engine_Db_Table {

    protected $_rowClass = 'Sitevideo_Model_Topic';

    public function getChannelTopices($lisiibg_id) {
        try {
            //MAKE QUERY 
            $select = $this->select()
                    ->where('channel_id = ?', $lisiibg_id)
                    ->order('sticky DESC')
                    ->order('modified_date DESC');
            //RETURN RESULTS
            return Zend_Paginator::factory($select);
        } catch (Exception $e) {
            echo $e;
            die();
        }
    }

}
