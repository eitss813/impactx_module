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
class Sitemember_Widget_UserReviewSitememberController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {
        
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3)
            return $this->setNoRender();
        
        //CHECK SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        //GET USER SUBJECT
        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');

        $this->view->user_id = $user_id = $user->getIdentity();

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');

        //SET PARAMS
        $this->view->params = $this->_getAllParams();

        //LOADED BY AJAX
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                $params['resource_id'] = $user_id;
                $params['resource_type'] = $user->getType();
                $params['type'] = 'user';
                $paginator = $reviewTable->listReviews($params);
                $this->_childCount = $paginator->getTotalItemCount();
                return;
            }
        }
        $this->view->showContent = true;

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $autorizationApi = Engine_Api::_()->authorization();
        $this->view->create_level_allow = $create_level_allow = $autorizationApi->getPermission($level_id, 'user', "review_create_member");
        $this->view->can_update = $can_update = $autorizationApi->getPermission($level_id, 'user', "review_update_member");

        $create_review = ($user->user_id == $viewer_id) ? 0 : 1;

        if (!$create_review || empty($create_level_allow)) {
            $this->view->can_create = 0;
        } else {
            $this->view->can_create = 1;
        }

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitemember');
        $coreApi = Engine_Api::_()->getApi('settings', 'core');

        //GET WIDGET PARAMETERS
        $this->view->sitemember_proscons = $sitemember_proscons = $coreApi->getSetting('sitemember.proscons', 0);
        $sitemember_limit_proscons = $coreApi->getSetting('sitemember.limit.proscons', 500);
        $sitemember_recommend = $coreApi->getSetting('sitemember.recommend', 0);
        $this->view->sitemember_report = $coreApi->getSetting('sitemember.report', 1);
        $this->view->sitemember_email = $coreApi->getSetting('sitemember.email', 1);
        $this->view->sitemember_share = $coreApi->getSetting('sitemember.share', 1);

        //GET REVIEW ID
        if (!empty($viewer_id)) {
            $params = array();
            $params['resource_id'] = $user->getIdentity();
            $params['resource_type'] = $user->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $review_id = $this->view->hasPosted = $reviewTable->canPostReview($params);
        } else {
            $review_id = $this->view->hasPosted = 0;
        }

        $this->view->withLightbox = 1;
        if (!$review_id && ($sitemember_proscons || $sitemember_recommend)) {
            $this->view->withLightbox = 0;
        }

        //CREATE FORM
        if ($this->view->can_create && !$review_id) {
            $this->view->form = new Sitemember_Form_Review_Create(array("settingsReview" => array('sitemember_proscons' => $sitemember_proscons, 'sitemember_limit_proscons' => $sitemember_limit_proscons, 'sitemember_recommend' => $sitemember_recommend), 'item' => $user));
        }

        //UPDATE FORM
        if ($can_update && $review_id) {
            $this->view->update_form = $update_form = new Sitemember_Form_Review_Update(array('item' => $user));
        }

        //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
        $params = array();
        $params['resource_id'] = $user_id;
        $params['resource_type'] = $user->getType();
        $params['type'] = 'user';

        $noReviewCheck = $reviewTable->getAvgRecommendation($params);
        ;
        $this->view->recommend_percentage = 0;
        if (!empty($noReviewCheck)) {
            $this->view->noReviewCheck = $noReviewCheck->toArray();

            if ($this->view->noReviewCheck)
                $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
        }

        $this->view->isajax = $this->_getParam('isajax', 0);

        //GET FILTER
        $option = $this->_getParam('option', 'fullreviews');
        $this->view->reviewOption = $params['option'] = $option;

        //SET ITEM PER PAGE
        if ($option == 'prosonly' || $option == 'consonly') {
            $this->view->itemProsConsCount = $setItemCountPerPage = $this->_getParam('itemProsConsCount', 20);
        } else {
            $this->view->itemReviewsCount = $setItemCountPerPage = $this->_getParam('itemReviewsCount', 5);
        }

        //GET SORTING ORDER
        $this->view->reviewOrder = $params['order'] = $this->_getParam('order', 'creationDate');
        $this->view->rating_value = $this->_getParam('rating_value', 0);
        $params['rating'] = 'rating';
        $params['rating_value'] = $this->view->rating_value;
        $params['resource_id'] = $user_id;
        $params['resource_type'] = $user->getType();
        $params['type'] = 'user';
        $this->view->params = $params;
        $paginator = $reviewTable->listReviews($params);
        $this->view->paginator = $paginator->setItemCountPerPage($setItemCountPerPage);
        $this->view->current_page = $current_page = $this->_getParam('page', 1);
        $this->view->paginator = $paginator->setCurrentPageNumber($current_page);
        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        //GET TOTAL REVIEWS
        $this->_childCount = $this->view->totalReviews = $tableUserInfo->getColumnValue($user_id, 'review_count');

        $this->view->total_reviewcats = 0;
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

            $this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($user_id, 'user', $user->getType());
        }

        $sitemember_overall_rating = Zend_Registry::isRegistered('sitemember_overall_rating') ? Zend_Registry::get('sitemember_overall_rating') : null;
        if (empty($sitemember_overall_rating))
            $this->view->setNoRender();

        //GET REVIEW RATE DATA
        $this->view->reviewRateMyData = $this->view->reviewRateData = $ratingTable->ratingsData($review_id, $viewer_id, $user_id);

        //CAN DELETE
        $this->view->can_delete = $autorizationApi->getPermission($level_id, 'user', "review_delete_member");

        //CAN REPLY
        $this->view->can_reply = $autorizationApi->getPermission($level_id, 'user', "review_reply_member");

        //CHECK PAGE
        $this->view->checkPage = "userProfile";

        //CUSTOM FIELDS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
        
        Engine_Api::_()->sitemember()->updateReviewCount();
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}