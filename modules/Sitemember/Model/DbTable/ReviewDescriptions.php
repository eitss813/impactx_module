<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ReviewDescriptions.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_DbTable_ReviewDescriptions extends Engine_Db_Table {

    protected $_rowClass = 'Sitemember_Model_Reviewdescription';

    /**
     * Review Descriptions
     *
     * @param Int $review_id
     * @return Review Descriptions
     */
    public function getReviewDescriptions($review_id) {

        $tableReviewDescriptionName = $this->info('name');
        $select = $this->select()->from($tableReviewDescriptionName, array('*'))
                ->where("$tableReviewDescriptionName.review_id =?", $review_id)
                ->order("$tableReviewDescriptionName.review_id DESC")
                ->order("$tableReviewDescriptionName.modified_date DESC");

        return Zend_Paginator::factory($select);
    }

    /**
     * Count Review Descriptions Ids
     *
     * @param Int $review_id
     * @return Count Review Descriptions Ids
     */
    public function getCount($review_id) {

        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(reviewdescription_id)'))
                        ->where('review_id = ?', $review_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

}