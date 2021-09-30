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
class Sitevideo_Widget_MyChannelsSitevideoController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }
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
        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];

        $page = $this->_getParam('page', 1);
        $sitevideoMyChannelVideo = Zend_Registry::isRegistered('sitevideoMyChannelVideo') ? Zend_Registry::get('sitevideoMyChannelVideo') : null;
        $this->view->topNavigationLink = $params['topNavigationLink'] = $this->_getParam('topNavigationLink', array('video', 'channel', 'createVideo', 'createChannel'));
        $this->view->channelNavigationLink = $params['channelNavigationLink'] = $this->_getParam('channelNavigationLink');
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType');
        $this->view->searchButton = $params['searchButton'] = $this->_getParam('searchButton', 1);
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->channelOption = $params['channelOption'] = $this->_getParam('channelOption');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);
        $this->view->id = $params['id'] = $this->_getParam('identity');
        if (empty($this->view->topNavigationLink) || !is_array($this->view->topNavigationLink))
            $this->view->topNavigationLink = $params['topNavigationLink'] = array();

        if (empty($this->view->channelOption) || !is_array($this->view->channelOption))
            $this->view->channelOption = $params['channelOption'] = array();

        if (empty($this->view->channelNavigationLink) || !is_array($this->view->channelNavigationLink))
            $this->view->channelNavigationLink = $params['channelNavigationLink'] = array();

        if (count($this->view->channelNavigationLink) <= 0)
            return $this->setNoRender();
        
        if(empty($sitevideoMyChannelVideo))
            return $this->setNoRender();

        if (empty($this->view->viewType) || !is_array($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array();
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
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->tab = $request->getParam('tab', null);
        $this->view->canUploadVideo = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create');
        $this->view->canCreateChannel = Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'create');
        $params['owner'] = $viewer->getType();
        $params['owner_id'] = $viewer->getIdentity();
        $this->view->params = $params;
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->_childCount = $paginator->getTotalItemCount();
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
