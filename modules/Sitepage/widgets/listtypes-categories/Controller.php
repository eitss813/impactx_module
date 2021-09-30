<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_ListtypesCategoriesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->viewDisplayHR = $this->_getParam('viewDisplayHR', 1);
        if ($this->view->viewDisplay) {
            $element = $this->getElement();
            $this->view->widgetTitle = $element->getTitle();
            $element->setTitle('');
        }

        $params = array();

        $beforeNavigation = $this->_getParam('beforeNavigation', 0);
        if ($beforeNavigation) {
            return $this->setNoRender();
        }

        $category_id = Zend_Registry::isRegistered('sitepageCategoryId') ? Zend_Registry::get('sitepageCategoryId') : null;
        if (!empty($category_id))
            $this->view->category_id = $category_id;

        //GET PRODUCT CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitepage');
     

        if (empty($category_id)) {
            $categoriesArray = array();
            $categories = $tableCategory->getCategories(array('category_id', 'category_name', 'category_slug', 'cat_dependency', 'subcat_dependency'), null, 0, 0, 1);
            foreach ($categories as $category) {
                $subcategoriesArray = $this->getSubCategory($category->category_id);
                $categoriesArray[$category->category_id] = array(
                    'category' => $category,
                    'subcategories' => $subcategoriesArray,
                );
            }

            $this->view->categoriesArray = $categoriesArray;
        } else {
            $subcategoriesArray = $this->getSubCategory($category_id);
            $this->view->categoriesArray = $subcategoriesArray;
        }

        $this->view->requestAllParams = $requestAllParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    }

    protected function getSubCategory($category_id) {
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitepage');
        $subcategoriesArray = array();
        $subcategories = $tableCategory->getSubCategories($category_id, array('category_id', 'category_name', 'cat_order', 'cat_dependency', 'subcat_dependency', 'category_slug'));
        foreach ($subcategories as $subcategory) {
            $subsubcatgories = $tableCategory->getSubCategories($subcategory->category_id);
            $ssb_cat_count = count($subsubcatgories);
            if (empty($ssb_cat_count)) {
                $subsubcatgories = array();
            }
            $subcategoriesArray[$subcategory->category_id] = array(
                'subcategory' => $subcategory,
                'subsubcatgories' => $subsubcatgories
            );
        }
        return $subcategoriesArray;
    }

}

