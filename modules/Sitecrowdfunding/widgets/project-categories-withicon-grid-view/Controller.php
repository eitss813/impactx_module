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
class Sitecrowdfunding_Widget_ProjectCategoriesWithiconGridViewController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->showAllCategories = $showAllCategories = $this->_getParam('showAllCategories', 1);
        $this->view->columnWidth = $this->_getParam('columnWidth', 268);
        $this->view->columnHeight = $this->_getParam('columnHeight', 260);
        $orderBy = $this->_getParam('orderBy', 'cat_order');
        $this->view->storage = Engine_Api::_()->storage();
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $this->view->category_id = $category_id = $request->getParam('category_id');
        $sitecrowdfundingCategoryWithouticon = Zend_Registry::isRegistered('sitecrowdfundingCategoryWithouticon') ? Zend_Registry::get('sitecrowdfundingCategoryWithouticon') : null;

        // GET ALL CATEGORIES
        if (empty($category_id))
            $categories = $tableCategory->getCategories(array(), null, 0, 0, 1, 0, 'category_name');
        else
            $categories = $tableCategory->getSubcategories($category_id);

        if (count($categories) == 0)
            return $this->setNoRender();

        if (empty($sitecrowdfundingCategoryWithouticon))
            return $this->setNoRender();
        $categoryParams = array();
        $projectCount = 0;
        foreach ($categories as $category) {

            if (empty($showAllCategories)) {
                $count = $tableProject->getProjectsCount($category->category_id, 'category_id');
                if (empty($count))
                    continue;
            }
            $tempCategoryParams['category_id'] = $category->getIdentity();
            $tempCategoryParams['category_name'] = $category->category_name;
            $tempCategoryParams['category_slug'] = $category->getCategorySlug();
            $tempCategoryParams['photo_id'] = $category->photo_id;
            $tempCategoryParams['title'] = $category->getTitle();
            $tempCategoryParams['order'] = $category->cat_order;
            $tempCategoryParams['file_id'] = $category->file_id;
            $tempCategoryParams['font_icon'] = $category->font_icon;
            $tempCategoryParams['projects_count'] = $count;
            $categoryParams[] = $tempCategoryParams;
        }
        $this->view->categoryParams = $categoryParams;
    }

}
