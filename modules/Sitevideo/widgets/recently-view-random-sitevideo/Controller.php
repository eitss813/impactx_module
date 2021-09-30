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
class Sitevideo_Widget_RecentlyViewRandomSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'video_view');

        if (!isset($params['viewFormat']))
            $params['viewFormat'] = $params['defaultViewType'];

        $this->view->viewFormat = str_replace("ZZZ", "_", $params['viewFormat']);
        $page = $this->_getParam('page', 1);
        $layouts_views = $params['viewType'] = $this->_getParam('viewType', array('video_view', 'grid_view', 'list_view'));
        foreach ($layouts_views as $key => $value)
            $layouts_views[$key] = str_replace("ZZZ", "_", $value);

        $this->view->viewType = $layouts_views;
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption');
        $this->view->videoViewCountPerPage = $params['videoItemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->gridViewCountPerPage = $params['gridItemCountPerPage'] = $this->_getParam('gridItemCountPerPage', 12);
        $this->view->listViewCountPerPage = $params['listItemCountPerPage'] = $this->_getParam('listItemCountPerPage', 12);
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->titleTruncationGridNVideoView = $params['titleTruncationGridNVideoView'] = $this->_getParam('titleTruncationGridNVideoView', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->showContent = $params['show_content'] = 2;
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/recently-view-random-sitevideo';

        if (empty($sitevideoVideosList))
            return $this->setNoRender();

        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $params['videoOption'] = array();
        }

        $this->view->isViewMoreButton = false;
        $this->view->showViewMore = $this->_getParam('showViewMore', false);
        $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
        $showTabArray = $params['ajaxTabs'] = $this->_getParam('ajaxTabs', array('most_recent', 'most_liked', 'most_viewed', 'most_commented', 'most_rated', 'most_favourites', 'random'));

        if ($showTabArray) {
            foreach ($showTabArray as $key => $value)
                $showTabArray[$key] = str_replace("ZZZ", "_", $value);
        } else {
            $showTabArray = array();
        }

        if (empty($this->view->viewType) || count($this->view->viewType) == 0) {
            $this->view->viewType = array($this->view->viewFormat);
        } else if (!in_array($this->view->viewFormat, $this->view->viewType)) {
            $this->view->viewFormat = $this->view->viewType[0];
        }

        $this->view->is_ajax_load = true;

        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;

            if (!$this->_getParam('detactLocation', 0) || $this->_getParam('contentpage', 1) > 1)
                $this->getElement()->removeDecorator('Title');

            $this->getElement()->removeDecorator('Container');
        } else {

            if ($this->_getParam('detactLocation', 0))
                $this->getElement()->removeDecorator('Title');

            $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
        }

        $this->view->is_ajax = $isAjax = $this->_getParam('is_ajax', 0);
        $this->view->tabCount = count($showTabArray);

        if (empty($this->view->tabCount)) {
            return $this->setNoRender();
        }

        $this->view->heading = "";
        if (count($showTabArray) == 1) {
            $element = $this->getElement();
            $widgetTitle = $this->view->heading = $element->getTitle();
            if (!empty($widgetTitle)) {
                $element->setTitle("");
            } else {
                $this->view->heading = "";
            }
        }
        $this->view->tabs = $showTabArray = $this->setTabsOrder($showTabArray);
        $paramsContentType = $this->_getParam('content_type', null);
        $this->view->content_type = $paramsContentType = $paramsContentType ? $paramsContentType : $showTabArray[0];

        if (!isset($params['category_id']))
            $params['category_id'] = 0;

        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;

        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;

        if (empty($params['category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('category_id');
            $params['subcategory_id'] = $this->_getParam('subcategory_id');
            $params['subsubcategory_id'] = $this->_getParam('subsubcategory_id');
        }

        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['width'] = $this->view->videoViewWidth;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($videoSize, $this->view->videoViewWidth);
        $this->view->videoViewThumbnailType = $params['videoViewThumbnailType'] = $thumbnailType;
        $videoSize['width'] = $this->view->gridViewWidth;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($videoSize, $this->view->gridViewWidth);
        $this->view->gridViewThumbnailType = $params['gridViewThumbnailType'] = $thumbnailType;
        $filter = "";
        $this->view->message = 'Nobody has created a video yet.';

        switch ($paramsContentType) {
            case 'most_recent' :
                $orderby = 'creation_date';
                break;
            case 'most_liked' :
                $orderby = 'like_count';
                $filter = 'like_count';
                $this->view->message = 'Nobody has liked a video yet.';
                break;
            case 'most_viewed' :
                $orderby = 'view_count';
                $filter = 'view_count';
                $this->view->message = 'Nobody has viewed a video yet.';
                break;
            case 'most_commented' :
                $orderby = 'comment_count';
                $filter = 'comment_count';
                $this->view->message = 'Nobody has commented on video yet.';
                break;
            case 'most_rated' :
                $orderby = 'rating';
                $filter = 'rating';
                $this->view->message = 'Nobody has rated on video yet.';
                break;
            case 'most_favourites' :
                $filter = 'favourite_count';
                $orderby = 'favourite_count';
                $this->view->message = 'Nobody has favourited a video yet.';
                break;
            case 'random' :
                $orderby = 'random';
                break;
            default :
                $orderby = 'creation_date';
        }

        $params['orderby'] = $orderby;
        $params['filter'] = $filter;
        $requestedAllParams = $this->_getAllParams();

        if (isset($requestedAllParams['hidden_video_category_id']) && !empty($requestedAllParams['hidden_video_category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_video_category_id');
            $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_video_subcategory_id');
            $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_video_subsubcategory_id');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params['videoType'] = $contentType = $request->getParam('videoType', null);
        if (empty($contentType)) {
            $params['videoType'] = $params['videoType'] = $this->_getParam('videoType', 'All');
        }
        $this->view->videoType = $params['videoType'];
        $this->view->params = $params;

        if (!$this->view->is_ajax_load)
            return;

        $paginatorVideoView = $this->view->paginatorVideoView = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $paginatorListView = $this->view->paginatorListView = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $paginatorGridView = $this->view->paginatorGridView = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $this->view->totalCount = $totalCount = $paginatorVideoView->getTotalItemCount();
        $videoViewCountPerPage = $params['videoItemCountPerPage'];
        $gridViewCountPerPage = $params['gridItemCountPerPage'];
        $listViewCountPerPage = $params['listItemCountPerPage'];
        $paginatorVideoView->setItemCountPerPage($videoViewCountPerPage);
        $paginatorListView->setItemCountPerPage($listViewCountPerPage);
        $paginatorGridView->setItemCountPerPage($gridViewCountPerPage);
        $paginatorVideoView->setCurrentPageNumber($page);
        $paginatorListView->setCurrentPageNumber($page);
        $paginatorGridView->setCurrentPageNumber($page);
    }

    public function setTabsOrder($tabs) {

        $tabsOrder['most_recent'] = $this->_getParam('recent_order', 1);
        $tabsOrder['most_liked'] = $this->_getParam('liked_order', 2);
        $tabsOrder['most_viewed'] = $this->_getParam('viewed_order', 3);
        $tabsOrder['most_commented'] = $this->_getParam('commented_order', 4);
        $tabsOrder['most_rated'] = $this->_getParam('rated_order', 5);
        $tabsOrder['most_favourites'] = $this->_getParam('favourites_order', 6);
        $tabsOrder['random'] = $this->_getParam('random_order', 7);
        $tempTabs = array();
        foreach ($tabs as $tab) {
            $order = $tabsOrder[$tab];
            if (isset($tempTabs[$order]))
                $order++;
            $tempTabs[$order] = $tab;
        }
        ksort($tempTabs);
        $orderTabs = array();
        $i = 0;
        foreach ($tempTabs as $tab)
            $orderTabs[$i++] = $tab;

        return $orderTabs;
    }

}
