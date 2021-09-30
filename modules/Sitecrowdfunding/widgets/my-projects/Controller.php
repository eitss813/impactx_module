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
class Sitecrowdfunding_Widget_MyProjectsController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        if (Engine_Api::_()->core()->hasSubject('user') && Engine_Api::_()->core()->getSubject('user')) {
            $viewer = Engine_Api::_()->core()->getSubject('user');
        } else
            $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        //TO SHOW ONLY GRID VIEW IN THE MOBILE VIEW
        $this->view->isSiteMobileView = Engine_Api::_()->sitecrowdfunding()->isSiteMobileMode();
        $this->view->topNavigationLink = $params['topNavigationLink'] = $this->_getParam('topNavigationLink');
        if (empty($this->view->topNavigationLink) || !is_array($this->view->topNavigationLink))
            $this->view->topNavigationLink = $params['topNavigationLink'] = array();

        $this->view->projectNavigationLink = $params['projectNavigationLink'] = $this->_getParam('projectNavigationLink');
        if (empty($this->view->projectNavigationLink) || !is_array($this->view->projectNavigationLink))
            $this->view->projectNavigationLink = $params['projectNavigationLink'] = array();

        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType');
        if (empty($this->view->viewType) || !is_array($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array();

        $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'gridView');

        if (!empty($params['viewType']) && !in_array($params['defaultViewType'], $params['viewType']))
            $this->view->defaultViewType = $params['defaultViewType'] = $params['viewType'][0];

        if (empty($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array($params['defaultViewType']);

        if (!empty($params['viewType']) && !in_array($params['defaultViewType'], $params['viewType']))
            $this->view->defaultViewType = $params['defaultViewType'] = $params['viewType'][0];
        if (empty($this->view->viewType))
            $this->view->viewType = $params['viewType'] = array($params['defaultViewType']);

        $this->view->projectOption = $params['projectOption'] = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
        $sitecrowdfundingProjects = Zend_Registry::isRegistered('sitecrowdfundingProjects') ? Zend_Registry::get('sitecrowdfundingProjects') : null;
        $params['selectProjects'] = $this->_getParam('selectProjects', 'all');
        $this->view->searchButton = $params['searchButton'] = $this->_getParam('searchButton', 1);
        $this->view->gridViewWidth = $params['gridWidth'] = $this->_getParam('gridWidth', 200);
        $this->view->gridViewHeight = $params['gridHeight'] = $this->_getParam('gridHeight', 200);
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->gridItemCount = $params['gridItemCount'] = $this->_getParam('gridItemCount', 12);
        $this->view->listItemCount = $params['listItemCount'] = $this->_getParam('listItemCount', 12);
        $this->view->titleTruncationGridView = $params['titleTruncationGridView'] = $this->_getParam('titleTruncationGridView', 25);
        if (empty($sitecrowdfundingProjects))
            return $this->setNoRender();
        $this->view->titleTruncationListView = $params['titleTruncationListView'] = $this->_getParam('titleTruncationListView', 40);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 100);
        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];

        if (count($this->view->projectNavigationLink) <= 0)
            return $this->setNoRender();

        if (!isset($params['viewFormat']))
            $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
        else
            $this->view->viewFormat = $params['viewFormat'];
        $page = $this->_getParam('page', 1);

        if (isset($params['is_ajax']))
            $this->view->is_ajax = $params['is_ajax'];
        else
            $this->view->is_ajax = $params['is_ajax'] = false;


        $this->view->showViewMore = true;

        if ($this->view->viewFormat == 'gridView')
            $this->view->itemCount = $this->view->gridItemCount;
        else
            $this->view->itemCount = $this->view->listItemCount;

        $params['tab'] = $request->getParam('tab', null);

        if (isset($params['link']) && !empty($params['link'])) {
            $params['tab'] = '';
            $currentLink = $params['link'];
        } else if (is_array($this->view->projectNavigationLink)) {
            $currentLink = $params['link'] = $params['projectNavigationLink'][0];
        } else
            $currentLink = $params['link'] = 'all';


        $this->view->canCreateProject = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'create');

        $this->view->widgetPath = 'widget/index/mod/sitecrowdfunding/name/my-projects';
        $this->view->message = 'You do not have any Projects.';
        $this->view->isViewMoreButton = true;
        $this->view->isEditButton = 0;
        $this->view->isDeleteButton = 0;

        if (isset($params['search']) && !empty($params['search']))
            $this->view->currentSearch = $params['search'];
        $params['owner'] = $viewer->getType();
        $params['owner_id'] = $viewer->getIdentity();

        $paginator = $this->view->paginator = $this->getDataByLink($params);

        $paginatorMapView = $this->view->paginatorMapView = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectLocationSelect($params);

        if (isset($params['search']) && !empty($params['search']))
            $this->view->message = 'No projects found in search criteria.';

        $params['search'] = null;
        $this->view->params = $params;
        //FIND TOTAL NO. OF RECORDS
        $this->_childCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($this->view->itemCount);
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
    }

    public function getDataByLink($params) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $projectIds = array();
        $projectIds[] = ' ';
        $tempParam = array();
        $projectTable = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
        $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
        $this->view->currenctTab = $currentLink = $params['link'];

        switch ($currentLink) {
            case 'all':
                $projectIdsTemp=array();
                $projectIdsAdminTemp=array();
                $projectIdsAllTemp=array();
                $this->view->message = "You don't have any Projects.";
                //backed
                $tempParam['user_id'] = $params['owner_id'];
                $projects = $backersTable->getBackedProjects($tempParam);
                foreach ($projects as $project) {
                    $projectIds[] = $project->project_id;
                }
                 //followed
                $select = $favouritesTable->select();
                $select->where('poster_id = ?', $params['owner_id'])
                    ->where('poster_type = ?', 'user')
                    ->where('resource_type = ?', 'sitecrowdfunding_project');
                $projects = $select->query()->fetchAll();
                foreach ($projects as $project) {
                    $projectIdsTemp[] = $project["resource_id"];
                }
                $projectIds = array_merge($projectIds,$projectIdsTemp);
                //admin
                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');
                $listIds = $listItemTable->select()
                    ->from($listItemTableName, array('list_id'))
                    ->where("child_id = ?", $viewer->getIdentity())
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                if(!empty($listIds) && is_array($listIds) && count($listIds) > 0){
                    $listTable = Engine_Api::_()->getDbTable('lists', 'sitecrowdfunding');
                    $listTableName = $listTable->info('name');
                    $selectProjectIds = $listTable->select()
                        ->from($listTableName, array('owner_id'))
                        ->where("list_id IN (?)",$listIds )
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                    $projectIdsAdminTemp = $selectProjectIds;
                }
                if(empty($projectIdsAdminTemp) || count($projectIdsAdminTemp) === 0){
                    $projectIdsAdminTemp = array(-1);
                }
                $projectIds = array_merge($projectIds,$projectIdsAdminTemp);
                 //My Projects
                $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
                $projectTableName = $projectTable->info('name');
                $select = $projectTable->select()->from($projectTableName, '*')
                    ->where('owner_id =?', $params['owner_id']);
                $projects = $projectTable->fetchAll($select);
                foreach ($projects as $project) {
                   // $projectIdsAllTemp = $project->project_id;
                    array_push($projectIdsAllTemp,$project->project_id);
                }
                $projectIds = array_merge($projectIds,$projectIdsAllTemp);
                $projectIds = array_unique($projectIds);
                break;

            case 'admin':
                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');
                $listIds = $listItemTable->select()
                    ->from($listItemTableName, array('list_id'))
                    ->where("child_id = ?", $viewer->getIdentity())
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                if(!empty($listIds) && is_array($listIds) && count($listIds) > 0){
                    $listTable = Engine_Api::_()->getDbTable('lists', 'sitecrowdfunding');
                    $listTableName = $listTable->info('name');
                    $selectProjectIds = $listTable->select()
                        ->from($listTableName, array('owner_id'))
                        ->where("list_id IN (?)",$listIds )
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                    $projectIds = $selectProjectIds;
                }
                if(empty($projectIds) || count($projectIds) === 0){
                    $projectIds = array(-1);
                }
                break;
            case 'backed':
                $this->view->message = 'You have not Backed any Projects.';
                $tempParam['user_id'] = $params['owner_id'];
                $projects = $backersTable->getBackedProjects($tempParam);
                foreach ($projects as $project) {
                    $projectIds[] = $project->project_id;
                }
                break;
            case 'liked':
                $this->view->message = 'You have not liked any Projects.';
                $select = $likesTable->select();
                $select->where('resource_type = ?', 'sitecrowdfunding_project');
                $select->where('poster_id = ?', $params['owner_id']);
                $projects = $select->query()->fetchAll();
                foreach ($projects as $project) {
                    $projectIds[] = $project["resource_id"];
                }
                $params['owner_id'] = null;
                break;
            case 'favourites':
                $this->view->message = 'You have not marked any Project as favourite.';
                $select = $favouritesTable->select();
                $select->where('poster_id = ?', $params['owner_id'])
                    ->where('poster_type = ?', 'user')
                    ->where('resource_type = ?', 'sitecrowdfunding_project');
                $projects = $select->query()->fetchAll();
                foreach ($projects as $project) {
                    $projectIds[] = $project["resource_id"];
                }
                break;
            case 'favourite':
                $this->view->message = 'You have not marked any Project as favourite.';
                $select = $favouritesTable->select();
                $select->where('poster_id = ?', $params['owner_id'])
                        ->where('poster_type = ?', 'user')
                        ->where('resource_type = ?', 'sitecrowdfunding_project');
                $projects = $select->query()->fetchAll();
                foreach ($projects as $project) {
                    $projectIds[] = $project["resource_id"];
                }
                break;
            case 'all':
            default: $tab = $params['tab'];
                switch ($tab) {
                    case 'launched': $params['selectProjects'] = 'all';
                        $this->view->message = 'You do not have any launched Projects.';
                        break;
                    case 'successful': $params['selectProjects'] = 'successful';
                        $this->view->message = 'You do not have any successful Projects.';
                        break;
                    case 'failed': $params['selectProjects'] = 'failed';
                        $this->view->message = 'You do not have any failed Projects.';
                        break;
                    default:
                        break;
                }
                $params['allProjects'] = 'all';
                $this->view->isEditButton = 1;
                $this->view->isDeleteButton = 1;
                $paginator = $projectTable->getProjectPaginator($params);
                return $paginator;
                break;
        }
        if (!isset($paginator)) {
            $params['project_ids'] = $projectIds;
            $params['owner_id'] = null;
            $paginator = $projectTable->getMyProjectPaginator($params);
        }
        return $paginator;
    }

}
