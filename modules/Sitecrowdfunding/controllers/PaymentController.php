<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_PaymentController extends Core_Controller_Action_Standard {

	/**
	 * @var User_Model_User
	 */
	protected $_user;

	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session;

	/**
	 * @var Payment_Model_Order
	 */
	protected $_order;

	/**
	 * @var Payment_Model_Gateway
	 */
	protected $_gateway;

	/**
	 * @var Sitecrowdfunding_Model_Project
	 */
	protected $_project;

	/**
	 * @var Payment_Model_Package
	 */
	protected $_package;
	protected $_success;

	public function init() {

		// Get user and session
		$this->_user = Engine_Api::_()->user()->getViewer();

		//AUTHORIZATION CHECK
		if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid()) {
			return;
		}

		// If no user, redirect to home?
		if (!$this->_user || !$this->_user->getIdentity()) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		}

		// If there are no enabled gateways or packages, disable
		if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
			Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding')->getEnabledNonFreePackageCount() <= 0) {
			return $this->_forward('show-error');
		}
		$this->_session = new Zend_Session_Namespace('Payment_Sitecrowdfunding');
		$this->_success = new Zend_Session_Namespace('Payment_Sitecrowdfunding_Success');

	}

	public function indexAction() {
		return $this->_forward('gateway');
	}

	public function showErrorAction() {
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation("sitecrowdfunding_main");
		if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
			$this->view->show = 1;
		} else {
			$this->view->show = 0;
		}
	}

	public function gatewayAction() {
		// Get subscription
		$projectId = $this->_getParam('project_id', $this->_session->project_id);
		if (!$projectId || !($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $projectId))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		}
		$this->view->project = $project;

		// Check subscription status
		if ($this->_checkProjectStatus($project)) {
			return;
		}

		// Get subscription
		if (!$this->_user ||
			!($projectId = $this->_getParam('project_id', $this->_session->project_id)) ||
			!($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $projectId)) || !($package = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		}
		$this->view->project = $project;
		$this->view->package = $package;

		// Unset certain keys
		unset($this->_session->gateway_id);
		unset($this->_session->order_id);

		// Gateways
		$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
		$gatewaySelect = $gatewayTable->select()
			->where('enabled = ?', 1)
			->where('plugin not in(?)', array('Sitegateway_Plugin_Gateway_MangoPay'));
		$gateways = $gatewayTable->fetchAll($gatewaySelect);
		$gatewayPlugins = array();

		foreach ($gateways as $gateway) {
			// Check billing cycle support
			if (!$package->isOneTime()) {
				$recurrence_type = $package->recurrence_type;
				$sbc = $gateway->getGateway()->getSupportedBillingCycles();
				if (!in_array($recurrence_type, array_map('strtolower', $sbc))) {
					continue;
				}
			}
			$gatewayPlugins[] = array(
				'gateway' => $gateway,
				'plugin' => $gateway->getGateway(),
			);
		}

		$this->view->gateways = $gatewayPlugins;
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation("sitecrowdfunding_main");
	}

	public function processAction() {

		//GET GATEWAY
		$gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
		if (!$gatewayId ||
			!($gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $gatewayId)) ||
			!($gateway->enabled)) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
		}
		$this->view->gateway = $gateway;

		//GET SUBSCRIPTION
		$projectId = $this->_getParam('project_id', $this->_session->project_id);
		if (!$projectId ||
			!($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $projectId))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		}
		$this->view->project = $project;

		//GET PACKAGE
		$package = $project->getPackage();
		if (!$package || $package->isFree()) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		}
		$this->view->package = $package;

		//CHECK SUBSCRIPTION?
		if ($this->_checkProjectStatus($project)) {
			return;
		}

		//PROCESS
		//CREATE ORDER
		$ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
		if (!empty($this->_session->order_id)) {
			$previousOrder = $ordersTable->find($this->_session->order_id)->current();
			if ($previousOrder && $previousOrder->state == 'pending') {
				$previousOrder->state = 'incomplete';
				$previousOrder->save();
			}
		}
		$ordersTable->insert(array(
			'user_id' => $this->_user->getIdentity(),
			'gateway_id' => $gateway->gateway_id,
			'state' => 'pending',
			'creation_date' => new Zend_Db_Expr('NOW()'),
			'source_type' => 'sitecrowdfunding_project',
			'source_id' => $project->project_id,
		));
		$this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

		//UNSET CERTAIN KEYS
		unset($this->_session->package_id);
		unset($this->_session->project_id);
		unset($this->_session->gateway_id);

		//GET GATEWAY PLUGIN
		$this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
		$plugin = $gateway->getPlugin();

		//PREPARE HOST INFO
		$schema = 'http://';
		if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
			$schema = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];

		//PREPARE TRANSACTION
		$params = array();
		$params['language'] = $this->_user->language;
		$localeParts = explode('_', $this->_user->language);
		if (count($localeParts) > 1) {
			$params['region'] = $localeParts[1];
		}
		$params['vendor_order_id'] = $order_id;
		$params['return_url'] = $schema . $host
		. $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitecrowdfunding'), 'default')
			. '?order_id=' . $order_id
			. '&state=' . 'return';
		$params['cancel_url'] = $schema . $host
		. $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitecrowdfunding'), 'default')
			. '?order_id=' . $order_id
			. '&state=' . 'cancel';
		$params['ipn_url'] = $schema . $host
		. $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'sitecrowdfunding'), 'default')
			. '&order_id=' . $order_id;
		//PROCESS TRANSACTION

		$transaction = $plugin->createProjectTransaction($this->_user, $project, $package, $params);
		//PULL TRANSACTION PARAMS
		$this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
		$this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
		$this->view->transactionData = $transactionData = $transaction->getData();

		//HANDLE REDIRECTION
		if ($transactionMethod == 'GET') {
			$transactionUrl .= '?' . http_build_query($transactionData);
			return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
		}

		//POST WILL BE HANDLED BY THE VIEW SCRIPT
	}

	public function returnAction() {

		//GET ORDER
		if (!$this->_user ||
			!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
			!($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
			$order->user_id != $this->_user->getIdentity() ||
			$order->source_type != 'sitecrowdfunding_project' ||
			!($project = $order->getSource()) ||
			!($package = $project->getPackage()) ||
			!($gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $order->gateway_id))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		}

		$this->_project = $project;
		//GET GATEWAY PLUGIN
		$this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
		$plugin = $gateway->getPlugin();

		//PROCESS RETURN
		unset($this->_session->errorMessage);
		try {
			$status = $plugin->onPageTransactionReturn($order, $this->_getAllParams());
		} catch (Payment_Model_Exception $e) {
			$status = 'failure';
			$this->_session->errorMessage = $e->getMessage();
		}
		$this->_success->succes_id = $project->project_id;
		return $this->_finishPayment($status);
	}

	public function finishAction() {

		$this->view->status = $status = $this->_getParam('state');
		$this->view->error = $this->_session->errorMessage;
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation("sitecrowdfunding_main");
		if (isset($this->_success->succes_id)) {
			$this->view->id = $this->_success->succes_id;
			unset($this->_success->succes_id);
		}
	}

	protected function _checkProjectStatus(
		Zend_Db_Table_Row_Abstract $project = null) {
		if (!$this->_user) {
			return false;
		}

		if (null == $project) {
			$project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->_session->project_id);
		}

		if ($project->getPackage()->isFree()) {
			$this->_finishPayment('free');
			return true;
		}

		return false;
	}

	protected function _finishPayment($state = 'active') {

		//REDIRECT
		if ($state == 'free') {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitecrowdfunding_general", true);
		} else {
			return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'controller' => 'payment', 'state' => $state), "sitecrowdfunding_extended", true);
		}
	}

}