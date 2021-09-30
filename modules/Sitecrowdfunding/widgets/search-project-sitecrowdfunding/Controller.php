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
class Sitecrowdfunding_Widget_SearchProjectSitecrowdfundingController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = $params = $request->getParams();
        //FORM CREATION
        $this->view->viewType = $this->_getParam('viewType', 'vertical');
        $this->view->whatWhereWithinmile = $this->_getParam('whatWhereWithinmile', 0);
        $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
        $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);
        $this->view->locationDetection = $this->_getParam('locationDetection', 1);

        $widgetSettings = array(
            'viewType' => $this->view->viewType,
            'whatWhereWithinmile' => $this->view->whatWhereWithinmile,
            'advancedSearch' => $this->view->advancedSearch,
            'showAllCategories' => $this->view->showAllCategories,
            'locationDetection' => $this->view->locationDetection,
        );
        $sitecrowdfundingSearchProjects = Zend_Registry::isRegistered('sitecrowdfundingSearchProjects') ? Zend_Registry::get('sitecrowdfundingSearchProjects') : null;

        $this->view->form = $form = new Sitecrowdfunding_Form_Search_ProjectSearch(array('widgetSettings' => $widgetSettings));
        $form->removeElement('location');
        $form->removeElement('locationmiles');
        $form->removeElement('project_street');
        $form->removeElement('project_city');
        $form->removeElement('project_state');
        $form->removeElement('project_country');
        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $order = Engine_Api::_()->sitecrowdfunding()->showSelectedProjectBrowseBy($this->view->identity);
            if (isset($form->orderby))
                $form->orderby->setValue("$order");
        }else {
            $params['orderby'] = $orderBy;
        }
        if (empty($sitecrowdfundingSearchProjects))
            return $this->setNoRender();
        if (!isset($params['category_id']))
            $params['category_id'] = 0;

        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;

        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;

        $this->view->category_id = $category_id = $params['category_id'];
        $this->view->subcategory_id = $subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $subsubcategory_id = $params['subsubcategory_id'];
        $this->view->categoryInSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitecrowdfunding_project', 'category_id');

        if (!isset($params['profile_type']) && !empty($this->view->category_id) && !empty($this->view->categoryInSearchForm)) {
            $categoryIds = array();
            $categoryIds[] = $this->view->category_id;
            $categoryIds[] = $this->view->subcategory_id;
            $categoryIds[] = $this->view->subsubcategory_id;

            $profile_type = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType($categoryIds);
            if (!empty($profile_type)) {
                $params['profile_type'] = $profile_type;
            }
        }

        if (!empty($params))
            $form->populate($params);

        //SHOW PROFILE FIELDS ON DOME READY
        if (!empty($this->view->categoryInSearchForm) && !empty($this->view->categoryInSearchForm->display) && !empty($category_id)) {
            $categoryIds = array();
            $categoryIds[] = $category_id;
            $categoryIds[] = $subcategory_id;
            $categoryIds[] = $subsubcategory_id;
        }


        $categories = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategories();
        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }
        $this->view->categories_slug = $categories_slug;
    }

}
