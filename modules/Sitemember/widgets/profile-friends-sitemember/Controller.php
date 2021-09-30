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
class Sitemember_Widget_ProfileFriendsSitememberController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //General Friend settings
        $this->view->make_list = Engine_Api::_()->getApi('settings', 'core')->user_friends_lists;

        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Don't render this if friendships are disabled
        if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
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
                $select = Engine_Api::_()->sitemember()->getFriendsSelect(array('user_id' => $subject->getIdentity()));
                $friends = Zend_Paginator::factory($select);
                $this->_childCount = $friends->getTotalItemCount();
                return;
            }
        }
        $this->view->showContent = true;

        // Multiple friend mode
        $params['user_id'] = $subject->getIdentity();
        $this->view->search = $params['search'] = $this->_getParam('search', null);

        if (!$this->_getParam('mutual', null)) {
            $select = Engine_Api::_()->sitemember()->getFriendsSelect($params);
            $this->view->friends = $friends = Zend_Paginator::factory($select);
            $this->view->mutual = 0;
        } elseif ($this->_getParam('mutual', null)) {
            $this->view->friends = $friends = $mutualFriends = Engine_Api::_()->seaocore()->getMutualFriend($subject->getIdentity());
            $this->view->mutual = 1;
        }

        $this->view->mutualFriendsCount = Engine_Api::_()->seaocore()->getMutualFriend($subject->getIdentity(), 2)->getTotalItemCount();


        $this->view->friendsCount = $friends->getTotalItemCount();

        $this->view->page = $this->_getParam('page', 1);
        // Set item count per page and current page number
        $friends->setItemCountPerPage($this->_getParam('itemCount', 20));
        $friends->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->isAjax = $this->_getParam('isAjax', null);
        if ($this->view->isAjax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->resource_id;
        }
        $this->view->friendIds = $ids;

        // Get the items
        $friendUsers = array();
        foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
            $friendUsers[$friendUser->getIdentity()] = $friendUser;
        }
        $this->view->friendUsers = $friendUsers;

        // Get lists if viewing own profile
        if ($viewer->isSelf($subject)) {
            // Get lists
            $listTable = Engine_Api::_()->getItemTable('user_list');
            $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));

            $listIds = array();
            foreach ($lists as $list) {
                $listIds[] = $list->list_id;
            }

            // Build lists by user
            $listItems = array();
            $listsByUser = array();
            if (!empty($listIds)) {
                $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
                $listItemSelect = $listItemTable->select()
                        ->where('list_id IN(?)', $listIds)
                        ->where('child_id IN(?)', $ids);
                $listItems = $listItemTable->fetchAll($listItemSelect);
                foreach ($listItems as $listItem) {
                    //$list = $lists->getRowMatching('list_id', $listItem->list_id);
                    //$listsByUser[$listItem->child_id][] = $list;
                    $listsByUser[$listItem->child_id][] = $listItem->list_id;
                }
            }
            $this->view->listItems = $listItems;
            $this->view->listsByUser = $listsByUser;
        }

        // Do not render if nothing to show
//        if ($paginator->getTotalItemCount() <= 0) {
//            return $this->setNoRender();
//        }
        // Add count to title if configured
        if ($this->_getParam('titleCount', false) && $friends->getTotalItemCount() > 0) {
            $this->_childCount = $friends->getTotalItemCount();
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}