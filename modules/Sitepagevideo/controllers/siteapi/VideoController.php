<?php

class Sitepagevideo_VideoController extends Siteapi_Controller_Action_Standard {

    public function init() {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $this->respondWithError('unauthorized');
        }

        //CHECK SUBJECT IS EXIST OR NOT IF NOT EXIST THEN SET ACCORDING TO THE PAGE ID AND PHOTO ID
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
                    null !== ($video = Engine_Api::_()->getItem('sitepagevideo_video', $video_id))) {
                Engine_Api::_()->core()->setSubject($video);
            } else if (0 !== ($page_id = (int) $this->_getParam('page_id')) &&
                    null !== ($sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id))) {
                Engine_Api::_()->core()->setSubject($sitepage);
            }
        }

        //GET PAGE ID
        $page_id = $this->_getParam('page_id');

        // PACKAGE BASE PRIYACY START    
        if (!empty($page_id)) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            if (!empty($sitepage)) {
                if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
                        $this->respondWithError('unauthorized');
                    }
                } else {
                    $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                    if (empty($isPageOwnerAllow)) {
                        $this->respondWithError('unauthorized');
                    }
                }
            }
        }
        // PACKAGE BASE PRIYACY END
        else {
            if (Engine_Api::_()->core()->hasSubject() != null) {
                $video = Engine_Api::_()->core()->getSubject();
                $page_id = $video->page_id;
            }
        }
    }

    public function viewAction() {

        $this->validateRequestMethod();

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        $value = $video->toArray();
        
        // User Rating work
        $ratingTable = Engine_Api::_()->getDbtable('ratings','sitepagevideo');
        $checkRated = $ratingTable->checkRated($video->video_id , $viewer_id);
        $averagerating = $ratingTable->rateVideo($video->video_id);
        
        $value['owner_title'] = $video->getOwner()->getTitle();
        $value['rating'] = $averagerating;
        $ownerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
        $videoImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, false);

        $value = array_merge($value, $ownerImages);
        $value = array_merge($value, $videoImages);

        $tempMenu = array();
        if ($video->authorization()->isAllowed($viewer, 'edit')) {

            $tempMenu[] = array(
                'label' => $this->translate('Edit Video'),
                'name' => 'edit',
                'url' => 'sitepage/video/edit/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
                'urlParams' => array(
                )
            );

            $tempMenu[] = array(
                'label' => $this->translate('Delete Video'),
                'name' => 'delete',
                'url' => 'sitepage/video/delete/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
                'urlParams' => array(
                )
            );
        }
        
        if(!$checkRated)
        {
            $tempMenu[] = array(
                'label' => $this->translate('Rating'),
                'description' => $this->translate("give rating in stars"),
                'name' => 'rating',
                'url' => 'sitepage/video/rating/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
                'urlParams' => array()
            );
        }


        $tempMenu[] = array(
            'label' => $this->translate('Highlight Video'),
            'name' => 'highlight',
            'url' => 'sitepage/video/highlight/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
            'urlParams' => array(
            )
        );

        $tempMenu[] = array(
            'label' => $this->translate('Make Featured'),
            'name' => 'featured',
            'url' => 'sitepage/video/featured/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
            'urlParams' => array(
            )
        );

        $tempMenu[] = array(
            'label' => $this->translate('Comment on Video'),
            'name' => 'comment',
            'url' => 'sitepage/video/comment/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
            'urlParams' => array(
            )
        );

        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        if (!$likeTable->isLike($subject, $viewer)) {
            $tempMenu[] = array(
                'label' => $this->translate('Like on Video'),
                'name' => 'like',
                'url' => 'sitepage/video/like/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
                'urlParams' => array(
                )
            );
        } else {
            $tempMenu[] = array(
                'label' => $this->translate('unlike on Video'),
                'name' => 'unlike',
                'url' => 'sitepage/video/unlike/' . $sitepage->getIdentity() . '/' . $video->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        $tempMenu[] = array(
            'name' => 'share',
            'label' => $this->translate('Share This Video'),
            'url' => 'activity/share',
            'urlParams' => array(
                "type" => $subject->getType(),
                "id" => $subject->getIdentity()
            )
        );

        $value['guttermenu'] = $tempMenu;

        $this->respondWithSuccess($value, true);
    }

    /*
     * Create a Directory page video
     */

    public function createAction() {

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }

        if (Engine_Api::_()->core()->hasSubject('sitepage_page')) {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        }

        if (!$sitepage)
            $this->respondWithError("no_record");

        // Manage admin work Start

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
        if (empty($isManageAdmin))
            $this->respondWithError('unauthorized');

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if (empty($isManageAdmin))
            $can_edit = 0;
        else
            $can_edit = 1;

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
        if (empty($isManageAdmin) && empty($can_edit))
            $this->respondWithError('unauthorized');

        // Manage admin work end

        if ($this->getRequest()->isGet()) {
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'sitepagevideo')->getCreateVideoForm();
            $this->respondWithSuccess($form_fields);
        } elseif ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepagevideo')->createformvalidators();
            $values['validators'] = $validators;

            $validationMessage = $this->isValid($values);

