<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminTransactionController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminTransactionController extends Core_Controller_Action_Admin {

	public function indexAction() {

	        //GET NAVIGATION
	        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
	                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_transactions');
 
	        //PAYMENT FLOW CHECK
	        $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);

	        //FORM GENERATION
	        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Filter(); 

	        $this->view->gatewayOptions = Engine_Api::_()->sitecrowdfunding()->getGatewayOptions();

	        $page = $this->_getParam('page', 1);

	        //MAKE QUERY
	        $userTableName = Engine_Api::_()->getItemTable('user')->info('name');

	        $transactionTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
	        $transactionTableName = $transactionTable->info('name');

	        $select = $transactionTable->select()
	                ->setIntegrityCheck(false)
	                ->from($transactionTableName)
	                ->joinLeft($userTableName, "$transactionTableName.user_id = $userTableName.user_id", array('username'))
	                ->where("$transactionTableName.sender_type = 0")
	                ->group($transactionTableName . '.transaction_id');

	        // $this->view->transaction_state = $transactionTable->getTransactionState();

	        //GET VALUES
	        if ($formFilter->isValid($this->_getAllParams())) {
	            $values = $formFilter->getValues();
	        }

	        foreach ($values as $key => $value) {
	            if (null === $value) {
	                unset($values[$key]);
	            }
	        }

	        $values = array_merge(array('order' => 'transaction_id', 'order_direction' => 'DESC'), $values);

	        if (!empty($_POST['username'])) {
	            $user_name = $_POST['username'];
	        } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
	            $user_name = $_GET['username'];
	        } else {
	            $user_name = '';
	        }

	        if (!empty($_POST['date'])) {
	            $date = $_POST['date'];
	        } elseif (!empty($_GET['date']) && !isset($_POST['post_search'])) {
	            $date = $_GET['date'];
	        } else {
	            $date = '';
	        }

	        if (isset($_POST['min_amount']) && $_POST['min_amount'] != '') {
	            $min_amount = $_POST['min_amount'];
	        } elseif (isset($_GET['min_amount']) && $_GET['min_amount'] != '' && !isset($_POST['post_search'])) {
	            $min_amount = $_GET['min_amount'];
	        } else {
	            $min_amount = '';
	        }

	        if (isset($_POST['max_amount']) && $_POST['max_amount'] != '') {
	            $max_amount = $_POST['max_amount'];
	        } elseif (isset($_GET['max_amount']) && $_GET['max_amount'] != '' && !isset($_POST['post_search'])) {
	            $max_amount = $_GET['max_amount'];
	        } else {
	            $max_amount = '';
	        }

	        if (!empty($_POST['gateway_id'])) {
	            $gateway_id = $_POST['gateway_id'];
	        } elseif (!empty($_GET['gateway_id']) && !isset($_POST['post_search'])) {
	            $gateway_id = $_GET['gateway_id'];
	        } else {
	            $gateway_id = '';
	        }

	        if (!empty($_POST['state'])) {
	            $state = $_POST['state'];
	        } elseif (!empty($_GET['state']) && !isset($_POST['post_search'])) {
	            $state = $_GET['state'];
	        } else {
	            $state = '';
	        }

	        // searching
	        $this->view->username = $values['username'] = $user_name;
	        $this->view->date = $values['date'] = $date;
	        $this->view->min_amount = $values['min_amount'] = $min_amount;
	        $this->view->max_amount = $values['max_amount'] = $max_amount;
	        $this->view->gateway_id = $values['gateway_id'] = $gateway_id;
	        $this->view->state = $values['state'] = $state;


	        if (!empty($user_name)) {
	            $select->where($userTableName . '.username  LIKE ?', '%' . trim($user_name) . '%');
	        }

	        if (!empty($date)) {
	            $select->where("CAST($transactionTableName.timestamp AS DATE) =?", trim($date));
	        }

	        if ($min_amount != '') {
	            $select->where("$transactionTableName.amount >=?", trim($min_amount));
	        }

	        if ($max_amount != '') {
	            $select->where("$transactionTableName.amount <=?", trim($max_amount));
	        }

	        if (!empty($gateway_id)) {
	            $select->where($transactionTableName . '.gateway_id  =?', $gateway_id);
	        }

	        if (!empty($state)) {
	            $select->where($transactionTableName . '.state LIKE ? ', '%' . $state . '%');
	        }

	        $this->view->formValues = array_filter($values);
	        $this->view->assign($values);

	        $select->order((!empty($values['order']) ? $values['order'] : 'transaction_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

	        //MAKE PAGINATOR
	        $this->view->paginator = Zend_Paginator::factory($select);
	        $this->view->paginator->setItemCountPerPage(20);
	        $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
	    }

		public function detailUserTransactionAction() {
	        $this->view->transaction_id = $this->_getParam('transaction_id');
	        $this->view->transaction_obj = $transaction_obj = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->fetchRow(array('transaction_id =?' => $this->_getParam('transaction_id')));
	        $this->view->backer_id = $transaction_obj->source_id;
	        $this->view->user_obj = Engine_Api::_()->getItem('user', $transaction_obj->user_id);
	        $this->view->gateway_name = Engine_Api::_()->sitecrowdfunding()->getGatwayName($transaction_obj->gateway_id); 
	    }

	    public function adminTransactionAction() {

	         //GET NAVIGATION
	        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
	                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_transactions');
 

	         //PAYMENT FLOW CHECK
	        $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);

	        //FORM GENERATION
	        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Filter(); 
	         
	        $this->view->gatewayOptions = Engine_Api::_()->sitecrowdfunding()->getGatewayOptions();

	        $page = $this->_getParam('page', 1);

	        //MAKE QUERY
	        $userTableName = Engine_Api::_()->getItemTable('user')->info('name');

	        $transactionTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');

	        //$this->view->transaction_state = $transactionTable->getTransactionState(true);

	        $paymentRequestTable = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding');
	        $paymentRequestTableName = $paymentRequestTable->info('name');

	        $temTableName = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
	        $projectTableName = $temTableName->info('name');

	        $transactionTableName = $transactionTable->info('name');
	        $select = $paymentRequestTable->select()
	                ->setIntegrityCheck(false)
	                ->from($paymentRequestTableName, array("$paymentRequestTableName.request_id", "$paymentRequestTableName.project_id", "$paymentRequestTableName.response_amount", "$paymentRequestTableName.gateway_id", "$paymentRequestTableName.gateway_profile_id", "$paymentRequestTableName.response_date"))
	                ->join($transactionTableName, "($transactionTableName.source_id = $paymentRequestTableName.request_id)", array("$transactionTableName.transaction_id", "$transactionTableName.type", "$transactionTableName.state"))
	                ->joinLeft($userTableName, "$transactionTableName.user_id = $userTableName.user_id", array("$userTableName.user_id", "$userTableName.username"))
	                ->joinLeft($projectTableName, "$paymentRequestTableName.project_id = $projectTableName.project_id", array("$projectTableName.title"))
	                ->where("$transactionTableName.sender_type = 1")
	                ->where("$transactionTableName.source_type = ?", 'sitecrowdfunding_paymentrequest')
	                ->group($paymentRequestTableName . '.request_id');

	        //GET VALUES
	        if ($formFilter->isValid($this->_getAllParams())) {
	            $values = $formFilter->getValues();
	        }

	        foreach ($values as $key => $value) {
	            if (null === $value) {
	                unset($values[$key]);
	            }
	        }

	        $values = array_merge(array('order' => 'transaction_id', 'order_direction' => 'DESC'), $values);

	        if (!empty($_POST['title'])) {
	            $title = $_POST['title'];
	        } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
	            $title = $_GET['title'];
	        } else {
	            $title = '';
	        }

	        if (!empty($_POST['username'])) {
	            $user_name = $_POST['username'];
	        } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
	            $user_name = $_GET['username'];
	        } else {
	            $user_name = '';
	        }

	        if (!empty($_POST['date'])) {
	            $date = $_POST['date'];
	        } elseif (!empty($_GET['date']) && !isset($_POST['post_search'])) {
	            $date = $_GET['date'];
	        } else {
	            $date = '';
	        }

	        if (isset($_POST['response_min_amount']) && $_POST['response_min_amount'] != '') {
	            $response_min_amount = $_POST['response_min_amount'];
	        } elseif (isset($_GET['response_min_amount']) && $_GET['response_min_amount'] != '' && !isset($_POST['post_search'])) {
	            $response_min_amount = $_GET['response_min_amount'];
	        } else {
	            $response_min_amount = '';
	        }

	        if (isset($_POST['response_max_amount']) && $_POST['response_max_amount'] != '') {
	            $response_max_amount = $_POST['response_max_amount'];
	        } elseif (isset($_GET['response_max_amount']) && $_GET['response_max_amount'] != '' && !isset($_POST['post_search'])) {
	            $response_max_amount = $_GET['response_max_amount'];
	        } else {
	            $response_max_amount = '';
	        }

	        if (!empty($_POST['gateway_id'])) {
	            $gateway_id = $_POST['gateway_id'];
	        } elseif (!empty($_GET['gateway_id']) && !isset($_POST['post_search'])) {
	            $gateway_id = $_GET['gateway_id'];
	        } else {
	            $gateway_id = '';
	        }

	        if (!empty($_POST['state'])) {
	            $state = $_POST['state'];
	        } elseif (!empty($_GET['state']) && !isset($_POST['post_search'])) {
	            $state = $_GET['state'];
	        } else {
	            $state = '';
	        }

	        // searching
	        $this->view->title = $values['title'] = $title;
	        $this->view->username = $values['username'] = $user_name;
	        $this->view->date = $values['date'] = $date;
	        $this->view->response_min_amount = $values['response_min_amount'] = $response_min_amount;
	        $this->view->response_max_amount = $values['response_max_amount'] = $response_max_amount;
	        $this->view->gateway_id = $values['gateway_id'] = $gateway_id;
	        $this->view->state = $values['state'] = $state;

	        if (!empty($title)) {
	            $select->where($projectTableName . '.title  LIKE ?', '%' . trim($title) . '%');
	        }

	        if (!empty($user_name)) {
	            $select->where($userTableName . '.username  LIKE ?', '%' . trim($user_name) . '%');
	        }

	        if (!empty($_POST['date'])) {
	            $select->where("CAST($transactionTableName.timestamp AS DATE) =?", trim($date));
	        }

	        if ($response_min_amount != '') {
	            $select->where("$paymentRequestTableName.response_amount >=?", trim($response_min_amount));
	        }

	        if ($response_max_amount != '') {
	            $select->where("$paymentRequestTableName.response_amount <=?", trim($response_max_amount));
	        }

	        if (!empty($gateway_id)) {
	            $select->where($transactionTableName . '.gateway_id  =?', $gateway_id);
	        }

	        if (!empty($state)) {
	            $select->where($transactionTableName . '.state LIKE ? ', '%' . $state . '%');
	        }

	        //ASSIGN VALUES TO THE TPL
	        $this->view->formValues = array_filter($values);
	        $this->view->assign($values);

	        $select->order((!empty($values['order']) ? $values['order'] : 'transaction_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
 
	        //MAKE PAGINATOR
	        $this->view->paginator = Zend_Paginator::factory($select);
	        $this->view->paginator->setItemCountPerPage(20);
	        $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
	    } 


	public function viewAdminTransactionAction() {
	    $this->view->transaction_id = $this->_getParam('transaction_id');
	    $this->view->project_id = $this->_getParam('project_id');
	    $this->view->payment_gateway = $this->_getParam('payment_gateway');
	    $this->view->payment_type = $this->_getParam('payment_type');
	    $this->view->payment_state = $this->_getParam('payment_state');
	    $this->view->payment_amount = $this->_getParam('payment_amount');
	    $this->view->gateway_transaction_id = $this->_getParam('gateway_transaction_id');
	    $this->view->gateway_order_id = $this->_getParam('gateway_order_id');
	    $this->view->date = $this->_getParam('date');

	    $this->view->project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->view->project_id);
	    $this->view->userObj = Engine_Api::_()->getItem('user', $this->view->project->owner_id);

	    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitecrowdfunding.currency.symbol') ? Zend_Registry::get('sitecrowdfunding.currency.symbol') : null;
	    if (empty($currencySymbol)) {
	        $this->view->currencySymbol = Engine_Api::_()->sitecrowdfunding()->getCurrencySymbol();
	    }
	}

	public function backerCommissionTransactionAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_transactions');
 
        //PAYMENT FLOW CHECK
        $this->view->paymentToSiteadmin = $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);

        if ($paymentToSiteadmin) {
            return;
        }

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Filter();

        $this->view->currency_symbol = Engine_Api::_()->sitecrowdfunding()->getCurrencySymbol();

        $page = $this->_getParam('page', 1);

        //MAKE QUERY
        $userTableName = Engine_Api::_()->getItemTable('user')->info('name');
        $projectTableName = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->info('name');

        $transactionTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
        $transactionTableName = $transactionTable->info('name');

        $projectBillTable = Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding');
        $projectBillTableName = $projectBillTable->info('name');

        $select = $transactionTable->select()
                ->setIntegrityCheck(false)
                ->from($transactionTableName)
                ->joinLeft($userTableName, "$transactionTableName.user_id = $userTableName.user_id", array("$userTableName.username"))
                ->joinLeft($projectBillTableName, "$transactionTableName.source_id = $projectBillTableName.projectbill_id", array('project_id', "message", "status"))
                ->joinLeft($projectTableName, "$projectBillTableName.project_id = $projectTableName.project_id", array("$projectTableName.title"))
                ->where("$transactionTableName.sender_type = 2")
                ->where("$transactionTableName.source_type = ?", 'sitecrowdfunding_projectbill');

        //GET VALUES
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array('order' => 'transaction_id', 'order_direction' => 'DESC'), $values);

        if (!empty($_POST['title'])) {
            $title = $_POST['title'];
        } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
            $title = $_GET['title'];
        } else {
            $title = '';
        }

        if (!empty($_POST['username'])) {
            $user_name = $_POST['username'];
        } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
            $user_name = $_GET['username'];
        } else {
            $user_name = '';
        }

        if (!empty($_POST['starttime']) && !empty($_POST['starttime']['date'])) {
            $values['from'] = $_POST['starttime']['date'];
        } elseif (!empty($_GET['starttime']) && !empty($_GET['starttime']['date']) && !isset($_POST['post_search'])) {
            $values['from'] = $_GET['starttime']['date'];
        } else {
            $values['from'] = '';
        }

        if (!empty($_POST['endtime']) && !empty($_POST['endtime']['date'])) {
            $values['to'] = $_POST['endtime']['date'];
        } elseif (!empty($_GET['endtime']) && !empty($_GET['endtime']['date']) && !isset($_POST['post_search'])) {
            $values['to'] = $_GET['endtime']['date'];
        } else {
            $values['to'] = '';
        }

        if (isset($_POST['min_amount']) && $_POST['min_amount'] != '') {
            $min_amount = $_POST['min_amount'];
        } elseif (isset($_GET['min_amount']) && $_GET['min_amount'] != '' && !isset($_POST['post_search'])) {
            $min_amount = $_GET['min_amount'];
        } else {
            $min_amount = '';
        }

        if (isset($_POST['max_amount']) && $_POST['max_amount'] != '') {
            $max_amount = $_POST['max_amount'];
        } elseif (isset($_GET['max_amount']) && $_GET['max_amount'] != '' && !isset($_POST['post_search'])) {
            $max_amount = $_GET['max_amount'];
        } else {
            $max_amount = '';
        }

        if (!empty($title)) {
            $select->where($projectTableName . '.title  LIKE ?', '%' . trim($title) . '%');
        }

        if (!empty($user_name)) {
            $select->where($userTableName . '.username  LIKE ?', '%' . trim($user_name) . '%');
        }

        if ($min_amount != '') {
            $select->where("$transactionTableName.amount >=?", trim($min_amount));
        }

        if ($max_amount != '') {
            $select->where("$transactionTableName.amount <=?", trim($max_amount));
        }

        if (isset($values['from']) && !empty($values['from'])) {
            $select->where("CAST($transactionTableName.timestamp AS DATE) >=?", trim($values['from']));
        }

        if (isset($values['to']) && !empty($values['to'])) {
            $select->where("CAST($transactionTableName.timestamp AS DATE) <=?", trim($values['to']));
        }

        $select->order((!empty($values['order']) ? $values['order'] : 'transaction_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        //MAKE PAGINATOR
        $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(20);
        $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);

        // searching
        $this->view->title = $values['title'] = $title;
        $this->view->username = $values['username'] = $user_name;
        $this->view->starttime = $values['from'];
        $this->view->endtime = $values['to'];
        $this->view->min_amount = $values['min_amount'] = $min_amount;
        $this->view->max_amount = $values['max_amount'] = $max_amount;

        $this->view->formValues = array_filter($values);
        $this->view->assign($values);
    }

    public function detailBackerCommissionTransactionAction() {
        $this->view->transaction_id = $transaction_id = $this->_getParam('transaction_id');
        $project_id = $this->_getParam('project_id');
        $this->view->message = $this->_getParam('message');
        $this->view->transaction_obj = $transaction_obj = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->fetchRow(array('transaction_id =?' => $transaction_id));
        $this->view->project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
    }
}