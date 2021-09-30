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
class Sitepage_Widget_MyPagesController extends Seaocore_Content_Widget_Abstract
{

    protected $_childCount;

    public function indexAction()
    {


        $this->view->routeName = $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        $this->view->actionName = $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // mange page in organisation
        if($routeName==='sitepage_general' && $actionName ==='manage'){
            $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
            $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        }else{
            $user_id = $request->getParam('id');
            $this->view->viewer = $viewer = Engine_Api::_()->user()->getUser($user_id);
            $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        }

        $params = $request->getParams();

        $this->view->userPageNavigationLink = $params['userPageNavigationLink'] = $this->_getParam('userPageNavigationLink');
        if (empty($this->view->userPageNavigationLink) || !is_array($this->view->userPageNavigationLink)) {
            $this->view->userPageNavigationLink = $params['userPageNavigationLink'] = array();
        }

        $this->view->columnWidth = $this->_getParam('columnWidth', 188);
        $this->view->columnHeight = $this->_getParam('columnHeight', 350);
        $this->view->ShowViewArray = $ShowViewArray = $this->_getParam('layout_views', array("GridView" => "Grid View", "ListView" => "List View", "MapView" => "Map View"));

        $this->view->show_list_view = 0;
        $this->view->show_grid_view = 0;
        $this->view->show_map_view = 0;
        $this->view->list_view = 0;
        $this->view->map_view = 0;
        $this->view->grid_view = 0;
        $list_limit = 0;
        $grid_limit = 0;
        $this->view->selected_layout_view = $selected_layout_view = $this->_getParam('selected_layout_view', 'GridView');
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
        $this->view->listview_turncation = $this->_getParam('listview_turncation', 40);
        $this->view->turncation = $this->_getParam('turncation', 40);

        // Show layout tabs
        if (in_array("GridView", $ShowViewArray)) {
            $grid_limit = $this->_getParam('grid_limit', 15);
            $this->view->show_grid_view = 1;
            $this->view->grid_view = 1;
        }
        if (in_array("ListView", $ShowViewArray)) {
            $this->view->show_list_view = 1;
            $list_limit = $this->_getParam('list_limit', 10);
            $this->view->list_view = 1;
        }
        if (in_array("MapView", $ShowViewArray)) {
            $list_limit = $this->_getParam('list_limit', 10);
            $this->view->show_map_view = 1;
            $this->view->map_view = 1;
        }

        $this->view->active_tab_list = $list_limit;
        $this->view->active_tab_image = $grid_limit;

        if (count($this->view->userPageNavigationLink) <= 0) {
            return $this->setNoRender();
        }

        if (isset($params['is_ajax'])) {
            $this->view->is_ajax = $params['is_ajax'];
        } else {
            $this->view->is_ajax = $params['is_ajax'] = false;
        }

        $params['tab'] = $request->getParam('tab', null);
        $params['user_id'] = $viewer_id;

        if (isset($params['link']) && !empty($params['link'])) {
            $params['tab'] = '';
            $currentLink = $params['link'];
        } else if (is_array($this->view->userPageNavigationLink)) {
            $currentLink = $params['link'] = $params['userPageNavigationLink'][0];
        } else {
            $currentLink = $params['link'] = 'created_pages';
        }

        $this->view->widgetPath = 'widget/index/mod/sitepage/name/my-pages';
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];

        $paginator = $this->getDataByLink($params);
        $this->view->paginator = $paginator;

        $this->view->sitepagesitepage = $paginator;
        $this->view->params = $params;

        // get locations
        if (!empty($checkLocation)) {
            $sitepage_temp = $ids = array();
            foreach ($paginator as $sitepage_page) {
                $id = $sitepage_page->getIdentity();
                $ids[] = $id;
                $sitepage_temp[$id] = $sitepage_page;
            }
            $values['page_ids'] = $ids;

            $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);
            $this->view->sitepage = $sitepage_temp;
        }

    }

    public function getDataByLink($params)
    {
        $paginator = array();
        $currentLink = $params['link'];
        switch ($currentLink) {
            case 'joined_pages':
                $userId = $params['user_id'];
                $paginator = Engine_Api::_()->getDbtable('pages', 'sitepage')->getJoinedPages($userId);
                return $paginator;
                break;
            case 'followed_pages':
                $userId = $params['user_id'];
                $paginator = Engine_Api::_()->getDbtable('pages', 'sitepage')->getFollowingPages($userId);
                return $paginator;
                break;
            case 'created_pages':
                $values = array();
                $values['user_id'] = $params['user_id'];
                $values['type'] = 'manage';
                $values['orderby'] = 'creation_date';
                $values['type_location'] = 'manage';
                $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values);
                return $paginator;
                break;
            case 'admin_pages':
                $userId = $params['user_id'];
                $paginator = Engine_Api::_()->getDbtable('pages', 'sitepage')->pagesIAdmin($userId);
                return $paginator;
                break;
            default:
                break;
        }

        return $paginator;
    }

}
