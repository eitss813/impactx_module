<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Linkedin.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_Dbtable_Linkedin extends Engine_Db_Table {
    protected $_url;
    public function getApi() {
        if (isset($_SESSION['linkedin_access_token']) && !empty($_SESSION['linkedin_access_token'])) {
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
        $loginEnable = $this->linkedinIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if linkedin login is enable
        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $client_id = isset($linkedinSettings['clientId']) ? $linkedinSettings['clientId'] : 0;
        $client_secret = isset($linkedinSettings['clientSecret']) ? $linkedinSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('linkedin');
        try {
            $params = array('response_type' => 'code',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'scope' => 'r_basicprofile r_emailaddress',
                'state' => uniqid('', true), // unique long string
                'redirect_uri' => $redirect_uri,
            );
            // Authentication request
            $this->_url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
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
        $loginEnable = $this->linkedinIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }
        //Check if linkedin login is enable
        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $client_id = isset($linkedinSettings['clientId']) ? $linkedinSettings['clientId'] : 0;
        $client_secret = isset($linkedinSettings['clientSecret']) ? $linkedinSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('linkedin');
        $params = array('grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri,
            'scope' => 'r_basicprofile r_emailaddress'
        );
        // Access Token request
        $this->_url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
        // Tell streams to make a POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $response = ob_get_contents();
        ob_end_clean();
        $token = json_decode($response);

        $_SESSION['linkedin_access_token'] = $token->access_token;

        return true;
    }
    /*
     * Function to fetch user info from linkedin
     * 
     * @return array
     */
    function fetch() {
        $params = array('oauth2_access_token' => $_SESSION['linkedin_access_token'],
            'format' => 'json',
            'scope' => 'r_basicprofile r_emailaddress'
        );
        try {
            // Need to use HTTPS
            $this->_url = 'https://api.linkedin.com/v1/people/~:(id,firstName,lastName,headline,phoneNumbers,location,skills,educations,industry,email-address,pictureUrl,picture-urls::(original))?' . http_build_query($params);
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
    public function linkedinIntegrationEnabled() {
        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $client_id = isset($linkedinSettings['clientId']) ? $linkedinSettings['clientId'] : 0;
        $client_secret = isset($linkedinSettings['clientSecret']) ? $linkedinSettings['clientSecret'] : 0;
        $loginEnable = isset($linkedinSettings['linkedinOptions']) ? $linkedinSettings['linkedinOptions'] : 0;
        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;
        return true;
    }
    
    public function linkedinIntegration() {
        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $client_id = isset($linkedinSettings['clientId']) ? $linkedinSettings['clientId'] : 0;
        $client_secret = isset($linkedinSettings['clientSecret']) ? $linkedinSettings['clientSecret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;
        return true;
    }
    public function linkedinButtonRender($action) {
        if (empty($action)) {
            return false;
        }
        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $loginEnable = isset($linkedinSettings['linkedinOptions']) ? $linkedinSettings['linkedinOptions'] : 0;
        return (in_array($action, $loginEnable)) ? true : false;
    }
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }
}