<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_IndexController extends Siteapi_Controller_Action_Standard {
    /**
     * Follow and unfollow a group
     */
    public function followAction() {
        // Validate request method
        $this->validateRequestMethod("POST");

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Get resource id and object
        $resource_id = $this->_getParam('resource_id');
        $resource_type = $this->_getParam('resource_type');
        $resourceObj = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (empty($resourceObj) || !isset($resourceObj))
            $this->respondWithError('no_record');

        //ADD ACTIVITY FEED
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $resource_type = $resourceObj->getType();
        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');

        try {

            if ($resource_type == 'user') {
                $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($resourceObj, $viewer);
            } else
                $follow = $followTable->getFollow($resourceObj, $viewer);

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            if ($follow) {
                $followTable->removeFollow($resourceObj, $viewer);

                if ($viewer_id != $resourceObj->getOwner()->getIdentity()) {
                    //DELETE ACTIVITY FEED
                    $action_id = Engine_Api::_()->getDbtable('actions', 'activity')
                            ->select()
                            ->from('engine4_activity_actions', 'action_id')
                            ->where('type = ?', "follow_$resource_type")
                            ->where('subject_id = ?', $viewer_id)
                            ->where('subject_type = ?', 'user')
                            ->where('object_type = ?', $resource_type)
                            ->where('object_id = ?', $resourceObj->getIdentity())
                            ->query()
                            ->fetchColumn();

                    if (!empty($action_id)) {
                        $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                        if (!empty($activity)) {
                            $activity->delete();
                        }
                    }
                }
            } else {
                $follow_id = $followTable->addFollow($resourceObj, $viewer);

                if ($viewer_id != $resourceObj->getOwner()->getIdentity()) {
                    $action = $activityApi->addActivity($viewer, $resourceObj, 'follow_' . $resource_type, '', array(
                        'owner' => $resourceObj->getOwner()->getGuid(),
                    ));
                    if (!empty($action))
                        $activityApi->attachActivity($action, $resourceObj);
                }
            }

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Get Followers Action
     */
    public function followersAction() {
        // Validate request method
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        $user_id = $resource_id = $this->_getParam('user_id', $this->_getParam('resource_id', null));
        $resource_type = $this->_getParam('resource_type', 'user');
        if ($user_id) {
            $user = Engine_Api::_()->getItem($resource_type, $user_id);
            if ($user) {
                Engine_Api::_()->core()->setSubject($user);
            }

            if (empty($user_id) || empty($user)) {
                $user = $viewer;
            }

            $followEnabled = Engine_Api::_()->getApi("settings", "core")->getSetting('user.friends.direction', 1) && Engine_Api::_()->getApi("settings", "core")->getSetting('sitemember.user.follow.enable', 1);
            try {
                $subject = Engine_Api::_()->core()->getSubject();
                if (!$subject->authorization()->isAllowed($viewer, 'view')) {
                    $this->respondWithError('unauthorized');
                }

                if ($resource_type == 'user')
                    $params['user_id'] = $subject->getIdentity();
                else
                    $params['resource_id'] = $subject->getIdentity();

                $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
                $select = $followTable->getFollowersSelect($subject, $params);
                $followers = Zend_Paginator::factory($select);

                // Set item count per page and current page number
                $followers->setItemCountPerPage($this->_getParam('limit', 20));
                $followers->setCurrentPageNumber($this->_getParam('page', 1));
                foreach ($followers as $following) {
                    if ($following->poster_type == 'user') {
                        $followuser = Engine_Api::_()->getItem('user', $following->poster_id);

                        if ($subject->getType() == 'user') {
                            $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($followuser, $subject);

                            if ($friendshipType == 'remove_friend') {
                                $this->removeFollow($user, $followuser);
                                continue;
                            }
                        }

                        $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($followuser);
                        $tempUser['displayname'] = $followuser->getTitle();
                        $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1);
                         $verification = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification',0);
                        $tempUser['isVerified'] = $verification;
                        if (empty($locationEnabled))
                            $tempUser['location'] = '';
                        // Add images
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($followuser);
                        $tempUser = array_merge($tempUser, $getContentImages);

                        $table = Engine_Api::_()->getDbtable('block', 'user');
                        $select = $table->select()
                                ->where('user_id = ?', $followuser->getIdentity())
                                ->where('blocked_user_id = ?', $viewer->getIdentity())
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        if ($row == NULL) {
                            $tempUser['menus'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($followuser);
                        } else {
                            $tempUser['menus'] = array();
                        }

                        // Add extra fields in case of Advanced Member Plugin.
                        $tempUser = array_merge($tempUser, Engine_Api::_()->getApi('Siteapi_Core', 'sitemember')->addAdvancedMemberSettings($followuser));
                        $users[] = $tempUser;
                    }
                }
                $params['isSitemember'] = 1;
                $params['totalItemCount'] = count($users);
                if (isset($users) && !empty($users))
                    $params['response'] = $users;

                $this->respondWithSuccess($params);
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function followingAction() {
        // Validate request method
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        // Try to set subject
        $user_id = $resource_id = $this->_getParam('user_id', $this->_getParam('resource_id', null));
        $resource_type = $this->_getParam('resource_type', 'user');
        if ($user_id) {
            $user = Engine_Api::_()->getItem($resource_type, $user_id);
            if ($user) {
                Engine_Api::_()->core()->setSubject($user);
            }

            if (empty($user_id) || empty($user)) {
                $user = $viewer;
            }
            try {

                $followEnabled = Engine_Api::_()->getApi("settings", "core")->getSetting('user.friends.direction', 1) && Engine_Api::_()->getApi("settings", "core")->getSetting('sitemember.user.follow.enable', 1);

                $subject = Engine_Api::_()->core()->getSubject('user');
                if (!$subject->authorization()->isAllowed($viewer, 'view')) {
                    $this->respondWithError('unauthorized');
                }

                $params['user_id'] = $subject->getIdentity();
                $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
                $select = $followTable->getFollowingSelect($subject, $params);
                $followingMembers = Zend_Paginator::factory($select);

                // Set item count per page and current page number
                $followingMembers->setItemCountPerPage($this->_getParam('limit', 20));
                $followingMembers->setCurrentPageNumber($this->_getParam('page', 1));

                foreach ($followingMembers as $following) {

                    if ($following->resource_type == 'user') {
                        $user = Engine_Api::_()->getItem('user', $following->resource_id);
                        if(!isset($user->user_id))
                            continue;
                        if ($subject->getType() == 'user') {
                            $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($user, $subject);
                            if ($friendshipType == 'remove_friend') {
                                $this->removeFollow($subject, $user);
                                continue;
                            }
                        }

                        $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                        $tempUser['displayname'] = $user->getTitle();
                        $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1);
                         $verification = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification',0);
                        $tempUser['isVerified'] = $verification;
                        if (empty($locationEnabled))
                            $tempUser['location'] = '';
                        // Add images
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                        $tempUser = array_merge($tempUser, $getContentImages);

                        $table = Engine_Api::_()->getDbtable('block', 'user');
                        $select = $table->select()
                                ->where('user_id = ?', $user->getIdentity())
                                ->where('blocked_user_id = ?', $viewer->getIdentity())
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        if ($row == NULL) {
                            $tempUser['menus'] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->userFriendship($user);
                        } else {
                            $tempUser['menus'] = array();
                        }

                        // Add extra fields in case of Advanced Member Plugin.
                        $tempUser = array_merge($tempUser, Engine_Api::_()->getApi('Siteapi_Core', 'sitemember')->addAdvancedMemberSettings($user));
                        $users[] = $tempUser;
                    }
                }

                $params['isSitemember'] = 1;
                $params['totalItemCount'] = count($users);
                $params['response'] = $users;

                $this->respondWithSuccess($params);
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function removeFollow($resource, $poster) {
        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $select = $followTable->select()
                ->where('poster_type = ?', $poster->getType())
                ->where('poster_id = ?', $poster->getIdentity())
                ->where('resource_type = ?', $resource->getType())
                ->where('resource_id = ?', $resource->getIdentity())
                ->limit(1);
        $row = $followTable->fetchRow($select);


        if (null === $row) {
            return;
        }

        $row->delete();

        if (isset($resource->follow_count)) {
            $resource->follow_count--;
            $resource->save();
        }
    }

}
