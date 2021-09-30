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
class Sitevideo_Widget_ChannelCarouselController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $param = array();
        $this->view->category_id = $param['category_id'] = $this->_getParam('category_id');
        $this->view->subcategory_id = $param['subcategory_id'] = $this->_getParam('subcategory_id');
        $this->view->showPagination = $param['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->interval = $param['interval'] = $this->_getParam('interval', 3500);
        $this->view->channelWidth = $param['channelWidth'] = $this->_getParam('channelWidth', 200);
        $this->view->channelHeight = $param['channelHeight'] = $this->_getParam('channelHeight', 200);
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->channelOption = $param['channelOption'] = $this->_getParam('channelOption');
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'creation_date');
        $sitevideoChannelCarousel = Zend_Registry::isRegistered('sitevideoChannelCarousel') ? Zend_Registry::get('sitevideoChannelCarousel') : null;
        $this->view->rowLimit = $param['rowLimit'] = $this->_getParam('itemCount', 7);
        $this->view->limit = $param['limit'] = $this->_getParam('itemCountPerPage', 50);
        $this->view->showChannel = $param['showChannel'] = $this->_getParam('showChannel', 'recent');
        $this->view->showLink = $this->_getParam('showLink', 1);
        $this->view->orderby = '';
        switch ($param['popularType']) {
            case 'comment':
                $this->view->orderby = $param['orderby'] = 'comment_count';
                break;
            case 'like':
                $this->view->orderby = $param['orderby'] = 'like_count';
                break;
            case 'rating':
                $this->view->orderby = $param['orderby'] = 'rating';
                break;
            case 'subscribe':
                $this->view->orderby = $param['orderby'] = 'subscribe_count';
                break;
            case 'favourite':
                $this->view->orderby = $param['orderby'] = 'favourite_count';
                break;
            case 'random':
                $this->view->orderby = $param['orderby'] = 'random';
                break;
            default :
                $this->view->orderby = $param['orderby'] = 'creation_date';
        }
        if (empty($sitevideoChannelCarousel)) {
            return $this->setNoRender();
        }
        if (empty($this->view->channelOption) || !is_array($this->view->channelOption)) {
            $this->view->channelOption = $param['channelOption'] = array();
        }
        $this->view->params = $param;
        $element = $this->getElement();
        $widgetTitle = $element->getTitle();
        if (empty($widgetTitle))
            $widgetTitle = "";
        if ($this->view->category_id) {
            $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_channel_category', $this->view->category_id);
            if($category)
            $link = $this->view->htmlLink($this->view->category->getHref(), " + " . $this->view->translate('See all ') . $this->view->category->getTitle()." channels");
        } else {
            $link = $this->view->htmlLink(array('route' => 'sitevideo_general', 'action' => 'browse'), " + " . $this->view->translate('See all channels'));
        }
        if ($this->view->showLink == 1)
            $element->setTitle(sprintf($widgetTitle . " " . $link));
        // List List featured
        $this->view->channels = $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->channelBySettings($param);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator->setItemCountPerPage($param['limit']);
        $this->view->totalCount = $count = $paginator->getTotalItemCount();
        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.width', 1600);
        $videoSize['width'] = $this->view->channelWidth;
        $this->view->thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($videoSize, $this->view->channelWidth);
    }

}
