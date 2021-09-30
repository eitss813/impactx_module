<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_DbTable_Ratings extends Engine_Db_Table {

    protected $_rowClass = "Sitemember_Model_Rating";

    /**
     * Update rating in the USERINFO table
     *
     * @param Int $resource_id
     * @param Varchar $resource_type
     * @return Updated rating
     */
    public function userRatingUpdate($resource_id, $resource_type, $rating_only = 0) {

        //RETURN IF RESOURCE ID IS EMPTY
        if (empty($resource_id) || empty($resource_type)) {
            return;
        }
        $tableRatingName = $this->info('name');
        $tableReviewName = Engine_Api::_()->getDbtable('reviews', 'sitemember')->info('name');

        if (!empty($rating_only)) {
            $rating_avg = $this
                    ->select()
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->where($tableRatingName . ".ratingparam_id = ?", 0)
                    ->where($tableRatingName . ".resource_id = ?", $resource_id)
                    ->where($tableRatingName . ".resource_type = ?", $resource_type)
                    ->where($tableRatingName . ".rating != ?", 0)
                    ->group($tableRatingName . '.resource_id')
                    ->query()
                    ->fetchColumn();
        } else {
            $rating_avg = $this
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
                    ->where($tableRatingName . ".ratingparam_id = ?", 0)
                    ->where($tableRatingName . ".resource_id = ?", $resource_id)
                    ->where($tableRatingName . ".resource_type = ?", $resource_type)
                    ->where($tableRatingName . ".rating != ?", 0)
                    ->where($tableReviewName . ".status = ?", 1)
                    ->group($tableRatingName . '.resource_id')
                    ->query()
                    ->fetchColumn();
        }

        if (!empty($rating_only)) {
            $rating_users = $this
                    ->select()
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->where($tableRatingName . ".ratingparam_id = ?", 0)
                    ->where($tableRatingName . ".resource_id = ?", $resource_id)
                    ->where($tableRatingName . ".resource_type = ?", $resource_type)
                    ->where($tableRatingName . ".type in (?) ", array('user'))
                    ->where($tableRatingName . ".rating != ?", 0)
                    ->group($tableRatingName . '.resource_id')
                    ->query()
                    ->fetchColumn();
        } else {
            $rating_users = $this
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
                    ->where($tableRatingName . ".ratingparam_id = ?", 0)
                    ->where($tableRatingName . ".resource_id = ?", $resource_id)
                    ->where($tableRatingName . ".resource_type = ?", $resource_type)
                    ->where($tableRatingName . ".type in (?) ", array('user'))
                    ->where($tableRatingName . ".rating != ?", 0)
                    ->where($tableReviewName . ".status = ?", 1)
                    ->group($tableRatingName . '.resource_id')
                    ->query()
                    ->fetchColumn();
        }

        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $user_id = $tableUserInfo->getColumnValue($resource_id, 'user_id');
        $review_count = $tableUserInfo->getColumnValue($resource_id, 'review_count');
        if ($user_id) {
            $tableUserInfo->update(array('rating_avg' => round($rating_avg, 4), 'rating_users' => round($rating_users, 4)), array('user_id = ?' => $resource_id));
        } else {
            $review_count = $review_count + 1;
            $tableUserInfo->insert(array(
                'user_id' => $resource_id,
                'rating_avg' => round($rating_avg, 4),
                'rating_users' => round($rating_users, 4),
                'review_count' => $review_count
            ));
        }

        $total_count = $this->select()->from($this->info('name'), array("COUNT(review_id) as total_count"))
                ->where($tableRatingName . ".resource_id = ?", $resource_id)
                ->where($tableRatingName . ".resource_type = ?", $resource_type)
                ->where($tableRatingName . ".type in (?) ", array('user'))
                ->query()
                ->fetchColumn();

        $tableUserInfo->update(array('review_count' => $total_count), array('user_id = ?' => $resource_id));

        return round($rating_users, 4);
    }

    /**
     * Get rating by category
     *
     * @param Int $resource_id
     * @param Varchar $type
     * @param Varchar $resource_type
     * @return Get rating by category
     */
    public function ratingbyCategory($resource_id, $type = null, $resource_type) {

        //RETURN IF PAGE ID IS EMPTY
        if (empty($resource_id) || empty($resource_type)) {
            return;
        }

        $tableRatingName = $this->info('name');
        $tableRatingParamsName = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->info('name');
        $tableReviewName = Engine_Api::_()->getDbtable('reviews', 'sitemember')->info('name');
        $select = $this
                ->select()
                ->setIntegrityCheck(false)
                ->from($tableRatingName, array('AVG(rating) AS avg_rating', 'ratingparam_id'))
                ->joinLeft($tableRatingParamsName, "$tableRatingName.ratingparam_id = $tableRatingParamsName.ratingparam_id", array('ratingparam_name'))
                ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
                ->where($tableRatingName . ".rating != ?", 0)
                ->where($tableRatingName . ".resource_id = ?", $resource_id)
                ->where($tableRatingName . ".resource_type = ?", $resource_type)
                ->where($tableReviewName . ".status = ?", 1)
                ->group($tableRatingName . '.ratingparam_id');

        $select->where("$tableReviewName.type in (?)", array('user'));

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Get ratings
     *
     * @param Int $review_id
     * @param Int $viewer_id
     * @param Int $resource_id
     * @param Int $ratingparam_id
     * @return Get ratings
     */
    public function ratingsData($review_id, $viewer_id = null, $resource_id = null, $ratingparam_id = -1) {

        $select = $this->select()
                ->from($this->info('name'), array('ratingparam_id', 'rating', 'user_id'))
                ->where("review_id = ?", $review_id);

        if (!empty($resource_id)) {
            $select->where("resource_id =?", $resource_id);
        }

        if (!empty($viewer_id)) {
            $select->where("user_id =?", $viewer_id);
        }
        if ($ratingparam_id != -1) {
            $select->where("ratingparam_id =?", $ratingparam_id);
        }

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Get profile rating
     *
     * @param Int $review_id
     * @param Int $viewer_id
     * @return Get profile rating
     */
    public function profileRatingbyCategory($review_id, $viewer_id = null) {

        //RETURN IF REVIEW ID IS EMPTY
        if (empty($review_id)) {
            return;
        }

        //GET RATING TABLE NAME
        $tableRatingName = $this->info('name');

        //GET REVIEW PARAMETER TABLE INFO
        $tableRatingParamsName = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->info('name');

        //MAKE QUERY
        $select = $this
                ->select()
                ->setIntegrityCheck(false)
                ->from($tableRatingName, array('rating'))
                ->joinLeft($tableRatingParamsName, "$tableRatingName.ratingparam_id = $tableRatingParamsName.ratingparam_id", array('ratingparam_name'))
                ->where("review_id = ?", $review_id);

        if (!empty($viewer_id)) {
            $select->where("user_id =?", $viewer_id);
        }
        //RETURN RESULTS
        return $this->fetchAll($select)->toArray();
    }

    /**
     * Create Rating Data
     *
     * @param Array $postData
     * @param Varchar $type
     * @return Created Rating Data
     */
    public function createRatingData($postData, $type) {

        $str = "";
        //DO ENTRY IN REVIEW RATING TABLE
        foreach ($postData as $key => $ratingdata) {
            if (empty($ratingdata))
                continue;
            if (strstr($key, 'update_member_rate_')) {
                $string_exist = strstr($key, 'update_member_rate_');
                $str = 'update_member_rate_';
            } else {
                $string_exist = strstr($key, 'member_rate_');
                $str = 'member_rate_';
            }
            if ($string_exist) {
                if ($str)
                    $ratingparam_id = explode($str, $key);
                $memberRating = $this->createRow();
                $memberRating->review_id = $postData['review_id'];
                $memberRating->user_id = $postData['user_id'];
                //  $memberRating->profiletype_id = $postData['profiletype_id'];
                $memberRating->resource_id = $postData['resource_id'];
                $memberRating->resource_type = $postData['resource_type'];
                $memberRating->ratingparam_id = $ratingparam_id[1];
                $memberRating->rating = $ratingdata;
                $memberRating->type = $type;
                $memberRating->save();
            }
        }
    }

    /**
     * Number of user rating
     *
     * @param Int $resource_id
     * @param Varchar $type
     * @param Int $ratingparam_id
     * @param Int $value
     * @param Int $user_id
     * @param Varchar $resource_type
     * @param Array $params
     * @return Number of user rating
     */
    public function getNumbersOfUserRating($resource_id, $type = 'user', $ratingparam_id = 0, $value = 0, $user_id = 0, $resource_type = 'user', $pageName = null) {

        $allow_member = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2);
        if (!empty($resource_id)) {
            $userTable = Engine_Api::_()->getDbtable('users', 'user');

            $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitemember');
            $rating_value = $ratingTable->select()
                    ->from($ratingTable->info('name'), 'rating')
                    ->where('resource_id = ?', $resource_id)
                    ->where('resource_type = ?', $resource_type)
                    ->where('user_id = ?', $user_id)
                    ->query()
                    ->fetchColumn();
        } else {
            $rating_value = 1;
        }
        $tableReviewName = Engine_Api::_()->getDbtable('reviews', 'sitemember')->info('name');
        $tableRatingName = $this->info('name');

        if (!empty($allow_member) && !empty($rating_value)) {
            $select = $this
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from($tableRatingName, new Zend_Db_Expr('COUNT(rating_id)'))
                    ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
                    ->where($tableReviewName . ".status = ?", 1)
                    ->where("$tableRatingName.ratingparam_id = ?", $ratingparam_id);
        } else {
            $select = $this
                    ->select()
                    ->from($tableRatingName, new Zend_Db_Expr('COUNT(rating_id)'))
                    ->where("$tableRatingName.ratingparam_id = ?", $ratingparam_id);
        }

        if ($resource_id) {
            $select->where("$tableRatingName.resource_id = ?", $resource_id);
        }

        if ($resource_type) {
            $select->where("$tableRatingName.resource_type = ?", $resource_type);
        }

        if ($value) {
            $select->where("$tableRatingName.rating = ?", $value);
        }

        $select->where("$tableRatingName.rating <> ?", 0);

        if (!empty($allow_member) && !empty($rating_value)) {
            $select->where("$tableReviewName.type in (?)", array('user'));
        } else {
            $select->where("$tableRatingName.type in (?)", array('user'));
        }

        if ($user_id) {
            $select->where("$user_id = ?", $user_id);
        }

        if ($pageName == 'owner') {
            $select->where("user_id = ?", $user_id);
        }

        $select->limit(1);

        $rating_users = $select
                ->query()
                ->fetchColumn();
        return $rating_users ? $rating_users : 0;
    }

    public function getReviewId($viewer_id, $resource_type, $user_id) {

        $selectReviewRatingTable = $this->select()
                ->where('resource_id = ?', $user_id)
                ->where('resource_type = ?', $resource_type)
                ->where('type = ?', 'user')
                ->where('user_id = ?', $viewer_id);
        $member = $this->fetchRow($selectReviewRatingTable);
        return $member;
    }

    public function getReviewIdExist($viewer_id, $resource_type, $user_id) {

        $selectReviewTable = $this->select()
                ->where('resource_id = ?', $user_id)
                ->where('resource_type = ?', $resource_type)
                ->where('type = ?', 'user')
                ->where('user_id = ?', $viewer_id);
        $member = $this->fetchRow($selectReviewTable);
        $exist_review_id = $member->review_id;
        return $exist_review_id;
    }

}