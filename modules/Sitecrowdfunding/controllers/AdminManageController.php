<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns

 */
class Sitecrowdfunding_AdminManageController extends Core_Controller_Action_Admin {

    public function rejectAction(){

        $project_id = $this->_getParam('project_id');
        $is_funding = $this->_getParam('is_funding');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Notes();

        if($is_funding){
            $form->setTitle('Reject this funding request');
        }

        $this->view->adminnotes = $adminnotes = Engine_Api::_()->getDbTable('adminnotes','sitecrowdfunding')->getAllAdminNotesByProjectId($project_id,$is_funding);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();
            if(!empty($values['description'])) {
                $table = Engine_Api::_()->getItemTable('sitecrowdfunding_adminnote');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {

                    $notes = $table->createRow();
                    $inputs = array(
                        'description' => $values['description'],
                        'project_id' => $project_id,
                        'is_funding' => $is_funding,
                        'admin_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
                    );
                    $notes->setFromArray($inputs);
                    $notes->save();
                    $db->commit();

                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $projectTable = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
            $db1 = $projectTable->getAdapter();
            $db1->beginTransaction();
            try {
                $owner = $project->getOwner();
                $sender = Engine_Api::_()->user()->getViewer();
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                if($is_funding){
                    $project->funding_approved = 0;
                    $project->funding_state = 'rejected';
                    $project->funding_status = 'initial';
                    if($settings->getSetting('sitecrowdfunding.reminder.project.funding.disapproval', 0)) {
                        Engine_Api::_()->sitecrowdfunding()->sendMailCustom('FUNDING_DISAPPROVED', $project_id);
                        //if funding rejected means no need to reject normal project
                    }
                    if($settings->getSetting('sitecrowdfunding.notification.project.funding.disapproval', 0)) {
                        //SEND NOTIFICATION TO PROJECT OWNER
                        $type = 'sitecrowdfunding_project_funding_disapproved';
                        $notifyApi->addNotification($owner, $sender, $project, $type);
                    }
                }else{
                    $project->approved = 0;
                    $project->state = 'rejected';
                    $project->status = 'initial';
                    //if normal project rejected means need to reject funding also
                    $project->funding_approved = 0;
                    $project->funding_state = 'rejected';
                    $project->funding_status = 'initial';
                    if($settings->getSetting('sitecrowdfunding.reminder.project.disapproval', 0)) {
                       Engine_Api::_()->sitecrowdfunding()->sendMailCustom('DISAPPROVED', $project_id);
                    }
                    if($settings->getSetting('sitecrowdfunding.notification.project.disapproval', 0)) {
                        //SEND NOTIFICATION TO PROJECT OWNER
                        $type = 'sitecrowdfunding_project_disapproved';
                        $notifyApi->addNotification($owner, $sender, $project, $type);
                    }
                }
                $project->save();

                $db1->commit();

            }catch (Exception $e){
                $db1->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Rejected Successfully.')
            ));

        }
    }

    //ACTION FOR MANAGE PROJECTS
    public function indexAction() {

        $this->view->contentType = $contentType = $this->_getParam('contentType', 'All');
        $this->view->contentModule = $contentModule = $this->_getParam('contentModule', 'sitecrowdfunding');

        //GET NAVIGATION
        if ($contentModule == 'sitecrowdfunding') {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_manage');
        } elseif ($contentModule == 'siteevent') {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_manageproject');
        } else {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_manageproject');
        }

        //MAKE FORM
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Manage_Filter();
        //SHOW THE PAYOUT AND REFUND BUTTON IN CASE OF MANNUAL PAYMENT
        $this->view->paymentSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.setting', 'mannual');

        //GET PAGE NUMBER
        $page = $this->_getParam('page', 1);
        $tempManageEventFlag = false;
        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        $tempManageEventFlag = true;
        //GET PROJECT TABLE
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $tableProject->info('name');

        //MAKE QUERY
        $select = $tableProject->select()
                ->setIntegrityCheck(false)
                ->from($projectTableName)
                ->joinLeft($tableUserName, "$projectTableName.owner_id = $tableUserName.user_id", 'username')
                ->group("$projectTableName.project_id");

        if ($contentType && $contentType != 'All' && $contentModule == 'sitereview' && !isset($_POST['search'])) {
            if (strpos($contentType, "sitereview_listing") !== false) {
                $explodedArray = explode("_", $contentType);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $projectTableName . '.parent_id', array(""));
                $select->where($projectTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            }
        } elseif ($contentType && $contentType != 'All' && !isset($_POST['search'])) {
            $select->where($projectTableName . '.parent_type = ? ', $contentType);
        }
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }
        foreach ($values as $key => $value) {

            if (null == $value) {
                unset($values[$key]);
            }
        }

