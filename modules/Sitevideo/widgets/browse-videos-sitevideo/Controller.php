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
class Sitevideo_Widget_BrowseVideosSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $module = $request->getModuleName();
        $action = $request->getActionName();
        $controller = $request->getControllerName();
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'videoView');
        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];
        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = $this->_getParam('page', 1);

        if (!isset($params['location'])) {
            $params['location'] = "";
        }
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', array('videoView', 'gridView', 'listView'));
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->titleTruncationGridNVideoView = $params['titleTruncationGridNVideoView'] = $this->_getParam('titleTruncationGridNVideoView', 100);
        $this->view->id = $params['id'] = $this->_getParam('identity');
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0);
        }
        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $params['videoOption'] = array();
        }
        $this->view->defaultLocationDistance = 1000;
        $this->view->latitude = 0;
        $this->view->longitude = 0;
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $this->view->latitude = $params['latitude'] = $this->_getParam('latitude', 0);
            $this->view->longitude = $params['longitude'] = $this->_getParam('longitude', 0);
        }
        if (!isset($params['category_id']))
            $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;
        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;
        
        $this->view->category_id = $params['category_id'];
        $this->view->subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $params['subsubcategory_id'];
        
        if (empty($params['category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('category_id', $this->_getParam('category'));
            $params['subcategory_id'] = $this->_getParam('subcategory_id', $this->_getParam('subcategory_id'));
            $params['subsubcategory_id'] = $this->_getParam('subsubcategory_id', $this->_getParam('subsubcategory_id'));
        }

        //GET CATEGORYID AND SUBCATEGORYID
        $this->view->categoryName = '';
        if ($this->view->category_id) {
            $this->view->categoryName = $params['categoryname'] = Engine_Api::_()->getItem('sitevideo_video_category', $this->view->category_id)->category_name;
            if ($this->view->subcategory_id) {
                $this->view->subCategoryName = $params['subcategoryname'] = Engine_Api::_()->getItem('sitevideo_video_category', $this->view->subcategory_id)->category_name;
            }

            if ($this->view->subsubcategory_id) {
                $this->view->subsubCategoryName = $params['subsubcategoryname'] = Engine_Api::_()->getItem('sitevideo_video_category', $this->view->subsubcategory_id)->category_name;
            }
        }
        //FORM GENERATION
        $form = new Sitevideo_Form_Search_VideoSearch();
        if (!empty($params)) {
            $form->populate($params);
        }
        $this->view->formValues = $form->getValues();
        $params = array_merge($params, $form->getValues());

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
        $this->view->can_upload_video = $allow_upload_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
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
        $params['type'] = 'browse';
        $element = $this->getElement();
        $widgetTitle = $this->view->heading = $element->getTitle();

        if (!empty($widgetTitle)) {
            $element->setTitle("");
        } else {
            $this->view->heading = "";
        }
        $this->view->params = $params;
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/browse-videos-sitevideo';
        $this->view->message = 'Nobody has created a video yet.';
        if ((isset($params['search']) && !empty($params['search'])) || (isset($params['category_id']) && !empty($params['category_id'])) || (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) || (isset($params['tag_id']) && !empty($params['tag_id'])) || (isset($params['location']) && !empty($params['location'])))
            $this->view->message = 'Nobody has created a video with that criteria.';
        $this->view->isViewMoreButton = false;
        $this->view->showViewMore = true;
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params, $customFieldValues);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);
         $this->view->showTopBottomContent = 1; 
    if($module == 'siteadvsearch' && $controller == 'index' && $action == 'browse-page') {
      $this->view->showTopBottomContent = 0; 
    }
    }

}
