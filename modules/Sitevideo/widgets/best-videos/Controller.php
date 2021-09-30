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
class Sitevideo_Widget_BestVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $param = array();
        $this->view->category_id = $param['category_id'] = $this->_getParam('category_id');
        $this->view->subcategory_id = $param['subcategory_id'] = $this->_getParam('subcategory_id');
        $this->view->videoWidth = $param['videoWidth'] = $this->_getParam('videoWidth', 200);
        $this->view->videoHeight = $param['videoHeight'] = $this->_getParam('videoHeight', 200);
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->videoOption = $param['videoOption'] = $this->_getParam('videoOption');
        $sitevideoBestVideo = Zend_Registry::isRegistered('sitevideoBestVideo') ? Zend_Registry::get('sitevideoBestVideo') : null;
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'creation_date');
        $this->view->showVideo = $param['showVideo'] = $this->_getParam('showVideo');
        $this->view->showLink = $this->_getParam('showLink', 1);
        $this->view->orderby = '';
        $this->view->buttonTitle = $this->_getParam('buttonTitle','Best Videos');
        if(empty($sitevideoBestVideo))
            return $this->setNoRender();
            
        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $param['videoOption'] = array();
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
            case 'view':
                $this->view->orderby = $param['orderby'] = 'view_count';
                break;
            case 'random':
                $this->view->orderby = $param['orderby'] = 'random';
                break;
            default :
                $this->view->orderby = $param['orderby'] = 'creation_date';
        }
        $this->view->params = $param;
        // List List featured
        $this->view->videos = $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->videoBySettings($param);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator->setItemCountPerPage(10);
        $this->view->totalCount = $count = $paginator->getTotalItemCount();
        
        if($this->view->category_id)
        $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_video_category', $this->view->category_id);
    }

}
