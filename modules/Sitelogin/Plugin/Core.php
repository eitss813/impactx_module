<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    include_once APPLICATION_PATH . '/application/modules/Seaocore/Plugin/Signup/Account.php';
  }

  public function onUserDeleteBefore($event) {
        $payload = $event->getPayload();
        if ($payload instanceof User_Model_User) {

            // Remove google/linkedin associations
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->delete('engine4_sitelogin_google', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_linkedin', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_instagram', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_pinterest', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_yahoo', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_flickr', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_vk', array('user_id = ?' => $payload->getIdentity(),));
            $db->delete('engine4_sitelogin_outlook', array('user_id = ?' => $payload->getIdentity(),));
        }

        unset($_SESSION['google_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['vk_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['access_token']);
        unset($_SESSION['linkedin_access_token']);
        unset($_SESSION['instagram_access_token']);
        unset($_SESSION['pinterest_access_token']);
        unset($_SESSION['yahoo_access_token']);
        unset($_SESSION['flickr_access_token']);
        unset($_SESSION['vk_access_token']);
        unset($_SESSION['outlook_access_token']);
        unset($_SESSION['google_access_token']);
    }

    public function onRenderLayoutDefault($event, $mode = null) {

    }

    public function onRenderLayoutDefaultSimple($event) {
        return $this->onRenderLayoutDefault($event, 'simple');
    }

    public function onRenderLayoutMobileDefault($event) {
        // Forward
        return $this->onRenderLayoutDefault($event);
    }

    public function onRenderLayoutMobileDefaultSimple($event) {
        // Forward
        return $this->onRenderLayoutDefault($event);
    }

    public function onUserLogoutBefore() {
        unset($_SESSION['google_signup']);
        unset($_SESSION['facebook_signup']);
        unset($_SESSION['twitter_signup']);
        unset($_SESSION['linkedin_signup']);
        unset($_SESSION['instagram_signup']);
        unset($_SESSION['pinterest_signup']);
        unset($_SESSION['flickr_signup']);
        unset($_SESSION['vk_signup']);
        unset($_SESSION['vk_email']);
        unset($_SESSION['outlook_signup']);
        unset($_SESSION['access_token']);
        unset($_SESSION['linkedin_access_token']);
        unset($_SESSION['instagram_access_token']);
        unset($_SESSION['pinterest_access_token']);
        unset($_SESSION['yahoo_access_token']);
        unset($_SESSION['flickr_access_token']);
        unset($_SESSION['vk_access_token']);
        unset($_SESSION['outlook_access_token']);
        unset($_SESSION['google_access_token']);
    }

    public function onUserSignupAfter($event) {
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        // Handle subscriptions
        if( Engine_Api::_()->hasModuleBootstrap('payment') ) {
            // Check for the user's plan
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            if( !$subscriptionsTable->check($viewer) ) {
    
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
                        
                        $redirector->gotoRoute(array('module' => 'payment',
                        'controller' => 'subscription', 'action' => 'gateway'), 'default', true);

                    } else {
                        $redirector->gotoRoute(array('module' => 'payment',
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
            $redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
            
        } // Handle normal signup
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
        
//        $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.redirectlink', 2);
//        // Redirect to referer page
//        if ($redirectlink == 2) {
//            $url = $view->baseUrl() . '/members/home';
//        } elseif ($redirectlink == 1) {
//            $url = $view->baseUrl() . '/profile/' . $viewer->getIdentity();
//        } elseif ($redirectlink == 3) {
//            $url = $view->baseUrl() . '/members/edit/profile';
//        } else {
//            $customeUrl = $redirectlink = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitelogin.customurl', '');
//            $url = $view->baseUrl() . $customeUrl;
//        }
//        $event->addResponse(array(
//            'redirect' => $url,
//        ));
     }

}
