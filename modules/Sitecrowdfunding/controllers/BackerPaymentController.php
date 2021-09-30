<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BackerPaymentController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_BackerPaymentController extends Core_Controller_Action_Standard {

	protected $_user;
	protected $_session;
	protected $_order;
	protected $_gateway;
	protected $_subscription;
	protected $_user_order;
	protected $_success;
	protected $_project_gateway_id;

	public function init() {

		// Get user and session
		$this->_user = Engine_Api::_()->user()->getViewer();
		$this->_session = new Zend_Session_Namespace('Payment_Sitecrowdfunding');
		$this->_success = new Zend_Session_Namespace('Payment_Success');

		// Check viewer and user
		if (!$this->_user_order) {
			if (!empty($this->_session->user_order_id)) {
				$this->_user_order = Engine_Api::_()->getItem('sitecrowdfunding_backer', $this->_session->user_order_id);
			}
		}
	}

	public function processAction() {
		if (!$this->_user_order) {
			$this->_session->unsetAll();
			return $this->_helper->redirector->gotoRoute(array(), 'sitecrowdfunding_general', true);
		}
		$parent_order_id = $this->_session->user_order_id;
		if (empty($this->_session->checkout_project_id)) {
			return $this->_helper->redirector->gotoRoute(array(), 'sitecrowdfunding_general', true);
		}
		$project_id = $this->_session->checkout_project_id;
        $this->view->paypal_type = $paypal_type = $this->_session->paypal_type;
		$plugin = '';
		$gatewayObject = Engine_Api::_()->getItem('payment_gateway', $this->_user_order->gateway_id);
		if (!empty($gatewayObject)) {
			$plugin = $gatewayObject->plugin;
		}

		if (empty($plugin)) {
			return $this->_helper->redirector->gotoRoute(array(), 'sitecrowdfunding_general', true);
		}
		$isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
		$allowedPaymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
		$isPaymentToSiteEnable = $allowedPaymentMethod == 'normal' && $isPaymentToSiteEnable;
		$project_gateway_id = $gatewayObject->gateway_id;
		if (empty($isPaymentToSiteEnable)) {
			$project_gateway_id = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getGatewayId($project_id, $plugin);
		}

		if (empty($project_gateway_id)) {
			return $this->_helper->redirector->gotoRoute(array(), 'sitecrowdfunding_general', true);
		} else {
			$this->_project_gateway_id = $project_gateway_id;
            $gateway = Engine_Api::_()->getItem("sitecrowdfunding_projectGateway", $project_gateway_id);
            $this->view->project_payment_set_email = $gateway->email;
		}

		// Get order
		if (!$parent_order_id ||
			!($user_order = Engine_Api::_()->getItem('sitecrowdfunding_backer', $parent_order_id))) {
			return $this->_helper->redirector->gotoRoute(array(), 'sitecrowdfunding_general', true);
		}

		// Process
		$ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
		if (!empty($this->_session->order_id)) {
			$previousOrder = $ordersTable->find($this->_session->order_id)->current();
			if ($previousOrder && $previousOrder->state == 'pending') {
				$previousOrder->state = 'incomplete';
				$previousOrder->save();
			}
		}

		$sourceType = 'sitecrowdfunding_backer';
		$sourceId = $parent_order_id;
		$gateway_id = $user_order->gateway_id;

		// Create order
		$ordersTable->insert(array(
			'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
			'gateway_id' => $gateway_id,
			'state' => 'pending',
			'creation_date' => new Zend_Db_Expr('NOW()'),
			'source_type' => $sourceType,
			'source_id' => $sourceId,
		));

		$this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();
		//PAYMENT_ORDER TABLE'S ORDER_ID SAVED IN BACKERS TABLE TO UPDATE ORDERS TABLE STATE ON PREAPPROVAL PAYMENT PAYOUT
		$user_order->order_id = $order_id;
		$user_order->save();
		$gateway = $gatewayObject;
		if ($isPaymentToSiteEnable) {
			$gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $project_gateway_id);
		} else {
			$gateway = Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $project_gateway_id);
		}

		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && ($gateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay')) {
			$gateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway($gateway->plugin);
		}

		// Get gateway plugin
		$this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();

		$plugin = $gateway->getPlugin();
		// Prepare host info
		$schema = 'http://';
		if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
			$schema = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];

		// Prepare transaction
		$params = array();
