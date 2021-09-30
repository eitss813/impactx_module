<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_ProjectAdminSettingsController extends Seaocore_Controller_Action_Standard
{

    protected $_hasPackageEnable;

    public function init()
    {
        //SET THE SUBJECT
        if (0 !== ($project_id = (int)$this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    public function settingsAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $this->view->project_id = $project_id = $project->project_id;

      /*  // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }
      */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        $this->view->TabActive = 'leaders';

        /*
        * List Project Admins
         */

        $this->view->list = $list = $project->getLeaderList();

        $list_id = $list['list_id'];

        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
        $listItemTableName = $listItemTable->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');
        $selectLeaders = $listItemTable->select()
            ->from($listItemTableName, array('child_id'))
            ->where("list_id = ?", $list_id)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $selectLeaders[] = $project->owner_id;

        $select = $userTable->select()
            ->from($userTableName)
            ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
            ->order('displayname ASC');

        $this->view->adminMembers = $userTable->fetchAll($select);

        //fetch the external project admin - invited mebers - custom code
        $selects = $listItemTable->select()
            ->from($listItemTableName)
            ->where("member_email IS NOT NULL");
        

        $this->view->externalMembers = $listItemTable->fetchAll($selects);



        /*
         * Manage Member Role
         */

        $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1);

        if ($manageMemberSettings == 3) {
            $is_admincreated = array("0" => 0, "1" => 1);
        } elseif ($manageMemberSettings == 2) {
            $is_admincreated = array("0" => 0);
        }

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding');
        $rolesTableName = $rolesTable->info('name');

        $select = $rolesTable->select()
            ->from($rolesTableName)
            ->where($rolesTableName . '.is_admincreated IN (?)', (array)$is_admincreated)
            ->where($rolesTableName . '.project_id = ? ', $project_id)
            ->order('role_id DESC');

        $this->view->manageRolesHistories = $rolesTable->fetchALL($select);

        /**
         * Members
        **/
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);
        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomPendingInvites($project_id);


    }

}
