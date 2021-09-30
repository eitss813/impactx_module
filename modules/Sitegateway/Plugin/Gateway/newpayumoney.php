<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Payumoney.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

/**
 * All the methods defined in this file will be required to update as per your new payment gateway requirement. However for your better understanding, we have given some sample code under some methods of Stripe gateway. You have to change these method's code according to your gateway requirement.
 */
class Sitegateway_Plugin_Gateway_Payumoney extends Sitegateway_Plugin_Gateway_Abstract {

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
            $class = 'Engine_Payment_Gateway_Payumoney';
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
     * method to process a transaction. 
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $resourceObject
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    protected function createResourceTransaction($user, $resourceObject, $package, $params = array()) {

        //customer name
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['customer_name_first']=$viewer->username;
        if(empty($viewer->username)) {
         $params['customer_name_first']=$viewer->displayname;
         }
        //customer email
            if($viewer->getIdentity())
            {
           // $params['email']=$viewer->email; 
            }
       
        $product_info = "You have received Rs.".round($package->price, 2)." amount for package payment";

        $params['product_info']=$product_info;

        $params['amount']=round($package->price, 2);

        $params['product_id'] = $object_id;

        $params=$this->setExtraParams($params);

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Process return of an user after order transaction. 
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
        if (empty($params['status']) ||empty($params['txnid'])||empty($params['amount'])||empty($params['hash'])||empty($params['firstname'])||empty($params['productinfo'])||empty($params['key'])||empty($params['email'])||empty($params['payuMoneyId'])) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }
         
        // Let's log it
        $this->getGateway()->getLog()->log('Return (Payumoney): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['mihpayid'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $paymentStatus = 'okay';

        $payout_status = '';
        switch (strtolower($params['status'])) {
            case 'success':
    
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                $payout_status = 'success';
                break;

            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                $payout_status = 'failed';
                break;
        }
 
    
        // Validate 
        //Get Salt
        $params = $this->setExtraParams($params);
     
        $givenHash = strtoupper($params['hash']);
    
        if(isset($params["additionalCharges"])) {
            $retHashSeq = $params["additionalCharges"].'|'.$params["salt"].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params["key"];
        } else {   
            $retHashSeq = $params['salt'].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params['key'];
        }
 
        $expectedHash =strtoupper( hash("sha512", $retHashSeq));

        if( $givenHash !== $expectedHash ) {
       
            $paymentStatus = 'failed';
        }
 
        //Insert transaction for (siteeventticket_order: "Tickets Purchase of Advanced Events" and sitestoreproduct_order: "Products Purchase of Stores / Marketplace")
        if ($order->source_type == 'siteeventticket_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        }

        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Payumoney')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $user_order->grand_total,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $params['payKey'],'payout_status'=>$payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay') {

            // Update order info
            $orderTable->update(array('gateway_profile_id' => $gateway_profile_id), array('order_id =?' => $user_order->order_id)
            );

            // Payment success
            $user_order->onPaymentSuccess();

            return 'active';
       
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
     * method to process return of subscription transactions. 
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
            } 
        } else {
            // Check subscription state
            if ($moduleObject->status == 'trial') {
                return 'active';
            } 
        }

        $isOneTimeMethodExist = method_exists($package, 'isOneTime');

        // Check for processed
        
