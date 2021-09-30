<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Playlists.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Playlists extends Engine_Db_Table {

    protected $_name = 'sitevideo_playlists';
    protected $_rowClass = 'Sitevideo_Model_Playlist';
    protected $_categories = array();

    /*
     * THIS FUNCTION IS USED TO GET THE TITLE OF PLAYLIST
     */

    public function getTitle() {
        return $this->title;
    }

    /*
     * THIS FUNCTION IS USED TO RETURN THE PLAYLIST DETAILS IN PAGINATION 
     */

    public function getPlaylistPaginator(array $params) {
        return Zend_Paginator::factory($this->getPlaylistSelect($params));
    }

    /*
     * MAKE A QUERY FOR PLAYLIST TABLE ACCOURDING TO REQUESTED PARAMETER
     */

    public function getPlaylistSelect(array $params) {

        $playlistTableName = $this->info('name');
        $playlistMapsTable = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
        $playlistMapsTableName = $playlistMapsTable->info('name');
        $videosTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videosTableName = $videosTable->info('name');
        $select = $this->select()->setIntegrityCheck(false)->from($playlistTableName, '*');
        if (!empty($params['owner_id']))
            $select->where("$playlistTableName.owner_id = ?", $params['owner_id']);
        if (isset($params['playlistOrder']) && $params['playlistOrder'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$playlistTableName.creation_date DESC");
        }
        if (isset($params['itemCountPerPage']) && !empty($params['itemCountPerPage'])) {
            $select->limit($params['itemCountPerPage']);
        }

        if ((isset($params['search']) && !empty($params['search'])) || (isset($params['video_title']) && !empty($params['video_title']))) {
            $select->joinLeft($playlistMapsTableName, "$playlistMapsTableName.playlist_id=$playlistTableName.playlist_id", null);
            $select->joinLeft($videosTableName, "$videosTableName.video_id=$playlistMapsTableName.video_id", null);
        }
        if (isset($params['search']) && !empty($params['search']) && isset($params['video_title']) && !empty($params['video_title'])) {
            $select->where("lower($playlistTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
            $select->where("lower($videosTableName.title) like ? ", '%' . strtolower($params['video_title']) . '%');
        } else if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($playlistTableName.title) like ?", '%' . strtolower($params['search']) . '%', '%' . strtolower($params['search']) . '%');
        } elseif (isset($params['video_title']) && !empty($params['video_title'])) {
            $select->where("lower($videosTableName.title) like ? ", '%' . strtolower($params['video_title']) . '%');
        }
        if (isset($params['browsePrivacy']) && $params['browsePrivacy']) {
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $select->where("$playlistTableName.privacy = ? or $playlistTableName.owner_id = ".$viewer->getIdentity(), $params['browsePrivacy']);
            } else
                $select->where("$playlistTableName.privacy = ?", $params['browsePrivacy']);
        }
        if (isset($params['membername']) && !empty($params['membername'])) {
            $text = strtolower($params['membername']);
            $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
            $select->joinLeft($tableUserName, "$playlistTableName.owner_id=$tableUserName.user_id", array("user_id"))
                    ->where("lower($tableUserName.username) LIKE '%$text%' OR lower($tableUserName.displayname) LIKE '%$text%' OR lower($tableUserName.email) LIKE '$text'");
        }
        if ((isset($params['search']) && !empty($params['search'])) || (isset($params['video_title']) && !empty($params['video_title'])) || (isset($params['name']) && !empty($params['name']))) {
            $select->group("$playlistTableName.playlist_id");
        }
        return $select;
    }

}
