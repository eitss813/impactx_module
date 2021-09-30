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
class Sitecrowdfunding_Widget_ProjectCategoryBannerController extends Engine_Content_Widget_Abstract {

    public function indexAction() {


        $this->view->backgroupImage = $this->_getParam('logo');
        $this->view->backgroundImageHeight = $this->_getParam('height', 555);
        $this->view->categoryImageHeight = $this->_getParam('categoryHeight', 400);
        $this->view->fullWidth = $this->_getParam('fullWidth', 1);
        $this->view->showExporeButton = $this->_getParam('showExplore');
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 100);
        $this->view->taglineTruncation = $this->_getParam('taglineTruncation', 200);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $sitecrowdfundingCategoryBanner = Zend_Registry::isRegistered('sitecrowdfundingCategoryBanner') ? Zend_Registry::get('sitecrowdfundingCategoryBanner') : null;
        $category_id = $request->getParam('subsubcategory_id', null);
        if (empty($category_id)) {
            $category_id = $request->getParam('subcategory_id', null);
            if (empty($category_id)) {
                $category_id = $request->getParam('category_id', null);
            }
        }
        if (empty($sitecrowdfundingCategoryBanner))
            return $this->setNoRender();
        //SET NO RENDER
        if (empty($category_id))
            return $this->setNoRender();

        //GET CATEGORY ITEM
        $this->view->category = $category = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategory($category_id);

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
    }

}
