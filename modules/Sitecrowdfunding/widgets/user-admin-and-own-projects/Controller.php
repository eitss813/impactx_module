<?php

class Sitecrowdfunding_Widget_UserAdminAndOwnProjectsController extends Seaocore_Content_Widget_Abstract
{
    public function indexAction()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->user_projects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getUserProjectAndAdminProjects($viewer_id);
        $this->view->total_user_projects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getUserProjectAndAdminProjectsCount($viewer_id);

        if ($this->view->total_user_projects < 0) {
            return $this->setNoRender();
        }

    }
}