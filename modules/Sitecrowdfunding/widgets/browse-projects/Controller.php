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
class Sitecrowdfunding_Widget_BrowseProjectsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        //TO SHOW ONLY GRID VIEW IN THE MOBILE VIEW
        $this->view->isSiteMobileView = Engine_Api::_()->sitecrowdfunding()->isSiteMobileMode();

        $params['projectType'] = $contentType = $request->getParam('projectType', null);
        if (empty($contentType)) {
            $params['projectType'] = $this->_getParam('projectType', 'All');
        }
        $sitecrowdfundingBrowseProjects = Zend_Registry::isRegistered('sitecrowdfundingBrowseProjects') ? Zend_Registry::get('sitecrowdfundingBrowseProjects') : null;
        $this->view->projectType = $params['projectType'];
        $params['selectProjects'] = $this->_getParam('selectProjects', 'all');
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType');
        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'gridView');
        if (!empty($params['viewType']) && !in_array($params['defaultViewType'], $params['viewType']))
            $this->view->defaultViewType = $params['defaultViewType'] = $params['viewType'][0];
        if (empty($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array($params['defaultViewType']);

        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];
        if (empty($sitecrowdfundingBrowseProjects))
            return $this->setNoRender();
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 150);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 150);
        $this->view->projectOption = $params['projectOption'] = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption))
            $this->view->projectOption = $params['projectOption'] = array();
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->gridViewCountPerPage = $gridViewCountPerPage = $params['gridItemCountPerPage'] = $this->_getParam('gridItemCountPerPage', 8);
        $this->view->listViewCountPerPage = $listViewCountPerPage = $params['listItemCountPerPage'] = $this->_getParam('listItemCountPerPage', 8);
        $this->view->titleTruncationGridView = $params['titleTruncationGridView'] = $this->_getParam('titleTruncationGridView', 25);
        $this->view->titleTruncationListView = $params['titleTruncationListView'] = $this->_getParam('titleTruncationListView', 40);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 100);

        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->orderby = $orderby = $params['orderby'] = $this->_getParam('orderby', 'startDate');
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        $element = $this->getElement();
        $widgetTitle = $this->view->heading = $element->getTitle();
        if (!empty($widgetTitle)) {
            $element->setTitle("");
        } else {
            $this->view->heading = "";
        }
        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = $this->_getParam('page', 1);

        $widgetSettings = array(
            'locationDetection' => $this->view->detactLocation,
        );

        //FORM GENERATION
        $form = new Sitecrowdfunding_Form_Search_ProjectSearch(array('widgetSettings' => $widgetSettings));
        if (!empty($params)) {
            $form->populate($params);
        }
        $this->view->formValues = $form->getValues();
        $params = array_merge($params, $form->getValues());

        if (!$this->view->detactLocation)
            $param['location'] = "";
        $this->view->latitude = $param['latitude'] = 0;
        $this->view->longitude = $param['longitude'] = 0;
        $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);
        }
        if ($this->view->detactLocation) {
            $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $this->view->latitude = $params['latitude'] = $cookieLocation['latitude'];
            $this->view->longitude = $params['longitude'] = $cookieLocation['longitude'];
            if (empty($request->getParams()['location']))
                $this->view->location = $params['location'] = '';
        }

        //CUSTOM FIELD WORK
        $customFieldValues = array();
        $customFieldValues = array_intersect_key($params, $form->getFieldElements());
        $params['orderby'] = $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            if ($orderby == 'startDate')
                $params['orderby'] = 'startDate';
            else
                $params['orderby'] = $orderby;
        }

        // get tag name if present
        $tagName = $request->getParam('tag', null);
        if(isset($tagName) && !empty($tagName)){
            $this->view->tagName = $tagName;
        }

        $this->view->params = $params;
        $this->view->widgetPath = 'widget/index/mod/sitecrowdfunding/name/browse-projects-sitecrowdfunding';
        $this->view->message = 'Nobody has started a project yet.';

        if ((isset($params['search']) && !empty($params['search'])) || (isset($params['category_id']) && !empty($params['category_id'])) || (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) || (isset($params['tag_id']) && !empty($params['tag_id'])) || (isset($params['location']) && !empty($params['location'])))
            $this->view->message = 'Nobody has started a project with that criteria.';

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $this->view->viewerId = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $this->view->can_upload_project = $allow_upload_project = Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', 'create');
        $this->view->isViewMoreButton = false;
        $this->view->showViewMore = true;
        $projectDbTables = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $paginatorGridView = $this->view->paginatorGridView = $projectDbTables->getProjectPaginator($params, $customFieldValues);
        $paginatorListView = $this->view->paginatorListView = $paginatorGridView;
        //$projectDbTables->getProjectPaginator($params, $customFieldValues);
        $paginatorMapView = $this->view->paginatorMapView = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectLocationSelect($params);
        if (count($this->view->viewType) == 2) {
            if ($gridViewCountPerPage > $listViewCountPerPage) {
                $this->view->gPaginator = $paginatorListView;
            } else {
                $this->view->gPaginator = $paginatorGridView;
            }
        } else {
            if ($this->view->viewType[0] == 'gridView') {
                $this->view->gPaginator = $paginatorGridView;
            } else {
                $this->view->gPaginator = $paginatorListView;
            }
        }
        $paginatorGridView->setItemCountPerPage($gridViewCountPerPage);
        $paginatorListView->setItemCountPerPage($listViewCountPerPage);

        $paginatorGridView->setCurrentPageNumber($page);
        $paginatorListView->setCurrentPageNumber($page);
        $this->view->countPage = $this->view->totalCount = $paginatorListView->getTotalItemCount();
    }

}
