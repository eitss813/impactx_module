<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Reviews.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_DbTable_Reviews extends Engine_Db_Table {

    protected $_rowClass = "Sitemember_Model_Review";

    /**
     * Return list members
     * @param Array $params
     * @return Zend_Db_Table_Select
     */
    public function listReviews($params = array()) {

        $reviewTableName = $this->info('name');
        $tableRating = Engine_Api::_()->getDbtable('ratings', 'sitemember');
        $tableRatingName = $tableRating->info('name');
        //MAKE QUERY
        $select = $this->select()->from($reviewTableName, array('*'));

        if (isset($params['resource_id']) && !empty($params['resource_id'])) {
            $select->where("$reviewTableName.resource_id = ?", $params['resource_id']);
        }

        if (isset($params['resource_type']) && !empty($params['resource_type'])) {
            $select->where("$reviewTableName.resource_type = ?", $params['resource_type']);
        }

        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where("$reviewTableName.owner_id = ?", $params['owner_id']);
        }

        if (isset($params['owner_ids']) && !empty($params['owner_ids'])) {
            $select->where("$reviewTableName.owner_id In(?)", (array) $params['owner_ids']);
        }

        $select->where("$reviewTableName.type in (?)", array('user'));

        if (isset($params['order'])) {
            if (isset($params['rating']) && $params['rating'] == 'rating') {
                $select->setIntegrityCheck(false)
                        ->join($tableRatingName, "$tableRatingName.review_id = $reviewTableName.review_id", array('rating'));
                $select->group("$tableRatingName.review_id");
                $select->where("ratingparam_id = ?", 0);
                if (isset($params['rating_value']) && !empty($params['rating_value'])) {
                    $select->where("rating =?", $params['rating_value']);
                }
                if ($params['order'] == 'highestRating') {
                    $select->order("$tableRatingName.rating DESC");
                } else if ($params['order'] == 'lowestRating') {
                    $select->order("$tableRatingName.rating ASC");
                }
            }

            if ($params['order'] == 'creationDate') {
                $select->order("$reviewTableName.review_id DESC");
            } else if ($params['order'] == 'helpful') {
                $select->order("$reviewTableName.helpful_count DESC")
                        ->order("$reviewTableName.modified_date DESC");
            } else if ($params['order'] == 'featured') {
                $select->order("$reviewTableName.featured DESC");
            } else if ($params['order'] == 'recommend') {
                $select->order("$reviewTableName.recommend DESC");
            }
        }

        if (isset($params['review_id']) && !empty($params['review_id'])) {
            $select->where("$reviewTableName.review_id <> ?", $params['review_id']);
        }

        $select->where("status =?", 1);

        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }

        //RETURN RESULTS
        return Zend_Paginator::factory($select);
    }

    /**
     * Return paginator
     * @param Array $params
     * @param Array $customParams
     * @return paginator
     */
    public function getReviewsPaginator($params = array(), $customParams = null) {

        $paginator = Zend_Paginator::factory($this->getReviewsSelect($params, $customParams));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    /**
     * Return paginator
     * @param Array $params
     * @param Array $customParams
     * @return Zend_Db_Table_Select
     */
    public function getReviewsSelect($params = array()) {

        $reviewTableName = $this->info('name');
        //GET USER TABLE NAME
        $userTable = Engine_Api::_()->getItemtable($params['resource_type']);
        $sitememberTableName = $userTable->info('name');
        $primary = current($userTable->info("primary"));

        if (isset($params['getRecommendCount']) && $params['getRecommendCount']) {
            //MAKE QUERY
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($reviewTableName, array('COUNT(resource_id) as recommend_count'))
                    ->join($sitememberTableName, "$reviewTableName.resource_id = $sitememberTableName.$primary", array())
                    ->where($reviewTableName . ".status =?", 1)
                    ->group("$reviewTableName.owner_id");
        } else {
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($reviewTableName, array('*'))
                    ->join($sitememberTableName, "$reviewTableName.resource_id = $sitememberTableName.$primary", array())
                    ->where($reviewTableName . ".status =?", 1)
                    ->group("$reviewTableName.review_id");
        }

        if (!isset($params['order'])) {
            $params['order'] = null;
        }

        $select->where("$reviewTableName.type in (?)", array('user'));

        if (isset($params['resource_type']) && !empty($params['resource_type'])) {
            $select->where("$reviewTableName.resource_type = ?", $params['resource_type']);
        }

        if (isset($params['resource_id']) && !empty($params['resource_id'])) {
            $select->where("$reviewTableName.resource_id = ?", $params['resource_id']);
        }

        if (isset($params['search']) && !empty($params['search'])) {
            $searchTable = Engine_Api::_()->getDbtable('search', 'core');
            $db = $searchTable->getAdapter();
            $sName = $searchTable->info('name');
            $select
                    ->joinRight($sName, $sName . '.id=' . $reviewTableName . '.review_id', null)
                    ->where($sName . '.type = ?', 'sitemember_review')
                    ->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['search'])))
                    ->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['search'])))
            ;
        }

        if (isset($params['recommend']) && !empty($params['recommend'])) {
            $select->where("recommend =?", $params['recommend']);
        }

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $select->where("$reviewTableName.owner_id =?", $params['user_id']);
        }

        if (isset($params['featured'])) {
            $select->where("$reviewTableName.featured =?", 1);
        }

        if (isset($params['user_ids']) && !empty($params['user_ids'])) {
            $select->where("$reviewTableName.owner_id in (?)", (array) $params['user_ids']);
        }

        if ((isset($params['rating']) && !empty($params['rating'])) || $params['order'] == 'rating_highest' || $params['order'] == 'rating_lowest') {
            $tableRating = Engine_Api::_()->getDbtable('ratings', 'sitemember');
            $tableRatingName = $tableRating->info('name');
            $select
                    ->join($tableRatingName, "$tableRatingName.review_id = $reviewTableName.review_id", array('rating'))
                    ->where("ratingparam_id = ?", 0)
                    ->group("$tableRatingName.review_id");
            if (isset($params['rating']) && !empty($params['rating']))
                $select->where("rating =?", $params['rating']);
        }

        if (isset($params['order'])) {
            if ($params['order'] == 'rating_highest') {
                $select->order("$tableRatingName.rating DESC");
            } else if ($params['order'] == 'rating_lowest') {
                $select->order("$tableRatingName.rating ASC");
            } else if ($params['order'] == 'view_most') {
                $select->order("$reviewTableName.view_count DESC");
            } else if ($params['order'] == 'like_most') {
                $select->order("$reviewTableName.like_count DESC");
            } else if ($params['order'] == 'helpfull_most') {
                $select->order("$reviewTableName.helpful_count DESC");
            } else if ($params['order'] == 'replay_most') {
                $select->order("$reviewTableName.reply_count DESC");
            } else if ($params['order'] == 'featured') {
                $select->order("$reviewTableName.featured DESC");
            }
        }
        $select->order("$reviewTableName.modified_date DESC");

        if (isset($params['getRecommendCount']) && $params['getRecommendCount']) {
            if ($select->query()->fetchColumn())
                return $select->query()->fetchColumn();
            else
                return '0';
        }

        return $select;
    }

    /**
     * Return average recommendetion for list members
     *
     * @param Array $params
     * @return average recommendetion for list members
     */
    public function getAvgRecommendation($params = array()) {

        $reviewTableName = $this->info('name');
        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('*', 'AVG(recommend) AS avg_recommend'));

        if (isset($params['resource_id']) && !empty($params['resource_id'])) {
            $select->where("$reviewTableName.resource_id = ?", $params['resource_id']);
        }

        if (isset($params['resource_type']) && !empty($params['resource_type'])) {
            $select->where("$reviewTableName.resource_type = ?", $params['resource_type']);
        }

        $select->where('status = ?', 1)
                ->group('resource_id');

        $select->where("$reviewTableName.type in (?)", array('user'));

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    /**
     * Return member data for checking that viewer has been posted a member or not
     *
     * @param Int resource_id
     * @param Int viewer_id
     * @return Zend_Db_Table_Select
     */
    public function canPostReview($params = array()) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('review_id'));

        $reviewTableName = $this->info('name');

        if (isset($params['resource_id']) && !empty($params['resource_id'])) {
            $select->where("$reviewTableName.resource_id = ?", $params['resource_id']);
        }

        if (isset($params['resource_type']) && !empty($params['resource_type'])) {
            $select->where("$reviewTableName.resource_type = ?", $params['resource_type']);
        }

        $select->where("$reviewTableName.type in (?)", array('user'));

        if (isset($params['viewer_id']) && !empty($params['viewer_id'])) {
            $select->where('owner_id = ?', $params['viewer_id']);
        }

        $hasPosted = $select->query()->fetchColumn();

        //RETURN RESULTS
        return $hasPosted;
    }

    /**
     * Return total reviews for members
     *
     * @param Array $params
     * @return total reviews for members
     */
    public function totalReviews($params = array()) {

        $reviewTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (isset($params['resource_id']) && !empty($params['resource_id'])) {
            $select->where("$reviewTableName.resource_id = ?", $params['resource_id']);
        }

        if (isset($params['resource_type']) && !empty($params['resource_type'])) {
            $select->where("$reviewTableName.resource_type = ?", $params['resource_type']);
        }

        $select->where("$reviewTableName.type in (?)", array('user'));

        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where('owner_id = ?', $params['owner_id']);
        }

        $totalReviews = $select->where("status = ?", 1)
                ->query()
                ->fetchColumn();

        //RETURN RESULTS
        return $totalReviews;
    }

    /**
     * Return paginator
     *
     * @param Int $user_id
     * @return paginator
     */
    public function getReviewComments($user_id = 0) {

        $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentTableName = $commentTable->info('name');

        $reviewTableName = $this->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($reviewTableName, array('review_id', 'resource_id', 'title', 'type'))
                ->join($commentTableName, "$commentTableName.resource_id = $reviewTableName.review_id", array('body AS comment', 'creation_date'))
                ->where("$commentTableName.poster_id = ?", $user_id);

        $select->where("$reviewTableName.type in (?)", array('user'));

        return Zend_Paginator::factory($select);
    }

    /**
     * Return total comment
     *
     * @param Int $user_id
     * @return total comment
     */
    public function countReviewComments($user_id = 0) {

        $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentTableName = $commentTable->info('name');

        $totalCommentCount = $commentTable->select()
                ->from($commentTableName, array('COUNT(comment_id) as total_comments'))
                ->where("$commentTableName.resource_type = ?", 'sitemember_review')
                ->where("$commentTableName.poster_id = ?", $user_id)
                ->query()
                ->fetchColumn();
        return $totalCommentCount;
    }

    /**
     * Return top memberers
     *
     * @param Array $params
     * @return top memberers
     */
    public function topReviewers($params = array()) {

        //GET USER TABLE INFO
        $tableUser = Engine_Api::_()->getDbtable('users', 'user');
        $tableUserName = $tableUser->info('name');

        //GET REVIEW TABLE NAME
        $reviewTableName = $this->info('name');

        //MAKE QUERY
        $select = $tableUser->select()
                ->setIntegrityCheck(false)
                ->from($tableUserName, array('user_id', 'displayname', 'username', 'photo_id'))
                ->join($reviewTableName, "$tableUserName.user_id = $reviewTableName.owner_id", array('COUNT(engine4_sitemember_members.review_id) AS member_count', 'MAX(engine4_sitemember_members.review_id) as max_review_id'));

        $select->where("$reviewTableName.type in (?)", array('user'));

        if (isset($params['resource_type']) && !empty($params['resource_type'])) {
            $select->where("$reviewTableName.resource_type = ?", $params['resource_type']);
        }

        $select->where($reviewTableName . '.status = ?', 1)
                ->group($tableUserName . ".user_id")
                ->order('member_count DESC')
                ->order('user_id DESC')
                ->limit($params['limit']);

        //RETURN THE RESULTS
        return $tableUser->fetchAll($select);
    }

    /**
     * Return column name
     *
     * @param Int $review_id
     * @param Varchar $column_name
     * @return column name
     */
    public function getColumnValue($review_id = 0, $column_name) {

        $column = $this->select()
                ->from($this->info('name'), array("$column_name"))
                ->where('review_id = ?', $review_id)
                ->limit(1)
                ->query()
                ->fetchColumn();

        return $column;
    }

}