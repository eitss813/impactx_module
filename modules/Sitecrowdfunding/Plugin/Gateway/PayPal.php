<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PayPal.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Plugin_Gateway_PayPal extends Engine_Payment_Plugin_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
    }

    /**
     * Get the service API
     *
     * @return Engine_Service_PayPal
     */
    public function getService() {
        return $this->getGateway()->getService();
    }

    /**
     * Get the gateway object
     *
     * @return Engine_Payment_Gateway
     */
    public function getGateway() {
        if (null == $this->_gateway) {
            $class = 'Engine_Payment_Gateway_PayPal';
            Engine_Loader::loadClass($class);
            $gateway = new $class(array(
                'config' => (array) $this->_gatewayInfo->config,
                'testMode' => $this->_gatewayInfo->test_mode,
                'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
            ));
            if (!($gateway instanceof Engine_Payment_Gateway)) {
                $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Plugin class not instance of Engine_Payment_Gateway');
                throw new Engine_Exception($error_msg1);
            }
            $this->_gateway = $gateway;
        }

        return $this->_gateway;
    }

    // Actions

    /**
     * Create a transaction object from specified parameters
     *
     * @return Engine_Payment_Transaction
     */
    public function createTransaction(array $params) {
        $transaction = new Engine_Payment_Transaction($params);
        $transaction->process($this->getGateway());
        return $transaction;
    }

    /**
     * Create an ipn object from specified parameters
     *
     * @return Engine_Payment_Ipn
     */
    public function createIpn(array $params) {
        $ipn = new Engine_Payment_Ipn($params);
        $ipn->process($this->getGateway());
        return $ipn;
    }

    // SEv4 Specific

    /**
     * Create a transaction for a subscription
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $subscription
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $subscription, Payment_Model_Package $package, array $params = array()) {
        
    }

    /**
     * Process return of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onSubscriptionTransactionReturn(
    Payment_Model_Order $order, array $params = array()) {
        
    }

    /**
     * Create a transaction for order
     *
     * @param User_Model_User $user
     * @param $order_id
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserOrderTransaction($parent_order_id, array $params = array(), $user = NULL) {
        // This is a one-time fee
        // set parameters
        // create an object for view
        $order = Engine_Api::_()->getItem($params['source_type'], $parent_order_id);

        $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $order->project_id);
        $params['amount'] = $amount = $order->amount;
        $backers[] = array(
            'NAME' => $item->getTitle(),
            'AMT' => @round($amount, 2),
            'QTY' => 1,
        );
        $params['driverSpecificParams']['PayPal'] = array(
            'AMT' => @round($amount, 2),
//        'CUSTOM' => '5',
//        'INVNUM' => '1',
            'ITEMAMT' => @round($amount, 2),
            'SELLERID' => '1',
            'ITEMS' => $backers,
            'SOLUTIONTYPE' => 'sole',
        );

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

        $user_backer = $order->getSource();
        $user = $order->getUser();

        if ($user_backer->payment_status == 'pending') {
            return 'pending';
        }

        // Check for cancel state - the user cancelled the transaction
        if ($params['state'] == 'cancel') {
            // Cancel order
            $order->onCancel();
            $user_backer->onPaymentFailure();

            // Error
            throw new Payment_Model_Exception('Your payment has been cancelled and not been charged. If this is not correct, please try again later.');
        }

        // Check params
        if (empty($params['token'])) {

            // Cancel order
            $order->onFailure();
            $user_backer->onPaymentFailure();

            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }

        // Get details
        try {
            $data = $this->getService()->detailExpressCheckout($params['token']);
        } catch (Exception $e) {
            // Cancel order
            $order->onFailure();
            $user_backer->onPaymentFailure();

            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
                . print_r($data, true), Zend_Log::INFO);

        // Do payment
        try {
            $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], array(
                'PAYMENTACTION' => 'Sale',
                'AMT' => $data['AMT'],
                'CURRENCYCODE' => $this->getGateway()->getCurrency(),
            ));
        } catch (Exception $e) {
            // Cancel order
            $order->onFailure();
            $user_backer->onPaymentFailure();

            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.

            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
                . print_r($rdata, true), Zend_Log::INFO);

        // Get payment state
        $paymentStatus = null;
        $orderStatus = null;
        switch (strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS'])) {
            case 'created':
            case 'pending':
                $paymentStatus = 'pending';
                $orderStatus = 'complete';
                break;

            case 'completed':
            case 'processed':
            case 'canceled_reversal': // Probably doesn't apply
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                break;

            case 'denied':
            case 'failed':
            case 'voided': // Probably doesn't apply
            case 'reversed': // Probably doesn't apply
            case 'refunded': // Probably doesn't apply
            case 'expired':  // Probably doesn't apply
            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                break;
        }

        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
        $order->save();

        // Insert transaction

        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => $user_backer->gateway_id,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            'source_type' => $order->source_type,
            'source_id' => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
            'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
            'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE'],
        );
        $transactionsTable->insert($transactionParams);

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
            Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
        }

        // Get benefit setting
        $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')
                ->getBenefitStatus($user);

        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        $user_backer->gateway_type = $paymentMethod;
        $user_backer->save();
        //SAVE THE REWARD ID IF THE PROJECT IS BACKED WITH REWARD
        if ($paymentStatus == 'okay' && isset($params['reward_id']) && !empty($params['reward_id'])) {
            $user_backer->reward_id = $params['reward_id'];
            $user_backer->save();
        } 
        // Check payment status
        if ($paymentStatus == 'okay' ||
                ($paymentStatus == 'pending' && $giveBenefit)) {

            // Update order info

            Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->update(
                    array('gateway_profile_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID']), array('backer_id =?' => $user_backer->backer_id)
            );

            // Payment success
            $user_backer->onPaymentSuccess();

            return 'active';
        } else if ($paymentStatus == 'pending') {

            // Update order info
            $user_backer->gateway_id = $this->_gatewayInfo->gateway_id;
            $user_backer->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

            // Payment pending
            $user_backer->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order
            $order->onFailure();
            $user_backer->onPaymentFailure();
            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed. Please ensure there are sufficient available funds in your account.');
        } else {
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }
    }

    public function createProjectTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $project, Sitecrowdfunding_Model_Package $package, array $params = array()) {
        // Process description
        $desc = $package->getPackageDescription();
        if (strlen($desc) > 127) {
            $desc = substr($desc, 0, 124) . '...';
        } else if (!$desc || strlen($desc) <= 0) {
            $desc = 'N/A';
        }
        if (function_exists('iconv') && strlen($desc) != iconv_strlen($desc)) {
            // PayPal requires that DESC be single-byte characters
            $desc = @iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
        }
        $params['vendor_order_id'] = 'P' . $params['vendor_order_id'];
        // This is a one-time fee
        if ($package->isOneTime()) {
            $params['driverSpecificParams']['PayPal'] = array(
                'AMT' => $package->price,
                'DESC' => $desc,
                'CUSTOM' => $project->project_id,
                'INVNUM' => $params['vendor_order_id'],
                'ITEMAMT' => $package->price,
                'ITEMS' => array(
                    array(
                        'NAME' => $project->getTitle() . ': Project',
                        'AMT' => $package->price,
                        'NUMBER' => $project->project_id,
                    ),
                ),
                'SOLUTIONTYPE' => 'sole'
                    //'BILLINGTYPE' => 'RecurringPayments',
                    //'BILLINGAGREEMENTDESCRIPTION' => $package->getPackageDescription(),
            );
            // Should fix some issues with GiroPay
            if (!empty($params['return_url'])) {
                $params['driverSpecificParams']['PayPal']['GIROPAYSUCCESSURL'] = $params['return_url']
                        . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
                $params['driverSpecificParams']['PayPal']['BANKTXNPENDINGURL'] = $params['return_url']
                        . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
            }
            if (!empty($params['cancel_url'])) {
                $params['driverSpecificParams']['PayPal']['GIROPAYCANCELURL'] = $params['cancel_url']
                        . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
            }
        }
        // This is a recurring subscription
        else {
            $params['driverSpecificParams']['PayPal'] = array(
                'AMT' => $package->price,
                'DESC' => $desc,
                'CUSTOM' => $project->project_id,
                'INVNUM' => $params['vendor_order_id'],
                'ITEMAMT' => $package->price,
                'ITEMS' => array(
                    array(
                        'NAME' => $project->getTitle() . ': Project',
                        'AMT' => $package->price,
                        'NUMBER' => $project->project_id,
                    ),
                ),
                'BILLINGTYPE' => 'RecurringPayments',
                'BILLINGAGREEMENTDESCRIPTION' => $desc,
            );
        }
        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Process return of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onPageTransactionReturn(
    Payment_Model_Order $order, array $params = array()) {
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg2);
        }

        // Get related info
        $user = $order->getUser();
        $projectOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->getOtherinfo($order->source_id);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $order->source_id);
        $package = $project->getPackage();

        // Check subscription state
        if (/* $project->status == 'active' || */
                $project->status == 'trial') {
            return 'active';
        } else if ($project->status == 'pending') {
            return Zend_Registry::get('Zend_Translate')->_('pending');
        }

        // Check for cancel state - the user cancelled the transaction
        if ($params['state'] == 'cancel') {
            // Cancel order and subscription?
            $order->onCancel();
            $project->onPaymentFailure();
            // Error
            $error_msg3 = Zend_Registry::get('Zend_Translate')->_('Your payment has been cancelled and not been charged. If this is not correct, please try again later.');
            throw new Payment_Model_Exception($error_msg3);
        }

        // Check params
        if (empty($params['token'])) {
            // Cancel order and subscription?
            $order->onFailure();
            $project->onPaymentFailure();
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            $error_msg4 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg4);
        }

        // Get details
        try {
            $data = $this->getService()->detailExpressCheckout($params['token']);
        } catch (Exception $e) {
            // Cancel order and subscription?
            $order->onFailure();
            $project->onPaymentFailure();
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            $error_msg5 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg5);
        }

        // Let's log it
        $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
                . print_r($data, true), Zend_Log::INFO);


        // One-time
        if ($package->isOneTime()) {

            // Do payment
            try {
                $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], array(
                    'PAYMENTACTION' => 'Sale',
                    'AMT' => $data['AMT'],
                    'CURRENCYCODE' => $this->getGateway()->getCurrency(),
                ));
            } catch (Exception $e) {
                // Cancel order and subscription?
                $order->onFailure();
                $project->onPaymentFailure();
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg6 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg6);
            }

            // Let's log it
            $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
                    . print_r($rdata, true), Zend_Log::INFO);

            // Get payment state
            $paymentStatus = null;
            $orderStatus = null;
            switch (strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS'])) {
                case 'created':
                case 'pending':
                    $paymentStatus = 'pending';
                    $orderStatus = 'complete';
                    break;

                case 'completed':
                case 'processed':
                case 'canceled_reversal': // Probably doesn't apply
                    $paymentStatus = 'okay';
                    $orderStatus = 'complete';
                    break;

                case 'denied':
                case 'failed':
                case 'voided': // Probably doesn't apply
                case 'reversed': // Probably doesn't apply
                case 'refunded': // Probably doesn't apply
                case 'expired':  // Probably doesn't apply
                default: // No idea what's going on here
                    $paymentStatus = 'failed';
                    $orderStatus = 'failed'; // This should probably be 'failed'
                    break;
            }

            // Update order with profile info and complete status?
            $order->state = $orderStatus;
            $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
            $order->save();

            // Insert transaction
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
            $transactionsTable->insert(array(
                'user_id' => $order->user_id,
                'gateway_id' => $this->_gatewayInfo->gateway_id,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'payment_order_id' => $order->order_id,
                'type' => 'payment',
                'source_type' => $project->getType(),
                'source_id' => $project->getIdentity(),
                'state' => $paymentStatus,
                'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
                'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
                'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE'],
            ));

            // Check payment status
            if ($paymentStatus == 'okay' ||
                    ($paymentStatus == 'pending')) {

                // Update subscription info
                $projectOtherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
                $projectOtherinfo->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
                $projectOtherinfo->save();

                // Payment success
                $project->onPaymentSuccess();

                // send notification
                if ($project->didStatusChange()) {
                    Engine_Api::_()->sitecrowdfunding()->sendMail("ACTIVE", $project->project_id);
                }

                return 'active';
            } else if ($paymentStatus == 'pending') {

                // Update subscription info
                $projectOtherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
                $projectOtherinfo->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
                $projectOtherinfo->save();

                // Payment pending
                $project->onPaymentPending();

                // send notification
                if ($project->didStatusChange()) {
                    Engine_Api::_()->sitecrowdfunding()->sendMail("PENDING", $project->project_id);
                }

                return 'pending';
            } else if ($paymentStatus == 'failed') {
                // Cancel order and subscription?
                $order->onFailure();
                $project->onPaymentFailure();
                // Payment failed
                $error_msg7 = Zend_Registry::get('Zend_Translate')->_('Your payment could not be completed. Please ensure there are sufficient available funds in your account.');
                throw new Payment_Model_Exception($error_msg7);
            } else {
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg8 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg8);
            }
        }

        // Recurring
        else {
            // Check for errors
            if (empty($data)) {
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg9 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg9);
            } else if (empty($data['BILLINGAGREEMENTACCEPTEDSTATUS']) ||
                    '0' == $data['BILLINGAGREEMENTACCEPTEDSTATUS']) {
                // Cancel order and subscription?
                $order->onCancel();
                $project->onPaymentFailure();
                // Error
                $error_msg10 = Zend_Registry::get('Zend_Translate')->_('Your payment has been cancelled and not been charged. If this in not correct, please try again later.');
                throw new Payment_Model_Exception($error_msg10);
            } else if (!isset($data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) ||
                    '0' != $data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) {
                // Cancel order and subscription?
                $order->onFailure();
                $project->onPaymentFailure();
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg11 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg11);
            }

            // Create recurring payments profile
            $desc = $package->getPackageDescription();
            if (strlen($desc) > 127) {
                $desc = substr($desc, 0, 124) . '...';
            } else if (!$desc || strlen($desc) <= 0) {
                $desc = 'N/A';
            }
            if (function_exists('iconv') && strlen($desc) != iconv_strlen($desc)) {
                // PayPal requires that DESC be single-byte characters
                $desc = @iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
            }
            $rpData = array(
                'TOKEN' => $params['token'],
                'PROFILEREFERENCE' => $order->order_id,
                'PROFILESTARTDATE' => $data['TIMESTAMP'],
                'DESC' => $desc,
                'BILLINGPERIOD' => ucfirst($package->recurrence_type),
                'BILLINGFREQUENCY' => $package->recurrence,
                'INITAMT' => 0,
                'AMT' => $package->price,
                'CURRENCYCODE' => $this->getGateway()->getCurrency(),
            );

            $count = $package->getTotalBillingCycleCount();
            if ($count) {
                $rpData['TOTALBILLINGCYCLES'] = $count;
            }

            // Create recurring payment profile
            try {
                $rdata = $this->getService()->createRecurringPaymentsProfile($rpData);
            } catch (Exception $e) {
                // Cancel order and subscription?
                $order->onFailure();
                $project->onPaymentFailure();
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg12 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg12);
            }

            // Let's log it
            $this->getGateway()->getLog()->log('CreateRecurringPaymentsProfile: '
                    . print_r($rdata, true), Zend_Log::INFO);

            // Check returned profile id
            if (empty($rdata['PROFILEID'])) {
                // Cancel order and subscription?
                $order->onFailure();
                $project->onPaymentFailure();
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg13 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg13);
            }
            $profileId = $rdata['PROFILEID'];

            // Update order with profile info and complete status?
            $order->state = 'complete';
            $order->gateway_order_id = $profileId;
            $order->save();
            // Check profile status
            if ($rdata['PROFILESTATUS'] == 'ActiveProfile' ||
                    ($rdata['PROFILESTATUS'] == 'PendingProfile' )) {
                // Enable now
                $projectOtherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
                $projectOtherinfo->gateway_profile_id = $rdata['PROFILEID'];
                $projectOtherinfo->save();
                $project->onPaymentSuccess();

                // send notification
                if ($project->didStatusChange()) {
                    Engine_Api::_()->sitecrowdfunding()->sendMail("ACTIVE", $project->project_id);
                }

                return 'active';
            } else if ($rdata['PROFILESTATUS'] == 'PendingProfile') {
                // Enable later
                $projectOtherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
                $projectOtherinfo->gateway_profile_id = $rdata['PROFILEID'];
                $projectOtherinfo->save();
                $project->onPaymentPending();

                // send notification
                if ($project->didStatusChange()) {
                    Engine_Api::_()->sitecrowdfunding()->sendMail("PENDING", $project->project_id);
                }

                return 'pending';
            } else {
                // Cancel order and subscription?
                $order->onFailure();
                $project->onPaymentFailure();
                // This is a sanity error and cannot produce information a user could use
                // to correct the problem.
                $error_msg14 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg14);
            }
        }
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
        // This is a one-time fee
        //FETCH RESPONSE DETAIL
        $response = Engine_Api::_()->getDbtable('paymentrequests', 'sitecrowdfunding')->getResponseDetail($request_id);
        $project_title = Engine_Api::_()->getItem('sitecrowdfunding_project', $response[0]['project_id'])->getTitle();

        // create an object for view
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $params['driverSpecificParams']['PayPal'] = array(
            'AMT' => @round($response[0]['response_amount'], 2),
            'ITEMAMT' => @round($response[0]['response_amount'], 2),
            'ITEMS' => array(
                array(
                    'NAME' => $project_title,
                    'DESC' => $response[0]['response_message'],
                    'AMT' => @round($response[0]['response_amount'], 2),
                ),
            ),
            'SOLUTIONTYPE' => 'sole',
        );

        // Create transaction
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
    public function createProjectBillTransaction($project_id, $bill_id, $params = array()) {
        //FETCH RESPONSE DETAIL
        $projectBillDetail = Engine_Api::_()->getDbtable('projectbills', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'projectbill_id = ?' => $bill_id));
        $project_title = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)->getTitle();

        $params['driverSpecificParams']['PayPal'] = array(
            'AMT' => round($projectBillDetail->amount, 2),
            'ITEMAMT' => round($projectBillDetail->amount, 2),
            'ITEMS' => array(
                array(
                    'NAME' => $project_title,
                    'DESC' => $projectBillDetail->message,
                    'AMT' => round($projectBillDetail->amount, 2),
                ),
            ),
            'SOLUTIONTYPE' => 'sole',
        );

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
    public function onProjectBillTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $projectBill = $order->getSource();
        $user = $order->getUser();
        if ($projectBill->status == 'pending') {
            return 'pending';
        }

        // Check for cancel state - the user cancelled the transaction
        if ($params['state'] == 'cancel') {
            // Cancel order and advertiesment?
            $order->onCancel();
            $projectBill->onPaymentFailure();

            // Error
            throw new Payment_Model_Exception('Your payment has been cancelled and ' .
            'not been charged. If this is not correct, please try again later.');
        }

        // Check params
        if (empty($params['token'])) {

            // Cancel order and advertiesment?
            $order->onFailure();
            $projectBill->onPaymentFailure();

            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }

        // Get details
        try {
            $data = $this->getService()->detailExpressCheckout($params['token']);
        } catch (Exception $e) {
            // Cancel order and advertiesment?
            $order->onFailure();
            $projectBill->onPaymentFailure();

            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
                . print_r($data, true), Zend_Log::INFO);

        // Do payment
        try {
            $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], array(
                'PAYMENTACTION' => 'Sale',
                'AMT' => $data['AMT'],
                'CURRENCYCODE' => $this->getGateway()->getCurrency(),
            ));
        } catch (Exception $e) {
            // Cancel order and advertiesment?
            $order->onFailure();
            $projectBill->onPaymentFailure();

            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
                . print_r($rdata, true), Zend_Log::INFO);

        // Get payment state
        $paymentStatus = null;
        $orderStatus = null;
        switch (strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS'])) {
            case 'created':
            case 'pending':
                $paymentStatus = 'pending';
                $orderStatus = 'complete';
                break;

            case 'completed':
            case 'processed':
            case 'canceled_reversal': // Probably doesn't apply
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                break;

            case 'denied':
            case 'failed':
            case 'voided': // Probably doesn't apply
            case 'reversed': // Probably doesn't apply
            case 'refunded': // Probably doesn't apply
            case 'expired':  // Probably doesn't apply
            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                break;
        }

        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
        $order->save();
        // Insert transaction
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
        $transactionParams = array(
            'user_id' => $order->user_id,
            'sender_type' => 2,
            'gateway_id' => $projectBill->gateway_id,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            'source_type' => $order->source_type,
            'source_id' => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
            'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
            'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE']
        );
        $transactionsTable->insert($transactionParams);

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
            Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
        }

        // Get benefit setting
        $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')
                ->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' ||
                ($paymentStatus == 'pending' && $giveBenefit)) {

            $projectBill->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

            // Payment success
            $projectBill->onPaymentSuccess();

            return 'active';
        } else if ($paymentStatus == 'pending') {

            // Update advertiesment info
            $projectBill->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

            // Payment pending
            $projectBill->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order and advertiesment?
            $order->onFailure();
            $projectBill->onPaymentFailure();
            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be ' .
            'completed. Please ensure there are sufficient available funds ' .
            'in your account.');
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

        // Check for cancel state - the user cancelled the transaction
        if ($params['state'] == 'cancel') {
            // Cancel order and advertiesment?
            $order->onCancel();
            $user_request->onPaymentFailure();
            // send notification
            if ($user_request->didStatusChange()) {
                // SEND OVERDUE MAIL HERE
                //Engine_Api::_()->communityad()->sendMail("OVERDUE", $user_request->userad_id);
            }
            // Error
            throw new Payment_Model_Exception('Your payment has been cancelled and ' .
            'not been charged. If this is not correct, please try again later.');
        }

        // Check params
        if (empty($params['token'])) {

            // Cancel order and advertiesment?
            $order->onFailure();
            $user_request->onPaymentFailure();

            // send notification
//      if ($user_request->didStatusChange()) {
//        // SEND OVERDUE MAIL HERE
//        Engine_Api::_()->communityad()->sendMail("OVERDUE", $user_request->userad_id);
//      }
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }

        // Get details
        try {
            $data = $this->getService()->detailExpressCheckout($params['token']);
        } catch (Exception $e) {
            // Cancel order and advertiesment?
            $order->onFailure();
            $user_request->onPaymentFailure();

            // send notification
//      if ($user_request->didStatusChange()) {
//        // SEND OVERDUE MAIL HERE
//        Engine_Api::_()->communityad()->sendMail("OVERDUE", $user_request->userad_id);
//      }
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
                . print_r($data, true), Zend_Log::INFO);

        // Do payment
        try {
            $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], array(
                'PAYMENTACTION' => 'Sale',
                'AMT' => $data['AMT'],
                'CURRENCYCODE' => $this->getGateway()->getCurrency(),
            ));
        } catch (Exception $e) {
            // Cancel order and advertiesment?
            $order->onFailure();
            $user_request->onPaymentFailure();
            // send notification
//      if ($user_request->didStatusChange()) {
//        // SEND OVERDUE MAIL HERE
//        Engine_Api::_()->communityad()->sendMail("OVERDUE", $user_request->userad_id);
//      }
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.

            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
                . print_r($rdata, true), Zend_Log::INFO);

        // Get payment state
        $paymentStatus = null;
        $orderStatus = null;
        switch (strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS'])) {
            case 'created':
            case 'pending':
                $paymentStatus = 'pending';
                $orderStatus = 'complete';
                break;

            case 'completed':
            case 'processed':
            case 'canceled_reversal': // Probably doesn't apply
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                break;

            case 'denied':
            case 'failed':
            case 'voided': // Probably doesn't apply
            case 'reversed': // Probably doesn't apply
            case 'refunded': // Probably doesn't apply
            case 'expired':  // Probably doesn't apply
            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                break;
        }

        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
        $order->save();

        // Insert transaction
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
        $transactionParams = array(
            'user_id' => $order->user_id,
            'sender_type' => 1,
            'gateway_id' => $user_request->gateway_id, // 2 FOR PAYPAL
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            'source_type' => $order->source_type,
            'source_id' => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
            'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
            'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE']
        );

        $transactionsTable->insert($transactionParams);
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id));
            Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
        }

        // Get benefit setting
        $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')
                ->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' ||
                ($paymentStatus == 'pending' && $giveBenefit)) {

//      // Update order info
//      $user_request->gateway_id = $this->_gatewayInfo->gateway_id;
            $user_request->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
//      $user_request->request_status = '1';
            // Payment success
            $user_request->onPaymentSuccess();

            // send notification
            if ($user_request->didStatusChange()) {

                // SEND ACTIVE MAIL HERE
                // Engine_Api::_()->communityad()->sendMail("ACTIVE", $user_request->userad_id);
            }

            return 'active';
        } else if ($paymentStatus == 'pending') {

            // Update advertiesment info
//      $user_request->gateway_id = $this->_gatewayInfo->gateway_id;
            $user_request->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

            // Payment pending
            $user_request->onPaymentPending();

            // send notification
            if ($user_request->didStatusChange()) {
                // SEND PENDING MAIL HERE
                // Engine_Api::_()->communityad()->sendMail("PENDING", $user_request->userad_id);
            }

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order and advertiesment?
            $order->onFailure();
            $user_request->onPaymentFailure();
            // send notification
            if ($user_request->didStatusChange()) {
                // SEND OVERDUE MAIL HERE
                //Engine_Api::_()->communityad()->sendMail("OVERDUE", $user_request->userad_id);
            }
            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be ' .
            'completed. Please ensure there are sufficient available funds ' .
            'in your account.');
        } else {
            // This is a sanity error and cannot produce information a user could use
            // to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * Process ipn of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onSubscriptionTransactionIpn(
    Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        
    }

    /**
     * Process ipn of page transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onPageTransactionIpn(
    Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg15 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg15);
        }

        // Get related info
        $user = $order->getUser();
        $project = $order->getSource();
        $package = $project->getPackage();

        // Get IPN data
        $rawData = $ipn->getRawData();

        // Get tx table
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');


        // Chargeback --------------------------------------------------------------
        if (!empty($rawData['case_type']) && $rawData['case_type'] == 'chargeback') {
            $project->onPaymentFailure(); // or should we use pending?
        }

        // Transaction Type --------------------------------------------------------
        else if (!empty($rawData['txn_type'])) {
            switch ($rawData['txn_type']) {

                // @todo see if the following types need to be processed:
                // — adjustment express_checkout new_case

                case 'express_checkout':
                    // Only allowed for one-time
                    if ($package->isOneTime()) {
                        switch ($rawData['payment_status']) {

                            case 'Created': // Not sure about this one
                            case 'Pending':
                                $project->onPaymentPending();
                                break;

                            case 'Completed':
                            case 'Processed':
                            case 'Canceled_Reversal': // Not sure about this one
                                $project->onPaymentSuccess();
                                // send notification
                                if ($project->didStatusChange()) {
                                    Engine_Api::_()->sitecrowdfunding()->sendMail("ACTIVE", $project->project_id);
                                }
                                break;

                            case 'Denied':
                            case 'Failed':
                            case 'Voided':
                            case 'Reversed':
                                $project->onPaymentFailure();
                                // send notification
                                if ($project->didStatusChange()) {
                                    Engine_Api::_()->sitecrowdfunding()->sendMail("OVERDUE", $project->project_id);
                                }
                                break;

                            case 'Refunded':
                                $project->onRefund();
                                // send notification
                                if ($project->didStatusChange()) {
                                    Engine_Api::_()->sitecrowdfunding()->sendMail("REFUNDED", $project->project_id);
                                }
                                break;

                            case 'Expired': // Not sure about this one
                                $project->onExpiration();
                                // send notification
                                if ($project->didStatusChange()) {
                                    Engine_Api::_()->sitecrowdfunding()->sendMail("EXPIRED", $project->project_id);
                                }
                                break;

                            default:
                                throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                                        'payment status %1$s', $rawData['payment_status']));
                                break;
                        }
                    }
                    break;

                // Recurring payment was received
                case 'recurring_payment':
                    if (!$package->isOneTime()) {
                        $project->onPaymentSuccess();
                        // send notification
                        if ($project->didStatusChange()) {
                            //  @todo sitecrowdfunding_project_recurrence
                            Engine_Api::_()->sitecrowdfunding()->sendMail("RECURRENCE", $project->project_id);
                        }
                    }
                    break;

                // Profile was created
                case 'recurring_payment_profile_created':
                    if ($rawData['initial_payment_status'] == 'Completed') {
                        //$subscription->active = true;
                        $project->onPaymentSuccess();
                        // @todo add transaction row for the initial amount?
                        // send notification
                        if ($project->didStatusChange()) {
                            Engine_Api::_()->sitecrowdfunding()->sendMail("ACTIVE", $project->project_id);
                        }
                    } else {
                        throw new Engine_Payment_Plugin_Exception(sprintf('Unknown ' .
                                'initial_payment_status %1$s', $rawData['initial_payment_status']));
                    }
                    break;

                // Profile was cancelled
                case 'recurring_payment_profile_cancel':
                    $project->onCancel();
                    // send notification
                    if ($project->didStatusChange()) {
                        Engine_Api::_()->sitecrowdfunding()->sendMail("CANCELLED", $project->project_id);
                    }
                    break;

                // Recurring payment expired
                case 'recurring_payment_expired':
                    if (!$package->isOneTime()) {
                        $project->onExpiration();
                        // send notification
                        if ($project->didStatusChange()) {
                            Engine_Api::_()->sitecrowdfunding()->sendMail("EXPIRED", $project->project_id);
                        }
                    }
                    break;

                // Recurring payment failed
                case 'recurring_payment_skipped':
                case 'recurring_payment_suspended_due_to_max_failed_payment':
                case 'recurring_payment_outstanding_payment_failed':
                case 'recurring_payment_outstanding_payment':
                    $project->onPaymentFailure();
                    // send notification
                    if ($project->didStatusChange()) {
                        Engine_Api::_()->sitecrowdfunding()->sendMail("OVERDUE", $project->project_id);
                    }
                    break;

                // What is this?
                default:
                    throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                            'type %1$s', $rawData['txn_type']));
                    break;
            }
        }

        // Payment Status ----------------------------------------------------------
        else if (!empty($rawData['payment_status'])) {
            switch ($rawData['payment_status']) {

                case 'Created': // Not sure about this one
                case 'Pending':
                    $project->onPaymentPending();
                    break;

                case 'Completed':
                case 'Processed':
                case 'Canceled_Reversal': // Not sure about this one
                    $project->onPaymentSuccess();
                    // send notification
                    if ($project->didStatusChange()) {
                        Engine_Api::_()->sitecrowdfunding()->sendMail("ACTIVE", $project->project_id);
                    }
                    break;

                case 'Denied':
                case 'Failed':
                case 'Voided':
                case 'Reversed':
                    $project->onPaymentFailure();
                    // send notification
                    if ($project->didStatusChange()) {
                        Engine_Api::_()->sitecrowdfunding()->sendMail("OVERDUE", $project->project_id);
                    }
                    break;

                case 'Refunded':
                    $project->onRefund();
                    // send notification
                    if ($project->didStatusChange()) {
                        Engine_Api::_()->sitecrowdfunding()->sendMail("REFUNDED", $project->project_id);
                    }
                    break;

                case 'Expired': // Not sure about this one
                    $project->onExpiration();
                    // send notification
                    if ($project->didStatusChange()) {
                        Engine_Api::_()->sitecrowdfunding()->sendMail("EXPIRED", $project->project_id);
                    }
                    break;

                default:
                    throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                            'payment status %1$s', $rawData['payment_status']));
                    break;
            }
        }

        // Unknown -----------------------------------------------------------------
        else {
            throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                    'data structure'));
        }

        return $this;
    }

    /**
     * Cancel a subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelSubscription($transactionId, $note = null) {
        
    }

    public function cancelProject($transactionId, $note = null) {
        $profileId = null;

        if ($transactionId instanceof Sitecrowdfunding_Model_Project) {
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
            $r = $this->getService()->cancelRecurringPaymentsProfile($profileId, $note);
        } catch (Exception $e) {
            // throw?
        }

        return $this;
    }

    /**
     * Generate href to a project detailing the order
     *
     * @param string $transactionId
     * @return string
     */
    public function getOrderDetailLink($orderId) {
        // @todo make sure this is correct
        // I don't think this works
        if ($this->getGateway()->getTestMode()) {
            // Note: it doesn't work in test mode
            return 'https://www.sandbox.paypal.com/vst/?id=' . $orderId;
        } else {
            return 'https://www.paypal.com/vst/?id=' . $orderId;
        }
    }

    /**
     * Generate href to a page detailing the transaction
     *
     * @param string $transactionId
     * @return string
     */
    public function getTransactionDetailLink($transactionId) {
        // @todo make sure this is correct
        if ($this->getGateway()->getTestMode()) {
            // Note: it doesn't work in test mode
            return 'https://www.sandbox.paypal.com/vst/?id=' . $transactionId;
        } else {
            return 'https://www.paypal.com/vst/?id=' . $transactionId;
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

    // IPN

    /**
     * Process an IPN
     *
     * @param Engine_Payment_Ipn $ipn
     * @return Engine_Payment_Plugin_Abstract
     */
    public function onIpn(Engine_Payment_Ipn $ipn) {
        $rawData = $ipn->getRawData();

        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');


        // Find transactions -------------------------------------------------------
        $transactionId = null;
        $parentTransactionId = null;
        $transaction = null;
        $parentTransaction = null;

        // Fetch by txn_id
        if (!empty($rawData['txn_id'])) {
            $transaction = $transactionsTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_transaction_id = ?' => $rawData['txn_id'],
            ));
            $parentTransaction = $transactionsTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_parent_transaction_id = ?' => $rawData['txn_id'],
            ));
        }
        // Fetch by txn_id
        if (!empty($rawData['txn_id'])) {
            $transaction = $transactionsTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_transaction_id = ?' => $rawData['txn_id'],
            ));
            $parentTransaction = $transactionsTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_parent_transaction_id = ?' => $rawData['txn_id'],
            ));
        }
        // Fetch by transaction->gateway_parent_transaction_id
        if ($transaction && !$parentTransaction &&
                !empty($transaction->gateway_parent_transaction_id)) {
            $parentTransaction = $transactionsTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_parent_transaction_id = ?' => $transaction->gateway_parent_transaction_id,
            ));
        }
        // Fetch by parentTransaction->gateway_transaction_id
        if ($parentTransaction && !$transaction &&
                !empty($parentTransaction->gateway_transaction_id)) {
            $transaction = $transactionsTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_parent_transaction_id = ?' => $parentTransaction->gateway_transaction_id,
            ));
        }
        // Get transaction id
        if ($transaction) {
            $transactionId = $transaction->gateway_transaction_id;
        } else if (!empty($rawData['txn_id'])) {
            $transactionId = $rawData['txn_id'];
        }
        // Get parent transaction id
        if ($parentTransaction) {
            $parentTransactionId = $parentTransaction->gateway_transaction_id;
        } else if ($transaction && !empty($transaction->gateway_parent_transaction_id)) {
            $parentTransactionId = $transaction->gateway_parent_transaction_id;
        } else if (!empty($rawData['parent_txn_id'])) {
            $parentTransactionId = $rawData['parent_txn_id'];
        }



        // Fetch order -------------------------------------------------------------
        $order = null;

        // Transaction IPN - get order by invoice
        if (!$order && !empty($rawData['invoice'])) {
            $order = $ordersTable->find($rawData['invoice'])->current();
        }

        // Subscription IPN - get order by recurring_payment_id
        if (!$order && !empty($rawData['recurring_payment_id'])) {
            // Get attached order
            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_order_id = ?' => $rawData['recurring_payment_id'],
            ));
        }

        // Subscription IPN - get order by rp_invoice_id
        //if( !$order && !empty($rawData['rp_invoice_id']) ) {
        //
    //}
        // Transaction IPN - get order by parent_txn_id
        if (!$order && $parentTransactionId) {
            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_transaction_id = ?' => $parentTransactionId,
            ));
        }

        // Transaction IPN - get order by txn_id
        if (!$order && $transactionId) {
            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_transaction_id = ?' => $transactionId,
            ));
        }

        // Transaction IPN - get order through transaction
        if (!$order && !empty($transaction->order_id)) {
            $order = $ordersTable->find($parentTransaction->order_id)->current();
        }

        // Transaction IPN - get order through parent transaction
        if (!$order && !empty($parentTransaction->order_id)) {
            $order = $ordersTable->find($parentTransaction->order_id)->current();
        }



        // Process generic IPN data ------------------------------------------------
        // Build transaction info
        if (!empty($rawData['txn_id'])) {
            $transactionData = array(
                'gateway_id' => $this->_gatewayInfo->gateway_id,
            );
            // Get timestamp
            if (!empty($rawData['payment_date'])) {
                $transactionData['timestamp'] = date('Y-m-d H:i:s', strtotime($rawData['payment_date']));
            } else {
                $transactionData['timestamp'] = new Zend_Db_Expr('NOW()');
            }
            // Get amount
            if (!empty($rawData['mc_gross'])) {
                $transactionData['amount'] = $rawData['mc_gross'];
            }
            // Get currency
            if (!empty($rawData['mc_currency'])) {
                $transactionData['currency'] = $rawData['mc_currency'];
            }
            // Get order/user
            if ($order) {
                $transactionData['user_id'] = $order->user_id;
                $transactionData['order_id'] = $order->order_id;
            }
            // Get transactions
            if ($transactionId) {
                $transactionData['gateway_transaction_id'] = $transactionId;
            }
            if ($parentTransactionId) {
                $transactionData['gateway_parent_transaction_id'] = $parentTransactionId;
            }
            // Get payment_status
            switch ($rawData['payment_status']) {
                case 'Canceled_Reversal': // @todo make sure this works

                case 'Completed':
                case 'Created':
                case 'Processed':
                    $transactionData['type'] = 'payment';
                    $transactionData['state'] = 'okay';
                    break;

                case 'Denied':
                case 'Expired':
                case 'Failed':
                case 'Voided':
                    $transactionData['type'] = 'payment';
                    $transactionData['state'] = 'failed';
                    break;

                case 'Pending':
                    $transactionData['type'] = 'payment';
                    $transactionData['state'] = 'pending';
                    break;

                case 'Refunded':
                    $transactionData['type'] = 'refund';
                    $transactionData['state'] = 'refunded';
                    break;
                case 'Reversed':
                    $transactionData['type'] = 'reversal';
                    $transactionData['state'] = 'reversed';
                    break;

                default:
                    $transactionData = 'unknown';
                    break;
            }

            // Insert new transaction
            if (!$transaction) {
                $transactionsTable->insert($transactionData);
            }
            // Update transaction
            else {
                unset($transactionData['timestamp']);
                $transaction->setFromArray($transactionData);
                $transaction->save();
            }

            // Update parent transaction on refund?
            if ($parentTransaction && in_array($transactionData['type'], array('refund', 'reversal'))) {
                $parentTransaction->state = $transactionData['state'];
                $parentTransaction->save();
            }
        }



        // Process specific IPN data -----------------------------------------------
        if ($order) {
            // Subscription IPN
            if ($order->source_type == 'sitecrowdfunding_project') {
                $this->onPageTransactionIpn($order, $ipn);
                return $this;
            }
            // Unknown IPN
            else {
                $error_msg16 = Zend_Registry::get('Zend_Translate')->_('Unknown order type for IPN');
                throw new Engine_Payment_Plugin_Exception($error_msg16);
            }
        }
        // Missing order
        else {
            $error_msg17 = Zend_Registry::get('Zend_Translate')->_('Unknown or unsupported IPN type, or missing transaction or payment ID');
            throw new Engine_Payment_Plugin_Exception($error_msg17);
        }
    }

    // Forms

    /**
     * Get the admin form for editing the gateway info
     *
     * @return Engine_Form
     */
    public function getAdminGatewayForm() {
        return new Payment_Form_Admin_Gateway_PayPal();
    }

    public function processAdminGatewayForm(array $values) {
        return $values;
    }

}
