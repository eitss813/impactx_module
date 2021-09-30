<?php

class Sesnewsletter_AdminMessageController extends Core_Controller_Action_Admin
{
  public function mailAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_message_mail');

    $this->view->form = $form = new Sesnewsletter_Form_Admin_Message_Mail();

    // let the level_ids be specified in GET string
    $level_ids = $this->_getParam('level_id', false);
    if (is_array($level_ids)) {
      $form->target->setValue($level_ids);
    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {

      if ($_POST['choose_member'] == '1' && empty($_POST['member_name'])) {
        $form->addError("Member Name * Please complete this field - it is required.");
      }
      if ($_POST['choose_member'] == '5' && empty($_POST['external_emails'])) {
        $form->addError("Emails * Please complete this field - it is required.");
      }
      return;
    }

    $values = $form->getValues();

    $table = Engine_Api::_()->getItemTable('user');
    $tableName = $table->info('name');

    //Specific Member
    if($values['choose_member'] == '1') {
        $user = Engine_Api::_()->getItem('user', $values['user_id']);
        $emails[] = $user->email;
    }
    //Members Without Photo
    else if($values['choose_member'] == '2') {

        $select = $table->select()
            ->from($table->info('name'), 'email')
            ->where('photo_id = ?', 0); // Do not email disabled members
        $results = $table->fetchAll($select);
        $emails = array();
        foreach( $results as $result ) {
            $emails[] = $result->email;
        }
    }
    //Members Having Birthday Today
    else if($values['choose_member'] == '3') {
        $results = Engine_Api::_()->sesnewsletter()->todayBirthdayMembers();
        $emails = array();
        foreach( $results as $result ) {
            $emails[] = $result->email;
        }

    }
    //Other Members
    else if($values['choose_member'] == '4') {

        $select = new Zend_Db_Select($table->getAdapter());
        $select
            ->from($table->info('name'), 'email')
            ->where('enabled = ?', true); // Do not email disabled members

        $level_ids = $this->_getParam('member_levels');
        if (is_array($level_ids) && !empty($level_ids)) {
            $select->where('level_id IN (?)', $level_ids);
        }


        if(!empty($values['networks']) && count($values['networks']) > 0) {

            $resultsN = Engine_Api::_()->sesnewsletter()->networksJoinedMembers($values['networks']);
            $networkUserIds = array();
            if(count($resultsN) > 0) {
                foreach($resultsN as $resultN) {
                    $networkUserIds[] = $resultN->user_id;
                }
                $select->where('user_id IN (?)', $networkUserIds);
            }
        }

        if(!empty($values['profile_types']) && count($values['profile_types']) > 0) {
            $resultsP = Engine_Api::_()->sesnewsletter()->profileTypesMembers($values['profile_types']);
            $profiletypeUserIds = array();
            if(count($resultsP) > 0) {
                foreach($resultsP as $resultP) {
                    $profiletypeUserIds[] = $resultP->item_id;
                }
                $select->where('user_id IN (?)', $profiletypeUserIds);
            }
        }

        $emails = array();
        foreach( $select->query()->fetchAll(Zend_Db::FETCH_COLUMN, 0) as $email ) {
            $emails[] = $email;
        }

    }
    //External Emails
    else if($values['choose_member'] == '5') {
        $emails = explode(',', $values['external_emails']);
    }


    $emailTable = Engine_Api::_()->getDbTable('emails', 'sesnewsletter');
    $db = $emailTable->getAdapter();
    $db->beginTransaction();
    try {
        foreach($emails as $email) {
            $row = $emailTable->createRow();
            $values['email'] = $email;
            $values['stop'] = '1';
            $row->setFromArray($values);
            $row->save();
        }
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

//     // temporarily enable queueing if requested
//     $temporary_queueing = Engine_Api::_()->getApi('settings', 'core')->core_mail_queueing;
//     if (isset($values['queueing']) && $values['queueing']) {
//       Engine_Api::_()->getApi('settings', 'core')->core_mail_queueing = 1;
//     }
//
//     $mailApi = Engine_Api::_()->getApi('mail', 'core');
//
//     $mail = $mailApi->create();
//     $mail
//       ->setFrom($values['from_address'], $values['from_name'])
//       ->setSubject($values['subject'])
//       ->setBodyHtml(nl2br($values['body']));
//
//     if( !empty($values['body_text']) ) {
//       $mail->setBodyText($values['body_text']);
//     } else {
//       $mail->setBodyText(strip_tags($values['body']));
//     }
//
//     foreach( $emails as $email ) {
//       $mail->addTo($email);
//     }
//
//     $mailApi->send($mail);
//
//     $mailComplete = $mailApi->create();
//     $mailComplete
//       ->addTo(Engine_Api::_()->user()->getViewer()->email)
//       ->setFrom($values['from_address'], $values['from_name'])
//       ->setSubject('Mailing Complete: '.$values['subject'])
//       ->setBodyHtml('Your email blast to your members has completed.  Please note that, while the emails have been
//         sent to the recipients\' mail server, there may be a delay in them actually receiving the email due to
//         spam filtering systems, incoming mail throttling features, and other systems beyond SocialEngine\'s control.')
//       ;
//     $mailApi->send($mailComplete);
//
//     // emails have been queued (or sent); re-set queueing value to original if changed
//     if (isset($values['queueing']) && $values['queueing']) {
//       Engine_Api::_()->getApi('settings', 'core')->core_mail_queueing = $temporary_queueing;
//     }

    $this->view->form = null;
    $this->view->status = true;
  }

  //Add new member using auto suggest
  public function getusersAction() {

    $sesdata = array();
    $users_table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $users_table->select()
                    ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
                    ->order('displayname ASC')->limit('40');
    $users = $users_table->fetchAll($select);

    foreach ($users as $user) {
      $user_icon_photo = $this->view->itemPhoto($user, 'thumb.icon');
      $sesdata[] = array(
          'id' => $user->user_id,
          'label' => $user->displayname,
          'photo' => $user_icon_photo
      );
    }
    return $this->_helper->json($sesdata);
  }

}
