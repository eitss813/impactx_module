<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Newgatewayname.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Engine_Service_Newgatewayname extends Zend_Service_Abstract {

    /**
     * Here you can define the API keys of your new payment gateway which will use later in this file. i.e. The publishable key for Stripe gateway.
     *
     * @var string
     */
    protected $_publishable;

    /**
     * Here you can define the API keys of your new payment gateway which will use later in this file. i.e. The Secret key for Stripe gateway.
     *
     * @var string
     */
    protected $_secret;

    /**
     * The log to send debug messages to
     * 
     * @var Zend_Log
     */
    protected $_log;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->setOptions($options);

        // Force the curl adapter if it's available
        if (extension_loaded('curl')) {
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
            //$adapter->setCurlOption(CURLOPT_VERBOSE, false);
            $this->getHttpClient()->setAdapter($adapter);
        }
        $this->getHttpClient()->setConfig(array('timeout' => 15));
    }

    /**
     * Set credentials for your gateway. i.e. here we have set the publishable and secret key for stripe gateway. 
     */
    public function setOptions(array $options) {
        foreach ($options as $key => $value) {
            $property = '_' . $key;
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        // Check options
        if (empty($this->_publishable) || empty($this->_secret)) {
            throw new Engine_Service_Newgatewayname_Exception('Not all connection ' .
            'options were specified.', 'MISSING_LOGIN');
            throw new Zend_Service_Exception('Not all connection options were specified.');
        }
    }

    /**
     * Get the http client and set default parameters
     *
     */
    protected function _prepareHttpClient() {
        
    }

    /**
     * Check params (This method call is useful, if you want to check some required parameters for some methods.)
     *
     * @param array $params
     * @param array $requiredParams
     * @param array $supportedParams
     * @return array
     */
    protected function _checkParams(array $params, $requiredParams = null, $supportedParams = null) {
        // Check params
        if (!is_array($params)) {
            if (!empty($params)) {
                throw new Engine_Service_Newgatewayname_Exception('Invalid data type', 'UNKNOWN_PARAM');
            } else {
                $params = array();
            }
        }

        // Check required params
        if (is_string($requiredParams)) {
            $requiredParams = array($requiredParams);
        } else if (null === $requiredParams) {
            $requiredParams = array();
        }

        // Check supported params
        if (is_string($supportedParams)) {
            $supportedParams = array($supportedParams);
        } else if (null === $supportedParams) {
            $supportedParams = array();
        }

        // Nothing to do
        if (empty($requiredParams) && empty($supportedParams) &&
                is_array($requiredParams) && is_array($supportedParams)) {
            return array();
        }

        // Build full supported
        if (is_array($supportedParams) && is_array($requiredParams)) {
            $supportedParams = array_unique(array_merge($supportedParams, $requiredParams));
        }

        // Run strtoupper on all keys?
        $params = array_combine(array_map('strtolower', array_keys($params)), array_values($params));

        // Init
        $processedParams = array();
        $foundKeys = array();

        // Process out simple params
        $processedParams = array_merge($processedParams, array_intersect_key($params, array_flip($supportedParams)));
        $params = array_diff_key($params, array_flip($supportedParams));
        $foundKeys = array_merge($foundKeys, array_keys($processedParams));

        // Process out complex params
        foreach ($supportedParams as $supportedFormat) {
            foreach ($params as $key => $value) {
                if (count($parts = sscanf($key, $supportedFormat)) > 0) {
                    $foundKeys[] = $supportedFormat;
                    $processedParams[$key] = $value;
                }
            }
        }

        // Remove complex params
        $params = array_diff_key($params, $processedParams);

        // Anything left is an unsupported param
        if (!empty($params)) {
            $paramStr = '';
            foreach ($params as $key => $unsupportedParam) {
                if ($paramStr != '')
                    $paramStr .= ', ';
                $paramStr .= "$key:" . $unsupportedParam;
            }

            throw new Engine_Service_Newgatewayname_Exception(sprintf('Unknown param(s): ' .
                    '%1$s', $paramStr), 'UNKNOWN_PARAM');
        }

        // Let's check required against foundKeys
        if (count($missingRequiredParams = array_diff_key($requiredParams, $foundKeys)) > 0) {
            $paramStr = '';
            foreach ($missingRequiredParams as $missingRequiredParam) {
                if ($paramStr != '')
                    $paramStr .= ', ';
                $paramStr .= $missingRequiredParam;
            }
            throw new Engine_Service_Newgatewayname_Exception(sprintf('Missing required ' .
                    'param(s): %1$s', $paramStr), 'MISSING_REQUIRED');
        }

        return $processedParams;
    }

    /**
     * Add the code to create products/plans in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function createProduct(array $params = array()) {
        
    }

    /**
     * Add the code to retrieve created products/plans from this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function retrieveProduct($productId) {
        
    }

    /**
     * Add the code to update created products/plans in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function updateProduct($productId, $params = null) {
        
    }

    /**
     * Add the code to delete created products/plans of this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function deleteProduct($productId) {
        
    }

    /**
     * Gets product details by vendor product id. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     * 
     * @param string $vendorProductId
     * @return object
     */
    public function detailVendorProduct($vendorProductId) {
        
    }

    /**
     * Used to cancel recurring payment profile. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @param string $profileId
     * @return object
     */
    public function cancelRecurringPaymentsProfile($profileId, $note = null) {
        
    }
    
    /**
     * Write the coupon creation code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site.
     */    
    public function createCoupon(array $params = array()) {

    }    
    
    /**
     * Write the coupon edit code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site.
     */       
    public function editCoupon($couponCode, $params = null) {

    }     
    
    /**
     * Write the coupon deletetion code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site.
     */       
    public function deleteCoupon($couponCode) {

    }       

}
