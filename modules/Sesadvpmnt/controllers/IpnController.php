<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvpmnt
 * @package    Sesadvpmnt
 * @copyright  Copyright 2019-2020 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IpnController.php  2019-04-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

include_once APPLICATION_PATH . "/application/modules/Sesadvpmnt/Api/Stripe/init.php";
class Sesadvpmnt_IpnController extends Core_Controller_Action_Standard
{
  public function __call($method, array $arguments)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
// retrieve the request's body and parse it as JSON
    $body = @file_get_contents('php://input');
    $params = $this->_getAllParams();
    $body = json_decode($body,true);
    $gatewayType = $params['action'];
    if( !empty($gatewayType) && 'index' !== $gatewayType ) {
      $params['gatewayType'] = $gatewayType;
    } else {
      $gatewayType = null;
    }
    // Log ipn
    $ipnLogFile = APPLICATION_PATH . '/temporary/log/stripe-ipn.log';
    file_put_contents($ipnLogFile,
        date('c') . ': ' .
        print_r($params, true),
        FILE_APPEND);
    try {
      //Get gateways
      $type = "";
      if($body['type'] == "invoice.payment_succeeded"){
        $metadata = $body['data']['object']['lines']['data'][0]['metadata'];
        if(!($order = Engine_Api::_()->getItem('payment_order',$metadata['order_id'])) || !($gateway = Engine_Api::_()->getItem($metadata['type'],$metadata['gateway_id'])) ) {
          return false;
        }
      } else {
         return false;
      }
    } catch( Exception $e ) {
      // Gateway detection failed
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'Gateway detection failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }
    // Validate ipn
    try {
      $gatewayPlugin = $gateway->getPlugin();
    } catch( Exception $e ) {
      // IPN validation failed
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'IPN validation failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }
    // Process IPN
    try {
      $gatewayPlugin->onTransactionIpn($order,$body);
    } catch( Exception $e ) {
      $gatewayPlugin->getGateway()->getLog()->log($e, Zend_Log::ERR);
      // IPN validation failed
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'IPN processing failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }
    // Exit
    echo 'OK';
    exit(0);
  }
}
