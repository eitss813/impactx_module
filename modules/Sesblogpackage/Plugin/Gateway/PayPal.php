<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: PayPal.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblogpackage_Plugin_Gateway_PayPal extends Engine_Payment_Plugin_Abstract {

  protected $_gatewayInfo;
  protected $_gateway;

  // General

  /**
   * Constructor
   */
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
    $this->_gatewayInfo = $gatewayInfo;

    // @todo
    // https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-recurring-payments&encrypted_profile_id=
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
    if (null === $this->_gateway) {
      $class = 'Engine_Payment_Gateway_PayPal';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
          'config' => (array) $this->_gatewayInfo->config,
          'testMode' => $this->_gatewayInfo->test_mode,
          'currency' => Engine_Api::_()->sesblogpackage()->getCurrentCurrency(),
      ));
      if (!($gateway instanceof Engine_Payment_Gateway)) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
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

  public function createBlogTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $item, Sesblogpackage_Model_Package $package, array $params = array()) {
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
 
    $currentCurrency = Engine_Api::_()->sesblogpackage()->getCurrentCurrency();
    $defaultCurrency = Engine_Api::_()->sesblogpackage()->defaultCurrency();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $currencyValue = 1;
    if ($currentCurrency != $defaultCurrency) {
      $currencyValue = $settings->getSetting('sesmultiplecurrency.' . $currentCurrency);
    }
    $price = @round(($params['amount'] * $currencyValue), 2);
    // This is a one-time fee
    if ($package->isOneTime()) {
      $params['driverSpecificParams']['PayPal'] = array(
          'AMT' => $price,
          'DESC' => $desc,
          'CUSTOM' => $item->getIdentity(),
          'INVNUM' => $params['vendor_order_id'],
          'ITEMAMT' => $price,
          'ITEMS' => array(
              array(
                  'NAME' => $package->title,
                  'DESC' => $desc,
                  'AMT' => $price,
                  'NUMBER' => $item->getIdentity(),
                  'QTY' => 1,
              ),
          )
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
  public function onSubscriptionTransactionReturn(
  Payment_Model_Order $order, array $params = array()) {
    
  }

  public function onBlogTransactionReturn(Payment_Model_Order $order, array $params = array()) {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();
    $item = $order->getSource();
    $package = $item->getPackage();
    
    $transaction = $item->getTransaction();
    // Check subscription state
    if ($transaction && ($transaction->state == 'trial')) {
      return 'active';
    } else if ($transaction && $transaction->state == 'pending') {
      return 'pending';
    }

    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      // Cancel order and item
      $order->onCancel();
      $item->onPaymentFailure();
      // Error
      throw new Payment_Model_Exception('Your payment has been cancelled and ' .
      'not been charged. If this is not correct, please try again later.');
    }

    // Check params
    if (empty($params['token'])) {
      // Cancel order and subscription?
      $order->onFailure();
      $item->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
      'transaction. Please try again later.');
    }

    // Get details
    try {
      $data = $this->getService()->detailExpressCheckout($params['token']);
    } catch (Exception $e) {
      // Cancel order and subscription?
      $order->onFailure();
      $item->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
      'transaction. Please try again later.');
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
        // Log the error
        $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
                . $e->__toString(), Zend_Log::ERR);

        // Cancel order and subscription?
        $order->onFailure();
        $item->onPaymentFailure();
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
          $paymentStatus = 'active';
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

      $orderPackageId = $item->existing_package_order ? $item->existing_package_order : false;
      $orderPackage = Engine_Api::_()->getItem('sesblogpackage_orderspackage', $orderPackageId);
      if (!$orderPackageId && !$orderPackage) {
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
          'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
          'creation_date' => new Zend_Db_Expr('NOW()'),
          'modified_date' => new Zend_Db_Expr('NOW()'),
          'order_id' => $order->order_id,
          'orderspackage_id' => $orderPackageId,
          'state' => 'initial',
          'total_amount' => $params['amount'],
          'change_rate' => $rate,
          'gateway_type' => 'Paypal',
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
        $transaction->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
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
        $transaction->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
        $transaction->save();
        // Payment pending
        $item->onPaymentPending();
        // send notification
        /* if( $subscription->didStatusChange() ) {
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
    }

    // Recurring
    else {
      // Check for errors
      if (empty($data)) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
      } else if (empty($data['BILLINGAGREEMENTACCEPTEDSTATUS']) ||
              '0' == $data['BILLINGAGREEMENTACCEPTEDSTATUS']) {
        // Cancel order and subscription?
        $order->onCancel();
        $item->onPaymentFailure();
        // Error
        throw new Payment_Model_Exception('Your payment has been cancelled and ' .
        'not been charged. If this in not correct, please try again later.');
      } else if (!isset($data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) ||
              '0' != $data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) {
        // Cancel order and subscription?
        $order->onFailure();
        $item->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
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
          'gateway_transaction_id' => '',
          'orderspackage_id' => $orderPackageId,
          'creation_date' => new Zend_Db_Expr('NOW()'),
          'modified_date' => new Zend_Db_Expr('NOW()'),
          'order_id' => $order->order_id,
          'state' => 'initial',
          'total_amount' => $params['amount'],
          'change_rate' => $rate,
          'gateway_type' => 'Paypal',
          'currency_symbol' => $currency,
          'ip_address' => $_SERVER['REMOTE_ADDR'],
      ));
      $transaction_id = $transactionsTable->getAdapter()->lastInsertId();
      $item->transaction_id = $transaction_id;
      
      //Delete Extra Entry
      Engine_Api::_()->getDbtable('orderspackages', 'sesblogpackage')->delete(array('orderspackage_id =?' => $item->orderspackage_id));
      
      $item->orderspackage_id = $orderPackageId;
      $item->save();
      $transaction = Engine_Api::_()->getItem('sesblogpackage_transaction', $transaction_id);

      $rpData = array(
          'TOKEN' => $params['token'],
          'PROFILEREFERENCE' => $order->order_id,
          'PROFILESTARTDATE' => $data['TIMESTAMP'],
          'DESC' => $desc,
          'BILLINGPERIOD' => ucfirst($package->recurrence_type),
          'BILLINGFREQUENCY' => $package->recurrence,
          'INITAMT' => 0,
          'AMT' => $price,
          'CURRENCYCODE' => $currency,
      );

      $count = $package->getTotalBillingCycleCount();
      if ($count) {
        $rpData['TOTALBILLINGCYCLES'] = $count;
      }
      if (!$isExistsOrderPackageId) {
        // Create recurring payment profile
        try {
          $rdata = $this->getService()->createRecurringPaymentsProfile($rpData);
        } catch (Exception $e) {
          // Cancel order and subscription?
          $order->onFailure();
          $item->onPaymentFailure();
          // This is a sanity error and cannot produce information a user could use
          // to correct the problem.
          throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
        }

        // Let's log it
        $this->getGateway()->getLog()->log('CreateRecurringPaymentsProfile: '
                . print_r($rdata, true), Zend_Log::INFO);

        // Check returned profile id
        if (empty($rdata['PROFILEID'])) {
          // Cancel order and subscription?
          $order->onFailure();
          $item->onPaymentFailure();
          // This is a sanity error and cannot produce information a user could use
          // to correct the problem.
          throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
        }
        $profileId = $rdata['PROFILEID'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $profileId;
        $order->save();

        // Get benefit setting
        $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')
                ->getBenefitStatus($user);

        // Check profile status
        if ($rdata['PROFILESTATUS'] == 'ActiveProfile' ||
                ($rdata['PROFILESTATUS'] == 'PendingProfile' && $giveBenefit)) {
          // Enable now
          $transaction->gateway_id = $this->_gatewayInfo->gateway_id;
          $transaction->gateway_profile_id = $rdata['PROFILEID'];
          $transaction->save();
          $item->onPaymentSuccess();
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
          /* if( $subscription->didStatusChange() ) {
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
        } else if ($rdata['PROFILESTATUS'] == 'PendingProfile') {
          // Enable later
          //$subscription->gateway_id = $this->_gatewayInfo->gateway_id;
          // $subscription->gateway_profile_id = $rdata['PROFILEID'];
          $item->onPaymentPending();
          // send notification
          /* if( $subscription->didStatusChange() ) {
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

  /**
   * Process ipn of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onSubscriptionTransactionIpn(
  Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
    
  }

  public function onBlogTransactionIpn(
  Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info	
    $user = $order->getUser();
    $item = $order->getSource();
    $package = $item->getPackage();
    $transaction = $item->getTransaction();
    // Get IPN data
    $rawData = $ipn->getRawData();
    // Let's log it
    // Get tx table
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage');


    // Chargeback --------------------------------------------------------------
    if (!empty($rawData['case_type']) && $rawData['case_type'] == 'chargeback') {
      $item->onPaymentFailure(); // or should we use pending?
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
                // @todo this might be redundant
                // Get benefit setting
                $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')->getBenefitStatus($user);
                if ($giveBenefit) {
                  $item->onPaymentSuccess();
                } else {
                  $item->onPaymentPending();
                }
                break;

              case 'Completed':
              case 'Processed':
              case 'Canceled_Reversal': // Not sure about this one
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

              case 'Denied':
              case 'Failed':
              case 'Voided':
              case 'Reversed':
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

              case 'Refunded':
                $item->onRefund();
                // send notification
                /* if( $item->didStatusChange() ) {
                  Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_refunded', array(
                  'subscription_title' => $package->title,
                  'subscription_description' => $package->description,
                  'subscription_terms' => $package->getPackageDescription(),
                  'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                  ));
                  } */
                break;

              case 'Expired': // Not sure about this one
                $item->onExpiration();
                // send notification
                /* if( $item->didStatusChange() ) {
                  Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_expired', array(
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
                        'payment status %1$s', $rawData['payment_status']));
                break;
            }
          }
          break;

        // Recurring payment was received
        case 'recurring_payment':
          if (!$package->isOneTime()) {
            $item->onPaymentSuccess();
            // send notification
            /* if( $item->didStatusChange() ) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_recurrence', array(
              'subscription_title' => $package->title,
              'subscription_description' => $package->description,
              'subscription_terms' => $package->getPackageDescription(),
              'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
              } */
          }
          break;

        // Profile was created
        case 'recurring_payment_profile_created':
          if ((!empty($rawData['initial_payment_status']) &&
                  $rawData['initial_payment_status'] == 'Completed') ||
                  (!empty($rawData['profile_status']) &&
                  $rawData['profile_status'] == 'Active')) {
            //$subscription->active = true;
            $item->onPaymentSuccess();
            // @todo add transaction row for the initial amount?
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
          } else {
            throw new Engine_Payment_Plugin_Exception(sprintf('Unknown or missing ' .
                    'initial_payment_status %1$s', @$rawData['initial_payment_status']));
          }
          break;

        // Profile was cancelled
        case 'recurring_payment_profile_cancel':
          $item->onCancel();
          // send notification
          /* if( $item->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_cancelled', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
            } */
          break;

        // Recurring payment expired
        case 'recurring_payment_expired':
          if (!$package->isOneTime()) {
            $item->onExpiration();
            // send notification
            /* if( $item->didStatusChange() ) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_expired', array(
              'subscription_title' => $package->title,
              'subscription_description' => $package->description,
              'subscription_terms' => $package->getPackageDescription(),
              'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
              } */
          }
          break;

        // Recurring payment failed
        case 'recurring_payment_skipped':
        case 'recurring_payment_suspended_due_to_max_failed_payment':
        case 'recurring_payment_outstanding_payment_failed':
        case 'recurring_payment_outstanding_payment':
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
        case 'recurring_payment_suspended':
          $item->onPaymentFailure();
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
          // Get benefit setting
          $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')->getBenefitStatus($user);
          if ($giveBenefit) {
            $item->onPaymentSuccess();
          } else {
            $item->onPaymentPending();
          }
          break;

        case 'Completed':
        case 'Processed':
        case 'Canceled_Reversal': // Not sure about this one
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

        case 'Denied':
        case 'Failed':
        case 'Voided':
        case 'Reversed':
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

        case 'Refunded':
          $item->onRefund();
          // send notification
          /* if( $item->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_refunded', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
            } */
          break;

        case 'Expired': // Not sure about this one
          $item->onExpiration();
          // send notification
          /* if( $item->didStatusChange() ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_expired', array(
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

  public function cancelBlog($transactionId, $note = null) {
    $profileId = null;
    if ($transactionId instanceof Sesblogpackage_Model_Transaction) {
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
   * Generate href to a page detailing the order
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
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sesblogpackage');


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

      if (!$transaction && !empty($rawData['recurring_payment_id'])) {
        $transaction = $transactionsTable->fetchRow(array(
            'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
            'gateway_profile_id = ?' => $rawData['recurring_payment_id'],
        ));
      }
      $parentTransaction = $transactionsTable->fetchRow(array(
          'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
          'gateway_parent_transaction_id = ?' => $rawData['txn_id'],
      ));
    }
    // Fetch by parent_txn_id
    if (!empty($rawData['parent_txn_id'])) {
      if (!$transaction) {
        $parentTransaction = $transactionsTable->fetchRow(array(
            'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
            'gateway_parent_transaction_id = ?' => $rawData['parent_txn_id'],
        ));
      }
      if (!$parentTransaction) {
        $parentTransaction = $transactionsTable->fetchRow(array(
            'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
            'gateway_transaction_id = ?' => $rawData['parent_txn_id'],
        ));
      }
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
        $transactionData['creation_date'] = date('Y-m-d H:i:s', strtotime($rawData['payment_date']));
      } else {
        $transactionData['creation_date'] = new Zend_Db_Expr('NOW()');
      }
      // Get amount
      if (!empty($rawData['mc_gross'])) {
        $transactionData['total_amount'] = $rawData['mc_gross'];
      }
      // Get currency
      if (!empty($rawData['mc_currency'])) {
        $transactionData['currency_symbol'] = $rawData['mc_currency'];
      }
      // Get order/user
      if ($order) {
        $transactionData['owner_id'] = $order->user_id;
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
          $transactionData['state'] = 'active';
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
        //$transaction->setFromArray($transactionData);
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
      $ipnProcessed = false;
      // Subscription IPN
      if ($order->source_type == 'sesblog_blog') {
        $this->onBlogTransactionIpn($order, $ipn);
        $ipnProcessed = true;
      }
      // Unknown IPN - could not be processed
      if (!$ipnProcessed) {
        throw new Engine_Payment_Plugin_Exception('Unknown order type for IPN');
      }
    }
    // Missing order
    else {
      throw new Engine_Payment_Plugin_Exception('Unknown or unsupported IPN ' .
      'type, or missing transaction or order ID');
    }

    return $this;
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
