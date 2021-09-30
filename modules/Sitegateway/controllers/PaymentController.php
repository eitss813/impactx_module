<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PaymentController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_PaymentController extends Core_Controller_Action_Standard {

    public function init() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $gatewayMethod = _GETGATEWAYMETHOD;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        if (empty($gatewayMethod))
            return $this->_helper->redirector->gotoRoute(array(), "default", true);

        if (((isset($params['source_type']) && $params['source_type'] != 'payment_subscription') || (isset($params['product_type']) && $params['product_type'] != 'payment_package')) && (!$viewer_id)) {
             return $this->_helper->redirector->gotoRoute(array(), "default", true);
        }
    }

    public function processAction() {
        
        $stripe_payment_gateway_session = isset($_SESSION['stripe_payment_begin']['order_id']) ? $_SESSION['stripe_payment_begin']['order_id'] : NULL;
        $sitegatewayPaymentProcess = Zend_Registry::isRegistered('sitegatewayPaymentProcess') ? Zend_Registry::get('sitegatewayPaymentProcess') : null;

       if (empty($stripe_payment_gateway_session)) {

            return $this->_helper->redirector->gotoRoute(array(), "default", true);
        } else {
            unset($_SESSION['stripe_payment_begin']['order_id']);
        }

        $this->view->allParams = $allParams = $this->_getAllParams();

        $payment_order_id = $allParams['INVNUM'];
        $source_type = $allParams['source_type'];
        $source_id = $allParams['source_id'];

        if (empty($source_type) || empty($source_id) || empty($payment_order_id) || empty($sitegatewayPaymentProcess)) {
            return $this->_helper->redirector->gotoRoute(array(), "default", true);
        }

        $source = Engine_Api::_()->getItem($source_type, $source_id);
        $sitegatewayGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.global.view', 0);
        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $sitegatewayManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.manage.type', 0);
        $sitegatewayInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.info.type', 0);
        $sitegatewayGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.global.type', 0);

        if (empty($sitegatewayGlobalType)) {
            for ($check = 0; $check < strlen($hostType); $check++) {
                $tempHostType += @ord($hostType[$check]);
            }
            $tempHostType = $tempHostType + $sitegatewayGlobalView;
        }
         $creditallow=false;
        $this->view->productQty = 1;
        $this->view->productParentId = 0;
        if ($source_type == 'siteevent_event') {//EVENT PAID PACKAGE CASE
            $creditallow=true;
            $this->view->product = Engine_Api::_()->getItem('siteeventpaid_package', $source->package_id);
            $this->view->productPrice = $this->view->product->price;
            $this->view->productDesc = $this->view->product->getDescription();
        } elseif ($source_type == 'sitereview_listing') {//LISTING PAID PACKAGE CASE
            $creditallow=true;
            $this->view->product = Engine_Api::_()->getItem('sitereviewpaidlisting_package', $source->package_id);
            $this->view->productPrice = $this->view->product->price;
            $this->view->productDesc = $this->view->product->getDescription();
        } elseif ($source_type == 'userads') {//COMMUNITYADS PACKAGE CASE
            $this->view->product = Engine_Api::_()->getItem('package', $source->package_id);
            $this->view->productPrice = $this->view->product->price;
            $this->view->productDesc = $this->view->product->getDescription();
        } elseif (($source_type == 'sitepage_page') || ($source_type == 'sitebusiness_business') || ($source_type == 'sitegroup_group') || ($source_type == 'sitestore_store') || ($source_type == 'payment_subscription')) {//PAID PACKAGE CASE
            $creditallow=true;
            $packageType = explode("_", $source_type);
            $packageType = $packageType['0'] . "_package";
            $this->view->product = Engine_Api::_()->getItem($packageType, $source->package_id);
            $this->view->productPrice = $this->view->product->price;
            $this->view->productDesc = $this->view->product->getDescription();
        } elseif ($source_type == 'sitestoreproduct_storebill') {//STORE BILLS/COMMISSION
            $this->view->product = $source;
            $this->view->productPrice = $this->view->product->amount;
            $this->view->productDesc = $this->view->product->message;
        } elseif ($source_type == 'sitestoreproduct_paymentrequest') {//PAYMENT REQUESTS BY SELLER
            $this->view->product = $source;
            $this->view->productPrice = $this->view->product->response_amount;
            $this->view->productDesc = $this->view->product->response_message;
            $this->view->productParentId = $source->store_id;
        } elseif ($source_type == 'sitestoreproduct_order') {

            $this->view->product = $source;
            $this->view->productDesc = $this->view->translate('Store Products');
            
            $sitestore_stripe_grand_total = isset($_SESSION['Sitestoreproduct_order_stripe_payment_begin']['grand_total']) ? $_SESSION['Sitestoreproduct_order_stripe_payment_begin']['grand_total'] : NULL;
            $sitestore_stripe_item_count = isset($_SESSION['Sitestoreproduct_order_stripe_payment_begin']['item_count']) ? $_SESSION['Sitestoreproduct_order_stripe_payment_begin']['item_count'] : NULL;

             if($sitestore_stripe_grand_total && $sitestore_stripe_item_count)
            {
              $this->view->productPrice = $sitestore_stripe_grand_total;
              $this->view->productQty = $sitestore_stripe_item_count;
              $this->view->productParentId = $source->store_id;
               unset($_SESSION['Sitestoreproduct_order_stripe_payment_begin']['item_count']);
               unset($_SESSION['Sitestoreproduct_order_stripe_payment_begin']['grand_total']);
            } else{

            $this->view->productPrice = $source->grand_total;
            $this->view->productQty = $source->item_count;
            $this->view->productParentId = $source->store_id;
            }
            
        } elseif ($source_type == 'siteeventticket_eventbill') {//EVENT BILLS/COMMISSION
            $this->view->product = $source;
            $this->view->productPrice = $this->view->product->amount;
            $this->view->productDesc = $this->view->product->message;
        } elseif ($source_type == 'siteeventticket_paymentrequest') {//PAYMENT REQUESTS BY SELLER
            $this->view->product = $source;
            $this->view->productPrice = $this->view->product->response_amount;
            $this->view->productDesc = $this->view->product->response_message;
            $this->view->productParentId = $source->event_id;
        } elseif ($source_type == 'siteeventticket_order') {//EVENT TICKETS
            $this->view->product = $source;
            $this->view->productPrice = $source->grand_total;
            $this->view->productDesc = $this->view->translate('Event Tickets');
            $this->view->productQty = $source->ticket_qty;
            $this->view->productParentId = $source->event_id;
        } elseif ($source_type == 'sitecrowdfunding_project') {//CROWDFUNDING PROJECT PAID PACKAGE CASE
            $this->view->product = Engine_Api::_()->getItem('sitecrowdfunding_package', $source->package_id);
            $this->view->productPrice = $this->view->product->price;
            $this->view->productDesc = $this->view->product->getDescription();
        } elseif ($source_type == 'sitecrowdfunding_backer') {//CROWDFUNDING BACKING ON PROJECT CASE
            $this->view->product = $source;
            $this->view->productPrice = $source->amount;
            $this->view->productDesc = $this->view->translate('Backing on Project');
            $this->view->productQty = 1;
            $this->view->productParentId = $source->project_id;

        }elseif ($source_type == 'sitecredit_order') {//EVENT TICKETS
            $this->view->product = $source;
            $this->view->productPrice = $source->grand_total;
            $this->view->productDesc = $this->view->translate('Credits');
            $this->view->productQty = $source->credit_point;
            $this->view->productParentId = $source->order_id;

        }
        //Coupon pluign work for recuring payment use coupon code.
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon') && !empty($allParams['coupon'])) {

            //COUPON VALUE ACCRORDING TO CODE. CALLING FUNCTION.
            $result = Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->getCode($allParams['coupon']);
            $discount_value = 0;
            if ($result->discount_type == 'price') {
                $discount_value = $result->discount_value;
            } else if ($result->discount_type == 'percentage') {
                $discount_value = ($this->view->productPrice) * ($result->discount_value / 100);
            }
            $this->view->productPrice = $this->view->productPrice - $discount_value;
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecredit')) {
                  
               if ($creditallow) {
                              //SESSION SET OF CREDITS.
                    if ($source_type == 'payment_subscription') {
                        if(!empty($allParams['credit_discount'])) {
                        $creditSession = new Zend_Session_Namespace('payment_subscription_credit');
                        if (!empty($creditSession->paymentSubscriptionCreditDetail)) {
                            $creditDetail = unserialize($creditSession->paymentSubscriptionCreditDetail);
                            $this->view->productPrice = $this->view->productPrice - $allParams['credit_discount'];
                        }
                        }
                    }
                
                    if($source_type == 'siteevent_event'||$source_type == 'sitereview_listing'||$source_type == 'sitepage_page'||$source_type == 'sitestore_store'||$source_type == 'sitegroup_group') {

                        $creditSession = new Zend_Session_Namespace('credit_package_payment_'.$this->view->product->getType());

                        if (!empty($creditSession->packagePaymentCreditDetail)) {
                            $creditDetail = unserialize($creditSession->packagePaymentCreditDetail);
                            if(!empty($creditDetail['credit_amount']))
                            $this->view->productPrice = $this->view->productPrice -$creditDetail['credit_amount'];
                        }
                    }                

               }    
           
        }
        if (!empty($sitegatewayGlobalType) || ($tempHostType == $sitegatewayManageType)) {
           
            $this->view->publishable=$publish =Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'key' => 'publishable', 'productType' => $this->view->product->getType(), 'productParentId' => $this->view->productParentId));
            
        } else {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegateway.global.type', 1);
        }
    }

    public function paymentAction() {
        $token = !empty($_POST['stripeToken']) ? $_POST['stripeToken'] : '';

        $product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : '';

        $product_type = !empty($_POST['product_type']) ? $_POST['product_type'] : '';

        $product_price = !empty($_POST['product_price']) ? $_POST['product_price'] : '';

        $product_desc = !empty($_POST['product_desc']) ? $_POST['product_desc'] : '';

        $productParentId = !empty($_POST['productParentId']) ? $_POST['productParentId'] : '';

        $couponCode = isset($_POST['allParams']['coupon']) ? $_POST['allParams']['coupon'] : '';

        $credit_discount = isset($_POST['allParams']['credit_discount']) ? $_POST['allParams']['credit_discount'] : '';

        if (empty($token) || empty($product_id) || empty($product_type) || empty($product_price)) {
            return;
        }

        $sitegatewayPaymentAction = Zend_Registry::isRegistered('sitegatewayPaymentAction') ? Zend_Registry::get('sitegatewayPaymentAction') : null;
        $sitegatewayApi = Engine_Api::_()->sitegateway();

        if ($product_type == 'communityad_package') {
            $product_type = 'package';
        }

        if ($product_type == 'sitead_package') {
            $product_type = 'package';
        }

        if (empty($sitegatewayPaymentAction)) {
            return;
        }

        $product = Engine_Api::_()->getItem($product_type, $product_id);

        // Create the charge on Stripe's servers - this will charge the user's card
        try {

            $gatewayObject = Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'returnGateway' => true, 'productType' => $product_type, 'productParentId' => $productParentId));
            $gatewayPlugin = $gatewayObject->getGateway();
            $viewer = Engine_Api::_()->user()->getViewer();

            $isOneTimeMethodExist = method_exists($product, 'isOneTime');
            // Check for processed
            if ($isOneTimeMethodExist && !$product->isOneTime()) {
                if ($product_type == 'payment_package' || empty($viewer->email)) {
                    $customerParams = array("description" => $this->view->translate("Payment by new signup user for subscription: %s", $product_desc),
                        "source" => $token);
                    //GET THE EMAIL OF USER WHO DOING SIGNUP
                    $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                    if(isset($subscriptionSession->user_id) && !empty($subscriptionSession->user_id)) {
                        $user = Engine_Api::_()->getItem('user', $subscriptionSession->user_id);
                        $customerParams = ($user && $user->email) ? array_merge($customerParams, array('email' => $user->email)) : $customerParams;
                    }
                } else {
                    $customerParams = array(
                        "email" => $viewer->email,
                        "description" => $this->view->translate("Payment by %1s for product: %2s", $viewer->email, $product_desc),
                        "source" => $token,
                    );
                }
                $customer = $gatewayPlugin->createCustomer($customerParams);
                $plan_id = $sitegatewayApi->getPaymentProductColumn(array('columnName' => 'sku', 'extension_type' => $product_type, 'extension_id' => $product_id));
                $subscriptionParams = array(
                    'customer_id' => $customer->id,
                    'plan' => $plan_id,
                );
                if (!empty($couponCode)) {
                    $subscriptionParams = array_merge($subscriptionParams, array('coupon' => strtoupper($couponCode)));
                }
                $subscription = $gatewayPlugin->createSubscription($subscriptionParams);
                $this->view->customer_id = $customer->id;
                $this->view->subscription_id = NULL;
                if (!empty($subscription)) {
                    $this->view->subscription_id = $subscription->id;
                }
            } else {
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0) && ($product_type == 'siteeventticket_order' || $product_type == 'sitestoreproduct_order' || $product_type == 'sitecrowdfunding_backer' )) {
                    $amount = null;
                    if ($product_type == 'siteeventticket_order') {
                        $gateway = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $product->event_id, 'plugin = \'Sitegateway_Plugin_Gateway_Stripe\''));
                    } elseif ($product_type == 'sitestoreproduct_order') {
                        $gateway = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $product->store_id, 'plugin = \'Sitegateway_Plugin_Gateway_Stripe\''));
                    } elseif ($product_type == 'sitecrowdfunding_backer') {
                        $gateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $product->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_Stripe\''));
                        $amount = $product->amount;
                    }
                    if (is_null($amount)) {
                        $amount = $product->grand_total;
                    }
                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripechargemethod', 1)) {

                        $chargeParams = array(
                            "amount" => $sitegatewayApi->getPrice($amount),
                            "currency" => $sitegatewayApi->getCurrency(),
                            "description" => $product_desc,
                            "source" => $token,
                            "destination" => $gateway->config['stripe_user_id'],
                            "application_fee" => $sitegatewayApi->getPrice($product->commission_value),
                        );

                        $charge = $gatewayPlugin->createCharge($chargeParams);
                    } else {

                        $chargeParams = array(
                            "amount" => $sitegatewayApi->getPrice($amount),
                            "currency" => $sitegatewayApi->getCurrency(),
                            "description" => $product_desc,
                            "source" => $token,
                            "application_fee" => $sitegatewayApi->getPrice($product->commission_value),
                        );

                        $charge = $gatewayPlugin->createCharge($chargeParams, array('stripe_account' => $gateway->config['stripe_user_id']));
                    }
                } else {

                    $chargeParams = array(
                        "amount" => $sitegatewayApi->getPrice($product_price),
                        "currency" => $sitegatewayApi->getCurrency(),
                        "description" => $product_desc,
                        "source" => $token
                    );

                    $charge = $gatewayPlugin->createCharge($chargeParams);
                }

                $this->view->charge_id = NULL;
                if (!empty($charge)) {
                    $this->view->charge_id = $charge->id;
                }
            }
        } catch (Exception $e) {
            //echo $e->getMessage();
        }
    }

    public function oAuthProcessAction() {

        $resource_type = $_POST['resource_type'];
        $resource_id = $_POST['resource_id'];

        if (isset($_SESSION['stripe_connect_oauth_process'])) {
            $session = new Zend_Session_Namespace('stripe_connect_oauth_process');
            $session->unsetAll();
        }

        $session = new Zend_Session_Namespace('stripe_connect_oauth_process');
        $session->resource_type = $resource_type;
        $session->resource_id = $resource_id;

        $this->view->client_id = Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'key' => 'client_id', 'productType' => 'payment_package'));
    }

    public function stripeConnectAction() {
        $sitegatewayStripConnection = Zend_Registry::isRegistered('sitegatewayStripConnection') ? Zend_Registry::get('sitegatewayStripConnection') : null;
        $secret = Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'key' => 'secret', 'productType' => 'payment_package'));
        $sitegatewayGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.global.view', 0);
        $sitegatewayLSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.lsettings', 0);
        $client_id = Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'key' => 'client_id', 'productType' => 'payment_package'));
        $sitegatewayManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.manage.type', 0);
        $sitegatewayInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.info.type', 0);
        $sitegatewayGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.global.type', 0);
        $tempSitemenuLtype=0;
        if (!isset($_SESSION['stripe_connect_oauth_process'])) {
            return $this->_helper->redirector->gotoRoute(array(), "default", true);
        } else {
            $session = new Zend_Session_Namespace('stripe_connect_oauth_process');
            $resource_type = $session->resource_type;
            $resource_id = $session->resource_id;
        }

        if (empty($sitegatewayGlobalType)) {
            for ($check = 0; $check < strlen($sitegatewayLSettings); $check++) {
                $tempSitemenuLtype += @ord($sitegatewayLSettings[$check]);
            }
            $tempSitemenuLtype = $tempSitemenuLtype + $sitegatewayGlobalView;
        }

        if (empty($sitegatewayGlobalType) && ($tempSitemenuLtype != $sitegatewayInfoType)) {
            $session->unsetAll();
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegateway.viewtypeinfo.type', 1);

            return $this->_helper->redirector->gotoRoute($routeArray, $routeName, true);
        }

        if (!empty($sitegatewayStripConnection) && isset($_GET['code'])) { // Redirect w/ code
            $code = $_GET['code'];

            $token_request_body = array(
                'grant_type' => 'authorization_code',
                'client_id' => $client_id,
                'code' => $code,
                'client_secret' => $secret
            );

            $req = curl_init("https://connect.stripe.com/oauth/token");
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POST, true);
            curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

            // TODO: Additional error handling
            //$respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
            $resp = json_decode(curl_exec($req), true);
            curl_close($req);
            $gatewayColumn='gateway_id';
            if ($resource_type == 'siteevent_event') {
                $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'siteeventticket');
                $primaryKey = 'event_id';
                $gatewayItem = 'siteeventticket_gateway';
                $routeName = 'siteeventticket_order';
                $routeArray = array('action' => 'payment-info', "$primaryKey" => $resource_id, 'stripe-connect' => 1);
            } elseif ($resource_type == 'sitestore_store') {
                $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
                $primaryKey = 'store_id';
                $gatewayItem = 'sitestoreproduct_gateway';
                $routeName = 'sitestore_store_dashboard';
                $routeArray = array('action' => 'store', "$primaryKey" => $resource_id, 'type' => 'product', 'menuId' => 53, 'method' => 'payment-info', 'stripe-connect' => 1);
            } elseif ($resource_type == 'sitecrowdfunding_project') {
                $gatewayTable = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $primaryKey = 'project_id';
                $gatewayItem = 'sitecrowdfunding_projectGateway';
                $routeName = 'sitecrowdfunding_specific';
                $routeArray = array('action' => 'payment-info', "$primaryKey" => $resource_id, 'stripe-connect' => 1);
                $gatewayColumn='projectgateway_id';
            }

            if (!empty($resource_id)) {

                $gateway_id = $gatewayTable->fetchRow(array("$primaryKey = ?" => $session->resource_id, 'plugin = \'Sitegateway_Plugin_Gateway_Stripe\''))->$gatewayColumn;

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    //GET VIEWER ID
                    $viewer = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner();
                    $viewer_id = $viewer->getIdentity();
                    if (empty($gateway_id)) {
                        $row = $gatewayTable->createRow();
                        $row->$primaryKey = $resource_id;
                        $row->user_id = $viewer_id;
                        $row->email = $viewer->email;
                        $row->title = 'Stripe';
                        $row->description = '';
                        $row->plugin = 'Sitegateway_Plugin_Gateway_Stripe';
                        $row->save();

                        $gateway = $row;
                    } else {
                        $gateway = Engine_Api::_()->getItem($gatewayItem, $gateway_id);
                        $gateway->email = $viewer->email;
                        $gateway->save();
                    }
                    $db->commit();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                $values = array();
                $values['publishable'] = $resp['stripe_publishable_key'];
                $values['secret'] = $resp['access_token'];
                $values['stripe_user_id'] = $resp['stripe_user_id'];
                $values['refresh_token'] = $resp['refresh_token'];
                $values['token_type'] = $resp['token_type'];

                $gateway->setFromArray(array(
                    'enabled' => 1,
                    'config' => $values,
                ));
                $gateway->save();
            }

            $session->unsetAll();

            $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
            $session->resource_type = $resource_type;

            return $this->_helper->redirector->gotoRoute($routeArray, $routeName, true);
        } else if (isset($_GET['error'])) { // Error
            //echo $_GET['error_description'];
            $session->unsetAll();

            return $this->_helper->redirector->gotoRoute($routeArray, $routeName, true);
        }
    }

}
