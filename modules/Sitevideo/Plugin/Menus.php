<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Menus {

    public function canCreateChannels() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return false;
        }
        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Must be able to create channels
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function canCreatePlaylists() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            return false;
        }

        return true;
    }

    public function canCreateVideos() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Must be able to create videos
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function canManageVideos() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Must be able to create videos or create channels
        if (Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create') || Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'create')) {
            return true;
        }

        return false;
    }

    public function canCreateBadge() {
        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Badge is Enable or Not
        $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.badge', 1);
        if (empty($badge_enable)) {
            return false;
        }
        return true;
    }

    public function canViewChannels() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return false;
        }
        // Must be able to view channels
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'view')) {
            return false;
        }

        return true;
    }

    public function canViewVideos() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Must be able to view videos
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'view')) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_SitevideoProfileAdd() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$subject)
            return false;

        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Must be able to create videos
        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
            return false;
        }

        if ($subject->owner_id != $viewer->getIdentity())
            return false;
        if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()):
            return array(
                'label' => $view->translate('Upload Videos'),
                'class' => 'data_SmoothboxSEAOClass seao_smoothbox',
                'route' => 'sitevideo_video_general',
                'params' => array(
                    'action' => 'create',
                    'channel_id' => $subject->getIdentity()
                )
            );
        else :
            return array(
                'label' => $view->translate('Upload Videos'),
                //  'class' => 'data_SmoothboxSEAOClass seao_smoothbox',
                'route' => 'sitevideo_video_general',
                'params' => array(
                    'action' => 'create',
                    'channel_id' => $subject->getIdentity()
                )
            );

        endif;
    }

    public function onMenuInitialize_SitevideoProfileManage() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$subject)
            return false;
        if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }
        $viewer_id = $viewer->getIdentity();
        if ($subject->owner_id != $viewer_id)
            return false;

        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        if (empty($allowed_upload_video_video))
            return false;

        return array(
            'label' => $view->translate('Manage Videos'),
            /*'icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/channel_manage.png',*/
            'class' => '',
            'route' => 'sitevideo_specific',
            'params' => array(
                'action' => 'editvideos',
                'channel_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SitevideoProfileEdit() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        return array(
            'label' => $view->translate('Edit Channel'),
            /*icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/channel_editinfo.png',*/
            'class' => '',
            'route' => 'sitevideo_specific',
            'params' => array(
                'action' => 'edit',
                'channel_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SitevideoProfileDelete() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        $mine = true;
        if (!$subject->getOwner()->isSelf($viewer)) {
            $mine = false;
        }

        if (!$mine && !$subject->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        if (!$subject->authorization()->isAllowed($viewer, 'delete')) {
            return false;
        }

        return array(
            'label' => $view->translate('Delete Channel'),
            /*'icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/channel_delete.png',*/
            'route' => 'sitevideo_specific',
            'class' => 'smoothbox',
            'params' => array(
                'action' => 'delete',
                'channel_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SitevideoProfileShare() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        $mine = true;
        if (!$subject->getOwner()->isSelf($viewer)) {
            $mine = false;
        }

        if (!$mine && !$subject->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        // Badge is Enable or Not
        $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.badge', 1);
        if (empty($badge_enable)) {
            return false;
        }

        return array(
            'label' => $view->translate('Share via Badge'),
            /*'icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/badge-share.png',*/
            'route' => 'sitevideo_badge',
            'class' => '',
            'params' => array(
                'action' => 'create',
                'channel_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SitevideoProfileMakechanneloftheday() {
        return false;
//        $viewer = Engine_Api::_()->user()->getViewer();
//        $subject = Engine_Api::_()->core()->getSubject();
//        $view = Zend_Registry::isRegistered('Zend_View') ?
//                Zend_Registry::get('Zend_View') : null;
//
//        if ($viewer->level_id != 1) {
//            return false;
//        }
//
//        // Must be able to view channels
//        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', 'everyone', 'view') || !Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', 'registered', 'view')) {
//            return false;
//        }
//
//        return array(
//            'label' => $view->translate('Make Channel of the Day'),
//            'icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/channel.png',
//            'route' => 'sitevideo_specific',
//            'class' => 'smoothbox item_icon_channel',
//            'params' => array(
//                'action' => 'add-channel-of-day',
//                'channel_id' => $subject->getIdentity(),
//            )
//        );
    }

    public function onMenuInitialize_SitevideoProfileGetlink() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;


        if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        return array(
            'label' => $view->translate('Get Link'),
            /*'icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/link.png',*/
            'route' => 'sitevideo_video_general',
            'class' => 'smoothbox sitevideo_icon_link',
            'params' => array(
                'action' => 'get-link',
                'subject' => $subject->getGuid(),
            )
        );
    }

    public function onMenuInitialize_SitevideoProfileEditlocation() {
        return false;
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$subject->authorization()->isAllowed($viewer, 'edit') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            return false;
        }

        return array(
            'label' => $view->translate('Edit Location'),
            /*'icon' => $view->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/map-search.png',*/
            'route' => 'sitevideo_video_general',
            'class' => 'smoothbox',
            'params' => array(
                'action' => 'edit-location',
                'subject' => $subject->getGuid(),
            )
        );
    }

    public function onMenuInitialize_SitevideoProfileSuggesttofriend() {

        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        $suggestionPluginStatus = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
        if (!empty($suggestionPluginStatus)) {
            $flag = false;
            if (!empty($suggestionPluginStatus)) {
                $linkShouldShow = Engine_Api::_()->suggestion()->getModSettings('sitevideo_channel', 'link');

                $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
                $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
                if ($versionStatus >= 0) {
                    $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitevideo_channel', $subject->getIdentity(), 1, null);
                    if (!empty($modContentObj)) {
                        $contentCreatePopup = @COUNT($modContentObj);
                    }
                }

                if (!empty($linkShouldShow) && !empty($contentCreatePopup)) {
                    $flag = true;
                }
            }
            // END WORK FOR SUGGESTION
        }

        if (empty($flag)) {
            return false;
        }

        return array(
            'label' => $view->translate('Suggest to Friends'),
            /*'icon' => $view->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/sugg_blub.png',*/
            'route' => 'default',
            'class' => 'smoothbox icon_page_friend_suggestion',
            'params' => array(
                'module' => 'suggestion',
                'controller' => 'index',
                'action' => 'switch-popup',
                'modName' => $subject->getType() == 'sitevideo_channel' ? 'sitevideo_channel': 'sitevideo',
                'modContentId' => $subject->getIdentity()
            )
        );
    }

    //VIDEO VIEW PAGE OPTIONS
    public function onMenuInitialize_SitevideoVideoEdit($row) {

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $subject = Engine_Api::_()->core()->getSubject();

        //VIDEO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT VIDEO
        if (!$subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) {
            return false;
        }

        return array(
            'label' => 'Edit Video',
            'route' => 'default',
            'class' => 'ui-btn-action smoothbox',
            'params' => array(
                'modules' => 'sitevideo',
                'controller' => 'video',
                'action' => 'edit',
                'video_id' => $subject->video_id
            )
        );
    }

    //VIDEO VIEW PAGE OPTIONS
    public function onMenuInitialize_SitevideoVideoDelete($row) {

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $subject = Engine_Api::_()->core()->getSubject();

        //VIDEO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT VIDEO
        if (!$subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'delete')) {
            return false;
        }

        return array(
            'label' => 'Delete Video',
            'route' => 'default',
            'class' => 'ui-btn-danger smoothbox',
            'params' => array(
                'modules' => 'sitevideo',
                'controller' => 'video',
                'action' => 'delete',
                'video_id' => $subject->video_id
            )
        );
    }

    public function onMenuInitialize_SitevideoVideoShare($row) {
        $subject = Engine_Api::_()->core()->getSubject();
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (!$viewer_id) {
            return false;
        }
        return array(
            'label' => 'Share',
            'class' => 'ui-btn-action smoothbox',
            'route' => 'default',
            'params' => array(
                'module' => 'activity',
                'action' => 'share',
                'type' => $subject->getType(),
                'id' => $subject->getIdentity(),
            )
        );
    }

    public function onMenuInitialize_SitevideoVideoReport($row) {
        $subject = Engine_Api::_()->core()->getSubject();
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (!$viewer_id) {
            return false;
        }
        return array(
            'label' => 'Report',
            'class' => 'ui-btn-action smoothbox',
            'route' => 'default',
            'params' => array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'subject' => $subject->getGuid(),
            )
        );
    }

    public function onMenuInitialize_SitevideoVideoMakeProfileVideo($row) {
        
    }

    public function onMenuInitialize_SitevideoVideoLocation() {
        return false;
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$subject->authorization()->isAllowed($viewer, 'edit') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            return false;
        }

        return array(
            'label' => $view->translate('Edit Location'),
            'route' => 'default',
            'class' => 'smoothbox',
            'params' => array(
                'module' => 'sitevideo',
                'controller' => 'index',
                'action' => 'edit-location',
                'subject' => $subject->getGuid(),
            )
        );
    }

}
