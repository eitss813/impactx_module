<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    LinkController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_LinkController extends Core_Controller_Action_Standard {

    protected $_viewer_id;
    protected $_viewer;

    public function init() {
        
        $this->_viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);

        $this->_viewer_id = $viewer_id;
    }

    public function linkedinAction() {   

        $url = $_SESSION['redirectURL'];
        $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');
        $linkedinEnabled = $linkedinTable->linkedinIntegrationEnabled();
        
        if (empty($linkedinEnabled))
            return $this->_redirect($url, array('prependBase' => false));

        $linkedinApi = $linkedinTable->getApi();
        $userLoginDetails = $linkedinTable->fetch();

        try {
            
            $linkedinTable->delete(array("linkedin_id = ?" => $userLoginDetails->id));
            $linkedinTable->insert(array(
                'user_id' => $this->_viewer_id,
                'linkedin_id' => $userLoginDetails->id,
                'access_token' => $_SESSION['linkedin_access_token'],
                'expires' => 0,
            ));
        } catch (Exception $e) {
            if ('development' == APPLICATION_ENV) {
                echo $e;
            }
        }
        if(empty($url)){
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $url = $view->baseUrl() . '/profile/' . $this->_viewer->username ;
        }
        
        return $this->_redirect($url, array('prependBase' => false));
    }

    public function googleAction() {
        $url = $_SESSION['redirectURL'];
        $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
        $googleEnabled = Engine_Api::_()->getDbtable('google', 'sitelogin')->googleIntegrationEnabled();

        if (empty($googleEnabled))
            return $this->_redirect($url, array('prependBase' => false));

        try {
            $authUrl = $googleTable->getGoogleInstance();
        } catch (Exception $e) {
            return $this->_redirect($url, array('prependBase' => false));
        }
        $loggedin = false;
        if (!$authUrl) {
            if (isset($_SESSION['google_access_token']))
                unset($_SESSION['google_access_token']);
            return $this->_redirect($url, array('prependBase' => false));
        }
            
        if (!isset($_SESSION['google_access_token'])) {
            try {
                return $this->_redirect($authUrl, array('prependBase' => false));
            } catch (Exception $e) {
                print_r($authUrl);
                die("exception : " . $e);
            }
        } else {
            $google_id = $authUrl->id;
            //Login user
            if (!empty($google_id)) {
                try {
            
                    $googleTable->delete(array("google_id = ?" => $google_id));
                    $googleTable->insert(array(
                        'user_id' => $this->_viewer_id,
                        'google_id' => $google_id,
                        'access_token' => $_SESSION['google_access_token'],
                        'expires' => 0,
                    ));
                } catch (Exception $e) {
                    if ('development' == APPLICATION_ENV) {
                        echo $e;
                    }
                }
            }
        }
        // Redirect to referer page
        $url = $_SESSION['redirectURL'];
        return $this->_redirect($url, array('prependBase' => false));
    }

    public function instagramAction() {
        
        $url = $_SESSION['redirectURL'];

        $instagramEnabled = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->instagramIntegrationEnabled();
        if (empty($instagramEnabled))
            return $this->_redirect($url, array('prependBase' => false));

        $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sitelogin');
        $instagramApi = $instagramTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        if (isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->fetch();
        }

        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['instagram_access_token']))
                unset($_SESSION['instagram_access_token']);
            return $this->_redirect($url, array('prependBase' => false));
        }
        $instagram_id = $userDetails->data->id;
        //Login user
        if (!empty($instagram_id)) {
            try {
            
                $instagramTable->delete(array("instagram_id = ?" => $instagram_id));
                $instagramTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'instagram_id' => $instagram_id,
                    'access_token' => $_SESSION['instagram_access_token'],
                    'expires' => 0,
                ));
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                    echo $e;
                }
            }   
        }
            
        return $this->_redirect($url, array('prependBase' => false));

    }

    public function pinterestAction() {

        $url = $_SESSION['redirectURL'];
        $pinterestEnabled = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->pinterestIntegrationEnabled();
        if (empty($pinterestEnabled))
            return $this->_redirect($url, array('prependBase' => false));

        $pinterestTable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin');
        $pinterestApi = $pinterestTable->getApi();

        // Fetch info from pinterest
        if (isset($_SESSION['pinterest_access_token']) && !empty($_SESSION['pinterest_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['pinterest_access_token']))
                unset($_SESSION['pinterest_access_token']);
            return $this->_redirect($url, array('prependBase' => false));
        }

        $pinterest_id = $userDetails->data->id;
        //Login user
        if (!empty($pinterest_id)) {
            try {
            
                $pinterestTable->delete(array("pinterest_id = ?" => $pinterest_id));
                $pinterestTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'pinterest_id' => $pinterest_id,
                    'access_token' => $_SESSION['pinterest_access_token'],
                    'expires' => 0,
                ));
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                     echo $e;
                }
            }
        }

        return $this->_redirect($url, array('prependBase' => false));
    }

    public function flickrAction() {
        $url = $_SESSION['redirectURL'];
        $flickrEnabled = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->flickrIntegrationEnabled();
        if (empty($flickrEnabled))
            return $this->_redirect($url, array('prependBase' => false));

        $flickrTable = Engine_Api::_()->getDbtable('flickr', 'sitelogin');
        $flickrApi = $flickrTable->getApi();

        // Fetch info from flickr
        if (isset($_SESSION['flickr_access_token']) && !empty($_SESSION['flickr_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->fetch();
        }

        if (!$userDetails || !isset($userDetails['id'])) {
            unset($_SESSION['flickr_access_token']);
            unset($_SESSION['access_token_secret']);
            unset($_SESSION['user_nsid']);
            return $this->_redirect($url, array('prependBase' => false));
        }
        $flickr_id = $userDetails['id'];
        //Login user
        if (!empty($flickr_id)) {
            try {
            
                $flickrTable->delete(array("flickr_id = ?" => $flickr_id));
                $flickrTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'flickr_id' => $flickr_id,
                    'access_token' => $_SESSION['flickr_access_token'],
                    'expires' => 0,
                ));
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                     echo $e;
                }
            }
        }

        return $this->_redirect($url, array('prependBase' => false));

    }

    public function yahooAction() {
        $url = $_SESSION['redirectURL'];
        $yahooEnabled = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->yahooIntegrationEnabled();
        if (empty($yahooEnabled))
            return $this->_redirect($url, array('prependBase' => false));

        $yahooTable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin');
        $yahooApi = $yahooTable->getApi();

        // Fetch info from Yahoo
        if (isset($_SESSION['yahoo_access_token']) && !empty($_SESSION['yahoo_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->fetch();
        }

        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['yahoo_access_token']))
                unset($_SESSION['yahoo_access_token']);
            return $this->_redirect($url, array('prependBase' => false));
        }

        $yahoo_id = $userDetails->profile->guid;
        //Login user
        if (!empty($yahoo_id)) {
            try {
            
                $yahooTable->delete(array("yahoo_id = ?" => $yahoo_id));
                $yahooTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'yahoo_id' => $yahoo_id,
                    'access_token' => $_SESSION['yahoo_access_token'],
                    'expires' => 0,
                ));
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                     echo $e;
                }
            }
        }

        return $this->_redirect($url, array('prependBase' => false));
    }

    public function outlookAction() {
        $url = $_SESSION['redirectURL'];
        $outlookEnabled = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->outlookIntegrationEnabled();
        if (empty($outlookEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);

        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $quickSignup = isset($outlookSettings['quickEnable']) ? $outlookSettings['quickEnable'] : 0;
        $outlookTable = Engine_Api::_()->getDbtable('outlook', 'sitelogin');
        $outlookApi = $outlookTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        // Fetch info from outlook
        if (isset($_SESSION['outlook_access_token']) && !empty($_SESSION['outlook_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->fetch();
        }

        if (!$userDetails || isset($userDetails->error)) {
            if (isset($_SESSION['outlook_access_token']))
                unset($_SESSION['outlook_access_token']);
            return $this->_redirect($url, array('prependBase' => false));
        }
        $outlook_id = $userDetails->id;
        //Login user
        if (!empty($outlook_id)) {
            try {
            
                $outlookTable->delete(array("outlook_id = ?" => $outlook_id));
                $outlookTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'outlook_id' => $outlook_id,
                    'access_token' => $_SESSION['outlook_access_token'],
                    'expires' => 0,
                ));
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                     echo $e;
                }
            }
        }
        return $this->_redirect($url, array('prependBase' => false));
    }

    public function vkAction() {

        $url = $_SESSION['redirectURL'];
        $vkEnabled = Engine_Api::_()->getDbtable('vk', 'sitelogin')->vkIntegrationEnabled();
        if (empty($vkEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $quickSignup = isset($vkSettings['quickEnable']) ? $vkSettings['quickEnable'] : 0;
        $vkTable = Engine_Api::_()->getDbtable('vk', 'sitelogin');
        $vkApi = $vkTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        // Fetch info from vk
        if (isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('vk', 'sitelogin')->fetch();

        }

        if (!$userDetails || isset($userDetails->error)) {
            unset($_SESSION['vk_access_token']);
            unset($_SESSION['email']);
            return $this->_redirect($url, array('prependBase' => false));
        }
        $vk_id = $userDetails->response[0]->id;
        //Login user
        if (!empty($vk_id)) {
            try {                
                $vkTable->delete(array("vk_id = ?" => $vk_id));
                $vkTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'vk_id' => $vk_id,
                    'access_token' => $_SESSION['vk_access_token'],
                    'expires' => 0,
                ));
            } catch (Exception $e) {
                if ('development' == APPLICATION_ENV) {
                     echo $e; die;
                }
            }
        }
        return $this->_redirect($url, array('prependBase' => false));
    }

    public function facebookAction() {
        $referrer = $_SESSION['redirectURL'];
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['facebook_lock']);
            unset($_SESSION['facebook_uid']);
        }

        $viewer = $this->_viewer;
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $db = Engine_Db_Table::getDefaultAdapter();
        // Enabled?
        if (!$facebook) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        //$referrer = $view->baseUrl() . '/profile/' . $viewer->username ;
        // Already connected
        if ($facebook->getUser()) {
            
            $signedRequest = $facebook->getSignedRequest();
            if (isset($signedRequest['code']))
            $code = $signedRequest['code'];
            // Check for facebook user
            $facebookInfo = $facebookTable->select()
                    ->from($facebookTable)
                    ->where('facebook_uid = ?', $facebook->getUser())
                    ->limit(1)
                    ->query()
                    ->fetch();

            if (!empty($facebookInfo) && $facebookInfo['user_id'] != $viewer->getIdentity()) {
                // Redirect to referer page
                $url = $referrer;
                $parsedUrl = parse_url($url);
                $separator = ($parsedUrl['query'] == NULL) ? '?' : '&';
                $url .= $separator . 'already_integrated_fb_account=1';
                $facebook->clearAllPersistentData();
                return $this->_redirect($url, array('prependBase' => false));
            }

            // Attempt to connect account
            $info = $facebookTable->select()
                    ->from($facebookTable)
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetch();

            if (empty($info)) {
                $facebookTable->insert(array(
                    'user_id' => $viewer->getIdentity(),
                    'facebook_uid' => $facebook->getUser(),
                    'access_token' => $facebook->getAccessToken(),
                    'code' => $code,
                    'expires' => 0,
                ));
            } else {
                // Save info to db
                $facebookTable->update(array(
                    'facebook_uid' => $facebook->getUser(),
                    'access_token' => $facebook->getAccessToken(),
                    'code' => $code,
                    'expires' => 0,
                        ), array(
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }
            // Redirect to referer page
            return $this->_redirect($referrer, array('prependBase' => false));
        }

        // Not connected
        else {
            // Okay
            if (!empty($_GET['code'])) {
                $facebook->setPersistentData('code', $_GET['code']);
                // This doesn't seem to be necessary anymore, it's probably
                // being handled in the api initialization
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }

            // Error
            else if (!empty($_GET['error'])) {
                // @todo maybe display a message?
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }

            // Redirect to auth page
            else {
                $url = $facebook->getLoginUrl(array(
                    'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://')
                    . $_SERVER['HTTP_HOST'] . $this->view->url(),
                    'scope' => join(',', array(
                        'email',
                        'user_birthday',
                    )),
                ));
                return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
            }
        }
    }

    public function twitterAction() {
        $redirecturl = $_SESSION['redirectURL'];
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['twitter_lock']);
            unset($_SESSION['twitter_token']);
            unset($_SESSION['twitter_secret']);
            unset($_SESSION['twitter_token2']);
            unset($_SESSION['twitter_secret2']);
        }

        if ($this->_getParam('denied')) {
            $this->view->error = 'Access Denied!';
            return;
        }

        // Setup
        $viewer = $this->_viewer;
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        // Check
        if (!$twitter || !$twitterOauth) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Connect
        try {

            $accountInfo = null;
            if (isset($_SESSION['twitter_token'], $_SESSION['twitter_secret'], $_GET['oauth_verifier'])) {
                $twitterOauth->getAccessToken('https://twitter.com/oauth/access_token', $_GET['oauth_verifier']);

                $_SESSION['twitter_token2'] = $twitter_token = $twitterOauth->getToken();
                $_SESSION['twitter_secret2'] = $twitter_secret = $twitterOauth->getTokenSecret();

                // Reload api?
                $twitterTable->clearApi();
                $twitter = $twitterTable->getApi();

                // Get account info
                $accountInfo = $twitter->account->verify_credentials();

                // Save to settings table (if logged in)
                if ($viewer->getIdentity()) {
                    $info = $twitterTable->select()
                            ->from($twitterTable)
                            ->where('user_id = ?', $viewer->getIdentity())
                            ->query()
                            ->fetch();

                    if (!empty($info)) {
                        $twitterTable->update(array(
                            'twitter_uid' => $accountInfo->id,
                            'twitter_token' => $twitter_token,
                            'twitter_secret' => $twitter_secret,
                                ), array(
                            'user_id = ?' => $viewer->getIdentity(),
                        ));
                    } else {
                        $twitterTable->insert(array(
                            'user_id' => $viewer->getIdentity(),
                            'twitter_uid' => $accountInfo->id,
                            'twitter_token' => $twitter_token,
                            'twitter_secret' => $twitter_secret,
                        ));
                    }

                    // Redirect
                    //$referrer = $view->baseUrl() . '/profile/' . $viewer->username ;
                    return $this->_helper->redirector->gotoUrl($redirecturl, array('prependBase' => false));
                }
            } else {

                unset($_SESSION['twitter_token']);
                unset($_SESSION['twitter_secret']);
                unset($_SESSION['twitter_token2']);
                unset($_SESSION['twitter_secret2']);

                // Reload api?
                $twitterTable->clearApi();
                $twitter = $twitterTable->getApi();
                $twitterOauth = $twitterTable->getOauth();

                // Connect account
                $twitterOauth->getRequestToken('https://twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url());

                $_SESSION['twitter_token'] = $twitterOauth->getToken();
                $_SESSION['twitter_secret'] = $twitterOauth->getTokenSecret();

                $url = $twitterOauth->getAuthorizeUrl('http://twitter.com/oauth/authenticate');

                return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
            }
        } catch (Services_Twitter_Exception $e) {
            if (in_array($e->getCode(), array(500, 502, 503))) {
                $this->view->error = 'Twitter is currently experiencing technical issues, please try again later.';
                return;
            } else {
                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

}