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
class Sitemember_Widget_ProfileFollowingSitememberController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $followEnabled = Engine_Api::_()->getApi("settings", "core")->getSetting('user.friends.direction', 1) && Engine_Api::_()->getApi("settings", "core")->getSetting('sitemember.user.follow.enable', 1);

        // Don't render this if two way friendship is disabled
        if (!$followEnabled) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        //SET PARAMS
        $this->view->params = $this->_getAllParams();
        $this->view->circularImage = $this->view->params['circularImage'] = $this->_getParam('circularImage', 0);
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
                $params['user_id'] = $subject->getIdentity();

                $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
                $select = $followTable->getFollowingSelect($subject,$params);
                $this->view->followingMembers = $followingMembers = Zend_Paginator::factory($select);
                $this->_childCount = $followingMembers->getTotalItemCount();
                // print_r($followingMembers->getTotalItemCount());die;
                return;
            }
        }
        $this->view->showContent = true;

        $params['user_id'] = $subject->getIdentity();
        $this->view->search = $params['search'] = $this->_getParam('search', null);

        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $select = $followTable->getFollowingSelect($subject,$params);
        $this->view->followingMembers = $followingMembers = Zend_Paginator::factory($select);

        $this->view->page = $this->_getParam('page', 1);
        // Set item count per page and current page number
        $followingMembers->setItemCountPerPage($this->_getParam('itemCount', 20));
        $followingMembers->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->isAjax = $this->_getParam('isAjax', null);
        if ($this->view->isAjax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        // Add count to title if configured
        if ($this->_getParam('titleCount', false) && $followingMembers->getTotalItemCount() > 0) {
            $this->_childCount = $followingMembers->getTotalItemCount();
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}