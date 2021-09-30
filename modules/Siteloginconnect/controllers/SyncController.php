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
class Siteloginconnect_SyncController extends Core_Controller_Action_Standard {

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
        $linkedinEnabled = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->linkedinIntegrationEnabled();
        if (empty($linkedinEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);

        $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');

        $linkedinApi = $linkedinTable->getApi();
        $userLoginDetails = $linkedinTable->fetch();

        try {
            if($userLoginDetails->id){

                $linkedinInfo = $linkedinTable->select()
                ->from($linkedinTable)
                ->where('linkedin_id = ?', $userLoginDetails->id)
                ->limit(1)
                ->query()
                ->fetch();

                if (!empty($linkedinInfo) && $linkedinInfo['user_id'] != $viewer->getIdentity()) {
                    $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'index','already_integrated' => 1,'social_site'=>'linkedin'), 'siteloginconnect_extended', true);
                }

                $linkedinTable->insert(array(
                    'user_id' => $this->_viewer_id,
                    'linkedin_id' => $userLoginDetails->id,
                    'access_token' => $_SESSION['linkedin_access_token'],
                    'expires' => 0,
                ));
            }                

        } catch (Exception $e) {
            if ('development' == APPLICATION_ENV) {
                echo $e;
            }
        }
        $linkedinUserdetails=urlencode(json_encode($userLoginDetails));
        return $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'selectdata','userdetails' => $linkedinUserdetails,'social_site'=>'linkedin'), 'siteloginconnect_extended', true);
    }

    public function instagramAction() {

        $instagramEnabled = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->instagramIntegrationEnabled();
        if (empty($instagramEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);

        
        $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sitelogin');
        $instagramApi = $instagramTable->getApi();
        if (isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token'])) {
            $userDetails = $instagramTable->fetch();
        }
        try {
             if($userDetails->data->id) {
                $instagramInfo = $instagramTable->select()
                ->from($instagramTable)
                ->where('instagram_id = ?', $userDetails->data->id)
                ->limit(1)
                ->query()
                ->fetch();

                if (!empty($instagramInfo) && $instagramInfo['user_id'] != $viewer->getIdentity()) {
                    $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'index','already_integrated' => 1,'social_site'=>'instagram'), 'siteloginconnect_extended', true);
                }

                $instagramTable->insert(array(
                'user_id' => $this->_viewer_id,
                'instagram_id' => $userDetails->data->id,
                'access_token' => $_SESSION['instagram_access_token'],
                'expires' => 0,
                ));
            }    

        } catch (Exception $e) {
            if ('development' == APPLICATION_ENV) {
                echo $e;
            }
        }

        $instagramuserDetails=urlencode(json_encode($userDetails));
        return $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'selectdata','userdetails' => $instagramuserDetails,'social_site'=>'instagram'), 'siteloginconnect_extended', true);
    }

    public function facebookAction() {

        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['facebook_lock']);
            unset($_SESSION['facebook_uid']);
        }

        $viewer = $this->_viewer;
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
        $facebookInviteApi = Engine_Api::_()->getApi('Facebook_Facebookinvite', 'seaocore');
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $db = Engine_Db_Table::getDefaultAdapter();
        // Enabled?
        if (!$facebook) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $referrer = $view->baseUrl() . '/profile/' . $viewer->username ;
        // Already connected
        if ($facebook->getUser()) {
            // Check for facebook user
            $facebookInfo = $facebookTable->select()
            ->from($facebookTable)
            ->where('facebook_uid = ?', $facebook->getUser())
            ->limit(1)
            ->query()
            ->fetch();

            if (!empty($facebookInfo) && $facebookInfo['user_id'] != $viewer->getIdentity()) {
                $facebook->clearAllPersistentData();
                $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'index','already_integrated' => 1,'social_site'=>'facebook'), 'siteloginconnect_extended', true);
            }
            
            $signedRequest = $facebook->getSignedRequest();
            if (isset($signedRequest['code']))
            $code = $signedRequest['code'];
            if (!empty($_GET['code'])) {
                $code = $_GET['code'];
            }
                //GETTING THE NEW ACCESS TOKEN FOR THIS REQUEST.
            $result = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB($code);
            if(!empty($result))
              $result=json_decode($result);


            $facebook->setPersistentData('code', $code);

            if (!empty($result->access_token)) {
                $facebook->setAccessToken($result->access_token);
                $facebook->setPersistentData('access_token', $facebook->getAccessToken());
            } else {
                $access_token = $facebook->getAccessToken();
            }
            if (empty($access_token)) {
                $access_token = $facebook->getAccessToken();
            }
            // Check for facebook user
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
                    'access_token' => $access_token,
                    'code' => $code,
                    'expires' => 0,
                ));
            } else {
                // Save info to db
                $facebookTable->update(array(
                    'facebook_uid' => $facebook->getUser(),
                    'access_token' => $access_token,
                    'code' => $code,
                    'expires' => 0,
                        ), array(
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }
            $_SESSION['facebook_uid'] = $facebook->getUser();
            //$session->aaf_fbaccess_token = $response_temp[1];
            $session->aaf_fbaccess_token = $access_token;
            //print_r($facebook);die("9999");
            $apiInfo = $facebook->api('v2.12/me?fields=name,gender,email,locale,age_range,about,website,birthday,education,hometown,location,religion,political,address,first_name,interested_in,relationship_status,work,photos');
            $apiInfoDetails=urlencode(json_encode($apiInfo));
            return $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'selectdata','userdetails' => $apiInfoDetails,'social_site'=>'facebook'), 'siteloginconnect_extended', true);
    
        }

        // Not connected
        else {
            // Okay
            if (!empty($_GET['code'])) {
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

                //check for twitter user
                $twitterInfo = $twitterTable->select()
                ->from($twitterTable)
                ->where('twitter_uid = ?', $accountInfo->id)
                ->limit(1)
                ->query()
                ->fetch();

                if (!empty($twitterInfo) && $twitterInfo['user_id'] != $viewer->getIdentity()) {
                    $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'index','already_integrated' => 1,'social_site'=>'twitter'), 'siteloginconnect_extended', true);
                }
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

                    $twitteruserDetails=urlencode(json_encode($accountInfo));
                    return $this->_helper->redirector->gotoRoute(array('controller'=>'index','action'=>'selectdata','userdetails' => $twitteruserDetails,'social_site'=>'twitter'), 'siteloginconnect_extended', true);
    
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