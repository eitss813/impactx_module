<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ReviewController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_ReviewController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //CHECK SUBJECT
        if (Engine_Api::_()->core()->hasSubject())
            return;

        //SET REVIEW SUBJECT
        if (0 != ($review_id = (int) $this->_getParam('review_id')) &&
                null != ($review = Engine_Api::_()->getItem('sitemember_review', $review_id))) {
            Engine_Api::_()->core()->setSubject($review);
        } else if (0 != ($user_id = (int) $this->_getParam('user_id')) &&
                null != ($user = Engine_Api::_()->getItem('user', $user_id))) {
            Engine_Api::_()->core()->setSubject($user);
        }
        Engine_Api::_()->sitemember()->updateReviewCount();
    }

    public function browseAction() {

        //GET VIEWER INFO
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
        //GET PARAMS
        $params['type'] = '';

        $params = $this->_getAllParams();
        if (!isset($params['order']) || empty($params['order']))
            $params['order'] = 'recent';
        if (isset($params['show'])) {

            switch ($params['show']) {
                case 'friends_reviews':
                    $params['user_ids'] = $viewer->membership()->getMembershipsOfIds();
                    if (empty($params['user_ids']))
                        $params['user_ids'] = -1;
                    break;
                case 'self_reviews':
                    $params['user_id'] = $viewer_id;
                    break;
                case 'featured':
                    $params['featured'] = 1;
                    break;
            }
        }

        $params['resource_type'] = 'user';

        $searchForm = $this->view->searchForm = new Sitemember_Form_Review_Search();
        $searchForm->populate($this->_getAllParams());
        $searchParams = $searchForm->getValues();

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');

        //GET PAGINATOR
        $paginator = $reviewTable->getReviewsPaginator($params);
        $this->view->paginator = $paginator->setItemCountPerPage(10);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->searchParams = $searchParams;

        //GET TOTAL REVIEWS
        $this->view->totalReviews = $paginator->getTotalItemCount();
        $this->view->page = $this->_getParam('page', 1);
        $this->view->totalPages = ceil(($this->view->totalReviews) / 10);

        //RENDER
        if (!$isappajax)
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR WRITE A REVIEW
    public function createAction() {

        //USER SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('user')->isValid())
            return;

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER SUBJECT
        $user = Engine_Api::_()->core()->getSubject();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'user', "review_create_member");

        if (empty($can_create)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $postData = $this->getRequest()->getPost();

        if ($this->getRequest()->isPost() && $postData) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $coreApi = Engine_Api::_()->getApi('settings', 'core');
                $this->view->sitemember_proscons = $sitemember_proscons = $coreApi->getSetting('sitemember.proscons', 1);
                $this->view->sitemember_limit_proscons = $sitemember_limit_proscons = $coreApi->getSetting('sitemember.limit.proscons', 500);
                $this->view->sitemember_recommend = $sitemember_recommend = $coreApi->getSetting('sitemember.recommend', 0);

                $form = new Sitemember_Form_Review_Create(array("settingsReview" => array('sitemember_proscons' => $this->view->sitemember_proscons, 'sitemember_limit_proscons' => $this->view->sitemember_limit_proscons, 'sitemember_recommend' => $this->view->sitemember_recommend), 'item' => $user));
                $form->populate($postData);
                $otherValues = $form->getValues();

                $values = array_merge($postData, $otherValues);
                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $user->getIdentity();
                $values['resource_type'] = $user->getType();
                $values['type'] = 'user';
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0)) {
                    $values['recommend'] = 1;
                } else {
                    $values['recommend'] = 0;
                }
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitemember');
                $review = $reviewTable->createRow();
                $review->setFromArray($values);
                $review->view_count = 1;
                $review->save();

                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitemember');
                $reviewRatingTable->delete(array('review_id = ?' => 0, 'resource_id = ?' => $review->resource_id, 'type = ?' => 'user', 'resource_type = ?' => $review->resource_type));
                $postData['user_id'] = $viewer_id;
                $postData['review_id'] = $review->review_id;
                $postData['resource_id'] = $review->resource_id;
                $postData['resource_type'] = $review->resource_type;

                $review_count = Engine_Api::_()->getDbtable('ratings', 'sitemember')->getReviewId($viewer_id, $review->resource_type, $review->resource_id);

                if (count($review_count) == 0) {
                    //CREATE RATING DATA
                    $reviewRatingTable->createRatingData($postData, $values['type']);
                } else {
                    $reviewRatingTable->update(array('review_id' => $review->review_id, 'rating' => $postData['rating']), array('resource_type = ?' => $review->resource_type, 'user_id = ?' => $viewer_id, 'resource_id = ?' => $review->resource_id, 'type = ?' => 'user'));
                }

                //UPDATE RATING IN RATING TABLE
                $reviewRatingTable->userRatingUpdate($review->resource_id, $review->resource_type);

                if (empty($review_id) && time() >= strtotime($user->creation_date)) {
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

                    //ACTIVITY FEED
                    $action = $activityApi->addActivity($viewer, $user, 'sitemember_review_add');

                    if ($action != null) {
                        $activityApi->attachActivity($action, $review);
                    }
                }

                if ($user->user_id != $viewer_id && !empty($review->owner_id)) {
                    $object_parent_with_link = '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] . '/' . $user->getHref() . '">' . $user->getTitle() . '</a>';
                    $subjectOwner = $user->getOwner('user');
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                    $notifyApi->addNotification($subjectOwner, $viewer, $review, 'sitemember_write_review', array("object_parent_with_link" => $object_parent_with_link));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            echo Zend_Json::encode(array('captchaError' => 0, 'review_href' => $review->getHref()));
            exit();
        }
    }

    //ACTION FOR UPDATE THE REVIEW
    public function updateAction() {

        //REVIEW SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('sitemember_review')->isValid())
            return;

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $user = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'user', "review_update_member");

        if (empty($can_update)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $postData = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $postData) {
            $review_id = (int) $this->_getParam('review_id');
            $review = Engine_Api::_()->core()->getSubject();

            $form = new Sitemember_Form_Review_Update(array('item' => $user));
            $form->populate($postData);
            $otherValues = $form->getValues();
            $postData = array_merge($postData, $otherValues);

            $postData['user_id'] = $viewer_id;
            $postData['resource_id'] = $user->getIdentity();
            $postData['resource_type'] = $user->getType();
            $postData['review_id'] = $review_id;

            $reviewDescription = Engine_Api::_()->getDbtable('reviewDescriptions', 'sitemember');
            $reviewDescription->insert(array('review_id' => $review_id, 'body' => $postData['body'], 'modified_date' => date('Y-m-d H:i:s'), 'user_id' => $viewer_id));

            $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitemember');
            $reviewRatingTable->delete(array('review_id = ?' => $review_id));

            //CREATE RATING DATA
            $reviewRatingTable->createRatingData($postData, 'user');
            Engine_Api::_()->getDbtable('ratings', 'sitemember')->userRatingUpdate($user->getIdentity(), $user->getType());
            echo Zend_Json::encode(array('captchaError' => 0, 'review_href' => $review->getHref()));
            exit();
        }
    }

    public function rateAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $rating = $this->_getParam('rating');
        $user_id = $this->_getParam('user_id');

        $postData = array();

        $getMemberLocationViews = Engine_Api::_()->sitemember()->getMemberLocationViews();
        $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitemember');
        $review = $reviewRatingTable->getReviewId($viewer_id, 'user', $user_id);

        if (count($review) == 0) {
            //CREATE RATING DATA
            $postData['user_id'] = $viewer_id;
            $postData['review_id'] = 0;
            $postData['resource_id'] = $user_id;
            $postData['resource_type'] = 'user';
            $postData['member_rate_0'] = $rating;
            $values['type'] = 'user';
            $reviewRatingTable->createRatingData($postData, $values['type']);
        } else {
            $reviewRatingTable->update(array('rating' => $rating), array('resource_type = ?' => 'user', 'user_id = ?' => $viewer_id, 'resource_id = ?' => $user_id));
        }

        if (!empty($getMemberLocationViews))
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemember.viewtypeinfo.type', 0);

        //UPDATE RATING IN RATING TABLE
        if (!empty($viewer_id) && (count($review) == 0)) {
            $rating_only = 1;
            $user_rating = $reviewRatingTable->userRatingUpdate($user_id, 'user', $rating_only);
        } else {
            $rating_only = 1;
            $user_rating = $reviewRatingTable->userRatingUpdate($user_id, 'user', $rating_only);
        }

        $totalUsers = $reviewRatingTable->select()
                        ->from($reviewRatingTable->info('name'), 'COUNT(*) AS count')
                        ->where('user_id != ?', 0)
                        ->where('type = ?', 'user')
                        ->where('resource_id = ?', $user_id)
                        ->query()->fetchColumn();

        $data = array();
        $data[] = array(
            'rating' => $rating,
            'rating_users' => $user_rating,
            'users' => $totalUsers,
        );
        return $this->_helper->json($data);
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

    //ACTION FOR MARKING HELPFUL REVIEWS
    public function helpfulAction() {

        //NOT VALID USER THEN RETURN
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER DETAIL
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET RATING
        $helpful = $this->_getParam('helpful');

        //GET REVIEW ID
        $review_id = $this->_getParam('review_id');

        $anonymous = $this->_getParam('anonymous', 0);
        if (!empty($anonymous)) {
            return $this->_helper->redirector->gotoRoute(array('review_id' => $review_id, 'user_id' => $this->_getParam('user_id')), "sitemember_view_review", true);
        }

        //GET HELPFUL TABLE
        $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitemember');

        $this->view->already_entry = $helpfulTable->getHelpful($review_id, $viewer_id, $helpful);

        if (empty($this->view->already_entry)) {
            $this->view->already_entry = 0;
        }

        //MAKE ENTRY FOR HELPFUL
        $helpfulTable->setHelful($review_id, $viewer_id, $helpful);

        echo Zend_Json::encode(array('already_entry' => $this->view->already_entry));
        exit();
    }

    //ACTION FOR VIEW REVIEWS
    public function viewAction() {

        //IF ANONYMOUS USER THEN SEND HIM TO SIGN IN PAGE
        $check_anonymous_help = $this->_getParam('anonymous');
        if ($check_anonymous_help) {
            if (!$this->_helper->requireUser()->isValid())
                return;
        }

        //GET LOGGED IN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $review = Engine_Api::_()->core()->getSubject();
        if (empty($review)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //IF MODE IS APP  THEN FIXED THE HEADER.
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
        }

        $this->_helper->content
                ->setContentName('sitemember_review_view')
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR EMAIL THE REVIEW
    public function emailAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;
        $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
        //SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('sitemember_review')->isValid())
            return;

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');
        $user = Engine_Api::_()->getItem('user', (int) $this->_getParam('user_id'));
        $review = Engine_Api::_()->core()->getSubject();

        //GET FORM
        $this->view->form = $form = new Sitemember_Form_Review_Email();
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            Zend_Registry::set('setFixedCreationForm', true);
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
            Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Email Review'));
            Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Send'));
            $this->view->form->setAttrib('id', 'emailReviewForm');
            Zend_Registry::set('setFixedCreationFormId', '#emailReviewForm');
            $this->view->form->removeElement('send');
            $this->view->form->removeElement('cancel');
            $form->setTitle('');
        }
        //NOT VALID FORM POST THEN RETURN
        if (!$this->getRequest()->isPost())
            return;

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $postData = $this->getRequest()->getPost();
            $userComment = $postData['userComment'];

            //EDPLODES EMAIL IDS
            $reciver_ids = explode(',', $postData['emailTo']);

            //CHECK VALID EMAIL ID FORMITE
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
            }

            //SEND EMAIL
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEMEMBER_EMAIL_FRIEND', array(
                'user_email' => Engine_Api::_()->getItem('user', $viewer_id)->email,
                'userComment' => $userComment,
                'sender' => Engine_Api::_()->user()->getViewer()->displayname,
                'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
                'review_title' => $review->title,
                'review_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'user_id' => $user->user_id), "sitemember_view_review", true) . '">' . $review->title . '</a>',
                'email' => Engine_Api::_()->getApi('settings', 'core')->core_mail_from,
                'queue' => false
            ));

            if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
                $this->_forwardCustom('success', 'utility', 'core', array(
                    'parentRedirect' => $review->getHref(),
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
                ));
            else
                $this->_forwardCustom('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    //'parentRefreshTime' => '15',
                    //'format' => 'smoothbox',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
                ));
        }
    }

    //ACTION FOR DELETING REVIEW
    public function deleteAction() {

        //ONLY LOGGED IN USER CAN DELETE REVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('sitemember_review')->isValid())
            return;

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->review = $review = Engine_Api::_()->core()->getSubject();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET REVIEW ID AND REVIEW OBJECT
        $review_id = $this->_getParam('review_id');

        //AUTHORIZATION CHECK
        $can_delete = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', "review_delete_member");

        //WHO CAN DELETE THE REVIEW
        if (empty($can_delete) || ($can_delete == 1 && $viewer_id != $review->owner_id)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                //DELETE REVIEW FROM DATABASE
                Engine_Api::_()->getItem('sitemember_review', (int) $review_id)->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            
            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'parentRefresh' => true,
                       'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your review has been deleted successfully.'))
            ));
        } else {
            $this->renderScript('review/delete.tpl');
        }
    }

    public function topRatedAction() {

        $this->_helper->content
                ->setContentName('sitemember_review_top-rated')
                ->setNoRender()
                ->setEnabled();
    }

    public function mostRecommendedMembersAction() {

        $this->_helper->content
                ->setContentName('sitemember_review_most-recommended-members')
                ->setNoRender()
                ->setEnabled();
    }

    public function mostReviewedMembersAction() {

        $this->_helper->content
                ->setContentName('sitemember_most_reviewer-members')
                ->setNoRender()
                ->setEnabled();
    }

    public function topReviewersAction() {

        $this->_helper->content
                ->setContentName('sitemember_top-reviewers')
                ->setNoRender()
                ->setEnabled();
    }

    public function topRatersAction() {

        $this->_helper->content
                ->setContentName('sitemember_top-raters')
                ->setNoRender()
                ->setEnabled();
    }

    public function memberReviewsAction() {

        //USER SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('user')->isValid())
            return;

        $this->_helper->content
                ->setContentName('sitemember_review_member-reviews')
                ->setNoRender()
                ->setEnabled();
    }

    public function ownerReviewsAction() {

        //USER SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('user')->isValid())
            return;

        $this->_helper->content
                ->setContentName('sitemember_review_owner-reviews')
                ->setNoRender()
                ->setEnabled();
    }

}