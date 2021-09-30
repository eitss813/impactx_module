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
class Sitevideo_Widget_VideosSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->showEditDeleteOption = true;
        if (Engine_Api::_()->core()->hasSubject('user') && Engine_Api::_()->core()->getSubject('user')) {
            $viewer = Engine_Api::_()->core()->getSubject('user');
            $this->view->showEditDeleteOption = false;
        } else
            $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        //FIND THE PAGE NUMBER
        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;
        // ASSIGNING THE PARAMETER
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        $this->view->viewFormat = $params['viewFormat'] = $this->_getParam('viewFormat', 'videoView');
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->orderby = $params['orderby'] = $this->_getParam('orderby');
        $params['owner'] = $viewer;
        $params['owner_id'] = $viewer->getIdentity();
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption');
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->videoViewThumbnailType = $params['videoViewThumbnailType'] = $this->_getParam('videoViewThumbnailType', 'thumb.normal');
        $this->view->gridViewThumbnailType = $params['gridViewThumbnailType'] = $this->_getParam('gridViewThumbnailType', 'thumb.normal');
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->titleTruncationGridNVideoView = $params['titleTruncationGridNVideoView'] = $this->_getParam('titleTruncationGridNVideoView', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/videos-sitevideo';
        $this->view->message = 'You do not have any videos.';

        if(empty($sitevideoVideosList))
            return $this->setNoRender();
        
        if (isset($params['search']) && !empty($params['search']))
            $this->view->message = 'No videos found in search criteria.';

        $this->view->isViewMoreButton = true;
        $this->view->id = $params['idenity'] = $this->_getParam('identity');

        if (is_null($this->view->videoOption)) {
            $this->view->videoOption = array();
            $params['videoOption'] = array();
        }

        $viewer_id = $this->view->viewerId = $viewer->getIdentity();
        $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        $this->view->can_upload_video = $allow_upload_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        $this->view->params = $params;
        $params['paginator'] = 1;
        //FIND THE VIDEO MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET THE NO. OF RECORDS PER PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
    }

}
