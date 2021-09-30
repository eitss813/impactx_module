<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Itemofthedays.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Itemofthedays extends Engine_Db_Table {

  protected $_rowClass = "Sitealbum_Model_Itemoftheday";

  /*
   * GET LIST OF ALBUMS WHICH SET FOR 'ALBUM OF DAY'
   */

  public function getAlbumOfDayList($params = array()) {

    $itemofthedayName = $this->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');

    $select = $albumTable->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($albumName, array('album_id', 'photo_id', 'title'))
            ->join($itemofthedayName, $albumName . '.album_id = ' . $itemofthedayName . '.resource_id', array('itemoftheday_id','start_date', 'end_date'))
            ->where('resource_type = ?', 'album');
    $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
    return $paginator = Zend_Paginator::factory($select);
  }

  /*
   * GET ALBUM OF DAY
   */

  public function getAlbumOfDay() {

    $date = date('Y-m-d');
    $itemofthedayName = $this->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');

    $select = $albumTable->select()
            ->setIntegrityCheck(false)
            ->from($albumName, array('album_id', 'photo_id', 'title', 'owner_id', 'like_count', 'view_count', 'comment_count', 'rating', 'category_id', 'location', 'seao_locationid', 'creation_date','photos_count'))
            ->join($itemofthedayName, $albumName . '.album_id = ' . $itemofthedayName . '.resource_id', null)
            ->where('search = ?', true)
            ->where('resource_type = ?', 'album')
            ->where('start_date <= ?', $date)
            ->where('end_date >= ?', $date)
            ->order('Rand()');
    $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select);
    return $albumTable->fetchRow($select);
  }

  /*
   * GET LIST OF PHOTOS WHICH SET FOR 'PHOTO OF DAY'
   */

  public function getPhotoOfDayList($params = array()) {

    $itemofthedayName = $this->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
    $photoName = $photoTable->info('name');
    $select = $photoTable->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($photoName, array('album_id', 'photo_id', 'file_id'))
            ->join($itemofthedayName, $photoName . '.photo_id = ' . $itemofthedayName . '.resource_id', array('start_date', 'end_date', 'itemoftheday_id'));

    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $photoName . '.album_id = ' . $albumName . '.album_id', array($albumName . '.title as ' . $albumName . '.title'));
    } else {
      $select->join($albumName, $photoName . '.collection_id = ' . $albumName . '.album_id', array($albumName . '.title as ' . $albumName . '.title'));
    }
    $select->where('resource_type = ?', 'album_photo');
    $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
    return $paginator = Zend_Paginator::factory($select);
  }

  /*
   * GET PHOTO OF DAY
   */

  public function getPhotoOfDay() {

    $date = date('Y-m-d'); 
    $itemofthedayName = $this->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
    $photoName = $photoTable->info('name');
    $select = $photoTable->select()
            ->setIntegrityCheck(false)
            ->from($photoName, array('album_id', 'photo_id', 'file_id', 'owner_id', 'like_count', 'view_count', 'comment_count', 'rating', 'location', 'seao_locationid', 'creation_date', 'title'))
            ->join($itemofthedayName, $photoName . '.photo_id = ' . $itemofthedayName . '.resource_id', null);
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $photoName . '.album_id = ' . $albumName . '.album_id', array($albumName . '.title as ' . $albumName . '.title'));
    } else {
      $select->join($albumName, $photoName . '.collection_id = ' . $albumName . '.album_id', array($albumName . '.title as ' . $albumName . '.title'));
    }
    $select->where('search = ?', true)
            ->where('resource_type = ?', 'album_photo')
            ->where('start_date <= ?', $date)
            ->where('end_date >= ?', $date)
            ->order('Rand()'); 
    $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
    return $photoTable->fetchRow($select);
  }

  public function getItem($resource_type, $resource_id) {
    $select = $this->select()
            ->where('resource_type = ?', $resource_type)
            ->where('resource_id = ?', $resource_id);
    return $row = $this->fetchRow($select);
  }
}