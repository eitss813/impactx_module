<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_OverallRatingsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3)
            return $this->setNoRender();
        
        //SET NO RENDER IF NO SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->sitemember = $sitemember = Engine_Api::_()->core()->getSubject('user');

        //GET SETTING
        $this->view->ratingParameter = $ratingParameter = $this->_getParam('ratingParameter', 1);
        $this->view->circularImage = $this->_getParam('circularImage', 0);
        $this->view->user_id = $user_id = $sitemember->user_id;
        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');
        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitemember');

        //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
        $params = array();
        $params['resource_id'] = $user_id;
        $params['resource_type'] = 'user';
        $noReviewCheck = $reviewTable->getAvgRecommendation($params);
        if (!empty($noReviewCheck)) {
            $this->view->noReviewCheck = $noReviewCheck->toArray();
            if ($this->view->noReviewCheck)
                $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
        }

        $sitemember_overall_rating = Zend_Registry::isRegistered('sitemember_overall_rating') ? Zend_Registry::get('sitemember_overall_rating') : null;
        if (empty($sitemember_overall_rating))
            return $this->setNoRender();

        $this->view->ratingData = $ratingTable->ratingbyCategory($user_id, 'user', 'user');
        $this->view->rating_avg = Engine_Api::_()->getDbtable('userInfo', 'seaocore')->getColumnValue($user_id, 'rating_avg');

        $params['resource_id'] = $user_id;
        $params['resource_type'] = 'user';
        $params['type'] = 'user';
        $paginator = $reviewTable->listReviews($params);
        //GET TOTAL REVIEWS
        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $this->view->totalReviews = $tableUserInfo->getColumnValue($user_id, 'review_count');

        if (empty($this->view->totalReviews))
            return $this->setNoRender();

        $this->view->contentDetails = Engine_Api::_()->sitemember()->getWidgetInfo('sitemember.user-review-sitemember', $this->view->identity);

        if (empty($this->view->contentDetails)) {
            $this->view->contentDetails->content_id = 0;
        }
    }

}