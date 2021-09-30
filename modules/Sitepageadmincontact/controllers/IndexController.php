<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageadmincontact_IndexController extends Core_Controller_Action_Standard {

  //ACION FOR SENDING THE MESSAGE
  public function indexAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = 0;
    }

    //IF SUPERADMIN,ADMIN IS NOT THERE THEN RETURN THE PRIVATE PAGE
    if (empty($level_id)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //IF SUPERADMIN,ADMIN IS NOT THERE THEN RETURN THE PRIVATE PAGE
    if ($level_id != 1 && $level_id != 2) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $this->view->results = $results = Engine_Api::_()->getDbtable('pages', 'sitepage')->checkPage();
    if (!empty($results)) {
      //MAKE FORM
      $this->view->form = $form = new Sitepageadmincontact_Form_Compose();
    }

    //ASSIGN THE COMPOSING STUFF
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (empty($data['composer']))
        continue;
      foreach ($data['composer'] as $type => $config) {
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;

    //GET PACKAGE ID
    $package_id = $this->_getParam('package_id');

    //MAKE PACKAGE ID ARRAY
    $package_id_array = explode(",", trim($package_id, ","));

    //GET CATEGORIES ID
    $categories_id = $this->_getParam('categories_id');

    //GET CATEGORIES ID ARRAY
    $categories_id_array = explode(",", trim($categories_id, ","));

    //GET CATEGORIES ID
    $status = $this->_getParam('status');

    //GET CATEGORIES ID ARRAY
    $status_id_array = explode(",", trim($status, ","));

    //GET USER TABLE
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

    //GET USER TABLE NAME
    $manageadminsTableName = $manageadminsTable->info('name');

    //GET PAGE TABLE NAME
    $pageTableName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');

    //PROCESS
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      //RY ATTACHMENT GETTING STUFF
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = $attachmentData['type'];
        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {
          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if ($config) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
          $parent = $attachment->getParent();
          if ($parent->getType() === 'user') {
            $attachment->search = 0;
            $attachment->save();
          } else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }

      //CHECK METHOD / DATA
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK METHOD / DATA
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      //CHECK PACKAGE IS ENABLED OR NOT
      if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!isset($package_id_array[0])) {
          $package_id_array[0] = 0;
        }
        if (empty($categories_id_array[0])) {
          $categories_id_array[0] = 0;
        }
      }

      //GET FORM VALUES
      $values = $form->getValues();

      //GETTING THE RESULTS
      if ((isset($package_id_array[0]) && empty($package_id_array[0])) && (isset($categories_id_array[0]) && empty($categories_id_array[0])) && (isset($status_id_array[0]) && empty($status_id_array[0]))) {
        $select = $manageadminsTable->select()->setIntegrityCheck(false)
                ->from($manageadminsTableName, array('user_id'))
                ->join($pageTableName, $pageTableName . '.page_id = ' . $manageadminsTableName . '.page_id', array())
                ->where($manageadminsTableName . '.user_id <>' . $viewer->getIdentity())
                ->group($manageadminsTableName . '.user_id')
                ->order('user_id ASC');
      } else {
        $select = $manageadminsTable->select()->setIntegrityCheck(false)
                ->from($manageadminsTableName, array('user_id'))
                ->join($pageTableName, $pageTableName . '.page_id = ' . $manageadminsTableName . '.page_id', array())
                ->where($manageadminsTableName . '.user_id <>' . $viewer->getIdentity())
                ->group($manageadminsTableName . '.user_id')
                ->order('user_id ASC');

        if (is_array($categories_id_array) && !empty($categories_id_array) && !empty($categories_id_array[0])) {
          $select->where('category_id IN (?)', $categories_id_array);
        }

        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          if (is_array($package_id_array) && !empty($package_id_array) && !empty($package_id_array[0])) {
            $select->where('package_id IN (?)', $package_id_array);
          }
        }

        $status_ids = $status_id_array;

        if (is_array($status_ids) && (in_array("Draft", $status_ids) && in_array("Published", $status_ids) )) {
          $select->where($rName . '.draft = ?', '0')->orWhere($rName . '.draft = ?', '1');
        } elseif (is_array($status_ids) && in_array("Draft", $status_ids)) {
          $select->where($rName . '.draft = ?', '0');
        } elseif (is_array($status_ids) && in_array("Published", $status_ids)) {
          $select->where($rName . '.draft = ?', '1');
        }

        if (is_array($status_ids) && (in_array("Open", $status_ids) && in_array("Closed", $status_ids) )) {
          $select->where($rName . '.closed = ?', '0')->orWhere($rName . '.closed = ?', '1');
        } elseif (is_array($status_ids) && in_array("Open", $status_ids)) {
          $select->where($rName . '.closed = ?', '0');
        } elseif (is_array($status_ids) && in_array("Closed", $status_ids)) {
          $select->where($rName . '.closed = ?', '1');
        }

        if (is_array($status_ids) && in_array("Featured", $status_ids)) {
          $select->where($rName . '.featured = ?', '1');
        }

        if (is_array($status_ids) && in_array("Sponsored", $status_ids)) {
          $select->where($rName . '.sponsored = ?', '1');
        }

        if (is_array($status_ids) && (in_array("Approved", $status_ids) && in_array("DisApproved", $status_ids) )) {
          $select->where($rName . '.approved = ?', '0')->orWhere($rName . '.approved = ?', '1');
        } elseif (is_array($status_ids) && in_array("Approved", $status_ids)) {
          $select->where($rName . '.approved = ?', '1');
        } elseif (is_array($status_ids) && in_array("DisApproved", $status_ids)) {
          $select->where($rName . '.approved = ?', '0');
        }

        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          if (is_array($status_ids) && (in_array("Running", $status_ids) && in_array("Expired", $status_ids) )) {
            $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"))->orWhere($rName . '.expiration_date < ?', date("Y-m-d H:i:s"));
          } elseif (is_array($status_ids) && in_array("Running", $status_ids)) {
            $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
          } elseif (is_array($status_ids) && in_array("Expired", $status_ids)) {
            $select->where($rName . '.expiration_date  < ?', date("Y-m-d H:i:s"));
          }
        }
      }

      //GET USER IDS
      $user_ids = $select->query()->fetchAll();
      if (!empty($user_ids)) {
        $user_idss = array();
        foreach ($user_ids as $key => $user_id) {
          $user_idss[] = $user_id['user_id'];
        }

        //GET RECIPIENTS
        $recipients = array_unique($user_idss);

        //GET USER
        $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

        //CREATE CONVERSATION
        $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer, $user_idss, $values['title'], $values['body'], $attachment
        );

        //SEND NOTIFICATIONS
        foreach ($recipientsUsers as $user) {
          if ($user->getIdentity() == $viewer->getIdentity()) {
            continue;
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $user, $viewer, $conversation, 'message_new'
          );
        }
      }

      // INCREMENT MESSAGE COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      //COMMIT
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
                  'smoothboxClose' => true,
                  'parentRefreshTime' => '60',
                  'parentRefresh' => 'true',
                  'format' => 'smoothbox'
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

}

?>