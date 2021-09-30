<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Subscriptions.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_DbTable_Subscriptions extends Engine_Db_Table {

  public function sendNotifications(Sesblog_Model_Blog $sesblog) {
  
    if( !empty($sesblog->draft) || $sesblog->owner_type != 'user' ) {
      return $this;
    }

    // Get sesblog owner
    $owner = $sesblog->getOwner('user');

    // Get notification table
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');

    // Get all subscribers
    $identities = $this->select()
      ->from($this, 'subscriber_user_id')
      ->where('user_id = ?', $sesblog->owner_id)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    if( empty($identities) || count($identities) <= 0 ) {
      return $this;
    }

    $users = Engine_Api::_()->getItemMulti('user', $identities);

    if( empty($users) || count($users) <= 0 ) {
      return $this;
    }

    // Send notifications
    foreach( $users as $user ) {
      $notificationTable->addNotification($user, $owner, $sesblog, 'sesblog_subscribed_new');
    }

    return $this;
  }

  public function checkSubscription(User_Model_User $user, User_Model_User $subscriber) {
  
    return (bool) $this->select()
        ->from($this, new Zend_Db_Expr('TRUE'))
        ->where('user_id = ?', $user->getIdentity())
        ->where('subscriber_user_id = ?', $subscriber->getIdentity())
        ->query()
        ->fetchColumn();
  }

  public function createSubscription(User_Model_User $user, User_Model_User $subscriber) {
  
    // Ignore if already subscribed
    if( $this->checkSubscription($user, $subscriber) ) {
      return $this;
    }

    // Create
    $this->insert(array(
      'user_id' => $user->getIdentity(),
      'subscriber_user_id' => $subscriber->getIdentity(),
    ));

    return $this;
  }

  public function removeSubscription(User_Model_User $user, User_Model_User $subscriber) {
  
    // Ignore if already not subscribed
    if( !$this->checkSubscription($user, $subscriber) ) {
      return $this;
    }

    // Delete
    $this->delete(array(
      'user_id = ?' => $user->getIdentity(),
      'subscriber_user_id = ?' => $subscriber->getIdentity(),
    ));

    return $this;
  }
}
