<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AuthController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Siteotpverifier_AuthController extends Core_Controller_Action_Standard
{
  public function loginAction()
  {
    $user = Engine_Api::_()->user()->getViewer();
    // Already logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are already signed in.');
      if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
        $this->_helper->redirector->gotoRoute(array(), 'default', true);
        ;
      }
      return;
    }
    $this->view->loginoption = $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
    $dontallowtwostep = in_array($loginoption, array('default', 'both'));
    // Make form
    $this->view->form = $form = new User_Form_Login();
    $form->setAction($this->view->url(array('return_url' => null), 'user_login'));
    $form->populate(array(
      'return_url' => $this->_getParam('return_url'),
    ));

    // Render
    $disableContent = $this->_getParam('disableContent', 0);
    if( !$disableContent ) {
      $this->_helper->content
        ->setContentName("user_auth_login")
        ->setEnabled()
      ;
    }
    // Not a post
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }
    // Form not valid
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check login creds
    extract($form->getValues());

    // $email, $password, $remember
    //$user_table = Engine_Api::_()->getDbtable('users', 'user');
    //$user_select = $user_table->select()
    //        ->where('email = ?', $email);          // If post exists
    //$user = $user_table->fetchRow($user_select);
    if( preg_match("/^([1-9][0-9]{4,15})$/", $email) ) {
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
        ->fetchRow(array('phoneno = ?' => $email));
      $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id ) : null;
      $phoneno = $email;
      $email = $user ? $user->email : $phoneno;
    } else {
      $user = Engine_Api::_()->getDbtable('users', 'user')
        ->fetchRow(array('email = ?' => $email));
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }

    // Get ip address
    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    // Check if user exists
    if( empty($user) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No record of a member with that email or phone number was found.');
      $form->addError(Zend_Registry::get('Zend_Translate')->_('No record of a member with that email or phone number was found.'));

      // Register login
      Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
        'email' => $email,
        'ip' => $ipExpr,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'state' => 'no-member',
      ));

      return;
    }
    // check core version
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    $checkVersion = Engine_Api::_()->seaocore()->checkVersion($coreVersion, '4.10.5');
    $email = $user->email;

    // changes in calling of function on the basis of core version
    if($checkVersion) {
      $isValidPassword = Engine_Api::_()->user()->checkCredential($user->getIdentity(), $password, $user);
    } else {
      $isValidPassword = Engine_Api::_()->user()->checkCredential($user->getIdentity(), $password);
    }

    if( !$isValidPassword ) {
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
    if( !$user->enabled ) {
      if( !$user->verified ) {
        $this->view->status = false;

        $token = Engine_Api::_()->user()->getVerifyToken($user->getIdentity());
        $resend_url = $this->_helper->url->url(array('action' => 'resend', 'token' => $token), 'user_signup', true);
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
      } else if( !$user->approved ) {
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


    //Handle Otp Verification
    $loginAllowed = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteotpverifier', 'login');
    $otpSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enableotp', 1);
    if( !$dontallowtwostep && !empty($loginAllowed) && !empty($otpSetting) && !empty($otpUser->phoneno) && !empty($otpUser->enable_verification) ) {
      // Register login
      Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
        'user_id' => $user->getIdentity(),
        'email' => $email,
        'ip' => $ipExpr,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'state' => 'otpNotVerified',
      ));

      $otpverificationSession = new Zend_Session_Namespace('Otp_verification');
      $otpverificationSession->unsetAll();
      $otpverificationSession->user_id = $user->getIdentity();
      $otpverificationSession->email = $email;
      $otpverificationSession->password = $password;
      $otpverificationSession->return_url = $form->getValue('return_url');
      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
      $type = 'login';
      // genrate OTP code codes
      $response = $forgotTable->createCode($type, $user);
      if( !empty($response['error']) ) {
        $translate = Zend_Registry::get('Zend_Translate');
        $error = $translate->translate($response['error']);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
      $code = $response['code'];
      //code for sending code to phone no.
      $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCode($otpUser, $code, $type);

      return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier',
          'controller' => 'verifier', 'action' => 'index'), 'siteotpverifier_extended', true);
    }

    // Handle subscriptions
    if( Engine_Api::_()->hasModuleBootstrap('payment') ) {
      // Check for the user's plan
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      if( !$subscriptionsTable->check($user) ) {
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
    foreach( (array) $event->getResponses() as $response ) {
      if( is_array($response) ) {
        if( !empty($response['error']) && !empty($response['message']) ) {
          $form->addError($response['message']);
        } else if( !empty($response['redirect']) ) {
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
    if( empty($user->password) ) {
      $compat = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.compatibility.password');
      $migration = null;
      try {
        $migration = Engine_Db_Table::getDefaultAdapter()->select()
          ->from('engine4_user_migration')
          ->where('user_id = ?', $user->getIdentity())
          ->limit(1)
          ->query()
          ->fetch();
      } catch( Exception $e ) {
        $migration = null;
        $compat = null;
      }
      if( !$migration ) {
        $compat = null;
      }

      if( $compat == 'import-version-3' ) {

        // Version 3 authentication
        $cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
        if( $cryptedPassword === $migration['user_password'] ) {
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
      // changes in calling of function on the basis of core version
      if($checkVersion) {
        $authResult = Engine_Api::_()->user()->authenticate($email, $password, $user);
      } else {
        $authResult = Engine_Api::_()->user()->authenticate($email, $password);
      }

      $authCode = $authResult->getCode();
      Engine_Api::_()->user()->setViewer();

      if( $authCode != Zend_Auth_Result::SUCCESS ) {
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

    // Increment sign-in count
    Engine_Api::_()->getDbtable('statistics', 'core')
      ->increment('user.logins');

    // Test activity @todo remove
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      $viewer->lastlogin_date = date("Y-m-d H:i:s");
      if( 'cli' !== PHP_SAPI ) {
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
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      // Redirect by form
      $uri = $form->getValue('return_url');
      if( $uri ) {
        if( substr($uri, 0, 3) == '64-' ) {
          $uri = base64_decode(substr($uri, 3));
        }
        return $this->_redirect($uri, array('prependBase' => false));
      }

      // Redirect by session
      $session = new Zend_Session_Namespace('Redirect');
      if( isset($session->uri) ) {
        $uri = $session->uri;
        $opts = $session->options;
        $session->unsetAll();
        return $this->_redirect($uri, $opts);
      } else if( isset($session->route) ) {
        $session->unsetAll();
        return $this->_helper->redirector->gotoRoute($session->params, $session->route, $session->reset);
      }

      // Redirect by hook
      foreach( (array) $event->getResponses() as $response ) {
        if( is_array($response) ) {
          if( !empty($response['error']) && !empty($response['message']) ) {
            return $form->addError($response['message']);
          } else if( !empty($response['redirect']) ) {
            return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
          }
        }
      }

      // Just redirect to home
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
  }

  public function forgotAction()
  {
	   // Render
    $this->_helper->content
        ->setContentName("user_auth_forgot")
        ->setEnabled()
        ;

    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    // Make form
    $this->view->form = $form = new Siteotpverifier_Form_Auth_Forgot();

    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $translate = Zend_Registry::get('Zend_Translate');
    // Check for existing user
    $user = Engine_Api::_()->getDbtable('users', 'user')
      ->fetchRow(array('email = ?' => $form->getValue('email')));
    $searchType = 'email';
    if( !$user || !$user->getIdentity() ) {
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
        ->fetchRow(array('phoneno = ?' => $form->getValue('email')));
      $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id ) : null;
      $searchType = 'phoneno';
      if( !$user || !$user->getIdentity() ) {
        $form->addError($translate->translate('A user account with this email or phone number was not found.'));
        return;
      }
    } else {
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }

    // Check to make sure they're enabled
    if( !$user->enabled ) {
      $form->addError($translate->translate('That user account has not yet been verified or disabled by an admin.'));
      return;
    }
    if( !empty($otpUser->phoneno) ) {
      return $this->_helper->redirector->gotoRoute(array('search' => $form->getValue('email'), 'type' => $searchType), 'siteotpverifier_lostpassword_choose', true);
    }
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    $db = $forgotTable->getAdapter();
    $db->beginTransaction();
    try {

      // Delete any existing reset password codes
      $forgotTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
      ));

      // Create a new reset password code
      $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
      $forgotTable->insert(array(
        'user_id' => $user->getIdentity(),
        'code' => $code,
        'creation_date' => date('Y-m-d H:i:s'),
        'type' => 'forgot',
        'verfied' => $user->verified
      ));
      // Send user an email
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
        'host' => $_SERVER['HTTP_HOST'],
        'email' => $user->email,
        'date' => time(),
        'recipient_title' => $user->getTitle(),
        'recipient_link' => $user->getHref(),
        'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
        'object_link' => $this->_helper->url->url(array('controller' => 'auth', 'action' => 'reset', 'code' => $code, 'uid' => $user->getIdentity()), 'siteotpverifier_extended', 'true'),
        'queue' => false,
      ));
      $db->commit();
      return;
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function chooseAction()
  {
    $search = $this->_getParam('search');
    $searchType = $this->_getParam('type');
    $finds = array('email', 'phoneno');
    if( empty($search) || !in_array($searchType, $finds) || Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    if ($searchType == 'phoneno') {
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
      ->fetchRow(array($searchType . ' = ?' => $search));
      $user = Engine_Api::_()->getItem('user', $otpUser->user_id);
    } else {
      $user = Engine_Api::_()->getDbtable('users', 'user')
      ->fetchRow(array($searchType . ' = ?' => $search));
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }

    if( empty($otpUser->phoneno) ) {
      $this->view->sent = true;
      return;
    }


    $this->view->sent = false;
    // Make form
    $this->view->form = $form = new Siteotpverifier_Form_Auth_Choose();

    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    //$values['option'] 1 email 0 phoneno
    // Ok now we can do the fun stuff
    if( !empty($values['option']) ) {
      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'user');
      $db = $forgotTable->getAdapter();
      $db->beginTransaction();
      try {
        // Delete any existing reset password codes
        $forgotTable->delete(array(
          'user_id = ?' => $user->getIdentity(),
        ));

        // Create a new reset password code
        $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
        $forgotTable->insert(array(
          'user_id' => $user->getIdentity(),
          'code' => $code,
          'creation_date' => date('Y-m-d H:i:s'),
        ));
        // Send user an email
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
          'host' => $_SERVER['HTTP_HOST'],
          'email' => $user->email,
          'date' => time(),
          'recipient_title' => $user->getTitle(),
          'recipient_link' => $user->getHref(),
          'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
          'object_link' => $this->_helper->url->url(array('controller' => 'auth', 'action' => 'reset', 'code' => $code, 'uid' => $user->getIdentity()), 'siteotpverifier_extended', 'true'),
          'queue' => false,
        ));
        // Show success
        $this->view->sent = true;
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    } else {
      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
      $type = 'forgot';
      // genrate OTP code codes
      $response = $forgotTable->createCode($type, $user);
      if( !empty($response['error']) ) {
        $translate = Zend_Registry::get('Zend_Translate');
        $error = $translate->translate($response['error']);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
      $code = $response['code'];
      //code for sending code to phone no.
      $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCode($otpUser, $code, $type);
      return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'verify', 'search' => $search, 'type' => $searchType), 'siteotpverifier_extended', false);
    }
  }

  public function verifyAction()
  {
    // no logged in users
    $this->view->url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'default', true);

    $search = $this->_getParam('search');
    $searchType = $this->_getParam('type');
    $finds = array('email', 'phoneno');
    if( empty($search) || !in_array($searchType, $finds) || Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    if ($searchType == 'phoneno') {
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
      ->fetchRow(array($searchType . ' = ?' => $search));
      $user = Engine_Api::_()->getItem('user', $otpUser->user_id);
    } else {
      $user = Engine_Api::_()->getDbtable('users', 'user')
      ->fetchRow(array($searchType . ' = ?' => $search));
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }

    // Check user
    if( !$otpUser || !$otpUser->getIdentity() || !$otpUser->phoneno ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    // Check for empty params
    $this->view->user_id = $user_id = $user->getIdentity();

    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    $this->view->form = $form = new Siteotpverifier_Form_Auth_Otpverifier();
    $phoneno = $otpUser->country_code . $otpUser->phoneno;
    $form->formdata($phoneno);
    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    $translate = Zend_Registry::get('Zend_Translate');
    // Check code
    $forgotSelect = $forgotTable->select()
        ->where('user_id = ?', $user->getIdentity())
        ->where('code = ?', $values['code'])->where('type = ?', 'forgot');
    $forgotRow = $forgotTable->fetchRow($forgotSelect);
    if( !$forgotRow || (int) $forgotRow->user_id !== (int) $user->getIdentity() ) {
      $form->addError($translate->translate("OTP entered is not valid."));
      return;
    }
    $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    // Code expired
    // Note: Let's set the current timeout for 10 minutes for now
    $min_creation_date = time() - ($expiaryTime);
    if( strtotime($forgotRow->modified_date) < $min_creation_date ) { // @todo The strtotime might not work exactly right
      $form->addError($translate->translate("The OTP code you entered has expired. Please click on 'RESEND' to get new OTP code."));
      return;
    }
    $forgotTable->update(array('verfied' => 1), array('code =?' => $values['code'], 'type =?' => 'forgot', 'user_id =?' => $user->getIdentity()));

    $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'reset', 'code' => $values['code'], 'uid' => $user->getIdentity()), 'siteotpverifier_extended', true);
  }

  public function resendAction()
  {

    $user_id = $this->_getParam('user_id');
    $type = $this->_getParam('type');
    $this->view->errormessage = '';
    $user = Engine_Api::_()->getItem('user', $user_id);
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      $this->view->otpSent = false;
    }
    $serviceenabled = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();
    if( !$serviceenabled ) {
      return;
    }
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    $response = $forgotTable->createCode($type, $user);
    if( !empty($response['error']) ) {
      $this->view->errormessage = $response['error'];
      return;
    }
    $code = $response['code'];
    $otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
    $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCode($otpUser, $code, $type);
    $this->view->otpSent = true;
  }

  public function resetAction()
  {
    // no logged in users
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    // Check for empty params
    $user_id = $this->_getParam('uid');
    $code = $this->_getParam('code');

    if( empty($user_id) || empty($code) ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Check user
    $user = Engine_Api::_()->getItem('user', $user_id);
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Check code
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    $forgotSelect = $forgotTable->select()
        ->where('user_id = ?', $user->getIdentity())
        ->where('code = ?', $code)->where('type = ?', 'forgot');

    $forgotRow = $forgotTable->fetchRow($forgotSelect);
    if( !$forgotRow || empty($forgotRow->verfied) || (int) $forgotRow->user_id !== (int) $user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    // Make form
    $this->view->form = $form = new User_Form_Auth_Reset();
    $form->setAction($this->_helper->url->url(array()));

    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $translate = Zend_Registry::get('Zend_Translate');
    // Check same password
    if( $values['password'] !== $values['password_confirm'] ) {
      $form->addError($translate->translate('The passwords you entered did not match.'));
      return;
    }

    // Db
    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Delete the lost password code now
      $forgotTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
        'type = ?' => 'forgot',
      ));

      // This gets handled by the post-update hook
      $user->password = $values['password'];
      $user->save();

      $db->commit();

      $this->view->reset = true;
      //return $this->_helper->redirector->gotoRoute(array(), 'user_login', true);
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function verifyMobileNoAction()
  {
    // Check post
    $this->view->otpSent = false;
    $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
    $phoneNo = $otpverifySession->phoneno;
    if( empty($phoneNo) ) {
      return;
    }
    $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
    Engine_Api::_()->getApi('core', 'siteotpverifier')->verifyMobileNo($phoneNo, $code);

    $this->view->otpSent = true;
  }

  public function setSessionAction()
  {
    $phone_no = $this->_getparam('phone_no');
    if( empty($phone_no) ) {
      return;
    }
    $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
    if( !empty($otpverifySession->phoneno) ) {
      $otpverifySession->phoneno = null;
      $otpverifySession->otp_code = null;
    }

    $otpverifySession->phoneno = $phone_no;
  }

  public function verificationAction()
  {
    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
    $this->view->dontallowtwostep = false;
    if( $loginoption == "default" || $loginoption == "both" ) {
      $this->view->dontallowtwostep = true;
    }

    $this->view->loginAllowed = $loginAllowed = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteotpverifier', 'login');

    $this->view->addnumber = false;
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id ) {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    } else {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject, null, 'edit'
    );

    // Render
    $this->_helper->content
      // ->setNoRender()
      ->setEnabled()
    ;
    $translate = Zend_Registry::get('Zend_Translate');
    $this->view->user = $user = $subject;
    $this->view->otpUser = $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    $this->view->allowOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enableotp', 1);
    if( empty($otpUser->phoneno) ) {
      $this->view->form = $form = new Siteotpverifier_Form_Addmobile();
      $form->setTitle($translate->translate(''))
        ->setDescription($translate->translate('Please select your country and edit / change your registered phone number.'));
      // Check request
      if( !$this->getRequest()->isPost() ) {
        return;
      }
      // Check data
      if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
      }
      $values = $form->getValues();
      $countrycode = $values['country_code'];
      $userTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
      $sqlquery = $userTable->select()
        ->from($userTable->info('name'), array('user_id'))
        ->where('phoneno = ?', $values['mobile']);
      $userAdded = $userTable->fetchRow($sqlquery);
      if( !empty($userAdded) ) {

        $form->addError($translate->translate("Someone already registered with this number please try another one."));
        return;
      }
      $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
      $mobileTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
      ));
      $mobileTable->insert(array(
        'user_id' => $user->getIdentity(),
        'phoneno' => $values['mobile'],
        'country_code' => $countrycode,
        'creation_date' => date('Y-m-d H:i:s'),
      ));
      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
      $type = 'add';
      // genrate OTP code codes
      $response = $forgotTable->createCode($type, $user);
      if( !empty($response['error']) ) {
        $translate = Zend_Registry::get('Zend_Translate');
        $error = $translate->translate($response['error']);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
      $code = $response['code'];
      //code for sending code to phone no.
      $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCodeWitoutUser($countrycode . $values['mobile'], $code, $type);
      $this->view->addnumber = true;
    }
  }

  public function codeVerificationAction()
  {
    $this->view->type = $type = $this->_getParam('type', 'edit');
    $this->_helper->layout->setLayout('default-simple');
    // Check for empty params
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');

    $user = Engine_Api::_()->user()->getViewer();
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $this->view->user_id = $user->getIdentity();
    $this->view->form = $form = new Siteotpverifier_Form_Auth_Otpverifier();
    $mobilenoTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
    $mobilenoSelect = $mobilenoTable->select()
      ->where('user_id = ?', $user->getIdentity());
    $mobilenoRow = $mobilenoTable->fetchRow($mobilenoSelect);
    if( $mobilenoRow ) {
      $phoneno = $mobilenoRow->country_code . $mobilenoRow->phoneno;
      $form->formdata($phoneno);
    }
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $translate = Zend_Registry::get('Zend_Translate');
    $values = $form->getValues();
    $forgotSelect = $forgotTable->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('code = ?', $values['code'])
      ->where('type = ?', $type);
    //echo $forgotSelect;
    $forgotRow = $forgotTable->fetchRow($forgotSelect);

    if( !$forgotRow || (int) $forgotRow->user_id !== (int) $user->getIdentity() ) {
      $form->addError($translate->translate("OTP entered is not valid."));
      return;
    }
    $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    // Code expired
    // Note: Let's set the current timeout for 10 minutes for now
    $min_creation_date = time() - ($expiaryTime);
    if( strtotime($forgotRow->creation_date) < $min_creation_date ) { // @todo The strtotime might not work exactly right
      $form->addError($translate->translate("The OTP code you entered has expired. Please click on 'RESEND' to get new OTP code."));
      return;
    }
    $forgotTable->delete(array(
      'user_id = ?' => $user->getIdentity(),
      'type = ?' => $type
    ));
    $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
    $mobileSelect = $mobileTable->select()
      ->where('user_id = ?', $user->getIdentity());
    $mobileRow = $mobileTable->fetchRow($mobileSelect);

    $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    $otpUser->country_code = $mobileRow->country_code;
    $otpUser->phoneno = $mobileRow->phoneno;
    $otpUser->save();
    $mobileTable->delete(array(
      'user_id = ?' => $user->getIdentity(),
    ));

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function enableVerificationAction()
  {
    //enable disbale modules 
    $user = Engine_Api::_()->user()->getViewer();
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    $enable_verification = $this->_getParam('enable_verification');
    $otpUser->enable_verification = $enable_verification;
    $otpUser->save();
    return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'verification'), 'default', true);
  }

  public function deleteMobileAction()
  {
    $user = Engine_Api::_()->user()->getViewer();
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    // Check post
    if( $this->getRequest()->isPost() ) {
      $otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
      $otpUser->phoneno = 0;
      $otpUser->save();

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
    }
  }

  public function editNumberAction()
  {

    $user = Engine_Api::_()->user()->getViewer();
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    $this->view->form = $form = new Siteotpverifier_Form_Addmobile();
    $translate = Zend_Registry::get('Zend_Translate');
    $form->setTitle($translate->translate('Edit Phone Number'))
      ->setDescription($translate->translate('Please select your country and edit / change your registered phone number.'));
    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    $countrycode = $values['country_code'];
    $userTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
    $sqlquery = $userTable->select()
      ->from($userTable->info('name'), array('user_id'))
      ->where('phoneno = ?', $values['mobile']);
    $userExist = $userTable->fetchRow($sqlquery);
    if( !empty($userExist) ) {
      $form->addError($translate->translate("Someone already registered with this number please try another one."));
      return;
    }
    $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
    $mobileTable->delete(array(
      'user_id = ?' => $user->getIdentity(),
    ));
    $mobileTable->insert(array(
      'user_id' => $user->getIdentity(),
      'phoneno' => $values['mobile'],
      'country_code' => $countrycode,
      'creation_date' => date('Y-m-d H:i:s'),
    ));
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');

    $type = 'edit';
    // genrate OTP code codes
    $response = $forgotTable->createCode($type, $user);
    if( !empty($response['error']) ) {
      $translate = Zend_Registry::get('Zend_Translate');
      $error = $translate->translate($response['error']);
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }
    $code = $response['code'];
    $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCodeWitoutUser($countrycode . $values['mobile'], $code, $type);

    $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'code-verification', 'type' => $type), 'default', false);
  }

  public function resendOtpAction()
  {

    $user_id = $this->_getParam('user_id');
    $type = $this->_getParam('type');
    $user = Engine_Api::_()->getItem('user', $user_id);
    if( !$user || !$user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      $this->view->otpSent = false;
    }

    $serviceenabled = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();
    if( $serviceenabled ) {
      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
// genrate OTP code codes
      $response = $forgotTable->createCode($type, $user);
      if( !empty($response['error']) ) {
        $this->view->errormessage = $response['error'];
        $this->view->otpSent = false;
        return;
      }
      $code = $response['code'];

      // Delete any existing reset password codes
      $forgotTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
        'type = ?' => $type
      ));
      $mobileTable = Engine_Api::_()->getDbtable('mobileno', 'siteotpverifier');
      $mobileSelect = $mobileTable->select()
        ->where('user_id = ?', $user->getIdentity());
      $mobileRow = $mobileTable->fetchRow($mobileSelect);
      $phone = $mobileRow->phoneno;
      $countrycode = $mobileRow->country_code;
      //code for sending code to phone no.
      $status = Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCodeWitoutUser($countrycode . $phone, $code, $type);
      $this->view->otpSent = true;
    }
  }

  public function showotpverificationAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->otpverifieda = false;
    $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
    $otpcode = $otpverifySession->otp_code;
    $phoneNo = $otpverifySession->phoneno;
    $this->view->form = $form = new Siteotpverifier_Form_Signup_Otpverify();
    $form->formdata($phoneNo);
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    $translate = Zend_Registry::get('Zend_Translate');
    $code = $values['Code'];

    $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    $min_creation_date = time() - ($expiaryTime);
    if( $otpverifySession->time < $min_creation_date ) {
      $form->addError($translate->translate("The OTP code you entered has expired. Please click on 'RESEND' to get new OTP code."));
      $this->view->otpverifieda = false;
    } elseif( $otpcode != $code ) {
      $form->addError($translate->translate("The OTP code you entered is invalid. Please enter the correct OTP code."));
      $this->view->otpverifieda = false;
    }
    $this->view->otpverifieda = true;
  }

  public function sendotponloginAction()
  {
    $this->view->otpsent = false;
    $this->view->errormessage = '';
    $translate = Zend_Registry::get('Zend_Translate');
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->view->errormessage = $translate->translate('You are already signed in.');
    }
    $phone_no = $this->_getparam('phone_no');
    $this->view->form = $form = new User_Form_Login();

    if( !$this->getRequest()->isPost() ) {
      $this->view->errormessage = $translate->translate('Invalid Request');
      return;
    }
    $data = $this->getRequest()->getPost();
    $emailName = $form->getEmailElementFieldName();
    if( empty($phone_no) ) {
      $this->view->errormessage = $translate->translate('Enter a valid Email Address or Phone Number');
      return;
    }
    // if( empty($data[$emailName]) || $data[$emailName] !== $phone_no ) {
    //   $this->view->errormessage = $translate->translate('Invalid Request');
    //   return;
    // }
    if( preg_match("/^([1-9][0-9]{4,15})$/", $phone_no) ) {
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')
        ->fetchRow(array('phoneno = ?' => $phone_no));
      $user = $otpUser ? Engine_Api::_()->getItem('user', $otpUser->user_id ) : null;
      $phoneno = $phone_no;
      $email = $user ? $user->email : null;
    } else {
      $user = Engine_Api::_()->getDbtable('users', 'user')
        ->fetchRow(array('email = ?' => $phone_no));
      $email = $phone_no;
      $otpUser = Engine_Api::_()->getDbtable('users', 'siteotpverifier')->getUser($user);
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
    if( !$user || !$user->getIdentity() ) {
      $this->view->errormessage = $translate->translate('No record of a member with that email or phone number was found.');
      // Register login
      Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
        'email' => $email,
        'ip' => $ipExpr,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'state' => 'no-member',
      ));
      return;
    }
    if( !$user->enabled ) {
      if( !$user->verified ) {
        $this->view->errormessage = $translate->translate('This account still requires either email verification.');
        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'disabled',
        ));

        return;
      } else if( !$user->approved ) {
        $this->view->errormessage = $translate->translate('This account still requires admin approval.');
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
    }

    if( empty($otpUser->phoneno) ) {
      $this->view->errormessage = $translate->translate('No Phone Number is registered with your account please enter password to login.');
      return;
    }
    // Register login
    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
      'user_id' => $user->getIdentity(),
      'email' => $email,
      'ip' => $ipExpr,
      'timestamp' => new Zend_Db_Expr('NOW()'),
      'state' => 'otpNotVerified',
    ));
    $sendTo = $this->_getparam('sendTo', 'mobile');
    $otpverificationSession = new Zend_Session_Namespace('Otp_verification');
    $otpverificationSession->unsetAll();
    $otpverificationSession->user_id = $user->getIdentity();
    $otpverificationSession->email = $email;
    $otpverificationSession->return_url = $this->_getparam('return_url');
    $otpverificationSession->sendTo = $sendTo;
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    $type = 'login';
    // genrate OTP code codes
    $response = $forgotTable->createCode($type, $user);
    if( !empty($response['error']) ) {
      $this->view->errormessage = $translate->translate($response['error']);
      $this->view->otpsent = false;
      return;
    }
    $code = $response['code'];
    if( $sendTo == 'mobile' ) {
      //code for sending code to phone no.
      Engine_Api::_()->getApi('core', 'siteotpverifier')->sendOtpCode($user, $code, $type);
    } else {
      // Send user an email
      $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
      $timestring = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);

      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'siteotpverifier_otpverify', array(
        'host' => $_SERVER['HTTP_HOST'],
        'email' => $user->email,
        'date' => time(),
        'recipient_title' => $user->getTitle(),
        'recipient_link' => $user->getHref(),
        'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
        'expirytime' => $timestring,
        'code' => $code,
        'queue' => false,
      ));
    }
    $this->view->errormessage = '';
    $this->view->otpsent = true;

    if(Engine_Api::_()->hasModuleBootstrap("sitemobile") && (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')||Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))) {
                  $this->view->body = $this->view->action('mobileindex', 'verifier', 'siteotpverifier', array(
                  'format' => 'html',
                  'onlyRenderForLogin' => true,
                  ));
   
    }else{
          $this->view->body = $this->view->action('index', 'verifier', 'siteotpverifier', array(
        'format' => 'html',
        'onlyRenderForLogin' => true,
      ));
    }

    $this->_helper->contextSwitch->initContext();
  }

}
