<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Photos extends Engine_Db_Table {

    protected $_rowClass = 'Sitevideo_Model_Photo';

    public function getPhotoId($channel_id = null, $file_id = null) {

        $photo_id = 0;
        $photo_id = $this->select()
                ->from($this->info('name'), array('photo_id'))
                ->where("channel_id = ?", $channel_id)
                ->where("file_id = ?", $file_id)
                ->query()
                ->fetchColumn();

        return $photo_id;
    }

    /**
     * Return photos
     *
     * @param string $channel_id
     * @return photos
     */
    public function GetEventPhoto($channel_id, $params = array()) {

        $select = $this->select()
                ->where('channel_id = ?', $channel_id);
        if (isset($params['limit']) && !empty($params['limit']))
            $select->limit($params['limit']);

        if (isset($params['order']) && !empty($params['order']))
            $select->order($params['order']);
        return $this->fetchAll($select)->toArray();
    }

}
