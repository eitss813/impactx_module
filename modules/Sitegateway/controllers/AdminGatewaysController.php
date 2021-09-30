<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminGatewaysController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_AdminGatewaysController extends Core_Controller_Action_Admin {

	public function indexAction() {

		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('sitegateway_admin_main', array(), 'sitegateway_admin_main_gateways');

		// Test curl support
		if (!function_exists('curl_version') ||
			!($info = curl_version())) {
			$this->view->error = $this->view->translate('The PHP extension cURL ' .
				'does not appear to be installed, which is required ' .
				'for interaction with payment gateways. Please contact your ' .
				'hosting provider.');
		} else if (!($info['features'] & CURL_VERSION_SSL) ||
			!in_array('https', $info['protocols'])) {
			$this->view->error = $this->view->translate('The installed version of ' .
				'the cURL PHP extension does not support HTTPS, which is required ' .
				'for interaction with payment gateways. Please contact your ' .
				'hosting provider.');
		}

		include_once APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license2.php';

		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}

	public function editAction() {

		$currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

		// Get gateway
		$gateway = $selectedGateway = Engine_Api::_()->getDbtable('gateways', 'payment')
			->find($this->_getParam('gateway_id'))
			->current();

		$gatewayPlugin = $gateway->getGateway();
		$supportedCurrency = $gatewayPlugin->getSupportedCurrencies();

		$gatewaySubscriptionNotSupported = array(
			'Sitegateway_Plugin_Gateway_MangoPay',
			'Sitegateway_Plugin_Gateway_Payumoney',
			'Sitegateway_Plugin_Gateway_Mollie',
		);
		$isSupported = true;
		if (in_array($gateway->plugin, $gatewaySubscriptionNotSupported)) {
			$isSupported = false;
		}
		// Make form
		$this->view->form = $form = $gateway->getPlugin()->getAdminGatewayForm();

		if (_ENGINE_ADMIN_NEUTER) {
			return;
		}

		// Populate form
		$form->populate($gateway->toArray());
		if (is_array($gateway->config)) {
			$form->populate($gateway->config);
		}

		include_once APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license2.php';

		// Check method/valid
		if (empty($_STRIPE_GATEWAY) || !$this->getRequest()->isPost()) {
			return;
		}
		if (empty($_STRIPE_GATEWAY) || !$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		// Process
		$values = $form->getValues();

		$testmode = $values['test_mode'];

		$gateway->test_mode = $testmode;
		$gateway->save();

		$enabled = (bool) $values['enabled'];

		unset($values['enabled']);
		// Validate gateway config
		if ($enabled) {
			$gatewayObject = $gateway->getGateway();
			try {
				$gatewayObject->setConfig($values);
				$gatewayObject->test();
				if (!in_array($currentCurrency, $supportedCurrency)) {
					$form->addError($currentCurrency . ', this currency is not supported by this payment gateway. Please change the currency.', false);
					$enabled = false;
					$form->populate(array('enabled' => false));
				}
			} catch (Exception $e) {
				$enabled = false;
				$form->populate(array('enabled' => false));
				$form->addError(sprintf('Gateway login failed. Please double check ' .
					'your connection information. The gateway has been disabled. ' .
					'The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
			}
		} else {
			$form->addError('Gateway is currently disabled.');
		}

		// Process
		$message = null;
		try {
			$values = $gateway->getPlugin()->processAdminGatewayForm($values);
		} catch (Exception $e) {
			$message = $e->getMessage();
			$values = null;
		}

		$formNotice = false;
		if (null !== $values) {
			$gateway->setFromArray(array(
				'enabled' => $enabled,
				'config' => $values,
			));
			$gateway->save();
			$formNotice = true;
		} else {
			$form->addError($message);
		}

		if (Engine_Api::_()->hasModuleBootstrap('sitecoupon')) {
			$gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
			try {
				foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
					$gatewayPlugin = $gateway->getGateway();

					if (method_exists($gatewayPlugin, 'createCoupon') && substr($gateway->plugin, 0, 27) == 'Sitegateway_Plugin_Gateway_') {

						foreach (Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->fetchAll() as $coupon) {
							//If it throws an exception, or returns empty, assume it doesn't exist?
							try {
								$couponInfo = $gatewayPlugin->retrieveCoupon($coupon->code);
							} catch (Exception $e) {
								$couponInfo = false;
							}

							if (!$couponInfo) {
								$gatewayPlugin->createCoupon($coupon->toArray());
							}
						}
					}
				}
				if ($isSupported) {
					$form->addNotice("Changes Saved. All the current discount coupons has been created successfully in this gateway.");
				}
			} catch (Exception $e) {
				$form->addError($e->getMessage());
			}
		}

		// Try to update/create all product if enabled
		$gatewayPlugin = $gateway->getGateway();
		if ($gateway->enabled &&
			method_exists($gatewayPlugin, 'createProduct') &&
			method_exists($gatewayPlugin, 'editProduct') &&
			method_exists($gatewayPlugin, 'detailVendorProduct')) {

			$modulesArray = array('siteeventpaid', 'sitereviewpaidlisting', 'sitepage', 'sitebusiness', 'sitegroup', 'sitestore', 'payment', 'sitecrowdfunding');
			$formSuccess = false;
			foreach ($modulesArray as $module) {

				if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($module)) {
					continue;
				}

				$packageTable = Engine_Api::_()->getDbtable('packages', $module);
				try {
					foreach ($packageTable->fetchAll() as $package) {

						if ($package->isFree()) {
							continue;
						}

						// Check billing cycle support
						if (!$package->isOneTime()) {
							$supportedBillingCycles = $gateway->getGateway()->getSupportedBillingCycles();
							if (!in_array($package->recurrence_type, array_map('strtolower', $supportedBillingCycles))) {
								continue;
							}
						}

						//If it throws an exception, or returns empty, assume it doesn't exist?
						try {
							$info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
						} catch (Exception $e) {
							$info = false;
						}
						//CREATE PRODUCT
						if (!$info) {
							$gatewayPlugin->createProduct($package->getGatewayParams());
						}
					}
					$formSuccess = true;
				} catch (Exception $e) {
					$formSuccess = false;
					$form->addError(sprintf('We were not able to ensure all packages have a product in this gateway.'));
					$form->addError($e->getMessage());
				}
			}

			if ($formNotice && $formSuccess && $isSupported) {
				$form->addNotice(sprintf('Changes Saved. All the current subscription plans & packages has been created successfully in this gateway.'));
			} elseif ($formNotice) {
				$form->addNotice(sprintf('Changes saved.'));
			}
		}
	}

}
