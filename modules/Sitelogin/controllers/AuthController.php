<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AuthController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_AuthController extends Core_Controller_Action_Standard {
    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if ($viewer_id)
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    public function loginAction() {
        // Already logged in
        $this->view->inwidget=true;
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are already signed in.');
            if (null === $this->_helper->contextSwitch->getCurrentContext()) {
                $this->_helper->redirector->gotoRoute(array(), 'default', true);
                ;
            }
            return;
        }
        // Make form
        $this->view->form = $form = new User_Form_Login();
        $form->setAction($this->view->url(array('return_url' => null), 'user_login'));
        $form->populate(array(
            'return_url' => $this->_getParam('return_url'),
        ));
        // Render
        $disableContent = $this->_getParam('disableContent', 0);
        if (!$disableContent) {
            $this->_helper->content
                    ->setEnabled()
            ;
        }
        // Not a post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
            return;
        }
        // Form not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        // Check login creds
        extract($form->getValues()); // $email, $password, $remember
        $user_table = Engine_Api::_()->getDbtable('users', 'user');
        $user_select = $user_table->select()
                ->where('email = ?', $email);          // If post exists
        $user = $user_table->fetchRow($user_select);
        // Get ip address
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        // Check if user exists
        if (empty($user)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.');
            $form->addError(Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));
            // Register login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'no-member',
            ));
            return;
        }
        $isValidPassword = Engine_Api::_()->user()->checkCredential($user->getIdentity(), $password);
        if (!$isValidPassword) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
            $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));
            // Register bad password login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'user_id' => $user->getIdentity(),
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'bad-password',
            ));
            return;
        }
        // Check if user is verified and enabled
        if (!$user->enabled) {
            if (!$user->verified) {
                $this->view->status = false;
                $resend_url = $this->_helper->url->url(array('action' => 'resend', 'email' => $email), 'user_signup', true);
                $translate = Zend_Registry::get('Zend_Translate');
                $error = $translate->translate('This account still requires either email verification.');
                $error .= ' ';
                $error .= sprintf($translate->translate('Click <a href="%s">here</a> to resend the email.'), $resend_url);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));
                return;
            } else if (!$user->approved) {
                $this->view->status = false;
                $translate = Zend_Registry::get('Zend_Translate');
                $error = $translate->translate('This account still requires admin approval.');
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));
                return;
            }
            // Should be handled by hooks or payment
            //return;
        }
        // Handle subscriptions
        if (Engine_Api::_()->hasModuleBootstrap('payment')) {
            // Check for the user's plan
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            if (!$subscriptionsTable->check($user)) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'unpaid',
                ));
                // Redirect to subscription page
                $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                $subscriptionSession->unsetAll();
                $subscriptionSession->user_id = $user->getIdentity();
                return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
                            'controller' => 'subscription', 'action' => 'index'), 'default', true);
            }
        }
        // Run pre login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
        foreach ((array) $event->getResponses() as $response) {
            if (is_array($response)) {
                if (!empty($response['error']) && !empty($response['message'])) {
                    $form->addError($response['message']);
                } else if (!empty($response['redirect'])) {
                    $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
                } else {
                    continue;
                }
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'third-party',
                ));
                // Return
                return;
            }
        }
        // Version 3 Import compatibility
        if (empty($user->password)) {
            $compat = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.compatibility.password');
            $migration = null;
            try {
                $migration = Engine_Db_Table::getDefaultAdapter()->select()
                        ->from('engine4_user_migration')
                        ->where('user_id = ?', $user->getIdentity())
                        ->limit(1)
                        ->query()
                        ->fetch();
            } catch (Exception $e) {
                $migration = null;
                $compat = null;
            }
            if (!$migration) {
                $compat = null;
            }
            if ($compat == 'import-version-3') {
                // Version 3 authentication
                $cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
                if ($cryptedPassword === $migration['user_password']) {
                    // Regenerate the user password using the given password
                    $user->salt = (string) rand(1000000, 9999999);
                    $user->password = $password;
                    $user->save();
                    Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                    // @todo should we delete the old migration row?
                } else {
                    $this->view->status = false;
                    $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));
                    return;
                }
                // End Version 3 authentication
            } else {
                $form->addError('There appears to be a problem logging in. Please reset your password with the Forgot Password link.');
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'v3-migration',
                ));
                return;
            }
        }
        // Normal authentication
        else {
            $authResult = Engine_Api::_()->user()->authenticate($email, $password);
            $authCode = $authResult->getCode();
            Engine_Api::_()->user()->setViewer();
            if ($authCode != Zend_Auth_Result::SUCCESS) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'bad-password',
                ));
                return;
            }
        }
        // -- Success! --
        // Register login
        $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
        $loginTable->insert(array(
            'user_id' => $user->getIdentity(),
            'email' => $email,
            'ip' => $ipExpr,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'state' => 'success',
            'active' => true,
        ));
        $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();
        // Remember
        if ($remember) {
            $lifetime = 1209600; // Two weeks
            Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
            Zend_Session::rememberMe($lifetime);
        }else{
            // hack to make to session for 30days instead of changing the php session
            $lifetime = 2592000; // 30 days
            Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
            Zend_Session::rememberMe($lifetime);
        }
        // Increment sign-in count
        Engine_Api::_()->getDbtable('statistics', 'core')
                ->increment('user.logins');
        // Test activity @todo remove
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $viewer->lastlogin_date = date("Y-m-d H:i:s");
            if ('cli' !== PHP_SAPI) {
                $viewer->lastlogin_ip = $ipExpr;
            }
            $viewer->save();
            Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($viewer, $viewer, 'login');
        }
        // Assign sid to view for json context
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Login successful');
        $this->view->sid = Zend_Session::getId();
        $this->view->sname = Zend_Session::getOptions('name');
        // Run post login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);
        // Do redirection only if normal context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            // Redirect by form
            $uri = $form->getValue('return_url');
            if ($uri) {
                if (substr($uri, 0, 3) == '64-') {
                    $uri = base64_decode(substr($uri, 3));
                }
                return $this->_redirect($uri, array('prependBase' => false));
            }
            // Redirect by session
            $session = new Zend_Session_Namespace('Redirect');
            if (isset($session->uri)) {
                $uri = $session->uri;
                $opts = $session->options;
                $session->unsetAll();
                return $this->_redirect($uri, $opts);
            } else if (isset($session->route)) {
                $session->unsetAll();
                return $this->_helper->redirector->gotoRoute($session->params, $session->route, $session->reset);
            }
            // Redirect by hook
            foreach ((array) $event->getResponses() as $response) {
                if (is_array($response)) {
                    if (!empty($response['error']) && !empty($response['message'])) {
                        return $form->addError($response['message']);
                    } else if (!empty($response['redirect'])) {
                        return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
                    }
                }
            }
            // Just redirect to home
            return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }
    }
    public function googleAction() {
        $value = $this->_getAllParams();
        if (!isset($value['google_connected']) && empty($value['google_connected'])) {
            unset($_SESSION['google_access_token']);
            unset($_SESSION['google_signup']);
        }
        $googleEnabled = Engine_Api::_()->getDbtable('google', 'sitelogin')->googleIntegrationEnabled();
        if (empty($googleEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
        $quickSignup = isset($googleSettings['quickEnable']) ? $googleSettings['quickEnable'] : 0;
        $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        try {
            $authUrl = $googleTable->getGoogleInstance();
        } catch (Exception $e) {
            if (isset($_SESSION['google_access_token']))
                unset($_SESSION['google_access_token']);
            return $this->_helper->redirector->gotoRoute(array('action'=>'home'), 'user_general', true);
        }

        $loggedin = false;
        if (!$authUrl) {
            if (isset($_SESSION['google_access_token']))
                unset($_SESSION['google_access_token']);
            return $this->_helper->redirector->gotoRoute(array('action'=>'home'), 'user_general', true);
        }
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        try {
            //Hit the google url for access_token
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
                    $loggedin = $this->_getUserLogin($google_id, "google");
                }
                //User not logged in do signup
                if (!$loggedin) {
                    if (!empty($quickSignup)) {
                        $_SESSION['google_signup'] = true;
                        //Save the info of user returned by google
                        $user = $this->saveUser($authUrl, 'google');
                        Engine_Api::_()->user()->setViewer($user);
                        Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                        unset($_SESSION['google_signup']);
                        unset($_SESSION['google_access_token']);
                        $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 1);
                        // Redirect to referer page
                        if ($redirectlink == 2) {
                            $url = $view->baseUrl() . '/';
                        } elseif ($redirectlink == 1) {
                            $url = $view->baseUrl() . '/profile/' . $user->getIdentity();
                        } elseif ($redirectlink == 3) {
                            $url = $view->baseUrl() . '/members/edit/profile';
                        } else {
                            $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
                            $url = $view->baseUrl() . $customeUrl;
                        }
                        return $this->_redirect($url, array('prependBase' => false));
                    } else {
                        $_SESSION['google_signup'] = true;
                        return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
                    }
                }
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function linkedinAction() {
        $linkedinEnabled = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->linkedinIntegrationEnabled();
        if (empty($linkedinEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $quickSignup = isset($linkedinSettings['quickEnable']) ? $linkedinSettings['quickEnable'] : 0;
        $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');
        $linkedinApi = $linkedinTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        // Fetch info from linkedin
        if (isset($_SESSION['linkedin_access_token']) && !empty($_SESSION['linkedin_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['linkedin_access_token']))
                unset($_SESSION['linkedin_access_token']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        try {
            $linkedin_id = $userDetails->id;
            //Login user
            if (!empty($linkedin_id)) {
                $loggedin = $this->_getUserLogin($linkedin_id, "linkedin");
            }
            if (!$loggedin) {
                // Quick Singup
                if (!empty($quickSignup)) {
                    $_SESSION['linkedin_signup'] = true;
                    $user = $this->saveUser($userDetails, 'linkedin');
                    Engine_Api::_()->user()->setViewer($user);
                    Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                    unset($_SESSION['linkedin_signup']);
                    unset($_SESSION['linkedin_access_token']);
                    $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 1);
                    // Redirect to referer page
                    if ($redirectlink == 2) {
                        $url = $view->baseUrl() . '/members/home';
                    } elseif ($redirectlink == 1) {
                        $url = $view->baseUrl() . '/profile/' . $user->getIdentity();
                    } elseif ($redirectlink == 3) {
                        $url = $view->baseUrl() . '/members/edit/profile';
                    } else {
                        $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
                        $url = $view->baseUrl() . $customeUrl;
                    } 
                    return $this->_redirect($url, array('prependBase' => false));
                } else {
                    //Normal Signup
                    $_SESSION['linkedin_signup'] = true;
                    return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
                }
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function instagramAction() {
        $instagramEnabled = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->instagramIntegrationEnabled();
        if (empty($instagramEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sitelogin');
        $instagramApi = $instagramTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        // Fetch info from instagram
        if (isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['instagram_access_token']))
                unset($_SESSION['instagram_access_token']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        try {
            $instagram_id = $userDetails->data->id;
            //Login user
            if (!empty($instagram_id)) {
                $loggedin = $this->_getUserLogin($instagram_id, "instagram");
            }
            if (!$loggedin) {
                // Quick Singup not possible as email not provided
                //Normal Signup
                $_SESSION['instagram_signup'] = true;
                return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function pinterestAction() {
        $pinterestEnabled = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->pinterestIntegrationEnabled();
        if (empty($pinterestEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $pinterestTable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin');
        $pinterestApi = $pinterestTable->getApi();
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        // Fetch info from pinterest
        if (isset($_SESSION['pinterest_access_token']) && !empty($_SESSION['pinterest_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['pinterest_access_token']))
                unset($_SESSION['pinterest_access_token']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        try {
            $pinterest_id = $userDetails->data->id;
            //Login user
            if (!empty($pinterest_id)) {
                $loggedin = $this->_getUserLogin($pinterest_id, "pinterest");
            }
            if (!$loggedin) {
                // Quick Singup not possible as email not provided
                //Normal Signup
                $_SESSION['pinterest_signup'] = true;
                return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function flickrAction() {
        $flickrEnabled = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->flickrIntegrationEnabled();
        if (empty($flickrEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $flickrTable = Engine_Api::_()->getDbtable('flickr', 'sitelogin');
        $flickrApi = $flickrTable->getApi();
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        // Fetch info from flickr
        if (isset($_SESSION['flickr_access_token']) && !empty($_SESSION['flickr_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->fetch();
        }
        if (!$userDetails || !isset($userDetails['id'])) {
            unset($_SESSION['flickr_access_token']);
            unset($_SESSION['access_token_secret']);
            unset($_SESSION['user_nsid']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        try {
            $flickr_id = $userDetails['id'];
            //Login user
            if (!empty($flickr_id)) {
                $loggedin = $this->_getUserLogin($flickr_id, "flickr");
            }
            if (!$loggedin) {
                // Quick Singup not possible as email not provided
                //Normal Signup
                $_SESSION['flickr_signup'] = true;
                return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function yahooAction() {
        $yahooEnabled = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->yahooIntegrationEnabled();
        if (empty($yahooEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $yahooTable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin');
        $yahooApi = $yahooTable->getApi();
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        // Fetch info from Yahoo
        if (isset($_SESSION['yahoo_access_token']) && !empty($_SESSION['yahoo_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->errorCode)) {
            if (isset($_SESSION['yahoo_access_token']))
                unset($_SESSION['yahoo_access_token']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        try {
            $yahoo_id = $userDetails->profile->guid;
            //Login user
            if (!empty($yahoo_id)) {
                $loggedin = $this->_getUserLogin($yahoo_id, "yahoo");
            }
            if (!$loggedin) {
                // Quick Singup not possible as email not provided
                //Normal Signup
                $_SESSION['yahoo_signup'] = true;
                return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function outlookAction() {
        $outlookEnabled = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->outlookIntegrationEnabled();
        if (empty($outlookEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $quickSignup = isset($outlookSettings['quickEnable']) ? $outlookSettings['quickEnable'] : 0;
        $outlookTable = Engine_Api::_()->getDbtable('outlook', 'sitelogin');
        $outlookApi = $outlookTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['vk_signup']);
        // Fetch info from outlook
        if (isset($_SESSION['outlook_access_token']) && !empty($_SESSION['outlook_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->error)) {
            if (isset($_SESSION['outlook_access_token']))
                unset($_SESSION['outlook_access_token']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        try {
            $outlook_id = $userDetails->id;
            //Login user
            if (!empty($outlook_id)) {
                $loggedin = $this->_getUserLogin($outlook_id, "outlook");
            }
            if (!$loggedin) {
                // Quick Singup
                if (!empty($quickSignup)) {
                    $_SESSION['outlook_signup'] = true;
                    $user = $this->saveUser($userDetails, 'outlook');
                    Engine_Api::_()->user()->setViewer($user);
                    Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                    unset($_SESSION['outlook_signup']);
                    unset($_SESSION['outlook_access_token']);
                    $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 1);
                    // Redirect to referer page
                    if ($redirectlink == 2) {
                        $url = $view->baseUrl() . '/members/home';
                    } elseif ($redirectlink == 1) {
                        $url = $view->baseUrl() . '/profile/' . $user->getIdentity();
                    } elseif ($redirectlink == 3) {
                        $url = $view->baseUrl() . '/members/edit/profile';
                    } else {
                        $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
                        $url = $view->baseUrl() . $customeUrl;
                    }
                    return $this->_redirect($url, array('prependBase' => false));
                } else {
                    //Normal Signup
                    $_SESSION['outlook_signup'] = true;
                    return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
                }
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function vkAction() {
        $vkEnabled = Engine_Api::_()->getDbtable('vk', 'sitelogin')->vkIntegrationEnabled();
        if (empty($vkEnabled))
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $quickSignup = isset($vkSettings['quickEnable']) ? $vkSettings['quickEnable'] : 0;
        $vkTable = Engine_Api::_()->getDbtable('vk', 'sitelogin');
        $vkApi = $vkTable->getApi();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['yahoo_signup']);
        // Fetch info from vk
        if (isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token'])) {
            $userDetails = Engine_Api::_()->getDbtable('vk', 'sitelogin')->fetch();
        }
        if (!$userDetails || isset($userDetails->error)) {
            unset($_SESSION['vk_access_token']);
            unset($_SESSION['vk_email']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        try {
            $vk_id = $userDetails->response[0]->id;
            //Login user
            if (!empty($vk_id)) {
                $loggedin = $this->_getUserLogin($vk_id, "vk");
            }
            if (!$loggedin) {
                // Quick Singup
                if (!empty($quickSignup) && isset($_SESSION['vk_email']) && !empty($_SESSION['vk_email'])) {
                    $_SESSION['vk_signup'] = true;
                    $user = $this->saveUser($userDetails->response[0], 'vk');
                    Engine_Api::_()->user()->setViewer($user);
                    Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                    unset($_SESSION['vk_signup']);
                    unset($_SESSION['vk_access_token']);
                    unset($_SESSION['vk_email']);
                    $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 1);
                    // Redirect to referer page
                    if ($redirectlink == 2) {
                        $url = $view->baseUrl() . '/members/home';
                    } elseif ($redirectlink == 1) {
                        $url = $view->baseUrl() . '/profile/' . $user->getIdentity();
                    } elseif ($redirectlink == 3) {
                        $url = $view->baseUrl() . '/members/edit/profile';
                    } else {
                        $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
                        $url = $view->baseUrl() . $customeUrl;
                    }
                    return $this->_redirect($url, array('prependBase' => false));
                } else {
                    //Normal Signup
                    $_SESSION['vk_signup'] = true;
                    return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
                }
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function facebookAction() {
        // Clear
        if (null !== $this->_getParam('clear')) {
            unset($_SESSION['facebook_lock']);
            unset($_SESSION['facebook_uid']);
        }
        unset($_SESSION['google_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['vk_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['yahoo_signup']);
        $viewer = Engine_Api::_()->user()->getViewer();
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        // Enabled?
        if (!$facebook) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        // Already connected
        if ($facebook->getUser()) {
            $code = $facebook->getPersistentData('code');
            // Attempt to login
            if (!$viewer->getIdentity()) {
                $facebook_uid = $facebook->getUser();
                if ($facebook_uid) {
                    $user_id = $facebookTable->select()
                            ->from($facebookTable, 'user_id')
                            ->where('facebook_uid = ?', $facebook_uid)
                            ->query()
                            ->fetchColumn();
                }
                if ($user_id &&
                        $viewer = Engine_Api::_()->getItem('user', $user_id)) {
                    Zend_Auth::getInstance()->getStorage()->write($user_id);
                    // Register login
                    $viewer->lastlogin_date = date("Y-m-d H:i:s");
                    if ('cli' !== PHP_SAPI) {
                        $viewer->lastlogin_ip = $ipExpr;
                        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                            'user_id' => $user_id,
                            'ip' => $ipExpr,
                            'timestamp' => new Zend_Db_Expr('NOW()'),
                            'state' => 'success',
                            'source' => 'facebook',
                        ));
                    }
                    $viewer->save();
                } else if ($facebook_uid) {
                    // They do not have an account
                    $facebookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_facebook;
                    $quickSignup = isset($facebookSettings['quickEnable']) ? $facebookSettings['quickEnable'] : 0;
                    if (!empty($quickSignup)) {
                        $_SESSION['facebook_signup'] = true;
                        $apiInfo = $facebook->api('/me?fields=email,locale,first_name,last_name,picture');
                        $user = $this->saveUser($apiInfo, 'facebook');
                        Engine_Api::_()->user()->setViewer($user);
                        Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                        /*
                        $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 1);
                        // Redirect to referer page
                        if ($redirectlink == 2) {
                             $url = $view->baseUrl() . '/';
                        } elseif ($redirectlink == 1) {
                            $url = $view->baseUrl() . '/profile/' . $user->getIdentity();
                        } elseif ($redirectlink == 3) {
                            $url = $view->baseUrl() . '/members/edit/profile';
                        } else {
                            $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
                            $url = $view->baseUrl() . $customeUrl;
                        }
                        */
                        $url = $_SESSION['redirectURL'];
                        return $this->_redirect($url, array('prependBase' => false));
                    }
                    $_SESSION['facebook_signup'] = true;
                    return $this->_helper->redirector->gotoRoute(array(
                        //'action' => 'facebook',
                        'return_url' => '64-' . base64_encode($this->url(array('action'=>'home'),"user_general"))
                    ), 'user_signup', true);
                }
            } else {
                // Check for facebook user
                $facebookInfo = $facebookTable->select()
                        ->from($facebookTable)
                        ->where('facebook_uid = ?', $facebook->getUser())
                        ->limit(1)
                        ->query()
                        ->fetch();
                if (!empty($facebookInfo) && $facebookInfo['user_id'] != $viewer->getIdentity()) {
                    // Redirect to referer page
                    $url = $_SESSION['redirectURL'];
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
            }
            // Redirect to referer page
            $url = $_SESSION['redirectURL'];
            return $this->_redirect($url, array('prependBase' => false));
        }
        // Not connected
        else {
            // Okay
            if (!empty($_GET['code'])) {
                // This doesn't seem to be necessary anymore, it's probably
                // being handled in the api initialization
                return $this->_helper->redirector->gotoRoute(array('action'=>'home'), 'user_general', true);
            }
            // Error
            else if (!empty($_GET['error'])) {
                // @todo maybe display a message?
                return $this->_helper->redirector->gotoRoute(array('action'=>'home'), 'user_general', true);
            }
            // Redirect to auth page
            else {
                $url = $facebook->getLoginUrl(array(
                    'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://')
                    . $_SERVER['HTTP_HOST'] . $this->view->url(),
                    'scope' => join(',', array(
                        'email',
                        // FB PERMISSION ISSUE: As this requires fb verify, so hide it now
                        //'user_birthday',
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
        unset($_SESSION['google_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['yahoo_signup']);
        unset($_SESSION['vk_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['outlook_signup']);
        // Setup
        $viewer = Engine_Api::_()->user()->getViewer();
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
            if (isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2'])) {
                // Try to login?
                if (!$viewer->getIdentity()) {
                    // Get account info
                    try {
                        $accountInfo = $twitter->account->verify_credentials();
                    } catch (Exception $e) {
                        // This usually happens when the application is modified after connecting
                        unset($_SESSION['twitter_token']);
                        unset($_SESSION['twitter_secret']);
                        unset($_SESSION['twitter_token2']);
                        unset($_SESSION['twitter_secret2']);
                        $twitterTable->clearApi();
                        $twitter = $twitterTable->getApi();
                        $twitterOauth = $twitterTable->getOauth();
                    }
                }
            }
            if (isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2'])) {
                // Try to login?
                if (!$viewer->getIdentity()) {
                    $info = $twitterTable->select()
                            ->from($twitterTable)
                            ->where('twitter_uid = ?', $accountInfo->id)
                            ->query()
                            ->fetch();
                    if (empty($info)) {
                        // They do not have an account
                        $twitterSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_twitter;
                        $quickSignup = isset($twitterSettings['quickEnable']) ? $twitterSettings['quickEnable'] : 0;
                        if (!empty($quickSignup)) {
                            $_SESSION['twitter_signup'] = true;
                            $user = $this->saveUser($accountInfo, 'twitter');
                            Engine_Api::_()->user()->setViewer($user);
                            Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                            $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 1);
                            // Redirect to referer page
                            if ($redirectlink == 2) {
                                $url = $view->baseUrl() . '/members/home';
                            } elseif ($redirectlink == 1) {
                                $url = $view->baseUrl() . '/profile/' . $user->getIdentity();
                            } elseif ($redirectlink == 3) {
                                $url = $view->baseUrl() . '/members/edit/profile';
                            } else {
                                $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
                                $url = $view->baseUrl() . $customeUrl;
                            }
                            return $this->_redirect($url, array('prependBase' => false));
                        }
                        $_SESSION['twitter_signup'] = true;
                        return $this->_helper->redirector->gotoRoute(array(
                                        //'action' => 'twitter',
                                        ), 'user_signup', true);
                    } else {
                        Zend_Auth::getInstance()->getStorage()->write($info['user_id']);
                        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                    }
                }
                // Success
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            } else if (isset($_SESSION['twitter_token'], $_SESSION['twitter_secret'], $_GET['oauth_verifier'])) {
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
                    return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                } else { // Otherwise try to login?
                    $info = $twitterTable->select()
                            ->from($twitterTable)
                            ->where('twitter_uid = ?', $accountInfo->id)
                            ->query()
                            ->fetch();
                    if (empty($info)) {
                        // They do not have an account
                        $_SESSION['twitter_signup'] = true;
                        return $this->_helper->redirector->gotoRoute(array(
                                        //'action' => 'twitter',
                                        ), 'user_signup', true);
                    } else {
                        Zend_Auth::getInstance()->getStorage()->write($info['user_id']);
                        // Register login
                        $viewer = Engine_Api::_()->getItem('user', $info['user_id']);
                        $viewer->lastlogin_date = date("Y-m-d H:i:s");
                        if ('cli' !== PHP_SAPI) {
                            $viewer->lastlogin_ip = $ipExpr;
                            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                                'user_id' => $info['user_id'],
                                'ip' => $ipExpr,
                                'timestamp' => new Zend_Db_Expr('NOW()'),
                                'state' => 'success',
                                'source' => 'twitter',
                            ));
                        }
                        $viewer->save();
                        // Redirect to referer page
                        $url = $_SESSION['redirectURL'];
                        return $this->_redirect($url, array('prependBase' => false));
                    }
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
    private function _getUserLogin($id = 0, $type = NULL) {
        if (!empty($type) && !empty($id)) {
            $column_name = $type . '_id';
            $siteTable = Engine_Api::_()->getDbtable($type, 'sitelogin');
            $user_id = $siteTable->select()
                    ->from($siteTable, 'user_id')
                    ->where("$column_name = ?", $id)
                    ->query()
                    ->fetchColumn();
        }
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        if ($user_id &&
                $viewer = $user = Engine_Api::_()->getItem('user', $user_id)) {
            $viewer_id = $viewer->getIdentity();
            $email = $user->email;
            if (!$user->enabled) {
                if (!$user->verified) {
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'third-party',
                    ));
                    return false;
                } else if (!$user->approved) {
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'third-party',
                    ));
                    return false;
                }
            }
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {
                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $user->email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'unpaid',
                    ));
                    // Redirect to subscription page
                    $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                    $subscriptionSession->unsetAll();
                    $subscriptionSession->user_id = $user->getIdentity();
                    return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
                                'controller' => 'subscription', 'action' => 'index'), 'default', true);
                }
            }
            // Run pre login hook
            $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
            foreach ((array) $event->getResponses() as $response) {
                if (is_array($response)) {
                    if (!empty($response['error']) && !empty($response['message'])) {
                        return false;
                    } else if (!empty($response['redirect'])) {
                        $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
                    } else {
                        continue;
                    }
                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'third-party',
                    ));
                    // Return
                    return false;
                }
            }
            // Register login
            $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
            $loginTable->insert(array(
                'user_id' => $user->getIdentity(),
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'success',
                'active' => true,
            ));
            $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();
            Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
            Engine_Api::_()->user()->setViewer();
            // Increment sign-in count
            Engine_Api::_()->getDbtable('statistics', 'core')
                    ->increment('user.logins');
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $viewer->lastlogin_date = date("Y-m-d H:i:s");
                if ('cli' !== PHP_SAPI) {
                    $viewer->lastlogin_ip = $ipExpr;
                }
                $viewer->save();
                Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($viewer, $viewer, 'login');
            }
            // Run post login hook
            $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);
            // Redirect by hook
            foreach ((array) $event->getResponses() as $response) {
                if (is_array($response)) {
                    if (!empty($response['error']) && !empty($response['message'])) {
                        return false;
                    } else if (!empty($response['redirect'])) {
                        return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
                    }
                }
            }
            // Just redirect to home
            return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }
        return false;
    }
    /**
     *  @param $getFormData - User Data. 
     *
     */
    public function saveUser($userLoginDetails, $loginType = NULL) {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
        if (!empty($userLoginDetails) && $loginType == 'facebook') {
            $facebookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_facebook;
            $defaultProfileId = isset($facebookSettings['facebookProfileType']) ? $facebookSettings['facebookProfileType'] : 0;
            $defaultMemberLevel = isset($facebookSettings['memberLevel']) ? $facebookSettings['memberLevel'] : 0;
            $getFormData['username'] = Engine_Api::_()->sitelogin()->getUserName($userLoginDetails['first_name'], $userLoginDetails['last_name']);
            $getFormData['email'] = $userLoginDetails['email'];
            $getFormData['familyName'] = $userLoginDetails['last_name'];
            $getFormData['givenName'] = $userLoginDetails['first_name'];
            $getFormData['language'] = isset($userLoginDetails['language']) && !empty($userLoginDetails['language']) ? $userLoginDetails['language'] : $settings->getSetting('core.locale.language', 'en_US');
            ;
            $getFormData['locale'] = isset($userLoginDetails['locale']) && !empty($userLoginDetails['locale']) ? $userLoginDetails['locale'] : $settings->getSetting('core.locale.locale', 'auto');
            ;
            $getFormData['member_level'] = $defaultMemberLevel;
            $getFormData['timezone'] = $settings->getSetting('core.locale.timezone', 'America/Los_Angeles');
            $getFormData['approved'] = 1;
            $getFormData['verified'] = 1;
            $getFormData['enabled'] = 1;
            $getFormData['search'] = true;
            $getFormData['profile_type'] = $defaultProfileId;
            $getFormData['profile_photo'] = "https://graph.facebook.com/" . $userLoginDetails['id'] . "/picture?type=large";
        }
        // Getdisplay name here
        if (!empty($userLoginDetails) && $loginType == 'google') {
            $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
            $defaultProfileId = isset($googleSettings['googleProfileType']) ? $googleSettings['googleProfileType'] : 0;
            $defaultMemberLevel = isset($googleSettings['memberLevel']) ? $googleSettings['memberLevel'] : 0;
            $getFormData['username'] = Engine_Api::_()->sitelogin()->getUserName($userLoginDetails->givenName, $userLoginDetails->familyName);
            $getFormData['email'] = $userLoginDetails->email;
            $getFormData['familyName'] = $userLoginDetails->familyName;
            $getFormData['givenName'] = $userLoginDetails->givenName;
            $getFormData['language'] = isset($userLoginDetails->language) && !empty($userLoginDetails->language) ? $userLoginDetails->language : $settings->getSetting('core.locale.language', 'en_US');
            ;
            $getFormData['locale'] = isset($userLoginDetails->locale) && !empty($userLoginDetails->locale) ? $userLoginDetails->locale : $settings->getSetting('core.locale.locale', 'auto');
            ;
            $getFormData['member_level'] = $defaultMemberLevel;
            $getFormData['timezone'] = $settings->getSetting('core.locale.timezone', 'America/Los_Angeles');
            $getFormData['approved'] = 1;
            $getFormData['verified'] = 1;
            $getFormData['enabled'] = 1;
            $getFormData['search'] = true;
            $getFormData['profile_type'] = $defaultProfileId;
            $getFormData['profile_photo'] = $userLoginDetails->picture;
        }
        if (!empty($userLoginDetails) && $loginType == 'linkedin') {
            //GetLinkedin Image
            if (isset($userLoginDetails->pictureUrls) && !empty($userLoginDetails->pictureUrls)) {
                $originalImageUrls = get_object_vars($userLoginDetails->pictureUrls);
                if (!empty($originalImageUrls)) {
                    $image = isset($originalImageUrls['values'][0]) ? $originalImageUrls['values'][0] : 0;
                }
            }
            $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
            $defaultProfileId = isset($googleSettings['linkedinProfileType']) ? $googleSettings['linkedinProfileType'] : 0;
            $defaultMemberLevel = isset($googleSettings['memberLevel']) ? $googleSettings['memberLevel'] : 0;
            $getFormData['username'] = Engine_Api::_()->sitelogin()->getUserName($userLoginDetails->firstName, $userLoginDetails->lastName);
            $getFormData['email'] = $userLoginDetails->emailAddress;
            $getFormData['familyName'] = $userLoginDetails->lastName;
            $getFormData['givenName'] = $userLoginDetails->firstName;
            $getFormData['approved'] = 1;
            $getFormData['verified'] = 1;
            $getFormData['enabled'] = 1;
            $getFormData['search'] = true;
            $getFormData['member_level'] = $defaultMemberLevel;
            $getFormData['timezone'] = $settings->getSetting('core.locale.timezone', 'America/Los_Angeles');
            ;
            $getFormData['profile_type'] = $defaultProfileId;
            $getFormData['profile_photo'] = $image;
        }
        if (!empty($userLoginDetails) && $loginType == 'vk') {
            if (!isset($_SESSION['vk_email']) || empty($_SESSION['vk_email']))
                return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
            //Get vk Image
            if (isset($userLoginDetails->photo_50) && !empty($userLoginDetails->photo_50)) {
                $image = $userLoginDetails->photo_50;
            }
            $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
            $defaultProfileId = isset($vkSettings['vkProfileType']) ? $vkSettings['vkProfileType'] : 0;
            $defaultMemberLevel = isset($vkSettings['memberLevel']) ? $vkSettings['memberLevel'] : 0;
            $getFormData['username'] = Engine_Api::_()->sitelogin()->getUserName($userLoginDetails->first_name, $userLoginDetails->last_name);
            $getFormData['email'] = $_SESSION['vk_email'];
            $getFormData['familyName'] = $userLoginDetails->last_name;
            $getFormData['givenName'] = $userLoginDetails->first_name;
            $getFormData['approved'] = 1;
            $getFormData['verified'] = 1;
            $getFormData['enabled'] = 1;
            $getFormData['search'] = true;
            $getFormData['member_level'] = $defaultMemberLevel;
            $getFormData['timezone'] = $settings->getSetting('core.locale.timezone', 'America/Los_Angeles');
            ;
            $getFormData['profile_type'] = $defaultProfileId;
            $getFormData['profile_photo'] = $image;
        }
        if (!empty($userLoginDetails) && $loginType == 'outlook') {
            //Get outlook Image
            // if (isset($userLoginDetails->photo_50) && !empty($userLoginDetails->photo_50)) {
            //     $image = $userLoginDetails->photo_50;
            // }
            $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
            $defaultProfileId = isset($outlookSettings['outlookProfileType']) ? $outlookSettings['outlookProfileType'] : 0;
            $defaultMemberLevel = isset($outlookSettings['memberLevel']) ? $outlookSettings['memberLevel'] : 0;
            $getFormData['username'] = Engine_Api::_()->sitelogin()->getUserName($userLoginDetails->givenName, $userLoginDetails->surname);
            $getFormData['email'] = $userLoginDetails->userPrincipalName;
            $getFormData['familyName'] = $userLoginDetails->surname;
            $getFormData['givenName'] = $userLoginDetails->givenName;
            $getFormData['approved'] = 1;
            $getFormData['verified'] = 1;
            $getFormData['enabled'] = 1;
            $getFormData['search'] = true;
            $getFormData['member_level'] = $defaultMemberLevel;
            $getFormData['timezone'] = $settings->getSetting('core.locale.timezone', 'America/Los_Angeles');
            ;
            $getFormData['profile_type'] = $defaultProfileId;
        }

        //Check  Email already exist
        $user = Engine_Api::_()->user()->getUser($getFormData['email']);
        if ($user->getIdentity()) {
            unset($_SESSION['linkedin_signup']);
            unset($_SESSION['google_signup']);
            unset($_SESSION['access_token']);
            unset($_SESSION['facebook_signup']);
            unset($_SESSION['outlook_signup']);
            unset($_SESSION['vk_signup']);
            unset($_SESSION['vk_email']);
            return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index', 'error_flag' => 1), 'user_signup', true);
        }
        if (empty($getFormData['username'])) {
            unset($_SESSION['linkedin_signup']);
            unset($_SESSION['google_signup']);
            unset($_SESSION['access_token']);
            unset($_SESSION['facebook_signup']);
            unset($_SESSION['outlook_signup']);
            unset($_SESSION['vk_signup']);
            unset($_SESSION['vk_email']);
            return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'user_signup', true);
        }
        $displayUserName = $this->setUserName($getFormData);
        if ($displayUserName == '' && !empty($getFormData['username'])) {
            $displayUserName = $getFormData['username'];
        }
        //Prepare field for user table.
        $userDetails = array();
        $userDetails['email'] = $getFormData['email'];
        if (!empty($getFormData['username'])) {
            $userDetails['username'] = $getFormData['username'];
        }
        $userDetails['displayname'] = $displayUserName;
        if (!empty($getFormData['language'])) {
            $userDetails['language'] = $getFormData['language'];
        }
        $userDetails['timezone'] = $getFormData['timezone'];
        $userDetails['creation_date'] = date('Y-m-d H:i:s');
        $userDetails['creation_ip'] = $_SERVER['REMOTE_ADDR'];
        $userDetails['modified_date'] = date('Y-m-d H:i:s');
        $userDetails['level_id'] = $getFormData['member_level'];
        $userDetails['enabled'] = $getFormData['enabled'];
        $userDetails['approved'] = $getFormData['approved'];
        $userDetails['verified'] = $getFormData['verified'];
        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        try {
            $db = $userTable->getAdapter();
            $db->beginTransaction();
            $userinfo = $userTable->createRow();
            $userinfo->setFromarray($userDetails);
            $userinfo->save();
            $sqlquery = $userTable->select()
                    ->from($userTable->info('name'), array('user_id'))
                    ->where('email = ?', $userDetails['email']);
            $userId = $userTable->fetchRow($sqlquery)->user_id;
            //get Authentication permission for use
            $authAllowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
            $getAuthCommentPermission = Engine_Api::_()->getApi('core', 'authorization')->getPermission($userDetails['level_id'], 'user', 'auth_comment');
            $getAuthCommentPermission = json_decode($getAuthCommentPermission);
            if (!is_null($getAuthCommentPermission)) {
                $getAuthRowarray = $this->getFieldsAuthAllow($userId, 'comment', $getAuthCommentPermission);
                foreach ($getAuthRowarray as $tuple) {
                    $userAuthAllow = $authAllowTable->createRow();
                    $userAuthAllow->setFromArray($tuple);
                    $selectQuery = $authAllowTable->select()
                            ->from($authAllowTable->info('name'), array('COUNT(1) AS exists'))
                            ->where('`resource_type` = ?', $userAuthAllow->resource_type)
                            ->where('`resource_id` = ?', $userAuthAllow->resource_id)
                            ->where('`action` = ?', $userAuthAllow->action)
                            ->where('`role` = ?', $userAuthAllow->role)
                            ->where('`role_id` = ?', $userAuthAllow->role_id)
                            ->where('`value` = ?', $userAuthAllow->value);
                    $result = $authAllowTable->fetchRow($selectQuery);
                    if ($result->exists == 0) {
                        $userAuthAllow->save();
                    }
                }
            }
            $getAuthViewPermission = Engine_Api::_()->getApi('core', 'authorization')->getPermission($userDetails['level_id'], 'user', 'auth_view');
            $getAuthViewPermission = json_decode($getAuthViewPermission);
            if (!is_null($getAuthViewPermission)) {
                $getAuthRowarray = $this->getFieldsAuthAllow($userId, 'view', $getAuthViewPermission);
                foreach ($getAuthRowarray as $tuple) {
                    $userAuthAllowViewPermission = $authAllowTable->createRow();
                    $userAuthAllowViewPermission->setFromarray($tuple);
                    $selectQuery = $authAllowTable->select()
                            ->from($authAllowTable->info('name'), array('COUNT(1) AS exists'))
                            ->where('`resource_type` = ?', $userAuthAllowViewPermission->resource_type)
                            ->where('`resource_id` = ?', $userAuthAllowViewPermission->resource_id)
                            ->where('`action` = ?', $userAuthAllowViewPermission->action)
                            ->where('`role` = ?', $userAuthAllowViewPermission->role)
                            ->where('`role_id` = ?', $userAuthAllowViewPermission->role_id)
                            ->where('`value` = ?', $userAuthAllowViewPermission->value);
                    $result = $authAllowTable->fetchRow($selectQuery);
                    if ($result->exists == 0) {
                        $userAuthAllowViewPermission->save();
                    }
                }
            }
            $user = Engine_Api::_()->getItem('user', $userId);
            if (!empty($getFormData['profile_photo'])) {
                Engine_Api::_()->sitelogin()->fetchImage($getFormData['profile_photo'], $user);
            }
            if ($loginType == 'outlook') {
                Engine_Api::_()->sitelogin()->outlookfetchImage($user);
            }
            try {
                // Preload profile type field stuff
                $profileTypeField = $this->getProfileTypeField();
                if ($profileTypeField) {
                    if ($getFormData['profile_type']) {
                        $values = Engine_Api::_()->fields()->getFieldsValues($user);
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $profileTypeField->field_id;
                        $valueRow->item_id = $user->getIdentity();
                        $valueRow->value = $getFormData['profile_type'];
                        $valueRow->save();
                    } else {
                        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                            $profileTypeField = $topStructure[0]->getChild();
                            $options = $profileTypeField->getOptions();
                            if (count($options) == 1) {
                                $values = Engine_Api::_()->fields()->getFieldsValues($user);
                                $valueRow = $values->createRow();
                                $valueRow->field_id = $profileTypeField->field_id;
                                $valueRow->item_id = $user->getIdentity();
                                $valueRow->value = $options[0]->option_id;
                                $valueRow->save();
                            }
                        }
                    }
                }
            } catch (Exception $ex) {
                
            }
            if (!empty($_SESSION['google_signup'])) {
                try {
                    $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
                    $googleApi = $googleTable->getApi();
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    $tokens = Zend_Json::decode($_SESSION['google_access_token']);
                    $googleTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'google_id' => $googleApi->id,
                        'access_token' => $tokens['access_token'],
                        'expires' => 0,
                    ));
                } catch (Exception $e) {
                    if ('development' == APPLICATION_ENV) {
                        echo $e;
                    }
                }
            }
            if (!empty($_SESSION['linkedin_signup'])) {
                try {
                    $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');
                    $linkedinTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'linkedin_id' => $userLoginDetails->id,
                        'access_token' => $_SESSION['linkedin_access_token'],
                        'expires' => 0,
                    ));
                } catch (Exception $e) {
                    if ('development' == APPLICATION_ENV) {
                        throw $e;
                    }
                }
            }
            if (!empty($_SESSION['vk_signup'])) {
                try {
                    $vkTable = Engine_Api::_()->getDbtable('vk', 'sitelogin');
                    $vkTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'vk_id' => $userLoginDetails->id,
                        'access_token' => $_SESSION['vk_access_token'],
                        'expires' => 0,
                    ));
                } catch (Exception $e) {
                    if ('development' == APPLICATION_ENV) {
                        throw $e;
                    }
                }
            }
            if (!empty($_SESSION['outlook_signup'])) {
                try {
                    $outlookTable = Engine_Api::_()->getDbtable('outlook', 'sitelogin');
                    $outlookTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'outlook_id' => $userLoginDetails->id,
                        'access_token' => $_SESSION['outlook_access_token'],
                        'expires' => 0,
                    ));
                } catch (Exception $e) {
                    if ('development' == APPLICATION_ENV) {
                        throw $e;
                    }
                }
            }
            // Attempt to connect facebook
            if (!empty($_SESSION['facebook_signup'])) {
                try {
                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebook = $facebookTable->getApi();
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    if ($facebook && $settings->core_facebook_enable) {
                        $facebookTable->insert(array(
                            'user_id' => $user->getIdentity(),
                            'facebook_uid' => $facebook->getUser(),
                            'access_token' => $facebook->getAccessToken(),
                            //'code' => $code,
                            'expires' => 0, // @todo make sure this is correct
                        ));
                    }
                } catch (Exception $e) {
                    // Silence
                    if ('development' == APPLICATION_ENV) {
                        throw $e;
                    }
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $userTable->update(array('enabled' => 1, 'approved' => 1, 'verified' => 1), array('user_id = ?' => $user->getIdentity()));
        try {
            // Handle subscriptions
            if (Engine_Api::_()->hasModuleBootstrap('payment')) {
                // Check for the user's plan
                $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
                if (!$subscriptionsTable->check($user)) {
                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $userDetails['email'],
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'unpaid',
                    ));
                    // Redirect to subscription page
                    $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
                    $subscriptionSession->unsetAll();
                    $subscriptionSession->user_id = $user->getIdentity();
                    return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
                                'controller' => 'subscription', 'action' => 'index'), 'default', true);
                }
            }
            Engine_Api::_()->user()->setViewer($user);
            // Increment signup counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');
            if ($user->verified && $user->enabled) {
                // Create activity for them
                Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
                // Set user as logged in if not have to verify email
                Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
            }
            return $user;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     *  @param Get Field Structure 
     *  @param Get Form Data 
     * @return USer display name here.
     */
    public function setUserName($getData) {
        if (!is_null($getData['givenName']) && !is_null($getData['familyName'])) {
            $displayname = $getData['givenName'] . ' ' . $getData['familyName'];
        } else if (!is_null($getData['givenName']) && is_null($getData['familyName'])) {
            $displayname = $getData['givenName'];
        } else if (is_null($getData['givenName']) && !is_null($getData['familyName'])) {
            $displayname = $getData['familyName'];
        } else {
            $displayname = '';
        }
        return $displayname;
    }
    /**
     *  @param user id,action, data for  permisssion
     * @return an array for authentication level.
     * */
    public function getFieldsAuthAllow($userId, $action, $data) {
        $authAllowRow = array();
        $index = 0;
        if ($userId == NULL && $data == NULL && $action == NULL) {
            return NULL;
        }
        foreach ($data as $key => $value) {
            $authAllowRow[$index]['resource_type'] = 'user';
            $authAllowRow[$index]['resource_id'] = $userId;
            $authAllowRow[$index]['action'] = $action;
            $authAllowRow[$index]['role'] = $value;
            $authAllowRow[$index]['role_id'] = 0;
            $authAllowRow[$index]['value'] = 1;
            $authAllowRow[$index]['params'] = NULL;
            $index++;
        }
        return $authAllowRow;
    }
    public function getProfileTypeField() {
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            return $topStructure[0]->getChild();
        }
        return null;
    }
}