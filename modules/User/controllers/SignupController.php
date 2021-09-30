<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: SignupController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_SignupController extends Core_Controller_Action_Standard
{
  public function init()
  {
  }
  
  public function indexAction()
  {
    // Render
    // todo: 5.2.1 Upgrade => Added redirect code logic
    // getting the return url
    $custom_redirect_url = $this->_getParam('custom_redirect_url',  null);
    $given_redirect_url = $this->_getParam('given_redirect_url',  null);

    $disableContent = $this->_getParam('disableContent', 0);
    if( !$disableContent ) {
      $this->_helper->content
        ->setEnabled()
        ;
    }
    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // If the user is logged in, they can't sign up now can they?
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    
    $formSequenceHelper = $this->_helper->formSequence;
    foreach( Engine_Api::_()->getDbtable('signup', 'user')->fetchAll() as $row ) {
      if( $row->enable == 1 ) {
        $class = $row->class;
        $formSequenceHelper->setPlugin(new $class, $row->order);
      }
    }

    // This will handle everything until done, where it will return true
    if( !$this->_helper->formSequence() ) {
      return;
    }

    // Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    // todo: 5.2.1 Upgrade => Added the functionalities back after upgrade
    // update the manage admin table for invited user as org admin start
    $userId = $viewer->getIdentity();
    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');
    $email = $userTable->select()->from($userTableName, 'email')->where('user_id =?', $userId)->query()->fetchColumn();
    if($email) {

      // custom code

      // Organization admin manage table admin if exists update
      $ManageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
      $ManageAdminsTableName = $ManageAdminsTable->info('name');
      $member_email_managetable = $ManageAdminsTable->select()
          ->from($ManageAdminsTableName)
          ->where('member_email = ?', $email)->query()->fetchColumn();
      if($member_email_managetable) {
          $ManageAdminsTable->update(array(
              'user_id' => $userId,
              'member_email' =>  null
          ), array(
              'member_email = ?' =>  $email
          ));
      }

      // Project admin - list table admin if exists update
      $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
      $listItemTableName = $listItemTable->info('name');
      $member_email_listtable = $listItemTable->select()
          ->from($listItemTableName)
          ->where('member_email = ?', $email)->query()->fetchColumn();
      if($member_email_listtable) {
          $listItemTable->update(array(
              'child_id' => $userId,
              'member_email' =>  null
          ), array(
              'member_email = ?' =>  $email
          ));
      }

      // Project team member - member table admin
//      $membersTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
//      $membersTableName = $membersTable->info('name');
//      $member_email_membertable = $membersTable->select()
//        ->from($membersTableName)
//        ->where('member_email = ?', $email)->query()->fetchColumn();
//        if($member_email_membertable) {
//          $membersTable->update(array(
//              'user_id' => $userId,
//              'member_email' =>  null
//          ), array(
//              'member_email = ?' =>  $email
//          ));
//        }
      }
      // update the mangae admin table for invited user as org admin end

    // Run post signup hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserSignupAfter', $viewer);
    $responses = $event->getResponses();
    if( $responses ){
      foreach( $event->getResponses() as $response ) {
        if( is_array($response) ) {
          // Clear login status
          if( !empty($response['error']) ) {
            Engine_Api::_()->user()->setViewer(null);
            Engine_Api::_()->user()->getAuth()->getStorage()->clear();
          }
          // Redirect
          if( !empty($response['redirect']) ) {
            return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
          }
        }
      }
    }
    
    // Handle subscriptions
    if( Engine_Api::_()->hasModuleBootstrap('payment') ) {
      // Check for the user's plan
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      if (!$subscriptionsTable->check($viewer) && $this->allowDefaultPlan($viewer)) {
        // Handle default payment plan
        $defaultSubscription = null;
        try {
          $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
          if( $subscriptionsTable ) {
            $defaultSubscription = $subscriptionsTable->activateDefaultPlan($viewer);
            if( $defaultSubscription ) {
              // Re-process enabled?
              $viewer->enabled = true;
              $viewer->save();
            }
          }
        } catch( Exception $e ) {
          // Silence
        }
        if( !$defaultSubscription ) {
          // Redirect to subscription page, log the user out, and set the user id
          // in the payment session
          $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
          $subscriptionSession->user_id = $viewer->getIdentity();
          
          Engine_Api::_()->user()->setViewer(null);
          Engine_Api::_()->user()->getAuth()->getStorage()->clear();

          if( !empty($subscriptionSession->subscription_id) ) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
              'controller' => 'subscription', 'action' => 'gateway'), 'default', true);
          } else {
            return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
              'controller' => 'subscription', 'action' => 'index'), 'default', true);
          }
        }
      }
    }

    // Handle email verification or pending approval
    if( !$viewer->enabled ) {
      Engine_Api::_()->user()->setViewer(null);
      Engine_Api::_()->user()->getAuth()->getStorage()->clear();

      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
      $confirmSession->approved = $viewer->approved;
      $confirmSession->verified = $viewer->verified;
      $confirmSession->enabled  = $viewer->enabled;
      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
    }

    // Handle normal signup
    else {
      Engine_Api::_()->user()->getAuth()->getStorage()->write($viewer->getIdentity());
      Engine_Hooks_Dispatcher::getInstance()
          ->callEvent('onUserEnable', array('user' => $viewer, 'shouldSendEmail' => false));
    }

    // Set lastlogin_date here to prevent issues with payment
    if( $viewer->getIdentity() ) {
      $viewer->lastlogin_date = date("Y-m-d H:i:s");
      if( 'cli' !== PHP_SAPI ) {
        $ipObj = new Engine_IP();
        $viewer->lastlogin_ip = $ipObj->toBinary();
      }
      $viewer->save();
    }

    // update name in db
    $data = $_POST;
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $viewer->setDisplayName(array('first_name' => $first_name, 'last_name' => $last_name));
    $viewer->save();

    // update location in db
    $locationTable = Engine_Api::_()->getDbtable('locations', 'user');
    $loctionV['location'] = $data['location'];;
    $loctionV['latitude'] = $data['latitude'];;
    $loctionV['longitude'] = $data['longitude'];;
    $loctionV['formatted_address'] = $data['formatted_address'];;
    $loctionV['country'] = $data['country'];;
    $loctionV['state'] = $data['state'];;
    $loctionV['zipcode'] = $data['zipcode'];;
    $loctionV['city'] = $data['city'];;
    $loctionV['address'] = $data['address'];;
    $loctionV['zoom'] = 16;
    $loctionV['user_id'] = $viewer->getIdentity();
    $locationRow = $locationTable->createRow();
    $locationRow->setFromArray($loctionV);
    $locationRow->save();

    // todo: 5.2.1 Upgrade => Added redirect code logic
    // fetching the return url
    if(!empty($custom_redirect_url) && $custom_redirect_url == '/'){
        $return_url = '/members/home';
        return $this->_redirect($return_url, array('prependBase' => false));
    } elseif(!empty($custom_redirect_url) && $custom_redirect_url == '/network/'){
        $return_url = '/network/members/home';
        return $this->_redirect($return_url, array('prependBase' => false));
    }elseif(!empty($custom_redirect_url) && ($custom_redirect_url != '/network/' && $custom_redirect_url == '/' )){
        $return_url = $custom_redirect_url;
        return $this->_redirect($return_url, array('prependBase' => false));
    } elseif(!empty($given_redirect_url)){
        $return_url = $given_redirect_url;
        // if url is base64 encoded
        if( substr($return_url, 0, 3) == '64-' ) {
            $return_url = base64_decode(substr($return_url, 3));
            if($return_url == '/network/'){
                return $this->_redirect('/network/members/home', array('prependBase' => false));
            }elseif ($return_url == '/net/'){
                return $this->_redirect('/net/members/home', array('prependBase' => false));
            }elseif ($return_url == '/'){
                return $this->_redirect('/members/home', array('prependBase' => false));
            }else{
                return $this->_redirect($return_url, array('prependBase' => false));
            }
        }else{
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
    }else{
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
  }

  public function verifyAction()
  {
    $verify = $this->_getParam('verify');
    $token = $this->_getParam('token');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // No code or token
    if( !$verify || !$token ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('The email or verification code was not valid.');
      return;
    }

    // Get verify user
    $userId = Engine_Api::_()->user()->getUserIdFromToken($token);
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $user = $userTable->fetchRow($userTable->select()->where('user_id = ?', $userId));

    if( !$user || !$user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('The email does not match an existing user.');
      return;
    }

    // If the user is already verified, just redirect
    if( $user->verified ) {
      $this->view->status = true;
      return;
    }

    // Get verify row
    $verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
    $verifyRow = $verifyTable->fetchRow($verifyTable->select()->where('user_id = ?', $user->getIdentity()));

    if( !$verifyRow || $verifyRow->code != $verify ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('There is no verification info for that user.');
      return;
    }
    
    // Process
    $db = $verifyTable->getAdapter();
    $db->beginTransaction();

    try {

      $verifyRow->delete();
      $user->verified = 1;
      $user->save();
      
      // Send Welcome E-mail
      $verifyemail = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.verifyemail', 0);
      if($verifyemail == 3) {
        $host = $_SERVER['HTTP_HOST'];
        if($host == 'stage.impactx.co'){
          $host = $host . '/network/';
        }else{
          $host = $host . '/net/';
        }
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user,'core_welcome', array('host' => $host, 'email' => $user->email, 'date' => time(), 'recipient_title' => $user->getTitle(), 'recipient_link' => $user->getHref(), 'recipient_photo' => $user->getPhotoUrl('thumb.icon'), 'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true)));
      }
      
      if( $user->enabled ) {
        Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserEnable', array('user' => $user, 'shouldSendEmail' => false));
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
  }

  public function takenAction()
  {
    $username = $this->_getParam('username');
    $email = $this->_getParam('email');

    // Sent both or neither username/email
    if( (bool) $username == (bool) $email )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid param count');
      return;
    }

    // Username must be alnum
    if( $username ) {
      $validator = new Zend_Validate_Alnum();
      if( !$validator->isValid($username) )
      {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid param value');
        //$this->view->errors = $validator->getErrors();
        return;
      }

      $table = Engine_Api::_()->getItemTable('user');
      $row = $table->fetchRow($table->select()->where('username = ?', $username)->limit(1));

      $this->view->status = true;
      $this->view->taken = ( $row !== null );
      return;
    }

    if( $email ) {
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);
      if( !$validator->isValid($email) )
      {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid param value');
        //$this->view->errors = $validator->getErrors();
        return;
      }

      $table = Engine_Api::_()->getItemTable('user');
      $row = $table->fetchRow($table->select()->where('email = ?', $email)->limit(1));

      $this->view->status = true;
      $this->view->taken = ( $row !== null );
      return;
    }
  }

  public function confirmAction()
  {
    $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
    $this->view->approved = $this->_getParam('approved', $confirmSession->approved);
    $this->view->verified = $this->_getParam('verified', $confirmSession->verified);
    $this->view->enabled  = $this->_getParam('verified', $confirmSession->enabled);
  }


  public function resendAction()
  {
    $token = $this->_getParam('token');
    $userId = Engine_Api::_()->user()->getUserIdFromToken($token);
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() || !$userId ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $user = $userTable->fetchRow($userTable->select()->where('user_id = ?', $userId));
    
    if( !$user ) {
      $this->view->error = 'That email was not found in our records.';
      return;
    }
    if( $user->verified ) {
      $this->view->error = 'That email has already been verified. You may now login.';
      return;
    }
    
    // resend verify email
    $verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
    $verifyRow = $verifyTable->fetchRow($verifyTable->select()->where('user_id = ?', $user->user_id)->limit(1));
    
    if( !$verifyRow ) {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $verifyRow = $verifyTable->createRow();
      $verifyRow->user_id = $user->getIdentity();
      $verifyRow->code = md5($user->email
          . $user->creation_date
          . $settings->getSetting('core.secret', 'staticSalt')
          . (string) rand(1000000, 9999999));
      $verifyRow->date = $user->creation_date;
      $verifyRow->save();
    }

    $host = $_SERVER['HTTP_HOST'];
    if($host == 'stage.impactx.co'){
      $host = $host . '/network/';
    }else{
      $host = $host . '/net/';
    }
    $mailParams = array(
      'host' => $host,
      'email' => $user->email,
      'date' => time(),
      'recipient_title' => $user->getTitle(),
      'recipient_link' => $user->getHref(),
      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
      'queue' => false,
    );
    
    $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'verify',
          //'email' => $email,
          //'verify' => $verifyRow->code
        ), 'user_signup', true)
      . '?'
      . http_build_query(array('token' => $token, 'verify' => $verifyRow->code))
      ;
    
    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      $user,
      'core_verification',
      $mailParams
    );
  }

  private function allowDefaultPlan($user)
  {
    $mappedProfileTypes = Engine_Api::_()->getDbtable('mapProfileTypeLevels', 'authorization')
      ->getMappedProfileTypeIds($user->level_id);
    $mappedPackages = Engine_Api::_()->getDbtable('packages', 'payment')->fetchRow(array(
      'level_id = ?' => $user->level_id,
      'enabled = ?' => true,
      'signup = ?' => true,
    ));

    return count($mappedProfileTypes) == 0  || $mappedPackages ||
      !Engine_Api::_()->getDbtable('subscriptions', 'payment')->isSignupSubscriptionEnable();
  }
}