            if (empty($params['status']) ||empty($params['txnid'])||empty($params['amount'])||empty($params['hash'])||empty($params['firstname'])||empty($params['productinfo'])||empty($params['key'])||empty($params['email'])||empty($params['payuMoneyId'])) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
               } 

            $gateway_order_id = $params['mihpayid'];

            $gateway_profile_id = NULL;
        

        // Let's log it
        $this->getGateway()->getLog()->log('Return (Payumoney): '
                . print_r($params, true), Zend_Log::INFO);

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();
         

         $paymentStatus = 'okay';


        $payout_status = '';
        switch (strtolower($params['status'])) {
            case 'success':
    
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                $payout_status = 'success';
                break;

            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                $payout_status = 'failed';
                break;
        }
     
       // Validate 
        //Get Salt
        $params = $this->setExtraParams($params);
        $givenHash = strtoupper($params['hash']);
    
        if(isset($params["additionalCharges"])) {
            $retHashSeq = $params["additionalCharges"].'|'.$params["salt"].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params["key"];
        } else {   
            $retHashSeq = $params['salt'].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params['key'];
        }
 
        $expectedHash =strtoupper( hash("sha512", $retHashSeq));
 
        if( $givenHash !== $expectedHash ) {
       
            $paymentStatus = 'failed';
        }
        
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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Payumoney')),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $package->price,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $params['payKey'],'payout_status'=>$payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Enable now
        if ($giveBenefit && $paymentStatus == 'okay' ) {
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
     * method to process return of a site admin after commissions/bills payment 
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

        if (empty($params['status']) ||empty($params['txnid'])||empty($params['amount'])||empty($params['hash'])||empty($params['firstname'])||empty($params['productinfo'])||empty($params['key'])||empty($params['email'])||empty($params['payuMoneyId'])) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }
         
        // Let's log it
        $this->getGateway()->getLog()->log('Return (Payumoney): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['mihpayid'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $moduleBill->gateway_id = $this->_gatewayInfo->gateway_id;
        $moduleBill->gateway_profile_id = $gateway_profile_id;
        $moduleBill->save();

        $paymentStatus = 'okay';


        $payout_status = '';
        switch (strtolower($params['status'])) {
            case 'success':
    
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                $payout_status = 'success';
                break;

            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                $payout_status = 'failed';
                break;
        }
      


        // Validate 
        //Get Salt
        $params = $this->setExtraParams($params);
        $givenHash = strtoupper($params['hash']);
    
        if(isset($params["additionalCharges"])) {
            $retHashSeq = $params["additionalCharges"].'|'.$params["salt"].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params["key"];
        } else {   
            $retHashSeq = $params['salt'].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params['key'];
        }
 
        $expectedHash =strtoupper( hash("sha512", $retHashSeq));
 
        if( $givenHash !== $expectedHash ) {
       
            $paymentStatus = 'failed';
        }
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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Payumoney')),
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

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $params['payKey'],'payout_status'=>$payout_status));
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
     * Method to process payment transaction after payment request made by sellers 
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

        if (empty($params['status']) ||empty($params['txnid'])||empty($params['amount'])||empty($params['hash'])||empty($params['firstname'])||empty($params['productinfo'])||empty($params['key'])||empty($params['email'])||empty($params['payuMoneyId'])) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }
     
        // Let's log it
        $this->getGateway()->getLog()->log('Return (Payumoney): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['mihpayid'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $user_request->payment_status = 'active';
        $user_request->gateway_profile_id = $gateway_order_id;
        $user_request->save();

        $paymentStatus = 'okay';

        $payout_status = '';
        switch ($params['status']) {
            case 'success':
    
                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                $payout_status = 'success';
                break;

            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                $payout_status = 'failed';
                break;
        }

       // Validate 
        //Get Salt
        $params = $this->setExtraParams($params);
     
        $givenHash = strtoupper($params['hash']);
    
        if(isset($params["additionalCharges"])) {
            $retHashSeq = $params["additionalCharges"].'|'.$params["salt"].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params["key"];
        } else {   
            $retHashSeq = $params['salt'].'|'.$params["status"].'|||||||||||'.$params["email"].'|'.$params["firstname"].'|'.$params["productinfo"].'|'.$params["amount"].'|'.$params["txnid"].'|'.$params['key'];
        }
 
        $expectedHash =strtoupper( hash("sha512", $retHashSeq));
 
        if( $givenHash !== $expectedHash ) {
       
            $paymentStatus = 'failed';
        }
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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Payumoney')),
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


        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $params['payKey'],'payout_status'=>$payout_status));
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
     * method for processing of IPN/Webhooks requests.
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    protected function onResourceTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        
    }

    /**
     * Process ipn of page transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onPageTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        
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

    }

    /**
     * Create a transaction object from given parameters. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @return Engine_Payment_Transaction
     */
    public function createResourceBillTransaction($object_id, $bill_id, $params = array()) {
                    
        $eventBillDetail = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->fetchRow(array('event_id = ?' => $object_id, 'eventbill_id = ?' => $bill_id));
        $event_title = Engine_Api::_()->getItem('siteevent_event', $object_id)->getTitle();
        //customer name
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['customer_name_first']=$viewer->username;
        if(empty($viewer->username)) {
         $params['customer_name_first']=$viewer->displayname;
         }
        //customer email
          
            if($viewer->getIdentity())
            {
        //    $params['email']=$viewer->email; 
            }
        

        $product_info = "You have received Rs.".round($eventBillDetail->amount, 2)." commission for Bill Id #".$bill_id." in event.";

        $params['product_info']=$product_info;

        $params['amount']=round($eventBillDetail->amount, 2);

        $params['product_id'] = $object_id;

        $params=$this->setExtraParams($params);

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Method to create a transaction for an order. 
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

        $tickets_sub_total = $tax = $grand_total = $temp_order_id = 0;
        $buy_tickets = $orderCouponDetail = array();
        $order_tickets = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getAllOrders($parent_order_id);

        $product_info= array();
        
        foreach ($order_tickets as $ticket) {

           
            if (!empty($ticket['coupon_detail'])) {
                $couponDetail = unserialize($ticket['coupon_detail']);
                $couponDetail = isset($couponDetail[$ticket['ticket_id']]) ? $couponDetail[$ticket['ticket_id']] : array();

                if (!empty($couponDetail)) {

                    $couponAmt = -@round($couponDetail['coupon_amount'], 2);

                    if (!empty($couponAmt)) {
                        if (!empty($couponDetail['coupon_type'])) { //FOR FIXED PRICE OFF
                            $discountedCouponPrice = $couponDetail['coupon_amount'];
                        } else { //FOR PERCENTAGE OFF
                            $discountedCouponPrice = $ticket['price'] * $ticket['quantity'] * ($couponDetail['coupon_amount'] / 100);
                        }

                    }
                }
            }

            if ($temp_order_id != $ticket['order_id']) {
               
                $temp_order_id = $ticket['order_id'];

                $grand_total += @round($ticket['grand_total'], 2);
               
            }

        }

        $product_info = "You have received Rs.".@round($grand_total, 2)." payment for Order Id #".$parent_order_id." in event.";
        
        $params['product_info']=$product_info;
                    
        $params['amount']=@round($grand_total, 2);

        $viewer = Engine_Api::_()->user()->getViewer();
         $viewer = Engine_Api::_()->user()->getViewer();
            if($viewer->getIdentity())
            {
           // $params['email']=$viewer->email; 
            $params['customer_name_first']=$viewer->username;
      
         if(empty($viewer->username)) {
         $params['customer_name_first']=$viewer->displayname;
         }
            }      
        
            
        } elseif ($params['source_type'] == 'sitestoreproduct_order') {

            $address = new Sitestoreproduct_Model_DbTable_Orderaddresses();
            $value= $address->getAddress($parent_order_id);
        
            //customer email
            $viewer = Engine_Api::_()->user()->getViewer();
        /*    if($viewer->getIdentity())
            {
            $params['email']=$viewer->email; 
            } else {
            $params['email']=$value->email;  
            }*/
            //customer name
            $params['customer_name_first']=$value->f_name;
            $params['customer_name_last'] =$value->l_name;

            //product info
            $product_info = "You have received Rs.".$order->grand_total." payment for Order Id #".$parent_order_id." in store.";
            $params['product_info']=$product_info;
        
            $params['amount'] = $order->grand_total;
            $params['additionalinfo'] = "Products";
        }
      
        $params = $this->setExtraParams($params);
        //Create transaction

       $transaction = $this->createTransaction($params);
        return $transaction;
    }

    /*
     * SET EXTRA PARAMETER 
     */

    public function setExtraParams($params = array()) {
        

        $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key'];
        $params['salt'] = $this->_gatewayInfo->config['salt'];
        return $params;
    }

    /**
     * Method to create a transaction for a payment request made by sellers.
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
                
             //customer name
            $viewer = Engine_Api::_()->user()->getViewer();
            $params['customer_name_first']=$viewer->username;
            
         if(empty($viewer->username)) {
         $params['customer_name_first']=$viewer->displayname;
         } 
            //customer email
          //  $params['email']=$viewer->email;
          
            //product info
      
            $product_info = "You have received Rs.".round($response[0]['response_amount'], 2)." payment for Request Id #".$request_id." in event.";
            $params['product_info']=$product_info;
   
            $params['amount'] = @round($response[0]['response_amount'], 2);
            $params = $this->setExtraParams($params);

        } elseif ($params['source_type'] == 'sitestoreproduct_paymentrequest') {

            $response = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->getResponseDetail($request_id);
            //customer name
            $viewer = Engine_Api::_()->user()->getViewer();
            $params['customer_name_first']=$viewer->username;
           
            if(empty($viewer->username)) {
            $params['customer_name_first']=$viewer->displayname;
            }
             //get email of customer
         //    $params['email']=$viewer->email;
            //product info
           
            $product_info = "You have received Rs.".round($response[0]['response_amount'], 2)." payment for Request Id #".$request_id." in store .";
            $params['product_info']=$product_info;
      
            $params['amount'] = @round($response[0]['response_amount'], 2);
            $params = $this->setExtraParams($params);

        }             
       
        // Create transaction
        $transaction = $this->createTransaction($params);

      
        return $transaction;
    }
     
  /**
   * Create a transaction object from specified parameters
   *
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params)
  {
    
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

    /**
     * Method to process an IPN/Webhooks request for an order transaction.
     *
     * @param Engine_Payment_Ipn $ipn
     * @return Engine_Payment_Plugin_Abstract
     */
    public function onIpn(Engine_Payment_Ipn $ipn) {

  
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
        return new Sitegateway_Form_Admin_Gateway_Payumoney();
    }

    /**
     * Process the form for editing the gateway credentials.
     *
     * @return Engine_Form
     */
    public function processAdminGatewayForm(array $values) {

    return $values;
        
    }
   
   /**
     * Method to create a transaction for commision made by store owner to site admin. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param User_Model_User $user
     * @param $request_id
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createStoreBillTransaction($store_id, $bill_id, $params = array()) {
  
       //FETCH RESPONSE DETAIL
        $storeBillDetail = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'storebill_id = ?' => $bill_id));
        $store_title = Engine_Api::_()->getItem('sitestore_store', $store_id)->getTitle();

        //customer name
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['customer_name_first']=$viewer->username;
        //customer email
          if(empty($viewer->username)) {
         $params['customer_name_first']=$viewer->displayname;
         }
       
      //  $params['email']=$viewer->email; 

        //product info
        $product_info = "You have received Rs.".round($storeBillDetail->amount, 2)." commision for Bill Id #".$bill_id." in store .";
        $params['product_info']=$product_info;
        $params['amount'] = @round($storeBillDetail->amount, 2);
        $params = $this->setExtraParams($params);
        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

}