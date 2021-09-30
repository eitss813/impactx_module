<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Playlistmaps.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Playlistmaps extends Engine_Db_Table {

    protected $_rowClass = 'Sitevideo_Model_Playlistmap';

    //THIS FUNCTION IS USED TO RETURN THE PLAYLIST MAP RECORDS IN PAGINATION
    public function playlistListings($playlist_id, $params = null) {

        //RETURN IF PLAYLIST ID IS EMPTY
        if (empty($playlist_id)) {
            return;
        }
        //GET PLAYLISTMAPS TABLE NAME
        $playlistListingTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->from($playlistListingTableName, '*')
                ->where($playlistListingTableName . ".playlist_id = ?", $playlist_id);

        if (isset($params['orderby']) && $params['orderby'] == 'random') {
            $select->order('RAND()');
        } else if (isset($params['orderby']) && !empty($params['orderby'])) {
            $select->order("$playlistListingTableName." . $params['orderby'] . " DESC");
        } else {
            $select->order($playlistListingTableName . '.creation_date' . " DESC");
        }
        //RETURN RESULTS
        return Zend_Paginator::factory($select);
    }

}
