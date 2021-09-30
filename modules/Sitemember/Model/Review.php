<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Review.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_Review extends Core_Model_Item_Abstract {

    protected $_parent_type = 'user';
    protected $_owner_type = 'user';

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getOwner($recurseType = null) {

        if ($this->owner_id == 0)
            return;

        return parent::getOwner();
    }

    /**
     * Return href
     * */
    public function getHref($params = array()) {

        //GET CONTENT ID
        $content_id = Engine_Api::_()->sitemember()->existWidget('sitemember_view_reviews', 0);
        $params = array_merge(array(
            'route' => "sitemember_view_review",
            'reset' => true,
            'user_id' => $this->resource_id,
            'review_id' => $this->review_id,
            'slug' => $this->getSlug(),
            'tab' => $content_id,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return parent
     * */
    public function getAuthorizationItem() {
        return $this->getParent('user');
    }

    /**
     * Return description
     * */
    public function getDescription() {
        $tmpBody = strip_tags($this->body);
        return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
    }

    /**
     * Return rich content for feed items
     * */
    public function getRichContent() {
        $view = Zend_Registry::get('Zend_View');
        $view = clone $view;
        $view->clearVars();
        $view->addScriptPath('application/modules/Sitemember/views/scripts/');

        // Render the thingy
        $view->review = $this;
        $view->ratingData = $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitemember')->profileRatingbyCategory($this->getIdentity());

        $rating_value = 0;
        foreach ($ratingData as $ratingparam):
            if (empty($ratingparam['ratingparam_name'])):
                $rating_value = $ratingparam['rating'];
                break;
            endif;
        endforeach;
        $view->ratingValue = $rating_value;

        return $view->render('activity-feed/_review.tpl');
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $maxstrlen = 64) {

        if (null === $str) {
            $str = $this->title;
        }

        return Engine_Api::_()->seaocore()->getSlug($str, 225);
    }

    /**
     * Return rating data
     * */
    public function getRatingData() {
        return Engine_Api::_()->getDbtable('ratings', 'sitemember')->profileRatingbyCategory($this->getIdentity());
    }

    /**
     * Return helpful count
     * */
    public function getCountHelpful($type = 1) {
        return Engine_Api::_()->getDbtable('helpful', 'sitemember')->getCountHelpful($this->getIdentity(), $type);
    }

    /**
     * Return previous member
     * */
    public function getPreviousReview() {
        $select = $this->getTable()->select()
                ->where('status =?', 1)
                ->where('review_id < (?)', $this->review_id)
                ->where('resource_id =?', $this->resource_id)
                ->where('resource_type =?', $this->resource_type)
                ->where("type in (?)", array('user'))
                ->order('review_id DESC');
        return $this->getTable()->fetchRow($select);
    }

    /**
     * Return next member
     * */
    public function getNextReview() {
        $select = $this->getTable()->select()
                        ->where('status =?', 1)
                        ->where('review_id > (?)', $this->review_id)
                        ->where('resource_id =?', $this->resource_id)
                        ->where('resource_type =?', $this->resource_type)->where("type in (?)", array('user'));
        return $this->getTable()->fetchRow($select);
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Delete the members and belongings
     * 
     */
    public function _delete() {

        $review_id = $this->review_id;
        $db = Engine_Db_Table::getDefaultAdapter();

        $db->beginTransaction();
        try {

            //DELETE RATING ENTRIES
            $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitemember');
            $ratingTable->delete(array('review_id =?' => $review_id));

            //DELETE UPDATED ENTRIES
            $memberDescriptionsTable = Engine_Api::_()->getDbtable('reviewDescriptions', 'sitemember');
            $memberDescriptionsTable->delete(array('review_id =?' => $review_id));

            //DELETE RATING ENTRIES
            $memberHelpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitemember');
            $memberHelpfulTable->delete(array('review_id =?' => $review_id));

            $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
            $review_count = $tableUserInfo->getColumnValue($this->resource_id, 'review_count');
            if ($review_count) {
                $review_count = $review_count - 1;
                $tableUserInfo->update(array('review_count' => $review_count), array('user_id = ?' => $this->resource_id));
            }

            $ratingTable->userRatingUpdate($this->resource_id, $this->resource_type);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        parent::_delete();
    }

}