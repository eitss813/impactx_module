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
class Sitepage_Widget_AjaxBasedProjectsHomeController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $pages_ids = array();

        if (Engine_Api::_()->core()->hasSubject('sitepage_page')) {
            $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
            $this->view->page_id = $page_id = $sitepage->page_id;
        } else {
            $page_id = $this->_getParam('page_id', null);
            if ($page_id) {
                $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
                $this->view->page_id = $page_id = $sitepage->page_id;
            }
        }

        // if no page_id, then dont render anything
        if (!$page_id) {
            return $this->setNoRender();
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $params['projectType'] = $contentType = $request->getParam('projectType', null);
        if (empty($contentType)) {
            $params['projectType'] = $this->_getParam('projectType', 'All');
        }
        $this->view->projectType = $params['projectType'];
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'list_view');

        if (!isset($params['viewFormat']))
            $params['viewFormat'] = $params['defaultViewType'];

        $this->view->viewFormat = str_replace("ZZZ", "_", $params['viewFormat']);

        $layouts_views = $params['viewType'] = $this->_getParam('viewType', array('grid_view', 'list_view','map_view'));

        foreach ($layouts_views as $key => $value)
            $layouts_views[$key] = str_replace("ZZZ", "_", $value);

        if (!empty($params['viewType']) && !in_array($params['defaultViewType'], $params['viewType']))
            $this->view->defaultViewType = $params['defaultViewType'] = $params['viewType'][0];

        if (empty($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array($this->view->viewFormat);

        $page = $this->_getParam('page', 1);

        $this->view->viewType = $layouts_views;

        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);

        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);

        $this->view->projectOption = $projectOption = $params['projectOption'] = $this->_getParam('projectOption');

        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }

        $this->view->gridViewCountPerPage = $params['gridItemCountPerPage'] = $this->_getParam('gridItemCountPerPage', 12);

        $this->view->listViewCountPerPage = $params['listItemCountPerPage'] = $this->_getParam('listItemCountPerPage', 12);

        $this->view->titleTruncationGridView = $params['titleTruncationGridView'] = $this->_getParam('titleTruncationGridView', 25);

        $this->view->titleTruncationListView = $params['titleTruncationListView'] = $this->_getParam('titleTruncationListView', 40);

        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 100);

        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);

        $this->view->daysFilter = $params['daysFilter'] = $this->_getParam('daysFilter', 20);

        $this->view->backedPercentFilter = $params['backedPercentFilter'] = $this->_getParam('backedPercentFilter', 50);

        $this->view->showContent = $params['show_content'] = 2;

        $this->view->isViewMoreButton = false;

        $this->view->showViewMore = $this->_getParam('showViewMore', false);

        $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);

        $showTabArray = $params['ajaxTabs'] = $this->_getParam('ajaxTabs', array('random','most_recent', 'most_commented', 'most_backed' ,'most_funded', 'most_liked','most_favourite'));

        if ($showTabArray) {
            foreach ($showTabArray as $key => $value)
                $showTabArray[$key] = str_replace("ZZZ", "_", $value);
        } else {
            $showTabArray = array();
        }

        if (empty($this->view->viewType) || count($this->view->viewType) == 0) {
            $this->view->viewType = array($this->view->viewFormat);
        } else if (!in_array($this->view->viewFormat, $this->view->viewType)) {
            $this->view->viewFormat = $this->view->viewType[0];
        }

        $this->view->is_ajax_load = true;
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);

        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;

            if (!$this->_getParam('detactLocation', 0) || $this->_getParam('contentpage', 1) > 1)
                $this->getElement()->removeDecorator('Title');

            $this->getElement()->removeDecorator('Container');
        } else {

            if ($this->_getParam('detactLocation', 0))
                $this->getElement()->removeDecorator('Title');

            $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
        }

        $this->view->latitude = $params['latitude'] = 0;
        $this->view->longitude = $params['longitude'] = 0;
        $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);

        if ($this->view->detactLocation && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $this->view->latitude = $params['latitude'] = $cookieLocation['latitude'];
            $this->view->longitude = $params['longitude'] = $cookieLocation['longitude'];
        }

        $this->view->is_ajax = $isAjax = $this->_getParam('is_ajax', 0);
        $this->view->tabCount = count($showTabArray);

        if (empty($this->view->tabCount)) {
            return $this->setNoRender();
        }

        $params['selectProjects'] = $params['selectProjects'] = $this->_getParam('selectProjects', 'all');

        $this->view->tabs = $showTabArray = $this->setTabsOrder($showTabArray);
        $paramsContentType = $this->_getParam('content_type', 'random');
        $this->view->content_type = $paramsContentType = $paramsContentType ? $paramsContentType : $showTabArray[0];

        if (!isset($params['category_id']))
            $params['category_id'] = 0;

        if (empty($params['category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('category');
        }
        $this->view->message = 'Nobody has started a project yet.';

        switch ($paramsContentType) {
            case 'random' :
                $orderby = 'random';
                break;
            case 'most_recent' :
                $orderby = 'startDate';
                break;
            case 'most_commented' :
                $orderby = 'commentCount';
                $this->view->message = 'Nobody has commented on project yet.';
                break;
            case 'most_backed' :
                $orderby = 'backerCount';
                $this->view->message = 'Nobody has backed a project yet.';
                break;
            case 'most_funded' :
                $orderby = 'mostFunded';
                $this->view->message = 'Nobody has funded a project yet.';
                break;
            case 'most_liked' :
                $orderby = 'likeCount';
                $this->view->message = 'Nobody has liked a project yet.';
                break;
            case 'most_favourite' :
                $orderby = 'favouriteCount';
                $this->view->message = 'Nobody has favourite any project yet.';
                break;
            default :
                $orderby = 'startDate';
        }
        $params['orderby'] = $orderby;

        if (!$this->view->is_ajax_load)
            return;


        // get partner-page-ids
        //$allPartnerPageIds = Engine_Api::_()->getDbtable('partners', 'sitepage')->getJoinedAndAddedPartnerPages($page_id);
        $allPartnerPageIds[] = $page_id;
        $params['page_ids'] = $allPartnerPageIds;

        $paginatorListView = $this->view->paginatorListView = $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);
        $paginatorGridView = $this->view->paginatorGridView = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);
        $paginatorMapView = $this->view->paginatorMapView = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectLocationSelect($params);

        $this->view->totalCount = $this->view->countPage = $paginator->getTotalItemCount();
        $paginatorListView->setItemCountPerPage($params['listItemCountPerPage']);
        $paginatorGridView->setItemCountPerPage($params['gridItemCountPerPage']);

        $paginatorListView->setCurrentPageNumber($page);
        $paginatorGridView->setCurrentPageNumber($page);

        $this->view->params = $params;

    }

    public function setTabsOrder($tabs) {
        $tabsOrder['random'] = $this->_getParam('randomOrder', 1);
        $tabsOrder['most_recent'] = $this->_getParam('recentOrder', 2);
        $tabsOrder['most_commented'] = $this->_getParam('commentedOrder', 3);
        $tabsOrder['most_backed'] = $this->_getParam('backedOrder', 4);
        $tabsOrder['most_funded'] = $this->_getParam('fundersOrder', 5);
        $tabsOrder['most_liked'] = $this->_getParam('likedOrder', 6);
        $tabsOrder['most_favourite'] = $this->_getParam('favouriteOrder', 7);
        $tempTabs = array();
        foreach ($tabs as $tab) {
            $order = $tabsOrder[$tab];
            if (isset($tempTabs[$order]))
                $order++;
            $tempTabs[$order] = $tab;
        }
        ksort($tempTabs);
        $orderTabs = array();
        $i = 0;
        foreach ($tempTabs as $tab)
            $orderTabs[$i++] = $tab;
        return $orderTabs;
    }

}
