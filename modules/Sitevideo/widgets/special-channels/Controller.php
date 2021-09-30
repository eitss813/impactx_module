<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_SpecialChannelsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }

        $starttime = $this->_getParam('starttime');
        $endtime = $this->_getParam('endtime');
        $currenttime = date('Y-m-d H:i:s');

        if (!empty($starttime) && $currenttime < $starttime) {
            return $this->setNoRender();
        }

        if (!empty($endtime) && $currenttime > $endtime) {
            return $this->setNoRender();
        }

        $params = array();
        $params['channel_ids'] = $this->_getParam('toValues', array());

        if (!empty($params['channel_ids'])) {
            $params['channel_ids'] = explode(',', $params['channel_ids']);
            $params['channel_ids'] = array_unique($params['channel_ids']);

            if (!empty($params['channel_ids']) && Count($params['channel_ids']) <= 0) {
                return $this->setNoRender();
            }
        } else {
            return $this->setNoRender();
        }

        $params['limit'] = $this->_getParam('itemCount');
        $this->view->gridViewWidth = $this->_getParam('columnWidth', 150);
        $this->view->gridViewHeight = $this->_getParam('columnHeight', 150);
        $this->view->channelInfo = $this->_getParam('channelInfo');
        $this->view->titleTruncation = $this->_getParam('titleTruncation');
        $this->view->itemCount = $params['limit'];
        //GET CHANNELS
        $sitevideoMyChannelVideo = Zend_Registry::isRegistered('sitevideoMyChannelVideo') ? Zend_Registry::get('sitevideoMyChannelVideo') : null;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->channelBySettings($params);
        if (empty($sitevideoMyChannelVideo))
            return $this->setNoRender();

        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }

        $paginator->setItemCountPerPage($params['limit']);
        $channelSize = array();
        $channelSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $channelSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $channelSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $channelSize['width'] = $this->view->gridViewWidth;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($channelSize, $this->view->gridViewWidth);
        $this->view->thumbnailType = $thumbnailType;
    }

}
