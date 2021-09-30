<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Videootherinfo.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Videootherinfo extends Engine_Db_Table {

    protected $_rowClass = "Sitevideo_Model_Videootherinfo";

    public function getOtherinfo($video_id) {

        $rName = $this->info('name');
        $select = $this->select()
                ->where($rName . '.video_id = ?', $video_id);

        $row = $this->fetchRow($select);

        if (empty($row))
            return;

        return $row;
    }

    public function getColumnValue($video_id, $column_name) {

        return $this->select()
                        ->from($this->info('name'), array("$column_name"))
                        ->where('video_id = ?', $video_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

    public function getOtherinfoColumns($params) {

        $select = $this->select()
                ->from($this->info('name'), $params['columns'])
                ->where('video_id = ?', $params['video_id']);

        $row = $this->fetchRow($select);

        if (empty($row))
            return;

        return $row;
    }

}
