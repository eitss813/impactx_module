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
class Sitevideo_Widget_ChannelViewController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;

        $this->view->videoWidth = $params['videoWidth'] = $this->_getParam('videoWidth', 150);
        $this->view->videoHeight = $params['videoHeight'] = $this->_getParam('videoHeight', 150);
        $this->view->marginVideo = $params['margin_video'] = $this->_getParam('margin_video', 2);
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption', array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'location', 'facebook', 'twitter', 'linkedin', 'googleplus'));
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $sitevideoChannelView = Zend_Registry::isRegistered('sitevideoChannelView') ? Zend_Registry::get('sitevideoChannelView') : null;
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);

        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $params['videoOption'] = array();
        }

        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['videoWidth'] = $this->view->videoWidth;
        $thumbnailType = $this->findThumbnailType($videoSize, $this->view->videoWidth);
        $this->view->thumbnailType = $params['thumbnailType'] = $thumbnailType;
        $channelurl = $params['channel_url'];

        if(empty($sitevideoChannelView))
            return $this->setNoRender();
        
        if (empty($channelurl))
            return $this->setNoRender();
        $params['channel_id'] = Engine_Api::_()->sitevideo()->getChannelId($channelurl);

        if (empty($params['channel_id']))
            return $this->setNoRender();

        $this->view->channelId = $params['channel_id'];
        $this->view->channel = Engine_Api::_()->getItem('sitevideo_channel', $this->view->channelId);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $this->view->viewerId = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $this->view->allow_upload_video = $allow_upload_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        $this->view->params = $params;
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);

        //ADD COUNT TO TITLE
        if ($this->view->totalCount > 0) {
            $this->_childCount = $this->view->totalCount;
        }
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

    public function getChildCount() {
        return $this->_childCount;
    }

}
