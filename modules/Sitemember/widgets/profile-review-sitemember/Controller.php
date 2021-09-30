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
class Sitemember_Widget_ProfileReviewSitememberController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3)
            return $this->setNoRender();
        
        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitemember_review')) {
            return $this->setNoRender();
        }

        //SET PARAMS
        $this->view->params = $params = $this->_getAllParams();

        //GET VIEWER DETAIL
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitemember');

        //GET USER
        $this->view->user = $user = Engine_Api::_()->getItem('user', Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id'));
        $this->view->user_id = $user_id = $user->getIdentity();
        $resource_type = $user->getType();

        //SET HAS POSTED
        if (empty($viewer_id)) {
            $hasPosted = $this->view->hasPosted = 0;
        } else {
            $params = array();
            $params['resource_id'] = $user_id;
            $params['resource_type'] = $resource_type;
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $hasPosted = $this->view->hasPosted = $reviewTable->canPostReview($params);
        }

        //GET WIDGET PARAMETERS
        $coreApi = Engine_Api::_()->getApi('settings', 'core');

        $this->view->sitemember_proscons = $sitemember_proscons = $coreApi->getSetting('sitemember.proscons', 1);
        $this->view->sitemember_limit_proscons = $sitemember_limit_proscons = $coreApi->getSetting('sitemember.limit.proscons', 500);
        $this->view->sitemember_recommend = $sitemember_recommend = $coreApi->getSetting('sitemember.recommend', 0);
        $this->view->sitemember_report = $coreApi->getSetting('sitemember.report', 1);
        $this->view->sitemember_email = $coreApi->getSetting('sitemember.email', 1);
        $this->view->sitemember_share = $coreApi->getSetting('sitemember.share', 1);

        $this->view->create_level_allow = $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'user', "review_create_member");

        $create_review = ($user->user_id == $viewer_id) ? 0 : 1;

        if (!$create_review || empty($create_level_allow)) {
            $this->view->can_create = 0;
        } else {
            $this->view->can_create = 1;
        }

        $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'user', "review_delete_member");

        $this->view->can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'user', "review_reply_member");

        $this->view->can_update = $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'user', "review_update_member");

        //MAKE CREATE FORM
        if ($this->view->can_create && !$hasPosted) {
            $this->view->form = $form = new Sitemember_Form_Review_Create(array("settingsReview" => array('sitemember_proscons' => $this->view->sitemember_proscons, 'sitemember_limit_proscons' => $this->view->sitemember_limit_proscons, 'sitemember_recommend' => $this->view->sitemember_recommend), 'item' => $user));
        }

        $this->view->review_id = $review_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id');

        //UPDATE FORM
        if ($can_update && $hasPosted) {
            $this->view->update_form = new Sitemember_Form_Review_Update(array('item' => $user));
        }

        //GET REVIEW ITEM
        $this->view->reviews = Engine_Api::_()->getItem('sitemember_review', $review_id);

        $params = array();
        $params['resource_id'] = $user_id;
        $params['resource_type'] = $resource_type;
        $params['type'] = 'user';
        $this->view->totalReviews = $reviewTable->totalReviews($params);

        //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
        $noReviewCheck = $reviewTable->getAvgRecommendation($params);
        $this->view->recommend_percentage = 0;
        if (!empty($noReviewCheck)) {
            $this->view->noReviewCheck = $noReviewCheck->toArray();
            if ($this->view->noReviewCheck)
                $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
        }

        $this->view->total_reviewcats = 0;
        $sitemember_profile_reviews = Zend_Registry::isRegistered('sitemember_profile_reviews') ? Zend_Registry::get('sitemember_profile_reviews') : null;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 2) {
            $profiletypeIdsArray = array();
            $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
            if (!empty($fieldsByAlias['profile_type'])) {
                $optionId = $fieldsByAlias['profile_type']->getValue($user);
                if ($optionId) {
                    $optionObj = Engine_Api::_()->fields()
                            ->getFieldsOptions($user)
                            ->getRowMatching('option_id', $optionId->value);
                    if ($optionObj) {
                        $profiletypeIdsArray[] = $optionObj->option_id;
                    }
                }
            }
            $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->memberParams($profiletypeIdsArray, $user->getType());
            $this->view->total_reviewcats = Count($this->view->reviewCategory);
            $this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($user_id, 'user', $resource_type);
        }

        $this->view->reviewRateData = $ratingTable->ratingsData($review_id);
        $this->view->reviewRateMyData = $ratingTable->ratingsData($hasPosted);
        $this->view->checkPage = "reviewProfile";
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', '');

        if (empty($sitemember_profile_reviews))
            $this->view->setNoRender();
    }

}