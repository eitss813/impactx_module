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
class Sitevideo_Widget_ChannelsSlideshowController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $this->view->categoryId = $params['category_id'] = $this->_getParam('category_id');
        $params['subcategory_id'] = $this->_getParam('subcategory_id');
        $this->view->channelOption = $params['channelOption'] = $this->_getParam('channelOption');
        $this->view->channelCount = $params['channelCount'] = $this->_getParam('channelCount');
        $this->view->videoCount = $params['videoCount'] = $this->_getParam('videoCount');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation');
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation');
        $this->view->channelOrderby = $params['channelOrderby'] = $this->_getParam('channelOrderby', 'random');
        $this->view->videoOrderby = $params['videoOrderby'] = $this->_getParam('videoOrderby', 'random');
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage');
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);
        $this->view->is_ajax_load = $params['is_ajax_load'] = $this->_getParam('is_ajax_load', true);
        $sitevideoChannelSlideshow = Zend_Registry::isRegistered('sitevideoChannelSlideshow') ? Zend_Registry::get('sitevideoChannelSlideshow') : null;
        $this->view->showLink = $params['showLink'] = $this->_getParam('showLink', 1);
        $page = $params['page'] = $this->_getParam('page', 1);
        $categorieIds = $this->_getParam('categorieIds');
        if (empty($sitevideoChannelSlideshow))
            return $this->setNoRender();

        if ($categorieIds) {
            $this->view->categorieIds = Zend_Json::decode($categorieIds);
        } else {
            $this->view->categorieIds = array();
        }

        if (empty($this->view->channelOption) || !is_array($this->view->channelOption)) {
            $this->view->channelOption = $params['channelOption'] = array();
        }

        if ($this->view->channelOrderby == 'liked')
            $this->view->channelOrderby = 'like_count';

        if ($this->view->videoOrderby == 'liked')
            $this->view->videoOrderby = 'like_count';

        $this->view->params = $params;
        $fetchColumns = array('category_id' => 'category_id', 'category_name' => 'category_name', 'subcat_dependency' => 'subcat_dependency', 'cat_dependency');
        $filterParams = array
            (
            'fetchColumns' => $fetchColumns,
            'cat_depandancy' => 0,
            'havingChannels' => true
        );
        if (!empty($this->view->categoryId)) {
            $filterParams['category_id'] = $this->view->categoryId;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $this->view->viewerId = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $this->view->can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'sitevideo_channel', 'create');
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getCategoriesPaginator($filterParams);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);
    }

}
