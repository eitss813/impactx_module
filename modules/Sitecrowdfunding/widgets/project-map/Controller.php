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
class Sitecrowdfunding_Widget_projectMapController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        //TO SHOW ONLY GRID VIEW IN THE MOBILE VIEW
        $this->view->isSiteMobileView = Engine_Api::_()->sitecrowdfunding()->isSiteMobileMode();
        $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
        $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);
        $this->view->locationDetection = $this->_getParam('locationDetection', 1);
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->itemCount = $this->_getParam('itemCount', 8);
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType');
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'gridView');
        $sitecrowdfundingBrowseLocation = Zend_Registry::isRegistered('sitecrowdfundingBrowseLocation') ? Zend_Registry::get('sitecrowdfundingBrowseLocation') : null;
        if (!empty($params['viewType']) && !in_array($params['defaultViewType'], $params['viewType']))
            $this->view->defaultViewType = $params['defaultViewType'] = $params['viewType'][0];
        if (empty($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array($params['defaultViewType']);

        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];

        $this->view->projectOption = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
//        if (empty($sitecrowdfundingBrowseLocation))
//            return $this->setNoRender();
        $widgetSettings = array(
            'advancedSearch' => $this->view->advancedSearch,
            'showAllCategories' => $this->view->showAllCategories,
            'locationDetection' => $this->view->locationDetection,
        );
        // Make form
        $this->view->form = $form = new Sitecrowdfunding_Form_Locationsearch(array('widgetSettings' => $widgetSettings));

        $this->view->is_ajax = 0;
        if (isset($_POST['is_ajax'])) {
            $this->view->is_ajax = $_POST['is_ajax'];
        }

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();
        $this->view->message = "No projects have been posted yet.";
//        if (!($module == 'sitecrowdfunding' && $controller == 'project' && $action == 'view') && empty($this->view->is_ajax)) {
//            return $this->setNoRender();
//        }


        if (empty($_POST['location'])) {
            $this->view->locationVariable = '1';
        }


        if (empty($_POST['is_ajax'])) {

            $values = $form->getValues();
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
            $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        } else {
            $values = $_POST;
            $form->isValid($values);
            $parms = $form->getValues();
            $values = array_merge($values, $parms);
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        }

        if ((isset($values['search']) && !empty($values['search'])) || (isset($values['location']) && !empty($values['location'])) || (isset($values['locationmiles']) && !empty($values['locationmiles'])) || (isset($values['orderby']) && !empty($values['orderby'])))
            $this->view->message = "No projects have been posted with this criteria yet.";

        unset($values['or']);
        $this->view->assign($values);
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

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $values['type'] = 'browse';
        $values['type_location'] = 'browseLocation';

        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->titleTruncationGridView = $this->_getParam('titleTruncationGridView', 25);
        $this->view->titleTruncationListView = $this->_getParam('titleTruncationListView', 40);
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation', 100);


        if (isset($values['show'])) {
            if ($form->show->getValue() == 3) {
                @$values['show'] = 3;
            }
        }

        if ($request->getParam('page'))
            $this->view->current_page = $page = $request->getParam('page');
        else
            $this->view->current_page = $page = $this->_getParam('page', 1);
        $form->page->setValue($page);

        $this->view->enableLocation = $checkLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

        //check for miles or street.
        if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {

            if (isset($values['project_street']) && !empty($values['project_street'])) {
                $values['location'] = $values['project_street'] . ',';
                unset($values['project_street']);
            }

            if (isset($values['project_city']) && !empty($values['project_city'])) {
                $values['location'].= $values['project_city'] . ',';
                unset($values['project_city']);
            }

            if (isset($values['project_state']) && !empty($values['project_state'])) {
                $values['location'].= $values['project_state'] . ',';
                unset($values['project_state']);
            }

            if (isset($values['project_country']) && !empty($values['project_country'])) {
                $values['location'].= $values['project_country'];
                unset($values['project_country']);
            }
        }

        $values['orderby'] = $orderBy = $request->getParam('orderby', null);

        if (empty($orderBy)) {
            $orderby = $this->_getParam('orderby', 'startDate');
            if ($orderby == 'startDate')
                $values['orderby'] = 'startDate';
            else
                $values['orderby'] = $orderby;
        }


        $result = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getProjectSelect($values, $customFieldValues);
        $paginator = Zend_Paginator::factory($result);
        $paginator->setItemCountPerPage($this->view->itemCount);
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
        $this->view->totalresults = $paginator->getTotalItemCount();
        $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
        $this->view->flageSponsored = 0;

        if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {

            $ids = array();
            foreach ($paginator as $project) {
                $id = $project->getIdentity();
                $ids[] = $id;
                $project_temp[$id] = $project;
            }
            $values['project_ids'] = $ids;
            $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation($values);

            foreach ($locations as $location) {
                if ($project_temp[$location->project_id]->sponsored) {
                    $this->view->flageSponsored = 1;
                    break;
                }
            }

            $this->view->list = $project_temp;
        }

        $this->view->isViewMoreButton = false;
        $this->view->widgetPath = 'widget/index/mod/sitecrowdfunding/name/browselocation-sitecrowdfunding';
    }

}
