<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paypal.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Plugin_Gateway_PayPal extends Engine_Payment_Plugin_Abstract {

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

  public function createPageTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $page, Sitepage_Model_Package $package, array $params = array()) {
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
    // This is a one-time fee
    if ($package->isOneTime()) {
      $params['driverSpecificParams']['PayPal'] = array(
          'AMT' => $package->price,
          'DESC' => $desc,
          'CUSTOM' => $page->page_id,
          'INVNUM' => $params['vendor_order_id'],
          'ITEMAMT' => $package->price,
          'ITEMS' => array(
              array(
                  'NAME' => $package->title,
                  'DESC' => $package->getPackageDescription(),
                  'AMT' => $package->price,
                  'NUMBER' => $page->page_id,
                  'QTY' => 1,
              ),
          ),
          'SOLUTIONTYPE' => 'sole'
              //'BILLINGTYPE' => 'RecurringPayments',
              //'BILLINGAGREEMENTDESCRIPTION' => $package->getPackageDescription(),
      );
      // Should fix some issues with GiroPay
      if( !empty($params['return_url']) ) {
        $params['driverSpecificParams']['PayPal']['GIROPAYSUCCESSURL'] = $params['return_url']
          . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
        $params['driverSpecificParams']['PayPal']['BANKTXNPENDINGURL'] = $params['return_url']
          . ( false === strpos($params['return_url'], '?') ? '?' : '&' ) . 'giropay=1';
      }
      if( !empty($params['cancel_url']) ) {
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
  public function onPageTransactionReturn(
  Payment_Model_Order $order, array $params = array()) {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      $error_msg2 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
      throw new Engine_Payment_Plugin_Exception($error_msg2);
    }

    // Get related info
    $user = $order->getUser();
    $page = $order->getSource();
    $package = $page->getPackage();
    
    //Coupon pluign work for recuring payment use coupon code.
		if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon') && !empty($params['code'])) {
		
			//COUPON VALUE ACCRORDING TO CODE. CALLING FUNCTION.
			$result = Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->getCode($params['code']);

			$discount_value = 0;
			if ($result->discount_type == 'price') {
				$discount_value = $result->discount_value;
			} else if ($result->discount_type == 'percentage') {
				$discount_value = ($package->price ) * ($result->discount_value / 100);
			}
			$totalValue = $package->price - $discount_value;
			$package->price = $totalValue;
		}

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
            //SESSION SET OF CREDITS.     
            $creditSession = new Zend_Session_Namespace('credit_package_payment_'.$package->getType());

            if (!empty($creditSession->packagePaymentCreditDetail)) {
                $creditDetail = unserialize($creditSession->packagePaymentCreditDetail);
                if(!empty($creditDetail['credit_amount']))
                $package->price =$package->price-$creditDetail['credit_amount'];
            }
    }    
		
    // Check subscription state
    if (/* $page->status == 'active' || */
            $page->status == 'trial') {
      return 'active';
    } else if ($page->status == 'pending') {
      return Zend_Registry::get('Zend_Translate')->_('pending');
    }

    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      // Cancel order and subscription?
      $order->onCancel();
      $page->onPaymentFailure();
      // Error
      $error_msg3 = Zend_Registry::get('Zend_Translate')->_('Your payment has been cancelled and not been charged. If this is not correct, please try again later.');
      throw new Payment_Model_Exception($error_msg3);
    }

    // Check params
    if (empty($params['token'])) {
      // Cancel order and subscription?
      $order->onFailure();
      $page->onPaymentFailure();
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
      $page->onPaymentFailure();
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
        $page->onPaymentFailure();
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
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitepage');
        $transactionsParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => $this->_gatewayInfo->gateway_id,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
            'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
            'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE'],
        );
      $transactionsTable->insert($transactionsParams);
      
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $transactionParams = array_merge($transactionsParams, array('resource_type' => $order->source_type,'resource_id'=>$order->source_id));
            Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
        }                

      // Get benefit setting
      $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitepage')
              ->getBenefitStatus($user);

      // Check payment status
      if ($paymentStatus == 'okay' ||
              ($paymentStatus == 'pending' && $giveBenefit)) {

        // Update subscription info
        $page->gateway_id = $this->_gatewayInfo->gateway_id;
        $page->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

        // Payment success
        $page->onPaymentSuccess();

        // send notification
        if ($page->didStatusChange()) {
          Engine_Api::_()->sitepage()->sendMail("ACTIVE", $page->page_id);
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
                //SESSION SET OF CREDITS.
                $param['user_id']=$user->getIdentity();
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
                        }
                    }
                    $creditSession->packagePaymentCreditDetail = null;

        }        

        return 'active';
      } else if ($paymentStatus == 'pending') {

        // Update subscription info
        $page->gateway_id = $this->_gatewayInfo->gateway_id;
        $page->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

        // Payment pending
        $page->onPaymentPending();

        // send notification
        if ($page->didStatusChange()) {
          Engine_Api::_()->sitepage()->sendMail("PENDING", $page->page_id);
        }

        return 'pending';
      } else if ($paymentStatus == 'failed') {
        // Cancel order and subscription?
        $order->onFailure();
        $page->onPaymentFailure();
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
        $page->onPaymentFailure();
        // Error
        $error_msg10 = Zend_Registry::get('Zend_Translate')->_('Your payment has been cancelled and not been charged. If this in not correct, please try again later.');
        throw new Payment_Model_Exception($error_msg10);
      } else if (!isset($data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) ||
              '0' != $data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) {
        // Cancel order and subscription?
        $order->onFailure();
        $page->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        $error_msg11 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
        throw new Payment_Model_Exception($error_msg11);
      }

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
        $page->onPaymentFailure();
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
        $page->onPaymentFailure();
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

      // Get benefit setting
      $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitepage')
              ->getBenefitStatus($user);

      // Check profile status
      if ($rdata['PROFILESTATUS'] == 'ActiveProfile' ||
              ($rdata['PROFILESTATUS'] == 'PendingProfile' && $giveBenefit)) {
        // Enable now
        $page->gateway_id = $this->_gatewayInfo->gateway_id;
        $page->gateway_profile_id = $rdata['PROFILEID'];
        $page->onPaymentSuccess();

        // send notification
        if ($page->didStatusChange()) {
          Engine_Api::_()->sitepage()->sendMail("ACTIVE", $page->page_id);
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
                //SESSION SET OF CREDITS.
                $param['user_id']=$user->getIdentity();
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
                        }
                    }
                    $creditSession->packagePaymentCreditDetail = null;

        }
        return 'active';
      } else if ($rdata['PROFILESTATUS'] == 'PendingProfile') {
        // Enable later
        $page->gateway_id = $this->_gatewayInfo->gateway_id;
        $page->gateway_profile_id = $rdata['PROFILEID'];
        $page->onPaymentPending();

        // send notification
        if ($page->didStatusChange()) {
          Engine_Api::_()->sitepage()->sendMail("PENDING", $page->page_id);
        }

        return 'pending';
      } else {
        // Cancel order and subscription?
        $order->onFailure();
        $page->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        $error_msg14 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
        throw new Payment_Model_Exception($error_msg14);
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
    $page = $order->getSource();
    $package = $page->getPackage();

    // Get IPN data
    $rawData = $ipn->getRawData();

    // Get tx table
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitepage');


    // Chargeback --------------------------------------------------------------
    if (!empty($rawData['case_type']) && $rawData['case_type'] == 'chargeback') {
      $page->onPaymentFailure(); // or should we use pending?
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
                $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitepage')->getBenefitStatus($user);
                if ($giveBenefit) {
                  $page->onPaymentSuccess();
                } else {
                  $page->onPaymentPending();
                }
                break;

              case 'Completed':
              case 'Processed':
              case 'Canceled_Reversal': // Not sure about this one
                $page->onPaymentSuccess();
                // send notification
                if ($page->didStatusChange()) {
                  Engine_Api::_()->sitepage()->sendMail("ACTIVE", $page->page_id);
                }
                break;

              case 'Denied':
              case 'Failed':
              case 'Voided':
              case 'Reversed':
                $page->onPaymentFailure();
                // send notification
                if ($page->didStatusChange()) {
                  Engine_Api::_()->sitepage()->sendMail("OVERDUE", $page->page_id);
                }
                break;

              case 'Refunded':
                $page->onRefund();
                // send notification
                if ($page->didStatusChange()) {
                  Engine_Api::_()->sitepage()->sendMail("REFUNDED", $page->page_id);
                }
                break;

              case 'Expired': // Not sure about this one
                $page->onExpiration();
                // send notification
                if ($page->didStatusChange()) {
                  Engine_Api::_()->sitepage()->sendMail("EXPIRED", $page->page_id);
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
            $page->onPaymentSuccess();
            // send notification
            if ($page->didStatusChange()) {
              //  @todo sitepage_page_recurrence
              Engine_Api::_()->sitepage()->sendMail("RECURRENCE", $page->page_id);
            }
          }
          break;

        // Profile was created
        case 'recurring_payment_profile_created':
          if ($rawData['initial_payment_status'] == 'Completed') {
            //$subscription->active = true;
            $page->onPaymentSuccess();
            // @todo add transaction row for the initial amount?
            // send notification
            if ($page->didStatusChange()) {
              Engine_Api::_()->sitepage()->sendMail("ACTIVE", $page->page_id);
            }
          } else {
            throw new Engine_Payment_Plugin_Exception(sprintf('Unknown ' .
                            'initial_payment_status %1$s', $rawData['initial_payment_status']));
          }
          break;

        // Profile was cancelled
        case 'recurring_payment_profile_cancel':
          $page->onCancel();
          // send notification
          if ($page->didStatusChange()) {
            Engine_Api::_()->sitepage()->sendMail("CANCELLED", $page->page_id);
          }
          break;

        // Recurring payment expired
        case 'recurring_payment_expired':
          if (!$package->isOneTime()) {
            $page->onExpiration();
            // send notification
            if ($page->didStatusChange()) {
              Engine_Api::_()->sitepage()->sendMail("EXPIRED", $page->page_id);
            }
          }
          break;

        // Recurring payment failed
        case 'recurring_payment_skipped':
        case 'recurring_payment_suspended_due_to_max_failed_payment':
        case 'recurring_payment_outstanding_payment_failed':
        case 'recurring_payment_outstanding_payment':
          $page->onPaymentFailure();
          // send notification
          if ($page->didStatusChange()) {
            Engine_Api::_()->sitepage()->sendMail("OVERDUE", $page->page_id);
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
          // Get benefit setting
          $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitepage')->getBenefitStatus($user);
          if ($giveBenefit) {
            $page->onPaymentSuccess();
          } else {
            $page->onPaymentPending();
          }
          break;

        case 'Completed':
        case 'Processed':
        case 'Canceled_Reversal': // Not sure about this one
          $page->onPaymentSuccess();
          // send notification
          if ($page->didStatusChange()) {
            Engine_Api::_()->sitepage()->sendMail("ACTIVE", $page->page_id);
          }
          break;

        case 'Denied':
        case 'Failed':
        case 'Voided':
        case 'Reversed':
          $page->onPaymentFailure();
          // send notification
          if ($page->didStatusChange()) {
            Engine_Api::_()->sitepage()->sendMail("OVERDUE", $page->page_id);
          }
          break;

        case 'Refunded':
          $page->onRefund();
          // send notification
          if ($page->didStatusChange()) {
            Engine_Api::_()->sitepage()->sendMail("REFUNDED", $page->page_id);
          }
          break;

        case 'Expired': // Not sure about this one
          $page->onExpiration();
          // send notification
          if ($page->didStatusChange()) {
            Engine_Api::_()->sitepage()->sendMail("EXPIRED", $page->page_id);
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

  public function cancelPage($transactionId, $note = null) {
    $profileId = null;

    if ($transactionId instanceof Sitepage_Model_Page) {
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
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitepage');


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
      
		if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
			$transactionParams = array_merge($transactionData, array('resource_type' => $order->source_type,'resource_id'=>$order->source_id));
			Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
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
      if ($order->source_type == 'sitepage_page') {
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
      $error_msg17 = Zend_Registry::get('Zend_Translate')->_('Unknown or unsupported IPN type, or missing transaction or order ID');
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