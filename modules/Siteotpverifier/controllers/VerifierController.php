<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    VerifierController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Siteotpverifier_VerifierController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    // Get user and session
    $user = Engine_Api::_()->user()->getViewer();
    if( $user && $user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $this->_session = new Zend_Session_Namespace('Otp_verification');

    // Check viewer and user
    if( !empty($this->_session->user_id) ) {
      $user = Engine_Api::_()->getItem('user', $this->_session->user_id);
    }
    // If no user, redirect to home?
    if( !$user || !$user->getIdentity() ) {
      $this->_session->unsetAll();
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $this->view->form = $form = new Siteotpverifier_Form_Auth_LoginOtpverifier();
    $otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
    $phoneno = $otpUser->country_code . $otpUser->phoneno;
    if(empty($this->_session->sendTo) || $this->_session->sendTo == 'mobile' ) {
      $form->setOTPMessage($phoneno, 'mobile');
    } else {
      $form->setOTPMessage($user->email, 'email');
    }
    $this->view->user_id = $user->getIdentity();
    if( $this->_getParam('onlyRenderForLogin', false) ) {
      return;
    }
    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $translate = Zend_Registry::get('Zend_Translate');
    $values = $form->getValues();
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    // Check code
    $forgotSelect = $forgotTable->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('code = ?', $values['code'])
      ->where('type= ?', 'login');

    $forgotRow = $forgotTable->fetchRow($forgotSelect);
    if( !$forgotRow ) {
      $form->addError($translate->translate("OTP entered is not valid."));
      return;
    }
    $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    // Code expired
    // Note: Let's set the current timeout for 10 minutes for now
    $relDate = new Zend_Date(time());
    $relDate->subSecond((int) $expiaryTime);
    if( strtotime($forgotRow->modified_date) < $relDate->getTimestamp() ) { // @todo The strtotime might not work exactly right
      $form->addError($translate->translate("The OTP code you entered has expired. Please click on 'RESEND' to get new OTP code."));
      return;
    }

    $this->_session = new Zend_Session_Namespace('Otp_verification');
    $forgotTable->delete(array(
      'user_id = ?' => $user->getIdentity(),
      'type = ?' => 'login'
    ));
    $email = $this->_session->email;
    $password = $this->_session->password;
    $return_url = $this->_session->return_url;
    $remember = $this->_session->remember;
    $this->_session->unsetAll();
    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
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
        $form->addError(Zend_Registry::get('Zend_Translate')->_('There appears to be a problem logging in. Please reset your password with the Forgot Password link.'));

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
      $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
      Engine_Api::_()->user()->setViewer();
      if( $loginoption === 'both' ) {
        Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
      } else {
        $authResult = Engine_Api::_()->user()->authenticate($email, $password);
        $authCode = $authResult->getCode();
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
    if( $remember ) {
      $lifetime = 1209600; // Two weeks
      Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
      Zend_Session::rememberMe($lifetime);
    }

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
      $uri = $return_url;
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
            echo $response['message'];
            return;
          } else if( !empty($response['redirect']) ) {
            return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
          }
        }
      }

      // Just redirect to home
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
  }
  public function mobileindexAction()
  {
    // Get user and session
    $user = Engine_Api::_()->user()->getViewer();
    if( $user && $user->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $this->_session = new Zend_Session_Namespace('Otp_verification');

    // Check viewer and user
    if( !empty($this->_session->user_id) ) {
      $user = Engine_Api::_()->getItem('user', $this->_session->user_id);
    }
    // If no user, redirect to home?
    if( !$user || !$user->getIdentity() ) {
      $this->_session->unsetAll();
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $this->view->form = $form = new Siteotpverifier_Form_Auth_LoginOtpverifier();
    $otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
    $phoneno = $otpUser->country_code . $otpUser->phoneno;
    if(empty($this->_session->sendTo) || $this->_session->sendTo == 'mobile' ) {
      $form->setOTPMessage($phoneno, 'mobile');
    } else {
      $form->setOTPMessage($user->email, 'email');
    }
    $this->view->user_id = $user->getIdentity();
    if( $this->_getParam('onlyRenderForLogin', false) ) {
      return;
    }
    // Check request
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $translate = Zend_Registry::get('Zend_Translate');
    $values = $form->getValues();
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
    // Check code
    $forgotSelect = $forgotTable->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('code = ?', $values['code'])
      ->where('type= ?', 'login');

    $forgotRow = $forgotTable->fetchRow($forgotSelect);
    if( !$forgotRow ) {
      $form->addError($translate->translate("OTP entered is not valid."));
      return;
    }
    $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    // Code expired
    // Note: Let's set the current timeout for 10 minutes for now
    $relDate = new Zend_Date(time());
    $relDate->subSecond((int) $expiaryTime);
    if( strtotime($forgotRow->modified_date) < $relDate->getTimestamp() ) { // @todo The strtotime might not work exactly right
      $form->addError($translate->translate("The OTP code you entered has expired. Please click on 'RESEND' to get new OTP code."));
      return;
    }

    $this->_session = new Zend_Session_Namespace('Otp_verification');
    $forgotTable->delete(array(
      'user_id = ?' => $user->getIdentity(),
      'type = ?' => 'login'
    ));
    $email = $this->_session->email;
    $password = $this->_session->password;
    $return_url = $this->_session->return_url;
    $remember = $this->_session->remember;
    $this->_session->unsetAll();
    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
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
        $form->addError(Zend_Registry::get('Zend_Translate')->_('There appears to be a problem logging in. Please reset your password with the Forgot Password link.'));

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
      $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
      Engine_Api::_()->user()->setViewer();
      if( $loginoption === 'both' ) {
        Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
      } else {
        $authResult = Engine_Api::_()->user()->authenticate($email, $password);
        $authCode = $authResult->getCode();
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
    if( $remember ) {
      $lifetime = 1209600; // Two weeks
      Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
      Zend_Session::rememberMe($lifetime);
    }

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
      $uri = $return_url;
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
            echo $response['message'];
            return;
          } else if( !empty($response['redirect']) ) {
            return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
          }
        }
      }

      // Just redirect to home
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
  }

  public function sendmailAction()
  {

    $user = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Otp_verification');

    // Check viewer and user
    if( !$user || !$user->getIdentity() ) {
      if( !empty($this->_session->user_id) ) {
        $user = Engine_Api::_()->getItem('user', $this->_session->user_id);
      }
      // If no user, redirect to home?
      if( !$user || !$user->getIdentity() ) {
        $this->_session->unsetAll();
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      }
    }
    if( $this->getRequest()->isPost() ) {

      $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
      $db = $forgotTable->getAdapter();
      $db->beginTransaction();
      // Delete any existing reset password codes
      $forgotTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
        'type = ?' => 'login'
      ));

      // Create a new reset password code
      $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
      $forgotTable->insert(array(
        'user_id' => $user->getIdentity(),
        'code' => $code,
        'creation_date' => date('Y-m-d H:i:s'),
        'type' => 'login',
      ));
      //code for sending code to mail.
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
      $db->commit();

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
    }
  }

