<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPaymentController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminPaymentController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_transactions');
    if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
         $this->view->error = $this->view->translate('You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.');
      return;
    }
    // Test curl support
    if (!function_exists('curl_version') ||
        !($info = curl_version())) {
      $this->view->error = $this->view->translate('The PHP extension cURL does not appear to be installed, which is required for interaction with payment gateways. Please contact your hosting provider.');
    }
    // Test curl ssl support
    else if (!($info['features'] & CURL_VERSION_SSL) ||
        !in_array('https', $info['protocols'])) {
      $this->view->error = $this->view->translate('The installed version of the cURL PHP extension does not support HTTPS, which is required for interaction with payment gateways. Please contact your hosting provider.');
    }
    // Check for enabled payment gateways
    else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
      $this->view->error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  for transactions to occur on your site.', '<a href="' .
              $this->view->baseUrl() . '/admin/payment/gateway" ' .
              " target='_blank'" . '">', '</a>');
    }


    // Make form
    $this->view->formFilter = $formFilter = new Sitepage_Form_Admin_Transaction_Filter();

    // Process form
    if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'transaction_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order = $filterValues['order'];
    $this->view->direction = $filterValues['direction'];

    // Initialize select
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitepage');
    $transactionsName = $transactionsTable->info('name');
    $orderssName = Engine_Api::_()->getDbtable('orders', 'payment')->info('name');
    $sitepageName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');
    $transactionSelect = $transactionsTable->select()
            ->from($transactionsName)
            ->setIntegrityCheck(false)
            ->join($orderssName, $orderssName . '.order_id=' . $transactionsName . '.order_id', array($orderssName . '.source_id'))
            ->join($sitepageName, $sitepageName . '.page_id=' . $orderssName . '.source_id', array($sitepageName . '.title', $sitepageName . '.page_id'));

    // Add filter values
    if (!empty($filterValues['gateway_id'])) {
      $transactionSelect->where($transactionsName . '.gateway_id = ?', $filterValues['gateway_id']);
    }
    if (!empty($filterValues['type'])) {
      $transactionSelect->where($transactionsName . '.type = ?', $filterValues['type']);
    }
    if (!empty($filterValues['state'])) {
      $transactionSelect->where($transactionsName . '.state = ?', $filterValues['state']);
    }
    if (!empty($filterValues['query'])) {
      $transactionSelect
          ->joinRight('engine4_users', 'engine4_users.user_id=engine4_sitepage_transactions.user_id', null)
          ->where('(' . $transactionsName . '.gateway_transaction_id LIKE ? || ' .
              $transactionsName . '.gateway_parent_transaction_id LIKE ? || ' .
              $transactionsName . '.gateway_order_id LIKE ? || ' .
              'title LIKE ? || ' .
              'displayname LIKE ? || username LIKE ? || ' .
              'engine4_users.email LIKE ?)', '%' . $filterValues['query'] . '%');
    }
    if (($user_id = $this->_getParam('user_id', @$filterValues['user_id']))) {
      $this->view->filterValues['user_id'] = $user_id;
      $transactionSelect->where('engine4_sitepage_transactions.user_id = ?', $user_id);
    }

    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'DESC';
      }
      $transactionSelect->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }


    $this->view->paginator = $paginator = Zend_Paginator::factory($transactionSelect);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Preload info
    $gatewayIds = array();
    $userIds = array();
    $orderIds = array();
    foreach ($paginator as $transaction) {
      if (!empty($transaction->gateway_id)) {
        $gatewayIds[] = $transaction->gateway_id;
      }
      if (!empty($transaction->user_id)) {
        $userIds[] = $transaction->user_id;
      }
      if (!empty($transaction->order_id)) {
        $orderIds[] = $transaction->order_id;
      }
    }
    $gatewayIds = array_unique($gatewayIds);
    $userIds = array_unique($userIds);
    $orderIds = array_unique($orderIds);

    // Preload gateways
    $gateways = array();
    if (!empty($gatewayIds)) {
      foreach (Engine_Api::_()->getDbtable('gateways', 'payment')->find($gatewayIds) as $gateway) {
        $gateways[$gateway->gateway_id] = $gateway;
      }
    }
    $this->view->gateways = $gateways;

    // Preload users
    $users = array();
    if (!empty($userIds)) {
      foreach (Engine_Api::_()->getItemTable('user')->find($userIds) as $user) {
        $users[$user->user_id] = $user;
      }
    }
    $this->view->users = $users;

    // Preload orders
    $orders = array();
    if (!empty($orderIds)) {
      foreach (Engine_Api::_()->getDbtable('orders', 'payment')->find($orderIds) as $order) {
        $orders[$order->order_id] = $order;
      }
    }
    $this->view->orders = $orders;
  }

  public function detailAction() {
    if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
      die;
    }
    // Missing transaction
    if (!($transaction_id = $this->_getParam('transaction_id')) ||
        !($transaction = Engine_Api::_()->getItem('sitepage_transaction', $transaction_id))) {
      return;
    }

    $this->view->transaction = $transaction;
    $this->view->gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
    $this->view->order = Engine_Api::_()->getItem('payment_order', $transaction->order_id);
    $this->view->title = Engine_Api::_()->getItem('sitepage_page', $this->view->order->source_id)->title;
    $this->view->user = Engine_Api::_()->getItem('user', $transaction->user_id);
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
      $this->view->credits=Engine_Api::_()->getDbtable('credits','sitecredit')->fetchRow(array('type =?' => 'sitepage_package', 'type_id =?' => $transaction->order_id));
    }
  }

  public function detailTransactionAction() {
    if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
      die;
    }
    $transaction_id = $this->_getParam('transaction_id');
    $transaction = Engine_Api::_()->getItem('sitepage_transaction', $transaction_id);
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

  public function detailOrderAction() {
    if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
      die;
    }
    $transaction_id = $this->_getParam('transaction_id');
    $transaction = Engine_Api::_()->getItem('sitepage_transaction', $transaction_id);
    $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

    if (!empty($transaction->gateway_order_id)) {
      $link = $gateway->getPlugin()->getOrderDetailLink($transaction->gateway_order_id);
    } else {
      $link = false;
    }

    if ($link) {
      return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
    } else {
      die();
    }
  }

  public function rawOrderDetailAction() {
    if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
      die;
    }
    // By transaction
    if (null != ($transaction_id = $this->_getParam('transaction_id')) &&
        null != ($transaction = Engine_Api::_()->getItem('sitepage_transaction', $transaction_id))) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
      $gateway_order_id = $transaction->gateway_order_id;
    }

    // By order
    else if (null != ($order_id = $this->_getParam('order_id')) &&
        null != ($order = Engine_Api::_()->getItem('payment_order', $order_id))) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id);
      $gateway_order_id = $order->gateway_order_id;
    }

    // By raw string
    else if (null != ($gateway_order_id = $this->_getParam('gateway_order_id')) &&
        null != ($gateway_id = $this->_getParam('gateway_id'))) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id);
    }

    if (!$gateway || !$gateway_order_id) {
      $this->view->data = false;
      return;
    }

    $gatewayPlugin = $gateway->getPlugin();

    try {
      $data = $gatewayPlugin->getOrderDetails($gateway_order_id);
      $this->view->data = $this->_flattenArray($data);
    } catch (Exception $e) {
      $this->view->data = false;
      return;
    }
  }

  public function rawTransactionDetailAction() {
    if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
      die;
    }
    // By transaction
    if (null != ($transaction_id = $this->_getParam('transaction_id')) &&
        null != ($transaction = Engine_Api::_()->getItem('sitepage_transaction', $transaction_id))) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
      $gateway_transaction_id = $transaction->gateway_transaction_id;
    }

    // By order
    else if (null != ($order_id = $this->_getParam('order_id')) &&
        null != ($order = Engine_Api::_()->getItem('payment_order', $order_id))) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id);
      $gateway_transaction_id = $order->gateway_transaction_id;
    }

    // By raw string
    else if (null != ($gateway_transaction_id = $this->_getParam('gateway_transaction_id')) &&
        null != ($gateway_id = $this->_getParam('gateway_id'))) {
      $gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id);
    }

    if (!$gateway || !$gateway_transaction_id) {
      $this->view->data = false;
      return;
    }

    $gatewayPlugin = $gateway->getPlugin();

    try {
      $data = $gatewayPlugin->getTransactionDetails($gateway_transaction_id);
      $this->view->data = $this->_flattenArray($data);
    } catch (Exception $e) {
      $this->view->data = false;
      return;
    }
  }

  protected function _flattenArray($array, $separator = '_', $prefix = '') {
    if (!is_array($array)) {
      return false;
    }

    $flattenedArray = array();
    foreach ($array as $key => $value) {
      $newPrefix = ( $prefix != '' ? $prefix . $separator : '' ) . $key;
      if (is_array($value)) {
        $flattenedArray = array_merge($flattenedArray, $this->_flattenArray($value, $separator, $newPrefix));
      } else {
        $flattenedArray[$newPrefix] = $value;
      }
    }

    return $flattenedArray;
  }

}