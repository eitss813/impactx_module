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
class Sitevideo_Widget_CategoriesSidebarSitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1))
            return $this->setNoRender();

        //GET CHANNEL CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET TRUNCATION LIMIT
        $this->view->catTruncLimit = 41;
        $this->view->subCatTruncLimit = 25;

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $categories = array();
        $category_info = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'file_id'), 'sponsored' => 0, 'cat_depandancy' => 1));
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $category_info2 = $tableCategory->getSubcategories(array('category_id' => $value->category_id, 'fetchColumns' => array('category_id', 'category_name', 'cat_order', 'file_id')));
            foreach ($category_info2 as $subresults) {
                $treesubarray = array();

                $subcategory_info2 = $tableCategory->getSubcategories(array('category_id' => $subresults->category_id, 'fetchColumns' => array('category_id', 'category_name', 'cat_order', 'file_id')));
                $treesubarrays[$subresults->category_id] = array();
                foreach ($subcategory_info2 as $subvalues) {
                    $treesubarrays[$subresults->category_id][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                        'tree_sub_cat_name' => $subvalues->category_name,
                        'order' => $subvalues->cat_order,
                        'file_id' => $subvalues->file_id,
                    );
                }

                $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'order' => $subresults->cat_order,
                    'file_id' => $subresults->file_id,
                );
                $sub_cat_array[] = $tmp_array;
            }

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'sub_categories' => $sub_cat_array,
                'file_id' => $value->file_id,
            );
        }

        $this->view->categories = $categories;

        if (Count($this->view->categories) <= 0) {
            return $this->setNoRender();
        }

        $params = $request->getParams();
        if (!isset($params['category_id']))
            $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;
        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;
        $this->view->category_id = $params['category_id'];
        $this->view->subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $params['subsubcategory_id'];
    }

}
