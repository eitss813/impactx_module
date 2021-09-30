<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Watchlaters.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Watchlaters extends Engine_Db_Table {

    protected $_rowClass = 'Sitevideo_Model_Watchlater';

    //RETURN WATCHLATER RESULT SET IN PAGINATION
    public function getWatchlaterPaginator(array $params) {
        return Zend_Paginator::factory($this->getWatchlaterSelect($params));
    }

    //MAKE A QUERY TO SELECT THE WATCHLATER ON GIVEN PARAMETER
    public function getWatchlaterSelect(array $params) {

        $watchlatersTableName = $this->info('name');
        $videosTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videosTableName = $videosTable->info('name');
        $select = $this->select()->from($watchlatersTableName, '*');
        if (!empty($params['owner_id']))
            $select->where("$watchlatersTableName.owner_id = ?", $params['owner_id']);

        if (isset($params['watchlaterOrder']) && $params['watchlaterOrder'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$watchlatersTableName.creation_date");
        }
        if (isset($params['itemCountPerPage']) && !empty($params['itemCountPerPage'])) {
            $select->limit($params['itemCountPerPage']);
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->joinLeft($videosTableName, "$videosTableName.video_id=$watchlatersTableName.video_id", null);
            $select->where("lower($videosTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$watchlatersTableName.watchlater_id");
        }
        //RETURN QUERY
        return $select;
    }

    public function getUserWatchStats($owner_id) {

        $stats[] = array();
        $select = $this->select()
                ->from($this->info('name'))
                ->where('owner_id = ?', $owner_id);
        return count($this->fetchAll($select));
    }

}
