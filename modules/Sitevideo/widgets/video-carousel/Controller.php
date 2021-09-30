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
class Sitevideo_Widget_VideoCarouselController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $param = array();
        $this->view->category_id = $param['category_id'] = $this->_getParam('category_id');
        $this->view->subcategory_id = $param['subcategory_id'] = $this->_getParam('subcategory_id');
        $this->view->showPagination = $param['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->interval = $param['interval'] = $this->_getParam('interval', 3500);
        $this->view->videoWidth = $param['videoWidth'] = $this->_getParam('videoWidth', 200);
        $this->view->videoHeight = $param['videoHeight'] = $this->_getParam('videoHeight', 200);
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->videoOption = $param['videoOption'] = $this->_getParam('videoOption');
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'creation_date');
        $this->view->rowLimit = $param['rowLimit'] = $this->_getParam('itemCount', 7);
        $this->view->limit = $param['limit'] = $this->_getParam('itemCountPerPage', 50);
        $this->view->showVideo = $param['showVideo'] = $this->_getParam('showVideo');
        $this->view->showLink = $this->_getParam('showLink', 1);
        $this->view->orderby = '';
        
        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $param['videoOption'] = array();
        }
        
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
            case 'view':
                $this->view->orderby = $param['orderby'] = 'view_count';
                break;
            case 'random':
                $this->view->orderby = $param['orderby'] = 'random';
                break;
            default :
                $this->view->orderby = $param['orderby'] = 'creation_date';
        }
        
        $this->view->params = $param;
        $element = $this->getElement();
        $widgetTitle = $element->getTitle();
        if (empty($widgetTitle))
            $widgetTitle = "";
        $link = "";
        if (empty($this->view->category_id))
        {
            $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_video_category', $this->view->category_id);
            if($this->view->category)
            $link = $this->view->htmlLink($this->view->category->getHref(), " + " . $this->view->translate('See all ').$this->view->category->getTitle()." videos");
        }
        else
        {
            $url = $this->view->url(array_merge(array('action' => 'browse'), $param) , 'sitevideo_video_general', true);
            $link = $this->view->htmlLink($url, " + " . $this->view->translate('See all videos'));
        }
        
        if($this->view->showLink==1)
            $element->setTitle(sprintf($widgetTitle . " " . $link));
        
        // List List featured
        $this->view->videos = $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->videoBySettings($param);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        
        $paginator->setItemCountPerPage($param['limit']);
        $this->view->totalCount = $count = $paginator->getTotalItemCount();
        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['width'] = $this->view->videoWidth;
        $this->view->thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($videoSize, $this->view->videoWidth);
    }

}
