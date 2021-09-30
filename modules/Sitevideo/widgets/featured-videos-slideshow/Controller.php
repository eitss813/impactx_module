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
class Sitevideo_Widget_FeaturedVideosSlideshowController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        //GET SLIDESHOW HIGHT
        $this->view->height = $params['slideshow_height'] = $this->_getParam('slideshow_height', 350);
        //GET SLIDESHOW DELAY
        $this->view->delay = $params['delay'] = $this->_getParam('delay', 3500);
        // GET CAPTION TRUNCATION LIMIT
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 200);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->taglineTruncation = $params['taglineTruncation'] = $this->_getParam('taglineTruncation', 200);
        $this->view->showTagline1 = $params['showTagline1'] = $this->_getParam('showTagline1', '');
        $this->view->showTagline2 = $params['showTagline2'] = $this->_getParam('showTagline2', '');
        $this->view->showTaglineDesc = $params['showTaglineDesc'] = $this->_getParam('showTaglineDesc', '');
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption', array('title', 'watchlater'));
        $this->view->fullWidth = $params['fullWidth'] = $this->_getParam('fullWidth', 1);
        $params['limit'] = $this->_getParam('slidesLimit', 10);
        $params['category_id'] = $this->_getParam('category_id');
        $params['subcategory_id'] = $this->_getParam('subcategory_id');
        $params['subsubcategory_id'] = $this->_getParam('subsubcategory_id');
        $params['popularType'] = $this->_getParam('popularType', 'random');
        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        $this->view->showNavigationButton = $params['showNavigationButton'] = $this->_getParam('showNavigationButton', 1);
        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $params['videoOption'] = array();
        }
        switch ($params['popularType']) {
            case 'view':
                $params['orderby'] = 'view_count';
                break;
            case 'comment':
                $params['orderby'] = 'comment_count';
                break;
            case 'like':
                $params['orderby'] = 'like_count';
                break;
            case 'rating':
                $params['orderby'] = 'rating';
                break;
            case 'creation':
                $params['orderby'] = 'creation_date';
                break;
            case 'modified':
                $params['orderby'] = 'modified_date';
                break;
            case 'random':
                $params['orderby'] = 'random';
                break;
        }
        $params['featured'] = 1;
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params['videoType'] = $contentType = $request->getParam('videoType', null);
        if (empty($contentType)) {
            $params['videoType'] = $params['videoType'] = $this->_getParam('videoType', 'All');
        }
        $this->view->videoType = $params['videoType'];
        $requestedAllParams = $this->_getAllParams();
        if (isset($requestedAllParams['hidden_video_category_id']) && !empty($requestedAllParams['hidden_video_category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_video_category_id');
            $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_video_subcategory_id');
            $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_video_subsubcategory_id');
        }
        $this->view->params = $params;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->videoBySettings($params);
        $paginator->setItemCountPerPage($params['limit']);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->totalCount = $paginator->getTotalItemCount();

        // Do not render if nothing to show
        if (($paginator->getTotalItemCount() <= 0)) {
            return $this->setNoRender();
        }

        $this->view->storage = Engine_Api::_()->storage();
    }

}
