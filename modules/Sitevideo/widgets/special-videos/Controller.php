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
class Sitevideo_Widget_SpecialVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
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
        $params['video_ids'] = $this->_getParam('toValues', array());

        if (!empty($params['video_ids'])) {
            $params['video_ids'] = explode(',', $params['video_ids']);
            $params['video_ids'] = array_unique($params['video_ids']);

            if (!empty($params['video_ids']) && Count($params['video_ids']) <= 0) {
                return $this->setNoRender();
            }
        } else {
            return $this->setNoRender();
        }

        $params['limit'] = $this->_getParam('itemCount');
        $this->view->gridViewWidth = $this->_getParam('columnWidth', 150);
        $this->view->gridViewHeight = $this->_getParam('columnHeight', 150);
        $this->view->titleTruncation = $this->_getParam('titleTruncation');
        $this->view->videoInfo = $this->_getParam('videoInfo');
        $this->view->itemCount = $params['limit'];
        //GET VIDEOS
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->videoBySettings($params);
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        if(empty($sitevideoVideosList))
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
