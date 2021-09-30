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
class Sitevideo_Widget_ListPopularChannelsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        if (!$coreApi->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }
        $param = array();
        $sitevideoChannelList = Zend_Registry::isRegistered('sitevideoChannelList') ? Zend_Registry::get('sitevideoChannelList') : null;
        $this->view->channelHeight = $param['channelHeight'] = $this->_getParam('channelHeight', 200);
        $this->view->channelWidth = $param['channelWidth'] = $this->_getParam('channelWidth', 200);
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->channelInfo = $param['channelInfo'] = $this->_getParam('channelInfo', array("title", "owner", "view", "like", "comment"));
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'comment');
        $param['interval'] = $interval = $this->_getParam('interval', 'overall');
        $url = $this->view->url(array('action' => 'browse'), "sitevideo_general", true);
        $this->view->titleLink = $this->_getParam('titleLink', '<a href="' . $url . '">Explore All »</a>');
        $this->view->titleLinkPosition = $this->_getParam('titleLinkPosition', 'top');
        $param['showChannel'] = $this->_getParam('featured', 0) ? 'featured' : '';
        $param['category_id'] = $this->_getParam('category_id');
        $param['subcategory_id'] = $this->_getParam('subcategory_id');

        switch ($param['popularType']) {
            case 'creation':
                $param['orderby'] = 'creation_date';
                break;
            case 'like':
                $param['orderby'] = 'like_count';
                break;
            case 'subscribe':
                $param['orderby'] = 'subscribe_count';
                break;
            case 'comment':
                $param['orderby'] = 'comment_count';
                break;
            case 'rating':
                $param['orderby'] = 'rating';
                break;
            case 'favourite':
                $param['orderby'] = 'favourite_count';
                break;
            case 'random':
                $param['orderby'] = 'random';
                break;
        }

        if(empty($sitevideoChannelList))
            return $this->setNoRender();
        
        $this->view->params = $param;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->channelBySettings($param);
        
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
        $channelSize['width'] = $this->view->channelWidth;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($channelSize, $this->view->channelWidth);
        $this->view->thumbnailType = $thumbnailType;
    }

}
