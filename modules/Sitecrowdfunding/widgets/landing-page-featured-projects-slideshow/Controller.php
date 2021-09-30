<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_LandingPageFeaturedProjectsSlideshowController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params['projectType'] = $contentType = $request->getParam('projectType', null);
        if (empty($contentType)) {
            $params['projectType'] = $this->_getParam('projectType', 'All');
        }
        $this->view->projectType = $params['projectType'];
        // //GET SLIDESHOW HIGHT
        $this->view->height = $params['height'] = $this->_getParam('height', 350);
        //GET SLIDESHOW DELAY
        $this->view->delay = $params['delay'] = $this->_getParam('delay', 3500);
        // GET CAPTION TRUNCATION LIMIT
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 50);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 100);

        $this->view->fullWidth = $params['fullWidth'] = $this->_getParam('fullWidth', 1);
        $params['limit'] = $this->_getParam('slidesLimit', 10);
        $params['category_id'] = $this->_getParam('category_id');
        $params['subcategory_id'] = $this->_getParam('subcategory_id');
        $params['subsubcategory_id'] = $this->_getParam('subsubcategory_id');
        $params['popularType'] = $this->_getParam('popularType', 'random');
        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        $this->view->showNavigationButton = $params['showNavigationButton'] = $this->_getParam('showNavigationButton', 1);

        switch ($params['popularType']) {
            case 'comment':
                $params['orderby'] = 'comment_count';
                break;
            case 'like':
                $params['orderby'] = 'like_count';
                break;
            case 'start_date':
                $params['orderby'] = 'start_date';
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
        $sitecrowdfundingFeaturedSlideshow = Zend_Registry::isRegistered('sitecrowdfundingFeaturedSlideshow') ? Zend_Registry::get('sitecrowdfundingFeaturedSlideshow') : null;
        $this->view->params = $params;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);
        $paginator->setItemCountPerPage($params['limit']);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->totalCount = $paginator->getTotalItemCount();
        if (empty($sitecrowdfundingFeaturedSlideshow))
            return $this->setNoRender();
        // Do not render if nothing to show
        if (($paginator->getTotalItemCount() <= 0)) {
            return $this->setNoRender();
        }

        $this->view->storage = Engine_Api::_()->storage();
    }

}
