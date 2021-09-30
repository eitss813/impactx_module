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
class Sitemember_Widget_ReviewsStatisticsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->viewer = $viewer = 0;

        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        }

        $this->view->user = 0;

        if (Engine_Api::_()->core()->hasSubject()) {
            $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        }

        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitemember');

        $ratingCount = array();
        $sitemember_review_statistics = Zend_Registry::isRegistered('sitemember_review_statistics') ? Zend_Registry::get('sitemember_review_statistics') : null;

        if ($this->view->user && $this->view->viewer) {

            $request = Zend_Controller_Front::getInstance()->getRequest();
            $module = $request->getModuleName();
            $controller = $request->getControllerName();
            $action = $request->getActionName();
            if ($module == 'sitemember' && $controller == 'review' && $action == 'owner-reviews') {
                for ($i = 5; $i > 0; $i--) {
                    $ratingCount[$i] = Engine_Api::_()->getDbtable('ratings', 'sitemember')->getNumbersOfUserRating(0, 'user', 0, $i, $user->getIdentity(), 'user', 'owner');
                }

                $paginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'resource_type' => 'user', 'user_id' => $user->getIdentity()));

                $this->view->totalReviews = $paginator->getTotalItemCount();
                $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user', 'user_id' => $user->getIdentity()));
                $this->view->totalRecommend = $recommendpaginator->getTotalItemCount();
            } else {
                for ($i = 5; $i > 0; $i--) {
                    $ratingCount[$i] = Engine_Api::_()->getDbtable('ratings', 'sitemember')->getNumbersOfUserRating($user->getIdentity(), 'user', 0, $i, $viewer->getIdentity(), 'user');
                }

                $paginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'resource_type' => 'user',));

                $this->view->totalReviews = $paginator->getTotalItemCount();
                $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user'));
                $this->view->totalRecommend = $recommendpaginator->getTotalItemCount();
            }
        } else {
            for ($i = 5; $i > 0; $i--) {
                $ratingCount[$i] = Engine_Api::_()->getDbtable('ratings', 'sitemember')->getNumbersOfUserRating(0, 'user', 0, $i, 0, 'user');
            }

            $paginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'resource_type' => 'user',));

            $this->view->totalReviews = $paginator->getTotalItemCount();
            $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user'));
            $this->view->totalRecommend = $recommendpaginator->getTotalItemCount();
        }

        $this->view->ratingCount = $ratingCount;

        if (empty($sitemember_review_statistics))
            $this->view->setNoRender();
    }

}