//    $params['language'] = $this->_user->language;
		$params['vendor_order_id'] = $order_id;
		$params['return_url'] = $schema . $host
		. $this->view->url(array('action' => 'return', 'controller' => 'backer-payment', 'module' => 'sitecrowdfunding'), 'default')
			. '?order_id=' . $order_id
			. '&state=' . 'return';
		$params['cancel_url'] = $schema . $host
		. $this->view->url(array('action' => 'return', 'controller' => 'backer-payment', 'module' => 'sitecrowdfunding'), 'default')
			. '?order_id=' . $order_id
			. '&state=' . 'cancel';
		$params['ipn_url'] = $schema . $host
		. $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
			. '?order_id=' . $order_id;

		if (!empty($project_id)) {
			$params['project_id'] = $project_id;
			$params['return_url'] .= '&project_id=' . $project_id . '&project_gateway_id=' . $project_gateway_id;
			$params['cancel_url'] .= '&project_id=' . $project_id . '&project_gateway_id=' . $project_gateway_id;
			$params['ipn_url'] .= '&project_id=' . $project_id . '&project_gateway_id=' . $project_gateway_id;
		}
		$params['source_type'] = $sourceType;
        $params['return_url'] .= '&paypal_type=' . $paypal_type;
        $params['cancel_url'] .= '&paypal_type=' . $paypal_type;
		// Process transaction
		$transaction = $plugin->createUserOrderTransaction($parent_order_id, $params, $this->_user);

		// get amount and currency
        $order = Engine_Api::_()->getItem($params['source_type'], $parent_order_id);
        $this->view->amount = $order->amount;
        $this->view->return_url = $params['return_url'] ;
        $this->view->cancel_url = $params['cancel_url'] ;


		$type = 'PAYMENT';
		if (isset($transaction->preapprovalKey) && !empty($transaction->preapprovalKey)) {
			$type = 'PREAPPROVAL';
		}
		$this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl($type);

		$this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
		$this->view->transactionData = $transactionData = $transaction->getData();
		unset($this->_session->user_order_id);
		// Handle redirection
		if ($transactionMethod == 'GET' && !Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) {
			if (isset($transaction->mangoPayRedirectUrl) && strlen($transaction->mangoPayRedirectUrl) > 0) {
				$transactionUrl = $transaction->mangoPayRedirectUrl;
			} elseif (isset($transaction->payKey)) {
				$transactionUrl .= $transaction->payKey;
				$this->_session->payKey = $transaction->payKey;
			} elseif (isset($transaction->preapprovalKey)) {
				$transactionUrl .= $transaction->preapprovalKey;
				$this->_session->preapprovalKey = $transaction->preapprovalKey;
			} else {
				$transactionUrl .= '?' . http_build_query($transactionData);
			}
			return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
		}
	}

	public function returnAction() {

		$params = $this->_getAllParams();
		if (isset($this->_session->payKey) && strlen($this->_session->payKey) > 0) {
			$params['payKey'] = $this->_session->payKey;
		} elseif (isset($this->_session->preapprovalKey) && strlen($this->_session->preapprovalKey) > 0) {
			$params['preapprovalKey'] = $this->_session->preapprovalKey;
		}
		$session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
		if ($session && isset($session->reward_id)) {
			$params['reward_id'] = $session->reward_id;
		}
		// Get order
		if (!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
			!($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
			($order->source_type != 'sitecrowdfunding_backer') ||
			!($user_order = $order->getSource())) {
			return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		}

		$project_gateway_id = $this->_getParam('project_gateway_id');
		$gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $order->gateway_id);
		$isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
		$allowedPaymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
		if ($allowedPaymentMethod == 'normal' && empty($isPaymentToSiteEnable)) {
			$gateway = Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $project_gateway_id);
		}

		if (!$gateway) {
			return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		}

		// Get gateway plugin
		$plugin = $gateway->getPlugin();

		unset($this->_session->errorMessage);

		try {
			$status = $plugin->onUserOrderTransactionReturn($order, $params);
		} catch (Payment_Model_Exception $e) {
			$status = 'failure';
			$this->_session->errorMessage = $e->getMessage();
		}
		$this->_success->succes_id = $user_order->backer_id;

		return $this->_finishPayment($status);
	}

	protected function _finishPayment($state = 'active') {

		// Clear session
		$errorMessage = $this->_session->errorMessage;
		$cartSession = new Zend_Session_Namespace('sitecrowdfunding_cart_data');
		$donationType = $cartSession->donationType;
		$sourceUrl = $cartSession->sourceUrl;
		$cartSession->unsetAll();
		$this->_session->unsetAll();
		$this->_session->errorMessage = $errorMessage;
		$this->_session->donationType = $donationType; //TYPE OF BACKING
		$this->_session->sourceUrl = $sourceUrl; //URL FOR REDIRECTION AFTER SUCCESSFULL BACKING: DONATION CASE
		$project_id = $this->_session->checkout_project_id;

		// Redirect
		if ($state == 'free') {
			return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		} else {
			return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state, 'project_id' => $project_id));
		}
	}

	public function finishAction() {
		$session = new Zend_Session_Namespace('Sitecrowdfunding_Backer_Payment_Detail');

		if (!empty($session->sitecrowdfundingBackerPaymentDetail)) {
			$session->sitecrowdfundingBackerPaymentDetail = '';
		}

		$paymentDetail = array('success_id' => $this->_success->succes_id, 'state' => $this->_getParam('state'), 'incrementBacker' => true, 'errorMessage' => $this->_session->errorMessage, 'donationType' => $this->_session->donationType, 'sourceUrl' => $this->_session->sourceUrl);
		$project_id = $this->_session->checkout_project_id;
		$session->sitecrowdfundingBackerPaymentDetail = $paymentDetail;

		return $this->_helper->redirector->gotoRoute(array('action' => 'success', 'project_id' => $project_id), 'sitecrowdfunding_backer', false);
	}

}
