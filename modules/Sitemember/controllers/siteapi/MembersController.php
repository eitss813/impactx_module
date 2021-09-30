<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MembersController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_MembersController extends Siteapi_Controller_Action_Standard {

    /**
     * Return the "Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitemember')->getSearchForm());
    }

    /*
     * Get browse members page.
     * 
     * @return array
     */

    public function indexAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $customFieldValues = $params = array();
        $values = $this->getRequestAllParams;
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        
        $coreSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general');
        
         $coreSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general');
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if((!$viewer || !$viewer->getIdentity()) && empty($coreSetting['browse'])){
             $this->respondWithError('unauthorized');
        }
        // Start work to find out the profile fields.
        $getUserProfileFields = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getContentProfileFields('user');
        foreach ($getUserProfileFields as $profileFieldFormElement) {
            if (isset($values[$profileFieldFormElement['name']]) && !empty($values[$profileFieldFormElement['name']])) {
                $customFieldValues[$profileFieldFormElement['name']] = $values[$profileFieldFormElement['name']];
                unset($values[$profileFieldFormElement['name']]);
            }
        }

        $tmp = array();
        foreach ($customFieldValues as $k => $v) {
            if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                continue;
            } else if (false !== strpos($k, '_field_')) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } else if (false !== strpos($k, '_alias_')) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $customFieldValues = $tmp;
        // End work to find out the profile fields.
        // Set default limit
        if (!isset($values['limit']))
            $values['limit'] = (int) $this->getRequestParam('limit', 20);

        // Set default page
        if (!isset($values['page']))
            $values['page'] = (int) $this->getRequestParam('page', 1);

        if (isset($values['titleAjax']) && !empty($values['titleAjax']))
            $values['search'] = $values['titleAjax'];

        //Get an array of friends
        if (@$values['show'] == 2) {
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $values['users'] = $ids;
        }

        if (!isset($values['orderby']))
            $values['orderby'] = 'creation_date';

        if (isset($values['orderby']) && ($values['orderby'] == 'creationDate'))
            $values['orderby'] = 'creation_date';

        if (isset($values['orderby']) && ($values['orderby'] == 'viewCount'))
            $values['orderby'] = 'view_count';

        if (isset($values['location']) && empty($values['location'])) {
            unset($values['location']);

            if (isset($values['latitude']) && empty($values['latitude'])) {
                unset($values['latitude']);
            }

            if (isset($values['longitude']) && empty($values['longitude'])) {
                unset($values['longitude']);
            }

            if (isset($values['Latitude']) && empty($values['Latitude'])) {
                unset($values['Latitude']);
            }

            if (isset($values['Longitude']) && empty($values['Longitude'])) {
                unset($values['Longitude']);
            }
        }
        
        $enabledModules = (_CLIENT_TYPE == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroid.maplocation', 0) : Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.maplocation', '1');
        
        $enabledModules = @unserialize($enabledModules);
        $enabledModules = (_CLIENT_TYPE == 'android') ? $enabledModules['siteandroid_maplocation']:$enabledModules['siteiosapp_maplocation'] ;
        $enableLocationBrowse=0;
        if(!in_array('sitemember', $enabledModules) && empty($values['viewType'])){
            unset($values['restapilocation']);
        }
        

        //Member location based result.......................................
        if (empty($values['location'])) {
            if (isset($values['viewType']) && !empty($values['viewType'])) {
                if (isset($values['restapilocation']) & !empty($values['restapilocation'])) {
                    $values['location'] = $values['restapilocation'];
                    $values['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 0);
                } else {
                    $values['map_view'] = 1;
                }
            } else {
                if (!empty($values['restapilocation']))
                    $values['location'] = $values['restapilocation'];
                $values['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 0);
            }
        }
        else {
            if (empty($values['locationmiles']))
                $values['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 0);
        }

        if ($values['user_id']) {
            unset($values['restapilocation']);
            unset($values['location']);
            unset($values['locationmiles']);
        }

        if (!empty($values['location'])) {
            $values['orderby'] = 'distance';
        }
        //...................................

        try {
            $paginator = Engine_Api::_()->sitemember()->getUsersSelect($values, $customFieldValues);
            $paginator->setItemCountPerPage($values['limit']);
            $paginator->setCurrentPageNumber($values['page']);
            $viewer = Engine_Api::_()->user()->getViewer();
            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $users = array();

            foreach ($paginator as $user) {
                $tempUser = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                $tempUser['displayname'] = $user->getTitle();
                $verification = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification');

                //Member location details
                if (!empty($tempUser['seao_locationid'])) {
                    $locationObj = Engine_Api::_()->getItem('seaocore_locationitems', $tempUser['seao_locationid']);
                    if (isset($locationObj) && !empty($locationObj)) {
                        $tempUser = array_merge($tempUser, $locationObj->toArray());
                        if (empty($tempUser['city'])) {
                            $tempUser['city'] = '';
                        }
                        if (empty($tempUser['state'])) {
                            $tempUser['state'] = '';
                        }
                        if (empty($tempUser['country'])) {
                            $tempUser['country'] = '';
                        }
                    }
                }
                if (isset($tempUser['location']))
                    $tempUser['location'] = @strip_tags(html_entity_decode($tempUser['location'], ENT_QUOTES, "utf-8"));

                $tempUser['isVerified'] = $verification;
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify'))
                    $tempUser["is_member_verified"] = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getVerifyInfo($user);
                else {
                    $tempUser["is_member_verified"] = 0;
                }
                $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1);
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
                $following_count = $this->_followingCount($user);
                $follower_count = $this->_followerCount($user);
                $friend_count = $user->membership()->getMemberCount($user);
                if ($friend_count > 1) {
                    $ff_text_Titile = $this->translate('Friends');
                } else {
                    $ff_text_Titile = $this->translate('Friend');
                }

                if ($follower_count > 1) {
                    $af_text_Titile = $this->translate('Followers');
                } else {
                    $af_text_Titile = $this->translate('Follower');
                }

                $mappData = array(
                    "rv_count" => !empty($tempUser['rating_avg']) ? $tempUser['rating_avg'] : $tempUser['view_count'],
                    "rv_text" => !empty($tempUser['rating_avg']) ? $this->translate('Rating') : $this->translate('Views'),
                    "af_count" => !empty($tempUser['age']) ? $tempUser['age'] : $follower_count,
                    "af_text" => !empty($tempUser['age']) ? $this->translate('Age') : $af_text_Titile,
                    "ff_count" => !empty($friend_count) ? $friend_count : $following_count,
                    "ff_text" => !empty($friend_count) ? $ff_text_Titile : $this->translate('Following')
                );
                $tempUser['mapData'] = $mappData;

                $users[] = $tempUser;
            }

            $params['isSitemember'] = 1;
            $params['page'] = $values['page'];
            $params['totalItemCount'] = $paginator->getTotalItemCount();
            $params['response'] = $users;

            $this->respondWithSuccess($params);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    private function _followingCount($subject, $params = array()) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (empty($seocoreFollowTable))
            return 0;
        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $select1 = $followTable->getFollowingSelect($subject, $params);
        $followingMembers = Zend_Paginator::factory($select1);
        $follwingCount = 0;
        foreach ($followingMembers as $following) {
            if ($following->resource_type == 'user') {
                $user = Engine_Api::_()->getItem('user', $following->resource_id);

                if ($subject->getType() == 'user') {
                    $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($user, $subject);
                    if ($friendshipType == 'remove_friend') {
                        continue;
                    }
                }
                $follwingCount++;
            }
        }
        return $follwingCount;
    }

    private function _followerCount($subject, $params = array()) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (empty($seocoreFollowTable))
            return 0;

        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $select = $followTable->getFollowersSelect($subject, $params);
        $followers = Zend_Paginator::factory($select);
        $followerCount = 0;
        foreach ($followers as $following) {
            if ($following->poster_type == 'user') {
                $followuser = Engine_Api::_()->getItem('user', $following->poster_id);

                if ($subject->getType() == 'user') {
                    $friendshipType = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFriendshipType($followuser, $subject);

                    if ($friendshipType == 'remove_friend') {
                        continue;
                    }
                }
                $followerCount++;
            }
        }
        return $followerCount;
    }

}
