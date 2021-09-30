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
class Sitecrowdfunding_Widget_ProjectCategoriesNavigationController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET PRODUCT CATEGORY TABLE
        $orderBy = $this->_getParam('orderBy', 'cat_order');
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');

        $categories = array();
        $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order'), null, 0, 0, 1, 0, $orderBy);
        $sitecrowdfundingCategoryNavigation = Zend_Registry::isRegistered('sitecrowdfundingCategoryNavigation') ? Zend_Registry::get('sitecrowdfundingCategoryNavigation') : null;
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $subcategories = $tableCategory->getSubCategories($value->category_id);

            foreach ($subcategories as $subresults) {
                $subsubcategories = $tableCategory->getSubCategories($subresults->category_id);
                $treesubarrays[$subresults->category_id] = array();

                foreach ($subsubcategories as $subsubcategoriesvalues) {
                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name);
                }
                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id]);
            }
            $categories[] = $category_array = array(
                'category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'sub_categories' => $sub_cat_array);
        }
        if (empty($sitecrowdfundingCategoryNavigation))
            return $this->setNoRender();
        $this->view->categories = $categories;
        $this->view->requestAllParams = $requestAllParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    }

}
