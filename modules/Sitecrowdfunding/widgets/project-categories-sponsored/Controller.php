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
class Sitecrowdfunding_Widget_ProjectCategoriesSponsoredController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $itemCount = $this->_getParam('itemCount', 0);
        $this->view->showIcon = $this->_getParam('showIcon', 1);
        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        //GET SPONSORED CATEGORIES
        $fetchColumns = array('category_id', 'category_name', 'cat_order', 'file_id', 'font_icon', 'category_slug', 'cat_dependency', 'subcat_dependency');
        $this->view->categories = $categories = $tableCategory->getCategories($fetchColumns, null, 0, 1, 0, $itemCount);

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
        //GET SPONSORED CATEGORIES COUNT
        $this->view->totalCategories = Count($categories);
        $sitecrowdfundingProjectCategorySponsored = Zend_Registry::isRegistered('sitecrowdfundingProjectCategorySponsored') ? Zend_Registry::get('sitecrowdfundingProjectCategorySponsored') : null;
        if (empty($sitecrowdfundingProjectCategorySponsored))
            return $this->setNoRender();
        if ($this->view->totalCategories <= 0) {
            return $this->setNoRender();
        }
    }

}
