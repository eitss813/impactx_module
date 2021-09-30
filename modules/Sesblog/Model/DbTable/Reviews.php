<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Reviews.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_DbTable_Reviews extends Engine_Db_Table {

  protected $_rowClass = 'Sesblog_Model_Review';
  
  public function getBlogReviewSelect($params = array()) {

    $table = Engine_Api::_()->getItemTable('sesblog_review');
    $blogReviewTableName = $table->info('name');
    $select = $table->select()->from($blogReviewTableName);

    $currentTime = date('Y-m-d H:i:s');
    if (isset($params['view'])) {
      if ($params['view'] == 'week') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 week"));
        $select->where("DATE(creation_date) between ('$endTime') and ('$currentTime')");
      } elseif ($params['view'] == 'month') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 month"));
        $select->where("DATE(creation_date) between ('$endTime') and ('$currentTime')");
      }
    }

    //Full Text	
    if (isset($params['order']) && !empty($params['order'])) {
      if ($params['order'] == 'featured')
        $select->where('featured = ?', '1');
      elseif ($params['order'] == 'verified')
        $select->where('verified = ?', '1');
      if ($params['order'] == 'week') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 week"));
        $select->where("DATE(creation_date) between ('$endTime') and ('$currentTime')");
      } elseif ($params['order'] == 'month') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 month"));
        $select->where("DATE(creation_date) between ('$endTime') and ('$currentTime')");
      }
    }
    if (!empty($params['search_text']))
      $select->where('`' . $blogReviewTableName . '`.`title` LIKE ?', '%' . $params['search_text'] . '%');
    if (!empty($params['owner_id']))
      $select->where('`' . $blogReviewTableName . '`.`owner_id` = ?', $params['owner_id']);
    if (!empty($params['review_stars']))
      $select->where('`' . $blogReviewTableName . '`.`rating` = ?', $params['review_stars']);
    if (!empty($params['review_recommended']))
      $select->where('`' . $blogReviewTableName . '`.`recommended` = ?', $params['review_recommended']);
    if (isset($params['order']) && $params['order'] != '') {
      $select->order($blogReviewTableName . '.' . $params['order']);
    }
    if (isset($params['criteria'])) {
      if ($params['criteria'] == 1)
        $select->where($blogReviewTableName . '.featured =?', '1');
      else if ($params['criteria'] == 2)
        $select->where($blogReviewTableName . '.verified =?', '1');
      else if ($params['criteria'] == 3)
        $select->where($blogReviewTableName . '.featured = 1 OR ' . $blogReviewTableName . '.verified = 1');
      else if ($params['criteria'] == 4)
        $select->where($blogReviewTableName . '.featured = 0 AND ' . $blogReviewTableName . '.verified = 0');
    }
    if (isset($params['info'])) {
      switch ($params['info']) {
        case 'most_viewed':
          $select->order('view_count DESC');
          break;
        case 'most_liked':
          $select->order('like_count DESC');
          break;
        case 'like_count':
          $select->order('like_count DESC');
          break;
        case 'most_commented':
          $select->order('comment_count DESC');
          break;
        case 'comment_count':
          $select->order('comment_count DESC');
          break;
        case "view_count":
          $select->order($blogReviewTableName . '.view_count DESC');
          break;
        case "most_rated":
          $select->order($blogReviewTableName . '.rating DESC');
          break;
        case "least_rated":
          $select->order($blogReviewTableName . '.rating ASC');
          break;
        case 'random':
          $select->order('Rand()');
          break;
        case "verified" :
          $select->where($blogReviewTableName . '.verified' . ' = 1')
                  ->order($blogReviewTableName . '.owner_id DESC');
          break;
        case "featured" :
          $select->where($blogReviewTableName . '.featured' . ' = 1')
                  ->order($blogReviewTableName . '.owner_id DESC');
          break;
        case "creation_date":
          $select->order($blogReviewTableName . '.creation_date DESC');
          break;
        case "modified_date":
          $select->order($blogReviewTableName . '.modified_date DESC');
          break;
      }
    }

    if (isset($params['widgetName']) && $params['widgetName'] == 'oftheday') {
      $select->where($blogReviewTableName . '.oftheday =?', 1)
              ->where($blogReviewTableName . '.starttime <= DATE(NOW())')
              ->where($blogReviewTableName . '.endtime >= DATE(NOW())')
              ->limit(1)
              ->order('RAND()');
    }
    
    if(isset($params['blog_id']) && !empty($params['blog_id']))
    $select->where($blogReviewTableName . '.blog_id =?', $params['blog_id']);
    
    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);
    if (!empty($params['limit_data']))
      $select->limit($params['limit_data']);
    $select->order($blogReviewTableName . '.creation_date DESC');

    if (isset($params['paginator']))
      return Zend_Paginator::factory($select);

    if (isset($params['fetchAll']))
      return $table->fetchAll($select);
    else
      return $select;
  }

	public function isReview($params = array()) {
		$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
		$select = $this->select()
		->from($this->info('name'), 'review_id')
		->where('blog_id = ?', $params['blog_id'])
		->where('owner_id = ?', $viewerId);
		return $select = $select->query()->fetchColumn();
	}
  
	public function ratingCount($resource_id = NULL){
		$rName = $this->info('name');
		return $this->select()
		->from($rName,new Zend_Db_Expr('COUNT(review_id) as total_rating'))
		->where($rName.'.blog_id = ?', $resource_id)
		->limit(1)->query()->fetchColumn();
	}
	// rating functions
	public function getRating($resource_id) {
		$rating_sum = $this->select()
		->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
		->group('blog_id')
		->where('blog_id = ?', $resource_id)
		->query()
		->fetchColumn(0);
		$total = $this->ratingCount($resource_id);
		if ($total)
		$rating = $rating_sum / $total;
		else
		$rating = 0;
		return $rating;
	}
	public function getSumRating($resource_id) {
		$rName = $this->info('name');
		$rating_sum = $this->select()
			->from($rName, new Zend_Db_Expr('SUM(rating)'))
			->where('blog_id = ?', $resource_id)
			->group('blog_id')
			->query()
			->fetchColumn();
		return $rating_sum;
	}
  public function getReviewCount($resource_id) {
		return $this->select()
			->from($this->info('name'), 'review_id')
			->where('blog_id = ?', $resource_id)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  
	public function getUserReviewCount($params = array()) {
		$select =  $this->select()
		->where('blog_id = ?', $params['blog_id']);
		if($params['rating']) {
			$select = $select->where('rating =?', $params['rating']);
		}
		return  $this->fetchAll($select); 
	}

}
