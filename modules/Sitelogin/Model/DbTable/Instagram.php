<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Instagram.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_Dbtable_Instagram extends Engine_Db_Table {
    protected $_url;
    public function getApi() {
        if (isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token'])) {
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
        $loginEnable = $this->instagramIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if linkedin login is enable
        $instagramSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram;
        $client_id = isset($instagramSettings['clientId']) ? $instagramSettings['clientId'] : 0;
        $client_secret = isset($instagramSettings['clientSecret']) ? $instagramSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('instagram');
        try {
            $params = array('response_type' => 'code',
                'client_id' => $client_id,
                'state' => uniqid('', true), // unique long string
                'redirect_uri' => $redirect_uri,
            );
            // Authentication request
            $this->_url = 'https://api.instagram.com/oauth/authorize/?' . http_build_query($params);
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
        $loginEnable = $this->instagramIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if instagram login is enable
        $instagramSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram;
        $client_id = isset($instagramSettings['clientId']) ? $instagramSettings['clientId'] : 0;
        $client_secret = isset($instagramSettings['clientSecret']) ? $instagramSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('instagram');

        $params = array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'authorization_code',
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri,
        );
        try {
            $client = new Zend_Http_Client();
            $client->setUri('https://api.instagram.com/oauth/access_token')
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($params);
            // Process response
            $response = $client->request();
            $responseData = $response->getBody();
            $responseData = Zend_Json::decode($responseData, Zend_Json::TYPE_ARRAY);
            $_SESSION['instagram_access_token'] = $responseData['access_token'];
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }
    /*
     * Function to fetch user info from instagram
     * 
     * @return array
     */
    function fetch() {
        $params = array('access_token' => $_SESSION['instagram_access_token']);

        try {
            // Need to use HTTPS
            $this->_url = 'https://api.instagram.com/v1/users/self/?' . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $response = ob_get_contents();
            ob_end_clean();
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
    public function instagramIntegrationEnabled() {
        $instagramSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram;
        $client_id = isset($instagramSettings['clientId']) ? $instagramSettings['clientId'] : 0;
        $client_secret = isset($instagramSettings['clientSecret']) ? $instagramSettings['clientSecret'] : 0;
        $loginEnable = isset($instagramSettings['instagramOptions']) ? $instagramSettings['instagramOptions'] : 0;
        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;
        return true;
    }
    public function instagramIntegration() {
        $instagramSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram;
        $client_id = isset($instagramSettings['clientId']) ? $instagramSettings['clientId'] : 0;
        $client_secret = isset($instagramSettings['clientSecret']) ? $instagramSettings['clientSecret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;
        return true;
    }
    public function instagramButtonRender($action) {
        if (empty($action)) {
            return false;
        }
        $instagramSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram;
        $loginEnable = isset($instagramSettings['instagramOptions']) ? $instagramSettings['instagramOptions'] : 0;
        return (in_array($action, $loginEnable)) ? true : false;
    }
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }
}