<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Paytm.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
include_once APPLICATION_PATH . "/application/modules/Epaytm/Api/PaytmKit/lib/encdec_paytm.php";
class Sesblogpackage_Plugin_Gateway_Paytm extends Engine_Payment_Plugin_Abstract
{
  protected $_gatewayInfo;
  protected $_gateway;
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo)
  {
    $this->_gatewayInfo = $gatewayInfo;
  }
  public function getService()
  {
    return $this->getGateway()->getService();
  }
  public function getGateway()
  {
    if( null === $this->_gateway ) {
        $class = 'Epaytm_Gateways_Paytm';
        Engine_Loader::loadClass($class);
        $gateway = new $class(array(
        'config' => (array) $this->_gatewayInfo->config,
        'testMode' => $this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if( !($gateway instanceof Engine_Payment_Gateway) ) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }
    return $this->_gateway;
  }
  public function createTransaction(array $params)
  {
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }
  public function createIpn(array $params)
  {
    $ipn = new Engine_Payment_Ipn($params);
    $ipn->process($this->getGateway());
    return $ipn;
  }
  public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $subscription, Payment_Model_Package $package, array $params = array()) {
  }
  public function createBlogTransaction(User_Model_User $user,
      Zend_Db_Table_Row_Abstract $subscription,
      Zend_Db_Table_Row_Abstract $package,
      array $params = array())
  {
    // Process description
    $desc = $package->getPackageDescription();
    if( strlen($desc) > 127 ) {
      $desc = substr($desc, 0, 124) . '...';
    } else if( !$desc || strlen($desc) <= 0 ) {
      $desc = 'N/A';
    }
    if( function_exists('iconv') && strlen($desc) != iconv_strlen($desc) ) {
      // PayPal requires that DESC be single-byte characters
      $desc = @iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
    }
    // This is a one-time fee
    if( $package->isOneTime() ) {
        $paytmParams  = array(
          /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
          "MID" => $this->_gatewayInfo->config['paytm_marchant_id'],
          /* Find your WEBSITE in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
          "WEBSITE" => $this->_gatewayInfo->config['paytm_website'],
          /* Find your INDUSTRY_TYPE_ID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
          "INDUSTRY_TYPE_ID" => $this->_gatewayInfo->config['paytm_industry_type'],
          /* WEB for website and WAP for Mobile-websites or App */
          "CHANNEL_ID" => $this->_gatewayInfo->config['paytm_channel_id'],
          /* Enter your unique order id */
          "ORDER_ID" => $params['vendor_order_id'],
          /* unique id that belongs to your customer */
          "CUST_ID" => $user->getIdentity(),
          /* customer's mobile number */
          /**
          * Amount in INR that is payble by customer
          * this should be numeric with optionally having two decimal points
          */
          "TXN_AMOUNT" =>  $params['amount'],
          /* on completion of transaction, we will send you the response on this URL */
          "CALLBACK_URL" => $params['return_url'],
        );
    }
    // This is a recurring subscription
    else { 
      $addTime = $package->duration == 1 ? "+".$package->duration.' '.$package->duration_type : "+".$package->duration.' '.$package->duration_type.'s';
      $paytmParams = array(
        "REQUEST_TYPE"			=> "SUBSCRIBE",
        "MID"					=> $this->_gatewayInfo->config['paytm_marchant_id'],
        "WEBSITE"				=> $this->_gatewayInfo->config['paytm_website'],
        "INDUSTRY_TYPE_ID"	    => $this->_gatewayInfo->config['paytm_industry_type'],
        "CHANNEL_ID" 			=> $this->_gatewayInfo->config['paytm_channel_id'],
        "ORDER_ID" 			=> $params['vendor_order_id'],
        "CUST_ID"				=> $user->getIdentity(),
        "TXN_AMOUNT"			=> $params['amount'],
        "SUBS_AMOUNT_TYPE" 	=> "VARIABLE",
        "SUBS_MAX_AMOUNT"		=> $params['amount'],
        "SUBS_FREQUENCY_UNIT"  => strtoupper($package->recurrence_type),
        "SUBS_FREQUENCY"		=> $package->recurrence,
        "SUBS_ENABLE_RETRY"	=> "1",
        "SUBS_START_DATE"		=> date('Y-m-d'),
        "SUBS_EXPIRY_DATE"	    => date('Y-m-d',strtotime($addTime,strtotime(date('Y-m-d')))),
        "SUBS_PPI_ONLY"		=> "N",
        "SUBS_PAYMENT_MODE"	=> "CC",
        "SUBS_GRACE_DAYS"		=> "1",
        "CALLBACK_URL"			=> $params['return_url']
      );
    }
    return $paytmParams;
    // Create transaction
  }
  public function onSubscriptionReturn(
      Payment_Model_Order $order,$transaction)
  {}
  public function onBlogTransactionReturn(
    Payment_Model_Order $order, array $params = array()) {
    if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }
    // Get related info
    $user = $order->getUser();
    $item = $order->getSource();
    $package = $item->getPackage();
    $transaction = $item->getTransaction();
    // Check subscription state
    if($transaction->state == 'active' ||
        $transaction->state == 'trial') {
      return 'active';
    } else if( $transaction->state == 'pending' ) {
      return 'pending';
    }
    // Check for cancel state - the user cancelled the transaction
    if($params['STATUS'] == 'TXN_FAILURE' ) {
      // Cancel order and subscription?
      $order->onCancel();
      $item->onPaymentFailure();
      // Error
      throw new Payment_Model_Exception('Your payment has been cancelled and ' .
          'not been charged. If this is not correct, please try again later.');
    }
          // Get payment state
    $paymentStatus = null;
    $orderStatus = null;
    switch($params['STATUS']) {
      case 'created':
      case 'pending':
        $paymentStatus = 'pending';
        $orderStatus = 'complete';
        break;
      case 'active':
      case 'succeeded':
      case 'completed':
      case 'processed':
      case 'TXN_SUCCESS': // Probably doesn't apply
        $paymentStatus = 'okay';
        $orderStatus = 'complete';
        break;
      case 'denied':
      case "TXN_FAILURE": 
        $paymentStatus = 'failed';
        $orderStatus = 'failed'; 
      case 'voided': // Probably doesn't apply
      case 'reversed': // Probably doesn't apply
      case 'refunded': // Probably doesn't apply
      case 'TXN_FAILURE':  // Probably doesn't apply
      default: // No idea what's going on here
        $paymentStatus = 'failed';
        $orderStatus = 'failed'; // This should probably be 'failed'
        break;
    }
//     // One-time
    if($package->isOneTime()) {
      // Update order with profile info and complete status?
      $order->state = $orderStatus;
      $order->gateway_transaction_id = $params['TXNID'];
      $order->save();
      $orderPackageId = $item->existing_package_order ? $item->existing_package_order : false;
      $orderPackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $orderPackageId);
      if (!$orderPackageId || !$orderPackage) {
        $transactionsOrdersTable = Engine_Api::_()->getDbtable('orderspackages', 'sesblogpackage');
        $transactionsOrdersTable->insert(array(
            'owner_id' => $order->user_id,
            'item_count' => ($package->item_count - 1 ),
            'package_id' => $package->getIdentity(),
            'state' => $paymentStatus,
            'expiration_date' => $package->getExpirationDate(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'modified_date' => new Zend_Db_Expr('NOW()'),
        ));
        $orderPackageId = $transactionsOrdersTable->getAdapter()->lastInsertId();
      } else {
        $orderPackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $orderPackageId);
        $orderPackage->item_count = $orderPackage->item_count--;
        $orderPackage->save();
        $orderPackageId = $orderPackage->getIdentity();
      }
      $session = new Zend_Session_Namespace('Payment_Sesblogpackage');
      $currency = $session->currency;
      $rate = $session->change_rate;
      if (!$rate)
        $rate = 1;
      $defaultCurrency = Engine_Api::_()->sesblogpackage()->defaultCurrency();
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $currencyValue = 1;
      if ($currency != $defaultCurrency)
        $currencyValue = $settings->getSetting('sesmultiplecurrency.' . $currency);
      $price = @round(($params['amount'] * $currencyValue), 2);
      //Insert transaction
      $daysLeft = 0;
      //check previous transaction if any for reniew
      if (!empty($transaction->expiration_date) && $transaction->expiration_date != '3000-00-00 00:00:00') {
        $expiration = $package->getExpirationDate();
        //check isonetime condition and renew exiration date if left
        if ($package->isOneTime()) {
          $datediff = strtotime($transaction->expiration_date) - time();
          $daysLeft = floor($datediff / (60 * 60 * 24));
        }
      }
      $oldOrderPackageId = $item->orderspackage_id;
      $tableBlog = Engine_Api::_()->getDbTable('blogs', 'sesblog');
      if (!empty($oldOrderPackageId)) {
        $select = $tableBlog->select()->from($tableBlog->info('name'))->where('orderspackage_id =?', $oldOrderPackageId);
        $totalItemCreated = count($tableBlog->fetchAll($select));
        if ($package->item_count >= $totalItemCreated && $package->item_count)
          $leftBlog = $package->item_count - $totalItemCreated;
        else if (!$package->item_count)
          $leftBlog = -1;
        else
          $leftBlog = 0;
      } else
      $leftBlog = $package->item_count - 1;
      $tableBlog->update(array('orderspackage_id' => $orderPackageId), array('orderspackage_id' => $oldOrderPackageId));
      $packageOrder = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $orderPackageId);
      $packageOrder->item_count = $leftBlog;
      $packageOrder->save();
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage');
      $transactionsTable->insert(array(
          'owner_id' => $order->user_id,
          'package_id' => $item->package_id,
          'item_count' => $leftBlog,
          'gateway_id' => $this->_gatewayInfo->gateway_id,
          'gateway_transaction_id' => $params['TXNID'],
          'creation_date' => new Zend_Db_Expr('NOW()'),
          'modified_date' => new Zend_Db_Expr('NOW()'),
          'order_id' => $order->order_id,
          'orderspackage_id' => $orderPackageId,
          'state' => 'initial',
          'total_amount' => $params['amount'],
          'change_rate' => $rate,
          'gateway_type' => 'Paytm',
          'currency_symbol' => $currency,
          'ip_address' => $_SERVER['REMOTE_ADDR'],
      ));
      $transaction_id = $transactionsTable->getAdapter()->lastInsertId();
      $item->transaction_id = $transaction_id;
      $item->orderspackage_id = $orderPackageId;
      $item->existing_package_order = 0;
      $item->save();
      $transaction = Engine_Api::_()->getItem('sesblogpackage_transaction', $transaction_id);
      // Get benefit setting
      $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')
              ->getBenefitStatus($user);
      // Check payment status
      if ($paymentStatus == 'okay' || $paymentStatus == 'active' ||
              ($paymentStatus == 'pending' && $giveBenefit)) {
        //Update subscription info
        $transaction->gateway_id = $this->_gatewayInfo->gateway_id;
        $transaction->gateway_profile_id = $params['TXNID'];
        $transaction->save();
        // Payment success
        $transaction = $item->onPaymentSuccess();
        if ($daysLeft >= 1) {
          $expiration_date = date('Y-m-d H:i:s', strtotime($transaction->expiration_date . '+ ' . $daysLeft . ' days'));
          $transaction->expiration_date = $expiration_date;
          $transaction->save();
          $orderpackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $orderPackageId);
          $orderpackage->expiration_date = $expiration_date;
          $orderpackage->save();
        }
        //For Coupon Plugin
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ecoupon')){
          $transaction->ordercoupon_id = Engine_Api::_()->ecoupon()->setAppliedCouponDetails($params['couponSessionCode']);
          $transaction->save();
        }
        //For Credit 
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sescredit') && isset($params['creditCode'])) {
          $sessionCredit = new Zend_Session_Namespace($params['creditCode']);
          $transaction->credit_point = $sessionCredit->credit_value;  
          $transaction->credit_value =  $sessionCredit->purchaseValue;
          $transaction->save();
          $userCreditDetailTable = Engine_Api::_()->getDbTable('details', 'sescredit');
          $userCreditDetailTable->update(array('total_credit' => new Zend_Db_Expr('total_credit - ' . $sessionCredit->credit_value)), array('owner_id =?' => $order->user_id));
        }
        // send notification
        if ($item->didStatusChange()) {
          /* Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            )); */
        }
        return 'active';
      } else if ($paymentStatus == 'pending') {
        // Update subscription info
        $transaction->gateway_id = $this->_gatewayInfo->gateway_id;
        $transaction->gateway_profile_id = $params['TXNID'];
        $transaction->save();
        // Payment pending
        $item->onPaymentPending();
        
        // send notification
        /* if( $transaction->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
          'subscription_title' => $package->title,
          'subscription_description' => $package->description,
          'subscription_terms' => $package->getPackageDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
          } */
        return 'pending';
      } else if ($paymentStatus == 'failed') {
        // Cancel order and subscription?
        $order->onFailure();
        $item->onPaymentFailure();
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
      
    } // For Recurring Payment
    else {
      $isExistsOrderPackageId = $orderPackageId = $item->existing_package_order ? $item->existing_package_order : false;
      if (!$orderPackageId) {
        if (!$orderPackageId) {
          $transactionsOrdersTable = Engine_Api::_()->getDbtable('orderspackages', 'sesblogpackage');
          $transactionsOrdersTable->insert(array(
              'owner_id' => $order->user_id,
              'item_count' => ($package->item_count - 1 ),
              'state' => 'active',
              'package_id' => $package->getIdentity(),
              'expiration_date' => $package->getExpirationDate(),
              'ip_address' => $_SERVER['REMOTE_ADDR'],
              'creation_date' => new Zend_Db_Expr('NOW()'),
              'modified_date' => new Zend_Db_Expr('NOW()'),
          ));
          $orderPackageId = $transactionsOrdersTable->getAdapter()->lastInsertId();
        }
      } else {
        $orderPackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $orderPackageId);
        $orderPackage->item_count = $orderPackage->item_count--;
        $orderPackage->save();
      }
      $item->existing_package_order = 0;
      $item->save();
      $session = new Zend_Session_Namespace('Payment_Sesblogpackage');
      $currency = $session->currency;
      $rate = $session->change_rate;
      if (!$rate)
        $rate = 1;
      $defaultCurrency = Engine_Api::_()->sesblogpackage()->defaultCurrency();
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $currencyValue = 1;
      if ($currency != $defaultCurrency) {
        $currencyValue = $settings->getSetting('sesmultiplecurrency.' . $currency);
      }
      $price = @round(($params['amount'] * $currencyValue), 2);

      // Insert transaction
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage');
      $transactionsTable->insert(array(
          'owner_id' => $order->user_id,
          'package_id' => $item->package_id,
          'item_count' => ($package->item_count - 1),
          'gateway_id' => $this->_gatewayInfo->gateway_id,
          'gateway_transaction_id' => $params['TXNID'],
          'orderspackage_id' => $orderPackageId,
          'creation_date' => new Zend_Db_Expr('NOW()'),
          'modified_date' => new Zend_Db_Expr('NOW()'),
          'order_id' => $order->order_id,
          'state' => 'initial',
          'total_amount' => $params['amount'],
          'change_rate' => $rate,
          'gateway_type' => 'Paytm',
          'currency_symbol' => $currency,
          'ip_address' => $_SERVER['REMOTE_ADDR'],
      ));
      $transaction_id = $transactionsTable->getAdapter()->lastInsertId();
      $item->transaction_id = $transaction_id;
      $item->orderspackage_id = $orderPackageId;
      $item->save();
      $transaction = Engine_Api::_()->getItem('sesblogpackage_transaction', $transaction_id);
      if (!$isExistsOrderPackageId) {
        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $params['TXNID'];
        $order->save();
        // Get benefit setting
        $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')
                ->getBenefitStatus($user);
        // Check profile status
        if ($paymentStatus == 'okay' || $paymentStatus == 'active' ||
              ($paymentStatus == 'pending' && $giveBenefit)) {
          // Enable now
          $transaction->gateway_id = $this->_gatewayInfo->gateway_id;
          $transaction->gateway_profile_id = $params['SUBS_ID'];
          $transaction->save();
          $item->onPaymentSuccess();
          Engine_Api::_()->epaytm()->updateRenewal($transaction,$package);
           //For Coupon Plugin
          if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ecoupon')){
            $transaction->ordercoupon_id = Engine_Api::_()->ecoupon()->setAppliedCouponDetails($params['couponSessionCode']);
            $transaction->save();
          }
          //For Credit 
          if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sescredit') && isset($params['creditCode'])) {
            $sessionCredit = new Zend_Session_Namespace($params['creditCode']);
            $transaction->credit_point = $sessionCredit->credit_value;  
            $transaction->credit_value =  $sessionCredit->purchaseValue;
            $transaction->save();
            $userCreditDetailTable = Engine_Api::_()->getDbTable('details', 'sescredit');
            $userCreditDetailTable->update(array('total_credit' => new Zend_Db_Expr('total_credit - ' . $sessionCredit->credit_value)), array('owner_id =?' => $order->user_id));
          }
          // send notification
          /* if( $transaction->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
            }
           */
          return 'active';
        } else if ($paymentStatus == 'pending') {
          // Enable later
          //$transaction->gateway_id = $this->_gatewayInfo->gateway_id;
          // $transaction->gateway_profile_id = $rdata['PROFILEID'];
          $item->onPaymentPending();
          // send notification
          /* if( $transaction->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
            }
           */
          return 'pending';
        } else {
          // Cancel order and subscription?
          $order->onFailure();
          $item->onPaymentFailure();
          // This is a sanity error and cannot produce information a user could use
          // to correct the problem.
          throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
        }
      }
    }
  }
  public function onSubscriptionTransactionIpn(
      Payment_Model_Order $order,
      Engine_Payment_Ipn $ipn)
  {
  }
  public function onSubscriptionTransactionReturn(Payment_Model_Order $order,array $params = array()){}
  public function cancelSubscription($transactionId, $note = null)
  {
    $paytmParams = array();
    $paytmParams['body'] = array(
        "mid"			=> $this->_gatewayInfo->config['paytm_marchant_id'],
        "subsId"		=> $transactionId,
    );
    $checksum = getChecksumFromString(json_encode($paytmParams['body'], JSON_UNESCAPED_SLASHES), $this->_gatewayInfo->config['paytm_secret_key']);
    $paytmParams["head"] = array(
        "version" => "V1",
        "requestTimestamp" => time(),
        "tokenType" => "AES",
        "signature" => $checksum
    );
    if($this->_gatewayInfo->test_mode){
      $url = 'https://securegw-stage.paytm.in/subscription/cancel'; // for staging
    } else {
      $url = 'https://securegw.paytm.in/subscription/cancel'; // for production
    }
    $post_fields = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    $response = curl_exec($ch);
    return $this;
  }
  

  /**
   * Generate href to a page detailing the order
   *
   * @param string $transactionId
   * @return string
   */
  public function getOrderDetailLink($orderId)
  {
    if( $this->getGateway()->getTestMode() ) {
      // Note: it doesn't work in test mode
      return 'https://dashboard.paytm.com/next/transactions';
    } else {
      return 'https://dashboard.paytm.com/next/transactions';
    }
  }

  public function getTransactionDetailLink($transactionId)
  {
    if( $this->getGateway()->getTestMode() ) {
      // Note: it doesn't work in test mode
      return 'https://dashboard.paytm.com/next/transactions';
    } else {
      return 'https://dashboard.paytm.com/next/transactions';
    }
  }

  public function getOrderDetails($orderId)
  {
    try {
      return $this->getService()->detailRecurringPaymentsProfile($orderId);
    } catch( Exception $e ) {
      echo $e;
    }

    try {
      return $this->getTransactionDetails($orderId);
    } catch( Exception $e ) {
      echo $e;
    }

    return false;
  }

  public function getTransactionDetails($transactionId)
  {
    return $this->getService()->detailTransaction($transactionId);
  }
  public function createOrderTransactionReturn($order,$transaction) {  
    $user = $order->getUser();
    return 'active';
  }
  function getSupportedCurrencies(){ 
      return array('INR'=>'INR');
 }
  public function getAdminGatewayForm(){
    return new Epaytm_Form_Admin_Settings_Paytm();
  }

  public function processAdminGatewayForm(array $values){
    return $values;
  }
  public function getGatewayUrl(){
  }
  function getSupportedBillingCycles(){ 
    return array(0=>'Day',2=>'Month',3=>'Year');
  }

  // IPN

  /**
   * Process an IPN
   *
   * @param Engine_Payment_Ipn $ipn
   * @return Engine_Payment_Plugin_Abstract
   */
   public function onIpn(Engine_Payment_Ipn $ipn)
  {
  }
    public function cancelResourcePackage($transactionId, $note = null) {


    }
    public function cancelSubscriptionOnExpiry($source, $package) {
      $paytmParams = array();
      $paytmParams['body'] = array(
          "mid"			=> $this->_gatewayInfo->config['paytm_secret_key'],
          "subsId"		=> $transactionId,
      );
      $checksum = getChecksumFromString(json_encode($paytmParams['body'], JSON_UNESCAPED_SLASHES), $this->_gatewayInfo->config['paytm_secret_key']);
      $paytmParams["head"] = array(
          "version" => "V1",
          "requestTimestamp" => time(),
          "tokenType" => "AES",
          "signature" => $checksum
      );
      if($this->_gatewayInfo->test_mode){
        $url = 'https://securegw-stage.paytm.in/subscription/cancel'; // for staging
      } else {
        $url = 'https://securegw.paytm.in/subscription/cancel'; // for production
      }
      $post_fields = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      $response = curl_exec($ch);
    }
    public function onIpnTransaction($subscription){
      $package = $subscription->getPackage();
      $paytmParams = array();
      $paytmParams['body'] = array(
        "mid"	        => $this->_gatewayInfo->config['paytm_marchant_id'],
        "orderId"	        => "ORDER_".time(),
        "subscriptionId"	=> $subscription->gateway_profile_id,
        "txnAmount"		=> 
          array(
            "value"     => $package->price,
            "currency"	=> "INR"
          )
      );
      $checksum = getChecksumFromString(json_encode($paytmParams['body'], JSON_UNESCAPED_SLASHES), $this->_gatewayInfo->config['paytm_secret_key']);
      $paytmParams["head"] = array(
          "clientId" 			=> $subscription->user_id,
          "version"			=> "v1",
          "requestTimestamp"   => time(),
          "signature"			=> $checksum
      );
      if($this->_gatewayInfo->test_mode){
        $url = 'https://securegw-stage.paytm.in/'; // for staging
      } else {
        $url = 'https://securegw.paytm.in/'; // for production
      }
      $url = $url."subscription/renew?mid=".$this->_gatewayInfo->config['paytm_marchant_id']."&orderId=".$paytmParams["body"]["orderId"];
      $post_fields = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      $response = curl_exec($ch);
      $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage');
      $order = null;
      $transaction = null;
      $transactionData = array(
            'gateway_id' => $this->_gatewayInfo->gateway_id,
      );
      // Transaction IPN - get order by subscription_id
      if (!$order && !empty($response['body'])) {
          $gateway_order_id = $response['body']['txnId'];
          $order = Engine_Api::_()->getItem('payment_order', $subscription->order_id);
          $transactionData['gateway_transaction_id'] = $gateway_order_id;
      }
      if (!empty($response['head'])) {
          $transactionData['creation_date'] = date('Y-m-d H:i:s', strtotime($response['head']['responseTimestamp']));
          $transactionData['total_amount'] = $package->price;
          $transactionData['currency_symbol'] = "INR";
          switch ($response['body']['resultInfo']['resultStatus']) {
            case 'S':
              $transactionData['type'] = 'payment';
              $transactionData['state'] = 'active';
              break;
            default:
              $transactionData['type'] = 'payment';
              $transactionData['state'] = 'failed';
              break;
          }
      } else {
        $transactionData['creation_date'] = new Zend_Db_Expr('NOW()');
      }
      if ($order) {
        $transactionData['owner_id'] = $order->user_id;
        $transactionData['order_id'] = $order->order_id;
      }
      $transactionsTable->insert($transactionData);
      if ($order) {
        $ipnProcessed = false;
        // Subscription IPN
        if ($order->source_type == 'sesblog_blog') {
          $this->onBlogTransactionIpn($order, $response);
          $ipnProcessed = true;
        }
        // Unknown IPN - could not be processed
        if (!$ipnProcessed) {
          throw new Engine_Payment_Plugin_Exception('Unknown order type for IPN');
        }
      }
  }
  public function onBlogTransactionIpn(Payment_Model_Order $order,  $rawData) { 
  // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }
    // Get related info	
    $user = $order->getUser();
    $item = $order->getSource();
    $package = $item->getPackage();
    $transaction = $item->getTransaction();
    // Get tx table
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage');
    switch ($rawData['body']['resultInfo']['resultStatus']) {
        case 'S':
          // Get benefit setting
          $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')->getBenefitStatus($user);
          if ($giveBenefit) {
            $item->onPaymentSuccess();
          } else {
            $item->onPaymentPending();
          }
          break;
        case 'S': // Not sure about this one
          $item->onPaymentSuccess();
          // send notification
          /* if( $item->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
            } */
          break;
        case 'F':
          $item->onPaymentFailure();
          // send notification
          /* if( $item->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_overdue', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
            } */
          break;
        default:
          throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                  'payment status %1$s', $rawData['body']['resultInfo']['resultMsg']));
          break;
    }
    return $this;
  }
  function setConfig(){}
  function test(){}

}
