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
class Sitevideo_Widget_MyRatedchannelsSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
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
        $this->view->order = $params['order'] = $this->_getParam('order');
        $params['owner'] = Engine_Api::_()->user()->getViewer();
        $params['owner_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->channelOption = $params['channelOption'] = $this->_getParam('channelOption');
        $this->view->videoViewWidth = $params['videoViewWidth'] = $this->_getParam('videoViewWidth', 150);
        $this->view->videoViewHeight = $params['videoViewHeight'] = $this->_getParam('videoViewHeight', 150);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->videoViewThumbnailType = $params['videoViewThumbnailType'] = $this->_getParam('videoViewThumbnailType', 'thumb.normal');
        $this->view->gridViewThumbnailType = $params['gridViewThumbnailType'] = $this->_getParam('gridViewThumbnailType', 'thumb.normal');
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/my-ratedchannels-sitevideo';
        $this->view->message = "You do not have any rated channels.";
        
        if (isset($params['search']) && !empty($params['search']))
            $this->view->message = 'No rated channels found in search criteria.';
        
        $this->view->showEditDeleteOption = false;
        $this->view->id = $params['idenity'] = $this->_getParam('identity');
        $this->view->isViewMoreButton = true;
        
        if (is_null($this->view->channelOption)) {
            $this->view->channelOption = array();
        }
        
        $this->view->channelOption[] = 'owner';
        $params['channelOption'] = $this->view->channelOption;
        $this->view->params = $params;
        $params['paginator'] = 1;
        //FIND THE PLAYLIST MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getRatedChannelPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET THE NO. OF RECORDS PER PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
    }

}
