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
class Sitevideo_Widget_myWatchlatersSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;

        //ASSIGNING THE PARAMETER VALUES
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->watchlaterOrder = $params['watchlaterOrder'] = $this->_getParam('watchlaterOrder');
        $this->view->viewFormat = $params['viewFormat'] = $this->_getParam('viewFormat', 'videoView');
        $params['owner'] = Engine_Api::_()->user()->getViewer();
        $params['owner_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->videoViewThumbnailType = $params['videoViewThumbnailType'] = $this->_getParam('videoViewThumbnailType', 'thumb.normal');
        $this->view->gridViewThumbnailType = $params['gridViewThumbnailType'] = $this->_getParam('gridViewThumbnailType', 'thumb.normal');
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption');

        if (is_null($this->view->videoOption)) {
            $this->view->videoOption = $params['videoOption'] = array('watchlater');
        }

        if (!in_array('watchlater', $this->view->videoOption)) {
            $this->view->videoOption[] = 'watchlater';
        }

        $this->view->videoOptionWithOwner = array_merge($params['videoOption'], 'owner');
        $params['videoOption'] = $this->view->videoOptionWithOwner;
        $this->view->showEditDeleteOption = false;
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/my-watchlaters-sitevideo';
        $this->view->message = 'You do not have added any videos in watch later.';

        if (isset($params['search']) && !empty($params['search']))
            $this->view->message = 'No watch later videos found in search criteria.';

        $this->view->isViewMoreButton = true;
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->titleTruncationGridNVideoView = $params['titleTruncationGridNVideoView'] = $this->_getParam('titleTruncationGridNVideoView', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->id = $params['idenity'] = $this->_getParam('identity');
        $this->view->params = $params;
        $params['paginator'] = 1;
        //FIND THE WATCHLATE MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getWatchlaterPaginator($params);
        //FIND THE NO. OF WATCHLATER
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET NO. OF WATCHLATER SHOULD BE IN A PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE
        $paginator->setCurrentPageNumber($page);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalCount) / $params['itemCountPerPage']);
    }

}
