<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Albums.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_DbTable_Albums extends Engine_Db_Table {

  protected $_rowClass = 'Sesblog_Model_Album';

  public function getUserAlbumCount($params = array()){
    return $this->select()->from($this->info('name'), new Zend_Db_Expr('COUNT(album_id) as total_albums'))->where('blog_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())->limit(1)->query()->fetchColumn();
  }

  public function getAlbumSelect($value = array()){
    // Prepare data
    $albumTableName = $this->info('name');
    $select = $this->select()
		    ->from($albumTableName)
		    ->where('search =?',1)
		    ->where($albumTableName.'.blog_id =?',$value['blog_id'])
		    ->group($albumTableName.'.album_id');
    return Zend_Paginator::factory($select);
  }

	public function editPhotos(){
		$albumTable = Engine_Api::_()->getItemTable('sesblog_album');
		$myAlbums = $albumTable->select()
				->from($albumTable, array('album_id', 'title'))
				->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
				->query()
				->fetchAll();
		return $myAlbums;
	}
	
  public function getItemCount($params = array()) {
    $select = $this->select()->from($this->info('name'), 'count(*) AS total');
    return $select->query()->fetchColumn();
  }
}
