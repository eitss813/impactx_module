<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Vk.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_Dbtable_Vk extends Engine_Db_Table {
    protected $_url;
    public function getApi() {
        ini_set('display_errors', '1');
        if (isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token'])) {
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
        $loginEnable = $this->vkIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if linkedin login is enable
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $client_id = isset($vkSettings['clientId']) ? $vkSettings['clientId'] : 0;
        $client_secret = isset($vkSettings['clientSecret']) ? $vkSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('vk');
        try {
            $params = array('response_type' => 'code',
                'client_id' => $client_id,
                'scope'=>'email',
                'display' => 'popup',
                'v' => '5.67',
                'state' => uniqid('', true), // unique long string
                'redirect_uri' => $redirect_uri,
            );
            // Authentication request
            $this->_url = 'https://oauth.vk.com/authorize?' . http_build_query($params);
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
        $loginEnable = $this->vkIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if Vk login is enable
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $client_id = isset($vkSettings['clientId']) ? $vkSettings['clientId'] : 0;
        $client_secret = isset($vkSettings['clientSecret']) ? $vkSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('vk');

        $params = array(
            'redirect_uri' => $redirect_uri,
            'client_id' => $client_id,
            'client_secret' => $client_secret,            
            'code' => $_GET['code'],
        );
        try {
            $client = new Zend_Http_Client();
            $client->setUri('https://oauth.vk.com/access_token')
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($params);
            // Process response
            $response = $client->request();
            $responseData = $response->getBody();
            $responseData = Zend_Json::decode($responseData, Zend_Json::TYPE_ARRAY);
            $_SESSION['vk_access_token'] = $responseData['access_token'];
            $_SESSION['vk_email'] = $responseData['email'];
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }
    /*
     * Function to fetch user info from Vk
     * 
     * @return array
     */
    function fetch() {
        $params = array('access_token' => $_SESSION['vk_access_token'],'v' => '5.67','fields'=>'photo_50,first_name,last_name,bdate');

        try {
            // Need to use HTTPS
            $this->_url = 'https://api.vk.com/method/users.get?' . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $response = ob_get_contents();
            ob_end_clean();
            $data=json_decode($response);
            if(isset($data->error)) {
                unset($_SESSION['vk_access_token']);
                unset($_SESSION['vk_email']);
                print_r($data->error);die;
                //$this->_helper->redirector->gotoUrl('/sitelogin/auth/vk', array('prependBase' => false));
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $data;
    }
    /*
     * Function to return if linkedin integration is enabled or not
     * 
     * @return boolean
     */
    public function vkIntegrationEnabled() {
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $client_id = isset($vkSettings['clientId']) ? $vkSettings['clientId'] : 0;
        $client_secret = isset($vkSettings['clientSecret']) ? $vkSettings['clientSecret'] : 0;
        $loginEnable = isset($vkSettings['vkOptions']) ? $vkSettings['vkOptions'] : 0;
        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;
        return true;
    }
    
    public function vkIntegration() {
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $client_id = isset($vkSettings['clientId']) ? $vkSettings['clientId'] : 0;
        $client_secret = isset($vkSettings['clientSecret']) ? $vkSettings['clientSecret'] : 0;
       
        if (empty($client_id) || empty($client_secret))
            return false;
        return true;
    }
    public function vkButtonRender($action) {
        if (empty($action)) {
            return false;
        }
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $loginEnable = isset($vkSettings['vkOptions']) ? $vkSettings['vkOptions'] : 0;
        return (in_array($action, $loginEnable)) ? true : false;
    }
    
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }
}