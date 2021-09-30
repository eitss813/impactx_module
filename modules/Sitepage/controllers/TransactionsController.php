<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_TransactionsController extends Core_Controller_Action_Standard
{

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init()
    {

        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
            return;

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

        $page_url = $this->_getParam('page_url', $this->_getParam('page_url', null));
        $page_id = $this->_getParam('page_id', $this->_getParam('page_id', null));

        if ($page_url) {
            $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
        }

        if ($page_id) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            if ($sitepage) {
                if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
                    Engine_Api::_()->core()->setSubject($sitepage);
                }
            }
        }

        //FOR UPDATE EXPIRATION
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time()) {
            Engine_Api::_()->sitepage()->updateExpiredPages();
        }
    }

    public function projectTransactionsDetailsAction()
    {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PROJECT SUBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // Get Backers Report
        $params = array();
        $params['project_id'] = $this->view->project_id = $project->project_id;
        $params['page'] = $this->view->page = $this->_getParam('page', 1);

        // Get Bankers Report
        $this->view->message = 'There are no backers for this project yet.';
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getBackersPaginator($params);
        $this->view->total_item = $paginator->getTotalItemCount();

        // Get External
        $this->view->externalfunding = $externalfunding = Engine_Api::_()->getDbtable('externalfundings', 'sitecrowdfunding')->getAllExternalFunding($project_id);

        // get funders count
        $this->view->fundingDatas = $fundingDatas = Engine_Api::_()->getDbTable('externalfundings', 'sitecrowdfunding')->getExternalFundingAmount($project->project_id);
        $this->view->totalFundingAmount = $totalFundingAmount = $fundingDatas['totalFundingAmount'];
        $this->view->memberCount = $memberCount = $fundingDatas['memberCount'];
        $this->view->orgCount = $orgCount = $fundingDatas['orgCount'];
        $this->view->total_backer_count = $total_backer_count = $fundingDatas['memberCount'] + $fundingDatas['orgCount'];
        $this->view->fundedAmount = $fundedAmount = $project->getFundedAmount();

    }

    public function getTransactionsAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET PROJECT ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //IF THERE IS NO PAGE.
        if (empty($sitepage)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // get params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        // get tab active id
        if (empty($params['tab_link'])) {
            $params['tab_link'] = 'all_transactions';
        } else if (isset($params['tab_link']) && !empty($params['tab_link'])) {
            $params['tab_link'] = $params['tab_link'];
        } else {
            $params['tab_link'] = 'all_transactions';
        }

        $this->view->params = $params;

        // get projects
        $this->view->projectsIds = $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        // logic for each tabs
        if ($params['tab_link'] == 'all_transactions') {

            $this->view->searchForm = $searchForm = new Sitecrowdfunding_Form_TransactionFilter();
            $searchForm->populate($_POST);

            $this->view->sort_field = $_POST['sort_field'];
            $this->view->sort_direction = $_POST['sort_direction'];

            $allTransactionsParams = array();
            $allTransactionsParams['page'] = $this->_getParam('page', 1);
            $allTransactionsParams['project_ids'] = $projectsIds;

            if (isset($_POST['search'])) {
                $values = $searchForm->getValues();
                $start_cal_date = $values['start_cal'];
                $end_cal_date = $values['end_cal'];
                if (!empty($start_cal_date)) {
                    $start_tm = date("Y-m-d", strtotime($start_cal_date));
                    $allTransactionsParams['from'] = $start_tm;
                }
                if (!empty($end_cal_date)) {
                    $end_tm = date("Y-m-d", strtotime($end_cal_date));
                    $allTransactionsParams['to'] = $end_tm;
                }
                $allTransactionsParams['user_name'] = $values['user_name'];
                $allTransactionsParams['user_id'] = $values['user_id'];
                $allTransactionsParams['project_id'] = $values['project_id'];
                $allTransactionsParams['project_name'] = $values['project_name'];
                $allTransactionsParams['transaction_min_amount'] = $values['transaction_min_amount'];
                $allTransactionsParams['transaction_max_amount'] = $values['transaction_max_amount'];
                $allTransactionsParams['commission_min_amount'] = $values['commission_min_amount'];
                $allTransactionsParams['commission_max_amount'] = $values['commission_max_amount'];
                $allTransactionsParams['payment_status'] = $values['payment_status'];
                $allTransactionsParams['sort_field'] = $values['sort_field'];
                $allTransactionsParams['sort_direction'] = $values['sort_direction'];

                if (isset($values['locationParams']) && $values['locationParams']) {
                    if (is_string($values['locationParams'])) {
                        $locationParams = Zend_Json_Decoder::decode($values['locationParams']);
                        $allTransactionsParams = array_merge($allTransactionsParams, $locationParams);
                        $allTransactionsParams['location_only_projects'] = true;
                    }
                }
                $allTransactionsParams['locationmiles'] = $values['locationmiles'];
            }

            $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->getBackerTransactionsPaginator($allTransactionsParams);
            $this->view->totalAmount = $totalAmount = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->getSumOfAmountFromBackerTransactions($allTransactionsParams);

            // fetch projects location only if location is filtered
            if (isset($_POST['search'])) {
                $formValues = $searchForm->getValues();
                if (isset($formValues['locationParams']) && $formValues['locationParams']) {
                    if ($paginator->getTotalItemCount() > 0) {
                        $project_ids = array();
                        foreach ($paginator as $item) {
                            $project_ids [] = $item->project_id;
                            $project_temp[$item->project_id] = Engine_Api::_()->getItem('sitecrowdfunding_project', $item->project_id);
                        }
                        $locationParams = array();
                        $locationParams['project_ids'] = array_unique($project_ids);
                        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation($locationParams);
                        $this->view->list = $project_temp;
                    }
                }
            }

        }

        // logic for each tabs
        if ($params['tab_link'] == 'transaction_by_projects') {

            $this->view->searchForm = $searchForm = new Sitecrowdfunding_Form_ProjectTransactionFilter();
            $searchForm->populate($_POST);

            $this->view->sort_field = $_POST['sort_field'];
            $this->view->sort_direction = $_POST['sort_direction'];

            $projectTransactionsParams = array();
            $projectTransactionsParams['page'] = $this->_getParam('page', 1);
            $projectTransactionsParams['project_ids'] = $projectsIds;
            if (isset($_POST['search'])) {
                $values = $searchForm->getValues();
                $projectTransactionsParams['project_id'] = $values['project_id'];
                $projectTransactionsParams['project_name'] = $values['project_name'];
                $projectTransactionsParams['user_id'] = $values['user_id'];
                $projectTransactionsParams['user_name'] = $values['user_name'];
                $projectTransactionsParams['state'] = $values['state'];
                $projectTransactionsParams['funding_state'] = $values['funding_state'];
                $projectTransactionsParams['goal_amount_max'] = $values['goal_amount_max'];
                $projectTransactionsParams['goal_amount_min'] = $values['goal_amount_min'];
                $projectTransactionsParams['funding_amount_min'] = $values['funding_amount_min'];
                $projectTransactionsParams['funding_amount_max'] = $values['funding_amount_max'];
                $projectTransactionsParams['total_funders_min'] = $values['total_funders_min'];
                $projectTransactionsParams['total_funders_max'] = $values['total_funders_max'];
                $projectTransactionsParams['sort_field'] = $values['sort_field'];
                $projectTransactionsParams['sort_direction'] = $values['sort_direction'];
            }

            $paginator = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->getProjectTransactionsPaginator($projectTransactionsParams);
            $this->view->paginator = $paginator;

        }

        // Transaction By Locations Tab
        if ($params['tab_link'] == 'transaction_by_location') {

            // location search form
            $this->view->locationForm = $locationForm = new Sitecrowdfunding_Form_TransactionLocationFiilter();
            $locationForm->populate($_POST);

            $transactionsByLocationParams = array();
            $transactionsByLocationParams['project_ids'] = $projectsIds;
            $page_no = $this->_getParam('page', 1);

            // set filtered values for getting data based on params pass
            if (isset($_POST['search'])) {
            }

            // get page projects
            $result = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getProjectSelect($transactionsByLocationParams, []);
            $paginator = Zend_Paginator::factory($result);
            $paginator->setItemCountPerPage(10);
            if (!empty($page_no)) {
                $paginator->setCurrentPageNumber($page_no);
            }
            $this->view->paginator = $paginator;

            // get project location
            if ($paginator->getTotalItemCount() > 0) {
                $ids = array();
                foreach ($paginator as $project) {
                    $id = $project->getIdentity();
                    $ids[] = $id;
                    $project_temp[$id] = $project;
                }
                $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation($transactionsByLocationParams);
                $this->view->list = $project_temp;
            }

        }
    }

    public function getUsersAction()
    {

        $data = array();

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $autoRequest = $this->_getParam('text', null);

        $select = $usersTable->select()
            ->where('displayname  LIKE ? ', '%' . $autoRequest . '%')
            ->order('displayname ASC')
            ->limit('40');
        $users = $usersTable->fetchAll($select);

        foreach ($users as $user) {
            $user_photo = $this->view->itemPhoto($user, 'thumb.icon', '', array('nolazy' => true));
            $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $user_photo
            );
        }
        return $this->_helper->json($data);
    }

    public function getProjectsAction()
    {

        $data = array();

        $page_id = $this->_getParam('page_id', null);
        $autoRequest = $this->_getParam('text', null);

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        if (!empty($page_id)) {
            $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageId($page_id);

            if (count($projectsIds) > 0) {
                $select = $projectTable->select()
                    ->from($projectTableName, array('project_id', 'title', 'description'))
                    ->where("lower(title) LIKE ? OR lower(description) LIKE ? ", '%' . strtolower($autoRequest) . '%')
                    ->where("project_id IN (?)", $projectsIds)
                    ->order('title ASC')
                    ->limit('40');
                $projects = $projectTable->fetchAll($select);

                foreach ($projects as $project) {
                    $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $project->project_id);
                    $photo = $this->view->itemPhoto($item, 'thumb.icon', '', array('nolazy' => true));
                    $data[] = array(
                        'id' => $project->project_id,
                        'label' => $project->title,
                        'photo' => $photo
                    );
                }
            }
        }
        return $this->_helper->json($data);
    }
}