//            var_dump($validationMessage);
//            die;
            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $getPackagevideoCreate = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');
            $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.set.type', 0);
            if (empty($isModType)) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagevideo.utility.type', convert_uuencode($sitepageModHostName));
            }

            $values['owner_id'] = $viewer->getIdentity();

            // Video creation process
            $videoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');

            $db = $videoTable->getAdapter();
            $db->beginTransaction();
            try {

                if ($values['type'] == 3) {
                    $sitepagevideo = Engine_Api::_()->getApi('Siteapi_Core', 'sitepagevideo')->uploadVideo();
                } else {
                    $sitepagevideo = $videoTable->createRow();
                }

                $sitepagevideo->setFromArray($values);
                $sitepagevideo->page_id = $sitepage->page_id;
                $sitepagevideo->save();

                // Thumbnail creation
                $thumbnail = $this->handleThumbnail($sitepagevideo->type, $sitepagevideo->code);
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
                            'parent_type' => $sitepagevideo->getType(),
                            'parent_id' => $sitepagevideo->getIdentity()
                        ));

                        // Remove temp files
                        @unlink($thumb_file);
                        @unlink($tmp_file);
                    } catch (Exception $e) {
                        
                    }
                    $information = $this->handleInformation($sitepagevideo->type, $sitepagevideo->code);
                    $sitepagevideo->duration = $information['duration'];
                    $sitepagevideo->photo_id = $thumbFileRow->file_id;
                    $sitepagevideo->status = 1;
                    $sitepagevideo->featured = 0;
                    $sitepagevideo->save();

                    // Insert new action item
                    $insert_action = true;
                }



                if ($values['ignore'] == true) {
                    $sitepagevideo->status = 1;
                    $sitepagevideo->save();
                    $insert_action = true;
                }

                // Comment privacy
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                $auth_comment = "everyone";
                $commentMax = array_search($auth_comment, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitepagevideo, $role, 'comment', ($i <= $commentMax));
                }

                // Tag work
                if (!empty($values['tags'])) {
                    $tags = preg_split('/[,]+/', $values['tags']);
                    $sitepagevideo->tags()->addTagMaps($viewer, $tags);
                }


                // Adding activity feed 
                if ($insert_action && $sitepagevideo->search == 1) {
                    $owner = $sitepagevideo->getOwner();
                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    $activityFeedType = null;
                    if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
                        $activityFeedType = 'sitepagevideo_admin_new';
                    elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
                        $activityFeedType = 'sitepagevideo_new';

                    if ($activityFeedType) {
                        $action = $actionTable->addActivity($owner, $sitepage, $activityFeedType);
                        Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
                    }


                    if ($action != null) {
                        $actionTable->attachActivity($action, $sitepagevideo);
                    }

                    // Sending activity feed to facebook.
                    $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                    if (!empty($enable_Facebooksefeed)) {

                        $video_array = array();
                        $video_array['type'] = 'sitepagevideo_new';
                        $video_array['object'] = $sitepagevideo;

                        Engine_Api::_()->facebooksefeed()->sendFacebookFeed($video_array);
                    }

                    //PAGE VIDEO CREATE NOTIFICATION AND EMAIL WORK
//                    $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
//                    if (!empty($action)) {
//                        if ($sitepageVersion >= '4.3.0p1') {
//                            Engine_Api::_()->sitepage()->sendNotificationEmail($sitepagevideo, $action, 'sitepagevideo_create', 'SITEPAGEVIDEO_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');
//                            $isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
//                            if (!empty($isPageAdmins)) {
//                                //NOTIFICATION FOR ALL FOLLWERS.
//                                Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitepagevideo, $action, 'sitepagevideo_create');
//                            }
//                        }
//                    }
                }



                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($sitepagevideo) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * Edit Directory Page video
     */

    public function editAction() {
        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        // Manage admin work start
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $can_edit = 0;
        } else {
            $can_edit = 1;
        }
        // End manage admin work
        // Superadmin, video owner and page owner can edit video
        if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'sitepagevideo')->getEditForm($subject);
            $this->respondWithSuccess($form_fields);
        } elseif ($this->getRequest()->isPost()) {
            Engine_Api::_()->sitepagevideo()->setVideoPackages();

            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepagevideo')->createformvalidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }


            // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            
            try {

                $subject->setFromArray($values);

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $subject->tags()->setTagMaps($viewer, $tags);
                $subject->save();

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     *    Lists the likes and comments on the Directory page video
     */

    public function listcommentsAction() {

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

        $pageApi = Engine_Api::_()->sitepage();
        $pageApi->isManageAdmin($pageSubject, 'edit');
        $viewAllLikes = $this->_getParam('viewAllLikes', false);
        $likes = $subject->likes()->getLikePaginator();

        // Likes work
        $likesData = array();
        if (!empty($likes)) {
            foreach ($likes as $like) {
                $likesData[$like->like_id] = $like->toArray();
                $poster = $like->getPoster();
                $posterImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, true);
                $likesData[$like->like_id]['owner_images'] = $posterImages;
                $likesData[$like->like_id]['owner_title'] = $poster->getTitle();
            }
        }


        // Comments work
        if (null !== ( $page = $this->_getParam('page'))) {
            $commentSelect = $subject->comments()->getCommentSelect('ASC');
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage(10);
        }
        // If not has a page, show the
        else {
            $commentSelect = $subject->comments()->getCommentSelect('DESC');
            $commentSelect->order('comment_id DESC');

            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
        }
        $commentsData = array();
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $poster = $comment->getPoster();
                $posterImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, true);
                $likes = $comment->likes();
                $commentsData[$comment->comment_id] = $comment->toArray();
                $commentsData[$comment->comment_id]['owner_images'] = $posterImages;
                $commentsData[$comment->comment_id]['owner_title'] = $poster->getTitle();
            }
        }

        $response['comments'] = $commentsData;
        $response['likes'] = $likesData;

        $this->respondWithSuccess($response, true);
    }
    
    /*
     * Give rating to a Directory page video
     */
    public function ratingAction()
    {
        
        $this->validateRequestMethod("PUT");
        // Get viewer info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");
        
        $rating = intval($this->_getParam('rating'));
        if(empty($rating) || $rating<=0 || $rating>5)
            $this->respondWithValidationError('parameter_missing', 'rating is missing or is invalid');
        
        $ratingTable = Engine_Api::_()->getDbtable('ratings','sitepagevideo');
        $checkrated = $ratingTable->checkRated($video->video_id , $viewer_id);
        
        if($checkrated)
            $this->respondWithValidationError('already_rated', "You have already rated this video");
        
        // Process
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                
                $ratingTable->setRating($video->video_id , $viewer_id , $rating);
                
                $db->commit();
                $this->successResponseNoContent('no_content');
                
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        
    }

    /*
     * Delete's the video
     *
     */

    public function deleteAction() {

        $this->validateRequestMethod("DELETE");

        // Check user validation
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Get viewer info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        // Start mamage admin check
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $can_edit = 0;
        } else {
            $can_edit = 1;
        }

        // Video owner and page owner can delete the video
        if ($viewer_id != $video->owner_id && $can_edit != 1) {
            $this->respondWithError('unauthorized');
        }

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->delete(array('video_id =?' => $this->getRequest()->getParam('video_id')));
            $video->delete();

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Returns comment form and posts comment on a video
     *
     *
     */

    public function commentAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");


        if ($this->getRequest()->isGet()) {
            $commentform = Engine_Api::_()->getApi('Siteapi_Core', 'Sitepagevideo')->getcommentForm($video->getType(), $video->video_id);
            $this->respondWithSuccess($commentform, true);
        }

        if ($this->getRequest()->isPost()) {
            $values = array();
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepagevideo')->getcommentValidation();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $body = $values['body'];
            $values['type'] = $subject->getType();
            $values['id'] = $subject->video_id;
            $values['identity'] = $subject->video_id;
            $db = $subject->comments()->getCommentTable()->getAdapter();
            $db->beginTransaction();
            try {
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $subjectOwner = $subject->getOwner('user');
                $subject->comments()->addComment($viewer, $body);

                // Activity
                $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body
                ));

                if (!empty($action)) {
                    $activityApi->attachActivity($action, $subject);
                }


                // add notification
