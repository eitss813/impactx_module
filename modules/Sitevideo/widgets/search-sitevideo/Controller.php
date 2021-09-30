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
class Sitevideo_Widget_SearchSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = $params = $request->getParams();
        //FORM CREATION
        $this->view->viewType = $this->_getParam('viewType', 'vertical');
        $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);
        $widgetSettings = array(
            'viewType' => $this->view->viewType,
            'showAllCategories' => $this->view->showAllCategories,
        );
        $this->view->form = $form = new Sitevideo_Form_Search_Search(array('widgetSettings' => $widgetSettings));
        $viewFormat = $params['viewFormat'] = $request->getParam('viewFormat', 'videoView');
        $orderBy = $request->getParam('orderby', null);

        if (!isset($params['category_id']))
            $params['category_id'] = 0;

        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;

        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;

        $this->view->category_id = $category_id = $params['category_id'];
        $this->view->subcategory_id = $subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $subsubcategory_id = $params['subsubcategory_id'];

        if (empty($orderBy)) {
            $order = Engine_Api::_()->sitevideo()->showSelectedBrowseBy($this->view->identity);
            if (isset($form->orderby))
                $form->orderby->setValue("$order");
        }

        $orderBy = $request->getParam('orderby', null);

        if (!empty($orderBy)) {
            $params['orderby'] = $orderBy;
        }

        $this->view->categoryInSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitevideo_channel', 'category_id');

        if (!isset($params['profile_type']) && !empty($this->view->category_id) && !empty($this->view->categoryInSearchForm)) {
            $categoryIds = array();
            $categoryIds[] = $this->view->category_id;
            $categoryIds[] = $this->view->subcategory_id;
            $categoryIds[] = $this->view->subsubcategory_id;

            
            
             $profile_type = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
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
            //GET PROFILE MAPPING ID
            $this->view->profileType = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
        }

        $categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'sponsored' => 0, 'cat_depandancy' => 1));
        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }

        $this->view->categories_slug = $categories_slug;
    }

}
