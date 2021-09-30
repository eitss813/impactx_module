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
class Sitevideo_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // only show videos if authorized
        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid())
            $this->respondWithError("unauthorized");

        $id = $this->getRequestParam('video_id', $this->getRequestParam('id', null));
        if ($id) {
            $video = Engine_Api::_()->getItem('sitevideo_video', $id);
            if ($video)
                Engine_Api::_()->core()->setSubject($video);
        }
    }

    public function indexAction() {
        // Validate request methods
        $this->validateRequestMethod();
        $response=array();
        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $params = $this->_getAllParams();

        $subject_type = $this->_getParam('subject_type');
        $subject_id = $this->_getParam('subject_id');

        if (empty($subject_id) || empty($subject_type))
            $this->respondWithError('no_record');

        //GET VIDEO SUBJECT
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);
        Engine_Api::_()->core()->setSubject($subject);

        $moduleName = $moduleName = strtolower($subject->getModuleName());
        $getShortType = ucfirst($subject->getShortType());

        if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview', 'checked' => 'enabled'))))
                $this->respondWithError('no_record');
        } else {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName()), 'checked' => 'enabled'))))
                $this->respondWithError('no_record');
        }

        $params['parent_type'] = $subject->getType();
        $params['parent_id'] = $subject->getIdentity();
        $canEdit = Engine_Api::_()->sitevideo()->isEditPrivacy($subject->getType(), $subject->getIdentity(), $subject);
        $canDelete = Engine_Api::_()->sitevideo()->canDeletePrivacy($subject->getType(), $subject->getIdentity(), $subject);

        if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore') {
            $isModuleOwnerAllow = 'is' . $getShortType . 'OwnerAllow';
            $videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');

            //START PACKAGE WORK
            if (Engine_Api::_()->$moduleName()->hasPackageEnable()) {
                if (!Engine_Api::_()->$moduleName()->allowPackageContent($subject->package_id, "modules", $moduleName . 'video')) {
                    $this->respondWithError('no_record');
                }
            } else {
                $isOwnerAllow = Engine_Api::_()->$moduleName()->$isModuleOwnerAllow($subject, 'svcreate');
                if (empty($isOwnerAllow)) {
                    $this->respondWithError('no_record');
                }
            }

            //END PACKAGE WORK
            $response['canCreate'] = $canCreate = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'svcreate');

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'view');
            if (empty($isManageAdmin)) {
                $this->respondWithError('no_record');
            }

            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');

            if (empty($isManageAdmin)) {
                $canEdit = 0;
            } else {
                $canEdit = 1;
            }

            if (empty($canCreate) && empty($videoCount) && empty($canEdit)) {
                $this->respondWithError('no_record');
            }
        } else if ($moduleName == 'siteevent') {
            $canEdit = $subject->authorization()->isAllowed($viewer, "edit");
            $videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            $response['canCreate'] = $canCreate = Engine_Api::_()->siteevent()->allowVideo($subject, $viewer, $videoCount);
            if (empty($canCreate) && empty($videoCount) && empty($canEdit)) {
                $this->respondWithError('no_record');
            }
        } else if ($moduleName == 'sitereview') {
            //AUTHORIZATION CHECK
            $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');

            $videoCount = $count = $table
                    ->select()
                    ->from($table->info('name'), array('count(*) as count'))
                    ->where("parent_type = ?", 'sitereview_listing_' . $subject->listingtype_id)
                    ->where("parent_id =?", $subject->getIdentity())
                    ->query()
                    ->fetchColumn();

            $response['canCreate'] = $canCreate = Engine_Api::_()->sitereview()->allowVideo($subject, $viewer, $this->view->videoCount);
            $canEdit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");

            if (empty($canCreate) && empty($videoCount) && empty($canEdit)) {
                $this->respondWithError('no_record');
            }

            $params['parent_type'] = 'sitereview_listing_' . $subject->listingtype_id;
            $params['parent_id'] = $subject->getIdentity();
        }

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        $params['videoWidth'] = $this->_getParam('videoWidth', 150);
        $params['videoHeight'] = $this->_getParam('videoHeight', 150);
        $params['columnHeight'] = $this->_getParam('columnHeight', 150);
        $params['margin_video'] = $this->_getParam('margin_video', 2);
        $params['videoOption'] = $this->_getParam('videoOption', array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'location', 'facebook', 'twitter', 'linkedin', 'googleplus'));
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $params['videoOption'] = array();
        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['videoWidth'] = $params['videoWidth'];

        $thumbnailType = $this->findThumbnailType($videoSize, $params['videoWidth']);
        $params['thumbnailType'] = $thumbnailType;

        //ignore if profile view.................
        unset($params['restapilocation']);

        $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);

        try {
            foreach ($paginator as $video) {

                $browseVideo = $video->toArray();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
                $browseVideo = array_merge($browseVideo, $getContentImages);

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
                $browseVideo = array_merge($browseVideo, $getContentImages);

                $browseVideo["owner_title"] = $video->getOwner()->getTitle();
                $browseVideo['creation_date'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitevideo')->getFormattedDate($browseVideo['creation_date']);
                $isAllowedView = $video->canView($viewer);
                $browseVideo['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
                $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browseVideo["like_count"] = $video->likes()->getLikeCount();
                $ratingParams['resource_id'] = $video->getIdentity();
                $ratingParams['resource_type'] = 'sitevideo_video';
                $browseVideo["rating_count"] = Engine_Api::_()->getDbTable('ratings', 'sitevideo')->ratingCount($ratingParams);

                $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoURL($video);
                $response['response'][] = $browseVideo;
            }
            $response['totalItemCount'] = $totalCount;
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $this->respondWithSuccess($response, true);
    }

    function findThumbnailType($videoSize, $vWidth) {
        arsort($videoSize);
        $thumbnailType = 'thumb.normal';
        $count = 0;
        $bool = true;
        foreach ($videoSize as $key => $tSize) {
            $videoSizeDup[] = $key;
            if ($key != 'videoWidth' && $tSize == $vWidth) {
                $bool = false;
                $thumbnailType = $key;
            }
        }
        if ($bool) {
            foreach ($videoSize as $k => $tSize) {
                if ($k == 'videoWidth') {
                    $thumbnailType = isset($videoSizeDup[$count - 1]) ? $videoSizeDup[$count - 1] : $videoSizeDup[$count + 1];
                    break;
                }
                $count++;
            }
        }
        return $thumbnailType;
    }

    /*
     * Calling of adv search form
     * 
     * @return array
     */

    public function searchFormAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');
        try {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getSearchForm(), true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of browse Videos
     * 
     * @return array
     */

    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $response = array();
        try {
            $tempParam = array();
            $params = array();
            $params = $this->getRequestAllParams;
            $params['status'] = 1;
            $viewer_id = 0;
            $params['owner_id'] = $this->getRequestParam('user_id', null);
            if (!empty($viewer)) {
                $viewer_id = $viewer->getIdentity();
            }
            if (isset($params['owner_id']) && $viewer_id && !empty($params['owner_id']) && $viewer_id != $params['owner_id'] && $viewer->level_id != 1) {
                $params['status'] = 1;
                $params['search'] = 1;
            }
            if (!empty($params['category_id'])) {
                $customParam = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getCustomVideoField($params['category_id'], 'video');

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

            $response = $this->_videoPaginator($params, $tempParam);
            $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'create')->checkRequire();
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Calling of manage Videos
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

        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        $values = array();
        $values = $this->getRequestAllParams;
        $values['status'] = 1;
        $values['owner_id'] = $this->getRequestParam('user_id', $viewer_id);
        if (!empty($values['category_id'])) {
            $customParam = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getCustomVideoField($values['category_id'], 'video');

            $customParam = $customParam[$params['category_id']];

            foreach ($customParam as $key => $value) {
                if (false !== strpos($value['name'], '_field_')) {

                    if (isset($values[$value['name']]) && !empty($values[$value['name']])) {

                        $tempParam[$value['name']] = $values[$value['name']];
                    }
                }
            }
        }

        try {
            $response = $this->_videoPaginator($values, true, $tempParam);
            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return the video View page.
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');
        if (empty($video))
            $this->respondWithError('no_record');
        
        if (!$video->authorization()->isAllowed($viewer, 'view')) {
            $this->respondWithError('unauthorized');
        }

        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view')) {
            $module_error_type = @ucfirst($video->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }
        $bodyParams = array();

        try {
            $bodyParams['response'] = $video->toArray();
            // unset($bodyParams['response'] ['password']);
            $bodyParams['response']['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
            if (!empty($getContentImages))
                $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

            //contentURL
            $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($video);
            if (!empty($contentURL))
                $bodyParams['response'] = array_merge($bodyParams['response'], $contentURL);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
            if (isset($video->owner_id) && !empty($video->owner_id)) {
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
                $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

                $bodyParams['response'] ["owner_title"] = $video->getOwner()->getTitle();
            }
            //$bodyParams['response']['creation_date'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitevideo')->getFormattedDate($bodyParams['response']['creation_date']);
            $ratingParams = array();
            $ratingParams['resource_id'] = $video->getIdentity();
            $ratingParams['resource_type'] = 'sitevideo_video';
            $bodyParams["rating_count"] = Engine_Api::_()->getDbTable('ratings', 'sitevideo')->ratingCount($ratingParams);

            // Getting viewer like or not to content.
            $bodyParams['response'] ["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($video);

            // Getting like count.
            $bodyParams['response'] ["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($video);
            //get all reaction on live video.
            $livstreamvideo = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('livestreamingvideo');
            if($livstreamvideo)
            {
             $streamTable = Engine_Api::_()->getDbtable ( "streams", "livestreamingvideo" );
             $isLivesteamingVideo = $streamTable->isLivestreamingVideo($video);
             if($isLivesteamingVideo)
                $bodyParams['response']['reactions'] = Engine_Api::_()->getApi('Siteapi_Core', 'livestreamingvideo')->getAllReactions($video);
            }
            $bodyParams['response'] ['category'] = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getCategoryName($video->category_id);
            $videoTags = $video->tags()->getTagMaps();
            if (!empty($videoTags)) {
                foreach ($videoTags as $tag) {
                    $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
                }

                $bodyParams['response'] ['tags'] = $tagArray;
            }
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($video, $role, 'view'))
                    $bodyParams['response'] ['auth_view'] = $role;

                if ($auth->isAllowed($video, $role, 'comment'))
                    $bodyParams['response'] ['auth_comment'] = $role;
            }

            // Check if edit/delete is allowed
            $bodyParams['response'] ['can_edit'] = $can_edit = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
            $bodyParams['response'] ['can_delete'] = $can_delete = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

            // check if embedding is allowed
            $can_embed = true;
            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
                $can_embed = false;
            } else if (isset($video->allow_embed) && !$video->allow_embed) {
                $can_embed = false;
            }
            $bodyParams['response'] ['can_embed'] = $can_embed;
            $bodyParams['is_password'] = isset($video->password) && !empty($video->password) ? 1 : 0;
            // increment count
            $embedded = "";
            if ($video->status == 1) {
                if (!$video->isOwner($viewer)) {
                    $video->view_count++;
                    $video->save();
                }
            }

            $bodyParams['response'] ['rated'] = Engine_Api::_()->getDbtable('ratings', 'sitevideo')->checkRated($ratingParams);
            $bodyParams['response'] ['videoEmbedded'] = $embedded;
            //Channel name
            if (isset($video->main_channel_id) && !empty($video->main_channel_id)) {
                $channel = Engine_Api::_()->getItem('sitevideo_channel', $video->main_channel_id);
                if (!empty($channel)) {
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($channel);
                    $bodyParams['response'] ['channel_title'] = $channel->title;
                    $bodyParams['response'] ['channel_image'] = $getContentImages['image_normal'];
                    $subscribeInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isSubscribedUser($video->main_channel_id, $viewer->getIdentity());
                    $bodyParams['response'] ['is_subscribe'] = count($subscribeInfo) > 0 ? 1 : 0;
                } else {
                    $bodyParams['response']['main_channel_id'] = 0;
                }

            } else {
                $bodyParams['response']['main_channel_id'] = 0;
            }

             $bodyParams['response']['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoURL($video);
            
            $bodyParams['response']['video_overlay_image'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoFilterImage($video);

            if (isset($video->profile_type) && !empty($video->profile_type)) {
                $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getInformation($video, 'video');

                if (count($getProfileInfo) > 0)
                    $bodyParams['response'] ['ProfileInfo'] = $getProfileInfo;
            }

            if ($this->getRequestParam('gutter_menu', true))
                $bodyParams['gutterMenu'] = $this->_gutterMenus($video);
            if ($this->getRequestParam('menu', true))
                $bodyParams['menus'] = $this->_menu($video);

            $userchannelVideo = $this->getchannelUserVideo($video);
            $bodyParams['relatedVideo'] = $userchannelVideo['response'];

            $this->respondWithSuccess($bodyParams,true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Delete the video.
     * 
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($video))
            $this->respondWithError('no_record');

        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid())
            $this->respondWithError('unauthorized');

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            /* if (!empty($video->main_channel_id)) {
              $channel = Engine_Api::_()->getItem('sitevideo_channel', $video->main_channel_id);
              $channel->videos_count--;
              $channel->save();
              } */

            Engine_Api::_()->sitevideo()->deleteVideo($video);
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Get Categories , Sub-Categories, SubSub-Categories and Videos array
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
        $showVideos = $this->getRequestParam('showVideos', 1);

        if ($this->getRequestParam('showCount')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }
        $orderBy = $this->getRequestParam('orderBy', 'category_name');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $categories = array();

        try {


            //GET EVENT TABLE
            $tableSiteVideo = Engine_Api::_()->getDbtable('videos', 'sitevideo');
            $images = array();
            if ($showCategories) {
                if ($showAllCategories) {

                    $category_info = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategoriesPaginator(array('cat_depandancy' => 1));
                    $items_count = $this->getRequestParam("limit", 20);
                    $category_info->setItemCountPerPage($items_count);
                    $requestPage = $this->getRequestParam('page', 1);
                    $category_info->setCurrentPageNumber($requestPage);

                    $response['totalItemCount'] = $category_info->getTotalItemCount();

                    $categoriesCount = count($category_info);
                    foreach ($category_info as $value) {
                        $sub_cat_array = array();
                        if ($value->video_id) {
                            $category_image_Object = Engine_Api::_()->storage()->get($value->video_id, '');
                            if (!empty($category_image_Object)) {
                                $category_image = $category_image_Object->getPhotoUrl();
                            }

                            $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                            if ($value->file_id) {
                                $category_icon_object = Engine_Api::_()->storage()->get($value->file_id, '');
                                if (!empty($category_icon_object)) {
                                    $category_icon = $category_icon_object->getPhotoUrl();
                                }
                                $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
                            } else {
                                $category_icon = "";

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
                                'count' => Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideosCount($value->category_id, 'category_id'),
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
                    $category_info = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategoriesPaginator(array('cat_dependency' => 1, 'havingVideos' => 1));
                    $items_count = $this->getRequestParam("limit", 20);
                    $category_info->setItemCountPerPage($items_count);
                    $requestPage = $this->getRequestParam('page', 1);
                    $category_info->setCurrentPageNumber($requestPage);

                    $response['totalItemCount'] = $category_info->getTotalItemCount();


                    foreach ($category_info as $value) {
                        $sub_cat_array = array();
                        if ($value->video_id) {
                            $category_image_Object = Engine_Api::_()->storage()->get($value->video_id, '');
                            if (!empty($category_image_Object)) {
                                $category_image = $category_image_Object->getPhotoUrl();
                            }

                            $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                            $category_icon_object = Engine_Api::_()->storage()->get($value->file_id, '');
                            if (!empty($category_icon_object)) {
                                $category_icon = $category_icon_object->getPhotoUrl();
                            }
                            $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
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
                                'count' => Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideosCount($value->category_id, 'category_id'),
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
                        $subSategory_info = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getSubCategories(array("category_id" => $category_id, 'havingVideos' => 0, 'fetchColumns' => '*'));
                        $subcategoriesCount = count($subSategory_info);

                        foreach ($subSategory_info as $value) {
                            $sub_cat_array = array();
                            if ($value->video_id) {
                                $category_image_Object = Engine_Api::_()->storage()->get($value->video_id, '');
                                if (!empty($category_image_Object)) {
                                    $category_image = $category_image_Object->getPhotoUrl();
                                }
                                $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                $category_icon_object = Engine_Api::_()->storage()->get($value->file_id, '');
                                if (!empty($category_icon_object)) {
                                    $category_icon = $category_icon_object->getPhotoUrl();
                                }
                                $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
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
                                    'count' => Engine_Api::_()->getDbtable('Videos', 'sitevideo')->getVideosCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
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
                        $subSategory_info = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getSubCategories(array("category_id" => $category_id, 'havingVideos' => 1, 'fetchColumns' => '*'));

                        foreach ($subSategory_info as $value) {
                            $sub_cat_array = array();
                            if ($value->video_id) {
                                $category_image_Object = Engine_Api::_()->storage()->get($value->video_id, '');
                                if (!empty($category_image_Object)) {
                                    $category_image = $category_image_Object->getPhotoUrl();
                                }
                                $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                $category_icon_object = Engine_Api::_()->storage()->get($value->file_id, '');
                                if (!empty($category_icon_object)) {
                                    $category_icon = $category_icon_object->getPhotoUrl();
                                }
                                $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
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
                                    'count' => Engine_Api::_()->getDbtable('Videos', 'sitevideo')->getVideosCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
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
                            $subsubSategory_info = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getSubCategories(array("category_id" => $subCategory_id, 'havingVideos' => 0, 'fetchColumns' => '*'));
                            $subcategoriesCount = count($subsubSategory_info);

                            foreach ($subsubSategory_info as $value) {
                                $subsub_cat_array = array();
                                if ($value->video_id) {
                                    $category_image_Object = Engine_Api::_()->storage()->get($value->video_id, '');
                                    if (!empty($category_image_Object)) {
                                        $category_image = $category_image_Object->getPhotoUrl();
                                    }

                                    $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                    $category_icon_object = Engine_Api::_()->storage()->get($value->file_id, '');
                                    if (!empty($category_icon_object)) {
                                        $category_icon = $category_icon_object->getPhotoUrl();
                                    }
                                    $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
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
                                        'count' => Engine_Api::_()->getDbtable('Videos', 'sitevideo')->getVideosCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
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
                            $subsubSategory_info = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getSubCategories(array("category_id" => $subCategory_id, 'havingVideos' => 1, 'fetchColumns' => '*'));
                            //$categoriesCount = count($category_info);

                            foreach ($subsubSategory_info as $value) {
                                $subsub_cat_array = array();
                                if ($value->video_id) {
                                    $category_image_Object = Engine_Api::_()->storage()->get($value->video_id, '');
                                    if (!empty($category_image_Object)) {
                                        $category_image = $category_image_Object->getPhotoUrl();
                                    }

                                    $category_image = strstr($category_image, 'http') ? $category_image : $getHost . $category_image;
                                    $category_icon_object = Engine_Api::_()->storage()->get($value->file_id, '');
                                    if (!empty($category_icon_object)) {
                                        $category_icon = $category_icon_object->getPhotoUrl();
                                    }
                                    $category_icon = strstr($category_icon, 'http') ? $category_icon : $getHost . $category_icon;
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
                                        'count' => Engine_Api::_()->getDbtable('Videos', 'sitevideo')->getVideosCount(array('columnName' => 'category_id', 'category_id' => $value->category_id)),
                                        'images' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value),
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

            if (!empty($showVideos) && !empty($category_id) && isset($category_id)) {
                $params = array();

                $params['category_id'] = $category_id;
                $params['subcategory_id'] = $subCategory_id;
                $params['subsubcategory_id'] = $subsubcategory_id;
                $params['status'] = 1;
                $user_id = $this->getRequestParam('user_id', null);
                if ($user_id)
                    $params['user_id'] = $user_id;
                // Get videos
                $response['video'] = $this->_videoPaginator($params);
            }

            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {

            //error msg
        }
    }

    /**
     * Get All playlist and playlist form.
     * return array
     */
    public function addToPlaylistAction() {
        //ONLY LOGGED IN USER CAN CREATE

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        //GET USER PLAYLIST
        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
        $playlistDatas = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->userPlaylists($viewer);
        $playlistDataCount = Count($playlistDatas);
        $video_id = $this->_getParam('video_id');
        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');
        if (empty($video))
            $this->respondWithError('no_record');


        //FORM GENERATION
        if ($this->getRequest()->isGet()) {

            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getAddToPlaylistForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            if (!empty($values['description']) && empty($values['title'])) {
                $this->respondWithError('parameter_missing');
            }
            //CHECK FOR TITLE
            if (empty($playlistDatas) && empty($values['title']))
                $this->respondWithValidationError("parameter_missing", "title");


            $playlistOldIds = array();
            $playlistMapTable = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');

            if (!empty($playlistDatas)) {

                foreach ($playlistDatas as $playlistData) {
                    $playlist = Engine_Api::_()->getItem('sitevideo_playlist', $playlistData->playlist_id);

                    $key_name = 'playlist_' . $playlistData->playlist_id;
                    if (isset($values[$key_name]) && !empty($values[$key_name])) {
                        $playlistMapTable->insert(array(
                            'playlist_id' => $playlistData->playlist_id,
                            'video_id' => $video_id,
                            'creation_date' => new Zend_Db_Expr('NOW()')
                        ));

                        if ($playlist) {
                            $playlist->video_count = ($playlist->video_count) + 1;
                            $playlist->save();
                        }
                    }
                    $in_key_name = 'inplaylist_' . $playlistData->playlist_id;


                    if (isset($values[$in_key_name]) && empty($values[$in_key_name])) {
                        //REDUCE THE VIDEO COUNT BY 1 FROM PLAYLIST TABLE WHEN VIDEO IS REMOVED FROM PLAYLIST
                        if ($playlist) {
                            $playlist->video_count = ($playlist->video_count) - 1;
                            $playlist->save();
                        }

                        $playlistOldIds[$playlistData->playlist_id] = $playlistData;
                        $playlistMapTable->delete(array('playlist_id = ?' => $playlistData->playlist_id, 'video_id = ?' => $video_id));
                    }
                }
            }


            if (!empty($values['title'])) {

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    //CREATE Playlist

                    $playlist = $playlistTable->createRow();
                    $playlist->setFromArray($values);
                    $playlist->owner_id = $viewer_id;
                    $playlist->owner_type = $viewer->getType();
                    $playlist->video_count = 1;
                    $playlist->save();
                    // Add photo
                    if (!empty($_FILES['photo'])) {
                        Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setPhoto($_FILES['photo'], $playlist);
                    }
                    //PRIVACY WORK
                    $auth = Engine_Api::_()->authorization()->context;
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                    if (empty($values['privacy'])) {
                        $values['privacy'] = 'owner';
                    }

                    $viewMax = array_search($values['privacy'], $roles);
                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                    }

                    $db->commit();
                    $playlistMapTable->insert(array(
                        'playlist_id' => $playlist->playlist_id,
                        'video_id' => $video_id,
                        'creation_date' => new Zend_Db_Expr('NOW()')
                    ));
                } catch (Exception $ex) {
                    $db->rollback();
                    $this->respondWithValidationError('internal_server_error', $ex->getMessage());
                }
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Subscribe or unsubscribe channel
     * return array
     */
    public function subscriptionAction() {
        // Validate request methods
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1))
            $this->respondWithError('unauthorized');


        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');
        if (empty($video))
            $this->respondWithError('no_record');

        if (empty($video->main_channel_id)) {
            $this->respondWithValidationError("parameter_missing", "channel_id");
        }
        $values = $this->_getAllParams();
        $values['id'] = $video->main_channel_id;

        try {
            Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->subscribed($values);
        } catch (Exception $ex) {
            $this->respondWithError($ex->message());
        }

        $this->successResponseNoContent('no_content', true);
    }

    /**
     * add or remove video to watch later
     * return array
     */
    public function watchLaterAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');
        if (empty($video))
            $this->respondWithError('no_record');
        $video_id = $video->getIdentity();
        $values = $this->_getAllParams();
        try {
            $watchlatersTable = Engine_Api::_()->getDbTable('watchlaters', 'sitevideo');
            if (isset($values['value']) && empty($values['value'])) {

                $watchlatersTable->delete(array('video_id = ?' => $video_id, 'owner_id = ?' => $viewer_id));
            } elseif (isset($values['value']) && !empty($values['value'])) {

                $wName = $watchlatersTable->info('name');
                $select = $watchlatersTable->select()
                        ->where('video_id = ?', $video_id)
                        ->where('owner_id = ?', $viewer_id)
                        ->limit(1);

                $row = $watchlatersTable->fetchAll($select);

                if (count($row) == 0) {

                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();
                    try {
                        $watchLater = $watchlatersTable->createRow();
                        $watchLater->video_id = $video_id;
                        $watchLater->owner_id = $viewer_id;
                        $watchLater->owner_type = $viewer->getType();
                        $watchLater->creation_date = new Zend_Db_Expr('NOW()');

                        $watchLater->save();
                        $db->commit();
                    } catch (Exception $ex) {
                        $db->rollback();
                        $this->respondWithError($ex->message());
                    }
                }
            } else
                $this->respondWithValidationError("parameter_missing", "type");

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * add or remove video from favourite.
     * return array
     */
    public function favouriteVideoAction() {
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');
        if (empty($video))
            $this->respondWithError('no_record');

        $values = $this->_getAllParams();

        if (isset($values['value'])) {
            try {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->favourite($values['video_id'], 'video', $values['value']);
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        } else {
            $this->respondWithValidationError("parameter_missing", "type");
        }
    }

    private function _videoPaginator($params = array(), $isManage = false, $tempParam = array()) {

        $viewer = Engine_Api::_()->user()->getViewer();
        unset($params['restapilocation']);
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view'))
            $this->respondWithError('unauthorized');
        if ($params['orderby'] == 'best_video' || $params['orderby'] == 'featured' || $params['orderby'] == 'sponsored') {

            if ($params['orderby'] == 'best_video')
                $params['showVideo'] = 'featuredSponsored';
            else
                $params['showVideo'] = $params['orderby'];

            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->videoBySettings($params);
        } elseif ($params['orderby'] == 'watch_later') {
            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getWatchlaterPaginator($params);
        } elseif ($params['orderby'] == 'playlist') {
            $paginator = Engine_Api::_()->getDbTable('playlists', 'sitevideo')->getPlaylistPaginator($params);
            $response = $this->_myPlaylist($paginator);
            $this->respondWithSuccess($response, true);
        } elseif ($params['orderby'] == 'like_count' && $isManage) {
            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getLikedVideoPaginator($params);
        } elseif ($params['orderby'] == 'favourite_count' && $isManage) {
            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getFavouriteVideoPaginator($params);
        } elseif ($params['orderby'] == 'rating' && $isManage) {
            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getRatedVideoPaginator($params);
        } else
            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params, $tempParam);

        $items_count = $this->getRequestParam("limit", 20);
        $paginator->setItemCountPerPage($items_count);
        $requestPage = $this->getRequestParam('page', 1);
        $paginator->setCurrentPageNumber($requestPage);

        foreach ($paginator as $video) {
            if (empty($video->video_id))
                continue;
            if (isset($params['showVideo']) && !empty($params['showVideo'])) {

                $video = Engine_Api::_()->getItem('sitevideo_video', $video->getIdentity());
                $browseVideo = $video->toArray();
            } else
                $browseVideo = $video->toArray();

            unset($browseVideo['password']);
            
            $browseVideo['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
            $ratingParams = array();
            $menus = array();
            $ratingParams['resource_id'] = $browseVideo['video_id'];
            $ratingParams['resource_type'] = 'sitevideo_video';
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
            
            $browseVideo['video_overlay_image'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoFilterImage($video);
            
            $browseVideo['is_password'] = isset($video->password) && !empty($video->password) ? 1 : 0;
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($video, $role, 'view'))
                    $browseVideo['auth_view'] = $role;

                if ($auth->isAllowed($video, $role, 'comment'))
                    $browseVideo['auth_comment'] = $role;
            }

            if ($isManage && $video->isOwner($viewer)) {
                if ($video->authorization()->isAllowed($viewer, 'edit')) {
                    $menus[] = array(
                        'label' => $this->translate('Edit Video'),
                        'name' => 'edit',
                        'url' => 'advancedvideo/edit/' . $video->getIdentity(),
                    );
                }
                if ($video->authorization()->isAllowed($viewer, 'delete')) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Video'),
                        'name' => 'delete',
                        'url' => 'advancedvideo/delete/' . $video->getIdentity(),
                    );
                }

                $browseVideo['menu'] = $menus;
            }
            $response['response'][] = $browseVideo;
        }
        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        //filter work
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitevideo_video');
        if (!empty($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']['display'])) {
            $multiOPtionsOrderBy = array();
            if (!$isManage) {
                $multiOPtionsOrderBy = array(
                    '' => '',
                    'creation_date' => 'Most Recent',
                    'modified_date' => 'Recently Updated',
                    'view_count' => 'Most View',
                    'like_count' => 'Most Liked',
                    'comment_count' => 'Most Commented',
                    'rating' => 'Most Rated',
                    'favourite_count' => 'Most Favourite',
                    'featured' => 'Featured',
                    'best_video' => 'Best Video',
                    'best_channel' => 'Best Channel',
                    'sponsored' => 'Sponsored',
                    'title' => "Alphabetical (A-Z)",
                    'title_reverse' => 'Alphabetical (Z-A)'
                );
//GET API
            } else {
                $multiOPtionsOrderBy = array(
                    'playlist' => 'Playlist',
                    'watch_later' => 'Watch Later',
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
        }
        return $response;
    }

    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();
        $type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($subject->type);

        // CREATE VIDEO LINK
        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Post New Video'),
                'name' => 'create',
                'url' => 'advancedvideos/create'
            );
        }
        $downloadLink = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoURL($subject);
        if ($viewer->getIdentity() && $type == 3) {
            $menus[] = array(
                'label' => $this->translate('Download Video'),
                'name' => 'download',
                'url' => $downloadLink
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'label' => $this->translate('Delete Video'),
                'name' => 'delete',
                'url' => 'advancedvideo/delete/' . $subject->getIdentity(),
            );
        }

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'label' => $this->translate('Edit Video'),
                'name' => 'edit',
                'url' => 'advancedvideo/edit/' . $subject->getIdentity(),
            );
        }


        $enableModule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

        if ($enableModule && $viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Suggest to friends'),
                'name' => 'suggest',
                'url' => 'suggestions/suggest-to-friend',
                'urlParams' => array(
                    "entity" => $subject->getType(),
                    "entity_id" => $subject->getIdentity()
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

        if ($viewer->getIdentity()) {
            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => 'sitevideo_video',
                    "id" => $subject->getIdentity()
                )
            );
        }

        return $menus;
    }

    private function _menu($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();
        try {
            $totalLikes = $subject->likes()->getLikeCount();
            $isLike = $subject->likes()->isLike($viewer);
            $totalComments = $subject->comments()->getCommentCount();

            if (Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject)) {
                $menus[] = array(
                    'label' => $this->translate('Like'),
                    'name' => 'like',
                    'url' => 'unlike',
                    'like_count' => $totalLikes,
                    'is_like' => $isLike ? 1 : 0,
                    'urlParams' => array(
                        "subject_type" => ' sitevideo_video',
                        'subject_id' => $subject->getIdentity()
                    )
                );
            } else {
                $menus[] = array(
                    'label' => $this->translate('Like'),
                    'name' => 'like',
                    'url' => 'like',
                    'like_count' => $totalLikes,
                    'is_like' => $isLike ? 1 : 0,
                    'urlParams' => array(
                        "subject_type" => ' sitevideo_video',
                        'subject_id' => $subject->getIdentity()
                    )
                );
            }

            if ($subject->authorization()->isAllowed($viewer, 'comment')) {
                $menus[] = array(
                    'label' => $this->translate('Comment'),
                    'name' => 'comment',
                    'url' => 'comment-create',
                    'comment_count' => $totalComments,
                    'urlParams' => array(
                        "subject_type" => ' sitevideo_video',
                        'subject_id' => $subject->getIdentity()
                    )
                );
            }

            $type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->watchLater($subject->getIdentity(), $viewer->getIdentity());

            if ($type) {
                if ($viewer->getIdentity()) {
                    $menus[] = array(
                        'label' => $this->translate('Added to Watch Later'),
                        'name' => 'watch_later',
                        'url' => 'advancedvideo/watch-later/' . $subject->getIdentity(),
                        'urlParams' => array(
                            "value" => 0
                        )
                    );
                }
            } else {
                if ($viewer->getIdentity()) {
                    $menus[] = array(
                        'label' => $this->translate('Watch Later'),
                        'name' => 'watch_later',
                        'url' => 'advancedvideo/watch-later/' . $subject->getIdentity(),
                        'urlParams' => array(
                            "value" => 1
                        )
                    );
                }
            }

            if ($viewer->getIdentity()) {
                $menus[] = array(
                    'label' => $this->translate('Add to Playlist'),
                    'name' => 'playlist',
                    'url' => 'advancedvideo/add-to-playlist/' . $subject->getIdentity(),
                );
            }


            $type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isFavourite($subject->getIdentity(), 'video', $viewer->getIdentity());

            if ($type) {
                if ($viewer->getIdentity()) {
                    $menus[] = array(
                        'label' => $this->translate('Unfavourite'),
                        'name' => 'favourite',
                        'url' => 'advancedvideo/favourite-video/' . $subject->getIdentity(),
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
                        'url' => 'advancedvideo/favourite-video/' . $subject->getIdentity(),
                        'urlParams' => array(
                            'value' => 1
                        )
                    );
                }
            }

            if (!empty($subject->main_channel_id)) {

                $subscribeInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isSubscribedUser($subject->main_channel_id, $viewer->getIdentity());
                if (count($subscribeInfo) > 0) {
                    $menus[] = array(
                        'label' => $this->translate('Subscribed'),
                        'name' => 'subscribe',
                        'url' => 'advancedvideo/subscription/' . $subject->getIdentity(),
                        'urlParams' => array(
                            'value' => 0,
                            'id' => $subject->main_channel_id
                        )
                    );
                } elseif ($viewer->getIdentity()) {
                    $menus[] = array(
                        'label' => $this->translate('Subscribe'),
                        'name' => 'subscribe',
                        'url' => 'advancedvideo/subscription/' . $subject->getIdentity(),
                        'urlParams' => array(
                            'value' => 1,
                            'id' => $subject->main_channel_id
                        )
                    );
                }
            }
        } catch (Exception $ex) {

            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }

        return $menus;
    }

    /**
     * Get video create form and post video
     * @return array
     */
    public function createAction() {

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid())
            $this->respondWithError('unauthorized');
        $params['owner_id'] = $viewer_id;

        $subject_type = $this->_getParam('subject_type');
        $subject_id = $this->_getParam('subject_id');
        $hasParent = 0;

        //GET VIDEO SUBJECT
        if (!empty($subject_type) && !empty($subject_id))
            $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

        if (isset($subject) && !empty($subject)) {
            $hasParent = 1;
        }

        if ($this->getRequest()->isGet()) {
            //advanced activity feed
            if (!empty($_GET['post_attach']) || !empty($_GET['message'])) {
                $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->advancedActivityVideoForm(),true);
            } else {
                $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoFrom(),true);
            }
        } else if ($this->getRequest()->isPost()) {
            $values = $data = $_REQUEST;

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));
            if (empty($values['post_attach']) && empty($values['is_storyPost'])) {
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoFrom();
                foreach ($getForm['form'] as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }


                //form validation
                $data = $values;
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getFormValidators();
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
                        $values['profile_type'] = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getProfileType($categoryIds);
                    } catch (Exception $ex) {
                        $values['profile_type'] = 0;
                    }

                    if (isset($values['profile_type']) && !empty($values['profile_type'])) {                          //profile fields validation
                        $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getFieldsFormValidations($values, 'video');
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
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $select = new Zend_Db_Select($db);
            $coreVersion = $select
                    ->from('engine4_core_modules', 'version')
                    ->where('name = ?', 'core')
                    ->query()
                    ->fetchColumn();

            $version = Engine_Api::_()->getApi('Core', 'siteapi')->checkVersion($coreVersion, 4.9);
            $insert_action = false;

            //Youtube/Dailymotion/Vimeo Upload video
            if (isset($values['url']) && !empty($values['url']) && $values['type'] != '6')
                $video = $this->_composeUploadAction($values);

            if (isset($values['url']) && !empty($values['url']) && $values['type'] == '6') {
                $information = $this->handleIframelyInformation($values['url']);

                if (empty($information)) {
                    $this->respondWithError('unauthorized', 'We could not find a video there - please check the URL and try again.');

                }
                $values['code'] = $information['code'];
                $thumbnail = $information['thumbnail'];
                $table = Engine_Api::_()->getDbtable('videos', 'video');
                $video = $table->createRow();
                $video->setFromArray($values);
                $video->title = $information['title'];
                $video->description = $information['description'];
                $video->duration = $information['duration'];
                $video->save();

                $insert_action = true;
            }

            // DEVICE UPLOADED VIDEOS.
            if (isset($_FILES['filedata']) && !empty($_FILES['filedata']['name']) && $values['type'] != '6') {

                $video = $this->_uploadVideoAction($values);
                if (!empty($_FILES['photo']))
                    $insert_action = true;
            }
            
            if($_FILES['video_overlay_image']){
               Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setPhoto($_FILES['video_overlay_image'],$video,false,true);
            }


            $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->addVideoMap($video);
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo') && isset($values['type']) && $values['type'] != 3 && $values['type'] != '6') {
                    try {
                        // Now try to create thumbnail
                        $thumbnail = Engine_Api::_()->getApi('Core', 'siteapi')->handleSiteVideoThumbnail($video->type, $video->code);
                        $video = Engine_Api::_()->getApi('Core', 'siteapi')->saveVideoThumbnail($thumbnail, $video);
                        if ($version == 0) {
                            $video->type = $values['type'];
                        } else {
                            $video->type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoType($video->type);
                        }
                        $video->save();
                        $insert_action = true;
                    } catch (Exception $ex) {
                        //Blank Exception
                    }
                } else {
                    // Now try to create thumbnail of My device
                    try {
                        
                        if ($video->type == '6') {
                            $video->status = 1;
                            if ((!isset($thumbnail) || empty($thumbnail)))
                                $thumbnail = $this->_handleThumbnail($video->type, $video->code);
                            $ext = ltrim(strrchr($thumbnail, '.'), '.');
                            $thumbnail_parsed = @parse_url($thumbnail);
                            if (@GetImageSize($thumbnail)) {
                                $valid_thumb = true;
                            } else {
                                $valid_thumb = false;
                            }

                            if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                                $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                                $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                                $src_fh = fopen($thumbnail, 'r');
                                $tmp_fh = fopen($tmp_file, 'w');
                                stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                                $image = Engine_Image::factory();
                                $image->open($tmp_file)
                                        ->resize(360, 480)
                                        ->write($thumb_file)
                                        ->destroy();
                                try {
                                    $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                        'parent_type' => $video->getType(),
                                        'parent_id' => $video->getIdentity()
                                    ));

                                    // Remove temp file
                                    @unlink($thumb_file);
                                    @unlink($tmp_file);
                                } catch (Exception $e) {
                                    
                                }
                                $video->photo_id = $thumbFileRow->file_id;
                            }
                        }
                        if ($video->type != '6')
                            $information = $this->_handleInformation($video->type, $video->code);
                        // $video->duration = $information['duration'];
                        if (!$video->description) {
                            $video->description = $information['description'];
                        }
                        if ($version == 0) {
                            $video->type = 3;
                        } else {
                            $video->type = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoType($video->type);
                        }
                         $video->save();
                    } catch (Exception $ex) {
                        //Blank Exception
                    }
                }
                // Save the profile fields information.
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setProfileFields($video, $values);

                //set authentication or view and comment
                $this->_authSet($values, $video);
                // Add tags
                if (isset($values['tags']) && !empty($values['tags'])) {
                    $tags = preg_split('/[,]+/', $values['tags']);
                    $tags = array_filter(array_map("trim", $tags));
                    $video->tags()->addTagMaps($viewer, $tags);
                }

                if (isset($hasParent) && !empty($hasParent)) {
                    $video->parent_type = $subject_type;
                    $video->parent_id = $subject_id;

                    $moduleName = $moduleName = strtolower($subject->getModuleName());
                    $getShortType = ucfirst($subject->getShortType());
                    if ($moduleName == 'sitereview') {
                        $video->parent_type = 'sitereview_listing_' . $subject->listingtype_id;
                    }
                    $video->save();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $db->beginTransaction();
            try {
                if ($insert_action && empty($values['post_attach'])) {

                    $owner = $video->getOwner();
                    $chanel = $video->getChannelModel();
                    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    $actionType = $chanel ? 'sitevideo_channel_video_new' : 'sitevideo_video_new';
                    $actionObject = $chanel ? $chanel : $video;
                    $action = $actionsTable->addActivity($owner, $actionObject, $actionType);

                    if ($action) {
                        $actionsTable->attachActivity($action, $video);
                    }
                    // Rebuild privacy
                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    foreach ($actionTable->getActionsByObject($video) as $action) {
                        $actionTable->resetActivityBindings($action);
                    }
                }



                $db->commit();
                unset($_FILES['photo']);
                unset($_FILES['filedata']);
                unset($_FILES['video_overlay_image']);
                // $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->commit();
                $_SERVER['REQUEST_METHOD'] = 'GET';
                $this->_forward('view', 'index', 'sitevideo', array(
                    'video_id' => $video->getIdentity()
                ));
            }
        }
        if ((!isset($values['message']) || empty($values['message'])) && !empty($values['post_attach']) && ((_IOS_VERSION && _IOS_VERSION >= '2.5.17') || (_ANDROID_VERSION && _ANDROID_VERSION >= '3.0'))) {
            $_POST['video_id'] = $video->getIdentity();
            $_POST['type'] = 'video';
            $this->_forward('post', 'feed', 'advancedactivity', array(
                'video_id' => $video->getIdentity(),
                'type' => 'video'
            ));
        } else {
            $this->setRequestMethod();
            $this->_forward('view', 'index', 'sitevideo', array(
                'video_id' => $video->getIdentity()
            ));
        }
    }

    /**
     * Return the "Edit Video" FORM AND HANDLE THE FORM POST ALSO.
     * 
     * @return array
     */
    public function editAction() {
        //viewer inforation
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');

        if (empty($video))
            $this->respondWithError('no_record');

        if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid())
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $categoryIds = array();
            $categoryIds['categoryIds'][] = $video->category_id;
            $categoryIds['categoryIds'][] = $video->subcategory_id;
            $categoryIds['categoryIds'][] = $video->subsubcategory_id;

            try {
                $form['formValues'] = $video->toArray();
                $form['formValues']['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
                //to provide the basis of profile fields
                $form['formValues']['fieldCategoryLevel'] = "";
                if (isset($video->category_id) && !empty($video->category_id)) {
                    $categoryObject = Engine_Api::_()->getItem('sitevideo_video_category', $sitereviewObj->category_id);
                    if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && !empty($categoryObject->profile_type))
                        $form['formValues']['fieldCategoryLevel'] = 'category_id';
                }
                if (isset($video->subcategory_id) && !empty($video->subcategory_id)) {
                    $categoryObject = Engine_Api::_()->getItem('sitevideo_video_category', $channel->subcategory_id);
                    if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && $categoryObject->profile_type)
                        $form['formValues']['fieldCategoryLevel'] = 'subcategory_id';
                }
                if (isset($video->subsubcategory_id) && !empty($video->subsubcategory_id)) {
                    $categoryObject = Engine_Api::_()->getItem('sitevideo_video_category', $channel->subsubcategory_id);
                    if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && $categoryObject->profile_type)
                        $form['formValues']['fieldCategoryLevel'] = 'subsubcategory_id';
                }

                $profiletype = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getProfileType($categoryIds);
            } catch (Exception $ex) {

                $profiletype = 0;
            }
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoFrom(1, $video, $profiletype);
            if (!empty($video) && !empty($video->profile_type))
                $profileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getProfileInfo($video, 'video', true);
            //  $form['formValues'] = $video->toArray();


            $tagStr = '';
            foreach ($video->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap->getTag();
                if (!isset($tag->text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag->text;
            }
            $form['formValues']['tags'] = $tagStr;

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);

            if (!empty($getContentImages))
                $form['formValues'] = array_merge($form['formValues'], $getContentImages);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($video, $role, 'view'))
                    $form['formValues']['auth_view'] = $role;

                if ($auth->isAllowed($video, $role, 'comment'))
                    $form['formValues']['auth_comment'] = $role;
            }

            if (!empty($profileInfo)) {
                $form['formValues'] = @array_merge($form['formValues'], $profileInfo);
            }
            $response['formValues'] = $form['formValues'];
            $this->respondWithSuccess($response);
        } else if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {
            $values = $data = $_REQUEST;
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoFrom(1);
            foreach ($getForm['form'] as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            $data = $values;
            //get form
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getFormValidators(1);
            $data['validators'] = $validators;

            //Validate form data
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
                    $values['profile_type'] = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getProfileType($categoryIds);
                } catch (Exception $ex) {

                    $values['profile_type'] = 0;
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {
                    // START PROFILE FORM VALIDATION
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getFieldsFormValidations($values, 'video');

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
            $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
            $db->beginTransaction();
            try {
                $video->setFromArray($values);
                $video->save();

                $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
                $videoMapTableName = $videoMapTable->info('name');
                if (!empty($values['main_channel_id']) && $values['main_channel_id'] != $video->main_channel_id) {
                    $videoMapTable->delete(array(
                        'video_id = ?' => $video->video_id, "channel_id= ?" => $video->main_channel_id
                    ));

                    Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->addVideoMap($video);
                }

                //set view and comment authentication
                $this->_authSet($values, $video);
                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $video->tags()->setTagMaps($viewer, $tags);
                Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setProfileFields($video, $values);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $db->beginTransaction();
            try {
                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($video) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Validate url.
     * 
     * @return array
     */
    public function validationAction() {
        //url validation
        $video_type = $this->_getParam('type');
        $url = $this->_getParam('url');
        $valid = false;
        $code = $this->_extractCode($url, $video_type);

        // check which API should be used
        if ($video_type == "1") {
            $valid = $this->_checkYouTube($code);
            $type = 1;
        } elseif ($video_type == "2") {
            $valid = $this->_checkVimeo($code);
            $type = 2;
        } elseif ($video_type == "4") {

            $valid = $this->_checkDailymotion($code);
            $type = 4;
        } elseif ($video_type == "instagram") {
            $scheme = $this->_getParam('scheme');
            $host = $this->_getParam('host');
            $code = $scheme . "://" . $host . $code;
            $valid = $this->_checkInstagram($code);
            $type = "instagram";
        } elseif ($video_type == "twitter") {
            $valid = $this->_checkTwitter($code);
            $type = "twitter";
        }

        if ($valid) {
            $information = $this->_handleInformation($video_type, $code);

            $response['response'] = array(
                "title" => (string) $information['title'],
                'discription' => strip_tags((string) $information['description']),
                'duration' => (int) $information['duration'],
            );
            $this->respondWithSuccess($response);
        } else {
            $this->respondWithValidationError('validation_fail', 'Invalid url');
        }
    }

    /**
     * Veryfy password.
     * 
     * @return array
     */
    public function passwordProtectionAction() {
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject())
            $video = Engine_Api::_()->core()->getSubject('video');
        if (empty($video))
            $this->respondWithError('no_record');

        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view')) {
            $module_error_type = @ucfirst($video->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }
        $params = $this->_getAllParams();
        try {
            $valid = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->checkPasswordProtection($params);
            if ($valid) {
                
            } else {
                $this->respondWithError('unauthorized', "This is not valid password. Please try again.");
            }
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
        $this->setRequestMethod();
        $this->_forward('view', 'index', 'sitevideo', array(
            'video_id' => $video->getIdentity(),
        ));
        $this->successResponseNoContent('no_content', true);
    }

    //ACTION FOR RATING THE VIDEO
    public function rateAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'sitevideo_channel', 'rate');
        if (empty($viewer_id) || empty($allowRating))
            return;

        $rating = $this->_getParam('rating');
        $video_id = $this->_getParam('video_id');
        $video = Engine_Api::_()->getItem('sitevideo_video', $video_id);

        if (empty($video)) {
            $this->respondWithError('no_record');
        }
        $table = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $table->setRating($video_id, 'sitevideo_video', $rating);
            $video = Engine_Api::_()->getItem('sitevideo_video', $video_id);
            $video->rating = $table->getRating($video->getIdentity(), 'sitevideo_video');
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }

        $total = $table->ratingCount(array('resource_id' => $video->getIdentity(), 'resource_type' => 'sitevideo_video'));


        $this->respondWithSuccess(array(
            "rating_count" => $total
        ));
    }

    private function _composeUploadAction($values) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity())
            $this->respondWithError("unauthorized");

        $values['user_id'] = $viewer->getIdentity();
        $code = $this->_extractCode($values['url'], $values['type']);

        // check if code is valid
        // check which API should be used
        if ($values['type'] == 1) {
            $valid = $this->_checkYouTube($code);
            if (empty($valid))
                $this->respondWithError("youtube_validation_fail");
        }

        if ($values['type'] == 2) {
            $valid = $this->_checkVimeo($code);
            if (empty($valid))
                $this->respondWithError("vimeo_validation_fail");
        }
        if ($values['type'] == 4)
            $valid = $this->_checkDailymotion($code);
        if (empty($valid))
            $this->respondWithError("vimeo_validation_fail");

        if (!empty($valid)) {
            $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
            $db->beginTransaction();
            try {
                // Getting the URL information.
                $information = $this->_handleInformation($values['type'], $code);

                $values['code'] = $code;

                if (empty($information['title'])) {
                    $information['title'] = '';
                }
                if (empty($information['description'])) {
                    $information['description'] = '';
                }

                $values['title'] = (!empty($values['title'])) ? $values['title'] : $information['title'];
                $values['description'] = !empty($values['description']) ? $values['description'] : $information['description'];
                $values['duration'] = !empty($information['duration']) ? $information['duration'] : '';

                if (!empty($values['main_channel_id'])) {
                    $channel = Engine_Api::_()->getItem('sitevideo_channel', $values['main_channel_id']);
                    $channel->videos_count++;
                    $channel->save();
                }

                // create video
                $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');
                $video = $table->createRow();
                $video->setFromArray($values);
                $video->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }

            return $video;
        } else
            return 0;
    }

    /**
     * Get helper method
     *
     * @return array
     */
    private function _extractCode($url, $type) {
        switch ($type) {
            //youtube
            case "1":
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                if ($arr['host'] === 'youtu.be') {
                    $data = explode("?", $new_code['basename']);
                    $code = $data[0];
                } else {
                    $parameters = $arr["query"];
                    parse_str($parameters, $data);
                    $code = $data['v'];
                    if ($code == "") {
                        $code = $new_code['basename'];
                    }
                }
                return $code;
            //vimeo
            case "2":
                // get the first variable after slash
                $code = @pathinfo($url);
                return isset($code['basename']) ? $code['basename'] : "";
            case "4":
                // get the first variable after slash
                $code = @pathinfo($url);
                return isset($code['basename']) ? $code['basename'] : "";
            case "6":
            case "5":
                return $url;
        }
    }

    private function _checkDailymotion($code) {
        $path = "http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/" . $code;
        $data = @file_get_contents($path);
        return ((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data))))) ? true : false;
    }

    private function _checkVimeo($code) {
        //http://www.vimeo.com/api/docs/simple-api
        //http://vimeo.com/api/v2/video
        $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $id = count($data->video->id);

        if ($id == 0)
            return false;
        return true;
    }

    // YouTube Functions
    private function _checkYouTube($code) {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $key = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
        if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key))
            return false;

        $data = Zend_Json::decode($data);
        if (empty($data['items']))
            return false;
        return true;
    }

    private function _checkInstagram($code) {
        $path = "https://api.instagram.com/oembed?url=" . $code;
        $data = @file_get_contents($path);
        return ((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data))))) ? true : false;

    }

    private function _checkTwitter($code) {
        $path = "https://api.twitter.com/1/statuses/oembed.json?id=" . $code;
        $data = @file_get_contents($path);
        return ((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data))))) ? true : false;

    }

    /**
     * Retrieves information and returns title and description.
     *
     * @return array
     */
    // retrieves infromation and returns title + desc
    private function _handleInformation($type, $code) {
        switch ($type) {
            //youtube
            case "1":
                $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
                $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
                if (empty($data)) {
                    return;
                }
                $data = Zend_Json::decode($data);
                $information = array();
                $youtube_video = $data['items'][0];
                $information['title'] = $youtube_video['snippet']['title'];
                $information['description'] = $youtube_video['snippet']['description'];
                $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
                return $information;
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
            //dailymotion
            case "4":
                $path = "http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/" . $code;

                $data = @file_get_contents($path);
                $information = array();
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $dailymotionData = Zend_Json::decode($data);

                    $information['title'] = $dailymotionData['title'];
                    $information['description'] = $dailymotionData['description'];
                    $durationUrl = 'https://api.dailymotion.com/video/' . $code . '?fields=duration';

                    $json_duration = file_get_contents($durationUrl);
                    if ($json_duration) {
                        $durationDecode = json_decode($json_duration);
                        $information['duration'] = $durationDecode->duration;
                    }
                }
                return $information;
            case "instagram":
                $path = "https://api.instagram.com/oembed/?url=" . $code;
                $data = @file_get_contents($path);
                $information = array();
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $instagramData = Zend_Json::decode($data);
                    $information['title'] = $instagramData['title'];
                    $information['description'] = "";
                }
                return $information;
            case "twitter":
                $path = "https://api.twitter.com/1/statuses/oembed.json?id=" . $code;
                $data = @file_get_contents($path);
                $information = array();
                if (((is_string($data) && (is_object(json_decode($data)) || is_array(json_decode($data)))))) {
                    $twitterData = Zend_Json::decode($data);
                    $information['url'] = $twitterData['url'];
                }
                return $information;
        }
    }

    /**
     * Upload video from device
     *
     * @return array
     */
    private function _uploadVideoAction($values) {
        if (!$this->_helper->requireUser()->checkRequire())
            return;

        if (empty($_FILES['filedata']))
            return;

        if (!isset($_FILES['filedata']) || !is_uploaded_file($_FILES['filedata']['tmp_name']))
            $this->respondWithError("invalid_upload");

        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
        if (in_array(pathinfo($_FILES['filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions))
            $this->respondWithError("invalid_upload");

        $db = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getAdapter();
        $db->beginTransaction();
        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $params = array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            );

            $video = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->createVideo($params, $_FILES['filedata'], $values);

            // sets up title and owner_id now just incase members switch page as soon as upload is completed
            if(empty($values['description']))
                $values['description'] = isset($values['body']) ? $values['body']:"";
            
            $video->title = (!empty($values['title'])) ? $values['title'] : "video";
            $video->description = (!empty($values['description'])) ? $values['description'] : '';
            $video->owner_id = $viewer->getIdentity();
            $video->owner_type = 'user';
            $video->main_channel_id = (!empty($values['main_channel_id'])) ? $values['main_channel_id'] : '';
            if (!empty($values['main_channel_id'])) {
                $channel = Engine_Api::_()->getItem('sitevideo_channel', $values['main_channel_id']);
                $channel->videos_count++;
                $channel->save();
            }
            $video->category_id = (!empty($values['category_id'])) ? $values['category_id'] : '';
            $video->subcategory_id = (!empty($values['subcategory_id'])) ? $values['subcategory_id'] : '';
            $video->subsubcategory_id = (!empty($values['subsubcategory_id'])) ? $values['subsubcategory_id'] : '';
            $video->profile_type = (!empty($values['profile_type'])) ? $values['profile_type'] : '';
            //$video->networks_privacy = Zend_Json::encode(explode(',', $values['networks_privacy']));


            $video->location = (!empty($values['location'])) ? $values['location'] : '';
            $video->rotation = (!empty($values['rotation'])) ? $values['rotation'] : '';
            $video->search = (!empty($values['search'])) ? $values['search'] : 0;
            $video->password = (!empty($values['password'])) ? $values['password'] : '';
            $video->duration = (!empty($values['duration'])) ? $values['duration'] : 0;

            $video->type = 3;
            $video->save();

            $db->commit();
        } catch (Exception $e) {

            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        return $video;
    }

    // handles thumbnails
    public function _handleThumbnail($type, $code = null) {
        switch ($type) {

            //youtube
            case "1":
                $thumbnail = "";
                $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
                foreach ($thumbnailSize as $size) {
                    $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
                    $file_headers = @get_headers($thumbnailUrl);
                    if (isset($file_headers[0]) && strpos($file_headers[0], '404 Not Found') == false) {
                        $thumbnail = $thumbnailUrl;
                        break;
                    }
                }
                return $thumbnail;
            //vimeo
            case "2":
                $thumbnail = "";
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                if (isset($data->video->thumbnail_large))
                    $thumbnail = $data->video->thumbnail_large;
                else if (isset($data->video->thumbnail_medium))
                    $thumbnail = $data->video->thumbnail_medium;
                else if (isset($data->video->thumbnail_small))
                    $thumbnail = $data->video->thumbnail_small;

                return $thumbnail;
            //dailymotion
            case "4":
                $thumbnail = "";
                $thumbnailUrl = 'https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_small_url,thumbnail_large_url,thumbnail_medium_url';
                $json_thumbnail = file_get_contents($thumbnailUrl);
                if ($json_thumbnail) {
                    $thumbnails = json_decode($json_thumbnail);
                    if (isset($thumbnails->thumbnail_large_url))
                        $thumbnail = $thumbnails->thumbnail_large_url;
                    else if (isset($thumbnails->thumbnail_medium_url)) {
                        $thumbnail = $thumbnails->thumbnail_medium_url;
                    } else if (isset($thumbnails->thumbnail_small_url)) {
                        $thumbnail = $thumbnails->thumbnail_small_url;
                    }
                }
                return $thumbnail;
        }
    }

    private function _authSet($values, $subject) {
        $auth = Engine_Api::_()->authorization()->context;

        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        if (isset($values['auth_comment']))
            $auth_comment = $values['auth_comment'];
        else
            $auth_comment = "everyone";

        if (isset($values['auth_view']))
            $auth_view = $values['auth_view'];
        else
            $auth_view = "everyone";

        $commentMax = array_search($auth_comment, $roles);
        $viewMax = array_search($auth_view, $roles);
        foreach ($roles as $i => $role) {
            $auth->setAllowed($subject, $role, 'comment', ($i <= $commentMax));
            $auth->setAllowed($subject, $role, 'view', ($i <= $viewMax));
        }
    }

    private function _videoThumnail($video) {
        // Now try to create thumbnail
        $thumbFileRow = array();
        $thumbnail = $this->_handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);
        if (@GetImageSize($thumbnail)) {
            $valid_thumb = true;
        } else {
            $valid_thumb = false;
        }

        if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
            $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

            $src_fh = fopen($thumbnail, 'r');
            $tmp_fh = fopen($tmp_file, 'w');
            stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

            $image = Engine_Image::factory();
            $image->open($tmp_file)
                    ->resize(120, 240)
                    ->write($thumb_file)
                    ->destroy();

            try {
                $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                    'parent_type' => $video->getType(),
                    'parent_id' => $video->getIdentity()
                ));

                // Remove temp file
                @unlink($thumb_file);
                @unlink($tmp_file);
            } catch (Exception $e) {
                
            }
        }
        return $thumbFileRow;
    }

    private function _myPlaylist($paginator = null) {
        if (empty($paginator)) {
            return;
        }
        try {

            $items_count = $this->getRequestParam("limit", 20);
            $paginator->setItemCountPerPage($items_count);
            $requestPage = $this->getRequestParam('page', 1);
            $paginator->setCurrentPageNumber($requestPage);

            foreach ($paginator as $playlist) {
                $browsePlayList = $playlist->toArray();
// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
                $browsePlayList = array_merge($browsePlayList, $getContentImages);

// Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
                $browsePlayList = array_merge($browsePlayList, $getContentImages);

                $browsePlayList["owner_title"] = $playlist->getOwner()->getTitle();
                $isAllowedView = $video->canView($viewer);
                $browsePlayList["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browsePlayList["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($playlist);
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Video'),
                        'name' => 'delete',
                        'url' => 'advancedvideo/playlist/delete/' . $playlist->getIdentity(),
                    );



                    $menus[] = array(
                        'label' => $this->translate('Edit Video'),
                        'name' => 'delete',
                        'url' => 'advancedvideo/playlist/edit/' . $playlist->getIdentity(),
                    );
                    $browsePlayList['menu'] = $menus;
                }


                $response['response'][] = $browsePlayList;
            }

            $response['totalItemCount'] = $paginator->getTotalItemCount();
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
        return $response;
    }

    protected function _process($video, $type, $compatibilityMode = false) {
        $tmpDir = $this->getTmpDir();
        $video = $this->getVideo($video);
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Update to encoding status
        $video->status = 2;
        $video->type = 'upload';
        $video->save();

        // Prepare information
        $owner = $video->getOwner();

        // Pull video from storage system for encoding
        $storageObject = $this->getStorageObject($video);
        $originalPath = $this->getOriginalPath($storageObject);

        $outputPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vconverted.' . $type;
        $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormalthumb.jpg';

        $thumbNormalLargePath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormallargethumb.jpg';
        $thumbMainPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vmainthumb.jpg';
        $width = 480;
        $height = 386;

        $videoCommand = $this->buildVideoCmd($video, $width, $height, $type, $originalPath, $outputPath, $compatibilityMode);

        // Prepare output header
        $output = PHP_EOL;
        $output .= $originalPath . PHP_EOL;
        $output .= $outputPath . PHP_EOL;
        $output .= $thumbPath . PHP_EOL;

        // Prepare logger
        $log = new Zend_Log();
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/sitevideo.log'));

        // Execute video encode command
        $videoOutput = $output .
                $videoCommand . PHP_EOL .
                shell_exec($videoCommand);

        // Log
        if ($log) {
            $log->log($videoOutput, Zend_Log::INFO);
        }

        // Check for failure
        $success = $this->conversionSucceeded($video, $videoOutput, $outputPath);

        // Failure
        if (!$success) {
            if (!$compatibilityMode) {
                $this->_process($video, true);
                return;
            }

            $exceptionMessage = '';

            $db = $video->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $video->save();
                $exceptionMessage = $this->notifyOwner($video, $owner);
                $db->commit();
            } catch (Exception $e) {
                $videoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;

                if ($log) {
                    $log->write($e->__toString(), Zend_Log::ERR);
                }

                $db->rollBack();
            }

            // Write to additional log in dev
            if (APPLICATION_ENV == 'development') {
                file_put_contents($tmpDir . '/' . $video->video_id . '.txt', $videoOutput);
            }

            throw new Sitevideo_Model_Exception($exceptionMessage);
        }

        // Success
        else {
            // Get duration of the video to caculate where to get the thumbnail
            $duration = $this->getDuration($videoOutput);

            // Log duration
            if ($log) {
                $log->log('Duration: ' . $duration, Zend_Log::INFO);
            }

            // Fetch where to take the thumbnail
            $thumb_splice = $duration / 2;

            $thumbMainSuccess = $this->generateMainThumbnail($outputPath, $output, $thumb_splice, $thumbMainPath, $log);

            // Save video and thumbnail to storage system
            $params = array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id
            );

            $db = $video->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $storageObject->setFromArray($params);
                //$storageObject->store($outputPath);

                if ($thumbMainSuccess) {
                    $thumbMainSuccessRow = Engine_Api::_()->storage()->create($thumbMainPath, array_merge($params, array('type' => 'thumb.main')));
                }

                $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormalthumb.jpg';

                $thumbNormalLargePath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vnormallargethumb.jpg';

                $image = Engine_Image::factory();
                $image->open($thumbMainPath)
                        ->resize(720, 720)
                        ->write($thumbNormalLargePath)
                        ->destroy();

                $image = Engine_Image::factory();
                $image->open($thumbMainPath)
                        ->resize(375, 375)
                        ->write($thumbPath)
                        ->destroy();
                Engine_Api::_()->storage()->create($thumbNormalLargePath, array_merge($params, array('type' => 'thumb.large')));
                $thumbNormalSuccessRow = Engine_Api::_()->storage()->create($thumbPath, array_merge($params, array('type' => 'thumb.normal')));

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();

                // delete the files from temp dir
                unlink($originalPath);
                unlink($outputPath);

                if ($thumbSuccess) {
                    unlink($thumbPath);
                }

                $video->status = 7;
                $video->save();

                $this->notifyOwner($video, $owner);

                throw $e; // throw
            }

            // Video processing was a success!
            // Save the information
            if ($thumbMainSuccess) {
                $video->photo_id = $thumbMainSuccessRow->file_id;
            }

            $video->duration = $duration;
            $video->status = 1;
            $video->save();

            // delete the files from temp dir
            unlink($originalPath);
            unlink($outputPath);
            unlink($thumbPath);
            unlink($thumbMainPath);
            unlink($thumbNormalLargePath);
            // insert action in a separate transaction if video status is a success
            $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
            $db = $actionsTable->getAdapter();
            $db->beginTransaction();

            try {
                // new action
                $chanel = $video->getChannelModel();
                $actionType = $chanel ? 'sitevideo_channel_video_new' : 'sitevideo_video_new';
                $actionObject = $chanel ? $chanel : $video;
                $action = $actionsTable->addActivity($owner, $actionObject, $actionType);

                if ($action) {
                    $actionsTable->attachActivity($action, $video);
                }

                // notify the owner
                Engine_Api::_()->getDbtable('notifications', 'activity')
                        ->addNotification($owner, $owner, $video, 'sitevideo_processed');

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e; // throw
            }
        }
    }

    private function generateMainThumbnail($outputPath, $output, $thumb_splice, $thumbPath, $log) {
        set_time_limit(0);
        $ffmpeg_path = $this->getFFMPEGPath();
        // Thumbnail process command
        $thumbCommand = $ffmpeg_path . ' '
                . '-i ' . escapeshellarg($outputPath) . ' '
                . '-f image2' . ' '
                . '-ss ' . $thumb_splice . ' '
                . '-vframes 1' . ' '
                . '-v 2' . ' '
                . '-y ' . escapeshellarg($thumbPath) . ' '
                . '2>&1';

        // Process thumbnail
        $thumbOutput = $output .
                $thumbCommand . PHP_EOL .
                shell_exec($thumbCommand);

        // Log thumb output
        if ($log) {
            $log->log($thumbOutput, Zend_Log::INFO);
        }

        // Check output message for success
        $thumbSuccess = true;
        if (preg_match('/video:0kB/i', $thumbOutput)) {
            $thumbSuccess = false;
        }
        $mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        // Resize thumbnail
        if ($thumbSuccess) {
            try {
                $image = Engine_Image::factory();
                $image->open($thumbPath)
                        ->resize($mainHeight, $mainWidth)
                        ->write($thumbPath)
                        ->destroy();
            } catch (Exception $e) {
                $this->_addMessage((string) $e->__toString());
                $thumbSuccess = false;
            }
        }

        return $thumbSuccess;
    }

    private function getTmpDir() {
        // Check the video temporary directory
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
                DIRECTORY_SEPARATOR . 'sitevideo';

        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0777, true)) {
            return;
        }

        if (!is_writable($tmpDir)) {
            return;
        }
        return $tmpDir;
    }

    private function getVideo($video) {
        // Get the video object
        if (is_numeric($video)) {
            $video = Engine_Api::_()->getItem('sitevideo_video', $video);
        }

        if (!($video instanceof Sitevideo_Model_Video)) {
            return;
        }
        return $video;
    }

    private function getStorageObject($video) {
        // Pull video from storage system for encoding
        $storageObject = Engine_Api::_()->getItem('storage_file', $video->file_id);

        if (!$storageObject) {
            return;
        }

        return $storageObject;
    }

    private function getOriginalPath($storageObject) {
        $originalPath = $storageObject->temporary();

        if (!file_exists($originalPath)) {
            return;
        }
        return $originalPath;
    }

    private function buildVideoCmd($video, $width, $height, $type, $originalPath, $outputPath, $compatibilityMode = false) {

        $ffmpeg_path = $this->getFFMPEGPath();

        $videoCommand = $ffmpeg_path . ' '
                . '-i ' . escapeshellarg($originalPath) . ' '
                . '-ab 64k' . ' '
                . '-ar 44100' . ' '
                . '-qscale 5' . ' '
                . '-r 25' . ' ';

        if ($type == 'mp4')
            $videoCommand .= '-vcodec libx264' . ' '
                    . '-acodec aac' . ' '
                    . '-strict experimental' . ' '
                    . '-preset veryfast' . ' '
                    . '-f mp4' . ' '
            ;
        else
            $videoCommand .= '-vcodec flv -f flv ';

        if ($compatibilityMode) {
            $videoCommand .= "-s ${width}x${height}" . ' ';
        } else {
            $filters = $this->getVideoFilters($video, $width, $height);
            $videoCommand .= '-vf "' . $filters . '" ';
        }

        $videoCommand .= '-y ' . escapeshellarg($outputPath) . ' '
                . '2>&1';
        return $videoCommand;
    }

    private function conversionSucceeded($video, $videoOutput, $outputPath) {
        $success = true;

        // Unsupported format
        if (preg_match('/Unknown format/i', $videoOutput) || preg_match('/Unsupported codec/i', $videoOutput) || preg_match('/patch welcome/i', $videoOutput) || preg_match('/Audio encoding failed/i', $videoOutput) || !is_file($outputPath) || filesize($outputPath) <= 0) {
            $success = false;
            $video->status = 3;
        }

        // This is for audio files
        else if (preg_match('/video:0kB/i', $videoOutput)) {
            $success = false;
            $video->status = 5;
        }

        return $success;
    }

    private function notifyOwner($video, $owner) {
        $translate = Zend_Registry::get('Zend_Translate');
        $language = !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null;

        $notificationMessage = '';
        $exceptionMessage = 'Unknown encoding error.';

        if ($video->status == 3) {
            $exceptionMessage = 'Video format is not supported by FFMPEG.';
            $notificationMessage = 'Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.';
        } else if ($video->status == 5) {
            $exceptionMessage = 'Audio-only files are not supported.';
            $notificationMessage = 'Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.';
        } else if ($video->status == 7) {
            $notificationMessage = 'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.';
        }

        $notificationMessage = $translate->translate(sprintf($notificationMessage, '', ''), $language);

        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $video, 'sitevideo_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_general', true),
        ));

        return $exceptionMessage;
    }

    private function getDuration($videoOutput) {
        $duration = 0;

        if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches)) {
            list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
            $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
        }

        return $duration;
    }

    private function getVideoFilters($video, $width, $height) {
        $filters = "scale=$width:$height";
        if ($video->rotation > 0) {
            $filters = "pad='max(iw,ih*($width/$height))':ow/($width/$height):(ow-iw)/2:(oh-ih)/2,$filters";

            if ($video->rotation == 180)
                $filters = "hflip,vflip,$filters";
            else {
                $transpose = array(90 => 1, 270 => 2);

                if (empty($transpose[$video->rotation]))
                    return;
                $filters = "transpose=${transpose[$video->rotation]},$filters";
            }
        }

        return $filters;
    }

    private function getFFMPEGPath() {
        set_time_limit(0);
        // Check we can execute
        if (!function_exists('shell_exec')) {
            return;
        }

        if (!function_exists('exec')) {
            return;
        }
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        // Make sure FFMPEG path is set
        $ffmpeg_path = $coreSettings->getSetting('sitevideo.ffmpeg.path', $coreSettings->getSetting('sitevideo.ffmpeg.path', ''));
        if (!$ffmpeg_path) {
            return;
        }

        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);

            if ($return > 0) {
                return;
            }
        }

        return $ffmpeg_path;
    }

    public function handleIframelyInformation($uri) {
        $iframelyDisallowHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_iframely_disallow');
        if (parse_url($uri, PHP_URL_SCHEME) === null) {
            $uri = "http://" . $uri;
        }
        $uriHost = Zend_Uri::factory($uri)->getHost();
        if ($iframelyDisallowHost && in_array($uriHost, $iframelyDisallowHost)) {
            return;
        }
        $config = Engine_Api::_()->getApi('settings', 'core')->core_iframely;
        $iframely = Engine_Iframely::factory($config)->get($uri);
        if (!in_array('player', array_keys($iframely['links']))) {
            return;
        }
        $information = array('thumbnail' => '', 'title' => '', 'description' => '', 'duration' => '');
        if (!empty($iframely['links']['thumbnail'])) {
            $information['thumbnail'] = $iframely['links']['thumbnail'][0]['href'];
            if (parse_url($information['thumbnail'], PHP_URL_SCHEME) === null) {
                $information['thumbnail'] = str_replace(array('://', '//'), '', $information['thumbnail']);
                $information['thumbnail'] = "http://" . $information['thumbnail'];
            }
        }

        if (!isset($information['thumbnail']) || empty($information['thumbnail'])) {
            $page_content = file_get_contents($uri);
            $dom_obj = new DOMDocument();
            $dom_obj->loadHTML($page_content);
            $meta_val = null;

            foreach ($dom_obj->getElementsByTagName('meta') as $meta) {

                if ($meta->getAttribute('property') == 'og:image') {

                    $information['thumbnail'] = $meta->getAttribute('content');
                }
            }
        }

        if (!empty($iframely['meta']['title'])) {
            $information['title'] = $iframely['meta']['title'];
        }
        if (!empty($iframely['meta']['description'])) {
            $information['description'] = $iframely['meta']['description'];
        }
        if (!empty($iframely['meta']['duration'])) {
            $information['duration'] = $iframely['meta']['duration'];
        }
        $information['code'] = $iframely['html'];
        return $information;
    }

    private function getchannelUserVideo($videoObj) {
        $values['status'] = 1;
//        $values['search'] = 1;
        if (isset($videoObj->main_channel_id) && !empty($videoObj->main_channel_id)) {
            $values['channel_id'] = $videoObj->main_channel_id;
        } else {
            $values['owner_id'] = $videoObj->owner_id;
        }


        try {

            $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($values);
            $items_count = $this->getRequestParam("limit", 20);
            $paginator->setItemCountPerPage($items_count);
            $requestPage = $this->getRequestParam('page', 1);
            $paginator->setCurrentPageNumber($requestPage);
            foreach ($paginator as $video) {
                $browseVideo = $video->toArray();
                if (!isset($browseVideo['video_id']) || empty($browseVideo['video_id']) || $browseVideo['video_id'] == $videoObj->video_id)
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

                $isAllowedView = $video->canView($viewer);
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

//            $subscribeInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->isSubscribedUser($channel->category_id, $viewer->getIdentity());
//            $response['is_subscribe'] = count($subscribeInfo) > 0 ? 1 : 0;

            $response['totalItemCount'] = $paginator->getTotalItemCount();
            $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();
            return $response;
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

//end of thumbnail
}

//end of class

