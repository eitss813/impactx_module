<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Flickr.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Model_Dbtable_Flickr extends Engine_Db_Table {

    protected $_url;

    public function getApi() {
        if (isset($_SESSION['flickr_access_token']) && !empty($_SESSION['flickr_access_token'])) {
            return;
        }
        if (isset($_GET['oauth_verifier']) && !empty($_GET['oauth_verifier'])) {
            $tokenRecieved = $this->getAccessToken();
            return $tokenRecieved;
        } else {
            $this->getAuthorizationCode();
        }
    }

    /*
     * Function to fetch access code from Flickr
     * 
     */

    function getAuthorizationCode() {
        $loginEnable = $this->flickrIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }

        //Check if linkedin login is enable
        $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;
        $client_id = isset($flickrSettings['clientId']) ? $flickrSettings['clientId'] : 0;
        $client_secret = isset($flickrSettings['clientSecret']) ? $flickrSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('flickr');
        try {

            $requestTokenUrl = "https://www.flickr.com/services/oauth/request_token";
            $oauthTimestamp = time();
            $nonce = md5(mt_rand());
            $oauthSignatureMethod = "HMAC-SHA1";
            $oauthVersion = "1.0";
            $sigBase = "GET&" . rawurlencode($requestTokenUrl) . "&"
            . rawurlencode("oauth_consumer_key=" . rawurlencode($client_id)
            . "&oauth_nonce=" . rawurlencode($nonce)
            . "&oauth_signature_method=" . rawurlencode($oauthSignatureMethod)
            . "&oauth_timestamp=" . $oauthTimestamp
            . "&oauth_version=" . $oauthVersion);
            //. "&oauth_callback=".rawurlencode($redirect_uri);


            $sigKey = $client_secret . "&";
            $oauthSig = base64_encode(hash_hmac("sha1", $sigBase, $sigKey, true));

            $requestUrl = $requestTokenUrl . "?"
                . "oauth_consumer_key=" . rawurlencode($client_id)
                . "&oauth_nonce=" . rawurlencode($nonce)
                . "&oauth_signature_method=" . rawurlencode($oauthSignatureMethod)
                . "&oauth_timestamp=" . rawurlencode($oauthTimestamp)
                . "&oauth_version=" . rawurlencode($oauthVersion)
                . "&oauth_signature=" . rawurlencode($oauthSig);

            $response = file_get_contents($requestUrl);
            $response=explode('&',$response);
            $oauth_token=explode('=',$response[1]);
            $oauth_token_secret=explode('=',$response[2]); 
            $_SESSION['oauth_token_secret'] =  $oauth_token_secret[1];
           // Redirect user to authenticate
            $requestTokenUrl = "https://www.flickr.com/services/oauth/authorize";
            $requestUrl = $requestTokenUrl . "?"
                 . "oauth_token=" . $oauth_token[1]
                 . "&oauth_callback=".rawurlencode($redirect_uri)
                 . "&perms=read";

             // Redirect user to authenticate
             header("Location: $requestUrl");
            exit;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /*
     * Function to return access token from flickr
     * 
     * @return boolean
     */

    function getAccessToken() {
        $loginEnable = $this->flickrIntegrationEnabled();
        if (empty($loginEnable)) {
            return;
        }

        //Check if Flickr login is enable
        $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;
        $client_id = isset($flickrSettings['clientId']) ? $flickrSettings['clientId'] : 0;
        $client_secret = isset($flickrSettings['clientSecret']) ? $flickrSettings['clientSecret'] : 0;

        $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('flickr');

        $requestTokenUrl = "https://www.flickr.com/services/oauth/access_token";
        $oauthTimestamp = time();
        $nonce = md5(mt_rand());
        $oauthSignatureMethod = "HMAC-SHA1";
        $oauthVersion = "1.0";
        $oauth_verifier=$_GET['oauth_verifier'];
        $oauth_token=$_GET['oauth_token'];
        $oauth_consumer_key=$client_id;

        $sigBase = "GET&" . rawurlencode($requestTokenUrl) . "&"
            . rawurlencode("oauth_consumer_key=" .$client_id
            . "&oauth_nonce=" . $nonce
            . "&oauth_signature_method=" . $oauthSignatureMethod
            . "&oauth_timestamp=" . $oauthTimestamp
            . "&oauth_token=". $oauth_token  
            . "&oauth_verifier=". $oauth_verifier            
            . "&oauth_version=" . $oauthVersion);

        $oauth_token_secret = $_SESSION['oauth_token_secret']; 
        $sigKey = $client_secret . "&".$oauth_token_secret;
        $oauthSig = base64_encode(hash_hmac("sha1", $sigBase, $sigKey, true));

        $requestUrl = $requestTokenUrl . "?"
                . "oauth_consumer_key=" . rawurlencode($client_id)
                . "&oauth_nonce=" . rawurlencode($nonce)
                . "&oauth_signature=" . urlencode($oauthSig)
                . "&oauth_signature_method=" . rawurlencode($oauthSignatureMethod)
                . "&oauth_timestamp=" . $oauthTimestamp
                . "&oauth_token=" . rawurlencode($oauth_token)
                . "&oauth_verifier=" . rawurlencode($oauth_verifier)                
                . "&oauth_version=" . rawurlencode($oauthVersion);                

        $response = file_get_contents($requestUrl);
        $response=explode('&',$response);
        $oauth_token=explode('=',$response[1]);
        $oauth_token_secret=explode('=',$response[2]);
        $user_nsid=explode('=',$response[3]);
        $_SESSION['flickr_access_token'] = $oauth_token[1];
        $_SESSION['access_token_secret'] = $oauth_token_secret[1];
        $_SESSION['user_nsid']=$user_nsid[1];
          
        return true;
    }

    /*
     * Function to fetch user info from Flickr
     * 
     * @return array
     */

    function fetch() {
        try {
            //Check if Flickr login is enable
            $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;
            $client_id = isset($flickrSettings['clientId']) ? $flickrSettings['clientId'] : 0;
            $client_secret = isset($flickrSettings['clientSecret']) ? $flickrSettings['clientSecret'] : 0;

            $redirect_uri = Engine_Api::_()->sitelogin()->getRedirectUrl('flickr');

            $requestTokenUrl = "https://api.flickr.com/services/rest";
            $oauthTimestamp = time();
            $nonce = md5(mt_rand());
            $oauthSignatureMethod = "HMAC-SHA1";
            $oauthVersion = "1.0";
            $oauth_token=$_SESSION['flickr_access_token'];
            $oauth_consumer_key=$client_id;
            $user_id=$_SESSION['user_nsid'];

            $sigBase = "GET&" . rawurlencode($requestTokenUrl) . "&"
            . rawurlencode("format=json"
            . "&method=flickr.people.getInfo"    
            . "&nojsoncallback=1"
            . "&oauth_consumer_key=" . $oauth_consumer_key
            . "&oauth_nonce=".$nonce 
            . "&oauth_signature_method=".$oauthSignatureMethod           
            . "&oauth_timestamp=".$oauthTimestamp
            . "&oauth_token=".$oauth_token            
            . "&oauth_version=". $oauthVersion
            . "&user_id=". $user_id);

            $access_token_secret = $_SESSION['access_token_secret']; 
            $sigKey = $client_secret . "&".$access_token_secret;
            $oauthSig = base64_encode(hash_hmac("sha1", $sigBase, $sigKey, true));

            $requestUrl = $requestTokenUrl . "?"
            . "format=json"
            . "&method=flickr.people.getInfo"
            . "&nojsoncallback=1"
            . "&oauth_consumer_key=" . rawurlencode($oauth_consumer_key)
            . "&oauth_nonce=". rawurlencode($nonce) 
            . "&oauth_signature_method=". rawurlencode($oauthSignatureMethod)           
            . "&oauth_timestamp=". rawurlencode($oauthTimestamp)
            . "&oauth_token=". rawurlencode($oauth_token)            
            . "&oauth_version=". rawurlencode($oauthVersion)
            . "&oauth_signature=". rawurlencode($oauthSig)
            . "&user_id=". $user_id;                

            $response = file_get_contents($requestUrl);
            $data=json_decode($response);
            $dataResponse = array();
            $dataResponse['id'] = $data->person->nsid;
            $dataResponse['name']= $data->person->realname->_content;
            $dataResponse['iconserver'] = $data->person->iconserver;
            $dataResponse['iconfarm'] = $data->person->iconfarm;
            $photoUrl = "http://farm".$dataResponse['iconfarm'].".staticflickr.com/".$dataResponse['iconserver']."/buddyicons/".$user_id.".jpg";
            $dataResponse['photoUrl'] = $photoUrl;

        } catch (Exception $ex) {
            throw $ex;
        }
        return $dataResponse;
    }

    /*
     * Function to return if linkedin integration is enabled or not
     * 
     * @return boolean
     */

    public function flickrIntegrationEnabled() {
        $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;

        $client_id = isset($flickrSettings['clientId']) ? $flickrSettings['clientId'] : 0;
        $client_secret = isset($flickrSettings['clientSecret']) ? $flickrSettings['clientSecret'] : 0;
        $loginEnable = isset($flickrSettings['flickrOptions']) ? $flickrSettings['flickrOptions'] : 0;

        if (empty($client_id) || empty($client_secret) || empty($loginEnable))
            return false;

        return true;
    }
    public function flickrIntegration() {
        $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;

        $client_id = isset($flickrSettings['clientId']) ? $flickrSettings['clientId'] : 0;
        $client_secret = isset($flickrSettings['clientSecret']) ? $flickrSettings['clientSecret'] : 0;
        
        if (empty($client_id) || empty($client_secret))
            return false;

        return true;
    }
    public function flickrButtonRender($action) {
        if (empty($action)) {
            return false;
        }

        $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;
        $loginEnable = isset($flickrSettings['flickrOptions']) ? $flickrSettings['flickrOptions'] : 0;

        return (in_array($action, $loginEnable)) ? true : false;
    }
    
    public function getCount() {
      $select = $this->select()
      ->from($this->info('name'), array('COUNT(user_id) as count'));
      $results = $this->fetchRow($select);
      return $results->count;
    }

}
