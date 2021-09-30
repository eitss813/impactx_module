<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Projectgateway.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Projectgateway extends Core_Model_Item_Abstract {

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
            $class = "Sitecrowdfunding_Plugin_Gateway_PayPal";
            if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && strstr($this->plugin, 'Sitegateway_Plugin_Gateway_')) {
                $class = $this->plugin;
            }
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

    public function uploadKYC($page, $document_type, $tag) {
        if ($page instanceof Zend_Form_Element_File) {
            $file = $page->getFileName();
            $fileName = $file;
        } else {
            throw new Zend_Exception("invalid argument passed to uploadKyc");
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $mode = 'live';
        if ($adminGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        $params = array();
        $params['user_id'] = $this->config[$mode]['mangopay_user_id'];
        $params['document_type'] = $document_type;
        $params['tag'] = $tag;
        $params['page'] = $file;
        $adminGateway->getService()->createKycPage($params);
    }

}
