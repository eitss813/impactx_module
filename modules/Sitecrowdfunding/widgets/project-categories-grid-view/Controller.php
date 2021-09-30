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
class Sitecrowdfunding_Widget_ProjectCategoriesGridViewController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $this->view->showSubCategoryCount = $showSubCategoryCount = $this->_getParam('subCategoriesCount', 5);
        $this->view->count = $count = $this->_getParam('showProjectCount', 0);
        $this->view->columnWidth = $this->_getParam('columnWidth', 268);
        $this->view->columnHeight = $this->_getParam('columnHeight', 260);
        $this->view->categoriesCount = $this->_getParam('categoriesCount', 5);
        $orderBy = $this->_getParam('orderBy', 'cat_order');
        $this->view->storage = Engine_Api::_()->storage();
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $this->view->category_id = $category_id = $request->getParam('category_id');
        if ($category_id)
            $this->view->category_slug = $category_slug = $tableCategory->getCategory($category_id)->getCategorySlug();
        $sitecrowdfundingCategoriesGridView = Zend_Registry::isRegistered('sitecrowdfundingCategoriesGridView') ? Zend_Registry::get('sitecrowdfundingCategoriesGridView') : null;


        $this->view->subcategoryExist = false;
        // GET ALL CATEGORIES
        if (empty($category_id)) {
            $categories = $tableCategory->getCategories(array(), null, 0, 0, 1);
        } else {
            $categories = $tableCategory->getSubcategories($category_id);
            $this->view->subcategoryExist = true;
        }

        if (count($categories) == 0)
            return $this->setNoRender();

        $categoryParams = array();
        if (empty($sitecrowdfundingCategoriesGridView))
            return $this->setNoRender();
        foreach ($categories as $category) {

            $subcategory_info2 = $tableCategory->getSubcategories($category->category_id);
            $SubCategoryArray = $tempSubCategoryArray = array();

            if (!empty($subcategory_info2)) {
                $tempFlag = 0;
                foreach ($subcategory_info2 as $subCategory) {
                    if ($tempFlag == $showSubCategoryCount)
                        break;
                    $tempFlag++;
                    $tempSubCategoryArray['sub_category_id'] = $subCategory->getIdentity();
                    $tempSubCategoryArray['sub_category_name'] = $subCategory->category_name;
                    $tempSubCategoryArray['category_slug'] = $subCategory->getCategorySlug();
                    $tempSubCategoryArray['photo_id'] = $category->photo_id;
                    $tempSubCategoryArray['title'] = $subCategory->getTitle();
                    $tempSubCategoryArray['order'] = $subCategory->cat_order;
                    $tempSubCategoryArray['count'] = !empty($count) ? $tableProject->getProjectsCount($subCategory->category_id, 'subcategory_id', 1) : false;
                    $SubCategoryArray[] = $tempSubCategoryArray;
                }
            }

            $tempCategoryParams['category_id'] = $category->getIdentity();
            $tempCategoryParams['category_name'] = $category->category_name;
            $tempCategoryParams['category_slug'] = $category->getCategorySlug();
            $tempCategoryParams['photo_id'] = $category->photo_id;
            $tempCategoryParams['title'] = $category->getTitle();
            $tempCategoryParams['order'] = $category->cat_order;
            $tempCategoryParams['subCategories'] = $SubCategoryArray;
            $tempCategoryParams['count'] = !empty($count) ? $tableProject->getProjectsCount($category->category_id, 'category_id', 1) : 'false';
            $categoryParams[] = $tempCategoryParams;
        }
        $this->view->categoryParams = $categoryParams;
    }

}
