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
class Sitevideo_Widget_CategoriesWithiconGridViewController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1))
            return $this->setNoRender();
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $tableSitevideo = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $this->view->columnWidth = $this->_getParam('columnWidth', 268);
        $this->view->columnHeight = $this->_getParam('columnHeight', 260);
        $showAllCategories = $this->_getParam('showAllCategories', 0);
        $orderBy = $this->_getParam('orderBy', 'cat_order');
        $this->view->showIcon = $this->_getParam('showIcon', 1);
        $this->view->storage = Engine_Api::_()->storage();
        $havingChannels = !$showAllCategories;
        // GET ALL CATEGORIES
        $categories = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'cat_dependency', 'category_name', 'category_slug', 'cat_order', 'video_id', 'file_id'), 'sponsored' => 0, 'cat_depandancy' => 1, 'havingChannels' => $havingChannels, 'orderBy' => $orderBy));

        if (count($categories) == 0)
            return $this->setNoRender();
        $categoryParams = array();
        foreach ($categories as $category) {

            $tempCategoryParams['category_id'] = $category->getIdentity();
            $tempCategoryParams['title'] = $category->getTitle();
            $tempCategoryParams['order'] = $category->cat_order;
            $tempCategoryParams['video_id'] = $category->video_id;
            $tempCategoryParams['file_id'] = $category->file_id;
            $tempCategoryParams['count'] = !empty($count) ? $tableSitevideo->getChannelsCount(array('category_id' => $category->category_id, 'columnName' => 'category_id', 'foruser' => 1)) : false;
            $categoryParams[] = $tempCategoryParams;
        }
        $this->view->categoryParams = $categoryParams;
    }

}
