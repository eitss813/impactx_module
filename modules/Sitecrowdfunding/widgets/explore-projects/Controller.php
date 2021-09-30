<?php

class Sitecrowdfunding_Widget_ExploreProjectsController extends Seaocore_Content_Widget_Abstract
{
    public function indexAction()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $params = array();
        $params['orderby'] = 'random';
        $params['listItemCountPerPage'] = 3;

        $projects = $this->view->projects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);
        $projects->setItemCountPerPage(3);
        $projects->setCurrentPageNumber(1);

        $this->view->total_projects = $projects->getTotalItemCount();

    }
}