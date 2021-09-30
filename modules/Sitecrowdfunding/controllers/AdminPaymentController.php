<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPaymentController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminPaymentController extends Core_Controller_Action_Admin {

    protected $_user_id;
    //User_Model_User
    protected $_user;
    // Zend_Session_Namespace
    protected $_session;
    // Payment_Model_Userad
    protected $_user_request;
    protected $_success;

    public function init() {

        $this->_user = Engine_Api::_()->user()->getViewer();
        $this->_user_id = $this->_user->getIdentity();

        // Get user and session
        $this->_session = new Zend_Session_Namespace('Payment_Sitecrowdfundings');
        $this->_success = new Zend_Session_Namespace('Payment_Success');

        // Check viewer and user
        if (!$this->_user_request) {
            if (!empty($this->_session->user_request_id)) {
                $this->_user_request = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $this->_session->user_request_id);
            }
        }
    }

    public function indexAction() {

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_paymentrequests');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Filter();

        $page = $this->_getParam('page', 1);

        $paymentRequestTable = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding');
        $paymentRequestTableName = $paymentRequestTable->info('name');

        $pageTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $pageTableName = $pageTable->info('name');

        $select = $paymentRequestTable->select()
                ->setIntegrityCheck(false)
                ->from($paymentRequestTableName)
                ->joinLeft($pageTableName, "$paymentRequestTableName.project_id = $pageTableName.project_id", array("$pageTableName.title"))
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

        $values = array_merge(array('order' => 'request_id', 'order_direction' => 'DESC'), $values);

        if (!empty($_POST['title'])) {
            $title = $_POST['title'];
        } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
            $title = $_GET['title'];
        } else {
            $title = '';
        }

        if (!empty($_POST['request_date'])) {
            $request_date = $_POST['request_date'];
        } elseif (!empty($_GET['request_date']) && !isset($_POST['post_search'])) {
            $request_date = $_GET['request_date'];
        } else {
            $request_date = '';
        }

        if (!empty($_POST['response_date'])) {
            $response_date = $_POST['response_date'];
        } elseif (!empty($_GET['response_date']) && !isset($_POST['post_search'])) {
            $response_date = $_GET['response_date'];
        } else {
            $response_date = '';
        }
        if (isset($_POST['request_min_amount']) && $_POST['request_min_amount'] != '') {
            $request_min_amount = $_POST['request_min_amount'];
        } elseif (isset($_GET['request_min_amount']) && $_GET['request_min_amount'] != '' && !isset($_POST['post_search'])) {
            $request_min_amount = $_GET['request_min_amount'];
        } else {
            $request_min_amount = '';
        }

        if (isset($_POST['request_max_amount']) && $_POST['request_max_amount'] != '') {
            $request_max_amount = $_POST['request_max_amount'];
        } elseif (isset($_GET['request_max_amount']) && $_GET['request_max_amount'] != '' && !isset($_POST['post_search'])) {
            $request_max_amount = $_GET['request_max_amount'];
        } else {
            $request_max_amount = '';
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
        } elseif (isset($_GET['response_max_amount']) && !empty($_GET['response_max_amount']) && !isset($_POST['post_search'])) {
            $response_max_amount = $_GET['response_max_amount'];
        } else {
            $response_max_amount = '';
        }

        if (!empty($_POST['request_status'])) {
            $request_status = $_POST['request_status'];
        } elseif (!empty($_GET['request_status']) && !isset($_POST['post_search'])) {
            $request_status = $_GET['request_status'];
        } else {
            $request_status = '';
        }


        // searching
        $this->view->title = $values['title'] = $title;
        $this->view->request_date = $values['request_date'] = $request_date;
        $this->view->response_date = $values['response_date'] = $response_date;
        $this->view->request_min_amount = $values['request_min_amount'] = $request_min_amount;
        $this->view->request_max_amount = $values['request_max_amount'] = $request_max_amount;
        $this->view->response_min_amount = $values['response_min_amount'] = $response_min_amount;
        $this->view->response_max_amount = $values['response_max_amount'] = $response_max_amount;
        $this->view->request_status = $values['request_status'] = $request_status;

        if (!empty($title)) {
            $select->where($pageTableName . '.title  LIKE ?', '%' . trim($title) . '%');
        }

        if (!empty($request_date)) {
            $select->where("CAST($paymentRequestTableName.request_date AS DATE) =?", trim($request_date));
        }

        if (!empty($response_date)) {
            $select->where("CAST($paymentRequestTableName.response_date AS DATE) =?", trim($response_date));
        }

        if ($request_min_amount != '') {
            $select->where("$paymentRequestTableName.request_amount >=?", trim($request_min_amount));
        }

        if ($request_max_amount != '') {
            $select->where("$paymentRequestTableName.request_amount <=?", trim($request_max_amount));
        }

        if ($response_min_amount != '') {
            $select->where("$paymentRequestTableName.response_amount >=?", trim($response_min_amount));
        }

        if ($response_max_amount != '') {
            $select->where("$paymentRequestTableName.response_amount <=?", trim($response_max_amount));
        }

        if (!empty($request_status)) {
            $request_status--;
            $select->where($paymentRequestTableName . '.request_status LIKE ? ', '%' . $request_status . '%');
        }

        //ASSIGN VALUES TO THE TPL
        $this->view->formValues = array_filter($values);
        $this->view->assign($values);

        $select->order((!empty($values['order']) ? $values['order'] : 'request_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    public function processPaymentAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_paymentrequests');

        $this->view->payment_req_obj = $payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $this->_getParam('request_id'));

        if (empty($payment_req_obj))
            return $this->_forward('notfound', 'error', 'core');

        if ($payment_req_obj->request_status == 1) {
            $this->view->sitecrowdfunding_payment_req_delete = true;
            return;
        }
        $gateway_id = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->isPayPalGatewayEnable($payment_req_obj->project_id);
        $gateway = Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $gateway_id);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $payment_req_obj->project_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $this->view->project->owner_id);
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
        if (empty($tempPaymentProcessFlag)) {
            return;
        }
        if (empty($project))
            return $this->_forward('notfound', 'error', 'core');

        if (empty($gateway_id)) {
            $this->view->gateway_disable = 1;
        } else {
            $this->_session = new Zend_Session_Namespace('Payment_Sitecrowdfundings');
            $this->_session->user_request_id = $payment_req_obj->request_id;

            $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Payment_PaymentTransfer(array('amount' => @round($payment_req_obj->request_amount, 2)));

            if (!$this->getRequest()->isPost()) {
                return;
            }

            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            $currency_symbol = Engine_Api::_()->sitecrowdfunding()->getCurrencySymbol();

            $values = $form->getValues();

            if (@round($values['user_req_amount'], 2) != @round($payment_req_obj->request_amount, 2)) {
                $user_previous_req_amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($values['user_req_amount']);
                $user_current_req_amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($payment_req_obj->request_amount);
                $form->addError("Seller has changed the requested amount from $user_previous_req_amount to $user_current_req_amount. So, please review the changed amount before making the payment.");

                $form->user_req_amount->setValue($payment_req_obj->request_amount);
                return;
            }

            if ($values['amount'] > $payment_req_obj->request_amount) {
                $form->addError('You can not approve an amount greater than the requested amount. So, please enter an amount less than or equal to the requested amount to approve the payment.');
                return;
            }
            $paypalGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway('Payment_Plugin_Gateway_PayPal');
            $pgi = 2;
            if ($paypalGateway) {
                $pgi = $paypalGateway->gateway_id;
            }
            $payment_gateway_id = Engine_Api::_()->hasModuleBootstrap('sitegateway') ? Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gateway->plugin, 'columnName' => 'gateway_id')) : $pgi;

            //UPDATE 
            Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding')->update(array(
                'response_amount' => @round($values['amount'], 2),
                'response_message' => $values['response_message'],
                'response_date' => new Zend_Db_Expr('NOW()'),
                'payment_flag' => 1,
                'gateway_id' => $payment_gateway_id
                    ), array('request_id =? ' => $payment_req_obj->request_id));

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitecrowdfunding', 'controller' => 'payment', 'action' => 'process', 'gateway_id' => $gateway_id), "admin_default", true);
        }
    }

    public function processAction() {
        if (!$this->_user_request) {
            $this->_session->unsetAll();
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        $request_id = $this->_session->user_request_id;
        $user_gateway_id = $this->_getParam('gateway_id');

        if (empty($user_gateway_id)) {
            $this->view->user_gateway_disable = 1;
            return;
        }

        // Get order
        if (!$request_id ||
                !($user_request = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id))) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        // Process
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->request_id)) {
            $previousOrder = $ordersTable->find($this->_session->request_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }
        $gateway = Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $user_gateway_id);

        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();
        $paypalGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway('Payment_Plugin_Gateway_PayPal');
        $pgi = 2;
        if ($paypalGateway) {
            $pgi = $paypalGateway->gateway_id;
        }
        $gateway_id = Engine_Api::_()->hasModuleBootstrap('sitegateway') ? Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gateway->plugin, 'columnName' => 'gateway_id')) : $pgi;

        // Create order
        $ordersTable->insert(array(
            'user_id' => $this->_user_id,
            'gateway_id' => $gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'sitecrowdfunding_paymentrequest',
            'source_id' => $request_id,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];

        // Prepare transaction
        $params = array();
        $params['language'] = $this->_user->language;
        $params['vendor_order_id'] = $order_id;

        $params['return_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitecrowdfunding'), 'admin_default', true)
                . '?order_id=' . $order_id
                . '&state=' . 'return'
                . '&gateway_id=' . $user_gateway_id;
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitecrowdfunding'), 'admin_default', true)
                . '?order_id=' . $order_id
                . '&state=' . 'cancel'
                . '&gateway_id=' . $user_gateway_id;
        $params['ipn_url'] = $schema . $host
                . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'admin_default', true)
                . '?order_id=' . $order_id
                . '&gateway_id=' . $user_gateway_id;

        $params['source_type'] = 'sitecrowdfunding_paymentrequest';

        $currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $supportedCurrency = $gatewayPlugin->getSupportedCurrencies();
        $isSupported = true;
        if (!in_array($currentCurrency, $supportedCurrency))
            $isSupported = false;
        $this->view->isSupported = $isSupported;
        if ($isSupported) {
            // Process transaction
            $transaction = $plugin->createUserRequestTransaction($this->_user, $request_id, $params);

            $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
            $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
            $this->view->transactionData = $transactionData = $transaction->getData();

            unset($this->_session->user_request_id);

            // Handle redirection
            if ($transactionMethod == 'GET') {
                $transactionUrl .= '?' . http_build_query($transactionData);
                return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
            }
        }
    }

    public function returnAction() {

        $user_gateway_id = $this->_getParam('gateway_id');
        // Get order
        if (!$this->_user ||
                !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
                !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
                $order->user_id != $this->_user->getIdentity() ||
                $order->source_type != 'sitecrowdfunding_paymentrequest' ||
                !($user_request = $order->getSource()) ||
                !($gateway = Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $user_gateway_id))) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        // Get gateway plugin
        $plugin = $gateway->getPlugin();
        unset($this->_session->errorMessage);

        try {
            $status = $plugin->onUserRequestTransactionReturn($order, $this->_getAllParams());
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        $this->_success->succes_id = $user_request->request_id;
        return $this->_finishPayment($status);
    }

    protected function _finishPayment($state = 'active') {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->_user;

        // No user?
        if (!$this->_user) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        // Log the user in, if they aren't already
        if (($state == 'active' || $state == 'free') &&
                $this->_user &&
                !$this->_user->isSelf($viewer) &&
                !$viewer->getIdentity()) {
            Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
            Engine_Api::_()->user()->setViewer();
        }

        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $userIdentity = $this->_session->user_id;
        $this->_session->unsetAll();
        $this->_session->user_id = $userIdentity;
        $this->_session->errorMessage = $errorMessage;

        // Redirect
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
        }
    }

    public function finishAction() {
        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_paymentrequests');

        $this->view->state = $state = $this->_getParam('state');

        $this->view->error = $error_message = $this->_session->errorMessage;

        if (isset($this->_success->succes_id)) {
            $request_id = $this->_success->succes_id;

            if ($state == 'active') {
                $payment_request_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);
                $project_id = $payment_request_obj->project_id;
                $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                $user = Engine_Api::_()->getItem('user', $project->owner_id);
                $viewer = Engine_Api::_()->user()->getViewer();
                $currency_symbol = Engine_Api::_()->sitecrowdfunding()->getCurrencySymbol();

                $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                $project_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $project->getHref() . '">' . $project->getTitle() . '</a>';

                /***
                 *
                 * send notification and email to all project admins
                 *
                 ***/
                $list = $project->getLeaderList();
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

                $selectUsers = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array)$selectLeaders)
                    ->order('displayname ASC');

                $adminMembers = $userTable->fetchAll($selectUsers);

                foreach($adminMembers as $adminMember){
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminMember, 'sitecrowdfunding_payment_request_approve', array(
                        'object_title' => $project->getTitle(),
                        'object_name' => $project_name,
                        'response_amount' => Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($payment_request_obj->response_amount),
                        'member_name' => $adminMember()->getTitle(),
                    ));
                }
            }

            unset($this->_success->succes_id);
        }
    }

    public function detailTransactionAction() {
        $transaction_id = $this->_getParam('transaction_id');
        $transaction = Engine_Api::_()->getItem('sitecrowdfunding_transaction', $transaction_id);
        $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

        $link = null;
        if ($this->_getParam('show-parent')) {
            if (!empty($transaction->gateway_parent_transaction_id)) {
                $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
            }
        } else {
            if (!empty($transaction->gateway_transaction_id)) {
                $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
            }
        }

        if ($link) {
            return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
        } else {
            die();
        }
    }

    public function deletePaymentRequestAction() {
        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);

        $project_id = $payment_req_obj->project_id;
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($request_id) || empty($payment_req_obj) || empty($project))
            return $this->_forward('notfound', 'error', 'core');

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('project_id = ?' => $project_id))->remaining_amount;
        $remaining_amount += $payment_req_obj->request_amount;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $payment_req_obj->request_status = 1;
            $payment_req_obj->save();

            //UPDATE REMAINING AMOUNT
            $remaining_amount_table_obj->update(
                    array('remaining_amount' => $remaining_amount), array('project_id =? ' => $project_id));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => 'Payment request deleted successfully.'
        ));
    }

    public function viewPaymentRequestAction() {
        $this->view->request_id = $request_id = $this->_getParam('request_id', null);
        $this->view->payment_req_obj = $payment_req_obj = Engine_Api::_()->getItem('sitecrowdfunding_paymentrequest', $request_id);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $payment_req_obj->project_id);
        $this->view->user_obj = Engine_Api::_()->getItem('user', $this->view->project->owner_id);

        if (empty($project) || empty($request_id) || empty($payment_req_obj))
            return $this->_forward('notfound', 'error', 'core');

        $this->view->currencySymbol = Engine_Api::_()->sitecrowdfunding()->getCurrencySymbol();
    }

}
