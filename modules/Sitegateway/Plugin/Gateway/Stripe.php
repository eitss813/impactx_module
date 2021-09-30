<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Stripe.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Plugin_Gateway_Stripe extends Sitegateway_Plugin_Gateway_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
    }

    /**
     * Get the gateway object
     *
     * @return Engine_Payment_Gateway
     */
    public function getGateway() {

        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_Stripe';
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
     * Common method for create a transaction for a package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $resourceObject
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    protected function createResourceTransaction($user, $resourceObject, $package, $params = array()) {

        $params['fixed'] = true;
        $productInfo = $this->getService()->detailVendorPlan($package->getGatewayIdentity());
        if (!empty($productInfo)) {
            $params['product_id'] = $productInfo['product_id'];
        }
        $params['quantity'] = 1;

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Process return of user order transaction
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

        //Here we are adding the validation check that buyer made the payment successfully or not
        if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (Stripe): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['charge_id'];

        // @todo process total?
        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();
        $dateColumnName = "date";
        $paymentStatus = 'okay';
        $amount = null;
        $otherColumns = array();
        // Insert transaction
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
        } elseif ($order->source_type == 'sitecredit_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecredit');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitecredit');
            $orderIdColumnName = 'order_id';
        }
        if (is_null($amount)) {
            $amount = $user_order->grand_total;
        }
        $transactionParams = array_merge(array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Stripe')),
            "$dateColumnName" => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $amount, // @todo use this or gross (-fee)?
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
                ), $otherColumns);
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        if ($order->source_type == 'sitecrowdfunding_backer' && ($paymentStatus == 'okay' || $paymentStatus == 'pending' ) && isset($params['reward_id']) && !empty($params['reward_id'])) {
            $user_order->reward_id = $params['reward_id'];
            $user_order->save();
        }
        // Get benefit setting
        if (!$order->source_type == 'sitecredit_order') {
            $giveBenefit = $transactionsTable->getBenefitStatus($user);
        }
        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            // Update order info
            $user_order->gateway_profile_id = $gateway_profile_id;
            $user_order->save();

            // Payment success
            $user_order->onPaymentSuccess();

            return 'active';
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
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }
    }

    /**
     * Common method for process return of subscription transaction
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

        if ($moduleName == 'communityad') {
            // Check subscription state
            if ($moduleObject->payment_status == 'trial') {
                return 'active';
            } elseif ($moduleObject->payment_status == 'pending') {
                return 'pending';
            }
        } elseif ($moduleName == 'sitead') {
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
        $this->getGateway()->getLog()->log('Return (Stripe): '
                . print_r($params, true), Zend_Log::INFO);

        // @todo process total?
        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();
        $orderIdColumnName = "order_id";
        $otherColumns = array();
        // Update subscription
        if ($moduleName == 'siteeventpaid' || $moduleName == 'sitereviewpaidlisting') {
            $parentPluginName = explode("_", $moduleObject->getType());
            $parentPluginName = $parentPluginName['0'];
            $otherinfo = Engine_Api::_()->getDbTable('otherinfo', $parentPluginName)->getOtherinfo($order->source_id);
            $otherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
            $otherinfo->gateway_profile_id = $gateway_profile_id;
            $otherinfo->save();
        } elseif ($moduleName == 'sitecrowdfunding') {
            $otherinfo = Engine_Api::_()->getDbTable('otherinfo', "sitecrowdfunding")->getOtherinfo($order->source_id);
            $otherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
            $otherinfo->gateway_profile_id = $gateway_profile_id;
            $otherinfo->save();
            $orderIdColumnName = "payment_order_id";
            $otherColumns = array('source_type' => $order->source_type, 'source_id' => $order->source_id);
        } elseif ($moduleName == 'communityad') {
            $moduleObject->gateway_id = $this->_gatewayInfo->gateway_id;
            $moduleObject->gateway_profile_id = $gateway_order_id;
            $moduleObject->save();
        } elseif ($moduleName == 'sitead') {
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
        $transactionParams = array_merge(array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Stripe')),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            "$orderIdColumnName" => $order->order_id,
            'type' => 'payment',
            'state' => 'okay',
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $package->price,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
                ), $otherColumns);
        $last_id=$transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Enable now
        if ($giveBenefit) {

            //This is the same as sale_id  
            $moduleObject->onPaymentSuccess();

            // send notification
            if ($moduleObject->didStatusChange()) {

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

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
                //SESSION SET OF CREDITS.
                $param['user_id']=$user->getIdentity();
                if ($order->source_type == 'payment_subscription') {
                $creditSession = new Zend_Session_Namespace('payment_subscription_credit');

                if (!empty($creditSession->paymentSubscriptionCreditDetail)) {
                    $creditDetail = unserialize($creditSession->paymentSubscriptionCreditDetail);
                        if($creditDetail['credit_points']) {
                            $param['type_id']=$order->order_id;
                            $param['credit_point']=-$creditDetail['credit_points'];
                            $param['type']='subscription';
                            $param['reason']='used credits for subscription';
                            $credit_table = Engine_Api::_()->getDbtable('credits','sitecredit');    
                            $credit_table->insertCredit($param);
                         }

                        $creditSession->paymentSubscriptionCreditDetail = null;
                }
                } else {
                   
                   $creditSession = new Zend_Session_Namespace('credit_package_payment_'.$package->getType());

                    if (!empty($creditSession->packagePaymentCreditDetail)) {
                        $creditDetail = unserialize($creditSession->packagePaymentCreditDetail);
                        if($creditDetail['credit_points']) {
                            $param['type_id']=$order->order_id;
                            $param['credit_point']=-$creditDetail['credit_points'];
                            $param['type']=strtolower($package->getType());
                            $param['reason']='used credits for package purchase';
                            $param['resource_type']=$order->source_type;
                            $param['resource_id']=$order->source_id;
                            $credit_table = Engine_Api::_()->getDbtable('credits','sitecredit');    
                            $credit_table->insertCredit($param);
                            print_r($last_id);
                            //$last_id = $transactionsTable->getAdapter()->lastInsertId();
                            Engine_Api::_()->getDbtable('transactions', $moduleName)->update(array('amount' => $package->price-$creditDetail['credit_amount']), array('transaction_id =?' => $last_id));
                        }
                    }
                    $creditSession->packagePaymentCreditDetail = null;
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
     * Common method for process return of site admin commission/bill transaction
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
        $this->getGateway()->getLog()->log('Return (Stripe): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['charge_id'];

        // @todo process total?
        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $moduleBill->gateway_id = $this->_gatewayInfo->gateway_id;
        $moduleBill->gateway_profile_id = $gateway_profile_id;
        $moduleBill->save();

        $paymentStatus = 'okay';

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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Stripe')),
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

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
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
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * Process return of user order transaction
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
        $this->getGateway()->getLog()->log('Return (Stripe): '
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

        // Insert transaction
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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Stripe')),
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

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
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
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * Common method for processing ipn of package transaction
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

            case 'account.updated':
            case 'account.application.deauthorized':
            case 'account.external_account.created':
            case 'account.external_account.deleted':
            case 'account.external_account.updated':
            case 'application_fee.created':
            case 'application_fee.refunded':
            case 'application_fee.refund.updated':
            case 'balance.available':
            case 'bitcoin.receiver.created':
            case 'bitcoin.receiver.filled':
            case 'bitcoin.receiver.updated':
            case 'bitcoin.receiver.transaction.created':
            case 'charge.captured':
            case 'charge.failed':break;

            case 'charge.refunded':
                // Payment Refunded
                $moduleObject->onRefund();
                // send notification
                if ($moduleObject->didStatusChange()) {

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

            case 'charge.succeeded':
            case 'charge.updated':
            case 'charge.dispute.closed':
            case 'charge.dispute.created':
            case 'charge.dispute.funds_reinstated':
            case 'charge.dispute.funds_withdrawn':
            case 'charge.dispute.updated':
            case 'coupon.created':
            case 'coupon.deleted':
            case 'coupon.updated':
            case 'customer.created':
            case 'customer.deleted':
            case 'customer.updated':
            case 'customer.bank_account.deleted':
            case 'customer.discount.created':
            case 'customer.discount.deleted':
            case 'customer.discount.updated':
            case 'customer.source.created':
            case 'customer.source.deleted':
            case 'customer.source.updated':
            case 'customer.subscription.created':break;

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

            case 'customer.subscription.trial_will_end':break;

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

            case 'invoice.created':break;

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

            case 'invoice.payment_succeeded':
            case 'invoice.updated':
            case 'invoiceitem.created':
            case 'invoiceitem.deleted':
            case 'invoiceitem.updated':
            case 'plan.created':
            case 'plan.deleted':
            case 'plan.updated':
            case 'recipient.created':
            case 'recipient.deleted':
            case 'recipient.updated':
            case 'transfer.created':
            case 'transfer.failed':
            case 'transfer.paid':
            case 'transfer.reversed':
            case 'transfer.updated':break;

            default:
                throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                        'type %1$s', $rawData['type']));
                break;
        }

        return $this;
    }

    /**
     * Common method for canceling a package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    protected function cancelResourcePackage($transactionId, $note = null) {

        $profileId = null;
        if ($transactionId instanceof Sitecrowdfunding_Model_Project) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->getOtherinfo($transactionId->getIdentity())->gateway_profile_id;
        } elseif ($transactionId instanceof Siteevent_Model_Event) {
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
            // Should we throw?
            return $this;
        }

        try {
            //INSTEAD OF CANCELING THE PLAN WE SHOULD ASSOCIATE THE NEW PLAN FOR THIS CUSTOMER USING: https://stripe.com/docs/api#update_subscription
            $r = $this->getService()->cancelRecurringPaymentsProfile($profileId, $note);
        } catch (Exception $e) {
            // throw?
        }

        return $this;
    }

    /**
     * Common method for canceling a package subscription on expiry (i.e. disable the recurring payment profile)
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

            if ($moduleName == 'siteevent' || $moduleName == 'sitereview' || $moduleName == 'sitecrowdfunding') {
                $profileId = Engine_Api::_()->getDbTable('otherinfo', $moduleName)->getOtherinfo($moduleObject->getIdentity())->gateway_profile_id;
            } elseif ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore' || $moduleName == 'payment') {
                $profileId = $moduleObject->gateway_profile_id;
            } else {
                return $this;
            }

            try {
                //INSTEAD OF CANCELING THE PLAN WE SHOULD ASSOCIATE THE NEW PLAN FOR THIS CUSTOMER USING: https://stripe.com/docs/api#update_subscription
                $r = $this->getService()->cancelRecurringPaymentsProfile($profileId);
            } catch (Exception $e) {
                // throw?
            }
        }
    }

    /**
     * Process ipn of order transaction
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
     * Create a transaction object from specified parameters
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
     * Create a transaction for order
     *
     * @param $parent_order_id
     * @param array $params
     * @param User_Model_User $user
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserOrderTransaction($parent_order_id, array $params = array(), $user = NULL) {

        $order = Engine_Api::_()->getItem($params['source_type'], $parent_order_id);
        $otherParams = array();
        //ADD ORDER SPECIFIC DATA IF NECESSARY
        if ($params['source_type'] == 'siteeventticket_order') {
            
        } elseif ($params['source_type'] == 'sitestoreproduct_order') {


            $products_sub_total = $tax = $shipping_price = $grand_total = $temp_order_id = 0;
            $cart_products = $orderCouponDetail = array();

            // downpayment remaining amount payment
            if (!empty($params['downpayment_make_payment'])) {
                if (isset($params['store_id'])) {
                    $sitestore = Engine_Api::_()->getItem('sitestore_store', $params['store_id']);
                    unset($params['store_id']);
                    $getStoreId = false;
                } else {
                    $getStoreId = true;
                }

                $order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getRemainingAmountOrderProducts(array('order_id' => $parent_order_id, 'getStoreId' => $getStoreId));

                foreach ($order_products as $product) {
                    $products_sub_total += @round($product['product_price'] * $product['quantity'], 2);
                    $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($product['product_title']);
                    if (empty($sitestore) && empty($temp_order_id)) {
                        $sitestore = Engine_Api::_()->getItem('sitestore_store', $product['store_id']);
                        $temp_order_id = true;
                    }
                    $cart_products[] = array(
                        'NAME' => $sitestore->getTitle() . ': ' . $temp_lang_title,
                        'AMT' => @round($product['product_price'], 2),
                        'QTY' => $product['quantity'],
                    );
                }
                $grand_total = $products_sub_total;
                unset($params['downpayment_make_payment']);
            } else {
                if (isset($params['store_id']) && !empty($params['store_id'])) {
                    $order_products = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getAllOrders($parent_order_id, array('store_id' => $params['store_id'], 'isDownPaymentEnable' => $params['isDownPaymentEnable']));
                    unset($params['store_id']);
                } else {
                    $order_products = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getAllOrders($parent_order_id, array('isDownPaymentEnable' => $params['isDownPaymentEnable']));
                }

                foreach ($order_products as $product) {
                    if ($temp_order_id != $product['order_id']) {
                        $sitestore = Engine_Api::_()->getItem('sitestore_store', $product['store_id']);
                        $tax += ( @round($product['store_tax'], 2) + @round($product['admin_tax'], 2) );
                        $shipping_price += @round($product['shipping_price'], 2);
                        $temp_order_id = $product['order_id'];
                        if (!empty($product['coupon_detail'])) {
                            $couponDetail = unserialize($product['coupon_detail']);
                            if (!empty($couponDetail)) {
                                $orderCouponDetail[] = array(
                                    'NAME' => $couponDetail['coupon_code'],
                                    'AMT' => -@round($couponDetail['coupon_amount'], 2),
                                    'QTY' => 1,
                                );
                            }
                        }
                        if (empty($params['isDownPaymentEnable'])) {
                            $grand_total += @round($product['grand_total'], 2);
                            $products_sub_total += @round($product['sub_total'], 2);
                        } else {
                            $products_sub_total += @round($product['downpayment_total'], 2);
                            $tempGrandTotal = round((@round($product['store_tax'], 2) + @round($product['admin_tax'], 2) + @round($product['shipping_price'], 2) + @round($product['downpayment_total'], 2)), 2);
                            $grand_total += $tempGrandTotal;
                        }
                    }

                    $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($product['product_title']);

                    $cart_products[] = array(
                        'NAME' => $sitestore->getTitle() . ': ' . $temp_lang_title,
                        'AMT' => @round($product['price'], 2),
                        'QTY' => $product['quantity'],
                    );
                }
                if (!empty($orderCouponDetail)) {
                    $cart_products = array_merge($cart_products, $orderCouponDetail);
                }
                unset($params['isDownPaymentEnable']);


                if ($grand_total != ($tax + $products_sub_total + $shipping_price))
                    $products_sub_total = $grand_total - ($tax + $shipping_price);
            }
            $item_count = 0;
            foreach ($cart_products as $value) {
                $item_count+=$value['QTY'];
            }
            $otherParams = array(
                'AMT' => @round($grand_total, 2),
                'ITEMAMT' => @round($grand_total, 2),
            );
            $session = new Zend_Session_Namespace('Sitestoreproduct_order_stripe_payment_begin');
            $session->grand_total = @round($grand_total, 2);
            $session->item_count = $item_count;

            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            if ($coreSettings->getSetting('sitegateway.stripeconnect', 0)) {
                unset($_SESSION['Sitestoreproduct_order_stripe_payment_begin']['item_count']);
                unset($_SESSION['Sitestoreproduct_order_stripe_payment_begin']['grand_total']);
            }
        } elseif ($params['source_type'] == 'sitecrowdfunding_backer') {
            $otherParams = array(
                'AMT' => $order->amount,
                'ITEMAMT' => $order->amount,
            );
        } elseif ($params['source_type'] == 'sitecredit_order') {

            $otherParams = array(
                'AMT' => $order->grand_total,
                'ITEMAMT' => $order->grand_total,
            );
        }
        if (!isset($otherParams['AMT'])) {
            $otherParams['AMT'] = $order->grand_total;
        }
        if (!isset($otherParams['ITEMAMT'])) {
            $otherParams['ITEMAMT'] = $order->sub_total;
        }

        $params = array_merge($params, $otherParams);

        //Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Create a transaction for user payment request
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
     * Process an IPN
     *
     * @param Engine_Payment_Ipn $ipn
     * @return Engine_Payment_Plugin_Abstract
     */
    public function onIpn(Engine_Payment_Ipn $ipn) {

        $rawData = $ipn->getData(); //$rawData = $ipn->getRawData();

        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');

        $order = null;

        // Transaction IPN - get order by subscription_id
        if (!$order && !empty($rawData['data']->object->id)) {
            $gateway_order_id = $rawData['data']->object->id;

            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_order_id = ?' => $gateway_order_id,
            ));
        }

        if ($order) {
            return $this->onResourceIpn($order);
        } elseif ($rawData['type'] == "customer.subscription.deleted") {
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            $select = $subscriptionsTable->select()->where('gateway_profile_id = ?', $rawData['data']->object->customer)->order("subscription_id DESC");
            $currentSubscription = $subscriptionsTable->fetchRow($select);
            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_order_id = ?' => $currentSubscription->getIdentity(),
            ));
            if($order) {
                return $this->onResourceIpn($order, $ipn);
            }

        } else {
            $error_msg17 = Zend_Registry::get('Zend_Translate')->_('Unknown or unsupported IPN type, or missing transaction or order ID');
            throw new Engine_Payment_Plugin_Exception($error_msg17);
        }
    }

    /**
     * Generate href to a page detailing the order
     *
     * @param string $orderId
     * @return string
     */
    public function getOrderDetailLink($orderId) {

        return $this->getGatewayPaymentDetailLink($orderId);
    }

    /**
     * Generate href to a page detailing the transaction
     *
     * @param string $transactionId
     * @return string
     */
    public function getTransactionDetailLink($transactionId) {

        return $this->getGatewayPaymentDetailLink($transactionId);
    }

    /**
     * Generate href to a page detailing the transaction
     *
     * @param string $id
     * @return string
     */
    public function getGatewayPaymentDetailLink($id) {

        $substring = substr($id, 0, 2);

        if ($substring == 'ch') {
            $urlLink = 'payments/';
        } elseif ($substring == 'cus') {
            $urlLink = 'customers/';
        } else {
            $urlLink = '';
            $id = '';
        }

        if ($this->getGateway()->getTestMode()) {
            return 'https://dashboard.stripe.com/test/' . $urlLink . $id;
        } else {
            return 'https://dashboard.stripe.com/' . $urlLink . $id;
        }
    }

    /**
     * Get raw data about an order or recurring payment profile
     *
     * @param string $orderId
     * @return array
     */
    public function getOrderDetails($orderId) {
        // We don't know if this is a recurring payment profile or a transaction id,
        // so try both
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
     * Get raw data about a transaction
     *
     * @param $transactionId
     * @return array
     */
    public function getTransactionDetails($transactionId) {
        return $this->getService()->detailTransaction($transactionId);
    }

    // Forms

    /**
     * Get the form for editing the gateway info
     *
     * @return Engine_Form
     */
    public function getAdminGatewayForm() {
        return new Sitegateway_Form_Admin_Gateway_Stripe();
    }

    /**
     * Get the admin form for editing the gateway info
     *
     * @return Engine_Form
     */
    public function processAdminGatewayForm(array $values) {
        return $values;
    }

}
