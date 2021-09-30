<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Siteloginconnect
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.Seaocores.com/license/
 * @version    $Id: SettingsController.php 2010-11-18 9:40:21Z Siteloginconnect $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_SettingsController extends Core_Controller_Action_User
{
  protected $_user;
  
  public function init()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id )
    {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    }
    else
    {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );
    unset($_SESSION['access_token']);
    // Set up navigation
    // $this->view->navigation = $navigation = Engine_Api::_()
    //   ->getApi('menus', 'core')
    //   ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
    
    $contextSwitch = $this->_helper->contextSwitch;
    $contextSwitch
      //->addActionContext('reject', 'json')
      ->initContext();
      
    // Render
    $this->_helper->content
                  ->setContentName("user_settings_general")
                //   ->setNoRender()
                  ->setEnabled();

  }

  public function generalAction()
  {
    // Config vars
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $userSettings = Engine_Api::_()->getDbtable('settings', 'user');
    $user = Engine_Api::_()->core()->getSubject();
    $this->view->form = $form = new Siteloginconnect_Form_Settings_General(array(
      'item' => $user
    ));

    $form->removeElement('accountType');
    
    // Removed disabled features
    if( $form->getElement('username') && (!Engine_Api::_()->authorization()->isAllowed('user', $user, 'username') ||
        Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username', 1) <= 0) ) {
      $form->removeElement('username');
    }

    //Set names of those elements that need to be removed and are also dependent on POST
    $removeElements = array();
    $socialsiteslink = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteloginconnect.global.linkaccount',array());
    
    foreach ($socialsiteslink as $socialsite) {
        if($socialsite=='facebook' || $socialsite=='twitter')
          continue;
        $socialsiteloggedin = $this->_getUserLogin($socialsite);
        if($socialsiteloggedin ) {
            $removeElements[$socialsite] = $socialsite;
            $form->getElement($socialsite.'_id')->setAttrib('checked', true);
        } else {
            $removeElements[$socialsite.'_id'] = $socialsite.'_id';
        }
    }

    // Facebook
    if(in_array('facebook', $socialsiteslink)){
        if( 'none' != $settings->getSetting('core.facebook.enable', 'none') ) {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookTable->getApi();
          if( $facebook && $facebook->getUser() ) {
            $removeElements['facebook'] = 'facebook';
            $form->getElement('facebook_id')->setAttrib('checked', true);
          } else {
            $removeElements['facebook_id'] = 'facebook_id';
          }
        } else {
          // these should already be removed inside the form, but lets do it again.
          @$form->removeElement('facebook');
          @$form->removeElement('facebook_id');
        }

        if ( in_array('facebook_id', $removeElements) && $this->_getParam('already_integrated_fb_account') ) {
            $form->facebook->addError('Facebook account you\'re trying to connect is already connected to another account.');
        }
    }
    // Twitter
    if(in_array('twitter', $socialsiteslink)){
        if( 'none' != $settings->getSetting('core.twitter.enable', 'none') ) {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          $twitter = $twitterTable->getApi();
          if( $twitter && $twitterTable->isConnected() ) {
            $form->removeElement('twitter');
            $form->getElement('twitter_id')->setAttrib('checked', true);
          } else {
            $form->removeElement('twitter_id');
          }
        } else {
          // these should already be removed inside the form, but lets do it again.
          @$form->removeElement('twitter');
          @$form->removeElement('twitter_id');
        }
    }

    // Check if post and populate
    if( !$this->getRequest()->isPost() ) {
      foreach($removeElements as $elementName) {
        $form->removeElement($elementName);
      }
      $form->populate($user->toArray());
      $form->populate(array(
        'janrainnoshare' => $userSettings->getSetting($user, 'janrain.no-share', 0),
      ));
      
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    // Check if valid
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // -- Process --

    $values = $form->getValues();

    // Check email against banned list if necessary
    if( ($emailEl = $form->getElement('email')) &&
        isset($values['email']) &&
        $values['email'] != $user->email ) {
      $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
      if( $bannedEmailsTable->isEmailBanned($values['email']) ) {
        return $emailEl->addError('This email address is not available, please use another one.');
      }
    }

    // Check username against banned list if necessary
    if( ($usernameEl = $form->getElement('username')) &&
        isset($values['username']) &&
        $values['username'] != $user->username ) {
      $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
      if( $bannedUsernamesTable->isUsernameBanned($values['username']) ) {
        return $usernameEl->addError('This profile address is not available, please use another one.');
      }
    }

    // Set values for user object
    $user->setFromArray($values);

    // If username is changed
    $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
    $user->setDisplayName($aliasValues);
    
    $user->save();

    // Update facebook settings
    if( isset($facebook) && $form->getElement('facebook_id') ) {
      if( $facebook->getUser() ) {
        if( empty($values['facebook_id']) ) {
          // Remove integration
          $facebookTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
          ));
          $facebook->clearAllPersistentData();
          unset($removeElements['facebook']);
          $removeElements['facebook_id'] = 'facebook_id';
        }
      }
    }

    // Update twitter settings
    if( isset($twitter) && $form->getElement('twitter_id') ) {
      if( $twitterTable->isConnected() ) {
        if( empty($values['twitter_id']) ) {
          // Remove integration
          $twitterTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
          ));
          unset($_SESSION['twitter_token2']);
          unset($_SESSION['twitter_secret2']);
          unset($_SESSION['twitter_token']);
          unset($_SESSION['twitter_secret']);
        }
      }
    }

    foreach ($socialsiteslink as $socialsite) {
        if($socialsite=='facebook' || $socialsite=='twitter')
          continue;
        $socialsiteloggedin = $this->_getUserLogin($socialsite);
        if( isset($socialsiteloggedin) && $form->getElement($socialsite.'_id') ) {
            if( empty($values[$socialsite.'_id']) ) {
              // Remove integration
              $socialsiteTable = Engine_Api::_()->getDbtable($socialsite, 'sitelogin');
              $socialsiteTable->delete(array(
                'user_id = ?' => $user->getIdentity(),
              ));
              unset($removeElements[$socialsite]);
              $removeElements[$socialsite.'_id'] = $socialsite.'_id';
            }
        }
    }

    // Update janrain settings
    if( !empty($values['janrainnoshare']) ) {
      $userSettings->setSetting($user, 'janrain.no-share', true);
    } else {
      $userSettings->setSetting($user, 'janrain.no-share', null);
    }

    foreach($removeElements as $elementName) {
      $form->removeElement($elementName);
    }
    // Send success message
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Settings saved.');
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
  }

  protected function _getUserLogin($type) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (!$viewer_id)
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    if (!empty($type) ) {
            $siteTable = Engine_Api::_()->getDbtable($type, 'sitelogin');
            $user_id = $siteTable->select()
                    ->from($siteTable, 'user_id')
                    ->where("user_id = ?", $viewer_id)
                    ->query()
                    ->fetchColumn();
            return $user_id;
        }
        return false;
  }
  
}