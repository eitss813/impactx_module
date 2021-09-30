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
class Sitecrowdfunding_ProjectFundingController extends Seaocore_Controller_Action_Standard
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

    public function detailsAction()
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
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }


        // Get rewards
        $this->view->rewards = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewards($project_id, 0);
        $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
        $this->view->rewardCount = 0;
        $this->view->rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->query()->fetchColumn();


        // Get Backers Report
        $params = array();
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $params['project_id'] = $this->view->project_id = $project->project_id;
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['page'] = $this->view->page = $this->_getParam('page', 1);

        // Get Bankers Report
        $multiOptions = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getPaymentStates();
        $this->view->payment_status = array_merge(array('' => 'Payment Status: Any'), $multiOptions);
        $this->view->message = 'There are no backers for this project yet.';
        $this->view->searchUser = '';
        $this->view->searchOption = 0;
        if (isset($_POST['searchByRewards'])) {
            $this->view->searchOption = $params['searchByRewards'] = $_POST['searchByRewards'];
            $this->view->message = 'There are no backers related to this criteria.';
        }
        if (isset($_POST['search'])) {
            $this->view->searchUser = $params['username'] = $_POST['username'];
            $params['user_id'] = $user_id = $_POST['user_id'];
            $this->view->selectedStatus = $params['payment_status'] = $_POST['payment_status'];
            $this->view->message = 'There are no backers related to this criteria.';
        }
        if (isset($user_id) && !empty($user_id)) {
            $this->view->searchUser = $user = Engine_Api::_()->user()->getUser($user_id)->getTitle();
        }
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getBackersPaginator($params);
        $this->view->total_item = $paginator->getTotalItemCount();

        // Get External
        $this->view->externalfunding = $externalfunding =  Engine_Api::_()->getDbtable('externalfundings','sitecrowdfunding')->getAllExternalFunding($project_id);

    }

}
