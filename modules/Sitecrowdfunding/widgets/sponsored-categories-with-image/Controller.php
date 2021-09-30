<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_SponsoredCategoriesWithImageController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->height = $this->_getParam('height', 400);

        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        $fetchColumns = array('category_id', 'category_name', 'cat_order', 'photo_id', 'file_id', 'font_icon', 'category_slug', 'cat_dependency', 'subcat_dependency');
        $this->view->categories = $paginator = Zend_Paginator::factory($tableCategory->getCategories($fetchColumns, null, 0, 1, 0, 6));
        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        $paginator->setItemCountPerPage(6);
        //GET SPONSORED CATEGORIES COUNT
        $this->view->totalCategories = $paginator->getTotalItemCount();
        $sitecrowdfundingSponsoredCategoryWithImage = Zend_Registry::isRegistered('sitecrowdfundingSponsoredCategoryWithImage') ? Zend_Registry::get('sitecrowdfundingSponsoredCategoryWithImage') : null;
        if ($this->view->totalCategories <= 0) {
            return $this->setNoRender();
        }
        if (empty($sitecrowdfundingSponsoredCategoryWithImage))
            return $this->setNoRender();
    }

}
