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
class Sitemember_Widget_UserRatingsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3)
            return $this->setNoRender();
        
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            $this->view->viewer = $viewer = $user;
            $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        }

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitemember');

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');
        if ($viewer_id) {
            $params = array();
            $params['resource_id'] = $user->getIdentity();
            $params['resource_type'] = $user->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $this->view->review_id = $hasPosted = $reviewTable->canPostReview($params);
        } else {
            $this->view->review_id = $hasPosted = 0;
        }

        $autorizationApi = Engine_Api::_()->authorization();
        if ($autorizationApi->getPermission($level_id, 'user', "review_create_member") && empty($hasPosted)) {
            $this->view->createAllow = 1;
        } elseif ($autorizationApi->getPermission($level_id, 'user', "review_update_member") && !empty($hasPosted)) {
            $this->view->createAllow = 2;
        } else {
            $this->view->createAllow = 0;
        }

        $this->view->update_permission = $autorizationApi->getPermission($level_id, 'user', "review_update_member");

        $selectRatingTable = $ratingTable->select()
                ->from($ratingTable->info('name'), 'rating_id')
                ->where('resource_id = ?', $user->getIdentity())
                ->where('resource_type = ?', $user->getType())
                ->where('user_id = ?', $viewer_id);
        $this->view->rating_exist = $selectRatingTable->query()->fetchColumn();

        $show_rating = 0;
        if (!empty($this->view->rating_exist))
            $show_rating = 1;

       // if (empty($this->view->createAllow) && empty($show_rating))
            //return $this->setNoRender();

        $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $this->view->rating_users = $tableUserInfo->getColumnValue($user->getIdentity(), 'rating_users');
    }

}