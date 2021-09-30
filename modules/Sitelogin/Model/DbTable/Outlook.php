<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Outlook.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_Dbtable_Outlook extends Engine_Db_Table {
    protected $_url;
    public function getApi() {
        if (isset($_SESSION['outlook_access_token']) && !empty($_SESSION['outlook_access_token'])) {
            return;
        }
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $tokenRecieved = $this->getAccessToken();
            return $tokenRecieved;
        } else {
            $this->getAuthorizationCode();
        }
    }
    /*
     * Function to fetch access code from linkedin
     * 
     */
    function getAuthorizationCode() {
        $loginEnable = $this->outlookIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if linkedin login is enable
        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $client_id = isset($outlookSettings['clientId']) ? $outlookSettings['clientId'] : 0;
        $client_secret = isset($outlookSettings['clientSecret']) ? $outlookSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('outlook');
        try {
            $params = array('response_type' => 'code',
                'client_id' => $client_id,
                'scope'=>'offline_access user.read',
                'state' => uniqid('', true), // unique long string
                'redirect_uri' => $redirect_uri,
            );
            // Authentication request
            $this->_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query($params);
            // Needed to identify request when it returns to us
            $_SESSION['state'] = $params['state'];
            // Redirect user to authenticate
            header("Location: $this->_url");
            exit;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    /*
     * Function to return access token from linkedin
     * 
     * @return boolean
     */
    function getAccessToken() {
        $loginEnable = $this->outlookIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if Outlook login is enable
        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $client_id = isset($outlookSettings['clientId']) ? $outlookSettings['clientId'] : 0;
        $client_secret = isset($outlookSettings['clientSecret']) ? $outlookSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('outlook');

        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'client_secret' => $client_secret,            
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri,
        );
        try {
            $client = new Zend_Http_Client();
            $client->setUri('https://login.microsoftonline.com/common/oauth2/v2.0/token')
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($params);
            // Process response
            $response = $client->request();
            $responseData = $response->getBody();
            $responseData = Zend_Json::decode($responseData, Zend_Json::TYPE_ARRAY);
            $_SESSION['outlook_access_token'] = $responseData['access_token'];
            $_SESSION['refresh_token'] = $responseData['refresh_token'];
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }
    /*
     * Function to fetch user info from Outlook
     * 
     * @return array
     */
    function fetch() {
        try {
            $service_url = 'https://graph.microsoft.com/v1.0/me';
            $curlHeaders = array (
                    'Host: graph.microsoft.com',
                    'Authorization: Bearer '.$_SESSION['outlook_access_token'],    
            );
            $curl = curl_init($service_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER,$curlHeaders);
            curl_setopt ($curl, CURLOPT_HEADER, false);
            ob_start();
            curl_exec($curl);
            curl_close($curl);
            $response = ob_get_contents();
            ob_end_clean();
            $data=json_decode($response);
            if(isset($data->error) && isset($_SESSION['refresh_token']) && !empty($_SESSION['refresh_token'])) {
                $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
                $client_id = isset($outlookSettings['clientId']) ? $outlookSettings['clientId'] : 0;
                $client_secret = isset($outlookSettings['clientSecret']) ? $outlookSettings['clientSecret'] : 0;

                $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('outlook');

                $params = array(
                    'grant_type' => 'refresh_token',
                    'scope'=>'offline_access user.read',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,            
                    'refresh_token' => $_SESSION['refresh_token'],
                    'redirect_uri' => $redirect_uri,
                );
                try {
                    $client = new Zend_Http_Client();
                    $client->setUri('https://login.microsoftonline.com/common/oauth2/v2.0/token')
                    ->setMethod(Zend_Http_Client::POST)
                    ->setParameterPost($params);
                    // Process response
                    $response = $client->request();
                    $responseData = $response->getBody();
                    $responseData = Zend_Json::decode($responseData, Zend_Json::TYPE_ARRAY);
                    $_SESSION['outlook_access_token'] = $responseData['access_token'];
                    $_SESSION['refresh_token'] = $responseData['refresh_token'];
                } catch (Exception $ex) {
                    throw $ex;
                }
                $service_url = 'https://graph.microsoft.com/v1.0/me';
                $curlHeaders = array (
                    'Host: graph.microsoft.com',
                    'Authorization: Bearer '.$_SESSION['outlook_access_token'],    
                );
                $curl = curl_init($service_url);
                curl_setopt($curl, CURLOPT_HTTPHEADER,$curlHeaders);
                curl_setopt ($curl, CURLOPT_HEADER, false);
                ob_start();
                curl_exec($curl);
                curl_close($curl);
                $response = ob_get_contents();
                ob_end_clean();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return json_decode($response);
    }
    
    /*
     * Function to return if linkedin integration is enabled or not
     * 
     * @return boolean
     */
    public function outlookIntegrationEnabled() {
        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $client_id = isset($outlookSettings['clientId']) ? $outlookSettings['clientId'] : 0;
        $client_secret = isset($outlookSettings['clientSecret']) ? $outlookSettings['clientSecret'] : 0;
        $loginEnable = isset($outlookSettings['outlookOptions']) ? $outlookSettings['outlookOptions'] : 0;
        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;
        return true;
    }
    public function outlookIntegration() {
        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $client_id = isset($outlookSettings['clientId']) ? $outlookSettings['clientId'] : 0;
        $client_secret = isset($outlookSettings['clientSecret']) ? $outlookSettings['clientSecret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;
        return true;
    }
    public function outlookButtonRender($action) {
        if (empty($action)) {
            return false;
        }
        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $loginEnable = isset($outlookSettings['outlookOptions']) ? $outlookSettings['outlookOptions'] : 0;
        return (in_array($action, $loginEnable)) ? true : false;
    }
    
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }
}