//  public function loginwithotpAction()
//  {
//    // Get user and session
//    $user = Engine_Api::_()->user()->getViewer();
//    $this->_session = new Zend_Session_Namespace('Otp_verification');
//    $this->view->userloggedin = false;
//    // Check viewer and user
//    if( !$user || !$user->getIdentity() ) {
//      if( !empty($this->_session->user_id) ) {
//        $user = Engine_Api::_()->getItem('user', $this->_session->user_id);
//      }
//      // If no user, redirect to home?
//      if( !$user || !$user->getIdentity() ) {
//        $this->_session->unsetAll();
//        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//      }
//    }
//
//    $this->view->form = $form = new Siteotpverifier_Form_Auth_LoginOtpverifier();
//    $phoneno = $user->country_code . $user->phoneno;
//    $form->formdata($phoneno);
//    $this->view->user_id = $user->getIdentity();
//    if( $this->_getParam('onlyRenderForLogin', false) ) {
//      return;
//    }
//    // Check request
//    if( !$this->getRequest()->isPost() ) {
//      return;
//    }
//    // Check data
//    if( !$form->isValid($this->getRequest()->getPost()) ) {
//      return;
//    }
//
//    $values = $form->getValues();
//    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'siteotpverifier');
//    // Check code
//    $forgotSelect = $forgotTable->select()
//      ->where('user_id = ?', $user->getIdentity())
//      ->where('code = ?', $values['code'])
//      ->where('type= ?', 'login');
//
//    $forgotRow = $forgotTable->fetchRow($forgotSelect);
//    if( !$forgotRow || (int) $forgotRow->user_id !== (int) $user->getIdentity() ) {
//      $form->addError(Zend_Registry::get('Zend_Translate')->_("OTP entered is not valid."));
//      return;
//    }
//    $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
//    // Code expired
//    // Note: Let's set the current timeout for 10 minutes for now
//    $min_creation_date = time() - ($expiaryTime);
//    if( strtotime($forgotRow->creation_date) < $min_creation_date ) { // @todo The strtotime might not work exactly right
//      $form->addError("OTP is already expired please resend otp.");
//      return;
//    }
//
//    $forgotTable->update(array('verfied' => 1), array('code =?' => $values['code'], 'user_id =?' => $user->getIdentity(), 'type=?' => 'login'));
//    $forgotTable->delete(array(
//      'user_id = ?' => $user->getIdentity(),
//      'type = ?' => 'login'
//    ));
//
//    $email = $this->_session->email;
//    $this->_session->unsetAll();
//    $db = Engine_Db_Table::getDefaultAdapter();
//    $ipObj = new Engine_IP();
//    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
//    if( $user ) {
//      $viewer = $user;
//      $viewer_id = $user->getIdentity();
//      $email = $user->email;
//      $user_id = $user->getIdentity();
//      // Handle subscriptions
//      if( Engine_Api::_()->hasModuleBootstrap('payment') ) {
//        // Check for the user's plan
//        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
//        if( !$subscriptionsTable->check($user) ) {
//          // Register login
//          Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
//            'user_id' => $user->getIdentity(),
//            'email' => $user->email,
//            'ip' => $ipExpr,
//            'timestamp' => new Zend_Db_Expr('NOW()'),
//            'state' => 'unpaid',
//          ));
//          // Redirect to subscription page
//          $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
//          $subscriptionSession->unsetAll();
//          $subscriptionSession->user_id = $user->getIdentity();
//          return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
//              'controller' => 'subscription', 'action' => 'index'), 'default', true);
//        }
//      }
//
//      // Run pre login hook
//      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
//      foreach( (array) $event->getResponses() as $response ) {
//        if( is_array($response) ) {
//          if( !empty($response['error']) && !empty($response['message']) ) {
//            return false;
//          } else if( !empty($response['redirect']) ) {
//            $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
//          } else {
//            continue;
//          }
//
//          // Register login
//          Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
//            'user_id' => $user->getIdentity(),
//            'email' => $email,
//            'ip' => $ipExpr,
//            'timestamp' => new Zend_Db_Expr('NOW()'),
//            'state' => 'third-party',
//          ));
//
//          // Return
//          return false;
//        }
//      }
//      // Register login
//      $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
//      $loginTable->insert(array(
//        'user_id' => $user->getIdentity(),
//        'email' => $email,
//        'ip' => $ipExpr,
//        'timestamp' => new Zend_Db_Expr('NOW()'),
//        'state' => 'success',
//        'active' => true,
//      ));
//      $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();
//      Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
//      Engine_Api::_()->user()->setViewer();
//      // Increment sign-in count
//      Engine_Api::_()->getDbtable('statistics', 'core')
//        ->increment('user.logins');
//
//      $viewer = Engine_Api::_()->user()->getViewer();
//      if( $viewer->getIdentity() ) {
//        $viewer->lastlogin_date = date("Y-m-d H:i:s");
//        if( 'cli' !== PHP_SAPI ) {
//          $viewer->lastlogin_ip = $ipExpr;
//        }
//        $viewer->save();
//        Engine_Api::_()->getDbtable('actions', 'activity')
//          ->addActivity($viewer, $viewer, 'login');
//      }
//
//      // Run post login hook
//      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);
//
//      // Redirect by hook
//      foreach( (array) $event->getResponses() as $response ) {
//        if( is_array($response) ) {
//          if( !empty($response['error']) && !empty($response['message']) ) {
//            return false;
//          } else if( !empty($response['redirect']) ) {
//            return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
//          }
//        }
//      }
//
//      $this->view->userloggedin = true;
//      // Just redirect to home
//      //return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
//    }
//  }

}
