<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE MEMBERS
  public function indexAction() {
    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitepagemember_admin_main', array(), 'sitepagemember_admin_manage_member');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION
    $this->view->formFilter = $formFilter = new Sitepagemember_Form_Admin_Manage_Filter();

    //FETCH MEMEBERS DATAS
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $tableSitepageName = Engine_Api::_()->getItemTable('sitepage_page')->info('name');

    $tablePagemember = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $tableNamePagemember = $tablePagemember->info('name');

    $select = $tablePagemember->select()
						->setIntegrityCheck(false)
						->from($tableNamePagemember, array('featured AS featured_member', 'member_id', 'user_id', 'COUNT("user_id") AS JOINP_COUNT'))
						->join($tableUserName, "$tableNamePagemember.user_id = $tableUserName.user_id", 'username')
						->join($tableSitepageName, "$tableNamePagemember.resource_id = $tableSitepageName.page_id")
						->where($tableNamePagemember . '.active = ?', 1)
						->where($tableSitepageName . '.closed = ?', '0')
						->where($tableSitepageName . '.approved = ?', '1')
						->where($tableSitepageName . '.search = ?', '1')
						->where($tableSitepageName . '.declined = ?', '0')
						->where($tableSitepageName . '.draft = ?', '1');

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    $this->view->title = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';
		if(!empty($_POST['title'])) { 
			$page_name = $_POST['title']; 
		} 
		elseif(!empty($_GET['title'])) { 
			$page_name = $_GET['title']; 
		} 
		elseif($this->_getParam('title', '')) { 
			$page_name = $this->_getParam('title', '');
		} 
		else { 
			$page_name = '';
		}
		$this->view->title = $values['title'] = $page_name; 
    	if (!empty($page_name)) {
			$select->where($tableSitepageName . '.title  LIKE ?', '%' . $page_name . '%');
		}    
    if (isset($_POST['search'])) {
      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUserName . '.displayname  LIKE ?', '%' . $_POST['owner'] . '%');
      }
		
      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($tableNamePagemember . '.featured = ? ', $_POST['featured']);
      }
      
      
      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'] )) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($tableSitepageName . '.category_id = ? ', $_POST['category_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'] )) {
        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $table = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $categoriesName = $table->info('name');
        $selectcategory = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");
        $row = $table->fetchRow($selectcategory);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }

        $select->where($tableSitepageName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableSitepageName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      }

      elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
        
        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $subsubcategory_id = $this->view->subsubcategory_id = $_POST['subsubcategory_id'];

        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $row->category_name;
        }
        $select->where($tableSitepageName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableSitepageName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                ->where($tableSitepageName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);;
      }
    }
    
    $values = array_merge(array('order' => 'member_id', 'order_direction' => 'DESC'), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'member_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    $select->group("$tableNamePagemember.user_id");
    
    include APPLICATION_PATH . '/application/modules/Sitepagemember/controllers/license/license2.php';
  }

  //ACTION FOR LEAVE THE MEMBER.
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    
    //GET PAGE ID.
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    if (!empty($page_id)) {

      Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('user_id =?' => $this->_getParam('user_id'), 'page_id =?' =>  $page_id));

			//DELETE ACTIVITY FEED OF JOIN PAGE ACCORDING TO USER ID.
			$action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?'  => 'sitepage_join', 'subject_id = ?' => $this->_getParam('user_id'), 'object_id = ?' => $page_id));
			$action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
			$action->delete();
			
			//MEMBER COUNT DECREASE WHEN MEMBER JOIN THE PAGE.
			$sitepage->member_count--;
			$sitepage->save();
    }
  }
  
  //ACTION FOR MULTI LEAVE MEMBER ENTRIES.
  public function multiDeleteMemberAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
				  Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('member_id =?' => (int) $value));
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
  
  //ACTION FOR MAKE MEMBERS FEATURED AND REMOVE FEATURED MEMBERS.
  public function featuredAction() {

    //GET USER ID AND PAGE ID
    $user_id = $this->_getParam('user_id');
    $page_id = $this->_getParam('page_id');

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $membershipTableName = $membershipTable->info('name');
    $select = $membershipTable->select()->from($membershipTableName, array('featured'))
              ->where('user_id = ?', $user_id);
    $sitepagemember = $membershipTable->fetchRow($select);

		if ($sitepagemember->featured == 0) {
			Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('featured' => 1), array('user_id = ?' => $user_id));
		}
		else {
			Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('featured'=>  '0'), array('user_id = ?' => $user_id));
		}
		
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
  
  //ACTION FOR TOTAL JOIN PAGE.
  public function pageJoinAction() {
  
		$this->view->user_id = $user_id = $this->_getParam('user_id');
		
		//GET THE FRIEND ID AND OBJECT OF USER.
    $this->view->showViewMore = $this->_getParam('showViewMore', 0);
		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($user_id, 'pageJoin');
		
		$paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }
}