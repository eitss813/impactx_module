<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Mollie.php 2017-06-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

/**
 * All the methods defined in this file will be required to update as per your new payment gateway requirement. However for your better understanding, we have given some sample code under some methods of Stripe gateway. You have to change these method's code according to your gateway requirement.
 */
class Sitegateway_Plugin_Gateway_Mollie extends Sitegateway_Plugin_Gateway_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;
    protected $_currencySymbol;

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
        $this->_currencySymbol = Engine_Api::_()->sitegateway()->getCurrencySymbol();
    }

    /**
     * Method to get the gateway object.
     *
     * @return Engine_Payment_Gateway
     */
    public function getGateway() { 

        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_Mollie';
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

    public function detectIpn($params = array()) { 
      
        //nothing---just to call onIpn from payment/ipn
        return true;
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
  

        $params['quantity'] = 1;
        $params['amount'] = round($package->price, 2);
        $params['additionalinfo'] = "Packages"; 

        $title = '';
        if(!empty($resourceObject->title)) {
           $title = "of ".$resourceObject->title;
         } 
        $params['product_info'] = "You have received $this->_currencySymbol".round($package->price, 2)." amount for package payment ".$title; 
        $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key'];

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
        
        $product_info= array(); 
        $eventTitle = Engine_Api::_()->getItem('siteevent_event', $order->event_id)->getTitle();
        $grand_total = $order->grand_total;

        $product_info = "You have received $this->_currencySymbol".@round($grand_total, 2)." payment for Order Id #".$parent_order_id." in event ".$eventTitle;
        
        $params['product_info'] = $product_info;
                    
        $params['amount'] = @round($grand_total, 2); 
            
        } elseif ($params['source_type'] == 'sitestoreproduct_order') { 

          $grand_total = $temp_order_id = 0; 

        // downpayment remaining amount payment
        if( !empty($params['downpayment_make_payment']) ) {
          $getStoreId = true;
          if( isset($params['store_id']) ) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $params['store_id']);
            unset($params['store_id']);
            $getStoreId = false;
          }  
            
          $order_products = Engine_Api::_()->getDbtable('orderProducts','sitestoreproduct')->getRemainingAmountOrderProducts(array('order_id' => $parent_order_id, 'getStoreId' => $getStoreId));
          
          foreach($order_products as $product) {
            $products_sub_total += @round($product['product_price'] * $product['quantity'], 2); 
     
            if( empty($sitestore) && empty($temp_order_id) ) { 
              $temp_order_id = true;
            } 
          }
          $grand_total = $products_sub_total;
          unset($params['downpayment_make_payment']);
        } else {
          if( isset($params['store_id']) && !empty($params['store_id']) ) {
            $order_products = Engine_Api::_()->getDbtable('orders','sitestoreproduct')->getAllOrders($parent_order_id, array('store_id' => $params['store_id'], 'isDownPaymentEnable' => $params['isDownPaymentEnable']));
            unset($params['store_id']);
          } else {
            $order_products = Engine_Api::_()->getDbtable('orders','sitestoreproduct')->getAllOrders($parent_order_id, array('isDownPaymentEnable' => $params['isDownPaymentEnable']));
          }

          foreach($order_products as $product) {
            if( $temp_order_id != $product['order_id'] ) { 
              $temp_order_id = $product['order_id'];
              if( empty($params['isDownPaymentEnable']) ) {
                $grand_total += @round($product['grand_total'], 2); 
              } else { 
                $tempGrandTotal = round((@round($product['store_tax'], 2) + @round($product['admin_tax'], 2) + @round($product['shipping_price'], 2) + @round($product['downpayment_total'], 2)), 2);
                $grand_total += $tempGrandTotal;
              }
            } 
          } 
          unset($params['isDownPaymentEnable']); 
           
        }  
            $storeTitle = Engine_Api::_()->getItem('sitestore_store', $order->store_id)->getTitle();

             //product info
            $product_info = "You have received $this->_currencySymbol".$grand_total." payment for Order Id #".$parent_order_id." in store ".$storeTitle;
            $params['product_info'] = $product_info;
        
            $params['amount'] = $grand_total; 

        }  elseif ($params['source_type'] == 'sitecredit_order') {  
            $params['additionalinfo'] = "Credits"; 
            $product_info = "You have received $this->_currencySymbol".@round($order->grand_total, 2)." payment for Order Id #".$parent_order_id." for credits"; 
            $params['product_info'] = $product_info; 
            $params['amount'] = $order->grand_total; 
        } 
        $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key'];
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
            
            $eventTitle = Engine_Api::_()->getItem('siteevent_event', $response[0]['event_id'])->getTitle();
             //customer name
            $viewer = Engine_Api::_()->user()->getViewer(); 

            //product info 
            $product_info = "You have received $this->_currencySymbol".round($response[0]['response_amount'], 2)." payment for Request Id #".$request_id." in event ".$eventTitle;
            $params['product_info']=$product_info;
   
            $params['amount'] = @round($response[0]['response_amount'], 2);
            $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key'];

        } elseif ($params['source_type'] == 'sitestoreproduct_paymentrequest') {

            $response = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->getResponseDetail($request_id);
            //customer name
            $viewer = Engine_Api::_()->user()->getViewer();

            $storeTitle = Engine_Api::_()->getItem('sitestore_store', $response[0]['store_id'])->getTitle();

            //product info 
            $product_info = "You have received $this->_currencySymbol".round($response[0]['response_amount'], 2)." payment for Request Id #".$request_id." in store ".$storeTitle;
            $params['product_info'] =  $product_info;
      
            $params['amount'] = @round($response[0]['response_amount'], 2);
            $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key'];

        }             
       
        // Create transaction
        $transaction = $this->createTransaction($params); 
        return $transaction;
    }


    /**
     * You must to define this method to process return of subscription transactions. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    protected function onResourceTransactionReturn(Payment_Model_Order $order, array $params = array()) { 
        
        $payment = $this->getService()->getPayment($order->gateway_order_id, $this->_gatewayInfo->config['merchant_key']);
        $params = array_merge($params, (array)$payment);
 
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg1);
        }

        if (empty($params['status']) ||empty($params['id'])||empty($params['createdDatetime'])||empty($params['amount'])||empty($params['profileId'])||empty($params['description'])||empty($params['metadata'])||empty($params['links'])) {  
                $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg2);
        }

        // Get related info
        $user = $order->getUser();
        $moduleObject = $order->getSource();
        $package = $moduleObject->getPackage();

        $moduleName = explode("_", $package->getType());
        $moduleName = $moduleName['0'];  
        
        // Check subscription state
        if ($moduleObject->status == 'trial') {
            return 'active';
        } elseif ($moduleObject->status == 'pending') {
            return 'pending';
        } 

        $gateway_order_id = $params['id'];
        $gateway_profile_id = $params['profileId'];  

        $isOneTimeMethodExist = method_exists($package, 'isOneTime');  

        // Check for processed
        if ($isOneTimeMethodExist && !$package->isOneTime()) {  
            
            $gatewayPlugin = $this->getService();  
            //MANDATES RELATED TO CUSTOMER  
            $mandates = $gatewayPlugin->customerMandates($params['customerId']);
 
            //IF THE FIRST PAYMENT WAS SUCCESSFULL AND MANDATE ALSO AVAILABLE THAN CREATE SUBSCRIPTION FOR CUSTOMER
            if($params['status'] == 'paid' && ($mandates->data[0]->status == 'valid' || $mandates->data[0]->status == 'pending')) {

                //DECREASE THE TIMES BY 1 AND INCREMENT THE START DATE ALSO BECAUSE FIRST PAYMENT HAS BEEN DONE
                $times = $package->getTotalBillingCycleCount()-1;
                $recurrence = $package->recurrence; 
                $recurrType = $package->recurrence_type;
                $interval = $recurrence.' '.$recurrType;  

                if($recurrType == 'day'){
                    $days = $recurrence; 
                } elseif($recurrType == 'week'){
                    $days = $recurrence*7;
                } elseif($recurrType == 'month'){
                    $days = $recurrence*30;
                }  
                $startDate = date("Y-m-d", strtotime('+'.$days.'days')); 
                
                $subscriptionParams = array(
                            "amount" => $params['amount'],
                            "times" => $times,
                            "interval" => $interval,
                            'startDate' => $startDate,
                            "description" => 'RECURRING: '.$params['description'], 
                            "webhookUrl"  => $params['metadata']->hookURL
                        );
              //CREATE SUBSCRIPTION ONLY WHEN TIMES IS MORE THAN ONE
              if($times > 1) {

                $subscription = $gatewayPlugin->createSubscription($subscriptionParams, $params['customerId']);  
                if($subscription->status == 'active') { 
                    //SUBSCRIPTION IS SAVED TO FETCH AT THE TIME OF WEBHOOK CALL
                    $order->gateway_transaction_id = $subscription->id; 
                    $order->save();
                } else {
                   $errorR = Zend_Registry::get('Zend_Translate')->_('Subscription not activated');
                    throw new Engine_Payment_Plugin_Exception($errorR);
                }
                  //WRITE THE SUBSCRIPTION DATA IN THE PAYMENT LOG FILE
                  $this->getGateway()->getLog()->log('Return (Mollie): '
                  . print_r($subscription, true), Zend_Log::INFO); 
              }  
            } 
        }  

        // Let's log it
        $this->getGateway()->getLog()->log('Return (Mollie): '
                . print_r($params, true), Zend_Log::INFO); 
        $paymentStatus = 'okay'; 

        switch (strtolower($params['status'])) {
            case 'paid':
                $paymentStatus = 'okay';
                $orderStatus = 'complete'; 
                break; 
            default:  
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; 
                break;
        } 
        $order->state = $orderStatus; 
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
            $otherinfo->gateway_profile_id = $gateway_order_id;
            $otherinfo->save(); 
        } else {
            $moduleObject->gateway_id = $this->_gatewayInfo->gateway_id;
            $moduleObject->gateway_profile_id = $gateway_order_id; 
            $moduleObject->save();
        }

        // Insert transaction
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', $moduleName);
        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Mollie')),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $package->price,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

         
        if ($paymentStatus == 'okay') { 
           
            $moduleObject->onPaymentSuccess();

            // send notification
            if ($moduleObject->didStatusChange()) {

               // Here 'payment_subscription' is used for SocialEngine signup subscription plans. 
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

        } else {  // Enable later

            //This is the same as sale_id  
            $moduleObject->onPaymentPending();

            // send notification
            if ($moduleObject->didStatusChange()) { 

                //Here 'payment_subscription' is used for SocialEngine signup subscription plans. 
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

        $payment = $this->getService()->getPayment($order->gateway_order_id, $this->_gatewayInfo->config['merchant_key']);
        $params = array_merge($params, (array)$payment);

        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {  
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg1);
        }
        // Get related info
        $user = $order->getUser();
        $moduleBill = $order->getSource();
 
        // Check subscription state
        if ($moduleBill->status == 'trial') {
            return 'active';
        } else
        if ($moduleBill->status == 'pending') {
            return 'pending';
        }
 
        if (empty($params['status']) ||empty($params['id'])||empty($params['createdDatetime'])||empty($params['amount'])||empty($params['profileId'])||empty($params['description'])||empty($params['metadata'])||empty($params['links'])) {  
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        } 
         
         // Let's log it
        $this->getGateway()->getLog()->log('Return (Mollie): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_order_id = $params['id'];
        $gateway_profile_id = $params['profileId']; 

        $moduleBill->gateway_id = $this->_gatewayInfo->gateway_id;
        $moduleBill->gateway_profile_id = $gateway_profile_id;
        $moduleBill->save();

        $paymentStatus = 'okay'; 
        $payout_status = '';
        
        switch (strtolower($params['status'])) {
            case 'paid':
    
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

        $order->state = $orderStatus;
        $order->gateway_order_id = $gateway_order_id;
        $order->save(); 

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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Mollie')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $moduleBill->amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

       $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $gateway_profile_id,'payout_status'=>$payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
 
        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending')) {

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

        $payment = $this->getService()->getPayment($order->gateway_order_id, $this->_gatewayInfo->config['merchant_key']);
        $params = array_merge($params, (array)$payment);

        $user_request = $order->getSource();
        $user = $order->getUser();

        if ($user_request->payment_status == 'pending') {
            return 'pending';
        }

        if (empty($params['status']) ||empty($params['id'])||empty($params['createdDatetime'])||empty($params['amount'])||empty($params['profileId'])||empty($params['description'])||empty($params['metadata'])||empty($params['links'])) {  
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        } 
     
          // Let's log it
        $this->getGateway()->getLog()->log('Return (Mollie): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $params['profileId'];
        $gateway_order_id = $params['id']; 

        $user_request->payment_status = 'active';
        $user_request->gateway_profile_id = $gateway_profile_id;
        $user_request->save();

        $paymentStatus = 'okay';

        $payout_status = '';
        switch ($params['status']) {
            case 'paid':
    
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
         // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_order_id = $gateway_order_id;
        $order->save();
        
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
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Mollie')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $user_request->response_amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        ); 

        $transactionsTable->insert($transactionParams); 

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $gateway_profile_id,'payout_status'=>$payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
 
        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending')) {

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
     * Process return of an user after order transaction. 
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserOrderTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $payment = $this->getService()->getPayment($order->gateway_order_id, $this->_gatewayInfo->config['merchant_key']);
        $params = array_merge($params, (array)$payment);
   
        $user_order = $order->getSource();
        $user = $order->getUser();

        if ($user_order->payment_status == 'pending') {
            return 'pending';
        }
       if (empty($params['status']) ||empty($params['id'])||empty($params['createdDatetime'])||empty($params['amount'])||empty($params['profileId'])||empty($params['description'])||empty($params['metadata'])||empty($params['links'])) {  
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        } 
         
         // Let's log it
        $this->getGateway()->getLog()->log('Return (Mollie): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $params['profileId']; 
        $gateway_order_id = $params['id']; 
        
        

        $paymentStatus = 'okay';

        $payout_status = '';
        switch (strtolower($params['status'])) {
            case 'paid':
    
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
        $order->state = $orderStatus;
        $order->gateway_order_id = $gateway_order_id;
        $order->save(); 
      
        //Insert transaction for (siteeventticket_order: "Tickets Purchase of Advanced Events" and sitestoreproduct_order: "Products Purchase of Stores / Marketplace")
        if ($order->source_type == 'siteeventticket_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        } elseif ($order->source_type == 'sitecredit_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecredit');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitecredit');
            $orderIdColumnName = 'order_id';
        }

        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Mollie')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $user_order->grand_total,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $gateway_order_id,'payout_status'=>$payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
  
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

        $orderTable = Engine_Api::_()->getDbtable('orders', 'payment');  

        $payment = $this->getService()->getPayment($_POST["id"], $this->_gatewayInfo->config['merchant_key']);  

        // Let's log it
        $this->getGateway()->getLog()->log('Return (Mollie): '
                . print_r($payment, true), Zend_Log::INFO);
        

        if($payment->status == 'paid') {
            $orderState = 'Complete';
            $state = 'okay';
            $payoutStatus = 'success';
        } else {
            $orderState = 'falied';
            $state = 'falied';
            $payoutStatus = 'falied';
        }
        
        $orderParams = array(
            'user_id' => $user->user_id,
            'gateway_id' => $order->gateway_id,
            'gateway_order_id' => $payment->id,//TRANSACTION ID OF RECURRING PAYMENT OF SUBSCRIPTION
            'gateway_transaction_id' => $payment->subscriptionId,
            'state' => $orderState,
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => $order->source_type,
            'source_id' => $order->source_id, 
        );

        $orderTable->insert($orderParams); 
      
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', $moduleName);
         
        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_Mollie')),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id, 
            'type' => 'recurring payment',
            'state' => $state,
            'gateway_transaction_id' => $payment->id,
            'gateway_parent_transaction_id' => $payment->subscriptionId, 
            'amount' => $payment->amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);
 
        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type,'resource_id' => $order->source_id, 'gateway_payment_key' => $payment->profileId, 'gateway_subscription_id' => $payment->subscriptionId));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
 
        if($state == 'okay') {

            $moduleObject->onPaymentSuccess();

            // send notification
            if ($moduleObject->didStatusChange()) {
            //SEND MAIL TO THE CUSTOMER FOR RECURRING PAYMENT INFORMATION 
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
            $this->cancelSubscriptionOnExpiry($moduleObject, $package);
        } else { 
          
            $moduleObject->onPaymentPending();

            // send notification
            if ($moduleObject->didStatusChange()) { 
                //SEND MAIL TO CUSTOMER FOR PENDING PAYMENT FOR SUBSCRIPTION
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
        } 
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
        $merchant_key = $this->_gatewayInfo->config['merchant_key'];
        try {
            $r = $this->getService()->cancelRecurringPaymentsProfile($profileId, $merchant_key);
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

    public function createResourceBillTransaction($project_id, $bill_id, $params = array()){

    } 

   /**
     * Method to create a transaction for commision made by store owner to site admin. You need to write the code accordingly in this method if you have enabled "Stores / Marketplace - Ecommerce" plugins.
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
 
        //product info
        $product_info = "You have received $this->_currencySymbol".round($storeBillDetail->amount, 2)." commision for Bill Id #".$bill_id." in store ".$store_title;
        $params['product_info']=$product_info;
        $params['amount'] = @round($storeBillDetail->amount, 2);
        $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key'];
        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    } 
     /**
     * Method to create a transaction for commision made by store owner to site admin. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events"   
     * 
     * @param $request_id
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createEventBillTransaction($event_id, $bill_id, $params = array()) {
    
       //FETCH RESPONSE DETAIL
        $eventBillDetail = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id, 'eventbill_id = ?' => $bill_id));
        $event_title = Engine_Api::_()->getItem('siteevent_event', $event_id)->getTitle(); 
       
        //product info
        $product_info = "You have received $this->_currencySymbol".round($eventBillDetail->amount, 2)." commision for Bill Id #".$bill_id." in event ".$event_title;
        $params['product_info']=$product_info;

        $params['amount'] = @round($eventBillDetail->amount, 2);
        $params['merchant_key'] = $this->_gatewayInfo->config['merchant_key']; 
        // Create transaction
        $transaction = $this->createTransaction($params); 
        return $transaction;
    } 

     /**
   * Create a transaction object from specified parameters
   *
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params) {
    
    $transaction = new Engine_Payment_Transaction($params);
    $gateway = $this->getGateway();
    $transaction->process($gateway);
    return $transaction;
  } 


    /**
     * Method to process an IPN/Webhooks request for an order transaction.
     *
     * @param Engine_Payment_Ipn $ipn
     * @return Engine_Payment_Plugin_Abstract
     */
    public function onIpn(Engine_Payment_Ipn $ipn) {

        //GET THE ORDER ROW FOR WHICH SUBSCRIPTION WAS CREATED USING ORDER ID
        $rawData = $ipn->getRawData(); 
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment'); 
        $order = null; 
     
        if (!empty($rawData['order_id'])) {
            $gateway_order_id = $rawData['order_id'];

            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'order_id = ?' => $rawData['order_id'],
            ));
        } 

        if ($order) {
            return $this->onResourceIpn($order, $ipn);
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
        return new Sitegateway_Form_Admin_Gateway_Mollie();
    }

    /**
     * Process the form for editing the gateway credentials.
     *
     * @return Engine_Form
     */
    public function processAdminGatewayForm(array $values) {
        return $values;
    }

}
