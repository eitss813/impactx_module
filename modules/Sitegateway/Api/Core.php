<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Api_Core extends Core_Api_Abstract {

	protected $_getGatewayMethod = _GETGATEWAYMETHOD;

	public function getGatewayColumn($params = array()) {

		$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');

		$select = $gatewayTable->select();

		if (!empty($params['fetchRow'])) {
			$select->from($gatewayTable);
		} else {
			$columnName = isset($params['columnName']) ? $params['columnName'] : 'gateway_id';
			$select->from($gatewayTable, $columnName);
		}

		if (!empty($params['pluginLike'])) {
			$pluginLike = $params['pluginLike'];
			$select->where('plugin LIKE ?', "$pluginLike%");
		} elseif (!empty($params['plugin'])) {
			$select->where('plugin = ?', $params['plugin']);
		}

		if (!empty($params['enabled'])) {
			$select->where('enabled = ?', $params['enabled']);
		}

		if (!empty($params['gateway_id'])) {
			$select->where('gateway_id = ?', $params['gateway_id']);
		}

		if (!empty($params['fetchRow'])) {
			return $gatewayTable->fetchRow($select);
		} else {
			return $select->query()->fetchColumn();
		}
	}

	public function getKey($params = array()) {

		if (!empty($this->_getGatewayMethod) && $params['gateway'] == $this->_getGatewayMethod) {

			$params['productType'] = trim($params['productType']);
			$coreSettings = Engine_Api::_()->getApi('settings', 'core');

			if (substr($params['productType'], -7) == 'package') {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($coreSettings->getSetting('is.sitestore.admin.driven', 0) && $params['productType'] == 'sitestoreproduct_order') {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && $coreSettings->getSetting('sitegateway.stripechargemethod', 1) && !$coreSettings->getSetting('siteeventticket.payment.to.siteadmin', 0) && $params['productType'] == 'siteeventticket_order') {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && !$coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('siteeventticket.payment.to.siteadmin', 0) && $params['productType'] == 'siteeventticket_order') {
				$gateway = $this->getTicketSellerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && $coreSettings->getSetting('sitegateway.stripechargemethod', 1) && !$coreSettings->getSetting('sitestore.payment.for.orders', 0) && $params['productType'] == 'sitestoreproduct_order') {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && !$coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('sitestore.payment.for.orders', 0) && $params['productType'] == 'sitestoreproduct_order') {
				$gateway = $this->getStoreProductSellerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && $coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('sitecrowdfunding.payment.method', 'split') == 'split' && $params['productType'] == 'sitecrowdfunding_backer') {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && !$coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('sitecrowdfunding.payment.method', 'split') == 'split' && $params['productType'] == 'sitecrowdfunding_backer') {
				$gateway = $this->getProjectOwnerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && $coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('sitecrowdfunding.payment.method', 'split') == 'normal' && !$coreSettings->getSetting('sitecrowdfunding.payment.to.siteadmin', 0) && $params['productType'] == 'sitecrowdfunding_backer') {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && !$coreSettings->getSetting('sitegateway.stripechargemethod', 1) && $coreSettings->getSetting('sitecrowdfunding.payment.method', 'split') == 'normal' && $coreSettings->getSetting('sitecrowdfunding.payment.to.siteadmin', 0) && $params['productType'] == 'sitecrowdfunding_backer') {
				$gateway = $this->getProjectOwnerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($params['productType'] == 'siteeventticket_eventbill' || ($params['productType'] == 'siteeventticket_order' && $coreSettings->getSetting('siteeventticket.payment.to.siteadmin', '0')) || $params['productType'] == 'sitestoreproduct_storebill' || ($params['productType'] == 'sitestoreproduct_order' && $coreSettings->getSetting('sitestore.payment.for.orders', '0')) || ($params['productType'] == 'sitecrowdfunding_backer' && $coreSettings->getSetting('sitecrowdfunding.payment.to.siteadmin', '0'))) {
				$gateway = $this->getAdminPaymentGateway();
			} elseif ($params['productType'] == 'siteeventticket_paymentrequest' || ($params['productType'] == 'siteeventticket_order' && !$coreSettings->getSetting('siteeventticket.payment.to.siteadmin', '0'))) {
				$gateway = $this->getTicketSellerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($params['productType'] == 'sitestoreproduct_paymentrequest' || ($params['productType'] == 'sitestoreproduct_order' && !$coreSettings->getSetting('sitestore.payment.for.orders', '0'))) {
				$gateway = $this->getStoreProductSellerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($params['productType'] == 'sitecrowdfunding_paymentrequest' || ($params['productType'] == 'sitecrowdfunding_backer' && !$coreSettings->getSetting('sitecrowdfunding.payment.to.siteadmin', '0'))) {
				$gateway = $this->getProjectOwnerPaymentGateway(array('productParentId' => $params['productParentId']));
			} elseif ($params['productType'] == 'sitecredit_order') {
				$gateway = $this->getAdminPaymentGateway();
			}

			if (!empty($params['returnGateway'])) {
				return $gateway;
			}
			return $gateway->config[$params['key']];
		}

		return null;
	}

	public function getAdminPaymentGateway($plugin = 'Sitegateway_Plugin_Gateway_Stripe') {

		$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
		$whereCondition = "plugin = '{$plugin}'";

		$gatewayId = $gatewayTable->select()
			->from($gatewayTable, 'gateway_id')
			->where($whereCondition)
			->query()
			->fetchColumn();

		return Engine_Api::_()->getItem('payment_gateway', $gatewayId);
	}

	public function getTicketSellerPaymentGateway($params = array()) {
		$sitegatewayAdminPaymentGateway = Zend_Registry::isRegistered('sitegatewayAdminPaymentGateway') ? Zend_Registry::get('sitegatewayAdminPaymentGateway') : null;
		if (!empty($sitegatewayAdminPaymentGateway)) {
			$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
			$event_id = $params['productParentId'];
			$whereCondition = "plugin = 'Sitegateway_Plugin_Gateway_Stripe' AND event_id = $event_id";

			$gatewayId = $gatewayTable->select()
				->from($gatewayTable, 'gateway_id')
				->where($whereCondition)
				->query()
				->fetchColumn();

			return Engine_Api::_()->getItem('siteeventticket_gateway', $gatewayId);
		}

		return;
	}

	public function getStoreProductSellerPaymentGateway($params = array()) {
		$sitegatewayAdminPaymentGateway = Zend_Registry::isRegistered('sitegatewayAdminPaymentGateway') ? Zend_Registry::get('sitegatewayAdminPaymentGateway') : null;
		if (!empty($sitegatewayAdminPaymentGateway)) {
			$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
			$store_id = $params['productParentId'];
			$whereCondition = "plugin = 'Sitegateway_Plugin_Gateway_Stripe' AND store_id = $store_id";

			$gatewayId = $gatewayTable->select()
				->from($gatewayTable, 'gateway_id')
				->where($whereCondition)
				->query()
				->fetchColumn();

			return Engine_Api::_()->getItem('sitestoreproduct_gateway', $gatewayId);
		}

		return;
	}

	public function getPaymentProductColumn($params = array()) {

		$paymentProductTable = Engine_Api::_()->getDbtable('products', 'payment');

		$select = $paymentProductTable->select();

		$columnName = isset($params['columnName']) ? $params['columnName'] : 'sku';

		$select->from($paymentProductTable, $columnName);

		switch ($params['extension_type']) {
		case 'sitereviewpaidlisting_package':
			$params['extension_type'] = 'sitereview_package';
			break;
		case 'sitepage_package':
			$params['extension_type'] = 'sitepage_page';
			break;
		case 'sitebusiness_package':
			$params['extension_type'] = 'sitebusiness_business';
			break;
		case 'sitegroup_package':
			$params['extension_type'] = 'sitegroup_group';
			break;
		case 'sitestore_package':
			$params['extension_type'] = 'sitestore_store';
			break;
		case 'payment_package':
			$params['extension_type'] = 'payment_subscription';
			break;
		default:
			$params['extension_type'] = $params['extension_type'];
			break;
		}

		if (!empty($params['extension_type'])) {
			$select->where('extension_type = ?', $params['extension_type']);
		}

		if (!empty($params['extension_id'])) {
			$select->where('extension_id = ?', $params['extension_id']);
		}
		$sitegatewayPaymentProductColumn = Zend_Registry::isRegistered('sitegatewayPaymentProductColumn') ? Zend_Registry::get('sitegatewayPaymentProductColumn') : null;

		if (!empty($sitegatewayPaymentProductColumn)) {
			return $select->query()->fetchColumn();
		}

		return;
	}

	public function getCurrency() {
		return Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
	}

	/**
	 * Used to get the order amount
	 *
	 * @link https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
	 * @param int/float $price
	 * @return int order amount
	 */
	public function getPrice($price) {

		$zeroDecimalCurrencies = array('BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF');

		$currencyEnabled = $this->getCurrency();
		if (in_array($currencyEnabled, $zeroDecimalCurrencies)) {
			return (int) $price;
		}

		return (int) ($price * _GETDEFAULTPRICE);
	}

	public function insertTransactions($params = array()) {

		$finalParams = array();
		$finalParams['user_id'] = $params['user_id'];
		$finalParams['resource_type'] = $params['resource_type'];
		$finalParams['gateway_id'] = $params['gateway_id'];
		$finalParams['timestamp'] = isset($params['date']) ? $params['date'] : $params['timestamp'];
		$sitegatewayInsertTransaction = Zend_Registry::isRegistered('sitegatewayInsertTransaction') ? Zend_Registry::get('sitegatewayInsertTransaction') : null;
		if ($params['resource_type'] == 'sitestoreproduct_paymentrequest' || $params['resource_type'] == 'sitestoreproduct_storebill' || $params['resource_type'] == 'sitestoreproduct_order') {
			$finalParams['order_id'] = $params['parent_order_id'];
		} else if ($params['resource_type'] == 'sitecrowdfunding_backer' || $params['resource_type'] == 'sitecrowdfunding_project' || $params['resource_type'] == 'sitecrowdfunding_paymentrequest' || $params['resource_type'] == 'sitecrowdfunding_projectbill') {
			$finalParams['order_id'] = $params['payment_order_id'];
		} else {
			$finalParams['order_id'] = $params['order_id'];
		}

		$finalParams['type'] = $params['type'];
		$finalParams['state'] = $params['state'];
		$finalParams['gateway_transaction_id'] = isset($params['gateway_transaction_id']) ? $params['gateway_transaction_id'] : NULL;
		$finalParams['gateway_parent_transaction_id'] = isset($params['gateway_parent_transaction_id']) ? $params['gateway_parent_transaction_id'] : NULL;
		$finalParams['gateway_order_id'] = isset($params['gateway_order_id']) ? $params['gateway_order_id'] : NULL;
		$finalParams['amount'] = $params['amount'];
		$finalParams['currency'] = $params['currency'];
		$finalParams['gateway_payment_key'] = isset($params['gateway_payment_key']) ? $params['gateway_payment_key'] : NULL;
		$finalParams['resource_id'] = $params['resource_id'];
		$finalParams['payout_status'] = isset($params['payout_status']) ? $params['payout_status'] : '';
		if (!empty($sitegatewayInsertTransaction)) {
			Engine_Api::_()->getDbtable('transactions', 'sitegateway')->insert($finalParams);
		}

		return;
	}

	public function getResourceName($resource_type) {

		switch ($resource_type) {
		case 'sitestoreproduct_order':
			$resource_name = 'Products Purchase of Stores / Marketplace';
			break;
		case 'sitestoreproduct_storebill':
			$resource_name = 'Commissions of Stores / Marketplace';
			break;
		case 'sitestoreproduct_paymentrequest':
			$resource_name = 'Payment to Sellers of Stores / Marketplace';
			break;
		case 'siteeventticket_order':
			$resource_name = 'Tickets Purchase of Advanced Events';
			break;
		case 'siteeventticket_eventbill':
			$resource_name = 'Commissions of Advanced Events';
			break;
		case 'siteeventticket_paymentrequest':
			$resource_name = 'Payment to Sellers of Advanced Events';
			break;
		case 'siteevent_event':
			$resource_name = 'Advanced Event - Packages';
			break;
		case 'sitereview_listing':
			$resource_name = 'Multiple Listing Types - Packages';
			break;
		case 'sitepage_page':
			$resource_name = 'Directory / Pages - Packages';
			break;
		case 'sitebusiness_business':
			$resource_name = 'Directory / Businesses - Packages';
			break;
		case 'sitegroup_group':
			$resource_name = 'Groups / Communities - Packages';
			break;
		case 'sitestore_store':
			$resource_name = 'Stores / Marketplace - Store Packages';
			break;
		case 'payment_subscription':
			$resource_name = 'Signup Subscriptions';
			break;
		case 'userads':
			$resource_name = 'Advertisements / Community Ads - Packages';
			break;
		case 'sitecrowdfunding_project':
			$resource_name = 'Crowdfunding - Packages';
			break;
		case 'sitecrowdfunding_backer':
			$resource_name = 'Backing on project of Crowdfunding';
			break;
		default:
			$resource_name = $resource_type;
		}

		return $resource_name;
	}

	public function totalDaysInPeriod($period_type = 'week') {

		switch ($period_type) {
		case 'week':
			$number_of_days = 7;
			break;
		case 'month':
			$number_of_days = 28;
			break;
		case 'year':
			$number_of_days = 365;
			break;
		}

		return $number_of_days;
	}

	public function getSplitNEscrowGatewayCommission($params = array()) {

		$resource_key = $params['resource_key'];
		$table = Engine_Api::_()->getItemTable($params['resource_type']);

		$columnName = 'payment_split = ?';
		$columnVal = "1";
		if ($params['resource_type'] == 'sitecrowdfunding_backer') {
			$columnName = 'gateway_type in(?)';
			$columnVal = array('split', 'escrow');
		}

		$select = $table->select()
			->from($table->info('name'), array("SUM(commission_value)"))
			->where("$resource_key = ?", $params['resource_id'])
			->where($columnName, $columnVal)
			->where("direct_payment = ?", 1)
			->where("gateway_profile_id != ?", 'NULL')
			->where("payment_status = ?", 'active');
		return $select->query()->fetchColumn();
	}

	public function getStripeConnectCommission($params = array()) {

		$resource_key = $params['resource_key'];

		$sitegatewayStripeConnectCommission = Zend_Registry::isRegistered('sitegatewayStripeConnectCommission') ? Zend_Registry::get('sitegatewayStripeConnectCommission') : null;
		$stripeGatewayId = !empty($sitegatewayStripeConnectCommission) ? Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_Stripe', 'columnName' => 'gateway_id')) : 0;

		$table = Engine_Api::_()->getItemTable($params['resource_type']);

		$select = $table->select()
			->from($table->info('name'), array("SUM(commission_value)"))
			->where("$resource_key = ?", $params['resource_id']);

		if (isset($params['payment_split'])) {
			$select->where("payment_split = ?", $params['payment_split']);
		}

		$select->where("gateway_id = ?", $stripeGatewayId)
			->where("direct_payment = ?", 1)
			->where("gateway_profile_id != ?", 'NULL')
			->where("payment_status = ?", 'active');

		return $select->query()->fetchColumn();
	}

	public function isValidGateway($payment_method) {

		$sitegatewayValidGateway = Zend_Registry::isRegistered('sitegatewayValidGateway') ? Zend_Registry::get('sitegatewayValidGateway') : null;
		$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');

		$payment_method = ucfirst($payment_method);

		$gatewayId = $gatewayTable->select()
			->from($gatewayTable, 'gateway_id')
			->where("plugin = ?", "Sitegateway_Plugin_Gateway_$payment_method")
			->query()
			->fetchColumn();

		$getGatewayID = !empty($sitegatewayValidGateway) ? $gatewayId : 0;
		return $getGatewayID;
	}

	public function getAdditionalEnabledGateways($params = array()) {
		$sitegatewayValidGateway = Zend_Registry::isRegistered('sitegatewayValidGateway') ? Zend_Registry::get('sitegatewayValidGateway') : null;
		$gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');

		$select = $gatewayTable->select();

		if (!empty($sitegatewayValidGateway)) {
			if (!empty($params['pluginLike'])) {
				$pluginLike = $params['pluginLike'];
				$select->where('plugin LIKE ?', "$pluginLike%");
			} elseif (!empty($params['plugin']) && is_array($params['plugin'])) {
				$select->where('plugin in (?)', $params['plugin']);
			} elseif (!empty($params['plugin'])) {
				$select->where('plugin = ?', $params['plugin']);
			}

			$select->where('enabled = ?', true);
		}

		return $gatewayTable->fetchAll($select);
	}

	public function getProjectOwnerPaymentGateway($params = array()) {
		$sitegatewayAdminPaymentGateway = Zend_Registry::isRegistered('sitegatewayAdminPaymentGateway') ? Zend_Registry::get('sitegatewayAdminPaymentGateway') : null;
		if (!empty($sitegatewayAdminPaymentGateway)) {
			$gatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
			$project_id = $params['productParentId'];
			$whereCondition = "plugin = 'Sitegateway_Plugin_Gateway_Stripe' AND project_id = $project_id";

			$gatewayId = $gatewayTable->select()
				->from($gatewayTable, 'projectgateway_id')
				->where($whereCondition)
				->query()
				->fetchColumn();

			return Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $gatewayId);
		}

		return;
	}

	function getTransaction($resourceType, $resourceId) {
		return Engine_Api::_()->getDbtable('transactions', 'sitegateway')->fetchRow(array('resource_type = ?' => $resourceType, 'resource_id = ?' => $resourceId));
	}

	function isEscrowGateway($plugin) {
		$escrowGatewaysList = array(
			'Sitegateway_Plugin_Gateway_MangoPay',
		);
		return in_array($plugin, $escrowGatewaysList);
	}

	function isSplitGateway($plugin) {
		$splitGatewaysList = array(
			'Sitegateway_Plugin_Gateway_MangoPay',
			'Sitegateway_Plugin_Gateway_Stripe',
		);
		return in_array($plugin, $splitGatewaysList);
	}

	public function getPriceWithCurrency($price) {
		if (empty($price)) {
			return $price;
		}

		$defaultParams = array();
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if (empty($viewer_id)) {
			$defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
		}

		$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		$defaultParams['precision'] = 2;
		$price = (float) $price;
		$priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
		return $priceStr;
	}

	/**
	 * Get Currency Symbol
	 *
	 * @return string
	 */
	public function getCurrencySymbol() {

		$localeObject = Zend_Registry::get('Locale');
		$currencyCode = $this->getCurrency();
		$currencySymbol = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currencyCode);
		return $currencySymbol;
	}

}
