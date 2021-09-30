<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Playlist.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Playlist extends Core_Model_Item_Abstract {

    protected $_type = 'sitevideo_playlist';

    /*
     * THIS FUNCTION USED TO GET THE TITLE OF PLAYLIST
     */

    public function getTitle() {
        return $this->title;
    }

    /*
     * THIS FUNCTION USED TO FIND THE PLAYLISTS TABLE MODEL
     */

    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
        }
        return $this->_table;
    }

    /*
     * Used to save the playlist thumnails
     */

    public function setPhoto($photo) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new Zend_Exception("invalid argument passed to setPhoto");
        }

        if (!$fileName) {
            $fileName = basename($file);
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'sitevideo_playlist',
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->owner_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($mainPath)
                ->destroy();

        // Resize image (profile)
        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($profilePath)
                ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($normalPath)
                ->destroy();

        // Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($squarePath)
                ->destroy();

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iSquare = $filesTable->createFile($squarePath, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');
        $iMain->bridge($iMain, 'thumb.main');
        // Remove temp files
        @unlink($mainPath);
        @unlink($profilePath);
        @unlink($normalPath);
        @unlink($squarePath);
        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->file_id = $iMain->getIdentity();
        $this->save();
        return $this;
    }

    /*
     * USED TO GENERATE THE URL FOR PLAYLIST VIEW PAGE
     */

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'sitevideo_playlist_view',
            'reset' => true,
            'slug' => $this->getSlug(),
            'playlist_id' => $this->getIdentity(),
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $maxstrlen = 64) {

        if (null === $str) {
            $str = $this->title;
        }

        $maxstrlen = 225;

        return Engine_Api::_()->seaocore()->getSlug($str, $maxstrlen);
    }

    /*
     * USED TO FIND THE PHOTO URL
     */

    public function getPhotoUrl($type = null) {
        $photo_id = $this->file_id;
        if (!$photo_id) {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

    public function getType($inflect = false) {
        if ($inflect) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_type)));
        }

        return $this->_type;
    }

    /*
     * FIND THE PLAYLIST MAP RECORDS FOR GIVEN PLAYLIST
     */

    public function getPlaylistMap($params = array()) {

        $paginator = Engine_Api::_()->getDbTable('playlistmaps', 'sitevideo')->playlistListings($this->playlist_id, $params);
        if (isset($params['limit']) && $params['limit'] > 0)
            $paginator->setItemCountPerPage($params['limit']);
        return $paginator;
    }

    /*
     * THIS FUNCTION IS USED TO CHECK WEATHER VIDEO IS ADDED INTO PLAYLIST OR NOT.
     */

    public function isVideoAdded($video_id) {
        $playlistmap = new Sitevideo_Model_DbTable_Playlistmaps();
        //CHECKING VIDEO IS ADDED INTO PLAYLIST
        $playlistmapModel = $playlistmap->fetchRow($playlistmap->select()
                        ->where('playlist_id = ?', $this->playlist_id)
                        ->where('video_id = ?', $video_id));
        //IF YES THEN RETURN TRUE
        if ($playlistmapModel)
            return true;
        return false;
    }

    /*
     * THIS FUNCTION IS USED TO FIND ALL THE PLAYLIST MAP RECORDS OF GIVEN PLAYLIST
     */

    public function getPlaylistAllMap($params = array()) {
        $playlistmap = new Sitevideo_Model_DbTable_Playlistmaps();
        $playlistmapTable = $playlistmap->info('name');
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoTableName = $videoTable->info('name');
        $select = $videoTable->select()
                ->from($videoTableName, array("$videoTableName.*", "$playlistmapTable.*"));
        
        $select->setIntegrityCheck(false)
                    ->joinLeft($playlistmapTable, "$playlistmapTable.video_id=$videoTableName.video_id", null);
        
        if (isset($params['orderby']) && $params['orderby'] == 'video_type') {
            $select->order("FIELD(type,5,6,7,8)");
        }
        
        $select->where('playlist_id = ?', $this->playlist_id);
        $select->order("$playlistmapTable.creation_date asc");

        //FIND THE PLAYLIST MAP OF A GIVEN PLAYLIST
        $playlistmapModel = $playlistmap->fetchAll($select);
        //IF NO RECORDS FOUND THE RETURN BLANK ARRAY
        if (!$playlistmapModel)
            return array();

        return $playlistmapModel;
    }

    public function canEdit() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity())
            return false;
        if (($this->owner_id != $viewer->getIdentity()))
            return false;
        return true;
    }
    /**
     * Delete the playlist and belongings
     * 
     */
    public function _delete() {

      //DELETE ALL MAPPING VALUES FROM wishlistmaps TABLE
      Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo')->delete(array('playlist_id = ?' => $this->playlist_id));

      //DELETE playlist
      parent::_delete();
    }

}
