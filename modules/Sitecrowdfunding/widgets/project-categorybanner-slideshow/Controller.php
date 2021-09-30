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
class Sitecrowdfunding_Widget_ProjectCategorybannerSlideshowController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        $this->view->backgroupImage = $this->_getParam('logo');
        $this->view->backgroundImageHeight = $this->_getParam('height', 555);
        $this->view->categoryImageHeight = $this->_getParam('categoryHeight', 400);
        $this->view->fullWidth = $this->_getParam('fullWidth', 1);
        $category_id = $params['category_id'] = $this->_getParam('category_id', null);
        $this->view->showExporeButton = $this->_getParam('showExplore');
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 100);
        $this->view->taglineTruncation = $this->_getParam('taglineTruncation', 200);
        $sitecrowdfundingCategoryBannerSlideshow = Zend_Registry::isRegistered('sitecrowdfundingCategoryBannerSlideshow') ? Zend_Registry::get('sitecrowdfundingCategoryBannerSlideshow') : null;
        // SET NO RENDER & GET CATEGORY ITEM
        if (empty($category_id))
            return $this->setNoRender();
        $categories = $params["category_id"];
        if (empty($sitecrowdfundingCategoryBannerSlideshow))
            return $this->setNoRender();
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategoriesPaginator($categories);

        $this->view->totalCount = $totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($this->view->totalCount);

        // Do not render if nothing to show
        if (($totalCount <= 0)) {
            return $this->setNoRender();
        }
        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
    }

}
