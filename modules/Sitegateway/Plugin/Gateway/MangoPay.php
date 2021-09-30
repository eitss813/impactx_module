<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MangoPay.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

/**
 * All the methods defined in this file will be required to update as per your new payment gateway requirement. However for your better understanding, we have given some sample code under some methods of Stripe gateway. You have to change these method's code according to your gateway requirement.
 */
class Sitegateway_Plugin_Gateway_MangoPay extends Sitegateway_Plugin_Gateway_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
    }

    /**
     * Method to get the gateway object.
     *
     * @return Engine_Payment_Gateway
     */
    public function getGateway() {

        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_MangoPay';
            Engine_Loader::loadClass($class);
            $gateway = new $class(array(
                'config' => (array) $this->_gatewayInfo->config,
                'testMode' => $this->_gatewayInfo->test_mode,
                'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
            ));
            if (!($gateway instanceof Engine_Payment_Gateway)) {
                throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
            }
            $this->_gateway = $gateway;
        }

        return $this->_gateway;
    }

    /**
     * You must to define this method to process a transaction. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.

     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $resourceObject
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    protected function createResourceTransaction($user, $resourceObject, $package, $params = array()) {

        $params['fixed'] = true;
        $productInfo = $this->getService()->detailVendorProduct($package->getGatewayIdentity());
        if (!empty($productInfo)) {
            $params['product_id'] = $productInfo['product_id'];
        }
        $params['quantity'] = 1;

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Process return of an user after order transaction. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserOrderTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $user_order = $order->getSource();
        $user = $order->getUser();

        if ($user_order->payment_status == 'pending') {
            return 'pending';
        }

        if (empty($params['transactionId']) || (!empty($params['transactionId']) && $params['transactionId'] == 'undefined')) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $mode = 'live';
        if ($adminGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        $payInTransaction = $adminGateway->getService()->getPayInDetail($params['transactionId']);
        if ($payInTransaction->Status == 'FAILED') {
            // Cancel order
            $order->onCancel();
            $user_order->onPaymentFailure();
            // Error
            throw new Payment_Model_Exception('Your payment has been failed. Please try again later.');
        }
        // Let's log it
        $this->getGateway()->getLog()->log('Return (MangoPay): '
                . print_r($params, true), Zend_Log::INFO);

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $payout = "";
        if ($order->source_type == 'sitecrowdfunding_backer') {
            $payout = $settings->getSetting('sitecrowdfunding.payment.method', 'split');
            $sellerGatewayObj = Engine_Api::_()->getItem('sitecrowdfunding_projectGateway', $params['project_gateway_id']);
            $tag = "Payout the money to Project owner and Site owner for Crowdfunding project";
            $amount = $user_order->amount;
        } elseif ($order->source_type == 'sitestoreproduct_order') {
            $payout = $settings->getSetting('sitestore.payment.method', 'split');
            $sellerGatewayObj = Engine_Api::_()->getItem('sitestoreproduct_gateway', $params['store_gateway_id']);
            $tag = "Payout the money to Seller and Siteowner for Store product";
        } elseif ($order->source_type == 'siteeventticket_order') {
            $payout = $settings->getSetting('siteeventticket.payment.method', 'split');
            $sellerGatewayObj = Engine_Api::_()->getItem('siteeventticket_gateway', $params['event_gateway_id']);
            $tag = "Payout the money to Seller and Siteowner for Event ticket.";
        }
        if (isset($user_order->gateway_type) && !empty($payout)) {
            $user_order->gateway_type = $payout;
            $user_order->save();
        }
        if (!isset($amount))
            $amount = $user_order->grand_total;
        $payout_status = '';
        switch (strtolower($payInTransaction->Status)) {
            case 'created':
                $paymentStatus = 'pending';
                $orderStatus = 'complete';
                break;

            case 'succeeded':
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                if ($payout == 'split' && isset($sellerGatewayObj->config[$mode]['bank_account_id']) && !empty($sellerGatewayObj->config[$mode]['bank_account_id']) && isset($sellerGatewayObj->config[$mode]['mangopay_user_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_user_id']) && isset($sellerGatewayObj->config[$mode]['mangopay_wallet_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_wallet_id'])) {
                    $sitegatewayApi = Engine_Api::_()->sitegateway();
                    $mangoPayCharge = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.mangopay.fees.charge', 0);
                    if (empty($mangoPayCharge)) {
                        $mangoPayChargeAmt = 0;
                    } else {
                        $mangoPayChargeAmt = ($amount / 100) * $mangoPayCharge;
                    }
                    $commisionValue = $user_order->commission_value + $mangoPayChargeAmt;
                    $pauoutParams = array();
                    $pauoutParams['bank_account_id'] = $sellerGatewayObj->config[$mode]['bank_account_id'];
                    $pauoutParams['user_id'] = $sellerGatewayObj->config[$mode]['mangopay_user_id'];
                    $pauoutParams['wallet_id'] = $sellerGatewayObj->config[$mode]['mangopay_wallet_id'];
                    $pauoutParams['amount'] = $payInTransaction->CreditedFunds->Amount;
                    $pauoutParams['currency'] = $payInTransaction->CreditedFunds->Currency;
                    $pauoutParams['fees_currency'] = $payInTransaction->CreditedFunds->Currency;
                    $pauoutParams['fees_amount'] = $sitegatewayApi->getPrice($commisionValue);
                    $pauoutParams['tag'] = $tag;
                    try {

                        $payoutDetail = $adminGateway->getService()->createPayout($pauoutParams);
                        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitegateway_payment_status', array(
                            'payout_id' => $payoutDetail->Id,
                            'resource_type' => $order->source_type,
                            'resource_id' => $order->source_id
                        ));
                    } catch (Exception $ex) {
                        //log into file
                    }

                    //CREATED, SUCCEEDED, FAILED
                    //NEED TO DO APPROPRIATE ACTION

                    if ($payoutDetail->Status == 'CREATED') {
                        //CREATED
                        if (isset($user_order->payout_status)) {
                            $user_order->payout_status = $payout_status = 'pending';
                        }
                    } else if ($payoutDetail->Status == 'SUCCEEDED') {
                        //SUCCEEDED
                        if (isset($user_order->payout_status)) {
                            $user_order->payout_status = $payout_status = 'success';
                        }
                    } else {
                        //FAILED
                        if (isset($user_order->payout_status)) {
                            $user_order->payout_status = $payout_status = 'failed';
                        }
                    }
                }
                break;
            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                break;
        }
        $user_order->save();
        $gateway_profile_id = $gateway_order_id = $params['transactionId'];

        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_order_id = $gateway_order_id;
        $order->save();
        $dateColumnName = "date";
        $amount = null;
        $otherColumns = array();
        //Insert transaction for (siteeventticket_order: "Tickets Purchase of Advanced Events" and sitestoreproduct_order: "Products Purchase of Stores / Marketplace")
        if ($order->source_type == 'siteeventticket_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        } elseif ($order->source_type == 'sitecrowdfunding_backer') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
            $orderTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
            $orderIdColumnName = 'source_id';
            $amount = $user_order->amount;
            $dateColumnName = "timestamp";
            $otherColumns = array('source_type' => $order->source_type);
        }
        if (is_null($amount)) {
            $amount = $user_order->grand_total;
        }

        $transactionParams = array_merge(array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_MangoPay')),
            "$dateColumnName" => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
                ), $otherColumns);
        $transactionsTable->insert($transactionParams);
        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id, 'gateway_payment_key' => $payInTransaction->Id, 'payout_status' => $payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        if ($order->source_type == 'sitecrowdfunding_backer' && ($paymentStatus == 'okay' || $paymentStatus == 'pending' ) && isset($params['reward_id']) && !empty($params['reward_id'])) {
            $user_order->reward_id = $params['reward_id'];
            $user_order->save();
        }
        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            // Update order info
            $user_order->gateway_profile_id = $gateway_profile_id;
            $user_order->save();

            // Payment success
            $user_order->onPaymentSuccess();
        } else if ($paymentStatus == 'pending') {

            // Update order info
            $user_order->gateway_id = $this->_gatewayInfo->gateway_id;
            $user_order->gateway_profile_id = $gateway_profile_id;

            // Payment pending
            $user_order->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order
            $order->onFailure();
            $user_order->onPaymentFailure();

            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed due to some reason, Please contact to site admin.');
        } else {
            // This is a sanity error and cannot produce information a user could use to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }
    }

    /**
     * You must to define this method to process return of subscription transactions. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    protected function onResourceTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg1);
        }

        // Get related info
        $user = $order->getUser();
        $moduleObject = $order->getSource();
        $package = $moduleObject->getPackage();

        $moduleName = explode("_", $package->getType());
        $moduleName = $moduleName['0'];

        /**
         * Here 'communityad' is a name of module for "Advertisements / Community Ads" plugin.
         */
        if ($moduleName == 'communityad') {
            // Check subscription state
            if ($moduleObject->payment_status == 'trial') {
                return 'active';
            } elseif ($moduleObject->payment_status == 'pending') {
                return 'pending';
            }
        } else {
            // Check subscription state
            if ($moduleObject->status == 'trial') {
                return 'active';
            } elseif ($moduleObject->status == 'pending') {
                return 'pending';
            }
        }

        $isOneTimeMethodExist = method_exists($package, 'isOneTime');

        // Check for processed
        if ($isOneTimeMethodExist && !$package->isOneTime()) {
            if (empty($params['subscription_id']) || empty($params['customer_id']) || (!empty($params['subscription_id']) && $params['subscription_id'] == 'undefined') || (!empty($params['customer_id']) && $params['customer_id'] == 'undefined')) {
                $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg2);
            }

            $gateway_order_id = $params['subscription_id'];
            $gateway_profile_id = $params['customer_id'];
        } else {
            if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
                $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg2);
            }

            $gateway_order_id = $params['charge_id'];
            $gateway_profile_id = NULL;
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (MangoPay): '
                . print_r($params, true), Zend_Log::INFO);

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        /**
         * Here 'siteeventpaid' is a name of module for "Advanced Events - Events Booking, Tickets Selling & Paid Events" plugin.
         * Here 'sitereviewpaidlisting' is a name of module for "Multiple Listing Types - Paid Listings Extension" plugin.
         * You can leave empty such code of blocks if you have not enabled these plugins.
         */
        if ($moduleName == 'siteeventpaid' || $moduleName == 'sitereviewpaidlisting') {
            $parentPluginName = explode("_", $moduleObject->getType());
            $parentPluginName = $parentPluginName['0'];
            $otherinfo = Engine_Api::_()->getDbTable('otherinfo', $parentPluginName)->getOtherinfo($order->source_id);
            $otherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
            $otherinfo->gateway_profile_id = $gateway_profile_id;
            $otherinfo->save();
        } elseif ($moduleName == 'communityad') {
            $moduleObject->gateway_id = $this->_gatewayInfo->gateway_id;
            $moduleObject->gateway_profile_id = $gateway_order_id;
            $moduleObject->save();
        } else {
            $moduleObject->gateway_id = $this->_gatewayInfo->gateway_id;
            $moduleObject->gateway_profile_id = $gateway_profile_id;
            $moduleObject->save();
        }

        // Insert transaction
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', $moduleName);
        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_MangoPay')),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 'payment',
            'state' => 'okay',
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $package->price,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Enable now
        if ($giveBenefit) {

            //This is the same as sale_id  
            $moduleObject->onPaymentSuccess();

            // send notification
            if ($moduleObject->didStatusChange()) {

                /*
                 * Here 'payment_subscription' is used for SocialEngine signup subscription plans.
                 */
                if ($order->source_type == 'payment_subscription') {

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("ACTIVE", $moduleObject->getIdentity());
                }
            }

            return 'active';
        }

        // Enable later
        else {

            //This is the same as sale_id  
            $moduleObject->onPaymentPending();

            // send notification
            if ($moduleObject->didStatusChange()) {

                /*
                 * Here 'payment_subscription' is used for SocialEngine signup subscription plans.
                 */
                if ($order->source_type == 'payment_subscription') {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("PENDING", $moduleObject->getIdentity());
                }
            }

            return 'pending';
        }
    }

    /**
     * You must to define this method to process return of a site admin after commissions/bills payment (if you have enabled the "Payment to Website / Site Admin" flow). You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onResourceBillTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg1);
        }

        // Get related info
        $user = $order->getUser();
        $moduleBill = $order->getSource();

        $moduleName = explode("_", $moduleBill->getType());
        $moduleName = $moduleName['0'];

        // Check subscription state
        if ($moduleBill->status == 'trial') {
            return 'active';
        } else
        if ($moduleBill->status == 'pending') {
            return 'pending';
        }

        if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (MangoPay): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['charge_id'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $moduleBill->gateway_id = $this->_gatewayInfo->gateway_id;
        $moduleBill->gateway_profile_id = $gateway_profile_id;
        $moduleBill->save();

        $paymentStatus = 'okay';

        /*
         * Here 'siteeventticket_eventbill' is used for "Advanced Events - Events Booking, Tickets Selling & Paid Events" commissions.
         * Here 'sitestoreproduct_storebill' is used for "Stores / Marketplace - Ecommerce" commissions. 
         */
        if ($order->source_type == 'siteeventticket_eventbill') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_storebill') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        }

        // Insert transaction
        $transactionParams = array(
            'user_id' => $order->user_id,
            'sender_type' => 2,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_MangoPay')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $moduleBill->amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            $moduleBill->gateway_profile_id = $gateway_profile_id;

            // Payment success
            $moduleBill->onPaymentSuccess();

            return 'active';
        } else if ($paymentStatus == 'pending') {

            // Update advertiesment info
            $moduleBill->gateway_profile_id = $gateway_profile_id;

            // Payment pending
            $moduleBill->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order and advertiesment?
            $order->onFailure();
            $moduleBill->onPaymentFailure();
            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed due to some reason, Please contact to site admin.');
        } else {
            // This is a sanity error and cannot produce information a user could use to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * Method to process payment transaction after payment request made by sellers (if you have enabled the "Direct Payment to Sellers" flow). You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserRequestTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $user_request = $order->getSource();
        $user = $order->getUser();

        if ($user_request->payment_status == 'pending') {
            return 'pending';
        }

        if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (MangoPay): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['charge_id'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $user_request->payment_status = 'active';
        $user_request->gateway_profile_id = $gateway_order_id;
        $user_request->save();

        $paymentStatus = 'okay';

        //Insert transaction
        /*
         * Here 'siteeventticket_paymentrequest' is used for "Advanced Events - Events Booking, Tickets Selling & Paid Events" payment requests.
         * Here 'sitestoreproduct_paymentrequest' is used for "Stores / Marketplace - Ecommerce" payment requests. 
         */
        if ($order->source_type == 'siteeventticket_paymentrequest') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_paymentrequest') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        }

        $transactionParams = array(
            'user_id' => $order->user_id,
            'sender_type' => 1,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_MangoPay')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $user_request->response_amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            $user_request->gateway_profile_id = $gateway_profile_id;

            // Payment success
            $user_request->onPaymentSuccess();

            return 'active';
        } else if ($paymentStatus == 'pending') {

            $user_request->gateway_profile_id = $gateway_profile_id;

            // Payment pending
            $user_request->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order and advertiesment?
            $order->onFailure();
            $user_request->onPaymentFailure();

            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed due to some reason, Please contact to site admin.');
        } else {
            // This is a sanity error and cannot produce information a user could use to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * You must to define this method for processing of IPN/Webhooks requests. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. Here, we have given some sample code for various actions performed under IPN/Webhooks. You need to update the code according to your gateway requirement.
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    protected function onResourceTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg7 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg7);
        }

        // Get related info
        $user = $order->getUser();
        $moduleObject = $order->getSource();
        $package = $moduleObject->getPackage();

        $moduleName = explode("_", $package->getType());
        $moduleName = $moduleName['0'];

        // Get IPN data
        $rawData = $ipn->getData();

        // switch message_type
        switch ($rawData['type']) {

            case 'charge.refunded':
                // Payment Refunded
                $moduleObject->onRefund();
                // send notification
                if ($moduleObject->didStatusChange()) {

                    /*
                     * Here 'payment' module is used for SocialEngine signup subscription plans.
                     */
                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_refunded', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("REFUNDED", $moduleObject->getIdentity());
                    }
                }

                break;

            case 'customer.subscription.deleted':

                $moduleObject->onCancel();
                // send notification
                if ($moduleObject->didStatusChange()) {

                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_cancelled', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("CANCELLED", $moduleObject->getIdentity());
                    }
                }
                break;

            case 'customer.subscription.updated':
                $moduleObject->onPaymentSuccess();

                if ($moduleObject->didStatusChange()) {

                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("RECURRENCE", $moduleObject->getIdentity());
                    }
                }

                $this->cancelSubscriptionOnExpiry($moduleObject, $package);

                break;

            case 'invoice.payment_failed':
                $moduleObject->onPaymentFailure();

                if ($moduleObject->didStatusChange()) {

                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_overdue', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("OVERDUE", $moduleObject->getIdentity());
                    }
                }
                break;

            default:
                throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                        'type %1$s', $rawData['type']));
                break;
        }

        return $this;
    }

    /**
     * You must to define this method to cancel a created package / sign-up subscription plan. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    protected function cancelResourcePackage($transactionId, $note = null) {

        $profileId = null;

        if ($transactionId instanceof Siteevent_Model_Event) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getOtherinfo($transactionId->getIdentity())->gateway_profile_id;
        } elseif ($transactionId instanceof Sitereview_Model_Listing) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getOtherinfo($transactionId->getIdentity())->gateway_profile_id;
        } elseif (($transactionId instanceof Sitepage_Model_Page) || ($transactionId instanceof Sitebusiness_Model_Business) || ($transactionId instanceof Sitegroup_Model_Group) || ($transactionId instanceof Sitestore_Model_Store) || ($transactionId instanceof Payment_Model_Subscription)) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = $transactionId->gateway_profile_id;
        } else if (is_string($transactionId)) {
            $profileId = $transactionId;
        } else {

            return $this;
        }

        try {
            $r = $this->getService()->cancelRecurringPaymentsProfile($profileId, $note);
        } catch (Exception $e) {
            
        }

        return $this;
    }

    /**
     * Common method for cancelling a package/sign-up subscription on its expiry. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @params $moduleObject
     * @params $package
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelSubscriptionOnExpiry($moduleObject, $package) {

        if ($package->isOneTime() || empty($package->duration) || empty($package->duration_type) || $package->duration_type == 'forever') {
            return $this;
        }

        $totalDaysPerCycle = $package->recurrence * Engine_Api::_()->sitegateway()->totalDaysInPeriod($package->recurrence_type);

        $totalDays = $package->duration * Engine_Api::_()->sitegateway()->totalDaysInPeriod($package->duration_type);

        $diff_days = round(strtotime(date('Y-m-d H:i:s')) - strtotime($moduleObject->creation_date)) / 86400;
        $remainingDays = round($totalDays - $diff_days);

        if ($remainingDays < 0) {
            return $this;
        }

        if ($remainingDays < $totalDaysPerCycle) {

            $moduleName = explode("_", $moduleObject->getType());
            $moduleName = $moduleName['0'];

            if ($moduleName == 'siteevent' || $moduleName == 'sitereview') {
                $profileId = Engine_Api::_()->getDbTable('otherinfo', $moduleName)->getOtherinfo($moduleObject->getIdentity())->gateway_profile_id;
            } elseif ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore' || $moduleName == 'payment') {
                $profileId = $moduleObject->gateway_profile_id;
            } else {
                return $this;
            }

            try {

                $r = $this->getService()->cancelRecurringPaymentsProfile($profileId);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * Method to process an IPN/Webhooks request for an order transaction.
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onUserOrderTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg7 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg7);
        }

        // Get related info
        $user = $order->getUser();
        $user_order = $order->getSource();

        // Get IPN data
        $rawData = $ipn->getData();
        // switch message_type
        switch ($rawData['type']) {

            case 'charge.refunded':
                // Payment Refunded
                $user_order->onRefund();

                break;

            default:
                throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                        'type %1$s', $rawData['message_type']));
                break;
        }

        return $this;
    }

    /**
     * Create a transaction object from given parameters. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @return Engine_Payment_Transaction
     */
    public function createResourceBillTransaction($object_id, $bill_id, $params = array()) {

        $params['product_id'] = $object_id;
        $params['quantity'] = 1;

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Method to create a transaction for an order. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param $parent_order_id
     * @param array $params
     * @param User_Model_User $user
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserOrderTransaction($parent_order_id, array $params = array(), $user = NULL) {

        $order = Engine_Api::_()->getItem($params['source_type'], $parent_order_id);
        /**
         * Here siteeventticket_order is used for "Tickets Purchase of Advanced Events" and sitestoreproduct_order for "Products Purchase of Stores / Marketplace". You can add order specific date if necessary.
         */
        if ($params['source_type'] == 'siteeventticket_order') {
            $sellerGateway = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $order->event_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            $tag = "Event Ticket";
        } elseif ($params['source_type'] == 'sitestoreproduct_order') {
            $sellerGateway = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $order->store_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            $tag = "Store Product";
        } elseif ($params['source_type'] == 'sitecrowdfunding_backer') {
            $amount = $order->amount;
            $sellerGateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $order->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            $tag = "Back on Project";
        }
        if (!isset($amount))
            $amount = $order->grand_total;

        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $mode = 'live';
        if ($adminGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        //Mango Pay Charge

        $packet = array();
        $packet['credited_wallet_id'] = $sellerGateway->config[$mode]['mangopay_wallet_id'];
        $packet['author_id'] = $sellerGateway->config[$mode]['mangopay_user_id'];
        $packet['db_currency'] = $sitegatewayApi->getCurrency();
        $packet['db_amount'] = $sitegatewayApi->getPrice($amount);
        $packet['fees_currency'] = $sitegatewayApi->getCurrency();
        $packet['fees_amount'] = 0;
        $packet['return_url'] = $params['return_url'];
        $packet['tag'] = $tag;
        $results = $adminGateway->getService()->createPayIn($packet);
        $params['mangoPayRedirectUrl'] = $results->ExecutionDetails->RedirectURL;
        //Create transaction
        $transaction = $this->createTransaction($params);
        return $transaction;
    }

    /**
     * Method to create a transaction for a payment request made by sellers. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param User_Model_User $user
     * @param $request_id
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserRequestTransaction(User_Model_User $user, $request_id, array $params = array()) {

        //FETCH RESPONSE DETAIL
        if ($params['source_type'] == 'siteeventticket_paymentrequest') {
            $response = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket')->getResponseDetail($request_id);
        } elseif ($params['source_type'] == 'sitestoreproduct_paymentrequest') {
            $response = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->getResponseDetail($request_id);
        }

        $params = array_merge($params, array(
            'AMT' => @round($response[0]['response_amount'], 2),
            'ITEMAMT' => @round($response[0]['response_amount'], 2),
            'SOLUTIONTYPE' => 'sole',
        ));

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Method to process an IPN/Webhooks request for an order transaction.
     *
     * @param Engine_Payment_Ipn $ipn
     * @return Engine_Payment_Plugin_Abstract
     */
    public function onIpn(Engine_Payment_Ipn $ipn) {

        //You can get the notification data from below code
        $rawData = $ipn->getData();

        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');

        $order = null;

        //You need to fetch the order object using order id sent by this gateway in notification. For reference you can see the below code for Stripe: Transaction IPN - get order by subscription_id
        if (!$order && !empty($rawData['data']->object->id)) {
            $gateway_order_id = $rawData['data']->object->id;

            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_order_id = ?' => $gateway_order_id,
            ));
        }

        if ($order) {
            return $this->onResourceIpn($order);
        } else {
            $error_msg17 = Zend_Registry::get('Zend_Translate')->_('Unknown or unsupported IPN type, or missing transaction or order ID');
            throw new Engine_Payment_Plugin_Exception($error_msg17);
        }
    }

    /**
     * Method to generate link for an order details page using orderId.
     *
     * @param string $orderId
     * @return string
     */
    public function getOrderDetailLink($orderId) {
        
    }

    /**
     * Method to generate link for an order details page using transactionId.
     *
     * @param string $transactionId
     * @return string
     */
    public function getTransactionDetailLink($transactionId) {
        if ($this->getGateway()->getTestMode()) {
            return 'https://dashboard.sandbox.mangopay.com/Users/PayIn/' . $transactionId;
        } else {
            return 'https://dashboard.mangopay.com/Users/PayIn/' . $transactionId;
        }
    }

    /**
     * Get raw data about an order or recurring payment profile.
     *
     * @param string $orderId
     * @return array
     */
    public function getOrderDetails($orderId) {

        try {
            return $this->getService()->detailRecurringPaymentsProfile($orderId);
        } catch (Exception $e) {
            echo $e;
        }

        try {
            return $this->getTransactionDetails($orderId);
        } catch (Exception $e) {
            echo $e;
        }

        return false;
    }

    /**
     * Get raw data about a transaction.
     *
     * @param $transactionId
     * @return array
     */
    public function getTransactionDetails($transactionId) {
        return $this->getService()->detailTransaction($transactionId);
    }

    /**
     * Get the form for editing the gateway credentials.
     *
     * @return Engine_Form
     */
    public function getAdminGatewayForm() {
        return new Sitegateway_Form_Admin_Gateway_MangoPay();
    }

    /**
     * Process the form for editing the gateway credentials.
     *
     * @return Engine_Form
     */
    public function processAdminGatewayForm(array $values) {
        return $values;
    }

    public function payoutAllTransaction($params) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $message = "";
        $amount = 0;
        $commission = 0;
        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $sellerGatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            $sellerGatewayObj = $sellerGatewayTable->fetchRow(array('project_id = ?' => $params['project_id'], 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['project_id']);
            $backersTableName = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->info('name');
            $content = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getCommissionValue($params['project_id']);
            $commission = $content->total_commission ? $content->total_commission : 0;
            $amount = $content->total_amount ? $content->total_amount : 0;
            $transactionsTableName = Engine_Api::_()->getDbtable('transactions', 'sitegateway')->info('name');
            $parentResourceType = 'sitecrowdfunding_project';
            $parentResourceId = $params['project_id'];
        }
        $db = $sellerGatewayTable->getAdapter();
        $adminGateway = $this->getGateway();
        $adminGatewayAP = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $mode = 'live';
        if ($adminGatewayAP->config['test_mode']) {
            $mode = 'sandbox';
        }

        if (!(isset($sellerGatewayObj->config[$mode]['mangopay_wallet_id']) && isset($sellerGatewayObj->config[$mode]['mangopay_user_id']) && isset($sellerGatewayObj->config[$mode]['bank_account_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_wallet_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_user_id']) && !empty($sellerGatewayObj->config[$mode]['bank_account_id']))) {
            return false;
        }
        $walletId = $sellerGatewayObj->config[$mode]['mangopay_wallet_id'];
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $mangoPayCharge = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.mangopay.fees.charge', 0);
        if (empty($mangoPayCharge)) {
            $mangoPayChargeAmt = 0;
        } else {
            $mangoPayChargeAmt = ($amount / 100) * $mangoPayCharge;
        }
        $commisionValue = $commission + $mangoPayChargeAmt;
        try {
            //Fetch the wallet detail eg Amount ,Currency
            $wallet = $adminGateway->getService()->viewWallet($walletId);
//$commissionValue
            //Check Wallet having money or not
            if ($wallet && isset($wallet->Balance->Amount) && $wallet->Balance->Amount > 0) {
                $pauoutParams = array();
                $pauoutParams['bank_account_id'] = $sellerGatewayObj->config[$mode]['bank_account_id'];
                $pauoutParams['user_id'] = $sellerGatewayObj->config[$mode]['mangopay_user_id'];
                $pauoutParams['wallet_id'] = $walletId;
                $pauoutParams['amount'] = $wallet->Balance->Amount;
                $pauoutParams['currency'] = $wallet->Balance->Currency;
                $pauoutParams['fees_amount'] = $sitegatewayApi->getPrice($commisionValue);
                $pauoutParams['fees_currency'] = $wallet->Balance->Currency;

                if (isset($sellerGatewayObj->config[$mode]['project_payout_id'])) {
                    $payoutDetail = $adminGateway->getService()->viewPayout($sellerGatewayObj->config[$mode]['project_payout_id']);
                    if ($payoutDetail && $payoutDetail->Status == 'FAILED') {
                        $payoutDetail = $adminGateway->getService()->createPayout($pauoutParams);
                    }
                } else {
                    $payoutDetail = $adminGateway->getService()->createPayout($pauoutParams);
                }
                $payout_status = '';
                if ($payoutDetail) {
                    $item->payout_status = 'payout';
                    $item->save();
                    switch ($payoutDetail->Status) {
                        case 'CREATED' :
                            $payout_status = 'pending';
                            $message = $view->translate("MangoPay Payment status is inprocess. MangoPay team verify then payout will be made.");
                            break;
                        case 'SUCCEEDED' :
                            $payout_status = 'success';
                            break;
                        case 'FAILED' :
                            $payout_status = 'failed';
                            $message = $payoutDetail->ResultMessage;
                            break;
                        default :
                            $payout_status = 'failed';
                    }
                    if ($payout_status == 'pending' || $payout_status == 'success') {
                        $config = array_merge(
                                $sellerGatewayObj->config[$mode], array('project_payout_id' => $payoutDetail->Id)
                        );
                        $cf = $sellerGatewayObj->config;
                        $cf[$mode] = $config;
                        $sellerGatewayObj->config = $cf;
                        $sellerGatewayObj->save();
                    }
                } else {
                    $payout_status = 'failed';
                }
                if (!empty($payoutDetail) && $payout_status != 'success') {
                    Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitegateway_payment_status', array(
                        'payout_id' => $payoutDetail->Id,
                        'resource_type' => $parentResourceType,
                        'resource_id' => $parentResourceId
                    ));
                }
            } else {
                $message = "No Balance in Wallet to payout";
            }
        } catch (Exception $ex) {
            $message = $ex->GetMessage();
            $payout_status = 'failed';
        }


        if ($params['resource_type'] == 'sitecrowdfunding_backer' && strlen($payout_status) > 0) {
            $query = "update {$transactionsTableName} set payout_status='{$payout_status}' where gateway_id ={$adminGatewayAP->gateway_id} and resource_type='sitecrowdfunding_backer' and resource_id in (select backer_id from {$backersTableName} where project_id = {$params['project_id']} and gateway_id ={$adminGatewayAP->gateway_id} and payment_status='active');";
            $db->query($query);
            $query = "update $backersTableName set payout_status = '{$payout_status}' where project_id = {$params['project_id']} and gateway_id ={$adminGatewayAP->gateway_id} and payment_status='active'";
            $db->query($query);
        }
        return $message;
    }

    public function payoutTransaction($params, $transaction = null) {

        $adminGatewayAP = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $mode = 'live';
        if ($adminGatewayAP->config['test_mode']) {
            $mode = 'sandbox';
        }
        $settingApi = Engine_Api::_()->sitegateway();
        $data = array('return' => 1, 'message' => 'Success');
        if (empty($transaction) && isset($params['resource_type']) && isset($params['resource_id']) && !empty($params['resource_type']) && !empty($params['resource_id'])) {
            $transaction = $settingApi->getTransaction($params['resource_type'], $params['resource_id']);
            $order = Engine_Api::_()->getItem($params['resource_type'], $params['resource_id']);

            if ($params['resource_type'] == 'sitecrowdfunding_backer') {
                $sellerGatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $sellerGatewayObj = $sellerGatewayTable->fetchRow(array('project_id = ?' => $params['project_id'], 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
                $amount = $order->amount;
            } elseif ($params['resource_type'] == 'sitestoreproduct_order') {
                $sellerGatewayTable = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
                $sellerGatewayObj = $sellerGatewayTable->fetchRow(array('store_id = ?' => $params['store_id'], 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            }
            if (!(isset($sellerGatewayObj->config[$mode]['mangopay_user_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_user_id']) )) {
                $data['return'] = 0;
                $data['message'] = 'Configure MangoPay Setting.';
                return $data;
            }
            if (!isset($amount)) {
                $amount = $order->grand_total;
            }
            $params['bank_account_id'] = $sellerGatewayObj->config[$mode]['bank_account_id'];
            $params['user_id'] = $sellerGatewayObj->config[$mode]['mangopay_user_id'];
            $params['wallet_id'] = $sellerGatewayObj->config[$mode]['mangopay_wallet_id'];
            $params['tag'] = "";
        }
        if (empty($transaction)) {
            $data['return'] = 3;
            $data['message'] = 'Transaction not found';
            return $data;
        }
        try {
            $payoutDetail = array();
            $payout_status = "";
            $sitegatewayApi = Engine_Api::_()->sitegateway();
            $mangoPayCharge = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.mangopay.fees.charge', 0);
            if (empty($mangoPayCharge)) {
                $mangoPayChargeAmt = 0;
            } else {
                $mangoPayChargeAmt = ($amount / 100) * $mangoPayCharge;
            }
            $commisionValue = $order->commission_value + $mangoPayChargeAmt;
            if ($transaction->payout_status != 'success') {
                $payInTransaction = $this->getGateway()->getService()->getPayInDetail($transaction->gateway_payment_key);
                if (strtolower($payInTransaction->Status) == 'succeeded') {
                    $params['amount'] = $payInTransaction->CreditedFunds->Amount;
                    $params['currency'] = $payInTransaction->CreditedFunds->Currency;
                    $params['fees_amount'] = $sitegatewayApi->getPrice($commisionValue);
                    $params['fees_currency'] = $payInTransaction->CreditedFunds->Currency;
                    $params['tag'] = $tag;

                    $payoutDetail = $this->getGateway()->getService()->createPayout($params);
                    $payoutDetail = $this->getGateway()->getService()->viewPayout($payoutDetail->Id);
                    if ($payoutDetail->Status == 'CREATED') {

                        $payout_status = "pending";
                        $data['message'] = "Payout transaction is created ,Waiting for approval ";
                    } else if ($payoutDetail->Status == 'SUCCEEDED') {
                        $payout_status = "success";
                    } else {
                        $payout_status = "failed";
                        $data['return'] = 1;
                        $data['message'] = $payoutDetail->ResultMessage;
                    }
                } else {
                    $data['return'] = 1;
                    $data['message'] = 'Transaction status :' . $payInTransaction->Status;
                }
            } else {
                $data['return'] = 1;
                $data['message'] = 'Amount is already refunded for this transaction';
            }
        } catch (Exception $ex) {
            $payout_status = 'failed';
            $data['return'] = 0;
            $data['message'] = $ex->getMessage();
        }
        if (!empty($payout_status)) {
            $transaction->payout_status = $payout_status;
            $transaction->save();
            $order = Engine_Api::_()->getItem($params['resource_type'], $transaction->resource_id);
            if ($order && isset($order->payout_status)) {
                $order->payout_status = $payout_status;
                $order->save();
            }
            if (!empty($payoutDetail) && $payout_status != 'success') {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitegateway_payment_status', array(
                    'payout_id' => $payoutDetail->Id,
                    'resource_type' => $params['resource_type'],
                    'resource_id' => $params['resource_id']
                ));
            }
        }
        return $data;
    }

    public function refundAllTransaction($params) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $message = "";
        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $sellerGatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            $sellerGatewayObj = $sellerGatewayTable->fetchRow(array('project_id = ?' => $params['project_id'], 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['project_id']);
        }
        $success = false;
        $failed = false;
        $db = $sellerGatewayTable->getAdapter();
        $adminGatewayAP = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $adminGateway = $this->getGateway();
        $mode = 'live';
        if ($adminGatewayAP->config['test_mode']) {
            $mode = 'sandbox';
        }
        if (!(isset($sellerGatewayObj->config[$mode]['mangopay_user_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_user_id']) )) {
            return false;
        }
        $transactions = Engine_Api::_()->getDbtable('transactions', 'sitegateway')->getAllTransactions(array('project_id' => $params['project_id'], 'gateway_id' => $adminGateway->gateway_id, 'gateway_payment_key' => '', 'resource_type' => $params['resource_type']));
        $refundParams = array();
        $refundParams['user_id'] = $sellerGatewayObj->config[$mode]['mangopay_user_id'];
        $refundParams['tag'] = "";
        foreach ($transactions as $transaction) {
            $this->refundTransaction($refundParams, $transaction);
        }
        $item->payout_status = 'refund';
        $item->save();
        $message = "";
        if ($success && $failed) {
            //Partially Success
            $message = $view->translate("Partially transaction refund in MangoPay");
        } elseif ($success == true && $failed == false) {
            //all success
            $message = $view->translate("All transaction successfully refund in MangoPay");
        } elseif ($success == false && $failed == true) {
            //failed
            $message = $view->translate("All transaction failed refund in MangoPay");
        }
        return $message;
    }

    public function refundTransaction($params, $transaction = null) {
        $settingApi = Engine_Api::_()->sitegateway();
        $data = array('return' => 1, 'message' => 'Success');
        $adminGateway = $this->getGateway();
        $adminGatewayAP = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $mode = 'live';
        if ($adminGatewayAP->config['test_mode']) {
            $mode = 'sandbox';
        }
        if (empty($transaction) && isset($params['resource_type']) && isset($params['resource_id']) && !empty($params['resource_type']) && !empty($params['resource_id'])) {
            $transaction = $settingApi->getTransaction($params['resource_type'], $params['resource_id']);

            if ($params['resource_type'] == 'sitecrowdfunding_backer') {
                $sellerGatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $sellerGatewayObj = $sellerGatewayTable->fetchRow(array('project_id = ?' => $params['project_id'], 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            } elseif ($params['resource_type'] == 'sitestoreproduct_order') {
                $sellerGatewayTable = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
                $sellerGatewayObj = $sellerGatewayTable->fetchRow(array('store_id = ?' => $params['store_id'], 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            }
            if (!(isset($sellerGatewayObj->config[$mode]['mangopay_user_id']) && !empty($sellerGatewayObj->config[$mode]['mangopay_user_id']) )) {
                $data['return'] = 0;
                $data['message'] = 'Configure MangoPay Setting.';
                return $data;
            }
            $params['user_id'] = $sellerGatewayObj->config[$mode]['mangopay_user_id'];
            $params['tag'] = "";
        }
        if (empty($transaction)) {
            $data['return'] = 3;
            $data['message'] = 'Transaction not found';
            return $data;
        }
        try {
            $refundDetail = array();
            $refund_status = "";
            if ($transaction->refund_status != 'success') {
                $refundDetail = $this->getGateway()->getService()->createRefund($transaction->gateway_payment_key, $params);
            } else {
                $refund_status = 'success';
                $data['return'] = 1;
                $data['message'] = 'Transaction has already been successfully refunded';
            }

            if (!empty($refundDetail)) {
                switch ($refundDetail->Status) {
                    case 'CREATED' :
                        $refund_status = 'pending';
                        $data['return'] = 2;
                        $data['message'] = $refundDetail->ResultMessage;
                        break;
                    case 'SUCCEEDED' :
                        $refund_status = 'success';
                        $payment_status = 'refunded';
                        $success = true;
                        break;
                    case 'FAILED' :
                        $refund_status = 'failed';
                        $data['return'] = 0;
                        $data['message'] = $refundDetail->ResultMessage;
                        break;
                }
            }
        } catch (Exception $ex) {
            $refund_status = 'failed';
            $data['return'] = 0;
            $data['message'] = $ex->getMessage();
        }
        if (!empty($refund_status)) {
            $transaction->refund_status = $refund_status;
            $transaction->save();
            $order = Engine_Api::_()->getItem($transaction->resource_type, $transaction->resource_id);
            if ($order && isset($order->refund_status)) {
                $order->refund_status = $refund_status;
                if ($payment_status == 'refunded') {
                    $order->payment_status = $payment_status;
                }
                $order->save();
            }
        }
        return $data;
    }

}
