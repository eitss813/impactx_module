<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminIndexController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_AdminIndexController extends Core_Controller_Action_Admin {

    public function indexAction() {
        
        //IMPORT TRANSACTION FROM OTHER TRANSACTION TABLES
        $this->_importTransactions();

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegateway_admin_main', array(), 'sitegateway_admin_main_transactions');

        // Test curl support
        if (!function_exists('curl_version') ||
                !($info = curl_version())) {
            $this->view->error = $this->view->translate('The PHP extension cURL ' .
                    'does not appear to be installed, which is required ' .
                    'for interaction with payment gateways. Please contact your ' .
                    'hosting provider.');
        }
        // Test curl ssl support
        else if (!($info['features'] & CURL_VERSION_SSL) ||
                !in_array('https', $info['protocols'])) {
            $this->view->error = $this->view->translate('The installed version of ' .
                    'the cURL PHP extension does not support HTTPS, which is required ' .
                    'for interaction with payment gateways. Please contact your ' .
                    'hosting provider.');
        }
        // Check for enabled payment gateways
        else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
            $this->view->error = $this->view->translate('There are currently no ' .
                    'enabled payment gateways. You must %1$sadd one%2$s before this ' .
                    'page is available.', '<a href="' .
                    $this->view->escape($this->view->url(array('controller' => 'gateways'))) .
                    '">', '</a>');
        }

        // Make form
        $this->view->formFilter = $formFilter = new Sitegateway_Form_Admin_Transactions_Filter();

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
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitegateway');
        $transactionSelect = $transactionsTable->select();

        // Add filter values
        if (!empty($filterValues['gateway_id'])) {
            $transactionSelect->where('gateway_id = ?', $filterValues['gateway_id']);
        }
        if (!empty($filterValues['type'])) {
            $transactionSelect->where('type = ?', $filterValues['type']);
        }
        if (!empty($filterValues['resource_type'])) {
            $transactionSelect->where('resource_type = ?', $filterValues['resource_type']);
        }
        if (!empty($filterValues['state'])) {
            $transactionSelect->where('state = ?', $filterValues['state']);
        }
        if (!empty($filterValues['query'])) {
            $transactionSelect
                    ->from($transactionsTable->info('name'))
                    ->joinRight('engine4_users', 'engine4_users.user_id=engine4_sitegateway_transactions.user_id', null)
                    ->where('(gateway_transaction_id LIKE ? || ' .
                            'gateway_parent_transaction_id LIKE ? || ' .
                            'gateway_order_id LIKE ? || ' .
                            'displayname LIKE ? || username LIKE ? || ' .
                            'email LIKE ?)', '%' . $filterValues['query'] . '%');
            ;
        }
        if (($user_id = $this->_getParam('user_id', @$filterValues['user_id']))) {
            $this->view->filterValues['user_id'] = $user_id;
            $transactionSelect->where('engine4_sitegateway_transactions.user_id = ?', $user_id);
        }
        if (!empty($filterValues['order'])) {
            if (empty($filterValues['direction'])) {
                $filterValues['direction'] = 'DESC';
            }
            $transactionSelect->order($filterValues['order'] . ' ' . $filterValues['direction']);
        }

        include_once APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license2.php';

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
        // Missing transaction
        if (!($transaction_id = $this->_getParam('transaction_id')) ||
                !($transaction = Engine_Api::_()->getItem('sitegateway_transaction', $transaction_id))) {
            return;
        }

        $this->view->transaction = $transaction;
        
        $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);        
        if($transaction->resource_type == 'siteeventticket_order') {
            $this->view->gatewayName = (Engine_Api::_()->hasModuleBootstrap('siteeventticket') ? Engine_Api::_()->siteeventticket()->getGatwayName($transaction->gateway_id) : ucfirst($transaction->type));
        }
        elseif($transaction->resource_type == 'sitestoreproduct_order') {
            $this->view->gatewayName = (Engine_Api::_()->hasModuleBootstrap('sitestoreproduct') ? Engine_Api::_()->sitestoreproduct()->getGatwayName($transaction->gateway_id) : ucfirst($transaction->type));
        } 
        else {
            $this->view->gatewayName = ( $gateway ? $gateway->title : '<i>' . 'Unknown Gateway' . '</i>' );
        }        
        
        $this->view->order = Engine_Api::_()->getItem('payment_order', $transaction->order_id);
        $this->view->user = Engine_Api::_()->getItem('user', $transaction->user_id);
    }

    public function detailTransactionAction() {
        $transaction_id = $this->_getParam('transaction_id');
        $transaction = Engine_Api::_()->getItem('sitegateway_transaction', $transaction_id);
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
        $transaction_id = $this->_getParam('transaction_id');
        $transaction = Engine_Api::_()->getItem('sitegateway_transaction', $transaction_id);
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
        // By transaction
        if (null != ($transaction_id = $this->_getParam('transaction_id')) &&
                null != ($transaction = Engine_Api::_()->getItem('sitegateway_transaction', $transaction_id))) {
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
        // By transaction
        if (null != ($transaction_id = $this->_getParam('transaction_id')) &&
                null != ($transaction = Engine_Api::_()->getItem('sitegateway_transaction', $transaction_id))) {
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

    private function _importTransactions() {

        //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $modulesArray = array('communityad', 'payment', 'sitebusiness', 'siteeventpaid', 'siteeventticket', 'sitegroup', 'sitepage', 'sitestore', 'sitereviewpaidlisting', 'sitestoreproduct', 'sitestore');

        $previousImport = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway_transactionimport');
        if (empty($previousImport)) {
            $previousImport = array();
            $previousImport = serialize($previousImport);
        }
        $previousImport = unserialize($previousImport);

        $sitegatewayTransactionTable = Engine_Api::_()->getDbTable('transactions', 'sitegateway');
        $sitegatewayTransactionTableName = $sitegatewayTransactionTable->info('name');
        $db = $sitegatewayTransactionTable->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($modulesArray as $module) {

                $moduleTransactionTableName = 'engine4_' . $module . '_transactions';

                //CONTINUE IF MODULE NOT INSTALLED
                if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($module)) {
                    continue;
                }

                //CONTINUE IF ALREADY IMPORTED
                if (!empty($previousImport) && in_array($module, $previousImport)) {
                    continue;
                }

                //CONTINUE IF TABLE NOT EXIST
                $isModuleTransactionTableExists = $db->query("SHOW TABLES LIKE '$moduleTransactionTableName'")->fetch();
                if (empty($isModuleTransactionTableExists)) {
                    continue;
                }

                $timestamp = 'timestamp';
                $orderId = 'order_id';
                $gatewayOrderId = 'gateway_order_id';
                if ($module == 'siteeventticket') {
                    $timestamp = 'date';
                    $orderId = 'order_id';
                    $gatewayOrderId = 'payment_order_id';
                } elseif ($module == 'sitestoreproduct') {
                    $timestamp = 'date';
                    $orderId = 'parent_order_id';
                    $gatewayOrderId = 'payment_order_id';
                }

                $stripeGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_Stripe', 'columnName' => 'gateway_id'));
                
                $db->query("INSERT IGNORE INTO `$sitegatewayTransactionTableName`(`user_id`, `gateway_id`, `timestamp`, `order_id`, `type`, `state`, `gateway_transaction_id`, `gateway_parent_transaction_id`, `gateway_order_id`, `amount`, `currency`) SELECT `user_id`, `gateway_id`, `$timestamp`, `$orderId`, `type`, `state`, `gateway_transaction_id`, `gateway_parent_transaction_id`, `$gatewayOrderId`, `amount`, `currency` FROM `$moduleTransactionTableName` WHERE `gateway_id` != $stripeGatewayId");

                $currentImport = array();
                $currentImport[] = $module;
                if (Count($currentImport) > 0) {

                    $previousImport = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway_transactionimport');
                    if (empty($previousImport)) {
                        $previousImport = array();
                        $previousImport = serialize($previousImport);
                    }
                    $previousImport = unserialize($previousImport);

                    if (Count($previousImport) > 0) {
                        $currentImport = array_merge($previousImport, $currentImport);
                    }
                    $import_value = serialize($currentImport);

                    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegateway_transactionimport', $import_value);
                }
            }

            //COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //UPDATE RESOURCE TYPE IN TRANSACTION TABLE
        $select = $sitegatewayTransactionTable->select()
                ->from($sitegatewayTransactionTableName, array('transaction_id', 'order_id', 'resource_type'))
                ->where("resource_type = '' OR resource_type IS NULL")
                ->where("order_id IS NOT NULL");

        $paymentOrderTable = Engine_Api::_()->getDbTable('orders', 'payment');
        $paymentOrderTableName = $paymentOrderTable->info('name');

        foreach ($sitegatewayTransactionTable->fetchAll($select) as $transaction) {
            $transaction->resource_type = $paymentOrderTable->select()
                    ->from($paymentOrderTableName, 'source_type')
                    ->where("order_id = ?", $transaction->order_id)
                    ->query()
                    ->fetchColumn();
            $transaction->save();
          
        }
    }

}
