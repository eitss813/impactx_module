<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Invites.php 10180 2014-04-28 21:02:01Z lucas $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Invite_Model_DbTable_Invites extends Engine_Db_Table
{
  protected $_name = 'invites';

  public function sendInvites(User_Model_User $user, $recipients, $message, $friendship)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    // Check recipients
    if( is_string($recipients) ) {
      $recipients = preg_split("/[\s,]+/", $recipients);
    }
    if( is_array($recipients) ) {
      $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
    }
    if( !is_array($recipients) || empty($recipients) ) {
      return 0;
    }
    
    // Only allow a certain number for now
    $max = $settings->getSetting('invite.max', 10);
    if( count($recipients) > $max ) {
      $recipients = array_slice($recipients, 0, $max);
    }

    // Check message
    $message = trim($message);
    
    // Get tables
    $userTable = Engine_Api::_()->getItemTable('user');
    $inviteTable = $this;
    $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);

    // Get ones that are already members
    $alreadyMembers = $userTable->fetchAll(array('email IN(?)' => $recipients));
    $alreadyMemberEmails = array();
    foreach( $alreadyMembers as $alreadyMember ) {
      if( in_array(strtolower($alreadyMember->email), $recipients) ) {
        $alreadyMemberEmails[] = strtolower($alreadyMember->email);
      }
    }

    // Remove the ones that are already members
    $recipients = array_diff($recipients, $alreadyMemberEmails);
    $emailsSent = 0;

    // Send them invites
    foreach( $recipients as $recipient ) {
      // start inserting database entry
      // generate unique invite code and confirm it truly is unique
      do {
        $inviteCode = substr(md5(rand(0, 999) . $recipient), 10, 7);
      } while( null !== $inviteTable->fetchRow(array('code = ?' => $inviteCode)) );

      $row = $inviteTable->createRow();
      $row->user_id = $user->getIdentity();
      $row->recipient = $recipient;
      $row->send_request = $friendship;
      $row->code = $inviteCode;
      $row->timestamp = new Zend_Db_Expr('NOW()');
      $row->message = $message;
      $row->save();
      
      try {
        
        $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'module' => 'invite',
              'controller' => 'signup',
            ), 'default', true)
          . '?'
          . http_build_query(array('code' => $inviteCode, 'email' => $recipient))
          ;

        $message = str_replace('%invite_url%', $inviteUrl, $message);
        
        // Send mail
        $mailType = ( $inviteOnlySetting == 2 ? 'invite_code' : 'invite' );
        $mailParams = array(
          'host' => $_SERVER['HTTP_HOST'],
          'email' => $recipient,
          'date' => time(),
          'sender_email' => $user->email,
          'sender_title' => $user->getTitle(),
          'sender_link' => $user->getHref(),
          'sender_photo' => $user->getPhotoUrl('thumb.icon'),
          'message' => $message,
          'object_link' => $inviteUrl,
          'code' => $inviteCode,
        );

        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $recipient,
          $mailType,
          $mailParams
        );
        
      } catch( Exception $e ) {
        // Silence
        if( APPLICATION_ENV == 'development' ) {
          throw $e;
        }
        continue;
      }
      
      $emailsSent++;
    }

    $user->invites_used += $emailsSent;
    $user->save();

    // @todo Send requests to users that are already members?

    return $emailsSent;
  }

  public function isInvited($subject){
    $recipient = $subject['email'];
    $select = $this->select()->from($this->info('name'), 'user_id');
    $select->where('recipient = ?', $recipient);
    return $this->fetchRow($select);
  }

    // todo: 5.2.1 Upgrade => Added missing functions which was present earlier
    public function sendCustomInvites(User_Model_User $user, $recipients, $message, $friendship, $project_id, $recipient_name, $roles_id)
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Check recipients
        if (is_string($recipients)) {
            $recipients = preg_split("/[\s,]+/", $recipients);
        }
        if (is_array($recipients)) {
            $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }
        if (!is_array($recipients) || empty($recipients)) {
            return 0;
        }

        // Only allow a certain number for now
        $max = $settings->getSetting('invite.max', 10);
        if (count($recipients) > $max) {
            $recipients = array_slice($recipients, 0, $max);
        }

        // Check message
        $message = trim($message);

        // Get tables
        $userTable = Engine_Api::_()->getItemTable('user');
        $inviteTable = $this;
        $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);

        // Get ones that are already members
        $alreadyMembers = $userTable->fetchAll(array('email IN(?)' => $recipients));
        $alreadyMemberEmails = array();
        foreach ($alreadyMembers as $alreadyMember) {
            if (in_array(strtolower($alreadyMember->email), $recipients)) {
                $alreadyMemberEmails[] = strtolower($alreadyMember->email);
            }
        }

        if (!empty($alreadyMemberEmails)) {
            foreach ($alreadyMemberEmails as $singleItem) {
                if (!empty($project_id)) {
                    $userTable = Engine_Api::_()->getDbtable('users', 'user');
                    $userTableName = $userTable->info('name');
                    $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $singleItem)->query()->fetchColumn();
                    $hasMembers = Engine_Api::_()->getDbTable('memberships', 'sitecrowdfunding')->hasMembers($user_id, $project_id);
                    if (empty($hasMembers)) {
                        $membersTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
                        $row = $membersTable->createRow();
                        $row->resource_id = $project_id;
                        $row->project_id = $project_id;
                        $row->user_id = $user_id;
                        $row->resource_approved = 1;
                        $row->active = 1;
                        $row->user_approved = 1;

                        //FOR CATEGORY WORK.
                        if (isset($roles_id)) {
                            $roleName = array();
                            foreach ($roles_id as $role_id) {
                                $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->getRoleName($role_id);
                            }
                            $roleTitle = json_encode($roleName);
                            $roleIDs = json_encode($roles_id);
                            if ($roleTitle && $roleIDs) {
                                $row->title = $roleTitle;
                                $row->role_id = $roleIDs;
                            }
                        }

                        $row->save();
                    }
                }
            }
        }

        // Remove the ones that are already members
        // $recipients = array_diff($recipients, $alreadyMemberEmails);
        $emailsSent = 0;

        // Send them invites
        foreach ($recipients as $recipient) {

            // start inserting database entry
            // generate unique invite code and confirm it truly is unique
            do {
                $inviteCode = substr(md5(rand(0, 999) . $recipient), 10, 7);
            } while (null !== $inviteTable->fetchRow(array('code = ?' => $inviteCode)));

            $row = $inviteTable->createRow();
            $row->user_id = $user->getIdentity();
            $row->recipient = $recipient;
            $row->send_request = $friendship;
            $row->code = $inviteCode;
            $row->project_id = $project_id;
            $row->recipient_name = $recipient_name;
            $row->timestamp = new Zend_Db_Expr('NOW()');
            $row->message = $message;
            $row->project_role = json_encode($roles_id);
            $row->save();

            try {

                $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                        'module' => 'invite',
                        'controller' => 'signup',
                    ), 'default', true)
                    . '?'
                    . http_build_query(array('code' => $inviteCode, 'email' => $recipient, 'project_id' => $project_id));

                $message = str_replace('%invite_url%', $inviteUrl, $message);

                // Send mail
                $mailType = ($inviteOnlySetting == 2 ? 'invite_code' : 'invite');
                $mailParams = array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'email' => $recipient,
                    'date' => time(),
                    'sender_email' => $user->email,
                    'sender_title' => $user->getTitle(),
                    'sender_link' => $user->getHref(),
                    'sender_photo' => $user->getPhotoUrl('thumb.icon'),
                    'message' => $message,
                    'object_link' => $inviteUrl,
                    'code' => $inviteCode,
                );

                Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                    $recipient,
                    $mailType,
                    $mailParams
                );

            } catch (Exception $e) {
                // Silence
                if (APPLICATION_ENV == 'development') {
                    throw $e;
                }
                continue;
            }

            $emailsSent++;

        }

        $user->invites_used += $emailsSent;
        $user->save();

        // @todo Send requests to users that are already members?

        return $emailsSent;
    }

    // todo: 5.2.1 Upgrade => Added missing functions which was present earlier
    public function getCustomPendingInvites($project_id)
    {

        $select = $this->select()
            ->where('project_id = ?', $project_id)
            ->where('new_user_id = 0');

        return $select->query()->fetchAll();

    }

    // todo: 5.2.1 Upgrade => Added missing functions which was present earlier
    public function getCustomORGPendingInvites($page_id)
    {

        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('new_user_id = 0');

        return $select->query()->fetchAll();

    }

    // todo: 5.2.1 Upgrade => Added missing functions which was present earlier
    public function sendCustomORGInvites(User_Model_User $user, $recipients, $message, $friendship, $page_id, $recipient_name, $roles_id)
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Check recipients
        if (is_string($recipients)) {
            $recipients = preg_split("/[\s,]+/", $recipients);
        }
        if (is_array($recipients)) {
            $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }
        if (!is_array($recipients) || empty($recipients)) {
            return 0;
        }

        // Only allow a certain number for now
        $max = $settings->getSetting('invite.max', 10);
        if (count($recipients) > $max) {
            $recipients = array_slice($recipients, 0, $max);
        }

        // Check message
        $message = trim($message);

        // Get tables
        $userTable = Engine_Api::_()->getItemTable('user');
        $inviteTable = $this;
        $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);

        // Get ones that are already members
        $alreadyMembers = $userTable->fetchAll(array('email IN(?)' => $recipients));
        $alreadyMemberEmails = array();
        foreach ($alreadyMembers as $alreadyMember) {
            if (in_array(strtolower($alreadyMember->email), $recipients)) {
                $alreadyMemberEmails[] = strtolower($alreadyMember->email);
            }
        }

        if (!empty($alreadyMemberEmails)) {
            foreach ($alreadyMemberEmails as $singleItem) {
                if (!empty($page_id)) {
                    $userTable = Engine_Api::_()->getDbtable('users', 'user');
                    $userTableName = $userTable->info('name');
                    $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $singleItem)->query()->fetchColumn();
                    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($user_id, $page_id);
                    if (empty($hasMembers)) {
                        $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                        $row = $membersTable->createRow();
                        $row->resource_id = $page_id;
                        $row->page_id = $page_id;
                        $row->user_id = $user_id;
                        $row->resource_approved = 1;
                        $row->active = 1;
                        $row->user_approved = 1;
                        if (isset($roles_id)) {
                            $roleName = array();
                            foreach ($roles_id as $role_id) {
                                $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRoleName($role_id);
                            }
                            $roleTitle = json_encode($roleName);
                            $roleIDs = json_encode($roles_id);
                            if ($roleTitle && $roleIDs) {
                                $row->title = $roleTitle;
                                $row->role_id = $roleIDs;
                            }
                        }
                        $row->save();
                    }
                }
            }
        }

        // Remove the ones that are already members
        $recipients = array_diff($recipients, $alreadyMemberEmails);
        $emailsSent = 0;

        // Send them invites
        foreach ($recipients as $recipient) {

            // start inserting database entry
            // generate unique invite code and confirm it truly is unique
            do {
                $inviteCode = substr(md5(rand(0, 999) . $recipient), 10, 7);
            } while (null !== $inviteTable->fetchRow(array('code = ?' => $inviteCode)));

            $row = $inviteTable->createRow();
            $row->user_id = $user->getIdentity();
            $row->recipient = $recipient;
            $row->send_request = $friendship;
            $row->code = $inviteCode;
            $row->page_id = $page_id;
            $row->recipient_name = $recipient_name;
            $row->timestamp = new Zend_Db_Expr('NOW()');
            $row->message = $message;
            $row->page_role = json_encode($roles_id);
            $row->save();

            try {

                $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                        'module' => 'invite',
                        'controller' => 'signup',
                    ), 'default', true)
                    . '?'
                    . http_build_query(array('code' => $inviteCode, 'email' => $recipient, 'page_id' => $page_id));

                $message = str_replace('%invite_url%', $inviteUrl, $message);

                // Send mail
                $mailType = ($inviteOnlySetting == 2 ? 'invite_code' : 'invite');
                $mailParams = array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'email' => $recipient,
                    'date' => time(),
                    'sender_email' => $user->email,
                    'sender_title' => $user->getTitle(),
                    'sender_link' => $user->getHref(),
                    'sender_photo' => $user->getPhotoUrl('thumb.icon'),
                    'message' => $message,
                    'object_link' => $inviteUrl,
                    'code' => $inviteCode,
                );

                Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                    $recipient,
                    $mailType,
                    $mailParams
                );

            } catch (Exception $e) {
                // Silence
                if (APPLICATION_ENV == 'development') {
                    throw $e;
                }
                continue;
            }

            $emailsSent++;

        }

        $user->invites_used += $emailsSent;
        $user->save();

        // @todo Send requests to users that are already members?

        return $emailsSent;

    }
}
