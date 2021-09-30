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
class Sitevideo_Widget_BestChannelsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $param = array();
        $this->view->category_id = $param['category_id'] = $this->_getParam('category_id');
        $this->view->subcategory_id = $param['subcategory_id'] = $this->_getParam('subcategory_id');
        $this->view->channelWidth = $param['channelWidth'] = $this->_getParam('channelWidth', 200);
        $this->view->channelHeight = $param['channelHeight'] = $this->_getParam('channelHeight', 200);
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->channelOption = $param['channelOption'] = $this->_getParam('channelOption');
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'creation_date');
        $this->view->showChannel = $param['showChannel'] = $this->_getParam('showChannel', 'recent');
        $this->view->buttonTitle = $this->_getParam('buttonTitle','Best Channels');
        $sitevideoBestChannel = Zend_Registry::isRegistered('sitevideoBestChannel') ? Zend_Registry::get('sitevideoBestChannel') : null;
        if(empty($sitevideoBestChannel))
            return $this->setNoRender();
        
        $this->view->showLink = $this->_getParam('showLink', 1);
        $this->view->orderby = '';
        if (empty($this->view->channelOption) || !is_array($this->view->channelOption)) {
            $this->view->channelOption = $param['channelOption'] = array();
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
            case 'subscribe':
                $this->view->orderby = $param['orderby'] = 'subscribe_count';
                break;
            case 'favourite':
                $this->view->orderby = $param['orderby'] = 'favourite_count';
                break;
            case 'random':
                $this->view->orderby = $param['orderby'] = 'random';
                break;
            default :
                $this->view->orderby = $param['orderby'] = 'creation_date';
        }
        $this->view->params = $param;
        // List List featured
        $this->view->channels = $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->channelBySettings($param);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator->setItemCountPerPage(10);
        $this->view->totalCount = $count = $paginator->getTotalItemCount();
        if($this->view->category_id)
        $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_channel_category', $this->view->category_id);
    }

}
