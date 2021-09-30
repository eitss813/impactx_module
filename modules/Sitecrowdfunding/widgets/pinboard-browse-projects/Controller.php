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
class Sitecrowdfunding_Widget_pinboardBrowseProjectsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = array_merge($this->_getAllParams(), $request->getParams());

        $params['projectType'] = $contentType = $request->getParam('projectType', null);
        if (empty($contentType)) {
            $params['projectType'] = $this->_getParam('projectType', 'All');
        }
        $this->view->projectType = $params['projectType'];

        if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
            $this->view->params['noOfTimes'] = 1000;
        $sitecrowdfundingPinboardBrowseProjects = Zend_Registry::isRegistered('sitecrowdfundingPinboardBrowseProjects') ? Zend_Registry::get('sitecrowdfundingPinboardBrowseProjects') : null;
        if ($this->_getParam('autoload', true)) {
            $this->view->autoload = true;

            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->autoload = false;
                if ($this->_getParam('contentpage', 1) > 1)
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                $this->getElement()->removeDecorator('Title');
            }
        } else {
            $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
            if ($this->_getParam('contentpage', 1) > 1) {
                $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            }
        }
        $params = $this->view->params;
        $this->view->projectOption = $params['projectOption'] = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
        if (empty($sitecrowdfundingPinboardBrowseProjects))
            return $this->setNoRender();
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $params['itemCount'] = $params['itemCountPerPage'];
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->userComment = $this->_getParam('userComment', 1);
        $this->view->defaultLoadingImage = $params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
        $this->view->itemWidth = $params['itemWidth'] = $this->_getParam('itemWidth', 237);
        $this->view->withoutStretch = $params['withoutStretch'] = $this->_getParam('withoutStretch', 1);
        $this->view->category_id = $params['category_id'] = $this->_getParam('category_id', 0);
        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 100);

        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
        if (!isset($params['category_id']))
            $params['category_id'] = 0;

        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;

        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;

        if (empty($params['category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('category_id');
            $params['subcategory_id'] = $this->_getParam('subcategory_id');
            $params['subsubcategory_id'] = $this->_getParam('subsubcategory_id');
        }

        //GET CATEGORYID AND SUBCATEGORYID
        $this->view->categoryName = '';
        if ($this->view->category_id) {
            $this->view->categoryName = $params['categoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $this->view->category_id)->category_name;
            if ($this->view->subcategory_id) {
                $this->view->subCategoryName = $params['subcategoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $this->view->subcategory_id)->category_name;
            }

            if ($this->view->subsubcategory_id) {
                $this->view->subsubCategoryName = $params['subsubcategoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $this->view->subsubcategory_id)->category_name;
            }
        }

        //FORM GENERATION
        $form = new Sitecrowdfunding_Form_Search_ProjectSearch();
        $this->view->params = $params;

        if (!empty($params)) {
            $form->populate($params);
        }

        $this->view->formValues = $form->getValues();
        $params = array_merge($params, $form->getValues());

        $requestedAllParams = $this->_getAllParams();
        if (isset($requestedAllParams['hidden_project_category_id']) && !empty($requestedAllParams['hidden_project_category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_project_category_id');
            $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_project_subcategory_id');
            $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_project_subsubcategory_id');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // FIND USERS' FRIENDS
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!empty($params['view_view']) && $params['view_view'] == 1) {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if (!$this->view->detactLocation)
            $params['location'] = "";

        $this->view->latitude = $params['latitude'] = 0;
        $this->view->longitude = $params['longitude'] = 0;
        $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
        if ($this->view->detactLocation && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $this->view->latitude = $params['latitude'] = $cookieLocation['latitude'];
            $this->view->longitude = $params['longitude'] = $cookieLocation['longitude'];
            $this->view->location = $params['location'] = '';
        }

        $customFieldValues = array();
        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($params, $form->getFieldElements());
        $params['orderby'] = $orderBy = $request->getParam('orderby', null);

        if (empty($orderBy)) {
            $orderby = $params['orderby'] = $this->_getParam('orderby', 'start_date');
        }

        $this->view->params = $params;
        $this->view->widgetPath = 'widget/index/mod/sitecrowdfunding/name/pinboard-browse-projects';
        $this->view->message = 'No Projects found in search criteria.';

        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('Projects', 'sitecrowdfunding')->getProjectPaginator($params, $customFieldValues);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));

        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_projects', null, 'create');

        $this->view->countPage = $paginator->count();

        if ($this->view->params['noOfTimes'] > $this->view->countPage)
            $this->view->params['noOfTimes'] = $this->view->countPage;

        $this->view->show_buttons = $this->_getParam('show_buttons', array("like", "comment", 'facebook', "linkedin", "googleplus", 'twitter'));
    }

}
