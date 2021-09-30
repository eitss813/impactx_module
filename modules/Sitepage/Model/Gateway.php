<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Gateway.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Gateway extends Core_Model_Item_Abstract {

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
  public function getPlugin() {
    if (null === $this->_plugin) {
      $class = $this->plugin;

      $classArray = explode('_', $class);

      if($classArray[0] === 'Payment') {
        $classArray[0] = 'Sitepage';
      }
      
      $classChange = implode('_', $classArray);
      $class = $classChange;      
      
      Engine_Loader::loadClass($class);
      $plugin = new $class($this);
      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
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
  public function getGateway() {
    return $this->getPlugin()->getGateway();
  }

  /**
   * Get the payment service api
   * 
   * @return Zend_Service_Abstract
   */
  public function getService() {
    return $this->getPlugin()->getService();
  }

}

?>