<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_settings');
    $this->view->form = $form = new Siteotpverifier_Form_Admin_Global();
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    if( !empty($values['allowCountry']) ) {
      if( !in_array($values['defaultCountry'], $values['allowCountry']) ) {
        $form->addError("Country selected as default should be from one of the allowed countries as selected in ‘Allow Countries’ setting.");
        return;
      }
      if( Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_allowCountry )
        Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteotpverifier_allowCountry');
    } else {
      unset($values['allowCountry']);
    }
    Engine_Api::_()->getApi('settings', 'core')->siteotpverifier = $values;
    $form->addNotice('Your changes have been saved.');
    $form->populate($values);
  }
  public function amazonAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_integration');
    $this->view->form = $form = new Siteotpverifier_Form_Admin_Amazon();
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    if( empty($values['clientId']) || empty($values['clientSecret']) ) {
      $values['clientId'] = '';
      $values['clientSecret'] = '';
    }
    if( Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_amazon )
      Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteotpverifier_amazon');
    Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_amazon = $values;
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    if( !empty($values['enable']) && Engine_Api::_()->siteotpverifier()->amazonIntegrationEnabled() ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('siteotpverifier.integration', 'amazon');
    } else if( $service == 'amazon' ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('siteotpverifier.integration', null);
    }
    $form->addNotice('Your changes have been saved.');
    $form->populate($values);
  }
  public function twilioAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_integration');
    $this->view->form = $form = new Siteotpverifier_Form_Admin_Twilio();
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    if( empty($values['clientId']) || empty($values['clientSecret']) ) {
      $values['clientId'] = '';
      $values['clientSecret'] = '';
    }
    if( Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_twilio )
      Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteotpverifier_twilio');
    Engine_Api::_()->getApi('settings', 'core')->siteotpverifier_twilio = $values;
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    if( !empty($values['enable']) && Engine_Api::_()->siteotpverifier()->twilioIntegrationEnabled() ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('siteotpverifier.integration', 'twilio');
    } else if( $service == 'twilio' ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('siteotpverifier.integration', null);
    }
    $form->addNotice('Your changes have been saved.');
    $form->populate($values);
  }
  public function contactinfoAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_contact');
    $this->view->formFilter = $formFilter = new Siteotpverifier_Form_Admin_Filter();
    $page = $this->_getParam('page', 1);
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $otpTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
    $tableName = $table->info('name');
    $otpTableName = $otpTable->info('name');
    $select = $table->select();
    $select->setIntegrityCheck(false)
            ->from($table->info('name'))
            ->joinLeft($otpTableName, "$tableName.user_id = $otpTableName.user_id");
    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
      'order' => 'user_id',
      'order_direction' => 'DESC',
      ), $values);
    $this->view->assign($values);
    $order = !empty($values['order']) ? $values['order'] : 'user_id' ;
    $order = in_array($order, array('phoneno', 'country_code')) ? $otpTableName . '.' . $order : $tableName . '.' . $order;
    // Set up select info
    $select->order($order . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    if( !empty($values['displayname']) ) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if( !empty($values['username']) ) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if( !empty($values['email']) ) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    if( !empty($values['phoneno']) ) {
      $select->where("$otpTableName.phoneno LIKE ?", '%' . $values['phoneno'] . '%');
    }
    //print_r($values['country_code']);
    if( isset($values['country_code']) && $values['country_code'] != 0 ) {
      $select->where("$otpTableName.country_code = ?", $values['country_code']);
    }
    if( !empty($values['user_id']) ) {
      $select->where("$tableName.user_id = ?", (int) $values['user_id']);
    }
    $select->where($otpTableName . '.phoneno != "" AND ' . $otpTableName .'.phoneno != 0 AND ' . $otpTableName . '.phoneno is not null');
    // Filter out junk
    $valuesCopy = array_filter($values);
    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $this->view->formValues = $valuesCopy;
    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    //$this->view->formDelete = new User_Form_Admin_Manage_Delete();
    $this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
  }
  public function editAction()
  {
    $id = $this->_getParam('id', null);
    $user = Engine_Api::_()->getItem('user', $id);
    $userLevel = Engine_Api::_()->getItem('authorization_level', $user->level_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewerLevel = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
    $superAdminLevels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll(array(
      'flag = ?' => 'superadmin',
    ));
    if( !$user || !$userLevel || !$viewer || !$viewerLevel ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
    $this->view->user = $user;
    $this->view->form = $form = new Siteotpverifier_Form_Admin_Edit(array(
      'userIdentity' => $id,
    ));
    // Do not allow editing level if the last superadmin
    if( $userLevel->flag == 'superadmin' && count(Engine_Api::_()->user()->getSuperAdmins()) == 1 ) {
      $form->removeElement('level_id');
    }
    // Do not allow admins to change to super admin
    if( $viewerLevel->flag != 'superadmin' && $form->getElement('level_id') ) {
      if( $userLevel->flag == 'superadmin' ) {
        $form->removeElement('level_id');
      } else {
        foreach( $superAdminLevels as $superAdminLevel ) {
          unset($form->getElement('level_id')->options[$superAdminLevel->level_id]);
        }
      }
    }
    // Get values
    $otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
    $values = array_merge($user->toArray(), $otpUser->toArray());
    unset($values['password']);
    if( _ENGINE_ADMIN_NEUTER ) {
      unset($values['email']);
    }
    // Get networks
    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($user);
    $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
    $values['network_id'] = $oldNetworks = array();
    foreach( $networks as $network ) {
      $values['network_id'][] = $oldNetworks[] = $network->getIdentity();
    }
    // Check if user can be enabled?
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    if( !$subscriptionsTable->check($user) && !$values['enabled'] ) {
      $form->enabled->setAttrib('disable', array('enabled'));
      $note = '<p>Note: You cannot enable a member using this form if he / she has not '
        . 'yet chosen a subscription plan for their account. You can just approve them '
        . 'here after which they\'ll be able to choose a subscription plan before trying '
        . 'to login on your site.</p>';
    } elseif( 2 === (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.verifyemail', 0) ) {
      $note = '<p>Note - Member can only be enabled when they are both approved and verified.</p>';
    } else {
      $note = '<p>Note - Member can only be enabled after they have been approved.</p>';
    }
    $form->addElement('note', 'desc', array(
      'value' => $note,
      'order' => 9
    ));
    // Populate form
    $form->populate($values);
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    // Check password validity
    if( empty($values['password']) && empty($values['password_conf']) ) {
      unset($values['password']);
      unset($values['password_conf']);
    } else if( $values['password'] != $values['password_conf'] ) {
      return $form->getElement('password')->addError('Passwords do not match.');
    } else {
      unset($values['password_conf']);
    }
    // Process
    $oldValues = $user->toArray();
    // Set new network
    $userNetworks = $values['network_id'];
    unset($values['network_id']);
    if( $userNetworks == NULL ) {
      $userNetworks = array();
    }
    $joinIds = array_diff($userNetworks, $oldNetworks);
    foreach( $joinIds as $id ) {
      $network = Engine_Api::_()->getItem('network', $id);
      $network->membership()->addMember($user)
        ->setUserApproved($user)
        ->setResourceApproved($user);
    }
    $leaveIds = array_diff($oldNetworks, $userNetworks);
    foreach( $leaveIds as $id ) {
      $network = Engine_Api::_()->getItem('network', $id);
      if( !is_null($network) ) {
        $network->membership()->removeMember($user);
      }
    }
    // Check for null usernames
    if( $values['username'] == '' ) {
      // If value is "NULL", then set to zend Null
      $values['username'] = new Zend_Db_Expr("NULL");
    }
    $user->setFromArray($values);
    $user->save();
    
    $otpUser->setFromArray($values);
    $otpUser->save();
    if( !$oldValues['enabled'] && $values['enabled'] ) {
      // trigger `onUserEnable` hook
      $payload = array(
        'user' => $user,
        'shouldSendWelcomeEmail' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enablewelcomeemail', 0),
        'shouldSendApprovedEmail' => true
      );
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserEnable', $payload);
    } else if( $oldValues['enabled'] && !$values['enabled'] ) {
      // trigger `onUserDisable` hook
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserDisable', $user);
    }
    // Forward
    return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format' => 'smoothbox',
        'messages' => array('Your changes have been saved.')
    ));
  }
  public function languageEditorAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_language');
    $this->view->form = $form = new Siteotpverifier_Form_Admin_Language();
    $translate = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    if( !in_array($defaultLanguage, $languageList) ) {
      if( $defaultLanguage == 'auto' && isset($languageList['en']) ) {
        $defaultLanguage = 'en';
      } else {
        $defaultLanguage = null;
      }
    }
    // Get level id
    if( null == ($lang = $this->_getParam('lang')) ) {
      $lang = $defaultLanguage;
    }
    $messageTable = Engine_Api::_()->getDbtable('messages', 'siteotpverifier');
    $select = $messageTable->select()->where('language=?', $lang);
    $param = $messageTable->fetchRow($select);
    if( empty($param) ) {
      $select = $messageTable->select()->where('language=?', 'en');
      $param = $messageTable->fetchRow($select);
    }
    $param = $param ? $param->toArray() : array();
    $form->language->setValue($lang);
    $form->populate($param);
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    foreach( $values as $key => $value ) {
      if( $key == "language" )
        continue;
      if( !empty($value) && strpos($value, '[code]') == false ) {
        $form->addError("Message should contain [code] keyword.");
        return;
      }
    }
    $db = $messageTable->getAdapter();
    $db->beginTransaction();
    try {
      $messageTable->delete(array(
        'language = ?' => $lang,
      ));
      $messageTable->insert($values);
      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }
  public function sendmessageAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_message');
    $values = array();
    $this->view->formFilter = $formFilter = new Siteotpverifier_Form_Admin_Messagefilter();
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    if( count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type' ) {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getElementParams('user');
      $this->view->profile_type = $options['options']['multiOptions'];
    }
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }
    $now = date('Y-m-d h:m:s');
    $now = strtotime($now);
    $raw = date('Y-m-d', $now);
    $page = $this->_getParam('page', 1);
    $bonusTable = Engine_Api::_()->getDbtable('sentmessages', 'siteotpverifier');
    $bonusTableName = $bonusTable->info('name');
    $select = $bonusTable->select()->setIntegrityCheck(false);
    if( !empty($_POST['show_time']) ) {
      $show_time = $_POST['show_time'];
    } elseif( !empty($_GET['show_time']) && !isset($_POST['post_search']) ) {
      $show_time = $_GET['show_time'];
    } else {
      $show_time = '';
    }
    if( !empty($_POST['username']) ) {
      $username = $_POST['username'];
    } elseif( !empty($_GET['username']) && !isset($_POST['post_search']) ) {
      $username = $_GET['username'];
    } else {
      $username = '';
    }
    if( !empty($_POST['user_id']) ) {
      $user_id = $_POST['user_id'];
    } elseif( !empty($_GET['user_id']) && !isset($_POST['post_search']) ) {
      $user_id = $_GET['user_id'];
    } else {
      $user_id = '';
    }
    if( !empty($_POST['starttime']) && !empty($_POST['starttime']['date']) ) {
      $creation_date_start = $_POST['starttime']['date'];
    } elseif( !empty($_GET['starttime']['date']) && !isset($_POST['post_search']) ) {
      $creation_date_start = $_GET['starttime']['date'];
    } else {
      $creation_date_start = '';
    }
    if( !empty($_POST['endtime']) && !empty($_POST['endtime']['date']) ) {
      $creation_date_end = $_POST['endtime']['date'];
    } elseif( !empty($_GET['endtime']['date']) && !isset($_POST['post_search']) ) {
      $creation_date_end = $_GET['endtime']['date'];
    } else {
      $creation_date_end = '';
    }
    if( !empty($_POST['from']) ) {
      $creation_date_start = $_POST['from'];
    } elseif( !empty($_GET['from']) && !isset($_POST['post_search']) ) {
      $creation_date_start = $_GET['from'];
    }
    if( !empty($_POST['to']) ) {
      $creation_date_end = $_POST['to'];
    } elseif( !empty($_GET['to']) && !isset($_POST['post_search']) ) {
      $creation_date_end = $_GET['to'];
    }
    if( isset($_POST['message']) && $_POST['message'] != '' ) {
      $message = $_POST['message'];
    } elseif( !empty($_GET['message']) && !isset($_POST['post_search']) ) {
      $message = $_GET['message'];
    } else {
      $message = '';
    }
    if( isset($_POST['basedon']) && $_POST['basedon'] != '' ) {
      $basedon = $_POST['basedon'];
    } elseif( !empty($_GET['basedon']) && !isset($_POST['post_search']) ) {
      $basedon = $_GET['basedon'];
    } else {
      $basedon = '';
    }
    if( isset($_POST['profiletype']) && $_POST['profiletype'] != '' ) {
      $profiletype = $_POST['profiletype'];
    } elseif( !empty($_GET['profiletype']) && !isset($_POST['post_search']) ) {
      $profiletype = $_GET['profiletype'];
    } else {
      $profiletype = '';
    }
    if( isset($_POST['member_level']) && $_POST['member_level'] != '' ) {
      $member_level = $_POST['member_level'];
    } elseif( !empty($_GET['member_level']) && !isset($_POST['post_search']) ) {
      $member_level = $_GET['member_level'];
    } else {
      $member_level = '';
    }
    // searching
    $this->view->show_time = $values['show_time'] = $show_time;
    $this->view->username = $values['username'] = $username;
    $this->view->starttime = $values['from'] = $creation_date_start;
    $this->view->endtime = $values['to'] = $creation_date_end;
    $this->view->message = $values['message'] = $message;
    $this->view->basedon = $values['basedon'] = $basedon;
    $this->view->profiletype = $values['profiletype'] = $profiletype;
    $this->view->member_level = $values['member_level'] = $member_level;
    if( !empty($username) ) {
      $userTable = Engine_Api::_()->getDbTable('users', 'user');
      $userTableName = $userTable->info('name');
      $select->from($bonusTableName)->join($userTableName, $userTableName . '.user_id = ' . $bonusTableName . '.user_id', array("$userTableName.displayname"));
      $select->where($userTableName . '.displayname  LIKE ?', '%' . trim($username) . '%');
    }
    if( !empty($show_time) ) {
      switch( $show_time ) {
        case 'day': $select->where("CAST($bonusTableName.creation_date AS DATE)=?", $raw);
          break;
        case 'weekly': $select->where("$bonusTableName.creation_date >= DATE(NOW()) - INTERVAL 7 DAY");
          break;
        case 'range': if( !empty($creation_date_start) ) {
            $select->where("CAST($bonusTableName.creation_date AS DATE) >=?", trim($creation_date_start));
          }
          if( !empty($creation_date_end) ) {
            $select->where("CAST($bonusTableName.creation_date AS DATE) <=?", trim($creation_date_end));
          } break;
      }
    }
    if( $message != '' ) {
      $select->where("$bonusTableName.message LIKE ?", '%' . trim($message) . '%');
    }
    if( !empty($user_id) ) {
      $select->where("$bonusTableName.user_id = ? ", $user_id);
    }
    if( $basedon == "profile" ) {
      $select->where("$bonusTableName.type = ? ", 0);
      if( !empty($profiletype) )
        $select->where("$bonusTableName.profile_type = ? ", $profiletype);
    }
    if( $basedon == "memberlevel" ) {
      $select->where("$bonusTableName.type = ? ", 1);
      if( !empty($member_level) )
        $select->where("$bonusTableName.member_level = ? ", $member_level);
    }
    $values = array_merge(array(
      'order' => 'sentmessage_id',
      'order_direction' => 'DESC',
      ), $values);
    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'sentmessage_id') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    $this->view->formValues = array_filter($values);
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPagenumber($page);
  }
  public function messageSendAction()
  {
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    if( count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type' ) {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $field_value = $options[0]['field_id'];
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer )
      return;
    $this->view->form = $form = new Siteotpverifier_Form_Admin_Send();
    //check post request
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {
      $value = $form->getValues();
      $values = array_merge($value, array(
        'owner_id' => $viewer->getIdentity(),
      ));
      if( $values['member'] == 1 ) {
        $values['user_id'] = 0;
      }
      $values['creation_date'] = new Zend_Db_Expr('NOW()');
      // Create blog
      try {
        if( !($values['type'] == 0) ) {
          if( !($values['user_id'] == 0) ) {
            $user = Engine_Api::_()->user()->getUser($values['user_id']);
            $values['member_level'] = $user->level_id;
            $values['profile_type'] = 0;
            Engine_Api::_()->getApi('core', 'siteotpverifier')->sendmessage($user, $values['message']);
          } else {
            $tableName = Engine_Api::_()->getDbTable('users', 'user');
            $select = $tableName->select();
            if( !empty($values['member_level']) ) {
              $select->where("level_id=?", $values['member_level']);
            }
            $userObjects = $tableName->fetchAll($select);
            foreach( $userObjects as $users ) {
              Engine_Api::_()->getApi('core', 'siteotpverifier')->sendmessage($users, $values['message']);
            }
          }
        } else {
          $values['user_id'] = 0;
          //for profile type
          if( empty($values['profile_type']) ) {
            $values['profile_type'] = 0;
            $tableName = Engine_Api::_()->getDbTable('users', 'user');
            $select = $tableName->select();
            $userObjects = $tableName->fetchAll($select);
            foreach( $userObjects as $users ) {
              Engine_Api::_()->getApi('core', 'siteotpverifier')->sendmessage($users, $values['message']);
            }
          } else {
            //fetch user of a specific profile id.
            $db = Engine_Db_Table::getDefaultAdapter();
            $map = $db->select()
              ->from('engine4_user_fields_values')
              ->where('value = ?', $values['profile_type'])
              ->where('field_id = ?', $field_value)
              ->query()
              ->fetchAll();
            if( !empty($map) ) {
              foreach( $map as $item ) {
                $user = Engine_Api::_()->getItem('user', $item->item_id);
                Engine_Api::_()->getApi('core', 'siteotpverifier')->sendmessage($user, $values['message']);
              }
            }
          }
        }
        $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
        if( $service == "amazon" ) {
          $integration = Engine_Api::_()->getApi('core', 'siteotpverifier')->amazonIntegrationEnabled();
          if( $integration )
            $serviceintegrated = "amazon";
        } elseif( $service == "twilio" ) {
          $integration = Engine_Api::_()->getApi('core', 'siteotpverifier')->twilioIntegrationEnabled();
          if( $integration )
            $serviceintegrated = "twilio";
        }
        if( !empty($serviceintegrated) ) {
          $values['service'] = $service;
          $row = Engine_Api::_()->getDbtable('sentmessages', 'siteotpverifier')->createRow();
          $row->setFromArray($values);
          $row->save();
          $form->addNotice('Message sent.');
        } else {
          $form->addError('Please check your service integration settings once.');
          return;
        }
      } catch( Exception $e ) {
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
    }
  }
  public function getallusersAction()
  {
    // for suggestions of users
    $text = $this->_getParam('search');
    $user_ids = $this->_getParam('user_ids', null);
    $levelid = $this->_getParam('level_id', null);
    $limit = $this->_getParam('limit', 40);
    $tableName = Engine_Api::_()->getDbTable('users', 'user');
    try {
      $select = $tableName->select()
        ->where('displayname  LIKE ? ', '%' . $text . '%');
      if( !empty($levelid) ) {
        $select->where("level_id=?", $levelid);
      }
      if( !empty($user_ids) ) {
        $select->where("user_id NOT IN ($user_ids)");
      }
      $select->order('displayname ASC')
        ->limit($limit);
      $userObjects = $tableName->fetchAll($select);
      $data = array();
      //FETCH RESULTS
      foreach( $userObjects as $users ) {
        $data[] = array(
          'id' => $users->user_id,
          'label' => $users->getTitle(),
          'photo' => $this->view->itemPhoto($users, 'thumb.icon'),
        );
      }
    } catch( Exception $e ) {
      throw $e;
    }
    return $this->_helper->json($data);
  }
  public function faqAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_faq');
    $this->view->faq = $this->_getParam('faq');
  }
  public function readmeAction()
  {
    
  }
}