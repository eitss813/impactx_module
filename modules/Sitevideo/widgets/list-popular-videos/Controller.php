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
class Sitevideo_Widget_ListPopularVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $param = array();
        $this->view->videoHeight = $param['videoHeight'] = $this->_getParam('videoHeight', 200);
        $this->view->videoWidth = $param['videoWidth'] = $this->_getParam('videoWidth', 200);
        $this->view->videoTitleTruncation = $param['videoTitleTruncation'] = $this->_getParam('videoTitleTruncation', 22);
        $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        $this->view->videoInfo = $param['videoInfo'] = $this->_getParam('videoInfo', array("title", "owner", "view", "like", "comment"));
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'comment');
        $param['interval'] = $interval = $this->_getParam('interval', 'overall');
        $video_url = $this->view->url(array('action' => 'browse'), "sitevideo_video_general", true);
        $this->view->titleLink = $this->_getParam('titleLink', '<a href="' . $video_url . '">Explore All »</a>');
        $this->view->titleLinkPosition = $this->_getParam('titleLinkPosition', 'top');
        $param['featured'] = $this->_getParam('featured', 0);
        $param['category_id'] = $this->_getParam('category_id');
        $param['subcategory_id'] = $this->_getParam('subcategory_id');

        switch ($param['popularType']) {
            case 'view':
                $param['orderby'] = 'view_count';
                break;
            case 'comment':
                $param['orderby'] = 'comment_count';
                break;
            case 'like':
                $param['orderby'] = 'like_count';
                break;
            case 'rating':
                $param['orderby'] = 'rating';
                break;
            case 'creation':
                $param['orderby'] = 'creation_date';
                break;
            case 'modified':
                $param['orderby'] = 'modified_date';
                break;
            case 'random':
                $param['orderby'] = 'random';
                break;
        }

        $this->view->params = $param;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->videoBySettings($param);

        if(empty($sitevideoVideosList))
            return $this->setNoRender();
        
        // Do not render if nothing to show
        if (($paginator->getTotalItemCount() <= 0)) {
            return $this->setNoRender();
        }

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $channelSize = array();
        $channelSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $channelSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $channelSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $channelSize['width'] = $this->view->videoWidth;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($channelSize, $this->view->videoWidth);
        $this->view->thumbnailType = $thumbnailType;
    }

}
