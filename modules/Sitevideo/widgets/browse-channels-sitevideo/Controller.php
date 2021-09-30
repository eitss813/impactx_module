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
class Sitevideo_Widget_BrowseChannelsSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'videoView');

        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = $this->_getParam('page', 1);
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', array('videoView', 'gridView', 'listView'));
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->channelOption = $params['channelOption'] = $this->_getParam('channelOption');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->titleTruncationGridNVideoView = $params['titleTruncationGridNVideoView'] = $this->_getParam('titleTruncationGridNVideoView', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $sitevideoBrowseChannel = Zend_Registry::isRegistered('sitevideoBrowseChannel') ? Zend_Registry::get('sitevideoBrowseChannel') : null;
        $this->view->id = $params['id'] = $this->_getParam('identity');
        if (empty($this->view->channelOption) || !is_array($this->view->channelOption)) {
            $this->view->channelOption = $params['channelOption'] = array();
        }
        if (empty($sitevideoBrowseChannel))
            return $this->setNoRender();
        if (!isset($params['category_id']))
            $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;
        if (empty($params['category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('category_id');
            $params['subcategory_id'] = $this->_getParam('subcategory_id');
        }
        //GET CATEGORYID AND SUBCATEGORYID
        $this->view->categoryName = '';
        if ($this->view->category_id) {
            $this->view->categoryName = $params['categoryname'] = Engine_Api::_()->getItem('sitevideo_channel_category', $this->view->category_id)->category_name;
            if ($this->view->subcategory_id) {
                $this->view->subCategoryName = $params['subcategoryname'] = Engine_Api::_()->getItem('sitevideo_channel_category', $this->view->subcategory_id)->category_name;
            }
        }
        //FORM GENERATION
        $form = new Sitevideo_Form_Search_Search();
        if (!empty($params)) {
            $form->populate($params);
        }
        $this->view->formValues = $form->getValues();
        $params = array_merge($params, $form->getValues());
        // FIND USERS' FRIENDS
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!empty($params['view_view']) && $params['view_view'] == 1) {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }
        $customFieldValues = array();
        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($params, $form->getFieldElements());
        $params['type'] = 'browse';
        $params['orderby'] = $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $orderby = $this->_getParam('orderby', 'creation_date');
            if ($orderby == 'creationDate')
                $params['orderby'] = 'creation_date';
            else
                $params['orderby'] = $orderby;
        }
        $viewer_id = $this->view->viewerId = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $this->view->can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'sitevideo_channel', 'create');
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

        $requestedAllParams = $this->_getAllParams();
        if (isset($requestedAllParams['hidden_category_id']) && !empty($requestedAllParams['hidden_category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
            $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
            $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
        }
        $element = $this->getElement();
        $widgetTitle = $this->view->heading = $element->getTitle();
        
        if (!empty($widgetTitle)) {
            $element->setTitle("");
        } else {
            $this->view->heading = "";
        }

        $this->view->params = $params;
        $this->view->isViewMoreButton = false;
        $this->view->showViewMore = true;
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/browse-channels-sitevideo';
        $this->view->message = 'Nobody has created a channel yet.';
        if ((isset($params['search']) && !empty($params['search'])) || (isset($params['category_id']) && !empty($params['category_id'])) || (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) || (isset($params['tag_id']) && !empty($params['tag_id'])))
            $this->view->message = 'Nobody has created a channel with that criteria.';
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelPaginator($params, $customFieldValues);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);
    }

}