//                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
//                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
//                    'label' => $subject->getShortType()
//                ));
                // Add a notification for all users that commented or like except the viewer and poster
                // @todo we should probably limit this
//                $commentedUserNotifications = array();
//                foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
//                    if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
//                        continue;
//
//                    // Don't send a notification if the user both commented and liked this
//                    $commentedUserNotifications[] = $notifyUser->getIdentity();
//                    $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
//                        'label' => $subject->getShortType()
//                    ));
//                }
                // Add a notification for all users that liked
                // @todo we should probably limit this
//                foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
//                    // Skip viewer and owner
//                    if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
//                        continue;
//
//                    // Don't send a notification if the user both commented and liked this
//                    if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
//                        continue;
//
//                    $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
//                        'label' => $subject->getShortType()
//                    ));
//
//
//                    //end check for page admin and page owner
//                }
                // Send notification to Page admins
                $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
                if ($sitepageVersion >= '4.2.9p3') {
                    Engine_Api::_()->sitepage()->itemCommentLike($subject, 'sitepage_contentcomment', $baseOnContentOwner);
                }

                // Increment comment count
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
     * Deletes a comment
     *
     *
     */

    public function removecommentAction() {

        // Validate request method
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }


        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        // Comment id
        $comment_id = $this->_getParam('comment_id');

        // Comment
        $comment = $subject->comments()->getComment($comment_id);
        if (!$comment) {
            $this->respondWithError('no_record');
        }

        // Process
        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->comments()->removeComment($comment_id);
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Allows to like a Video
     *
     */

    public function likeAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            $this->respondWithError('unauthorized');
        }

        $commentedItem = $subject;
        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        if ($likeTable->isLike($subject, $viewer))
            $this->successResponseNoContent('no_content', true);

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();
        try {
            $commentedItem->likes()->addLike($viewer);
            // Add notification
            $owner = $commentedItem->getOwner();
            $this->view->owner = $owner->getGuid();
            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                    'label' => $commentedItem->getShortType()
                ));
            }

            $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
            if ($sitepageVersion >= '4.2.9p3') {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember'))
                    Engine_Api::_()->sitepagemember()->joinLeave($subject, 'Join');
                Engine_Api::_()->sitepage()->itemCommentLike($subject, 'sitepage_contentlike', '');
            }
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     *   Allows to ulike a video
     *
     */

    public function unlikeAction() {
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");


        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            $this->respondWithError('unauthorized');
        }

        // Check for already unliked
        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        if (!$likeTable->isLike($subject, $viewer))
            $this->successResponseNoContent('no_content', true);

        $commentedItem = $subject;
        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();
        try {

            $commentedItem->likes()->removeLike($viewer);

            // Remove notification
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')) {
                Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity()));
            }

            $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
            if ($sitepageVersion >= '4.2.9p3') {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember'))
                    Engine_Api::_()->sitepagemember()->joinLeave($subject, 'Join');
            }

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Make a video as featured
     */

    public function featuredAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        $subject->featured = !$subject->featured;
        $subject->save();
        $this->successResponseNoContent('no_content', true);
    }

    /*
     *   Highlight a video
     */

    public function highlightAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitepagevideo_video')) {
            $subject = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
            $sitepage = $video->getParent();
        }

        if (!$video || !$sitepage)
            $this->respondWithError("no_record");

        $subject->highlighted = !$subject->highlighted;
        $subject->save();
        $this->successResponseNoContent('no_content', true);
    }

    //ACTION FOR HANDLES THUMBNAIL
    private function handleThumbnail($type, $code = null) {
        switch ($type) {
            //youtube
            case "1":
                //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
                return "https://i.ytimg.com/vi/$code/default.jpg";
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                return $thumbnail;
        }
    }

    //ACTION FOR HANDLE INFORMATION
    private function handleInformation($type, $code) {
        switch ($type) {
            //youtube
            case "1":
                $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
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
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
        }
    }

}
