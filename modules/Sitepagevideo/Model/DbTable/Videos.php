<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Videos.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Model_DbTable_Videos extends Engine_Db_Table {

  protected $_rowClass = "Sitepagevideo_Model_Video";

  /**
   * Return video count
   *
   * @param int $page_id
   * @return video count
   */
  public function getPageVideoCount($page_id) {

    $selectVideo = $this->select()
                    ->from($this->info('name'), 'count(*) as count')
                    ->where('page_id = ?', $page_id);
    $data = $this->fetchRow($selectVideo);
    return $data->count;
  }

  /**
   * Return video data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function widgetVideosData($params = array(),$videoType = null,$widgetType = null) {

    $tableVideoName = $this->info('name');
    if(isset($params['profile_page_widget']) && !empty($params['profile_page_widget'])) {

      $select = $this->select()
								->from($tableVideoName, array('video_id', 'page_id', 'owner_id', 'title', 'creation_date', 'rating', 'comment_count', 'like_count', 'view_count', 'featured', 'photo_id','duration','description'))
								->where('status = ?', '1')
								->where('search = ?', '1')
								->where('page_id = ?', $params['page_id'])
								->limit($params['limit']);

      if (isset($params['zero_count']) && !empty($params['zero_count'])) {
				$select = $select->where($tableVideoName . '.' . $params['zero_count'] . '!= ?', 0);
      }
      if (isset($params['orderby']) && !empty($params['orderby'])) {
				$select = $select->order($tableVideoName . '.' . $params['orderby']);
      }
      $select->order($tableVideoName . '.creation_date DESC');
    }
    elseif(isset($params['view_action']) && !empty($params['view_action'])) {
      $select = $this->select();
      if($widgetType == 'sameposter') {
      $select = $this->select()
								->where($tableVideoName . '.page_id = ?', $params['page_id'])
								->where($tableVideoName . '.video_id != ?', $params['video_id'])
								->limit($params['limit'])
								->order($tableVideoName . '.creation_date DESC');
      }    
			elseif($widgetType == 'showalsolike') {
				$likesTable = Engine_Api::_()->getDbtable('likes', 'core');
				$likesTableName = $likesTable->info('name');
				$select = $this->select()
									->setIntegrityCheck(false)
									->from($tableVideoName)
									->joinLeft($likesTableName, $likesTableName.'.resource_id=video_id', null)
									->joinLeft($likesTableName . ' as l2', $likesTableName.'.poster_id=l2.poster_id', null)
									->where($likesTableName . '.poster_type = ?', 'user')
									->where('l2.poster_type = ?', 'user')
									->where($likesTableName . '.resource_type = ?', $params['resource_type'])
									->where('l2.resource_type = ?', $params['resource_type'])
									->where($likesTableName . '.resource_id != ?', $params['resource_id'])
									->where('l2.resource_id = ?', $params['resource_id'])
									->where($tableVideoName .'.video_id != ?', $params['video_id'])
									->limit($params['limit'])
									->group("$tableVideoName.video_id")
									->order($tableVideoName . '.like_count DESC');

			}   
			elseif($widgetType == 'showsametag') {
				// Get tags for this video
				$tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
				$tagsTable = Engine_Api::_()->getDbtable('tags', 'core');
				
				// Get tags
				$tags = $tagMapsTable->select()
					->from($tagMapsTable, 'tag_id')
					->where('resource_type = ?', $params['resource_type'])
					->where('resource_id = ?', $params['resource_id'])
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);

				// No tags
				if( !empty($tags) ) {
				// Get other with same tags
				$select = $this->select()
									->setIntegrityCheck(false)
									->from($tableVideoName)
									->joinLeft($tagMapsTable->info('name'), 'resource_id=video_id', null)
									->where('resource_type = ?', $params['resource_type'])
									->where('resource_id != ?', $params['resource_id'])
									->where('tag_id IN(?)', $tags)
									->limit($params['limit'])
									->order($tableVideoName . '.creation_date DESC')
									->group("$tableVideoName.video_id");
				}
        else {
          return;
        }
			}
    }
    else {
			$tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
			$tablePageName = $tablePage->info('name');
			$pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
			$pagePackageTableName = $pagePackagesTable->info('name');
			$select = $this->select()
								->setIntegrityCheck(false)
								->from($tableVideoName, array('video_id', 'page_id', 'owner_id', 'title', 'creation_date', 'rating', 'comment_count', 'like_count', 'view_count', 'featured', 'photo_id','duration','description'))
								->joinLeft($tablePageName, "$tablePageName.page_id = $tableVideoName.page_id", array('page_id', 'title AS page_title', 'owner_id'))
								->where($tableVideoName . '.status = ?', '1')
								->where($tableVideoName . '.search = ?', '1');

			if (isset($params['zero_count']) && !empty($params['zero_count'])) {
				$select = $select->where($tableVideoName . '.' . $params['zero_count'] . '!= ?', 0);
			}

		  if (isset($params['category_id']) && !empty($params['category_id'])) {
				$select = $select->where($tablePageName . '.	category_id =?', $params['category_id']);
			}

			if (isset($params['orderby']) && !empty($params['orderby'])) {
				$select = $select->order($tableVideoName . '.' . $params['orderby']);
			}

			if (isset($params['page_id']) && !empty($params['page_id'])) {
				$select = $select->where($tableVideoName . '.page_id = ?', $params['page_id']);
			}

			if (isset($params['limit']) && !empty($params['limit'])) {
				if (!isset($params['start_index']))
					$params['start_index'] = 0;
				$select->limit($params['limit'], $params['start_index']);
			}

			if ($videoType == 'sponsored') {
				$select->join($pagePackageTableName, "$pagePackageTableName.package_id = $tablePageName.package_id",array('package_id', 'price'));
				$select->where($pagePackageTableName . '.price != ?', '0.00');
				$select->order($pagePackageTableName . '.price' . ' DESC');
				$select ->limit($params['limit']);
			}
			$select = $select->order($tableVideoName .'.video_id DESC');
			$select = $select
											->where($tablePageName . '.closed = ?', '0')
											->where($tablePageName . '.approved = ?', '1')
											->where($tablePageName . '.declined = ?', '0')
											->where($tablePageName . '.search = ?', '1')
											->where($tablePageName . '.draft = ?', '1');
			if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
				$select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
			}
			//Start Network work
			if (!isset($params['page_id']) || empty($params['page_id'])) {
				$select = $tablePage->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $tableVideoName . ".video_id"));
			}
    }
    //End Network work
    return $this->fetchAll($select);
  }

  /**
   * Get sitepagevideo detail
   * @param array $params : contain desirable sitepagevideo info
   * @return  object of sitepagevideo
   */
  public function getSitepagevideosPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getSitepagevideosSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Get sitepagevideos 
   * @param array $params : contain desirable sitepagevideo info
   * @return  array of sitepagevideos
   */
  public function getSitepagevideosSelect($params = array()) {

    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
    $videoTableName = $videoTable->info('name');

    $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tagMapTableName = $tagMapTable->info('name');

    if (isset($params['show_count']) && $params['show_count'] == 1) {

      $select = $videoTable->select();
      $select = $select
                      ->setIntegrityCheck(false)
                      ->from($videoTableName, array(
                          'COUNT(*) AS show_count'));
    } else {
      if (isset($params['orderby']) && $params['orderby'] == 'view_count') {
        $select = $videoTable->select()
                        ->order('view_count DESC')
                        ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'comment_count') {
        $select = $videoTable->select()
                        ->order('comment_count DESC')
                        ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'rating') {
        $select = $videoTable->select()
                        ->order('rating DESC')
                        ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'like_count') {
        $select = $videoTable->select()
                        ->order('like_count DESC')
                        ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'featured') {
        $select = $videoTable->select()
                        ->where($videoTableName . '.featured = ?', 1)
                        ->order('creation_date DESC');
      }
      elseif (isset($params['orderby']) && $params['orderby'] == 'highlighted') {
        $select = $videoTable->select()
                        ->where($videoTableName . '.highlighted = ?', 1)
                        ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'vote_count') {
        $select = $videoTable->select()
                        ->order('vote_count DESC')
                        ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'creation_date') {
        $select = $videoTable->select()
                        ->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : 'creation_date DESC');
      }
      else {
       $select = $videoTable->select()
														->order('highlighted DESC')
														->order('creation_date DESC');
       }

      $select = $select
                      ->setIntegrityCheck(false)
                      ->from($videoTableName)
                      ->group("$videoTableName.video_id");
    }

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($videoTableName . '.owner_id = ?', $params['user_id']);
    }

    if (isset($params['featured'])) {
      $select->where($videoTableName . '.featured = ?', $params['featured']);
    }

    if (isset($params['highlighted'])) {
      $select->where($videoTableName . '.highlighted = ?', $params['highlighted']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($videoTableName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($videoTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['owner_id'])) {
      $select->where($videoTableName . '.owner_id = ?', $params['owner_id']);
    }

    if (isset($params['page_id'])) {
      $select->where($videoTableName . '.page_id = ?', $params['page_id']);
    }

    if (!empty($params['search'])) {
      $select->where($videoTableName . ".title LIKE ? OR " . $videoTableName . ".description LIKE ?", '%' . $params['search'] . '%');
    }
    if (!empty($params['text'])) {
      $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $videoTableName.video_id and " . $tagMapTableName . ".resource_type = 'sitepagevideo_video'", null)
              ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", null)
              ->where($videoTableName . '.status = ?', 1)
              ->where($videoTableName . '.search = ?', 1);

      $select->where($videoTableName . ".title LIKE ? OR " . $videoTableName . ".description LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['text'] . '%');
    }

    if (!empty($params['show_video']) && empty($params['search'])) {
      $select->where($videoTableName . ".status = ?", 1)
              ->where($videoTableName . ".search = ?", 1)
              ->orwhere($videoTableName . ".owner_id = ?", $params['video_owner_id']);
    }

    if (!empty($params['show_video']) && (!empty($params['search']) )) {
      $select->where("($videoTableName.status = 1)  AND ($videoTableName.search = 1) OR ($videoTableName.owner_id = " . $params['video_owner_id'] . ")");
    }


    if (isset($params['page_id'])) {
      $select->where($videoTableName . '.page_id = ?', $params['page_id']);
    }

    if (!empty($params['see_all'])) {
      $select
              ->where($videoTableName . '.status = ?', 1)
              ->where($videoTableName . '.search = ?', 1);
    }

    if (!empty($params['tag'])) {
      $select
              ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $videoTableName.video_id", NULL)
              ->where($videoTableName . '.status = ?', 1)
              ->where($videoTableName . '.search = ?', 1)
              ->where($tagMapTableName . '.resource_type = ?', 'sitepagevideo_video')
              ->where($tagMapTableName . '.tag_id = ?', $params['tag']);
    }
    return $select;
  }

  /**
   * Return page videos
   *
   * @param string $hotVideo
   * @return Zend_Db_Table_Select
   */
  public function getVideos($params = array()) {

    //VIDEO TABLE NAME
    $videoTableName = $this->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
    $pagePackageTableName = $pagePackagesTable->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');  

    //QUERY MAKING
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('photo_id', 'title as sitepage_title'))
                    ->join($videoTableName, $videoTableName . '.page_id = ' . $pageTableName . '.page_id')
                    ->join($pagePackageTableName, "$pagePackageTableName.package_id = $pageTableName.package_id",array('package_id', 'price'))
                    ->where($videoTableName . '.status = ?', '1')
                    ->where($videoTableName . '.search = ?', '1')
                    ->group("$videoTableName.video_id");


    if (!empty($params['title'])) {

      $select->where($pageTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
    }
    
  
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if(isset($params['show']) && $params['show'] == 'my_video') {
			$select->where($videoTableName . '.owner_id = ?', $viewer_id);
		}
		elseif ((isset($params['show']) && $params['show'] == 'sponsored video') || !empty($params['sponsoredvideo'])) {
				
				$select->where($pagePackageTableName . '.price != ?', '0.00');
				$select->order($pagePackageTableName . '.price' . ' DESC');
		}
		elseif (isset($params['show']) && $params['show'] == 'Networks') {
				$select = $pageTable->getNetworkBaseSql($select, array('browse_network' => 1));

		}
		elseif((isset($params['show']) && $params['show'] == 'featured') || !empty($params['featuredvideo'])) {
			$select = $select
											->where($videoTableName . '.featured = ?', 1)
											->order($videoTableName .'.creation_date DESC');
		}
    elseif((isset($params['show']) && $params['show'] == 'highlighted') || !empty($params['highlightedvideo'])) {
			$select = $select
											->where($videoTableName . '.highlighted = ?', 1)
											->order($videoTableName .'.creation_date DESC');
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
   
		if ((isset($params['orderby']) && $params['orderby'] == 'view_count') || !empty($params['viewedvideo'])) {
			$select = $select
											->order($videoTableName .'.view_count DESC')
											->order($videoTableName .'.creation_date DESC');
		} elseif ((isset($params['orderby']) && $params['orderby'] == 'comment_count') || !empty($params['commentedvideo'])) {
			$select = $select
											->order($videoTableName .'.comment_count DESC')
											->order($videoTableName .'.creation_date DESC');
		} elseif ((isset($params['orderby']) && $params['orderby'] == 'rating') || !empty($params['ratedvideo'])) {
			$select = $select
											->order($videoTableName .'.rating DESC')
											->order($videoTableName .'.creation_date DESC');
		} elseif ((isset($params['orderby']) && $params['orderby'] == 'like_count') || !empty($params['likedvideo'])) {
			$select = $select
											->order($videoTableName .'.like_count DESC')
											->order($videoTableName .'.creation_date DESC');
		}
    $tag_value = Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null);
    if (!empty($params['search_video']) || !empty($tag_value)) {

      $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tmName, "$tmName.resource_id = $videoTableName.video_id and " . $tmName . ".resource_type = 'sitepagevideo_video'", null)
              ->joinLeft($tagName, "$tagName.tag_id = $tmName.tag_id", array($tagName . ".text"));
      if(!empty($tag_value)) {
        $select->where($tagName . '.tag_id = ?', $tag_value);
      }
      else {
				$select->where($videoTableName . ".title LIKE ? OR " . $videoTableName . ".description LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search_video'] . '%');
      }
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

    if(empty($params['orderby'])) {
			$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.order', 1);
			switch ($order) {
				case "1":
					$select->order($videoTableName . '.creation_date DESC');
					break;
				case "2":
					$select->order($videoTableName . '.title');
					break;
				case "3":
					$select->order($videoTableName . '.featured' . ' DESC');
					break;
				case "4":
					$select->order($pagePackageTableName . '.price' . ' DESC');
					break;
				case "5":
					$select->order($videoTableName . '.featured' . ' DESC');
					$select->order($pagePackageTableName . '.price' . ' DESC');
					break;
				case "6":
					$select->order($pagePackageTableName . '.price' . ' DESC');
					$select->order($videoTableName . '.featured' . ' DESC');
					break;
			}
    }
    $select->order($videoTableName . '.creation_date DESC');
    $select = $select
                    ->where($pageTableName . '.closed = ?', '0')
                    ->where($pageTableName . '.approved = ?', '1')
                    ->where($pageTableName . '.search = ?', '1')
                    ->where($pageTableName . '.declined = ?', '0')
                    ->where($pageTableName . '.draft = ?', '1');

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
   
   //Start Network work
  $select = $pageTable->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $videoTableName . ".video_id"));
    //End Network work
  

    return Zend_Paginator::factory($select);
  }

  /**
   * Return video of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function videoOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $videoOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->info('name');

		//GET PAGE TABLE NAME
		$pageTableName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');

    //GET VIDEO TABLE NAME
    $videoTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($videoTableName, array('video_id', 'title', 'page_id', 'owner_id', 'description','photo_id','duration'))
                    ->join($videoOfTheDayTableName, $videoTableName . '.video_id = ' . $videoOfTheDayTableName . '.resource_id')
										->join($pageTableName, $videoTableName . '.page_id = ' . $pageTableName . '.page_id', array(''))
										->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->where('resource_type = ?', 'sitepagevideo_video')
                    ->where('start_date <= ?', $date)
                    ->where('end_date >= ?', $date)
                    ->order('Rand()');

		//PAGE SHOULD BE AUTHORIZED
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($pageTableName.'.expiration_date  > ?', date("Y-m-d H:i:s"));

		//PAGE SHOULD BE AUTHORIZED
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
    if ($stusShow == 0) {
      $select->where($pageTableName.'.closed = ?', '0');
    }

    //RETURN RESULTS
    return $this->fetchRow($select);
  }

  public function getTagCloud($limit=100) {

    $tableTagmaps = 'engine4_core_tagmaps';
    $tableTags = 'engine4_core_tags';

    $tableSitepagevideos = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableSitepagevideos, 'title')
            ->joinInner($tableTagmaps, "$tableSitepagevideos.video_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'));

    $select->where($tableSitepagevideos . ".search = ?", 1);
    $select = $select            
            ->where($tableTagmaps . '.resource_type = ?', 'sitepagevideo_video')
            ->group("$tableTags.text")
            ->order("Frequency DESC")
            ->limit($limit);

    return $select->query()->fetchAll();
  }

  public function topcreatorData($limit = null,$category_id) {

    //VIDEO TABLE NAME
    $videoTableName = $this->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('photo_id', 'title as sitepage_title','page_id'))
                    ->join($videoTableName, "$pageTableName.page_id = $videoTableName.page_id", array('COUNT(engine4_sitepage_pages.page_id) AS item_count'))
                    ->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->group($videoTableName . ".page_id")
                    ->order('item_count DESC')
                    ->limit($limit);
    if (!empty($category_id)) {
      $select->where($pageTableName . '.category_id = ?', $category_id);
    }
    return $select->query()->fetchAll();
  }

}
?>