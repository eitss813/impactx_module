<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LightboxController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_LightboxController extends Core_Controller_Action_Standard {

    public function indexAction() {

        $this->view->module_name = $modulename = $this->_getParam('module_name', null);
        $this->view->isLightBox = true;
        $this->view->is_ajax_lightbox = $this->_getParam('is_ajax_lightbox', 0);

        $id = $this->_getParam('video_id', $this->_getParam('id', null));

        if (empty($id))
            return;

        $video = Engine_Api::_()->getDbtable('videos', $modulename)->fetchRow(array('video_id = ?' => $id));

        if (empty($video))
            return;

        $subject = Engine_Api::_()->core()->setSubject($video);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $video_id = $video->getIdentity();

        // if this is sending a message id, the user is being directed from a coversation
        // check if member is part of the conversation
        $message_id = $this->getRequest()->getParam('message');
        $message_view = false;
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient($viewer)) {
                $message_view = true;
            }
        }
        $this->view->message_view = $message_view;
        if ($modulename == 'sitevideo') {
            $this->view->viewPermission = $video->authorization()->isAllowed($viewer, 'view');
        }
        if ($message_view) {
            $this->view->viewPermission = true;
        }
        $sitevideo_password_protected = isset($_COOKIE["sitevideo_password_protected_$video_id"]) ? $_COOKIE["sitevideo_password_protected_$video_id"] : 0;
        $this->view->videoPasswordProtected = false;
        if (isset($video->password) && !empty($video->password) && $video->owner_id != $viewer->getIdentity() && !$sitevideo_password_protected) {
            $this->view->viewPermission = false;
            $this->view->videoPasswordProtected = true;
        }

        $this->view->canComment = $video->authorization()->isAllowed($viewer, 'comment');
        // Check if edit/delete is allowed
        $this->view->can_edit = $can_edit = $video->authorization()->isAllowed($viewer, 'edit');

        $this->view->can_delete = $can_delete = $video->authorization()->isAllowed($viewer, 'delete');
        $params = array();
        $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
        $this->view->width = $settingsCoreApi->getSetting('sitevideo.lightbox.player.width', 0);
        $this->view->height = $settingsCoreApi->getSetting('sitevideo.lightbox.player.height', 440);
        switch ($modulename) {
            case 'video' :
            case 'sitevideo' :
                // check if embedding is allowed
                $can_embed = true;
                if (!$settingsCoreApi->getSetting('sitevideo.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;
                $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
                $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $video->getIdentity(), 'resource_type' => 'video'));
                $this->view->rated = $ratingTable->checkRated(array('resource_id' => $video->getIdentity(), 'resource_type' => 'video'));
                $this->view->update_permission = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideorating.update', 1);
                if ($video->category_id) {
                    $this->view->category = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategory($video->category_id);
                }
         //   todo: 5.2.1 Upgrade => fixed video issue additional cloumn which is deleted in SE version 5
//                if (!$video->main_channel_id) {
//                    $video->main_channel_id = 0;
//                }
                $this->view->deleteLinkParamsArray = array('route' => 'sitevideo_video_specific', 'action' => 'delete', 'video_id' => $video_id, 'format' => 'smoothbox', 'parent_type' => $video->parent_type, 'parent_id' => $video->parent_id);

                $this->view->editLinkParamsArray = array('route' => 'sitevideo_video_specific', 'action' => 'edit', 'video_id' => $video_id,
                    'parent_type' => $video->parent_type,
                    'parent_id' => $video->parent_id);

                $this->view->tagFilterUrlArray = array('module' => 'sitevideo', 'controller' => 'video', 'action' => 'browse');
                $this->view->tag_filter_url_route = 'default';



                if ($modulename == 'sitevideo') {
                    $this->view->rateLinkParamsArray = array('module' => 'sitevideo', 'controller' => 'index', 'action' => 'rate');
                    $this->view->embedLinkParamsArray = array('module' => 'sitevideo', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'video_id' => $video_id, 'format' => 'smoothbox');
                } else {
                    $this->view->embedLinkParamsArray = array('module' => 'video', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'video_id' => $video_id, 'format' => 'smoothbox');

                    $this->view->rateLinkParamsArray = array('module' => 'video', 'controller' => 'index', 'action' => 'rate');
                }


                break;
            case 'ynvideo':  //@Reference : 'YouNet Video Plugin' by author YouNet Company 
                $watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
                $watchLaterTbl->update(array(
                    'watched' => '1',
                    'watched_date' => date('Y-m-d H:i:s')
                        ), array(
                    "video_id = {$video_id}",
                    "user_id = {$viewer_id}"
                ));
                // check if embedding is allowed
                $can_embed = true;
                if (!$settingsCoreApi->getSetting('video.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;

                $apiYnvideo = Engine_Api::_()->ynvideo();
                $this->view->rating_count = $apiYnvideo->ratingCount($video_id);
                $this->view->rated = $apiYnvideo->checkRated($video_id, $viewer_id);

                if ($video->category_id) {
                    $this->view->categories = $categories = Engine_Api::_()->getDbTable('categories', 'ynvideo')->getCategories(array($video->category_id, $video->subcategory_id));
                }

                $this->view->deleteLinkParamsArray = array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'delete', 'video_id' => $video_id, 'format' => 'smoothbox');

                $this->view->editLinkParamsArray = array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'edit', 'video_id' => $video_id);

                $this->view->embedLinkParamsArray = array('module' => 'video', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'video', 'controller' => 'index', 'action' => 'rate');

                $this->view->tagFilterUrlArray = array('action' => 'list');

                $this->view->tag_filter_url_route = 'video_general';
                break;
            case 'avp': //@Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
                //@Comments : We have modified our code to make Video Lightbox viewer Plugin compatible to their plugin.
                if ($video->hasGroupPrivacy() && !in_array($viewer->getIdentity(), array_merge(explode(";", $video->can_view), array($video->owner_id)))) {
                    $this->view->viewPermission = false;
                }
                $apiAvp = Engine_Api::_()->avp();
                $this->view->rating_count = $apiAvp->getTotalRatings($video);

                $this->view->rated = ($video->isOwner($viewer) ? 2 : (int) $apiAvp->hasRated($video));
                $this->view->deleteLinkParamsArray = array('action' => 'delete', 'id' => $video_id, 'route' => 'avp_general', 'format' => 'smoothbox');

                $this->view->editLinkParamsArray = array('action' => 'edit', 'id' => $video_id, 'route' => 'avp_general');
                $this->view->can_embed = false;

                $this->view->rateLinkParamsArray = array('module' => 'avp', 'controller' => 'index', 'action' => 'rate');
                $this->view->tagFilterUrlArray = array();
                $this->view->tag_filter_url_route = 'avp_general';

                $this->view->favorite = false;
                $this->view->can_playlist = false;
                if (!empty($viewer_id)) {
                    $this->view->can_playlist = true;
                    $favorites = $apiAvp->getFavorites($viewer_id);

                    if (!empty($favorites)) {
                        $favorites = !empty($favorites->favorites) ? Zend_Json::decode($favorites->favorites) : array();
                        if (in_array($video->video_id, $favorites))
                            $this->view->favorite = true;
                    }
                }
                $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
                $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($video, 1);
                break;

            case 'sitepagevideo':
                $sitepage = Engine_Api::_()->getItem('sitepage_page', $video->page_id);
                $page_id = $sitepage->getIdentity();

                $this->view->viewPermission = (bool) Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');

                $this->view->can_delete = $this->view->can_edit = $can_edit = $video->owner_id == $viewer_id || Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');


                if ($viewer_id != $video->owner_id && $can_edit != 1 && ($video->search != 1 || $video->status != 1)) {
                    $this->view->viewPermission = false;
                }

                $can_embed = true;
                if (!$settingsCoreApi->getSetting('sitepagevideo.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;

                $this->view->deleteLinkParamsArray = array('route' => 'sitepagevideo_delete', 'video_id' => $video_id, 'page_id' => $page_id);

                $this->view->editLinkParamsArray = array('route' => 'sitepagevideo_edit', 'video_id' => $video_id, 'page_id' => $page_id);

                $this->view->embedLinkParamsArray = array('module' => 'sitepagevideo', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'rate');
                $this->view->canComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');

                $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo')->ratingCount($video_id);

                //MAKE HIGHLIGHTED OR NOT
                $this->view->canMakeHighlighted = $settingsCoreApi->getSetting('sitepagevideo.featured', 1);

                $this->view->allowMakeFeatured = $this->view->allowViewSitepage = false;
                if (!empty($viewer_id) && $viewer->level_id == 1) {
                    $auth = Engine_Api::_()->authorization()->context;
                    $this->view->allowViewSitepage = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
                    $this->view->allowMakeFeatured = $this->view->allowViewSitepage;
                }

                $params['search_text'] = $this->_getParam('search_text', null);
                $params['my_video'] = $this->_getParam('my_video', null);
                $params['browse'] = $this->_getParam('browse', null);


                break;
            case 'sitebusinessvideo':

                $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $video->business_id);
                $sitebusiness_id = $sitebusiness->getIdentity();

                $this->view->viewPermission = (bool) Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');

                $this->view->can_delete = $this->view->can_edit = $can_edit = $video->owner_id == $viewer_id || Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');

                if ($viewer_id != $video->owner_id && $can_edit != 1 && ($video->search != 1 || $video->status != 1)) {
                    $this->view->viewPermission = false;
                }

                $can_embed = true;
                if (!$settingsCoreApi->getSetting('sitebusinessvideo.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;

                $this->view->deleteLinkParamsArray = array('route' => 'sitebusinessvideo_delete', 'video_id' => $video_id, 'business_id' => $sitebusiness_id);

                $this->view->editLinkParamsArray = array('route' => 'sitebusinessvideo_edit', 'video_id' => $video_id, 'business_id' => $sitebusiness_id);

                $this->view->embedLinkParamsArray = array('module' => 'sitebusinessvideo', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'sitebusinessvideo', 'controller' => 'index', 'action' => 'rate');

                $this->view->canComment = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');

                $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitebusinessvideo')->ratingCount($video_id);

                //MAKE HIGHLIGHTED OR NOT
                $this->view->canMakeHighlighted = $settingsCoreApi->getSetting('sitebusinessvideo.featured', 1);

                $this->view->allowMakeFeatured = $this->view->allowViewSitebusiness = false;
                if (!empty($viewer_id) && $viewer->level_id == 1) {
                    $auth = Engine_Api::_()->authorization()->context;
                    $this->view->allowViewSitebusiness = $auth->isAllowed($sitebusiness, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitebusiness, 'registered', 'view') === 1 ? true : false;
                    $this->view->allowMakeFeatured = $this->view->allowViewSitebusiness;
                }

                $params['search_text'] = $this->_getParam('search_text', null);
                $params['my_video'] = $this->_getParam('my_video', null);
                $params['browse'] = $this->_getParam('browse', null);
                break;
            case 'sitegroupvideo':
                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $video->group_id);
                $group_id = $sitegroup->getIdentity();

                $this->view->viewPermission = (bool) Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');

                $this->view->can_delete = $this->view->can_edit = $can_edit = $video->owner_id == $viewer_id || Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');


                if ($viewer_id != $video->owner_id && $can_edit != 1 && ($video->search != 1 || $video->status != 1)) {
                    $this->view->viewPermission = false;
                }

                $can_embed = true;
                if (!$settingsCoreApi->getSetting('sitegroupvideo.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;

                $this->view->deleteLinkParamsArray = array('route' => 'sitegroupvideo_delete', 'video_id' => $video_id, 'group_id' => $group_id);

                $this->view->editLinkParamsArray = array('route' => 'sitegroupvideo_edit', 'video_id' => $video_id, 'group_id' => $group_id);

                $this->view->embedLinkParamsArray = array('module' => 'sitegroupvideo', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'sitegroupvideo', 'controller' => 'index', 'action' => 'rate');
                $this->view->canComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');

                $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitegroupvideo')->ratingCount($video_id);

                //MAKE HIGHLIGHTED OR NOT
                $this->view->canMakeHighlighted = $settingsCoreApi->getSetting('sitegroupvideo.featured', 1);

                $this->view->allowMakeFeatured = $this->view->allowViewSitegroup = false;
                if (!empty($viewer_id) && $viewer->level_id == 1) {
                    $auth = Engine_Api::_()->authorization()->context;
                    $this->view->allowViewSitegroup = $auth->isAllowed($sitegroup, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitegroup, 'registered', 'view') === 1 ? true : false;
                    $this->view->allowMakeFeatured = $this->view->allowViewSitegroup;
                }

                $params['search_text'] = $this->_getParam('search_text', null);
                $params['my_video'] = $this->_getParam('my_video', null);
                $params['browse'] = $this->_getParam('browse', null);


                break;
            case 'sitestorevideo':
                $sitestore = Engine_Api::_()->getItem('sitestore_store', $video->store_id);
                $store_id = $sitestore->getIdentity();

                $this->view->viewPermission = (bool) Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');

                $this->view->can_delete = $this->view->can_edit = $can_edit = $video->owner_id == $viewer_id || Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');


                if ($viewer_id != $video->owner_id && $can_edit != 1 && ($video->search != 1 || $video->status != 1)) {
                    $this->view->viewPermission = false;
                }

                $can_embed = true;
                if (!$settingsCoreApi->getSetting('sitestorevideo.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;

                $this->view->deleteLinkParamsArray = array('route' => 'sitestorevideo_delete', 'video_id' => $video_id, 'store_id' => $store_id);

                $this->view->editLinkParamsArray = array('route' => 'sitestorevideo_edit', 'video_id' => $video_id, 'store_id' => $store_id);

                $this->view->embedLinkParamsArray = array('module' => 'sitestorevideo', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'sitestorevideo', 'controller' => 'index', 'action' => 'rate');
                $this->view->canComment = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');

                $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitestorevideo')->ratingCount($video_id);

                //MAKE HIGHLIGHTED OR NOT
                $this->view->canMakeHighlighted = $settingsCoreApi->getSetting('sitestorevideo.featured', 1);

                $this->view->allowMakeFeatured = $this->view->allowViewSitestore = false;
                if (!empty($viewer_id) && $viewer->level_id == 1) {
                    $auth = Engine_Api::_()->authorization()->context;
                    $this->view->allowViewSitestore = $auth->isAllowed($sitestore, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitestore, 'registered', 'view') === 1 ? true : false;
                    $this->view->allowMakeFeatured = $this->view->allowViewSitestore;
                }

                $params['search_text'] = $this->_getParam('search_text', null);
                $params['my_video'] = $this->_getParam('my_video', null);
                $params['browse'] = $this->_getParam('browse', null);


                break;
            case 'sitereview':
                // check if embedding is allowed
                $can_embed = true;
                $sitereview = $video->getParent();
                $can_embed = true;
                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.video.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;
                $this->view->rating_count = Engine_Api::_()->getDbTable('videoratings', 'sitereview')->ratingCount($video_id);

                $this->view->rated = Engine_Api::_()->getDbTable('videoratings', 'sitereview')->checkRated($video_id, $viewer_id);
                // Check if edit/delete is allowed
                $this->view->can_edit = $can_edit = $can_edit = $this->view->can_edit = $sitereview->authorization()->isAllowed($viewer, "edit_listtype_$sitereview->listingtype_id");
                if (empty($can_edit) && $viewer_id == $video->owner_id) {
                    $this->view->can_edit = $can_edit = 1;
                }

                $this->view->can_delete = $can_delete = $this->view->can_edit;

                $this->view->deleteLinkParamsArray = array('route' => "sitereview_video_delete_listtype_$sitereview->listingtype_id", 'listing_id' => $sitereview->getIdentity(), 'video_id' => $video_id, 'format' => 'smoothbox');

                $this->view->editLinkParamsArray = array('route' => "sitereview_video_edit_listtype_$sitereview->listingtype_id", 'listing_id' => $sitereview->getIdentity(), 'video_id' => $video_id);

                $this->view->tagFilterUrlArray = array('action' => 'browse');
                $this->view->tag_filter_url_route = 'sitereview_video_general';


                $this->view->embedLinkParamsArray = array('route' => "sitereview_video_embed_listtype_$sitereview->listingtype_id", 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'sitereview', 'controller' => 'video', 'action' => 'rate');

                break;

            case 'siteevent':
                $siteevent = $video->getParent();
                $can_embed = true;
                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.video.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($video->allow_embed) && !$video->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;
                $this->view->rating_count = Engine_Api::_()->getDbTable('videoratings', 'siteevent')->ratingCount($video_id);

                $this->view->rated = Engine_Api::_()->getDbTable('videoratings', 'siteevent')->checkRated($video_id, $viewer_id);
                // Check if edit/delete is allowed
                $this->view->can_edit = $can_edit = $can_edit = $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");
                if (empty($can_edit) && $viewer_id == $video->owner_id) {
                    $this->view->can_edit = $can_edit = 1;
                }

                $this->view->can_delete = $can_delete = $this->view->can_edit;

                $this->view->deleteLinkParamsArray = array('route' => "siteevent_video_delete", 'event_id' => $siteevent->getIdentity(), 'video_id' => $video_id, 'format' => 'smoothbox');

                $this->view->editLinkParamsArray = array('route' => "siteevent_video_edit", 'event_id' => $siteevent->getIdentity(), 'video_id' => $video_id);

                $this->view->tagFilterUrlArray = array('action' => 'browse');
                $this->view->tag_filter_url_route = 'siteevent_video_general';


                $this->view->embedLinkParamsArray = array('route' => "siteevent_video_embed", 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'siteevent', 'controller' => 'video', 'action' => 'rate');

                break;
            case 'sitestoreproduct':
                // check if embedding is allowed
                $can_embed = true;
                $sitestoreproduct = $video->getParent();
                $can_embed = true;
                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.video.embeds', 1)) {
                    $can_embed = false;
                } else if (isset($sitestoreproductvideo->allow_embed) && !$sitestoreproductvideo->allow_embed) {
                    $can_embed = false;
                }
                $this->view->can_embed = $can_embed;
                $this->view->rating_count = Engine_Api::_()->getDbTable('videoratings', 'sitestoreproduct')->ratingCount($video_id);

                $this->view->rated = Engine_Api::_()->getDbTable('videoratings', 'sitestoreproduct')->checkRated($video_id, $viewer_id);
                // Check if edit/delete is allowed
                $this->view->can_edit = $can_edit = $can_edit = $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");
                if (empty($can_edit) && $viewer_id == $video->owner_id) {
                    $this->view->can_edit = $can_edit = 1;
                }

                $this->view->can_delete = $can_delete = $this->view->can_edit;

                $this->view->deleteLinkParamsArray = array('route' => "sitestoreproduct_video_delete", 'product_id' => $sitestoreproduct->getIdentity(), 'video_id' => $video_id, 'format' => 'smoothbox');

                $this->view->editLinkParamsArray = array('route' => "sitestoreproduct_video_edit", 'product_id' => $sitestoreproduct->getIdentity(), 'video_id' => $video_id);

                $this->view->tagFilterUrlArray = array('action' => 'browse');
                $this->view->tag_filter_url_route = 'sitestoreproduct_video_general';


                $this->view->embedLinkParamsArray = array('route' => "sitestoreproduct_video_embed", 'id' => $video_id, 'format' => 'smoothbox');

                $this->view->rateLinkParamsArray = array('module' => 'sitestoreproduct', 'controller' => 'video', 'action' => 'rate');

                break;
        }

        $params['type'] = $this->_getParam('type', null);
        $params['subject_guid'] = $this->_getParam('subject_guid', null);
        $params['duration'] = $this->_getParam('duration', 0);

        if ($this->_getParam('count', null)) {
            $params['count'] = $this->_getParam('count', null);
        } else {
            $params['count'] = Engine_Api::_()->sitevideo()->getCountTotal($video, $params);
        }

        if ($params['count'] > 1) {

            if ($this->_getParam('offset', null)) {
                $params['offset'] = $this->_getParam('offset', null);
            } else {
                $params['offset'] = Engine_Api::_()->sitevideo()->getCollectibleIndex($video, $params);
            }
            $this->view->prevVideo = Engine_Api::_()->sitevideo()->getPrevVideo($video, $params);
            if (empty($params['offset'])) {
                $this->view->PrevOffset = $params['count'] - 1;
            } else {
                $this->view->PrevOffset = $params['offset'] - 1;
            }
            $this->view->nextVideo = Engine_Api::_()->sitevideo()->getNextVideo($video, $params);
            if ($params['offset'] >= ($params['count'] - 1)) {
                $this->view->NextOffset = 0;
            } else {
                $this->view->NextOffset = $params['offset'] + 1;
            }
            $this->view->showLink = $params['count'];
        }

        $this->view->count = $params['count'];
        $this->view->params = $params;

        // increment count
        $embedded = "";
        if ($video->status == 1) {
            if (!$video->isOwner($viewer) && $this->view->viewPermission) {
                $video->view_count++;
                $video->save();
            }
            if ($modulename != 'avp') {
                $embedded = $video->getRichContent(true);
                $this->view->videoTags = $video->tags()->getTagMaps();
            } else {
                //@Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
                //@Comments : We have modified our code to make Video Lightbox viewer Plugin compatible to their plugin.
                $embedded = $video->getPlayer(false, false, array(), true);
                $this->view->videoTags = array();
            }
        }

        if ($this->view->viewPermission) {
            $this->view->videoEmbedded = $embedded;
            if ($modulename != 'sitereview')
                $this->view->canShowSuggestFriendLink = Engine_Api::_()->seaocore()->canShowSuggestFriendLink($modulename);
        }

        if ($video->checkType('youtube')) {
            $this->view->videoEmbedded = $embedded = str_replace('feature=player_embedded&fs=1', 'feature=player_embedded&fs=1&autoplay=1', $embedded);
        } else if ($video->checkType('vimeo')) {
            $this->view->videoEmbedded = $embedded = str_replace('color=&amp;fullscreen=1', 'color=&amp;fullscreen=1&autoplay=1', $embedded);
        } else if ($video->checkType('dailymotion')) {
            $this->view->videoEmbedded = $embedded = str_replace('&foreground=E8D9AC&highlight=FFFFF0', '&foreground=E8D9AC&highlight=FFFFF0&autoplay=1', $embedded);
        }
        if ($video->checkType('upload') && $video->status == 1) {
            if (!empty($video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                if ($storage_file) {
                    $this->view->video_location = $storage_file->map();
                    $this->view->video_extension = $storage_file->extension;
                }
            }
        }

        $this->view->video = $video;
        if ($video->status != 1) {
            $this->view->viewPermission = false;
        }
        //@Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
        //@Comments : We have modified our code to make Video Lightbox viewer Plugin compatible to their plugin.
        if ($modulename == 'avp' && $video->embed != "") {
            $importer = new Avp_Import;

            try {
                $adapter = $importer->getAdapterByKey($video->video_website);
                $settings = Engine_Api::_()->getApi('settings', 'avp');
                $disabled = @$settings->disabled;
                if (!empty($disabled)) {
                    $disabled = Zend_Json::decode($settings->disabled);
                } else {
                    $disabled = array();
                }

                if (in_array($adapter->key(), $disabled)) {
                    $this->view->viewPermission = false;
                    $this->view->error_message = $this->view->translate('We currently have problems with videos imported from ') . $adapter->name() . '. ' . $this->view->translate('Please try again later.');
                }
            } catch (Exception $e) {
                $this->view->viewPermission = false;
                $this->view->error_message = $this->view->translate('We currently have problems with this video.') . ' ' . $this->view->translate('Please try again later.');
            }
        }
    }

    public function setFeaturedAction() {

        $subject_guid = $this->_getParam('subject_guid', null);
        $status = false;

        if (!empty($subject_guid)) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
            $subject->featured = !$subject->featured;
            $subject->save();
            $status = true;
        }

        echo Zend_Json::encode(array('status' => $status, 'featured' => $subject->featured));
        exit(0);
    }

    public function setHighlightedAction() {

        $subject_guid = $this->_getParam('subject_guid', null);
        $status = false;

        if (!empty($subject_guid)) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
            $subject->highlighted = !$subject->highlighted;
            $subject->save();
            $status = true;
        }

        echo Zend_Json::encode(array('status' => $status, 'highlighted' => $subject->highlighted));
        exit(0);
    }

    public function saveContentAction() {
        $subject_guid = $this->_getParam('subject_guid', null);
        $text_string = $this->_getParam('text_string', null);
        $column = $this->_getParam('column', null);
        $status = false;

        if (!empty($subject_guid)) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
            $subject->$column = $text_string;
            $subject->save();
            $status = true;
        }

        echo Zend_Json::encode(array('status' => $status));
        exit(0);
    }

//@Reference : 'Video Feed Plugin' by author Radcodes Developments
    //@Comments : We have modified our code to make Video Lightbox viewer Plugin compatible to their plugin.
    public function videofeedProfileAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewPermission = Engine_Api::_()->authorization()->isAllowed('videofeed', $viewer, 'view');

        try {
            $yt = new Zend_Gdata_YouTube();
            $yt->setMajorProtocolVersion(2);
            $this->view->video = $videoEntry = $yt->getVideoEntry($this->_getParam('videofeed_id'));

            $subject = new Videofeed_Model_Videofeed(array('video' => $videoEntry));
            Engine_Api::_()->core()->setSubject($subject);
        } catch (Exception $ex) {
            return $this->view->message = 'The video you are looking for does not exist.';
        }
        if (!$this->view->viewPermission) {
            $this->view->message = 'You do not have permission to view this private page.';
        }
    }

}
