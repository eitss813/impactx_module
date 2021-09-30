<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Albums.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Albums extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Album';
  protected $_serializedColumns = array('cover_params');
  public function getSpecialAlbum(Sitepage_Model_Page $page, $type) {

    if (!in_array($type, array('note', 'overview','wall', 'announcements', 'discussions','cover'))) {
      throw new Sitepage_Model_Exception('Unknown special album type');
    }
    $page_id = $page->page_id;
    $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);
    $album = $this->fetchRow($select);

    //CREATE PHOTOS ALBUM IF IT DOESN't EXIST YET
    if (null === $album) {
      $album = $this->createRow();
      $album->default_value = 0;
      $album->page_id = $page_id;
      $album->owner_id = $page->owner_id;
      $album->title = Zend_Registry::get('Zend_Translate')->_(ucfirst($type) . ' Photos');
      $album->type = $type;
      if ($type == 'message') {
        $album->search = 0;
      } else {
        $album->search = 1;
      }
      $album->save();
    }
    return $album;
  }

  /**
   * Gets album for the pages
   *
   * @param array params
   * @return albums for pages
   */   
  public function getAlbums($params = array(),$widgetType = null, $fetchColumns = array()) {
  
		$select = $this->select();		
    $albumTableName = $this->info('name');
    
    if($widgetType == 'browsealbum' || $widgetType == 'sponsored') {						
			$pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
			$pageTableName = $pageTable->info('name');

			$pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
			$pagePackageTableName = $pagePackagesTable->info('name');
      $select->setIntegrityCheck(false);
			$select->from($pageTableName, array('photo_id', 'title as sitepage_title'));
      
      if(!empty($fetchColumns)) {
          $select->join($albumTableName, $albumTableName . '.page_id = ' . $pageTableName . '.page_id', $fetchColumns);
      }
      else {
          $select->join($albumTableName, $albumTableName . '.page_id = ' . $pageTableName . '.page_id');
      }
      
			$select->join($pagePackageTableName, "$pagePackageTableName.package_id = $pageTableName.package_id",array('package_id', 'price'))
              ->group("$albumTableName.album_id");
      if (!empty($params['title'])) {
				$select->where($pageTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
      }

      if (!empty($params['search_album'])) {
				$select->where($albumTableName . ".title LIKE ? " , '%' . $params['search_album'] . '%');
      }

      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			if(isset($params['show']) && $params['show'] == 'my_album') {
				$select->where($albumTableName . '.owner_id = ?', $viewer_id);
			}
			elseif ((isset($params['show']) && $params['show'] == 'sponsored album') || !empty($params['sponsoredalbum']) || ($widgetType == 'sponsored')) {
					
					$select->where($pagePackageTableName . '.price != ?', '0.00');
					$select->order($pagePackageTableName . '.price' . ' DESC');
			}
      elseif ((isset($params['show']) && $params['show'] == 'featured')) {
					$select->where($albumTableName . '.featured = ?', 1);
			}
			elseif (isset($params['show']) && $params['show'] == 'Networks') {
					$select = $pageTable->getNetworkBaseSql($select, array('browse_network' => 1));

			}
			elseif (isset($params['show']) && $params['show'] == 'my_like') {
				$likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
				$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
				$select
								->join($likeTableName, "$likeTableName.resource_id = $pageTableName.page_id")
								->where($likeTableName . '.poster_type = ?', 'user')
								->where($likeTableName . '.poster_id = ?', $viewer_id)
								->where($likeTableName . '.resource_type = ?', 'sitepage_page');
			}
			
      if ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'view_count')) {
			$select = $select
											->order($albumTableName .'.view_count DESC')
											->order($albumTableName .'.creation_date DESC');
			} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'like_count')) {
				$select = $select
												->order($albumTableName .'.like_count DESC')
												->order($albumTableName .'.creation_date DESC');
			}

			if (!empty($params['category'])) {
			$select->where($pageTableName . '.category_id = ?', $params['category']);
			}

			if (!empty($params['category_id'])) {
				$select->where($pageTableName . '.category_id = ?', $params['category_id']);
			}

			if (!empty($params['subcategory'])) {
				$select->where($pageTableName . '.subcategory_id = ?', $params['subcategory']);
			}

			if (!empty($params['subcategory_id'])) {
				$select->where($pageTableName . '.subcategory_id = ?', $params['subcategory_id']);
			}

			if (!empty($params['subsubcategory'])) {
				$select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
			}

			if (!empty($params['subsubcategory_id'])) {
				$select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
			}

      if(empty($params['orderby_browse'])) {
				$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.order', 1);
				switch ($order) {
				case "1":
					$select->order($albumTableName . '.creation_date DESC');
					break;
				case "2":
					$select->order($albumTableName . '.title');
					break;
				case "3":
					$select->order($albumTableName . '.featured' . ' DESC');
					break;
				case "4":
					$select->order($pagePackageTableName . '.price' . ' DESC');
					break;
				case "5":
					$select->order($albumTableName . '.featured' . ' DESC');
					$select->order($pagePackageTableName . '.price' . ' DESC');
					break;
				case "6":
					$select->order($pagePackageTableName . '.price' . ' DESC');
					$select->order($albumTableName . '.featured' . ' DESC');
					break;
			  }  
      }

      $select = $select
                    ->where($pageTableName . '.closed = ?', '0')
                    ->where($pageTableName . '.approved = ?', '1')
                    ->where($pageTableName . '.search = ?', '1')
                    ->where($pageTableName . '.declined = ?', '0')
                    ->where($pageTableName . '.draft = ?', '1');
       
       $select->order($this->info('name'). '.album_id DESC');
       //Start Network work
			if (!isset($params['page_id']) || empty($params['page_id'])) {
				$select = $pageTable->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $albumTableName . ".album_id"));
			}
			//End Network work
    }

		if(isset($params['getSpecialField']) && !empty($params['getSpecialField'])) {
			$select->from($this->info('name'), array('title', 'album_id'));	
		}
	  
		if(isset($params['page_id']) && !empty($params['page_id'])) {
			$select->where($this->info('name'). '.page_id'.'= ?', $params['page_id']);
		}
					
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1) && !isset($params['viewPage'])) {
			$select->where($this->info('name'). '.default_value'.'= ?', 0);
			$select->where($albumTableName . ".type is Null");
    } else {
			if(isset($params['default_value']) && !empty($params['default_value'])) {
				$select->where($this->info('name'). '.default_value'.'= ?', 1);
			} 
    }

		$select->order('order ASC');
		if((isset($params['orderby']) && !empty($params['orderby']))) {
			$select->order($params['orderby']);
		}		
		if(isset($params['getSpecialField']) && !empty($params['getSpecialField'])) {
			$select->group($this->info('name'). '.album_id');
		}
    elseif($widgetType != 'browsealbum' && $widgetType != 'sponsored') {
		$select->from($this->info('name'), array('album_id', 'page_id', 'owner_id', 'order', 'title', 'search', 'photo_id', 'view_count', 'comment_count', 'default_value', 'like_count', 'type', 'modified_date', 'creation_date'));	
 		}
				
		if((isset($params['start']) && !empty($params['start'])) || (isset($params['end']) && !empty($params['end']))) {
			if(!isset($params['end'])) {
				$params['end'] = '';
			}			
			$select->limit($params['start'], $params['end']);
		}

    if(isset($params['limit'])) {
			$select ->limit($params['limit']);
    }

    if($widgetType == 'browsealbum' || (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) || isset($params['cover'])) {
      return Zend_Paginator::factory($select);
    }
    else {
			return $this->fetchAll($select);
    }
	} 	

  /**
   * Gets photo id by order
   *
   * @param int $page_id 
   * @return photo ids
   */
  public function getPageAlbumsOrder($page_id) {
    $currentOrder = $this->select()
            ->from($this->info('name'), 'photo_id')
            ->where('page_id = ?', $page_id)
            ->order('order ASC')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN)
    ;
    return $currentOrder;
  }

  /**
   * Get Default Album of page
   *
   * @param int $page_id
   * @return default album
   */
  public function getDefaultAlbum($page_id) {
    $albumselect = $this->select()
            ->from($this->info('name'), array('album_id', 'photo_id'))
            ->where('page_id = ?', $page_id)
            ->where('default_value = ?', 1);
    return $this->fetchRow($albumselect);
  }

  /**
   * Return album of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function albumOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');
    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablePageName = $tablePage->info('name');
    //GET ITEM OF THE DAY TABLE NAME
    $albumOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->info('name');

    //GET ALBUM TABLE NAME
    $albumTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($albumTableName)
                    ->joinLeft($tablePageName, "$tablePageName.page_id = $albumTableName.page_id", array('title AS page_title', 'photo_id as page_photo_id'))
                    ->join($albumOfTheDayTableName, $albumTableName . '.album_id = ' . $albumOfTheDayTableName . '.resource_id')
                    ->where('resource_type = ?', 'sitepage_album')
                    ->where('start_date <= ?', $date)
                    ->where('end_date >= ?', $date)
                    ->order('Rand()');

    $select = $select
              ->where($tablePageName . '.closed = ?', '0')
              ->where($tablePageName . '.approved = ?', '1')
              ->where($tablePageName . '.declined = ?', '0')
              ->where($tablePageName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }

    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1) ) {
			$select->where($albumTableName. '.default_value'.'= ?', 0);
			$select->where($albumTableName . ".type is Null");
    } 

    //RETURN RESULTS
    return $this->fetchRow($select);
  }

  /**
   * Gets count for the albums
   *
   * @param array params
   * @return count for the albums
   */   
  public function getAlbumsCount($params = array()) {
  
		$select = $this->select();		
    $albumTableName = $this->info('name');

	  $select->from($albumTableName, array('count(*) as count'));
	  
		if(isset($params['page_id']) && !empty($params['page_id'])) {
			$select->where($albumTableName. '.page_id'.'= ?', $params['page_id']);
		}
					
		if((isset($params['start']) && !empty($params['start'])) || (isset($params['end']) && !empty($params['end']))) {
			if(!isset($params['end'])) {
				$params['end'] = '';
			}			
			$select->limit($params['start'], $params['end']);
		}

    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1) && !isset($params['viewPage'])) {
			$select->where($albumTableName. '.default_value'.'= ?', 0);
			$select->where($albumTableName . ".type is Null");
    } else {
			if(isset($params['default_value']) && !empty($params['default_value'])) {
				$select->where($this->info('name'). '.default_value'.'= ?', 1);
			} 
    }

    return $select->query()->fetchColumn();
	} 	
  
}

?>