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
class Sitecrowdfunding_Widget_ProjectPeoplesController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        $sitecrowdfundingProjects = Zend_Registry::isRegistered('sitecrowdfundingProjects') ? Zend_Registry::get('sitecrowdfundingProjects') : null;

        if (empty($sitecrowdfundingProjects)){
            return $this->setNoRender();
        }

        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->project_id = $project_id = $project->getIdentity();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        //
        $params['resource_id'] = $resource_id = $project->getIdentity();
        $params['resource_type'] = $resource_type = $project->getType();


        $this->view->peopleNavigationLink = $params['peopleNavigationLink'] = $this->_getParam('peopleNavigationLink');
        if (empty($this->view->peopleNavigationLink) || !is_array($this->view->peopleNavigationLink)){
            $this->view->peopleNavigationLink = $params['peopleNavigationLink'] = array();
        }

        if (count($this->view->peopleNavigationLink) <= 0){
            return $this->setNoRender();
        }

        if (isset($params['is_ajax'])){
            $this->view->is_ajax = $params['is_ajax'];
        }else{
            $this->view->is_ajax = $params['is_ajax'] = false;
        }

        $params['tab'] = $request->getParam('tab', null);

        if (isset($params['link']) && !empty($params['link'])) {
            $params['tab'] = '';
            $currentLink = $params['link'];
        } else if (is_array($this->view->projectNavigationLink)) {
            $currentLink = $params['link'] = $params['projectNavigationLink'][0];
        } else{
            $currentLink = $params['link'] = 'creator';
        }

        $this->view->widgetPath = 'widget/index/mod/sitecrowdfunding/name/project-peoples';
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];


        $this->view->paginator = $paginator = $this->getDataByLink($params);
        $this->view->params = $params;

        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomPendingInvites($resource_id);

    }

    public function getDataByLink($params) {
        $paginator = array();
        $currentLink = $params['link'];
        switch ($currentLink) {
            case 'joined':
                $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listJoinedMembers($params['resource_id']);
                return $paginator;
                break;
            case 'followed':
                $paginator = Engine_Api::_()->getApi('favourite', 'seaocore')->peopleFavourite($params['resource_type'],$params['resource_id']);
                return $paginator;
                break;
            case 'creator':
                $paginator = [1];
                return $paginator;
                break;
            case 'admin':
                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['resource_id']);
                $list = $project->getLeaderList();
                $list_id = $list['list_id'];
                $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
                $listItemTableName = $listItemTable->info('name');
                $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
                $selectLeaders[] = $project->owner_id;

                $userTable = Engine_Api::_()->getDbtable('users', 'user');
                $userTableName = $userTable->info('name');
                $select = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array) $selectLeaders)
                    ->order('displayname ASC');

                $paginator = $userTable->fetchAll($select);
                return $paginator;
                break;
            default:
                break;
        }

        return $paginator;
    }

}
