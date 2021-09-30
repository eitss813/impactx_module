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
class Sitevideo_Widget_CategoriesGridViewController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1))
            return $this->setNoRender();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $showSubCategoryCount = $this->_getParam('showSubCategoriesCount', 5);
        $this->view->count = $count = $this->_getParam('showCount', 0);
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $tableSitevideo = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $this->view->columnWidth = $this->_getParam('columnWidth', 268);
        $this->view->columnHeight = $this->_getParam('columnHeight', 260);
        $showAllCategories = $this->_getParam('showAllCategories', 0);
        $orderBy = $this->_getParam('orderBy', 'cat_order');
        $this->view->storage = Engine_Api::_()->storage();
        $havingChannels = !$showAllCategories;
        $this->view->category_id = $category_id = $request->getParam('category_id');
        $sitevideoCategoryView = Zend_Registry::isRegistered('sitevideoCategoryView') ? Zend_Registry::get('sitevideoCategoryView') : null;
        // GET ALL CATEGORIES
        if (empty($category_id))
            $categories = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'cat_dependency', 'category_name', 'category_slug', 'cat_order', 'video_id'), 'sponsored' => 0, 'cat_depandancy' => 1, 'havingChannels' => $havingChannels, 'orderBy' => $orderBy));
        else {
            $categories = $tableCategory->getSubcategories(array('category_id' => $category_id, 'havingChannels' => $havingChannels, 'fetchColumns' => array('category_id', 'cat_dependency', 'category_name', 'cat_order', 'video_id')));
        }

        if(empty($sitevideoCategoryView)) 
            return $this->setNoRender();
        
        if (count($categories) == 0)
            return $this->setNoRender();
        $categoryParams = array();

        foreach ($categories as $category) {
            $subcategory_info2 = $tableCategory->getSubcategories(array('category_id' => $category->category_id, 'havingChannels' => $havingChannels, 'fetchColumns' => array('category_id', 'category_name', 'cat_order')));
            $SubCategoryArray = $tempSubCategoryArray = array();
            if (!empty($subcategory_info2)) {
                $tempFlag = 0;

                foreach ($subcategory_info2 as $subCategory) {
                    if ($tempFlag == $showSubCategoryCount)
                        break;
                    else
                        $tempFlag++;
                    $tempSubCategoryArray['sub_category_id'] = $subCategory->getIdentity();
                    $tempSubCategoryArray['title'] = $subCategory->getTitle();
                    $tempSubCategoryArray['order'] = $subCategory->cat_order;
                    $tempSubCategoryArray['count'] = !empty($count) ? $tableSitevideo->getChannelsCount(array('category_id' => $subCategory->category_id, 'columnName' => 'subcategory_id', 'foruser' => 1)) : false;

                    $SubCategoryArray[] = $tempSubCategoryArray;
                }
            }

            $tempCategoryParams['category_id'] = $category->getIdentity();
            $tempCategoryParams['title'] = $category->getTitle();
            $tempCategoryParams['order'] = $category->cat_order;
            $tempCategoryParams['video_id'] = $category->video_id;
            $tempCategoryParams['subCategories'] = $SubCategoryArray;
            $tempCategoryParams['count'] = !empty($count) ? $tableSitevideo->getChannelsCount(array('category_id' => $category->category_id, 'columnName' => 'category_id', 'foruser' => 1)) : false;
            $categoryParams[] = $tempCategoryParams;
        }

        $this->view->categoryParams = $categoryParams;
    }

}
