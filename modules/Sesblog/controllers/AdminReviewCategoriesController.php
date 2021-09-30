<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AdminReviewCategoriesController.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_AdminReviewCategoriesController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_reviewsettings');
    
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main_reviewsettings', array(), 'sesblog_admin_main_review_cat');
    
    $this->view->subsubNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main_review_cat', array(), 'sesblog_admin_main_review_subcategories');
    
    $profiletype = array();
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sesblog_review');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $options = $profileTypeField->getElementParams('sesblog_blog');
      unset($options['options']['order']);
      unset($options['options']['multiOptions']['0']);
      $profiletype = $options['options']['multiOptions'];
    }
    $this->view->profiletypes = $profiletype;

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategory(array('column_name' => '*', 'profile_type' => true));
  }

  public function editCategoryAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_reviewsettings');
    
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main_reviewsettings', array(), 'sesblog_admin_main_review_cat');
    
    $this->view->subsubNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main_review_cat', array(), 'sesblog_admin_main_review_subcategories');

    $this->view->form = $form = new Sesblog_Form_Admin_Review_Category_Edit();

    $category = Engine_Api::_()->getItem('sesblog_category', $this->_getParam('id'));
    $form->populate($category->toArray());
    if ($category->subcat_id == 0 && $category->subsubcat_id == 0) {
      $form->setTitle('Edit This Category');
    } elseif ($category->subcat_id != 0) {
      $form->setTitle('Edit This 2nd-level Category');
    } elseif ($catparam == 'subsub') {
      $form->setTitle('Edit This 3rd-level Category');
    }

    //Check post
    if (!$this->getRequest()->isPost())
      return;

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $category->profile_type_review = isset($_POST['profile_type_review']) ? $_POST['profile_type_review'] : '';
      $category->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'sesblog', 'action' => 'index', 'controller' => 'review-categories'), 'admin_default', true);
  }
  
	public function reviewParameterAction() {
	
		$category_id = $this->_getParam('id',null);
		if(!$category_id)
			return $this->_forward('notfound', 'error', 'core');
			
		// In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    
    $this->view->form = $form = new Sesblog_Form_Admin_Review_AddParameter();
    
    $reviewParameters = Engine_Api::_()->getDbtable('parameters', 'sesblog')->getParameterResult(array('category_id'=>$category_id));
    
    if(!count($reviewParameters))
      $form->setTitle('Add Review Parameters');
    else{
      $form->setTitle('Edit Review Parameters');
      $form->submit->setLabel('Edit'); 
    }

    if( !$this->getRequest()->isPost())
      return;  
		 
    $table = Engine_Api::_()->getDbtable('parameters', 'sesblog');
    $tablename = $table->info('name');
    try {
    
      $values = $form->getValues();
      unset($values['addmore']);
      
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      
      $deleteIds = explode(',',$_POST['deletedIds']);
      
      foreach($deleteIds as $val) {
        if(!$val)
          continue;
        $query = 'DELETE FROM '.$tablename.' WHERE parameter_id = '.$val;
        $dbObject->query($query);
      }
      
      foreach($_POST as $key=>$value){
          if(count(explode('_',$key)) != 3 || !$value)
            continue;
          $id = str_replace('sesblog_review_','',$key);
          $query = 'UPDATE '.$tablename.' SET title = "'.$value .'" WHERE parameter_id = '.$id;
          $dbObject->query($query);
      }
      foreach($_POST['parameters'] as $val){					
        $query = 'INSERT IGNORE INTO '.$tablename.' (`parameter_id`, `category_id`, `title`, `rating`) VALUES ("","'.$category_id.'","'.$val.'","0")';
        $dbObject->query($query);
      }
    } catch( Exception $e ) {
			throw $e;
		}
		
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_("Review Parameters have been saved."))
    ));
	}
}
