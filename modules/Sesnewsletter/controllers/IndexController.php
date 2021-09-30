<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_IndexController extends Core_Controller_Action_Standard {

    public function newsletterAction() {

        $email = $this->_getParam('email', null);
        $table = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter');

        $types = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getEnabledTypes();
        $email_verify = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.emailsubsverify', 4);
        if(count($types) > 0) {
            foreach($types as $type) {

                $isExistType = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExistType($email, $type->getIdentity());
                if(empty($isExistType)) {
                    $getUserId = Engine_Api::_()->sesnewsletter()->getUserId($email);
                    if(!empty($getUserId)) {
                        $user = Engine_Api::_()->getItem('user', $getUserId);
                        $values = array('resource_id' => $getUserId, 'level_id' => $user->level_id, 'email' => $email, 'resource_type' => 'user', 'displayname' => $user->getTitle(), 'type_id' => $type->getIdentity());
                        if(in_array($email_verify, array('1','2'))) {
                          $values['enabled'] = '0';
                        }
                    } else {
                        $values = array('resource_id' => 0, 'level_id' => 5, 'email' => $email, 'resource_type' => 'guest', 'type_id' => $type->getIdentity());
                        if(in_array($email_verify, array('1','3'))) {
                          $values['enabled'] = '0';
                        }
                    }

                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();
                    try {
                        $item = $table->createRow();
                        $item->setFromArray($values);
                        $item->save();
                        

                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'sesnewsletter_subscribe', array('host' => $_SERVER['HTTP_HOST']));

                        $this->view->subscriber_id = $item->subscriber_id;
                        $db->commit();
                    } catch(Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                } else {
                    $this->view->subscriber_id = 0;
                }
            }
            if(in_array($email_verify, array('1','2','3'))) {

                $verify_link = $this->view->absoluteUrl(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesnewsletter', 'controller' => 'index', 'action' => 'verify', 'email' => base64_encode($email)), 'default', true));

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'sesnewsletter_mailverify', array('host' => $_SERVER['HTTP_HOST'], 'verify_link' => $verify_link));
            }
        }
    }
    
    public function unsubscribeAction() {
      
      $email = $this->_getParam('email');
      $email = base64_decode($email);
      $db = Engine_Db_Table::getDefaultAdapter();
      
      if( empty($email) ) {
        $this->view->message = 2;
      }
      
      $isExist = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExist($email);
      
      if(!empty($isExist)) {
        $db->query('DELETE FROM `engine4_sesnewsletter_subscribers` WHERE `engine4_sesnewsletter_subscribers`.`email` = "'.$email.'";');
        $this->view->message = 1;
      } else {
        $this->view->message = 0;
      }
      
      
    }
    
    public function verifyAction() {
      
      $email = $this->_getParam('email');
      $email = base64_decode($email);
      if( empty($email) ) {
        $this->view->message = 2;
      }
      
      $isExist = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExist($email);
      
      if(!empty($isExist)) {
        $subscriber = Engine_Api::_()->getItem('sesnewsletter_subscriber', $isExist);
        $subscriber->enabled = 1;
        $subscriber->save();
        $this->view->message = 1;
      } else {
        $this->view->message = 0;
      }
      
      
    }
}
