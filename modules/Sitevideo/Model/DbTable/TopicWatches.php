<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicWatches.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_TopicWatches extends Engine_Db_Table {

    public function isWatching($resource_id, $topic_id, $user_id) {

        $select = $this
                ->select()
                ->from($this->info('name'), 'watch')
                ->where('resource_id = ?', $resource_id)
                ->where('topic_id = ?', $topic_id)
                ->where('user_id = ?', $user_id)
                ->limit(1);

        $result = $this->fetchRow($select);
        if ($result)
            return $result->watch;
        else
            return 0;
    }

    public function getNotifyUserIds($params = array()) {

        return $this->select()
                        ->from($this->info('name'), 'user_id')
                        ->where('resource_id = ?', $params['channel_id'])
                        ->where('topic_id = ?', $params['topic_id'])
                        ->where('watch = ?', 1)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

}
