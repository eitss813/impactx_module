<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Dashboardmenus {

    public function onMenuInitialize_SitevideoDashboardEditinfo($row) {

        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitevideo_specific',
            'action' => 'edit',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitevideoDashboardYoutubevideos($row) {

        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }
        $youtubeUploadPrivacy = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitevideo_channel', "youtube_channel");
        $apiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
        if (empty($youtubeUploadPrivacy) || empty($apiKey)) {
            return false;
        }
        return array(
            'label' => $row->label,
            'route' => 'sitevideo_dashboard',
            'action' => 'youtube-videos',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitevideoDashboardOverview($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.overview', 1))
            return false;
        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }


        $overviewPrivacy = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitevideo_channel', "overview");
        if (empty($overviewPrivacy)) {
            return false;
        }
        return array(
            'label' => $row->label,
            'route' => 'sitevideo_specific',
            'action' => 'overview',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitevideoDashboardProfilepicture($row) {

        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        //AUTHORIZATION CHECK
        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($channel, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitevideo_dashboard',
            'action' => 'change-photo',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitevideoDashboardEditphoto($row) {

        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed($channel, $viewer, "photo");
        if (empty($allowPhotoUpload)) {
            return false;
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        return array(
            'label' => $row->label,
            'route' => 'sitevideo_albumspecific',
            'action' => 'editphotos',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitevideoDashboardEditvideo($row) {
        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        if (empty($allowed_upload_video_video))
            return false;

        if ($channel->owner_id != $viewer_id)
            return false;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        return array(
            'label' => $row->label,
            'route' => 'sitevideo_dashboard',
            'action' => 'video-edit',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitevideoDashboardEditmetakeyword($row) {

        //GET CHANNEL ID AND CHANNEL OBJECT
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->getType() !== 'sitevideo_channel') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $channel->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        $allowMetaKeywords = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitevideo_channel', "metakeyword");

        if (empty($allowMetaKeywords) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.metakeyword', 1)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitevideo_dashboard',
            'action' => 'meta-detail',
            'class' => 'ajax_dashboard_enabled',
            'href' => '',
            'params' => array(
                'channel_id' => $channel->getIdentity()
            ),
        );
    }

}
