<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_PaymentController extends Core_Controller_Action_Standard {

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
	 * @var Sitepage_Model_Page
	 */
	protected $_page;
	/**
	 * @var Payment_Model_Package
	 */
	protected $_package;
	protected $_success;

	public function init() {

		// Get user and session
		$this->_user = Engine_Api::_()->user()->getViewer();

		// If no user, redirect to home?
		if (!$this->_user || !$this->_user->getIdentity()) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		}
		// If there are no enabled gateways or packages, disable
		if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
			Engine_Api::_()->getDbtable('packages', 'sitepage')->getEnabledNonFreePackageCount() <= 0) {
			return $this->_forward('show-error');
		}
		$this->_session = new Zend_Session_Namespace('Payment_Sitepage');
		$this->_success = new Zend_Session_Namespace('Payment_Sitepage_Success');
	}

	public function indexAction() {
		return $this->_forward('gateway');
	}

	public function showErrorAction() {
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('sitepage_main');
		if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
			$this->view->show = 1;
		} else {
			$this->view->show = 0;
		}
	}

	public function gatewayAction() {
		// Get subscription
		$pageId = $this->_getParam('page_id', $this->_session->page_id);
		if (!$pageId ||
			!($page = Engine_Api::_()->getItem('sitepage_page', $pageId))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		}
		$this->view->page = $page;

		// Check subscription status
		if ($this->_checkPageStatus($page)) {
			return;
		}

		$existManageAdmin = Engine_Api::_()->sitepage()->isPageOwner($page);

		// Get subscription
		if (!$this->_user ||
			!($pageId = $this->_getParam('page_id', $this->_session->page_id)) ||
			!($page = Engine_Api::_()->getItem('sitepage_page', $pageId)) ||
			!$existManageAdmin ||
			!($package = Engine_Api::_()->getItem('sitepage_package', $page->package_id))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		}
		$this->view->page = $page;
		$this->view->package = $package;

		// Unset certain keys
		unset($this->_session->gateway_id);
		unset($this->_session->order_id);

		// Gateways
		$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
		$gatewaySelect = $gatewayTable->select()
			->where('enabled = ?', 1)
		;
		$gateways = $gatewayTable->fetchAll($gatewaySelect);

		$gatewayPlugins = array();
		foreach ($gateways as $gateway) {
			if ($gateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
				continue;
			}

			// Check billing cycle support
			if (!$package->isOneTime()) {
				$sbc = $gateway->getGateway()->getSupportedBillingCycles();
				if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
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
			->getNavigation('sitepage_main');

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$creditAllow = false;
		$isSitecreditEnabled = Engine_Api::_()->getDbtable("modules", "core")->isModuleEnabled("sitecredit");

		if ($isSitecreditEnabled) {

			if (!empty($viewer_id)) {

				$CreditModuleTable = Engine_Api::_()->getDbtable('modules', 'sitecredit');
				$select = $CreditModuleTable->select()->where('name = ?', 'sitepage')->where('flag=?', 'package');
				$creditModuleAllow = $CreditModuleTable->fetchRow($select);

				if (!empty($creditModuleAllow->integrated)) {
					$creditAllow = true;
					$creditLimit = $creditModuleAllow->percentage_checkout;
					if (empty($creditLimit)) {
						$creditAllow = false;
					} else {
						$creditValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecredit.value', 0);
						$this->view->maxcredit = @round($creditValue * (($package->price * $creditLimit) / 100));
					}
				}
			}
		}

		$creditSession = new Zend_Session_Namespace('credit_package_payment_' . $package->getType());

		if (!empty($creditSession->packagePaymentCreditDetail)) {
			$creditSession->packagePaymentCreditDetail = null;
		}
		$this->view->creditAllow = $creditAllow;

	}

	public function processAction() {
		// Get gateway
		$gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
		if (!$gatewayId ||
			!($gateway = Engine_Api::_()->getItem('sitepage_gateway', $gatewayId)) ||
			!($gateway->enabled)) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
		}
		$this->view->gateway = $gateway;

		// Get subscription
		$pageId = $this->_getParam('page_id', $this->_session->page_id);
		if (!$pageId ||
			!($page = Engine_Api::_()->getItem('sitepage_page', $pageId))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		}
		$this->view->page = $page;

		// Get package
		$package = $page->getPackage();
		if (!$package || $package->isFree()) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		}
		$this->view->package = $package;

		// Check subscription?
		if ($this->_checkPageStatus($page)) {
			return;
		}

		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
			$creditSession = new Zend_Session_Namespace('credit_package_payment_' . $package->getType());

			if (!empty($creditSession->packagePaymentCreditDetail)) {
				$creditDetail = unserialize($creditSession->packagePaymentCreditDetail);

				$discount_value = $creditDetail['credit_amount'];
				$credit_value = $creditDetail['credit_points'];

				$totalValue = $package->price - $discount_value;
				$package->price = @round($totalValue, 2);
			}

		}
		// Process
		// Create order
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
			'source_type' => 'sitepage_page',
			'source_id' => $page->page_id,
		));
		$this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

		// Unset certain keys
		unset($this->_session->package_id);
		unset($this->_session->page_id);
		unset($this->_session->gateway_id);

		// Get gateway plugin
		$this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
		$plugin = $gateway->getPlugin();

		if (Engine_Api::_()->hasModuleBootstrap('sitecredit')) {
			//SESSION SET OF CREDITS.
			$creditSession = new Zend_Session_Namespace('credit_package_payment_' . $package->getType());

			if (!empty($creditSession->packagePaymentCreditDetail)) {
				$creditDetail = unserialize($creditSession->packagePaymentCreditDetail);

				if (empty($package->price)) {
					$order = Engine_Api::_()->getItem('payment_order', $order_id);
					$page_user = $order->getUser();
					$page_page = $order->getSource();
					$page_package = $page_page->getPackage();
					$order->state = 'complete';
					$order->save();
					$page_page->onPaymentSuccess();
					if ($page_page->didStatusChange()) {
						// SEND ACTIVE MAIL HERE
						Engine_Api::_()->sitepage()->sendMail("ACTIVE", $page_page->page_id);
					}
					$param['type_id'] = $order->order_id;
					$param['credit_point'] = -$creditDetail['credit_points'];
					$param['type'] = strtolower($page_package->getType());
					$param['reason'] = 'used credits for package purchase';
					$param['resource_type'] = $order->source_type;
					$param['resource_id'] = $order->source_id;
					$credit_table = Engine_Api::_()->getDbtable('credits', 'sitecredit');
					$credit_table->insertCredit($param);
					$creditSession->packagePaymentCreditDetail = null;

					$this->_success->succes_id = $page_page->page_id;
					return $this->_finishPayment('active');
				}
			}
		}

		// Prepare host info
		$schema = 'http://';
		if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
			$schema = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];

		// Prepare transaction
		$params = array();
		$params['language'] = $this->_user->language;
		$localeParts = explode('_', $this->_user->language);
		if (count($localeParts) > 1) {
			$params['region'] = $localeParts[1];
		}
		$params['vendor_order_id'] = $order_id;
		$params['return_url'] = $schema . $host
		. $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitepage'), 'default')
			. '?order_id=' . $order_id
			. '&state=' . 'return';
		$params['cancel_url'] = $schema . $host
		. $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitepage'), 'default')
			. '?order_id=' . $order_id
			. '&state=' . 'cancel';
		$params['ipn_url'] = $schema . $host
		. $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'sitepage'), 'default')
			. '?order_id=' . $order_id;
		// Process transaction
		$transaction = $plugin->createPageTransaction($this->_user, $page, $package, $params);

		// Pull transaction params
		$this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
		$this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
		$this->view->transactionData = $transactionData = $transaction->getData();

		// Handle redirection
		if ($transactionMethod == 'GET') {
			$transactionUrl .= '?' . http_build_query($transactionData);
			return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
		}

		// Post will be handled by the view script
	}

	public function returnAction() {
		// Get order
		if (!$this->_user ||
			!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
			!($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
			$order->user_id != $this->_user->getIdentity() ||
			$order->source_type != 'sitepage_page' ||
			!($page = $order->getSource()) ||
			!($package = $page->getPackage()) ||
			!($gateway = Engine_Api::_()->getItem('sitepage_gateway', $order->gateway_id))) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		}

		$levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.level.createhost', 0);

		$package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);
		$LevelHost = $this->checkLevelHost($levelHost, 'sitepage');
		$PackagesHost = $this->checkPackageHost($package);
		if (($PackagesHost != $LevelHost)) {
			$this->_finishPayment('active');
		}

		$this->_page = $page;
		// Get gateway plugin
		$this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
		$plugin = $gateway->getPlugin();

		// Process return
		unset($this->_session->errorMessage);
		try {
			$status = $plugin->onPageTransactionReturn($order, $this->_getAllParams());
		} catch (Payment_Model_Exception $e) {
			$status = 'failure';
			$this->_session->errorMessage = $e->getMessage();
		}
		$this->_success->succes_id = $page->page_id;
		return $this->_finishPayment($status);
	}

	public function finishAction() {

		$this->view->status = $status = $this->_getParam('state');
		$this->view->error = $this->_session->errorMessage;
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('sitepage_main');
		if (isset($this->_success->succes_id)) {
			$this->view->id = $this->_success->succes_id;
			unset($this->_success->succes_id);
		}
	}

	protected function _checkPageStatus(
		Zend_Db_Table_Row_Abstract $page = null) {
		if (!$this->_user) {
			return false;
		}

		if (null == $page) {
			$page = Engine_Api::_()->getItem('sitepage_page', $this->_session->page_id);
		}

		if ($page->getPackage()->isFree()) {
			$this->_finishPayment('free');
			return true;
		}

		return false;
	}

	protected function _finishPayment($state = 'active') {
		$viewer = Engine_Api::_()->user()->getViewer();
		$page = $this->_page;

		// No user?
		//    if (!$this->_page) {
		//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		//    }
		// @todo: work all here Sitepage
		// Log the user in, if they aren't already
		//    if (($state == 'active' || $state == 'free') &&
		//        $this->_page &&
		//        !$viewer->getIdentity()) {
		//      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
		//      Engine_Api::_()->user()->setViewer();
		//      $viewer = $this->_user;
		//    }
		// Handle email verification or pending approval
		//    if ($viewer->getIdentity() && (!$viewer->enabled || !$viewer->verified)) {
		//      Engine_Api::_()->user()->setViewer(null);
		//      Engine_Api::_()->user()->getAuth()->getStorage()->clear();
		//
		//      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
		//      $confirmSession->approved = $viewer->enabled;
		//      $confirmSession->verified = $viewer->verified;
		//      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
		//    }
		//
		//    // Clear session
		//    $errorMessage = $this->_session->errorMessage;
		//    $userIdentity = $this->_session->user_id;
		//    $this->_session->unsetAll();
		//    $this->_session->user_id = $userIdentity;
		//    $this->_session->errorMessage = $errorMessage;
		// Redirect

		if ($state == 'free') {
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
		} else {
			return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'controller' => 'payment', 'state' => $state), 'sitepage_extended', true);
		}
	}

	public function checkLevelHost($object, $itemType) {
		$length = 7;
		$encodeorder = 0;
		$obj_length = strlen($object);
		if ($length > $obj_length) {
			$length = $obj_length;
		}

		for ($i = 0; $i < $length; $i++) {
			$encodeorder += ord($object[$i]);
		}
		$req_mode = $encodeorder % strlen($itemType);
		$encodeorder += ord($itemType[$req_mode]);
		$isEnabled = Engine_Api::_()->sitepage()->isEnabled();
		if (empty($isEnabled)) {
			return 0;
		} else {
			return $encodeorder;
		}
	}

	public function checkPackageHost($strKey) {
		$str = explode("-", $strKey);
		$str = $str[2];
		$char_array = array();
		for ($i = 0; $i < 6; $i++) {
			$char_array[] = $str[$i];
		}

		$key = array();
		foreach ($char_array as $value) {
			$v_a = ord($value);
			if ($v_a > 47 && $v_a < 58) {
				continue;
			}

			$possition = 0;
			$possition = $v_a % 10;
			if ($possition > 5) {
				$possition -= 4;
			}

			$key[] = $char_array[$possition];
		}
		$isEnabled = Engine_Api::_()->sitepage()->isEnabled();
		if (empty($isEnabled)) {
			return 0;
		} else {
			return $getStr = implode($key);
		}
	}

}
?>
