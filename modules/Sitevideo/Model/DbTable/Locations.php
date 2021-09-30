<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locations.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Locations extends Engine_Db_Table {

    protected $_rowClass = "Sitevideo_Model_Location";

    /**
     * Get location
     *
     * @param array $params
     * @return object
     */
    public function getLocation($params = array()) {


        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {

            $locationName = $this->info('name');

            $select = $this->select();
            if (isset($params['id'])) {
                $select->where('video_id = ?', $params['id']);
                return $this->fetchRow($select);
            }

            if (isset($params['video_ids'])) {
                $select->where('video_id IN (?)', (array) $params['video_ids']);
                return $this->fetchAll($select);
            }
        }
    }

}