        //PACKAGE LIST 
        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding');
        $packageselect = $packageTable->select()->from($packageTable->info("name"), array("package_id", "title"))->order("package_id DESC");
        $this->view->packageList = $packageTable->fetchAll($packageselect);
        // searching
        $this->view->owner = '';
        $this->view->title = '';
        $this->view->sponsored = '';
        $this->view->approved = '';
        $this->view->funding_approved = '';
        $this->view->featured = '';
        $this->view->funding = '';
        $this->view->state = '';
        $this->view->funding_state = '';
        $this->view->projectbrowse = '';
        $this->view->category_id = '';
        $this->view->subcategory_id = '';
        $this->view->subsubcategory_id = '';
        $this->view->package_id = '';
        $this->view->package = 1;
        if (isset($_POST['search'])) {
            if (!empty($_POST['owner'])) {
                $owner = $this->view->owner = $_POST['owner'];
                $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
            }
            if (!empty($_POST['title'])) {
                $this->view->title = $_POST['title'];
                $select->where($projectTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
            }

            if (!empty($_POST['sponsored'])) {
                $this->view->sponsored = $_POST['sponsored'];
                $_POST['sponsored'] --;

                $select->where($projectTableName . '.sponsored = ? ', $_POST['sponsored']);
            }

            if (!empty($_POST['approved'])) {
                $this->view->approved = $_POST['approved'];
                $_POST['approved'] --;
                $select->where($projectTableName . '.approved = ? ', $_POST['approved']);
            }

            if (!empty($_POST['funding_approved'])) {
                $this->view->funding_approved = $_POST['funding_approved'];
                $_POST['funding_approved'] --;
                $select->where($projectTableName . '.funding_approved = ? ', $_POST['funding_approved']);
            }

            if (!empty($_POST['featured'])) {
                $this->view->featured = $_POST['featured'];
                $_POST['featured'] --;
                $select->where($projectTableName . '.featured = ? ', $_POST['featured']);
            }

            if (!empty($_POST['funding'])) {
                $this->view->funding = $_POST['funding'];
                $_POST['funding'] --;
                $select->where($projectTableName . '.is_fund_raisable = ? ', $_POST['funding']);
            }

            if (!empty($_POST['state'])) {
                $this->view->state = $_POST['state'];
                $state = "";
                switch ($_POST['state']) {
                    case "1" :
                        $state = 'draft';
                        break;
                    case "2" :
                        $state = "published";
                        break;
                    case "3" :
                        $state = "successful";
                        break;
                    case "4" :
                        $state = "failed";
                        break;
                    case "5" :
                        $state = "submitted";
                        break;
                    case "6" :
                        $state = "rejected";
                        break;
                }
                if (!empty($state)) {
                    $select->where($projectTableName . '.state = ? ', $state);
                }
            }

            if (!empty($_POST['funding_state'])) {
                $this->view->funding_state = $_POST['funding_state'];
                $funding_state = "";
                switch ($_POST['funding_state']) {
                    case "1" :
                        $funding_state = 'draft';
                        break;
                    case "2" :
                        $funding_state = "published";
                        break;
                    case "3" :
                        $funding_state = "successful";
                        break;
                    case "4" :
                        $funding_state = "failed";
                        break;
                    case "5" :
                        $funding_state = "submitted";
                        break;
                    case "6" :
                        $funding_state = "rejected";
                        break;
                }
                if (!empty($funding_state)) {
                    $select->where($projectTableName . '.funding_state = ? ', $funding_state);
                }
            }

            if (!empty($_POST['package_id'])) {
                $this->view->package_id = $_POST['package_id'];
                $select->where($projectTableName . '.package_id = ? ', $_POST['package_id']);
            }
            if (!empty($_POST['projectbrowse'])) {
                $this->view->projectbrowse = $_POST['projectbrowse'];
                $_POST['projectbrowse'] --;
                if ($_POST['projectbrowse'] == 0) {
                    $select->order($projectTableName . '.backer_count DESC');
                } else {
                    $select->order($projectTableName . '.project_id DESC');
                }
            }

            if (isset($_POST['contentType']) && !empty($_POST['contentType']) && $_POST['contentType'] != 'All') {
                $this->view->contentType = $_POST['contentType'];
                if (strpos($_POST['contentType'], "sitereview_listing") !== false) {
                    $explodedArray = explode("_", $_POST['contentType']);
                    $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                    $listingTableName = $listingTable->info('name');
                    $select->join($listingTableName, $listingTableName . '.listing_id = ' . $projectTableName . '.parent_id', array(""));
                    $select->where($projectTableName . ".parent_type =?", 'sitereview_listing')
                            ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
                } else {
                    $select->where($projectTableName . '.parent_type = ? ', $_POST['contentType']);
                }
            }

            if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
                $this->view->category_id = $_POST['category_id'];
                $select->where($projectTableName . '.category_id = ? ', $_POST['category_id']);
            } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
                $this->view->category_id = $_POST['category_id'];
                $this->view->subcategory_id = $_POST['subcategory_id'];
                $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;

                $select->where($projectTableName . '.category_id = ? ', $_POST['category_id'])
                        ->where($projectTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
            } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
                $this->view->category_id = $_POST['category_id'];
                $this->view->subcategory_id = $_POST['subcategory_id'];
                $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
                $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
                $this->view->subsubcategory_name = $tableCategory->getCategory($this->view->subsubcategory_id)->category_name;

                $select->where($projectTableName . '.category_id = ? ', $_POST['category_id'])
                        ->where($projectTableName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                        ->where($projectTableName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
            }
        }
        $values = array_merge(array(
            'order' => 'project_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        $select->order((!empty($values['order']) ? $values['order'] : 'project_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    //ACTION FOR MULTI-DELETE PROJECTS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getItem('sitecrowdfunding_project', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION FOR MANAGE BACKERS
    public function backersAction() {
        //GET NAVIGATION 
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_backers');
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //MAKE FORM
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_BackerFilter();

        //PROCESS FORM
        if ($formFilter->isValid($this->_getAllParams())) {
            $filterValues = $formFilter->getValues();
        } else {
            $filterValues = array();
        }
        if (empty($filterValues['order'])) {
            $filterValues['order'] = 'backer_id';
        }
        if (empty($filterValues['direction'])) {
            $filterValues['direction'] = 'DESC';
        }
        foreach ($filterValues as $key => $value) {
            if (null == $value) {
                unset($filterValues[$key]);
            }
        }
        $this->view->order = $filterValues['order'];
        $this->view->direction = $filterValues['direction'];


        //GET PAGE NUMBER
        $page = $this->_getParam('page', 1);
        //GET PROJECT TABLE
        $tableBacker = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $tableBacker->info('name');
        $projectTableName = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->info('name');
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //MAKE QUERY
        $select = $tableBacker->select()
                ->setIntegrityCheck(false)
                ->from($backerTableName)
                ->joinLeft($tableUserName, "$backerTableName.user_id = $tableUserName.user_id", array('username', 'user_id'))
                ->joinLeft($projectTableName, "$backerTableName.project_id = $projectTableName.project_id", 'title')
                ->where('payment_status = "active" OR payment_status = "authorised"
                            OR payment_status = "failed" OR payment_status = "refunded"
                            OR payment_status = "pending"');

        if (!empty($project_id)) {
            $select->where($backerTableName . '.project_id = ?', $project_id);
            $filterValues['title'] = $project->title;
        }

        $this->view->isGatewayEnabled = Engine_Api::_()->hasModuleBootstrap('sitegateway');

        if (!empty($filterValues['gateway_id'])) {
            $select->where($backerTableName . '.gateway_id = ?', $filterValues['gateway_id']);
        }

        if (!empty($filterValues['status']))
            $select->where($backerTableName . '.payment_status = ?', $filterValues['status']);

        if (!empty($filterValues['title']))
            $select->where($projectTableName . '.title LIKE ?', '%' . $filterValues['title'] . '%');
        //$this->view->assign($filterValues);
        $formFilter->populate($filterValues);
        $select->order((!empty($filterValues['order']) ? $filterValues['order'] : 'backer_id' ) . ' ' . (!empty($filterValues['order_direction']) ? $filterValues['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    //ACTION TO RETURN THE BACKED PROJECTS IN SUGGESTION
    public function getBackedProjectsAction() {
        //GET PROJECTS TABLE
        $sitecrowdfundingTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $sitecrowdfundingTableName = $sitecrowdfundingTable->info('name');
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $projects = $backersTable->getBackedProjects();
        foreach ($projects as $project) {
            $projectIds[] = $project->project_id;
        }

        //MAKE QUERY
        $select = $sitecrowdfundingTable->select()
                ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where("$sitecrowdfundingTableName.state != ?", 'draft')
                ->where("$sitecrowdfundingTableName.project_id IN(?)", $projectIds)
                ->order('title ASC');

        //FETCH RESULTS
        $usersiteprojects = $sitecrowdfundingTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($usersiteprojects as $usersiteproject) {
                $content_photo = $this->view->itemPhoto($usersiteproject, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteproject->project_id,
                    'label' => $usersiteproject->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($usersiteprojects as $usersiteproject) {
                $content_photo = $this->view->itemPhoto($usersiteproject, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteproject->project_id,
                    'label' => $usersiteproject->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR MANAGE REWARDS
    public function manageRewardsAction() {
        //GET NAVIGATION

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_managerewards');
        $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

        //MAKE FORM
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Manage_Filter();

        //GET PAGE NUMBER
        $page = $this->_getParam('page', 1);
        $tempManageEventFlag = false;
        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
        //GET PROJECT TABLE
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $tableProject->info('name');

        //GET REWARDS TABLE
        $tableReward = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding');
        $rewardsTableName = $tableReward->info('name');


        //MAKE QUERY

        $select = $tableReward->select()
                ->setIntegrityCheck(false)
                ->from($rewardsTableName, array('reward_id', 'owner_id', 'title', 'quantity', 'pledge_amount', 'creation_date', 'delivery_date'))
                ->join($projectTableName, "$projectTableName.project_id = $rewardsTableName.project_id", array('title as project_title', 'project_id as project_id'))
                ->join($tableUserName, "$projectTableName.owner_id = $tableUserName.user_id", array('username', 'displayname'))
                ->group("$rewardsTableName.reward_id");

        $values = array();

        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }
        foreach ($values as $key => $value) {

            if (null == $value) {
                unset($values[$key]);
            }
        }
        // searching
        $this->view->title = '';
        $this->view->project = '';
        $this->view->projectOwner = '';
        $this->view->minAmount = '';
        $this->view->maxAmount = '';
        $this->view->status = '';

        if (isset($_POST['search'])) {

            if (!empty($_POST['title'])) {
                $this->view->title = $_POST['title'];
                $select->where($rewardsTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
            }
            if (!empty($_POST['project'])) {
                $this->view->project = $_POST['project'];
                $select->where($projectTableName . '.title  LIKE ?', '%' . $_POST['project'] . '%');
            }
            if (!empty($_POST['username'])) {
                $projectOwner = $this->view->projectOwner = $_POST['username'];
                $select->where("$tableUserName.username  LIKE '%$projectOwner%' OR $tableUserName.displayname  LIKE '%$projectOwner%'");
            }

            if (!empty($_POST['min_amount']) && !empty($_POST['max_amount'])) {
                $this->view->minAmount = $minAmount = $_POST['min_amount'];
                $this->view->maxAmount = $maxAmount = $_POST['max_amount'];
                $select->where($rewardsTableName . '.pledge_amount BETWEEN ' . $minAmount . ' AND ' . $maxAmount . '');
            }
        }
        $values = array_merge(array(
            'order' => 'reward_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        $select->order((!empty($values['order']) ? $values['order'] : 'reward_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    //ACTION FOR MULTI-DELETE REWARDS
    public function multiDeleteRewardsAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getItem('sitecrowdfunding_reward', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage-rewards'));
    }

    //ACTION FOR DELETE THE REWARDS
    public function deleteRewardAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $this->view->reward_id = $reward_id = $this->_getParam('reward_id');

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id)->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-manage/delete-reward.tpl');
    }

    //ACTION FOR NOT DELETE THE REWARD
    public function deleteNotAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $this->renderScript('admin-manage/delete-not.tpl');
    }

    public function commissionAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_commissions');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Manage_Filter();
        //GET VALUES
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }
        $values = array_merge(array('order' => 'project_id', 'order_direction' => 'DESC'), $values);

        $this->view->backed_min_amount = $values['backed_min_amount'] = 0;
        $this->view->backed_max_amount = $values['backed_max_amount'] = 0;
        $this->view->commission_min_amount = $values['commission_min_amount'] = 0;
        $this->view->commission_max_amount = $values['commission_max_amount'] = 0;

        if (!empty($_POST['title'])) {
            $values['title'] = $_POST['title'];
        } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
            $values['title'] = $_GET['title'];
        } else {
            $values['title'] = '';
        }

        if (!empty($_POST['username'])) {
            $values['username'] = $_POST['username'];
        } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
            $values['username'] = $_GET['username'];
        } else {
            $values['username'] = '';
        }

        if (!empty($_POST['commission_min_amount']) && is_numeric($_POST['commission_min_amount'])) {
            $values['commission_min_amount'] = $_POST['commission_min_amount'];
        } elseif (!empty($_GET['commission_min_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['commission_min_amount'])) {
            $values['commission_min_amount'] = $_GET['commission_min_amount'];
        } else {
            $values['commission_min_amount'] = '';
        }

        if (!empty($_POST['commission_max_amount']) && is_numeric($_POST['commission_max_amount'])) {
            $values['commission_max_amount'] = $_POST['commission_max_amount'];
        } elseif (!empty($_GET['commission_max_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['commission_max_amount'])) {
            $values['commission_max_amount'] = $_GET['commission_max_amount'];
        } else {
            $values['commission_max_amount'] = '';
        }

        if (!empty($_POST['backed_min_amount']) && is_numeric($_POST['backed_min_amount'])) {
            $values['backed_min_amount'] = $_POST['backed_min_amount'];
        } elseif (!empty($_GET['backed_min_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['backed_min_amount'])) {
            $values['backed_min_amount'] = $_GET['backed_min_amount'];
        } else {
            $values['backed_min_amount'] = '';
        }

        if (!empty($_POST['backed_max_amount']) && is_numeric($_POST['backed_max_amount'])) {
            $values['backed_max_amount'] = $_POST['backed_max_amount'];
        } elseif (!empty($_GET['backed_max_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['backed_max_amount'])) {
            $values['backed_max_amount'] = $_GET['backed_max_amount'];
        } else {
            $values['backed_max_amount'] = '';
        }

        if (!empty($_POST['project_id'])) {
            $values['project_id'] = $_POST['project_id'];
        } elseif (!empty($_GET['project_id']) && !isset($_POST['post_search'])) {
            $values['project_id'] = $_GET['project_id'];
        } else {
            $values['project_id'] = '';
        }

        $tempProjectPaidCommission = Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding')->getPaidCommissionDetail();
        $projectPaidCommission = array();

        foreach ($tempProjectPaidCommission as $amount) {
            $projectPaidCommission[$amount['project_id']]['paid_commission'] = $amount['paid_commission'];

            if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {  
                $projectPaidCommission[$amount['project_id']]['paid_commission'] = $amount['paid_commission'] + Engine_Api::_()->sitegateway()->getSplitNEscrowGatewayCommission(array('resource_type' => 'sitecrowdfunding_backer', 'resource_id' => $amount['project_id'], 'resource_key' => 'project_id'));
            }        
        }
      
      $this->view->projectPaidCommission = $projectPaidCommission;

        $page = $values['page'] = $this->_getParam('page', 1);

        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET PROJECT TABLE
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $tableProject->info('name');

        //GET BACKERS TABLE
        $tableBacker = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backersTableName = $tableBacker->info('name');


        //MAKE QUERY
        $select = $tableBacker->select()
                ->setIntegrityCheck(false)
                ->from($projectTableName, array('project_id', 'owner_id', 'title', 'backer_count', 'state'))
                ->join($tableUserName, "$projectTableName.owner_id = $tableUserName.user_id", 'username')
                ->join($backersTableName, "$backersTableName.project_id = $projectTableName.project_id", array('sum(commission_value) as total_commission', 'sum(amount) as total_backed_amount'))
                ->where("$backersTableName.payment_status = 'active' OR $backersTableName.payment_status = 'authorised'")
                ->group("$backersTableName.project_id");


        if (!empty($values['title']))
            $select->where($projectTableName . '.title  LIKE ?', '%' . $values['title'] . '%');

        if (!empty($values['username']))
            $select->where($tableUserName . '.username  LIKE ?', '%' . $values['username'] . '%');

        if (!empty($values['commission_min_amount']) && !empty($values['commission_max_amount'])) {
            $select->having('total_commission > ?', $values['commission_min_amount']);
            $select->having('total_commission < ?', $values['commission_max_amount']);
        }

        if (!empty($values['backed_min_amount']) && !empty($values['backed_max_amount'])) {
            $select->having('total_backed_amount > ?', $values['backed_min_amount']);
            $select->having('total_backed_amount < ?', $values['backed_max_amount']);
        }

        $values = array_merge(array(
            'order' => 'project_id',
            'order_direction' => 'DESC',
                ), $values);

        $select->order((!empty($values['order']) ? $values['order'] : 'project_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        // searching
        $this->view->title = $values['title'];
        $this->view->projectOwner = $values['username'];
        $this->view->backed_min_amount = $values['backed_min_amount'];
        $this->view->backed_max_amount = $values['backed_max_amount'];
        $this->view->commission_min_amount = $values['commission_min_amount'];
        $this->view->commission_max_amount = $values['commission_max_amount'];

        $this->view->formValues = array_filter($values);
        $this->view->assign($values);
        $this->view->currency_symbol = Engine_Api::_()->sitecrowdfunding()->getCurrencySymbol();
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    //ACTION TO SHOW THE DETAILS OF PARTICULAR REWARD 
    public function rewardDetailsAction() {

        $this->_helper->layout->setLayout('admin-simple');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            return;
        }

        $project_id = $this->_getParam('project_id');
        $this->view->reward_id = $reward_id = $this->_getParam('reward_id');

        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->projectOwner = Engine_Api::_()->getItem('user', $project->owner_id);

        $reward = $this->view->reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $reward_id);
        $this->view->selectedRewardCount = $reward->spendRewardQuantity();
        $this->view->dispatchedRewardQuantity = $reward->spendRewardQuantity(true);
    }

    //ACTION TO SHOW THE DETAILS OF PARTICULAR BACKER 
    public function backerDetailsAction() {

        $this->_helper->layout->setLayout('admin-simple');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerIdentity = $viewer->getIdentity();
        if (empty($viewerIdentity)) {
            return;
        }

        $this->view->backer_id = $backer_id = $this->_getParam('backer_id', null);
        $this->view->project_id = $project_id = $this->_getParam('project_id', null);

        $this->view->backer = $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $backer_id);

        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $backer->reward_id);

        $this->view->owner = $owner = Engine_Api::_()->getItem('user', $project->owner_id);

        if ($backer->reward_status)
            $this->view->reward_status = "Yes";
        else
            $this->view->reward_status = "No";
    }


    //ACTION FOR DELETE A MEMBER FROM PROJECT
    public function deleteMemberAction() {

        $this->_helper->layout->setLayout('admin-simple');

        //GET PAGE ID.
        $user_id = $this->_getParam('user_id');
        $project_id = $this->_getParam('project_id');

        if (!empty($user_id) && !empty($project_id) ) {

            Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->delete(
                array(
                    'user_id =?' => $user_id,
                    'resource_id =?' =>  $project_id,
                    'project_id =?' =>  $project_id,
                )
            );
        }
    }

    //ACTION TO SHOW LIST MEMBERS JOINED IN PROJECT
    public function listProjectMembersAction(){

        $this->view->project_id = $project_id = $this->_getParam('project_id');

        $this->view->showViewMore = $this->_getParam('showViewMore', 0);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listJoinedMembers($project_id);

    }
}
