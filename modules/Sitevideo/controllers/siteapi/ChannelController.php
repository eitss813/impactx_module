<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    indexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_ChannelController extends Siteapi_Controller_Action_Standard {

    public function init() {
        
// only show videos if authorized
        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid())
            $this->respondWithError("unauthorized");
        
        if ($this->getRequestParam("channel_id") && (0 !== ($channel_id = (int) $this->getRequestParam("channel_id")) &&
                null !== ($channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id)))) {
            Engine_Api::_()->core()->setSubject($channel);
        }
        else if ($this->getRequestParam("channel_id") && (null !== ($channel_url = (string) $this->getRequestParam("channel_id")))) {
           $channel = Engine_Api::_()->getApi('Core','siteapi')->getSubjectByModuleUrl('sitevideo','channels','channel_url',$channel_url);  

            Engine_Api::_()->core()->setSubject($channel);
        }
    }

    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view'))
            $this->respondWithError('unauthorized');
        try {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getBrowseChannelForm(), true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of Manage Channels
     * 
     * @return array
     */

    public function manageAction() {
// Validate request methods

        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $params = $this->getRequestAllParams;
        $params['status'] = 1;
        $params['owner_id'] = $this->getRequestParam('user_id', null);
        $response = array();
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->checkRequire();

        if (!empty($params['category_id'])) {
            $customParam = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getCustomChannelField($params['category_id'], 'sitevideo_channel');

            $customParam = $customParam[$params['category_id']];
            foreach ($customParam as $key => $value) {
                if (false !== strpos($value['name'], '_field_')) {

                    if (isset($params[$value['name']]) && !empty($params[$value['name']])) {

                        $tempParam[$value['name']] = $params[$value['name']];
                    }
                }
            }
        }
        $params['owner_id'] = $this->getRequestParam('user_id', $viewer_id);

        try {
            $response = $this->_channelPaginator($params, true);
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of browse Channels
     * 
     * @return array
     */

    public function browseAction() {
// Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $params = $this->getRequestAllParams;
        if (!empty($params['user_id'])) {
            $params['owner_id'] = $params['user_id'];
        }
        $params['status'] = 1;
        $response = array();
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->checkRequire();

        if (!empty($params['category_id'])) {
            $customParam = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getCustomChannelField($params['category_id'], 'sitevideo_channel');

            $customParam = $customParam[$params['category_id']];
            foreach ($customParam as $key => $value) {
                if (false !== strpos($value['name'], '_field_')) {

                    if (isset($params[$value['name']]) && !empty($params[$value['name']])) {

                        $tempParam[$value['name']] = $params[$value['name']];
                    }
                }
            }
        }
        if ($params['orderby'] == 'featured')
            $params['filter'] = 'featured';

        try {
            $response = $this->_channelPaginator($params, $tempParam);

            $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->checkRequire();
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return the channel View page.
     * 
     * @return array
     */
    public function viewAction() {

// Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if (Engine_Api::_()->core()->hasSubject())
            $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        if (empty($channel))
            $this->respondWithError('no_record');


        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view')) {
            $module_error_type = @ucfirst($channel->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }

        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = $this->_gutterMenus($channel);

//GET THE VIDEO PROFILE TABS.
        if ($this->getRequestParam('profile_tabs', true))
            $bodyParams['profile_tabs'] = $this->_profileTAbsContainer($channel);

        $bodyParams['response'] = $channel->toArray();
        try {

// Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($channel);

            if (!empty($getContentImages))
                $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

// Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($channel, true);
            $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
            $ratingParams = array();
            $ratingParams['resource_id'] = $channel->getIdentity();
            $ratingParams['resource_type'] = 'channel';
            $bodyParams['response']["rating_count"] = Engine_Api::_()->getDbTable('ratings', 'sitevideo')->ratingCount($ratingParams);
// Getting viewer like or not to content.
            $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($channel);

// Getting like count.
            $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($channel);

            $channelTags = $channel->tags()->getTagMaps();
            if (!empty($channelTags)) {
                foreach ($channelTags as $tag) {
                    $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
                }

                $bodyParams['response']['tags'] = $tagArray;
            }

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($channel, $role, 'view'))
                    $bodyParams['response']['auth_view'] = $role;

                if ($auth->isAllowed($channel, $role, 'comment'))
                    $bodyParams['response']['auth_comment'] = $role;
            }

// Check if edit/delete is allowed
            $bodyParams['response']['can_edit'] = $can_edit = $this->_helper->requireAuth()->setAuthParams($channel, null, 'edit')->checkRequire();
            $bodyParams['response']['can_delete'] = $can_delete = $this->_helper->requireAuth()->setAuthParams($channel, null, 'delete')->checkRequire();


            if (!$channel->isOwner($viewer)) {
                $channel->view_count++;
                $channel->save();
            }

            if (isset($channel->profile_type) && !empty($channel->profile_type)) {
                $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getInformation($channel, 'sitevideo_channel');
                if (count($getProfileInfo) > 0)
                    $bodyParams['response']['ProfileInfo'] = $getProfileInfo;
            }

            $bodyParams['response']['rated'] = Engine_Api::_()->getDbtable('ratings', 'sitevideo')->checkRated($ratingParams);

            if ($channel->category_id) {
                $category = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getChannelCategory($channel->category_id);

                if (!empty($category) && isset($category->category_name))
                    $bodyParams['response']['category'] = $category->category_name;
            }

            $subscribeInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isSubscribedUser($channel->channel_id, $viewer->getIdentity());
            $bodyParams['response']['is_subscribe'] = count($subscribeInfo) > 0 ? 1 : 0;
            $bodyParams['response']['canUpload'] = Engine_Api::_()->authorization()->isAllowed($channel, $viewer, "photo");

            if (!empty($content_url['content_url']))
                $bodyParams['response']['content_url'] = $content_url['content_url'];
            $type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isFavourite($channel->getIdentity(), 'sitevideo_channel', $viewer->getIdentity());
            if ($type) {
                $bodyParams['is_favourite_option'] = 1;
            } else {
                $bodyParams['is_favourite_option'] = 0;
            }
            $chanUploadVideo = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
            $onwer_id = 0;
            try {
                $onwer_id = $channel->owner_id;
            } catch (Exception $ex) {
                
            }
            if (!empty($chanUploadVideo) && $viewer_id && $viewer_id == $onwer_id)
                $bodyParams['response']['canUploadVideo'] = 1;
            else
                $bodyParams['response']['canUploadVideo'] = 0;

            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $owner = $subject->getOwner();
        $menus = array();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $chanUploadVideo = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        $onwer_id = 0;
        try {
            $onwer_id = $subject->owner_id;
        } catch (Exception $ex) {
            
        }
        if (!empty($chanUploadVideo) && $viewer_id && $viewer_id == $onwer_id) {
            $menus[] = array(
                'label' => $this->translate('Upload Videos'),
                'name' => 'create',
                'url' => 'advancedvideos/create/',
                'urlParams' => array(
                    "main_channel_id" => $subject->getIdentity()
                )
            );
        }


        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete Channel'),
                'name' => 'delete',
                'url' => 'advancedvideo/channel/delete/' . $subject->getIdentity(),
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit Channel'),
                'name' => 'edit',
                'url' => 'advancedvideo/channel/edit/' . $subject->getIdentity(),
            );
        }

        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Suggest to friends'),
                'name' => 'suggest',
                'url' => 'advancedactivity/friends/suggest',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        if ($viewer->getIdentity() && ($viewer->getIdentity() != $owner->getIdentity())) {
            $menus[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        $type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isFavourite($subject->getIdentity(), 'sitevideo_channel', $viewer->getIdentity());

        if ($type) {
            if ($viewer->getIdentity()) {
                $menus[] = array(
                    'label' => $this->translate('Unfavourite'),
                    'name' => 'favourite',
                    'url' => 'advancedvideo/channel/favourite-channel/' . $subject->getIdentity(),
                    'urlParams' => array(
                        'value' => 0
                    )
                );
            }
        } else {

            if ($viewer->getIdentity()) {
                $menus[] = array(
                    'label' => $this->translate('Favourite'),
                    'name' => 'favourite',
                    'url' => 'advancedvideo/channel/favourite-channel/' . $subject->getIdentity(),
                    'urlParams' => array(
                        'value' => 1
                    )
                );
            }
        }

        return $menus;
    }

    private function _profileTAbsContainer($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject_id = $subject->getIdentity();
        $value = array();
        $values['status'] = 1;

        $values['owner_id'] = $this->getRequestParam('user_id', null);
        $values['channel_id'] = $subject->channel_id;
        $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($values);
        $videoCount = $paginator->getTotalItemCount();
        if ($subject_id) {
            $response[] = array(
                'name' => 'video',
                'label' => $this->translate('Videos'),
                'totalItemCount' => $videoCount,
                'url' => 'advancedvideo/channel/videos/' . $subject->getIdentity()
            );
        }

        if (!empty($viewer))
            $viewer_id = $viewer->getIdentity();
        $response[] = array(
            'name' => 'update',
            'label' => $this->translate('Updates'),
        );

        $hasOverview = true;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.overview', 1)) {
            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitevideo');
            $overview = $tableOtherinfo->getColumnValue($subject->getIdentity(), 'overview');
            $hasOverview = !empty($overview);
        }
        if ($hasOverview) {

            $response[] = array(
                'label' => $this->translate('Overview'),
                'name' => 'overview',
                'url' => 'advancedvideo/channel/description/' . $subject->getIdentity()
            );
        }

        if (!empty($subject_id)) {

            $response[] = array(
                'label' => $this->translate('Information'),
                'name' => 'information',
                'url' => 'advancedvideo/channel/information/' . $subject->getIdentity()
            );
        }

        if (!empty($subject_id)) {
            $subscribedUsersId = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo')->getSubscribedUser($subject->getIdentity(),null);
            $subsTotal=0;
            foreach ($subscribedUsersId as $value) {
                $user_subject = Engine_Api::_()->user()->getUser($value['owner_id']);
                if (!empty($user_subject)) {
                    $flag = $user_subject->toArray();
                    if (empty($flag))
                        continue;
                }
                elseif (empty($user_subject))
                    continue;
                $subsTotal++;
            }

            $response[] = array(
                'name' => 'Subscriber',
                'label' => $this->translate('Subscribers'),
                'totalItemCount' => $subsTotal,
                'url' => 'advancedvideo/channel/subscribers/' . $subject->getIdentity()
            );
        }

        if ($subject_id) {
            $photoTotal = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->countPhoto($subject->getIdentity());
            $response[] = array(
                'name' => 'photos',
                'label' => $this->translate('Photos'),
                'totalItemCount' => $photoTotal,
                'url' => 'advancedvideo/channel/photo/' . $subject->getIdentity(),
            );
        }

        return $response;
    }

    public function videosAction() {
// Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        if (empty($channel))
            $this->respondWithError('no_record');
        $values = array();
        $values = $this->getRequestAllParams;
        $values['status'] = 1;
//        $values['search'] = 1;
        $values['owner_id'] = $this->getRequestParam('user_id', null);
        $values['channel_id'] = $channel->channel_id;
        try {

            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($values);
            $items_count = $this->getRequestParam("limit", 20);
            $paginator->setItemCountPerPage($items_count);
            $requestPage = $this->getRequestParam('page', 1);
            $paginator->setCurrentPageNumber($requestPage);
            foreach ($paginator as $video) {
                $browseVideo = $video->toArray();
                if (!isset($browseVideo['video_id']) || empty($browseVideo['video_id']))
                    continue;
                $browseVideo['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
                $ratingParams = array();
                $ratingParams['resource_id'] = $browseVideo['video_id'];
                $ratingParams['resource_type'] = 'video';
// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
                $browseVideo = array_merge($browseVideo, $getContentImages);

// Add owner images
                if ($video->owner_id) {
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
                    $browseVideo = array_merge($browseVideo, $getContentImages);

                    $browseVideo["owner_title"] = $video->getOwner()->getTitle();
                }
                $isAllowedView = $video->authorization()->isAllowed($viewer, 'view');
                $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browseVideo["like_count"] = $video->likes()->getLikeCount();
                $browseVideo["rating_count"] = Engine_Api::_()->getDbTable('ratings', 'sitevideo')->ratingCount($ratingParams);
                $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoURL($video);



                $response['response'][] = $browseVideo;
            }

            if (count($response) > 0) {
                $response['totalItemCount'] = $paginator->getTotalItemCount();
            } else {
                $response['totalItemCount'] = 0;
            }

            $subscribeInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isSubscribedUser($channel->category_id, $viewer->getIdentity());
            $response['is_subscribe'] = count($subscribeInfo) > 0 ? 1 : 0;

            $response['totalItemCount'] = $paginator->getTotalItemCount();
            $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();
            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return the All channel subscriber details.
     * 
     * @return array
     */
    public function subscribersAction() {
// VALIDATE REQUEST METHOD
        $this->validateRequestMethod();
//GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        try {


            $channel_id = $this->_getParam('channel_id');
            $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

            if (empty($channel) && !isset($channel))
                $this->respondWithError('no_record');

            $subscribedUsersId = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo')->getSubscribedUser($channel_id);

            foreach ($subscribedUsersId as $value) {
                $user_subject = Engine_Api::_()->user()->getUser($value['owner_id']);
                if (!empty($user_subject)) {
                    $flag = $user_subject->toArray();
                    if (empty($flag))
                        continue;
                }
                elseif (empty($user_subject))
                    continue;

                $userData = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user_subject);
                $verification = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification');
                $userData['isVerified'] = $verification;
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user_subject);
                $response['members'][] = array_merge($userData, $getContentImages);
            }

            $response['getTotalItemCount'] =$response['totalItemCount'] = count($response['members']);
            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return overview about channel.
     * 
     * @return array
     */
    public function descriptionAction() {

// VALIDATE REQUEST METHOD
        $this->validateRequestMethod();

//GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $channel_id = $this->_getParam('channel_id');
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        if (empty($channel) && !isset($channel))
            $this->respondWithError('no_record');
        try {

            $hasOverview = true;
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.overview', 1)) {
                $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitevideo');
                $overview['response'] = $tableOtherinfo->getColumnValue($channel->getIdentity(), 'overview');
                $hasOverview = !empty($overview);
            }
            if ($hasOverview)
                $this->respondWithSuccess($overview['response'], true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return  all photo of channel.
     * 
     * @return array
     */
    public function photoAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $photo_id = (int) $this->_getParam('photo_id');

// CHECK AUTHENTICATION
        if (Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            $channel = $subject = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        } else if (Engine_Api::_()->core()->hasSubject('sitevideo_photo')) {
            $photo = $subject = Engine_Api::_()->core()->getSubject('sitevideo_photo');
            $channel_id = $photo->channel_id;
            $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        }
        $bodyResponse = $tempResponse = array();

        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, "photo");
        if (empty($_FILES) && $this->getRequest()->isGet()) {
            $requestLimit = $this->getRequestParam("limit", 10);
            $page = $requestPage = $this->getRequestParam("page", 1);

//GET PAGINATOR

            $album = $channel->getSingletonAlbum();

            $paginator = $album->getCollectiblesPaginator();


            $bodyResponse[' totalPhotoCount'] = $totalItemCount = $bodyResponse['totalItemCount'] = $paginator->getTotalItemCount();
            $paginator->setItemCountPerPage($requestLimit);
            $paginator->setCurrentPageNumber($requestPage);
// Check the Page Number for pass photo_id.

            if (!empty($photo_id)) {
                for ($page = 1; $page <= ceil($totalItemCount / $requestLimit); $page++) {
                    $paginator->setCurrentPageNumber($page);
                    $tmpGetPhotoIds = array();
                    foreach ($paginator as $photo) {
                        $tmpGetPhotoIds[] = $photo->photo_id;
                    }
                    if (in_array($photo_id, $tmpGetPhotoIds)) {
                        $bodyResponse['page'] = $page;
                        break;
                    }
                }
            }

            if ($totalItemCount > 0) {
                foreach ($paginator as $photo) {
                    $tempImages = $photo->toArray();

// Add images
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
                    $tempImages = array_merge($tempImages, $getContentImages);

                    $tempImages['user_title'] = $photo->getOwner()->getTitle();
                    $tempImages['likes_count'] = $photo->likes()->getLikeCount();
                    $tempImages['is_like'] = ($photo->likes()->isLike($viewer)) ? 1 : 0;

                    if (isset($tempImages) && !empty($tempImages))
                        $bodyResponse['images'][] = $tempImages;
                }
            }
            $bodyResponse['canUpload'] = $allowed_upload_photo;
            $this->respondWithSuccess($bodyResponse, true);
        }
        else if (isset($_FILES) && $this->getRequest()->isPost()) {
            if (empty($viewer_id) || empty($allowed_upload_photo))
                $this->respondWithError('unauthorized');
            $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitevideo');
            $db = $tablePhoto->getAdapter();
            $db->beginTransaction();

            try {
                $viewer = Engine_Api::_()->user()->getViewer();
                $album = $channel->getSingletonAlbum();
                $rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
                $order = 0;
                if (!empty($rows)) {
                    $order = $rows->order + 1;
                }
                $params = array(
                    'collection_id' => $album->getIdentity(),
                    'album_id' => $album->getIdentity(),
                    'channel_id' => $channel->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'order' => $order,
                );
                if (!empty($_POST['title'])) {
                    $params['title'] = $_POST['title'];
                }
                if (!empty($_POST['description'])) {
                    $params['description'] = $_POST['description'];
                }
                $photoCount = count($_FILES);
                if (isset($_FILES['photo']) && $photoCount == 1) {
                    $photo_id = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->createPhoto($params, $_FILES['photo'])->file_id;
                    if (!$channel->file_id) {
                        $channel->file_id = $photo_id;
                        $channel->save();
                    }
                } else if (!empty($_FILES) && $photoCount > 1) {
                    foreach ($_FILES as $photo) {
                        Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->createPhoto($params, $photo);
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Get Categories , Sub-Categories, SubSub-Categories and channel array
     */
    public function categoriesAction() {

// VALIDATE REQUEST METHOD
        $this->validateRequestMethod();

//GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();


// PREPARE RESPONSE
        $values = $response = array();
        $category_id = $this->getRequestParam('category_id', null);
        $subCategory_id = $this->getRequestParam('subCategory_id', null);
        $subsubcategory_id = $this->getRequestParam('subsubcategory_id', null);
        $showAllCategories = $this->getRequestParam('showAllCategories', 1);
        $showCategories = $this->getRequestParam('showCategories', 1);
        $showChannels = $this->getRequestParam('showChannels', 1);

        if ($this->getRequestParam('showCount')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }
        $orderBy = $this->getRequestParam('orderBy', 'category_name');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $categories = array();
        $images = array();
//GET Channel Category  TABLE
        try {


            $category_info = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoriesPaginator(array('cat_depandancy' => 1));
            $items_count = $this->getRequestParam("limit", 20);
            $category_info->setItemCountPerPage($items_count);
            $requestPage = $this->getRequestParam('page', 1);
            $category_info->setCurrentPageNumber($requestPage);

            $response['totalItemCount'] = $category_info->getTotalItemCount();


            if ($showCategories) {
                if ($showAllCategories) {
                    $categoriesCount = count($category_info);
                    foreach ($category_info as $value) {
                        $category_image = '';
                        $category_icon = '';
                        $sub_cat_array = array();

                        if ($value->video_id) {
                            $category_image = Engine_Api::_()->storage()->get($value->video_id, '')->getPhotoUrl();
                            $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                            if ($value->file_id) {
                                $category_icon = Engine_Api::_()->storage()->get($value->file_id, '')->getPhotoUrl();
                                $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                            }
                            $images = array(
                                "image_icon" => $category_icon,
                                "image" => $category_image
                            );
                        } else {
                            $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                        }


                        if ($showCount) {
                            $categories[] = $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'count' => Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                'images' => $images
                            );
                        } else {
                            $categories[] = $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'images' => $images
                            );
                        }
                    }
                } else {
                    $category_info = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoriesPaginator(array('cat_dependency' => 'yes', 'havingChannels' => 1));
//$categoriesCount = count($category_info);

                    foreach ($category_info as $value) {
                        $category_image = '';
                        $category_icon = '';
                        $sub_cat_array = array();
                        if ($value->video_id) {
                            $category_image = Engine_Api::_()->storage()->get($value->video_id, '')->getPhotoUrl();
                            $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                            if ($value->file_id) {
                                $category_icon = Engine_Api::_()->storage()->get($value->file_id, '')->getPhotoUrl();
                                $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                            }
                            $images = array(
                                "image_icon" => $category_icon,
                                "image" => $category_image
                            );
                        } else {
                            $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                        }
                        if ($showCount) {
                            $categories[] = $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'count' => Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                'images' => $images
                            );
                        } else {
                            $categories[] = $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'images' => $images
                            );
                        }
                    }
                }

                $response['categories'] = $categories;

                if (!empty($category_id)) {

                    if ($showAllCategories) {
                        $subSategory_info = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array("category_id" => $category_id, 'havingChannels' => 0, 'fetchColumns' => '*'));
                        $subcategoriesCount = count($subSategory_info);

                        foreach ($subSategory_info as $value) {
                            $category_image = '';
                            $category_icon = '';
                            $sub_cat_array = array();
                            if ($value->video_id) {
                                $category_image = Engine_Api::_()->storage()->get($value->video_id, '')->getPhotoUrl();
                                $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                if ($value->file_id) {
                                    $category_icon = Engine_Api::_()->storage()->get($value->file_id, '')->getPhotoUrl();
                                    $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                                }
                                $images = array(
                                    "image_icon" => $category_icon,
                                    "image" => $category_image
                                );
                            } else {
                                $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                            }
                            if ($showCount) {
                                $sub_cat_array[] = $category_array = array('sub_cat_id' => $value->category_id,
                                    'sub_cat_name' => $this->translate($value->category_name),
                                    'count' => Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                    'images' => $images
                                );
                            } else {
                                $sub_cat_array[] = $category_array = array('sub_cat_id' => $value->category_id,
                                    'sub_cat_name' => $this->translate($value->category_name),
                                    'images' => $images
                                );
                            }
                        }
                    } else {
                        $subSategory_info = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array("category_id" => $category_id, 'havingChannels' => 1, 'fetchColumns' => '*'));


                        foreach ($subSategory_info as $value) {
                            $category_image = '';
                            $category_icon = '';
                            $sub_cat_array = array();
                            if ($value->video_id) {
                                $category_image = Engine_Api::_()->storage()->get($value->video_id, '')->getPhotoUrl();
                                $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                if ($value->file_id) {
                                    $category_icon = Engine_Api::_()->storage()->get($value->file_id, '')->getPhotoUrl();
                                    $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                                }
                                $images = array(
                                    "image_icon" => $category_icon,
                                    "image" => $category_image
                                );
                            } else {
                                $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                            }
                            if ($showCount) {
                                $categories[] = $category_array = array('sub_cat_id' => $value->category_id,
                                    'sub_cat_name' => $this->translate($value->category_name),
                                    'count' => Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                    'images' => $images
                                );
                            } else {
                                $categories[] = $category_array = array('category_id' => $value->category_id,
                                    'category_name' => $this->translate($value->category_name),
                                    'images' => $images
                                );
                            }
                        }
                    }
                    $response['subCategories'] = $sub_cat_array;

                    if (!empty($subCategory_id)) {

                        if ($showAllCategories) {

                            $subsubSategory_info = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array("category_id" => $subCategory_id, 'fetchColumns' => '*'));

                            $subcategoriesCount = count($subsubSategory_info);

                            foreach ($subsubSategory_info as $value) {
                                $category_image = '';
                                $category_icon = '';
                                $subsub_cat_array = array();
                                if ($value->video_id) {
                                    $category_image = Engine_Api::_()->storage()->get($value->video_id, '')->getPhotoUrl();
                                    $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                    if ($value->file_id) {
                                        $category_icon = Engine_Api::_()->storage()->get($value->file_id, '')->getPhotoUrl();
                                        $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                                    }
                                    $images = array(
                                        "image_icon" => $category_icon,
                                        "image" => $category_image
                                    );
                                } else {
                                    $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                                }
                                if ($showCount) {
                                    $subsub_cat_array[] = $category_array = array('tree_sub_cat_id' => $value->category_id,
                                        'tree_sub_cat_name' => $this->translate($value->category_name),
                                        'count' => Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                        'images' => $images
                                    );
                                } else {
                                    $subsub_cat_array[] = $category_array = array('tree_sub_cat_id' => $value->category_id,
                                        'tree_sub_cat_name' => $this->translate($value->category_name),
                                        'images' => $images
                                    );
                                }
                            }
                        } else {
                            $subSategory_info = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getSubCategories(array("category_id" => $subCategory_id, 'havingChannels' => 1, 'fetchColumns' => '*'));

                            foreach ($subsubSategory_info as $value) {
                                $category_image = '';
                                $category_icon = '';
                                $subsub_cat_array = array();
                                if ($value->video_id) {
                                    $category_image = Engine_Api::_()->storage()->get($value->video_id, '')->getPhotoUrl();
                                    $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                    if ($value->file_id) {
                                        $category_icon = Engine_Api::_()->storage()->get($value->file_id, '')->getPhotoUrl();
                                        $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                                    }
                                    $images = array(
                                        "image_icon" => $category_icon,
                                        "image" => $category_image
                                    );
                                } else {
                                    $images = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                                }
                                if ($showCount) {
                                    $subsub_cat_array[] = $category_array = array('tree_sub_cat_id' => $value->category_id,
                                        'tree_sub_cat_name' => $this->translate($value->category_name),
                                        'count' => Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelsCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                        'images' => $images
                                    );
                                } else {
                                    $subsub_cat_array[] = $category_array = array('tree_sub_cat_id' => $value->category_id,
                                        'tree_sub_cat_name' => $this->translate($value->category_name),
                                        'images' => $images
                                    );
                                }
                            }
                        }
                        $response['subsubCategories'] = $subsub_cat_array;
                    }
                }
            }

            if (!empty($showChannels) && !empty($category_id) && isset($category_id)) {
                $params = array();

                $params['category_id'] = $category_id;
                $params['subcategory_id'] = $subCategory_id;
                $params['subsubcategory_id'] = $subsubcategory_id;
                $params['status'] = 1;
//$params['search'] = 1;

                $user_id = $this->getRequestParam('user_id', null);
                $response['channels'] = $this->_channelPaginator($params);
            }
            $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->checkRequire();

            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * add or remove video from favourite.
     * return array
     */
    public function favouriteChannelAction() {
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        if (Engine_Api::_()->core()->hasSubject())
            $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        if (empty($channel))
            $this->respondWithError('no_record');

        $values = $this->_getAllParams();

        if (isset($values['value'])) {
            try {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->favourite($values['channel_id'], 'sitevideo_channel', $values['value']);

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        } else {
            $this->respondWithValidationError("parameter_missing", "value");
        }
    }

    private function _channelPaginator($params = array(), $isManage = false, $customParams = array()) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // FIND USERS' FRIENDS
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!empty($params['view_view']) && $params['view_view'] == 1) {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        if ($params['orderby'] == 'best_channel' || $params['orderby'] == 'featured' || $params['orderby'] == 'sponsored') {
            if ($params['orderby'] == 'best_channel')
                $params['showChannel'] = 'featuredSponsored';
            else {
                $params['showChannel'] = $params['orderby'];
            }

            $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->channelBySettings($params);
        } elseif ($params['orderby'] == 'favourite_count' && $isManage) {
            $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getFavouriteChannelPaginator($params);
        } elseif ($params['orderby'] == 'rating' && $isManage) {
            $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getRatedChannelPaginator($params);
        } elseif ($params['orderby'] == 'like_count' && $isManage) {
            $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getLikedChannelPaginator($params);
        } elseif ($params['orderby'] == 'subscribe_count') {
            $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getSubscribedChannelPaginator($params);
        } else
            $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelPaginator($params, $tempParam);

        $items_count = $this->getRequestParam("limit", 20);
        $paginator->setItemCountPerPage($items_count);
        $requestPage = $this->getRequestParam('page', 1);
        $paginator->setCurrentPageNumber($requestPage);

        foreach ($paginator as $channel) {
            if (isset($params['showChannel']) && !empty($params['showChannel'])) {
                $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel->getIdentity());
                $browseChannel = $channel->toArray();
            } else
                $browseChannel = $channel->toArray();
            $ratingParams = array();
            $ratingParams['resource_id'] = $browseChannel['channel_id'];
            $ratingParams['resource_type'] = 'channel';

// Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($channel);
            $browseChannel = array_merge($browseChannel, $getContentImages);

// Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($channel, true);
            $browseChannel = array_merge($browseChannel, $getContentImages);

            $browseChannel["owner_title"] = $channel->getOwner()->getTitle();
            $isAllowedView = $channel->authorization()->isAllowed($viewer, 'view');
            $browseChannel["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
            $browseChannel["like_count"] = $channel->likes()->getLikeCount();
            $browseChannel["rating_count"] = Engine_Api::_()->getDbTable('ratings', 'sitevideo')->ratingCount($ratingParams);
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($channel, $role, 'view'))
                    $browseChannel['auth_view'] = $role;

                if ($auth->isAllowed($channel, $role, 'comment'))
                    $browseChannel['auth_comment'] = $role;
            }

            $menus = array();
            if ($isManage) {
                if ($channel->authorization()->isAllowed($viewer, 'delete')) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Channel'),
                        'name' => 'delete',
                        'url' => 'advancedvideo/channel/delete/' . $channel->getIdentity(),
                    );
                }

                if ($channel->authorization()->isAllowed($viewer, 'edit')) {
                    $menus[] = array(
                        'label' => $this->translate('Edit Channel'),
                        'name' => 'edit',
                        'url' => 'advancedvideo/channel/edit/' . $channel->getIdentity(),
                    );
                }
            }
            $browseChannel['menu'] = $menus;

            $response['response'][] = $browseChannel;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $multiOPtionsOrderBy = array();
        if (!$isManage) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Most Recent',
                'modified_date' => 'Recently Updated',
                'view_count' => 'Most View',
                'like_count' => 'Most Liked',
                'comment_count' => 'Most Commented',
                'favourite_count' => 'Most Favourite',
                'featured' => 'Featured',
                'best_channel' => 'Best Channel',
                'videos_count' => 'Most Videos',
                'sponsored' => 'Sponsored',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );
//GET API
        } else {
            $multiOPtionsOrderBy = array(
                'subscribe_count' => 'Subscribed',
                'like_count' => 'Liked',
                'rating' => 'Rated',
                'favourite_count' => 'Favourite',
            );
        }
        $enableRating = $settings->getSetting('sitevideo.rating', 1);

        if ($enableRating) {
            $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
        }

        $filter = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => $this->translate('Browse By'),
            'multiOptions' => $this->translate($multiOPtionsOrderBy)
        );
        $response['filter'] = $filter;

        $response['totalItemCount'] = $paginator->getTotalItemCount();
        return $response;
    }

    public function createAction() {
        //Viewer information
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->isValid())
            $this->respondWithError('unauthorized');
        $params['owner_id'] = $viewer_id;
        if ($this->getRequest()->isGet()) {
            try {
                $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getChannelForm());
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        } else if ($this->getRequest()->isPost()) {
            $values = $data = $_REQUEST;
            //form validation
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getChannelForm();
            foreach ($getForm['form'] as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            $data = $values;
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getChannelFormValidators();
            $data['validators'] = $validators;
            $videovalidationMessage = $this->isValid($data);

            if (!empty($videovalidationMessage) && @is_array($videovalidationMessage)) {
                $this->respondWithValidationError('validation_fail', $videovalidationMessage);
            }
            if (!@is_array($videovalidationMessage) && isset($values['category_id'])) {

                $categoryIds = array();
                $categoryIds['categoryIds'][] = $values['category_id'];
                $categoryIds['categoryIds'][] = $values['subcategory_id'];
                $categoryIds['categoryIds'][] = $values['subsubcategory_id'];

                try {
                    $values['profile_type'] = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getProfileType($categoryIds);
                } catch (Exception $ex) {

                    $values['profile_type'] = 0;
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {

                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getFieldsFormValidations($values, 'sitevideo_channel');
                    $data['validators'] = $profileFieldsValidators;
                    $profileFieldsValidationMessage = $this->isValid($data);
                }
            }
            if (is_array($videovalidationMessage) && is_array($profileFieldsValidationMessage))
                $validationMessage = array_merge($videovalidationMessage, $profileFieldsValidationMessage);
            else if (is_array($videovalidationMessage))
                $validationMessage = $videovalidationMessage;
            else if (is_array($profileFieldsValidationMessage))
                $validationMessage = $profileFieldsValidationMessage;
            else
                $validationMessage = 1;

            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            if (isset($values['channel_uri']) && !empty($values['channel_uri'])) {
                $this->channelurlValidationAction(1);
            }
//end validation
            try {

                //save channel values
                $channel = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->saveChannel($values);
                //channel url validation
                $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1);
                $table = Engine_Api::_()->getItemTable('sitevideo_channel');
                if (empty($show_url)) {
                    $resultChannelTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                                    ->query()->fetchAll();
                    $count_index = count($resultChannelTable);
                    $resultChannelUrl = $table->select()->where('channel_url =?', $values['title'])->from($table, 'channel_url')
                                    ->query()->fetchAll();
                    $count_index_url = count($resultChannelUrl);
                }
                $urlArray = Engine_Api::_()->sitevideo()->getBannedUrls();

                if (!empty($show_url)) {
                    if (isset($values['channel_uri']) && in_array(strtolower($values['channel_uri']), $urlArray)) {
                        $this->respondWithValidationError('validation_fail', $this->translate(array('url' => 'Sorry, this URL has been restricted by our automated system. Please choose a different URL.')));
                    }
                } else {
                    $lastchannel_id = $table->select()
                            ->from($table->info('name'), array('channel_id'))->order('channel_id DESC')
                            ->query()
                            ->fetchColumn();
                    $values['channel_uri'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $lastchannel_id = $lastchannel_id + 1;
                        $values['channel_uri'] = $values['channel_uri'] . '-' . $lastchannel_id;
                    } else {
                        $values['channel_uri'] = $values['channel_uri'];
                    }
                    if (in_array(strtolower($values['channel_uri']), $urlArray)) {

                        $this->respondWithValidationError($this->translate(array('title' => 'Sorry, this Channel Title has been restricted by our automated system. Please choose a different Title.')));
                    }
                }

                if (isset($values['channel_uri'])) {
                    $channel->channel_url = $values['channel_uri'];
                    $channel->save();
                }

                $channel_id = $channel->channel_id;
                if (empty($show_url)) {
                    $values['channel_uri'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $values['channel_uri'] = $values['channel_uri'] . '-' . $channel_id;
                        $table->update(array('channel_url' => $values['channel_uri']), array('channel_id = ?' => $channel_id));
                    } else {
                        $values['channel_uri'] = $values['channel_uri'];
                        $table->update(array('channel_url' => $values['channel_uri']), array('channel_id = ?' => $channel_id));
                    }
                }

                if (!empty($_FILES['photo'])) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setPhoto($_FILES['photo'], $channel);
                }
                //profile field
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setProfileFields($channel, $values);

                $tags = $values['tags'];
                if (!empty($tags)) {
                    $tags = preg_split('/[,]+/', $tags);
                    $tags = array_filter(array_map("trim", $tags));
                    $channel->tags()->addTagMaps($viewer, $tags);
                }
                //add channel to Advanced feed activity
                $owner = $channel->getOwner();
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'sitevideo_channel_new');
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $channel);
                }
                $response['response']['channel_id'] = $channel_id;
                $this->respondWithSuccess($response);
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function channelurlValidationAction($validate = 0) {

        $channel_url = $this->_getParam('channel_uri');

        $urlArray = Engine_Api::_()->sitevideo()->getBannedUrls();

        if (empty($channel_url)) {
            $this->respondWithValidationError('validation_fail', $this->translate(array('url' => 'URL not valid')));
        }

        $url_lenght = strlen($channel_url);
        if ($url_lenght < 3) {
            $this->respondWithValidationError('validation_fail', $this->translate(array('url' => 'URL component should be atleast 3 characters long.')));
        } elseif ($url_lenght > 255) {
            $this->respondWithValidationError('validation_fail', $this->translate(array('url' => 'URL component should be maximum 255 characters long.')));
        }

        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.change.url', 1);
        $check_url = $this->_getParam('check_url');
        if (!empty($check_url)) {
            $channelId = $this->_getParam('channel_id');
            $channelId = Engine_Api::_()->sitevideo()->getChannelId($channel_url, $channelId);
        } else {
            $channelId = Engine_Api::_()->sitevideo()->getChannelId($channel_url);
        }

        if (!empty($channelId) || (in_array(strtolower($channel_url), $urlArray))) {
            $this->respondWithValidationError('validation_fail', $this->translate(array('url' => 'URL not available.')));
        }

        if (!preg_match("/^[a-zA-Z0-9-_]+$/", $channel_url)) {
            $this->respondWithValidationError('validation_fail', $this->translate(array('url' => 'URL component can contain alphabets, numbers, underscores & dashes only.')));
        } else {
            if ($validate == 1)
                return 1;
            else
                $this->successResponseNoContent('no_content', true);
        }
    }

    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($channel))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams($channel, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $db = $channel->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            // delete channel ratings
            Engine_Api::_()->getDbtable('ratings', 'sitevideo')->delete(array(
                'resource_id = ?' => $channel->channel_id, 'resource_type =?' => 'channel'
            ));
            //delelte channel photo
            if ($channel->file_id)
                Engine_Api::_()->getItem('storage_file', $channel->file_id)->remove();
            //delete channel
            if ($channel) {
                $channel->delete();
            }
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function editAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $channel = $subject = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        if ($viewer->getIdentity() != $channel->owner_id && !$this->_helper->requireAuth()->setAuthParams($channel, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $categoryIds = array();
            $categoryIds['categoryIds'][] = $channel->category_id;
            $categoryIds['categoryIds'][] = $channel->subcategory_id;
            $categoryIds['categoryIds'][] = $channel->subsubcategory_id;

            try {
                $form['formValues'] = $subject->toArray();

                //to provide the basis of profile fields
                $form['formValues']['fieldCategoryLevel'] = "";
                if (isset($channel->category_id) && !empty($channel->category_id)) {
                    $categoryObject = Engine_Api::_()->getItem('sitevideo_channel_category', $sitereviewObj->category_id);
                    if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && !empty($categoryObject->profile_type))
                        $form['formValues']['fieldCategoryLevel'] = 'category_id';
                }
                if (isset($channel->subcategory_id) && !empty($channel->subcategory_id)) {
                    $categoryObject = Engine_Api::_()->getItem('sitevideo_channel_category', $channel->subcategory_id);
                    if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && $categoryObject->profile_type)
                        $form['formValues']['fieldCategoryLevel'] = 'subcategory_id';
                }
                if (isset($channel->subsubcategory_id) && !empty($channel->subsubcategory_id)) {
                    $categoryObject = Engine_Api::_()->getItem('sitevideo_channel_category', $channel->subsubcategory_id);
                    if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && $categoryObject->profile_type)
                        $form['formValues']['fieldCategoryLevel'] = 'subsubcategory_id';
                }

                $profiletype = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getProfileType($categoryIds);
            } catch (Exception $ex) {
                $profiletype = 0;
            }
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getChannelForm(1, $channel, $profiletype);
            if (!empty($subject) && !empty($subject->profile_type))
                $profileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getProfileInfo($subject, 'sitevideo_channel', true);
            //  $form['formValues'] = $subject->toArray();


            $tagStr = '';
            foreach ($channel->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap->getTag();
                if (!isset($tag->text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag->text;
            }
            $form['formValues']['tags'] = $tagStr;

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);

            if (!empty($getContentImages))
                $form['formValues'] = array_merge($form['formValues'], $getContentImages);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($channel, $role, 'view'))
                    $form['formValues']['auth_view'] = $role;

                if ($auth->isAllowed($channel, $role, 'comment'))
                    $form['formValues']['auth_comment'] = $role;
            }

            if (!empty($profileInfo)) {
                $form['formValues'] = @array_merge($form['formValues'], $profileInfo);
            }
            $response['formValues'] = $form['formValues'];

            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {
            $values = $data = $_REQUEST;
            //form validation
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getChannelForm(1);

            foreach ($getForm['form'] as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));


            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getChannelFormValidators(1);
            $data['validators'] = $validators;


            $videovalidationMessage = $this->isValid($data);

            if (!empty($videovalidationMessage) && @is_array($videovalidationMessage)) {
                $this->respondWithValidationError('validation_fail', $videovalidationMessage);
            }
            if (!@is_array($videovalidationMessage) && isset($values['category_id'])) {

                $categoryIds = array();
                $categoryIds['categoryIds'][] = $values['category_id'];
                $categoryIds['categoryIds'][] = $values['subcategory_id'];
                $categoryIds['categoryIds'][] = $values['subsubcategory_id'];

                try {
                    $values['profile_type'] = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getProfileType($categoryIds);
                } catch (Exception $ex) {

                    $values['profile_type'] = 0;
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getFieldsFormValidations($values, 'sitevideo_channel');


                    $data['validators'] = $profileFieldsValidators;
                    $profileFieldsValidationMessage = $this->isValid($data);
                }
            }
            if (is_array($videovalidationMessage) && is_array($profileFieldsValidationMessage))
                $validationMessage = array_merge($videovalidationMessage, $profileFieldsValidationMessage);
            else if (is_array($videovalidationMessage))
                $validationMessage = $videovalidationMessage;
            else if (is_array($profileFieldsValidationMessage))
                $validationMessage = $profileFieldsValidationMessage;
            else
                $validationMessage = 1;

            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            // end of form validation

            $db = $channel->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                //save channel value
                $channel->setFromArray($values);
                $channel->save();

                //channel url validation
                $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1);

                $table = Engine_Api::_()->getItemTable('sitevideo_channel');
                if (empty($show_url)) {
                    $resultChannelTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                                    ->query()->fetchAll();
                    $count_index = count($resultChannelTable);
                    $resultChannelUrl = $table->select()->where('channel_url =?', $values['title'])->from($table, 'channel_url')
                                    ->query()->fetchAll();
                    $count_index_url = count($resultChannelUrl);
                }
                $urlArray = Engine_Api::_()->sitevideo()->getBannedUrls();

                if (!empty($show_url)) {
                    if (isset($values['channel_url']) && in_array(strtolower($values['channel_url']), $urlArray)) {
                        $this->respondWithValidationError('validation_fail', $this->translate('Sorry, this URL has been restricted by our automated system. Please choose a different URL.'));
                    }
                } else {
                    $lastchannel_id = $table->select()
                            ->from($table->info('name'), array('channel_id'))->order('channel_id DESC')
                            ->query()
                            ->fetchColumn();
                    $values['channel_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');

                    if (!empty($count_index) || !empty($count_index_url)) {
                        $lastchannel_id = $lastchannel_id + 1;
                        $values['channel_url'] = $values['channel_url'] . '-' . $lastchannel_id;
                    } else {
                        $values['channel_url'] = $values['channel_url'];
                    }
                    if (in_array(strtolower($values['channel_url']), $urlArray)) {

                        $this->respondWithValidationError('validation_fail', $this->translate('Sorry, this Channel Title has been restricted by our automated system. Please choose a different Title.'));
                    }
                }



                $channel_id = $channel->channel_id;
                if (empty($show_url)) {
                    $values['channel_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $values['channel_url'] = $values['channel_url'] . '-' . $channel_id;
                    } else {
                        $values['channel_url'] = $values['channel_url'];
                    }
                }
                if (isset($values['channel_url'])) {
                    $channel->channel_url = $values['channel_url'];
                    $channel->save();
                }

                //authentication
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                if ($values['auth_view'])
                    $auth_view = $values['auth_view'];
                else
                    $auth_view = "everyone";

                if ($values['auth_comment'])
                    $auth_comment = $values['auth_comment'];
                else
                    $auth_comment = "everyone";

                $viewMax = array_search($auth_view, $roles);
                $commentMax = array_search($auth_comment, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($channel, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($channel, $role, 'comment', ($i <= $commentMax));
                }

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $channel->tags()->setTagMaps($viewer, $tags);

                //cover photo
                if (!empty($_FILES['filedata'])) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setPhoto($_FILES['filedata'], $channel);
                }
                //profile field
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setProfileFields($channel, $values);

                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($channel) as $action) {
                    $actionTable->resetActivityBindings($action);
                }


                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function channelSubscribeAction() {
        // Validate request methods
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            $this->respondWithError('unauthorized');

        $this->validateRequestMethod('POST');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        if (empty($channel))
            $this->respondWithError('no_record');
        $values['id'] = $channel->getIdentity();
        $values['value'] = $this->getRequestParam('value', NULL);

        try {
            Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->subscribed($values);
        } catch (Exception $ex) {
            $this->respondWithError($ex->message());
        }

        $this->successResponseNoContent('no_content', true);
    }

    public function informationAction() {
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');

        if (empty($channel))
            $this->respondWithError('no_record');
        try {
            $bodyParams['Basic Information']['Created By'] = $channel->getOwner()->getTitle();
            $bodyParams['Basic Information']['Created On'] = date('M d, Y', strtotime($channel->creation_date));
            $bodyParams['Basic Information']['Last Updated'] = date('M d, Y', strtotime($channel->creation_date));
            $bodyParams['Basic Information']['Videos'] = $channel->videos_count;
            $bodyParams['Basic Information']['Views'] = $channel->view_count;
            $bodyParams['Basic Information']['Likes'] = $channel->like_count;
            $bodyParams['Basic Information']['Subscribers'] = $channel->subscribe_count;
            $bodyParams['Basic Information']['Favourites'] = $channel->favourite_count;
            $bodyParams['Basic Information']['Description'] = $channel->description;
            if ($channel->category_id) {
                $category = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getChannelCategory($channel->category_id);

                if (!empty($category) && isset($category->category_name))
                    $bodyParams['Basic Information']['Category'] = $category->category_name;
            }
            $profile_info = array();
            if (isset($channel->profile_type) && !empty($channel->profile_type)) {
                $profile_info = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getInformation($channel, 'sitevideo_channel');
                if (count($profile_info) > 0)
                    $bodyParams['Profile Information'] = $profile_info;
            }

            if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order'])) {
                foreach ($bodyParams as $key => $value) {
                    $bodyParams[$key] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($value);
                }
                $bodyParams = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($bodyParams);
            }

            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

}

//end of class