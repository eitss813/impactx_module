<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Itemofthedays.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Itemofthedays extends Engine_Db_Table {

    protected $_rowClass = "Sitevideo_Model_Itemoftheday";

    /*
     * GET LIST OF CHANNELS WHICH SET FOR 'CHANNEL OF DAY'
     */

    public function getChannelOfDayList($params = array()) {

        $itemofthedayName = $this->info('name');
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelName = $channelTable->info('name');

        $select = $channelTable->select();
        $select = $select
                ->setIntegrityCheck(false)
                ->from($channelName, array('channel_id', 'video_id', 'title'))
                ->join($itemofthedayName, $channelName . '.channel_id = ' . $itemofthedayName . '.resource_id', array('itemoftheday_id', 'start_date', 'end_date'))
                ->where('resource_type = ?', 'sitevideo_channel');
        $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
        return $paginator = Zend_Paginator::factory($select);
    }

    /*
     * GET CHANNEL OF DAY
     */

    public function getChannelOfDay() {

        $date = date('Y-m-d');
        $itemofthedayName = $this->info('name');
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelName = $channelTable->info('name');

        $select = $channelTable->select()
                ->setIntegrityCheck(false)
                ->from($channelName, array('channel_id', 'video_id', 'title', 'owner_id', 'like_count', 'view_count', 'comment_count', 'rating', 'category_id', 'location', 'seao_locationid', 'creation_date', 'videos_count'))
                ->join($itemofthedayName, $channelName . '.channel_id = ' . $itemofthedayName . '.resource_id', null)
                ->where('search = ?', true)
                ->where('resource_type = ?', 'sitevideo_channel')
                ->where('start_date <= ?', $date)
                ->where('end_date >= ?', $date)
                ->order('Rand()');
        $select = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getNetworkBaseSql($select);
        return $channelTable->fetchRow($select);
    }

    /*
     * GET LIST OF VIDEOS WHICH SET FOR 'VIDEO OF DAY'
     */

    public function getVideoOfDayList($params = array()) {

        $itemofthedayName = $this->info('name');
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelName = $channelTable->info('name');
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoName = $videoTable->info('name');
        $select = $videoTable->select();
        $select = $select
                ->setIntegrityCheck(false)
                ->from($videoName, array('main_channel_id', 'video_id', 'file_id'))
                ->join($itemofthedayName, $videoName . '.video_id = ' . $itemofthedayName . '.resource_id', array('start_date', 'end_date', 'itemoftheday_id'));

        if (!Engine_Api::_()->sitevideo()->isLessThan417ChannelModule()) {
            $select->join($channelName, $videoName . '.main_channel_id = ' . $channelName . '.channel_id', array($channelName . '.title as ' . $channelName . '.title'));
        } else {
            $select->join($channelName, $videoName . '.collection_id = ' . $channelName . '.channel_id', array($channelName . '.title as ' . $channelName . '.title'));
        }
        $select->where('resource_type = ?', 'sitevideo_video');
        $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
        return $paginator = Zend_Paginator::factory($select);
    }

    /*
     * GET VIDEO OF DAY
     */

    public function getVideoOfDay() {

        $date = date('Y-m-d');
        $itemofthedayName = $this->info('name');
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelName = $channelTable->info('name');
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoName = $videoTable->info('name');
        $select = $videoTable->select()
                ->setIntegrityCheck(false)
                ->from($videoName, array('main_channel_id', 'video_id', 'file_id', 'owner_id', 'like_count', 'view_count', 'comment_count', 'rating', 'location', 'seao_locationid', 'creation_date', 'title'))
                ->join($itemofthedayName, $videoName . '.video_id = ' . $itemofthedayName . '.resource_id', null);
        if (!Engine_Api::_()->sitevideo()->isLessThan417ChannelModule()) {
            $select->join($channelName, $videoName . '.main_channel_id = ' . $channelName . '.channel_id', array($channelName . '.title as ' . $channelName . '.title'));
        } else {
            $select->join($channelName, $videoName . '.collection_id = ' . $channelName . '.channel_id', array($channelName . '.title as ' . $channelName . '.title'));
        }
        $select->where('search = ?', true)
                ->where('resource_type = ?', 'sitevideo_video')
                ->where('start_date <= ?', $date)
                ->where('end_date >= ?', $date)
                ->order('Rand()');
        $select = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        return $videoTable->fetchRow($select);
    }

    public function getItem($resource_type, $resource_id) {
        $select = $this->select()
                ->where('resource_type = ?', $resource_type)
                ->where('resource_id = ?', $resource_id);
        return $row = $this->fetchRow($select);
    }

}
