<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Google.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_DbTable_Google extends Engine_Db_Table {

    public function getGoogleInstance() {
        return $this->getApi();
    }

    //If $_GET['code'] is empty, redirect user to google authentication page for code.
    //Code is required to aquire Access Token from google
    //Once we have access token, assign token to session variable
    //and we can get the user info then.
    public function getApi() {
        $loginEnable = $this->googleIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }

        $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
        $client_id = isset($googleSettings['clientId']) ? $googleSettings['clientId'] : 0;
        $client_secret = isset($googleSettings['clientSecret']) ? $googleSettings['clientSecret'] : 0;

        // Get Code
        try {
            $redirect_uri = Engine_Api::_()->sitelogin()->getGoogleRedirectUrl();
            $client = new Google_Client();
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->addScope("email");
            $client->addScope("profile");
            Zend_Registry::set('Google_Client', $client);

            $service = new Google_Service_Oauth2($client);
            Zend_Registry::set('Google_Service', $service);

            if (isset($_GET['code'])) {
                $client->authenticate($_GET['code'], 1);
                $_SESSION['google_access_token'] = $client->getAccessToken();
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
                exit;
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        // Set google_access_token
        try {
            if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
                $client->setAccessToken($_SESSION['google_access_token']);
            } else {
                $authUrl = $client->createAuthUrl();
            }
        } catch (Exception $ex) {
            unset($_SESSION['google_access_token']);
        }

        if (isset($authUrl))
            return $authUrl;

        // Get User Info
        try {
            $user = $service->userinfo->get();
            return $user;
        } catch (Google_Auth_Exception $ex) {
            unset($_SESSION['google_access_token']);
        }
    }

    /*
     * Function to return if google integration is enabled or not
     * 
     * @return boolean
     */

    public function googleIntegrationEnabled() {
        $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
        $client_id = isset($googleSettings['clientId']) ? $googleSettings['clientId'] : 0;
        $client_secret = isset($googleSettings['clientSecret']) ? $googleSettings['clientSecret'] : 0;
        $loginEnable = isset($googleSettings['googleOptions']) ? $googleSettings['googleOptions'] : 0;

        return !empty($client_id) && !empty($client_secret) && !empty($loginEnable);
    }
    public function googleIntegration() {
        $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
        $client_id = isset($googleSettings['clientId']) ? $googleSettings['clientId'] : 0;
        $client_secret = isset($googleSettings['clientSecret']) ? $googleSettings['clientSecret'] : 0;
        
        return !empty($client_id) && !empty($client_secret);
    }
    
    public function googleButtonRender($action) {
        if (empty($action)) {
            return false;
        }

        $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
        $loginEnable = isset($googleSettings['googleOptions']) ? $googleSettings['googleOptions'] : 0;

        return (in_array($action, $loginEnable)) ? true : false;
    }
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }

}
