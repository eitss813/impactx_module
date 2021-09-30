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
class Sitevideo_Widget_MyVideosSitevideoController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        if (Engine_Api::_()->core()->hasSubject('user') && Engine_Api::_()->core()->getSubject('user')) {
            $viewer = Engine_Api::_()->core()->getSubject('user');
        } else
            $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'videoView');
        $this->view->playlistDefaultViewType = $params['playlistDefaultViewType'] = $this->_getParam('playlistDefaultViewType', 'gridView');

        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];

        $page = $this->_getParam('page', 1);
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        $this->view->topNavigationLink = $params['topNavigationLink'] = $this->_getParam('topNavigationLink', array('video', 'channel', 'createVideo', 'createChannel'));
        $this->view->videoNavigationLink = $params['videoNavigationLink'] = $this->_getParam('videoNavigationLink', array('video', 'playlist', 'watchlater', 'liked', 'favourite', 'rated'));
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', array('videoView', 'gridView', 'listView'));
        $this->view->searchButton = $params['searchButton'] = $this->_getParam('searchButton', 1);
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->playlistGridViewWidth = $params['playlistGridViewWidth'] = $this->_getParam('playlistGridViewWidth', 150);
        $this->view->playlistGridViewHeight = $params['playlistGridViewHeight'] = $this->_getParam('playlistGridViewHeight', 150);
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption', array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'location', 'facebook', 'twitter', 'linkedin', 'googleplus'));
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->titleTruncationGridNVideoView = $params['titleTruncationGridNVideoView'] = $this->_getParam('titleTruncationGridNVideoView', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->orderby = $params['orderby'] = $this->_getParam('orderby');
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];

        if(empty($sitevideoVideosList))
            return $this->setNoRender();
        
        if (isset($params['is_ajax']))
            $this->view->is_ajax = $params['is_ajax'];
        else
            $this->view->is_ajax = $params['is_ajax'] = false;

        if (empty($this->view->topNavigationLink) || !is_array($this->view->topNavigationLink))
            $this->view->topNavigationLink = $params['topNavigationLink'] = array();

        if (empty($this->view->videoOption) || !is_array($this->view->videoOption))
            $this->view->videoOption = $params['videoOption'] = array();

        if (empty($this->view->videoNavigationLink) || !is_array($this->view->videoNavigationLink))
            $this->view->videoNavigationLink = $params['videoNavigationLink'] = array();

        if (count($this->view->videoNavigationLink) <= 0)
            return $this->setNoRender();

        if (empty($this->view->viewType) || !is_array($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array();

        $this->view->tab = $request->getParam('tab', null);
        $this->view->canUploadVideo = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create');
        $this->view->canCreateChannel = Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'create');
        $this->view->playlistViewType = $params['playlistViewType'] = $this->_getParam('playlistViewType', array('gridView', 'listView'));
        $this->view->videoShowLinkCount = $params['videoShowLinkCount'] = $this->_getParam('videoShowLinkCount', 3);
        $this->view->playlistOrder = $params['playlistOrder'] = $this->_getParam('playlistOrder', 'creation_date');
        $this->view->playlistVideoOrder = $params['playlistVideoOrder'] = $this->_getParam('playlistVideoOrder', 'creation_date');
        $this->view->showPlayAllOption = $params['showPlayAllOption'] = $this->_getParam('showPlayAllOption', 1);
        $this->view->watchlaterOrder = $params['watchlaterOrder'] = $this->_getParam('watchlaterOrder', 'creation_date');
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/my-videos-sitevideo';
        $this->view->message = 'You do not have any videos.';

        if (isset($params['search']) && !empty($params['search']))
            $this->view->message = 'No videos found in search criteria.';
        
        if(empty($this->view->playlistViewType))
            $this->view->playlistViewType = array();
        
        $this->view->isViewMoreButton = true;
        $this->view->showEditDeleteOption = true;
        $this->view->id = $params['id'] = $this->_getParam('identity');
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
        $params['owner'] = $viewer->getType();
        $params['owner_id'] = $viewer->getIdentity();
        $this->view->params = $params;
        //FIND THE VIDEO MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->_childCount = $paginator->getTotalItemCount();
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
