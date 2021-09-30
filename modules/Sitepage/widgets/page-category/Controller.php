<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageCategoryController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {

        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $this->view->page_id = $page_id = $sitepage->getIdentity();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

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
        if (empty($category_id)){
            $categories = $tableCategory->getCategoriesWithProjectsCount(0,$page_id);
        }else{
            $categories = $tableCategory->getSubcategories($category_id);
        }

        if(!count($categories)){
            return $this->setNoRender();
        }
        $this->view->categoryParams = $categories;

        if(count($categories) <= 0 ){
            return $this->setNoRender();
        }
    }
}

?>