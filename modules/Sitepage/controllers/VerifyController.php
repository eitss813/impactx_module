<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VerifyController.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_VerifyController extends Seaocore_Controller_Action_Standard {

  protected $_viewer;

  public function init() {
    $this->_viewer = Engine_Api::_()->user()->getViewer();
  }

  public function indexAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET THE VALUE OF RESOURCE ID ,RESOURCE TYPE AND RESOURCR TITLE
    $this->view->resource_id = $this->_getParam('resource_id');
    $this->view->resource = $resource = Engine_Api::_()->getItem('sitepage_page', $this->view->resource_id);

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $authorizationApi = Engine_Api::_()->authorization();
    $this->view->allowVerify = $allowVerify = $authorizationApi->isAllowed('sitepage_page', $viewer, 'page_verify');

    if (empty($allowVerify))
      return;

    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.comment.verifypopup', 0);
    $this->view->resource_title = $resource->getTitle();

  }

  // GET LIST OF USERS WHO HAS VERIFIED CURRENT VIEWING USERS
  public function contentVerifyMemberListAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) 
      Zend_Registry::set('setFixedCreationFormBack', 'Back');


    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
    $resource = Engine_Api::_()->getItem('sitepage_page', $resource_id);
    $this->view->resource_title = $resource->getTitle();

    $this->view->current_page = $page = $this->_getParam('page', 1);
    $this->view->current_total_verify = $page * 10;

    $verifyTable = Engine_Api::_()->getDbtable('verifies', 'sitepage');
    $this->view->verify_count = $verifyTable->getVerifyCount($resource_id);

    // GET LIST OF USERS WHO HAS VERIFIED CURRENT VIEWING USERS
    $params = array('admin_approve' => 1, 'resource_id' => $resource_id, 'status' => 1);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('verifies', 'sitepage')->getVerifyPaginator($params);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(10);
  }


  //HERE CURRENT VIEWING USER IS VERIFIED AND A SUCCESSFULL MESSEGE IS DISPLAYED
  public function proceedToVerifyAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    $viewer_id = $this->_viewer->getIdentity();
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET THE VALUE OF RESOURCE ID ,RESOURCE TYPE AND RESOURCR TITLE
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
    $resource = Engine_Api::_()->getItem('sitepage_page', $resource_id);
    $pageOwner = Engine_Api::_()->getItem('user', $resource->owner_id);

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $authorizationApi = Engine_Api::_()->authorization();
    $this->view->allowVerify = $allowVerify = $authorizationApi->isAllowed('sitepage_page', $viewer, 'page_verify');

    if (empty($allowVerify)) {
      return;
    }

    $this->view->resource_title = $resource->getTitle();
    //DUMP ALL THE VALUE IN AN ARRAY
    $values = array();
    $values['resource_type'] = 'page';
    $values['resource_id'] = $resource_id;
    $values['poster_type'] = "user";
    $values['poster_id'] = $viewer_id;
    $values['comments'] = $this->_getParam('comments');

    //GET THE VERIFY COUNT ,VERIFY LIMIT, ALLOW UNVERIFY AND IS COMMENT .
    $verifyTableObj = Engine_Api::_()->getDbtable('verifies', 'sitepage');

    $verify_count = $verifyTableObj->getVerifyCount($resource_id);

    $this->view->verify_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3);
    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.comment.verifypopup', 0);

    //IF ADMIN APPROVE IS ENABLED THEN ADMIN APPROVE WILL BE 0.
    $this->view->admin_approve = $admin_approve = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admin.approve', 0);

    if (!empty($admin_approve))
      $values['admin_approve'] = 0;
    else
      $verify_count = ++$verify_count; //OTHERWISE VERIFYCOUNT WILL BE INCREASED.
    
    $this->view->verify_count = $verify_count;

    //TO INSERT DATA IN VERIFY TABLE
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $verify = $verifyTableObj->createRow();
      $verify->setFromArray($values);
      $verify->save();
      $this->view->verify_id = $verify->verify_id; //$db->lastInsertId($verifyTableName);
      // NOTIFICATION AND ACTIVITY FEED WORK
      if (empty($admin_approve)) {
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($pageOwner, $this->_viewer, $verify, 'sitepage_verify_new');
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($this->_viewer, $resource, 'sitepage_verify_new');
      } else {
        $userObj = Engine_Api::_()->getDbtable('users', 'user');
        $select = $userObj->select()->where('level_id = ?', 1);
        $adminObj = $userObj->fetchAll($select);
        foreach ($adminObj as $adminRow) {
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($adminRow, $this->_viewer, $verify, 'sitepage_verify_user_request');
        }
      }
      
      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }


  //EDIT VERIFY ENTRY
  public function editAction() {
    $this->_helper->layout->setLayout('admin-simple');

    $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));
    $this->view->form = $form = new Sitepage_Form_Admin_Edit();

    //POPULATE EDIT FORM
    $form->populate($verifyObj->toarray());

    // CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        $verifyObj->comments = $_POST['comments'];
        // UPDATE THE VERIFY ENTRY FROM DATABASE
        $verifyObj->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      // AFTER EDIT FORWARD TO THE SAME PAGE
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have changed comments successfully.'))
      ));
    }
  }


  // DELETE SINGLE VERIFY ENTRY
  public function deleteAction() {

    // CHECK POST
    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $verifyObj = Engine_Api::_()->getItem('sitepage_verify', $this->_getParam('id'));

        // //DELETE ACTIVITY FEED
        $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'siteverify_new', 'subject_id = ?' => $verifyObj->poster_id, 'object_id = ?' => $verifyObj->resource_id));
        if (!empty($action_id)) {
          $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
          $action->delete();
        }
        // DELETE THE VERIFY ENTRY FROM DATABASE
        $verifyObj->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      // AFTER DELETE FORWARD TO THE SAME PAGE
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This verify entry has been deleted successfully.'))
      ));
    }

    // OUTPUT
    // $this->renderScript('/verify/delete.tpl');
  }

}