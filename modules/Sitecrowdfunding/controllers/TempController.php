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
class Sitecrowdfunding_TempController extends Seaocore_Controller_Action_Standard
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

    // action to get all members
    public function getAdminMembersAction()
    {

        //GET Project ID.
        $project_id = $this->_getParam('project_id', null);

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $list = $project->getLeaderList();

        $list_id = $list['list_id'];
        $text = $this->_getParam('text', null);

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
            ->setIntegrityCheck(false)
            ->from($userTableName);

        $select = $select->where("$userTableName.user_id NOT IN (?)", (array)$selectLeaders);
        $select = $select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ? OR " . $userTableName . ".email LIKE ?", '%' . $text . '%')
            ->group("$userTableName.user_id")
            ->order('displayname ASC')
            ->limit(20);

        //FETCH ALL RESULT.
        $userlists = $userTable->fetchAll($select);
        $data = array();

        foreach ($userlists as $userlist) {
            $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
            $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
            );
        }

        return $this->_helper->json($data);
    }

    // action to add members as admin in project
    public function addAdminMembersAction()
    {

        // require user
        if (!$this->_helper->requireUser()->isValid()) return;

        // get viewer user
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PROJECT DETAILS
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $list = $project->getLeaderList();

        //PREPARE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepEightAdmin(array(
            'project_id' => $project_id
        ));

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // if form is submitted
        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPost())) {

                $values = $form->getValues();

                $members_ids = explode(",", $values['toValues']);

                if (empty($values['user_ids']) && empty($values['toValues'])) {
                    $form->addError('Please complete this field - It is requried.');
                    return;
                }

                if (empty($values['toValues'])) {
                    $form->addError('This is an invalid user name. Please select a valid user name from the autosuggest.');
                    return;
                }

                $auth = Engine_Api::_()->authorization()->context;

                if (!empty($members_ids)) {

                    foreach ($members_ids as $members_id) {

                        $user = Engine_Api::_()->getItem('user', $members_id);

                        //RETURN IF USER IS NOT ALREADY A LEADER: A CASE WHEN WE CLICK MULTIPLE TIMES
                        if (!$list->has($user)) {

                            $table = $list->getTable();
                            $db = $table->getAdapter();
                            $db->beginTransaction();

                            try {
                                $list->add($user);
                                $leaderList = $project->getLeaderList();

                                // Create some auth stuff for all leaders
                                $auth->setAllowed($project, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($project, $leaderList, 'edit', 1);
                                $auth->setAllowed($project, $leaderList, 'delete', 1);

                                // Add notification
                                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                                $notifyApi->addNotification($user, $viewer, $project, 'sitecrowdfunding_create_leader');

                                // Add activity
                                $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
                                $activityApi->addActivity($user, $project, 'sitecrowdfunding_promote');

                                $db->commit();
                            } catch (Exception $e) {
                                $db->rollBack();
                                throw $e;
                            }

                        }

                    }
                }

                return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully added as into this project.')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                ));

            }
        }

    }

    public function stepSevenAction()
    {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        } else {
            $this->view->project = $project = Engine_Api::_()->core()->getSubject();
            $this->view->project_id = $project_id = $project->project_id;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            return;
        }
        $this->view->paypalForm = $paypalForm = new Sitecrowdfunding_Form_PayPal();

        $paypalForm->removeDecorator('title');

        //$this->view->paypalEnable = true;
        $this->view->stripeConnected = 0;
        $this->view->paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.paymentmethod', 'paypal');
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'split') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
        } elseif ($paymentMethod == 'escrow') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
        } else {
            if (empty($paymentToSiteadmin)) {
                $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
            }
        }
        $projectEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'project_gateway');
        if (!empty($projectEnabledgateway)) {
            $projectEnabledgateway = Zend_Json_Decoder::decode($projectEnabledgateway);
        }
        $getEnabledGateways = array();
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('plugin' => array('Sitegateway_Plugin_Gateway_Stripe', 'Sitegateway_Plugin_Gateway_MangoPay')));
        }
        foreach ($getEnabledGateways as $getEnabledGateway) {
            $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
            $gatewayKey = strtolower($gatewyPlugin[1]);
            $gatewayKeyUC = ucfirst($gatewyPlugin[1]);
            if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                if (isset($_SESSION['redirect_stripe_connect_oauth_process'])) {
                    $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
                    $session->unsetAll();
                }
                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                if (!empty($projectGatewayObj) && !empty($projectGatewayObj->projectgateway_id)) {
                    if (is_array($projectGatewayObj->config) && !empty($projectGatewayObj->config['stripe_user_id'])) {
                        $this->view->stripeConnected = 1;
                        $this->view->stripeEnabled = true;
                    }
                }
            } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                $this->view->mangopayForm = $mangopayForm = new Sitecrowdfunding_Form_MangoPay();
                $this->view->mangopayBankDetailForm = $mangopayBankDetailForm = new Sitecrowdfunding_Form_MangoPayBankDetail();

                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_MangoPay\''));
                if (!empty($projectGatewayObj)) {
                    // Populate form
                    $mangopayForm->populate($projectGatewayObj->toArray());
                    $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
                    $mode = 'live';
                    if ($adminAPGateway->config['test_mode']) {
                        $mode = 'sandbox';
                    }
                    $config = isset($projectGatewayObj->config[$mode]) ? ($projectGatewayObj->config[$mode]) : null;
                    if (is_array($config)) {
                        $birthday = $projectGatewayObj->config[$mode]['birthday'];
                        $config['birthday'] = date('Y-m-d', $birthday);
                        $mangopayForm->populate($config);
                        $mangopayBankDetailForm->populate($config);
                    }
                    if ($projectGatewayObj->enabled == 1) {
                        $this->view->mangopayEnable = true;
                    }
                } else {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                    $select = $searchTable->select();
                    $select->where('item_id = ?', $viewer->getIdentity());
                    $otherUserRecords = $searchTable->fetchRow($select);
                    if ($otherUserRecords) {
                        $formData['first_name'] = $otherUserRecords->first_name;
                        $formData['last_name'] = $otherUserRecords->last_name;
                        $formData['birthday'] = $otherUserRecords->birthdate;
                    }
                    $formData['email'] = $viewer->email;
                    $mangopayForm->populate($formData);
                }
            } else {
                $formName = "form$gatewayKeyUC";
                $formClass = "Sitegateway_Form_Order_$gatewayKeyUC";
                $this->view->$formName = $gatewayForm = new $formClass();
                $gatewayForm->setName("sitecrowdfunding_payment_info_$gatewayKey");
                if ((!empty($projectEnabledgateway[$gatewayKey]) || !empty($paymentToSiteadmin))) {
                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin = ?' => $getEnabledGateway->plugin));
                    if (!empty($projectGatewayObj)) {
                        $gateway_id = $projectGatewayObj->projectgateway_id;
                        if (!empty($gateway_id)) {
                            $gatewyEnabled = $gatewayKey . 'Enabled';
                            $this->view->$gatewyEnabled = true;
                            $gatewayForm->populate($projectGatewayObj->toArray());
                            if (is_array($projectGatewayObj->config)) {
                                $gatewayForm->populate($projectGatewayObj->config);
                            }
                        }
                    }
                }
            }
        }

        if (!empty($projectEnabledgateway['paypal']) || !empty($paymentToSiteadmin)) {
            $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
            if (!empty($projectGatewayObj)) {
                $gateway_id = $projectGatewayObj->projectgateway_id;
                $this->view->paypalEnable = true;
                $paypalForm->populate($projectGatewayObj->toArray());
                if (is_array($projectGatewayObj->config)) {
                    $paypalForm->populate($projectGatewayObj->config);
                }
            }
        }

        // Show form by default
        $this->view->paypalEnable = true;
    }

    public function stepEightAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        /**
         * privacy form
         **/
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepEight(array(
            'project_id' => $project_id
        ));

        $form->setTitle('Privacy Settings');
        $form->removeDecorator('description');
        $form->removeElement('cancel');
        $form->execute->setLabel('Next');
        $leaderList = $project->getLeaderList();

        /**
         * Set Admin list in UI
         **/
        $this->view->list = $list = $project->getLeaderList();
        $list_id = $list['list_id'];
        $auth = Engine_Api::_()->authorization()->context;

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

        $userSelect = $userTable->select()
            ->from($userTableName)
            ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
            ->order('displayname ASC');

        $this->view->members = $userTable->fetchAll($userSelect);

        /***
         * Set Privacy in UI
         ***/
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $projectTags = $project->tags()->getTagMaps();
            $tagString = '';

            foreach ($projectTags as $tagmap) {
                $temp = $tagmap->getTag();
                if (!empty($temp)) {
                    if ($tagString != '')
                        $tagString .= ', ';
                    $tagString .= $tagmap->getTag()->getTitle();
                }
            }

            $this->view->tagNamePrepared = $tagString;
            if (isset($form->tags))
                $form->tags->setValue($tagString);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($project, $role, "view")) {
                        $form->auth_view->setValue($roleString);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($project, $role, "comment")) {
                        $form->auth_comment->setValue($roleString);
                    }
                }
            }
            $ownerList = '';
            $roles_photo = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

            foreach ($roles_photo as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $sitecrowdfundingAllow = Engine_Api::_()->getApi('allow', 'sitecrowdfunding');
                if ($form->auth_topic && 1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'topic')) {
                    $form->auth_topic->setValue($roleString);
                }

                if (isset($form->auth_post) && $form->auth_post && 1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'post')) {
                    $form->auth_post->setValue($roleString);
                }
            }
            if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                if (empty($project->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                } else {
                    $form->networks_privacy->setValue(explode(",", $project->networks_privacy));
                }
            }
        }


        /****
         * SAVE
         ***/
        if ($this->getRequest()->isPost()) {

            /***
             * SAVE PRIVACY FORM
             **/
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                if (empty($values))
                    return;

                $projectModel = $project;

                //PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;

                $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $leaderList = $projectModel->getLeaderList();

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = "registered";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }

                    $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                    $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
                }
                $ownerList = '';
                $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                if (empty($values['auth_topic'])) {
                    $values['auth_topic'] = "registered";
                }
                if (isset($values['auth_post']) && empty($values['auth_post'])) {
                    $values['auth_post'] = "registered";
                }

                $topicMax = array_search($values['auth_topic'], $roles);
                $postMax = '';
                if (isset($values['auth_post']) && !empty($values['auth_post']))
                    $postMax = array_search($values['auth_post'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }
                    $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                    if (!is_null($postMax)) {
                        $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                    }
                }
                // Create some auth stuff for all leaders
                $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'delete', 1);
                //UPDATE KEYWORDS IN SEARCH TABLE
                if (!empty($keywords)) {
                    Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'sitecrowdfunding_project', 'id = ?' => $projectModel->project_id));
                }
                if (!empty($project_id)) {
                    $projectModel->setLocation();
                }
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($projectModel) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'project-create', 'action' => 'step-nine', 'project_id' => $project_id), 'sitecrowdfunding_create_temp', true);

        }

    }

    public function stepNineAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $goals_ids = Engine_Api::_()->getDbTable('sdggoals', 'sitecrowdfunding')->getSDGGoals();

        $targets = Engine_Api::_()->getDbTable('sdgtargets', 'sitecrowdfunding')->getSDGTargetsWithActualIDS();

        $this->view->goals_ids = $goals_ids;
        $this->view->target_ids = $targets;

        $this->view->goals = $goals = Engine_Api::_()->getDbtable('goals', 'sitecrowdfunding')->getAllGoalsByProjectId($project_id);

    }

    public function stepTenAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        // get details
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
            if (empty($editPrivacy)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        if ($project->is_fund_raisable) {
            //enabling the funding
            $project->funding_approved = 1;
            if (empty($project->funding_approved_date))
                $project->funding_approved_date = date('Y-m-d H:i:s');
            $project->funding_state = 'published';
            $project->funding_status = 'active';

            if (empty($project->approved_date))
                $project->approved_date = date('Y-m-d H:i:s');
            $project->approved = 1;
            $project->state = 'published';
            $project->status = 'active';
        } else {
            if (empty($project->approved_date))
                $project->approved_date = date('Y-m-d H:i:s');
            $project->approved = 1;
            $project->state = 'published';
            $project->status = 'active';
        }

        $project->save();

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);
        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomPendingInvites($project_id);
    }
}
