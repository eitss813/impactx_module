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
class Sitemember_Widget_ReviewedMembersSitememberController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        if (!$user->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        //FETCH REVIEW DATA
        $params = array();
        $sitemember_reviewed_members = Zend_Registry::isRegistered('sitemember_reviewed_members') ? Zend_Registry::get('sitemember_reviewed_members') : null;
        $this->view->statistics = $this->_getParam('statistics', array("likeCount", "replyCount", "commentCount"));
        $params['limit'] = $this->_getParam('itemCount', 3);
        $params['resource_type'] = 'user';
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $this->view->ownerPage = 0;
        if ($module == 'sitemember' && $controller == 'review' && $action == 'owner-reviews') {
            $params['resource_id'] = $user->user_id;
            $this->view->ownerPage = 1;
        } else {
            $params['owner_id'] = $user->user_id;
        }
        $params['order'] = $params['rating'] = 'rating';
        $this->view->reviews = $reviews = Engine_Api::_()->getDbtable('reviews', 'sitemember')->listReviews($params);

        if ($reviews->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }

        if (empty($sitemember_reviewed_members))
            $this->view->setNoRender();
    }

}