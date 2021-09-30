<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetsController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_AdminWidgetsController extends Core_Controller_Action_Admin {

  //ACTION FOR WIDGET SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagemember_admin_main', array(), 'sitepagemember_admin_widget_settings');
  
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepagemember_Form_Admin_Manage_Filter();

    $page = $this->_getParam('page', 1);

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    $values = array_merge( array('order' => 'start_date', 'order_direction' => 'DESC'), $values );

    $this->view->assign($values);
    $this->view->memberOfDaysList = $memberOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->getItemOfDayList($values, 'user_id', 'user');

    $memberOfDay->setItemCountPerPage(50);
    $memberOfDay->setCurrentPageNumber($page);

  }
  
  //ACTION FOR ADDING MEMBER OF THE DAY.
  public function addMemberOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepagemember_Form_Admin_MemberOfDay();

    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->getElement('title')->setLabel('Member Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()
														->where('resource_id = ?', $values["member_id"])
														->where('resource_type = ?', 'user');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["member_id"];
        }

        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'user';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Member of the Day has been added successfully.'))
      ));
    }
  }
  
  //ACTION FOR GET MEMBER.
  public function getitemAction() {
  
		$member_name = $this->_getParam('text', null);
		$limit = $this->_getParam('limit', 40);
    $data = array();
    $getTempItem = false;

		$UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$UserTableName = $UserTable->info('name');

		$memberstable = Engine_Api::_()->getDbtable('membership', 'sitepage');
		$tableMemberName = $memberstable->info('name'); 
    
    include APPLICATION_PATH . '/application/modules/Sitepagemember/controllers/license/license2.php';
		
    $select = $UserTable->select()
				->setIntegrityCheck(false)
				->from($UserTableName, array('user_id', 'displayname', 'photo_id'))
				->joinleft($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id')
			  ->where($UserTableName . ".username LIKE ? OR " . $UserTableName . ".displayname LIKE ? ", '%' . $member_name . '%')
				->where('active = ?', '1')
				->where('user_approved = ?', '1')
				->group("$tableMemberName.user_id")
				->order('displayname ASC')
				->limit($limit);

    //FETCH RESULTS
    $members = $UserTable->fetchAll($select);
    
    foreach ($members as $member) {
			$member_photo = $this->view->itemPhoto($member, 'thumb.icon');
      $data[] = array(
				'id' => $member->user_id,
				'label' => $member->displayname,
				'photo' => $member_photo
      );
    }
        
    if( empty($getTempItem) ) {
      return;
    }else {
      return $this->_helper->json($data);
    }
  }
  
  //ACTION FOR DELETE MEMBER OF DAY ENTRY.
  public function deleteMemberOfDayAction() {
  
    $this->view->id = $this->_getParam('id');
    
    if ($this->getRequest()->isPost()) {

      Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));

			return $this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
			));
    }

    $this->renderScript('admin-widgets/delete.tpl');
  }
  
  //ACTION FOR MULTI DELETE MEMBER ENTRIES
  public function multiDeleteMemberAction() {

    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $sitepageitemofthedays = Engine_Api::_()->getItem('sitepage_itemofthedays', (int) $value);
          if (!empty($sitepageitemofthedays)) {
            $sitepageitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}