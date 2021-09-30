<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvpmnt
 * @package    Sesadvpmnt
 * @copyright  Copyright 2019-2020 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Stripe.php  2019-04-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
include_once APPLICATION_PATH . "/application/modules/Sesadvpmnt/Api/Stripe/init.php";
class Sesadvpmnt_Plugin_Gateway_Stripe extends Engine_Payment_Plugin_Abstract
{
  protected $_gatewayInfo;
  protected $_gateway;
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo)
  {
      $this->_gatewayInfo = $gatewayInfo;
      \Stripe\Stripe::setApiKey($this->_gatewayInfo->config['sesadvpmnt_stripe_secret']);
  }

  public function getService()
  {
    return $this->getGateway()->getService();
  }

  public function getGateway()
  {
    if( null === $this->_gateway ) {
        $class = 'Sesadvpmnt_Gateways_Stripe';
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

  public function createSubscriptionTransaction(User_Model_User $user,
      Zend_Db_Table_Row_Abstract $subscription,
      Payment_Model_Package $package,
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
    
    \Stripe\Stripe::setApiKey($this->_gatewayInfo->config['sesadvpmnt_stripe_secret']);
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $currency = $settings->getSetting('payment.currency', 'USD');
    $logo = !empty($this->_gatewayInfo->config['sesadvpmnt_stripe_logo']) ? $this->_gatewayInfo->config['sesadvpmnt_stripe_logo'] : "https://";	
    $logo = 'http://' . $_SERVER['HTTP_HOST'].$logo;
    if($package->isOneTime()) {
      return \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
          'name' => $package->title." ",
          'description' => $package->description." ",
          'images' => [$logo],
          'amount_decimal' => $params['amount']*100,
          'currency' => $currency,
          'quantity' => 1,
        ]],
        'metadata'=>['gateway_id'=>$order->gateway_id,'order_id'=>$order->order_id],
        'success_url' => $params['return_url'].'&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $params['cancel_url'].'&session_id={CHECKOUT_SESSION_ID}',
      ]);
    } else  { 
      try{
        $stripePlan = \Stripe\Plan::retrieve("package_".$package->package_id);
      } catch(Exception $e){
        $stripePlan = 0;
      }
      try{
        if(!$stripePlan) {
          $stripePlan = \Stripe\Plan::create(array(
              "id"=>"package_".$package->package_id,
              "amount_decimal" => $params['amount']*100,
              "interval" => $package->recurrence_type,
              "interval_count" => $package->recurrence,
              "currency" => $currency,
              "product" => [
                  "name" => $package->title,
                  "type" => "service"
              ],
              'metadata'=>['gateway_id'=>$order->gateway_id,'package_id'=>$package->package_id]
          ));
        }
        return \Stripe\Checkout\Session::create([
          'payment_method_types' => ['card'],
          'subscription_data' => [
            'items' => [[
              'plan' => $stripePlan->id,
            ]],
            'metadata'=> ['order_id'=>$params['order_id'],'type'=>$params['type'],'gateway'=>$this->_gatewayInfo->getIdentity()],
          ],
          'success_url' => $params['return_url'].'&session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' => $params['cancel_url'].'&session_id={CHECKOUT_SESSION_ID}',
        ]);
      } catch(Exception $e){
        throw $e;
      }
    }
  }
  public function onSubscriptionReturn(
      Payment_Model_Order $order,$params)
  {
    if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();
    $subscription = $order->getSource();
    $package = $subscription->getPackage();

    $session = \Stripe\Checkout\Session::retrieve($params['session_id']);
    // Check subscription state
    $paymentStatus = 'okay';
    $orderStatus = 'complete';

    // Check subscription state
    if( $subscription->status == 'active' ||
        $subscription->status == 'trial') {
      return 'active';
    } else if( $subscription->status == 'pending' ) {
      return 'pending';
    }

    // One-time
    if($package->isOneTime()) {
      $transaction = \Stripe\PaymentIntent::retrieve($session['payment_intent']);
      // Update order with profile info and complete status?
      $order->state = $orderStatus;
      $order->gateway_transaction_id = $transaction->id;
      $order->save();

      // Insert transaction
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
      $transactionsTable->insert(array(
        'user_id' => $order->user_id,
        'gateway_id' => $this->_gatewayInfo->gateway_id,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'order_id' => $order->order_id,
        'type' => 'payment',
        'state' => $paymentStatus,
        'gateway_transaction_id' => $transaction->id,
        'amount' => $transaction->amount/100, // @todo use this or gross (-fee)?
        'currency' => strtoupper($transaction->currency),
      ));
      $transaction_id = $transactionsTable->getAdapter()->lastInsertId();
      // Get benefit setting
      $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'payment')
          ->getBenefitStatus($user);
      // Check payment status
      if( $paymentStatus == 'okay' ||
          ($paymentStatus == 'pending' && $giveBenefit) ) {
     
        // Update subscription info
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $transaction->id;
        // Payment success
        $subscription->onPaymentSuccess();
        $paymentTransaction = Engine_Api::_()->getItem('payment_transaction', $transaction_id);
        //For Coupon
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ecoupon')){
          $couponSessionCode = $package->getType().'-'.$package->package_id.'-'.$subscription->getType().'-'.$subscription->subscription_id.'-1';
         Engine_Api::_()->ecoupon()->setAppliedCouponDetails($couponSessionCode);
          $paymentTransaction->save();
        }
        //For Credit 
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sescredit')) {
           $creditCode =  'credit'.'-payment-'.$package->package_id.'-'.$subscription->subscription_id;
          $sessionCredit = new Zend_Session_Namespace($creditCode);
          if(isset($sessionCredit->credit_value)){
            $paymentTransaction->credit_point = $sessionCredit->credit_value;  
            $paymentTransaction->credit_value =  $sessionCredit->purchaseValue;
            $paymentTransaction->save();
            $userCreditDetailTable = Engine_Api::_()->getDbTable('details', 'sescredit');
            $userCreditDetailTable->update(array('total_credit' => new Zend_Db_Expr('total_credit - ' . $sessionCredit->credit_value)), array('owner_id =?' => $order->user_id));
          }
        }
        unset($paymentTransaction);
        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }
        return 'active';
      }
      else if( $paymentStatus == 'pending' ) {

        // Update subscription info
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $transaction->id;
        // Payment pending
        $subscription->onPaymentPending();
        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }

        return 'pending';
      }
      else if( $paymentStatus == 'failed' ) {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // Payment failed
        throw new Payment_Model_Exception('Your payment could not be ' .
            'completed. Please ensure there are sufficient available funds ' .
            'in your account.');
      }
      else {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }
    } // For Recurring Payment
    else {
      $transaction = \Stripe\Subscription::retrieve($session['subscription']);
      // Create recurring payments profile
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
      $order->state = 'complete';
      $order->gateway_order_id = $transaction->id;
      $order->gateway_transaction_id = $transaction->id;
      $order->save();

      // Get benefit setting
      $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'payment')
          ->getBenefitStatus($user);

      // Check profile status
      if($paymentStatus == 'okay' ||
          ($paymentStatus == 'pending' && $giveBenefit)) {
        // Enable now
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $transaction->id;
        $subscription->onPaymentSuccess();
        // send notification
        if( $subscription->didStatusChange()) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }

        return 'active';

      } else if($paymentStatus == 'pending') {
        // Enable later
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $transaction->id;
        $subscription->onPaymentPending();
        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }
        return 'pending';

      } else {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }
    }
  }
  public function onSubscriptionTransactionReturn(Payment_Model_Order $order,array $params = array()){}
  public function onSubscriptionTransactionIpn(Payment_Model_Order $order,Engine_Payment_Ipn $ipn){}
  public function cancelSubscription($transactionId, $note = null)
  {
    $profileId = null;
    if( $transactionId instanceof Payment_Model_Subscription ) {
      $package = $transactionId->getPackage();
      if( $package->isOneTime() ) {
        return $this;
      }
      $profileId = $transactionId->gateway_profile_id;
    }
    else if(is_string($transactionId) ) {
      $profileId = $transactionId;
    }else {
      // Should we throw?
      return $this;
    }
    $this->view->secretKey = $secretKey = $this->_gatewayInfo->config['sesadvpmnt_stripe_secret'];
    \Stripe\Stripe::setApiKey($secretKey);
    $sub = \Stripe\Subscription::retrieve($profileId);
    $cancel = $sub->cancel();
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
      return 'https://dashboard.stripe.com/test/search?query' . $orderId;
    } else {
      return 'https://dashboard.stripe.com/search?query' . $orderId;
    }
  }

  public function getTransactionDetailLink($transactionId)
  {
    if( $this->getGateway()->getTestMode() ) {
      // Note: it doesn't work in test mode
      return 'https://dashboard.stripe.com/test/search?query' . $transactionId;
    } else {
      return 'https://dashboard.stripe.com/search?query' . $transactionId;
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

  public function createOrderTransaction($params = array()) {
    $currencyValue = $params['change_rate'] ? $params['change_rate'] : 1;
    return \Stripe\Checkout\Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [[
        'name' => $this->_gatewayInfo->config['sesadvpmnt_stripe_title']." ",
        'description' => $this->_gatewayInfo->config['sesadvpmnt_stripe_description']." ",
        'images' => ['https://example.com/t-shirt.png'],
        'amount' => $params['amount']*100,
        'currency' => $params['currency'],
        'quantity' => 1,
      ]],
      'metadata'=>['order_id'=>$params['order_id'],'type'=>$params['type'],'change_rate'=>$currencyValue],
      'success_url' => $params['return_url'].'&session_id={CHECKOUT_SESSION_ID}',
      'cancel_url' => $params['cancel_url'].'&session_id={CHECKOUT_SESSION_ID}',
    ]);
  }
  public function createOrderTransactionReturn($order,$transaction) {
    $user = $order->getUser();
    $viewer = Engine_Api::_()->user()->getViewer();
    $orderPayment = $order->getSource();
    $paymentOrder = $order;

    return 'active';
  }
  function getSupportedCurrencies(){
      return array('USD'=>'USD','AED'=>'AED','AFN'=>'AFN','ALL'=>'ALL','AMD'=>'AMD','ANG'=>'ANG','AOA'=>'AOA','ARS'=>'ARS','AUD'=>'AUD'
      ,'AWG'=>'AWG','AZN','BAM'=>'BAM','BBD'=>'BBD','BDT'=>'BDT','BGN'=>'BGN','BIF'=>'BIF','BMD'=>'BMD','BND'=>'BND','BOB'=>'BOB','BRL'=>'BRL',
      'BSD'=>'BSD','BWP'=>'BWP','BZD'=>'BZD','CAD'=>'CAD','CDF'=>'CDF','CHF'=>'CHF','CLP'=>'CLP','CNY'=>'CNY','COP'=>'COP','CRC'=>'CRC','CVE'=>'CVE',
      'CZK'=>'CZK','DJF'=>'DJF','DKK'=>'DKK','DOP'=>'DOP','DZD'=>'DZD','EGP'=>'EGP','ETB'=>'ETB','EUR'=>'EUR','FJD'=>'FJD','FKP'=>'FKP','GBP'=>'GBP',
      'GEL'=>'GEL','GIP'=>'GIP','GMD'=>'GMD','GNF'=>'GNF','GTQ'=>'GTQ','GYD'=>'GYD','HKD'=>'HKD','HNL'=>'HNL','HRK'=>'HRK','HTG'=>'HTG','HUF'=>'HUF',
      'IDR'=>'IDR','ILS'=>'ILS','INR'=>'INR','ISK'=>'ISK','JMD'=>'JMD','JPY'=>'JPY','KES'=>'KES','KGS'=>'KGS','KHR'=>'KHR','KMF'=>'KMF','KRW'=>'KRW',
      'KYD'=>'KYD','KZT'=>'KZT','LAK'=>'LAK','LBP'=>'LBP','LKR'=>'LKR','LRD'=>'LRD','LSL'=>'LSL','MAD'=>'MAD','MDL'=>'MDL','MGA'=>'MGA','MKD','MMK'=>'MMK',
      'MNT'=>'MNT','MOP'=>'MOP','MRO'=>'MRO','MUR'=>'MUR','MVR'=>'MVR','MWK'=>'MWK','MXN'=>'MXN','MYR'=>'MYR','MZN'=>'MZN','NAD'=>'NAD','NGN'=>'NGN','NIO'=>'NIO',
      'NOK'=>'NOK','NPR'=>'NPR','NZD'=>'NZD','PAB'=>'PAB','PEN'=>'PEN','PGK'=>'PGK','PHP'=>'PHP','PKR'=>'PKR','PLN'=>'PLN','PYG'=>'PYG','QAR'=>'QAR','RON'=>'RON',
      'RSD'=>'RSD','RUB'=>'RUB','RWF'=>'RWF','SAR'=>'SAR','SBD'=>'SBD','SCR'=>'SCR','SEK'=>'SEK','SGD'=>'SGD','SHP'=>'SHP','SLL'=>'SLL','SOS'=>'SOS','SRD'=>'SRD',
      'STD'=>'STD','SZL'=>'SZL','THB'=>'THB','TJS'=>'TJS','TOP'=>'TOP','TRY'=>'TRY','TTD'=>'TTD','TWD'=>'TWD','TZS'=>'TZS','UAH'=>'UAH','UGX'=>'UGX','UYU'=>'UYU','UZS'=>'UZS','VND'=>'VND','VUV'=>'VUV','WST'=>'WST','XAF'=>'XAF','XCD'=>'XCD','XOF'=>'XOF','XPF'=>'XPF','YER'=>'YER','ZAR'=>'ZAR','ZMW'=>'ZMW');
 }
  public function getAdminGatewayForm(){
    return new Sesadvpmnt_Form_Admin_Settings_Stripe();
  }

  public function processAdminGatewayForm(array $values){
    return $values;
  }
  public function getGatewayUrl(){
  }
  function getSupportedBillingCycles(){
    return array(0=>'Day',1=>'Week',2=>'Month',3=>'Year');
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
      $this->view->secretKey = $secretKey = $this->_gatewayInfo->config['sesadvpmnt_stripe_secret'];
        if($package->duration_type != "forever"){
              $durationTime = (($package->duration > 1 || $package->duration == 0) ? ("+".$package->duration." ".$package->duration_type."s") : ("+".$package->duration." ".$package->duration_type));
                $subscriptionDate = strtotime($source->creation_date);
              $date = date($subscriptionDate,strtotime($durationTime));
        if(strtotime("now") >= $date ) {
          \Stripe\Stripe::setApiKey($secretKey);
          $sub = \Stripe\Subscription::retrieve($source->gateway_profile_id);
          $sub->cancel();
          echo "Subscription canceled";
        }
      }
      echo "Subscription Continue";
  }
  public function onIpnTransaction($rawData){}
  public function onTransactionIpn(Payment_Model_Order $order,  $rawData) {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
        throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }
    // Get related info
    $user = $order->getUser();
    $source = $order->getSource();
    $package = $source->getPackage();
    $moduleName = explode("_", $package->getType());
    $moduleName = $moduleName['0'];
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
        case 'charge.failed':return false; break;
        case 'charge.refunded':
            // Payment Refunded
            $source->onRefund();
            // send notification
            if ($source->didStatusChange()) {
                if ($moduleName == 'payment') {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_refunded', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("REFUNDED", $source->getIdentity());
                }
            }
            return true;
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
        case 'customer.subscription.created': return false; break;
        case 'customer.subscription.deleted':
            $source->onCancel();
            // send notification
            if ($source->didStatusChange()) {
                if ($moduleName == 'payment') {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_cancelled', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("CANCELLED", $source->getIdentity());
                }
            }
             return true;
            break;

        case 'customer.subscription.trial_will_end':return false; break;
        case 'customer.subscription.updated':
            $source->onPaymentSuccess();
            if ($source->didStatusChange()) {
                if ($moduleName == 'payment') {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("RECURRENCE", $source->getIdentity());
                }
            }
            $this->cancelSubscriptionOnExpiry($source, $package);
             return true;
            break;
        case 'invoice.created':break;
        case 'invoice.payment_failed':
          $source->onPaymentFailure();
          if ($moduleName == 'payment') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
            $transactionsTable->insert(array(
                'user_id' => $order->user_id,
                'gateway_id' => $this->_gatewayInfo->gateway_id,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'order_id' => $order->order_id,
                'type' => 'payment',
                'state' => 'failed',
                'gateway_transaction_id' => $rawData['data']['object']['charge'],
                'amount' => $rawData['data']['object']['amount_paid']/100, 
                'currency' => strtoupper($rawData['data']['object']['currency']),
            ));
            try {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_overdue', array(
                  'subscription_title' => $package->title,
                  'subscription_description' => $package->description,
                  'subscription_terms' => $package->getPackageDescription(),
                  'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                      Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
            } catch (Exception $e) {}
          } else {
              Engine_Api::_()->$moduleName()->sendMail("OVERDUE", $source->getIdentity());
          }
          return true;
          break;
        case 'invoice.payment_succeeded':
            $source->onPaymentSuccess();
            if ($moduleName == 'payment') {
              $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
              $transactionsTable->insert(array(
                      'user_id' => $order->user_id,
                      'gateway_id' => $this->_gatewayInfo->gateway_id,
                      'timestamp' => new Zend_Db_Expr('NOW()'),
                      'order_id' => $order->order_id,
                      'type' => 'payment',
                      'state' => 'okay',
                      'gateway_transaction_id' => $rawData['data']['object']['charge'],
                      'amount' => $rawData['data']['object']['amount_paid']/100,
                      'currency' => strtoupper($rawData['data']['object']['currency']),
              ));
            } else {
                Engine_Api::_()->$moduleName()->sendMail("RECURRENCE", $source->getIdentity());
            }
            $this->cancelSubscriptionOnExpiry($source, $package);
            return true;
           break;
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
        case 'transfer.updated': return false; break;
        default:
          throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
              'type %1$s', $rawData['type']));
          break;
    }
    return $this;
  }
  function setConfig(){}
  function test(){}

}
