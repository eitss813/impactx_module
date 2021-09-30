<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Gateway.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblogpackage_Model_Gateway extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  
  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;
  /**
   * Get the payment plugin
   *
   * @return Engine_Payment_Plugin_Abstract
   */
  public function getPlugin()
  {
    if( null === $this->_plugin ) {
      $class = $this->plugin;
			$class = str_replace('Payment','Sesblogpackage',$class);
			if($this->plugin == "Sesadvpmnt_Plugin_Gateway_Stripe")
        $class = str_replace('Sesadvpmnt','Sesblogpackage',$class);
      if($this->plugin == "Epaytm_Plugin_Gateway_Paytm")
        $class = str_replace('Epaytm','Sesblogpackage',$class);
      if($this->plugin == "Ecashfree_Plugin_Gateway_Cashfree")
        $class = str_replace('Ecashfree','Sesblogpackage',$class);
      Engine_Loader::loadClass($class);
      $plugin = new $class($this);
      if( !($plugin instanceof Engine_Payment_Plugin_Abstract) ) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
            'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }
    return $this->_plugin;
  }

  /**
   * Get the payment gateway
   * 
   * @return Engine_Payment_Gateway
   */
  public function getGateway()
  {
    return $this->getPlugin()->getGateway();
  }

  /**
   * Get the payment service api
   * 
   * @return Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getPlugin()->getService();
  }
}
