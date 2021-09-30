<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvpmnt
 * @package    Sesadvpmnt
 * @copyright  Copyright 2019-2020 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: PaymentController.php  2019-04-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
include_once APPLICATION_PATH . "/application/modules/Sesadvpmnt/Api/Stripe/init.php";
class Sesadvpmnt_PaymentController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Payment_Model_Order
   */
  protected $order_id;

  protected $_type;

  /**
   * @var Payment_Model_Gateway
   */
  protected $_gateway;

  /**
   * @var Payment_Model_Subscription
   */
  protected $_item;
  protected $_package;

  protected $_module;

  public function init()
  {
    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Payment_Subscription');
    if( !$this->_user || !$this->_user->getIdentity() ) {
      if( !empty($this->_session->user_id) ) {
        $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
      }
    }
  }
  public function indexAction()
  { 
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    $requestType = $this->_getParam('type',null);
    if($requestType == "user") {
        $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
        if(!$gatewayId ||
            !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
            !($gateway->enabled)) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $this->_gateway = $gateway;
        if(!($subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id)) ||
            !($subscription = Engine_Api::_()->getItem('payment_subscription', $subscriptionId)) ||
            !($package = Engine_Api::_()->getItem('payment_package', $subscription->package_id))) {
          return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            if( !empty($this->_session->order_id) ) {
                $previousOrder = $ordersTable->find($this->_session->order_id)->current();
                if( $previousOrder && $previousOrder->state == 'pending' ) {
                    $previousOrder->state = 'incomplete';
                    $previousOrder->save();
                }
            }
            $ordersTable->insert(array(
                'user_id' => $this->_user->getIdentity(),
                'gateway_id' => $gateway->gateway_id,
                'state' => 'pending',
                'creation_date' => new Zend_Db_Expr('NOW()'),
                'source_type' => 'payment_subscription',
                'source_id' => $subscription->subscription_id,
            ));
            $params['order_id'] = $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();
            // For Coupon 
            $couponSessionCode = $package->getType().'-'.$package->package_id.'-'.$subscription->getType().'-'.$subscription->subscription_id.'-1';
            $params['amount'] = @isset($_SESSION[$couponSessionCode]) ? round($package->price - $_SESSION[$couponSessionCode]['discount_amount']) : $package->price;
            //For Credit integration
            $creditCode =  'credit'.'-payment-'.$package->package_id.'-'.$subscription->subscription_id;
            $sessionCredit = new Zend_Session_Namespace($creditCode);
            if(isset($sessionCredit->total_amount) && $sessionCredit->total_amount > 0) { 
              $params['amount'] = $sessionCredit->total_amount;
            }
            $this->view->amount = $params['amount'];
            $params['type'] = "user";
            $this->view->currency =  $params['currency'] = $settings->getSetting('payment.currency', 'USD');
        }
    }
    if($this->_getParam('type',null) == "booking"){
      if(!$gatewayId ||
        !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
        !($gateway->enabled)) {
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      }
      $this->_gateway = $gateway;
      $viewer = Engine_Api::_()->user()->getViewer();
      $this->order_id = $this->_getParam('order_id',$this->_session->order_id);
      $order = Engine_Api::_()->getItem('booking_order',$this->order_id);
      $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
      $ordersTable->insert(array(
        'user_id' => $viewer->getIdentity(),
        'gateway_id' => $gateway->gateway_id,
        'state' => 'pending',
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'source_type' => 'booking_order',
        'source_id' => $order->order_id,
      ));
    }
    // Prepare host info
    $schema = _ENGINE_SSL ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];

    $params['vendor_order_id'] = $order_id;
    $this->view->publishKey = $publishKey = $gateway->config['sesadvpmnt_stripe_publish']; 
    
    $plugin = $this->_gateway->getPlugin(); 
    $this->_type = $this->_getParam('type');
      // Unset certain keys
    unset($this->_session->gateway_id);
    if(!array_key_exists(strtoupper($params['currency']) , $plugin->getSupportedCurrencies())){
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    try{
      $secretKey = $gateway->config['sesadvpmnt_stripe_secret'];
      \Stripe\Stripe::setApiKey($secretKey);
      if($requestType == "user") {
          $this->view->returnUrl = $params['return_url'] = $schema . $host
          . $this->view->url(array('action' => 'return'),'sesadvpmnt_payment')
          . '?order_id=' . $order_id
          . '&state=' . 'return';
          $params['cancel_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'),'sesadvpmnt_payment')
            . '?order_id=' . $order_id
            . '&state=' . 'cancel';
          $this->view->session = $plugin->createSubscriptionTransaction($this->_user,
                    $subscription, $package, $params);
      } else { 
        $params['return_url'] = $schema . $host
          . $this->view->url(array('action' => 'return-order'),'sesadvpmnt_payment')
          . '?order_id=' . $order_id
          . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
          . $this->view->url(array('action' => 'return-order'),'sesadvpmnt_payment')
          . '?order_id=' . $order_id
          . '&state=' . 'cancel';
        $this->view->session = $plugin->createOrderTransaction($params);
      }
    }catch(Exception $e){
      $this->view->error = true;
      $this->view->message = $e->getMessage();
    }
  }
  public function returnOrderAction() {
    if(!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
        !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id))) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } 
		return $params;
  }
  public function returnAction()
  {
    // Get order
    if( !$this->_user ||
        !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
        $order->user_id != $this->_user->getIdentity() ||
        $order->source_type != 'payment_subscription' ||
        !($subscription = $order->getSource()) ||
        !($package = $subscription->getPackage()) ||
        !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id)) ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();
    // Process return
    unset($this->_session->errorMessage);
    try {
      if($_GET['state'] != "cancel"){
        $status = $plugin->onSubscriptionReturn($order,$this->_getAllParams());
        if(($status == 'active' || $status == 'free')) {
            $admins = Engine_Api::_()->user()->getSuperAdmins();
            foreach($admins as $admin){
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin,'payment_subscription_transaction', array(
                    'gateway_type' => $gateway->title,
                    'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'payment'), 'admin_default', true),
                ));
            }
        }
      }else{
        $status = 'failure';
        $this->_session->errorMessage = "Payment Cancelled";
      }
    } catch( Payment_Model_Exception $e ) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }
    return $this->_finishPayment($status);
  }
  protected function _checkSubscriptionStatus(
      Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_user ) {
      return false;
    }
    if( null === $subscription ) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'active = ?' => true,
      ));
    }
    if( !$subscription ) {
      return false;
    }

    if( $subscription->status == 'active' ||
        $subscription->status == 'trial' ) {
      if( !$subscription->getPackage()->isFree() ) {
        $this->_finishPayment('active');
      } else {
        $this->_finishPayment('free');
      }
      return true;
    } else if( $subscription->status == 'pending' ) {
      $this->_finishPayment('pending');
      return true;
    }

    return false;
  }
 protected function _finishPayment($state = 'active')
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->_user;

    // No user?
    if( !$this->_user ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Log the user in, if they aren't already
    if( ($state == 'active' || $state == 'free') &&
        $this->_user &&
        !$this->_user->isSelf($viewer) &&
        !$viewer->getIdentity() ) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
      $viewer = $this->_user;
    }

    // Handle email verification or pending approval
    if( $viewer->getIdentity() && !$viewer->enabled ) {
      Engine_Api::_()->user()->setViewer(null);
      Engine_Api::_()->user()->getAuth()->getStorage()->clear();

      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
      $confirmSession->approved = $viewer->approved;
      $confirmSession->verified = $viewer->verified;
      $confirmSession->enabled  = $viewer->enabled;
      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if( $state == 'free' ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
    }
  }


  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
    $this->view->url = $this->view->url(array(), 'default', true);
    // If user's member level changed then redirect to edit profile page.
    if (Engine_Api::_()->getDbtable('values', 'authorization')->changeUsersProfileType($this->_user)) {
      Engine_Api::_()->getDbtable('values', 'authorization')->resetProfileValues($this->_user);
      $this->view->url = $this->view->url(array('action' => 'profile', 'controller' => 'edit'), 'user_extended');
    }
  }

  protected function _checkDefaultPaymentPlan()
  {
    // No user?
    if( !$this->_user ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Handle default payment plan
    try {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      if( $subscriptionsTable ) {
        $subscription = $subscriptionsTable->activateDefaultPlan($this->_user);
        if( $subscription ) {
          return $this->_finishPayment('free');
        }
      }
    } catch( Exception $e ) {
      // Silence
    }

    // Fall-through
  }

}
