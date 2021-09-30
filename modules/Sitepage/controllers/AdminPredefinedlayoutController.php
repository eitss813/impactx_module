<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDefaultlayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminPredefinedlayoutController extends Core_Controller_Action_Admin {

  public function indexAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_layoutedit');

    $this->view->layouts = Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getLayouts();

  }
  public function createAction()
  {

    $this->view->form = $form = new Sitepage_Form_Admin_Definedlayout_Create();
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return ;
    }
    $values=$form->getValues();
    // Check if Duplicating
    $old_page_id = $this->_getParam('duplicate');
    // Get page param
    $page = $this->_getParam('page');
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    // Make new page
    if( ($page == 'new' || $page === null) && $this->getRequest()->isPost() ) {
      $pageObject = $pageTable->createRow();
      $pageObject->displayname = ( null !== ($name = $this->_getParam('name')) ? $name : 'Untitled' );
      $pageObject->title = $values['title'];
      $pageObject->provides = 'no-subject';
      $pageObject->save();
      $pageObject->name = "sitepage_index_view_layout_".$pageObject->page_id;
      $pageObject->save();
      $new_page_id = $pageObject->page_id;

      if( $old_page_id != 0) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $old_page_content = $db->select()
        ->from('engine4_core_content')
        ->where('`page_id` = ?', $old_page_id)
        ->order(array('type', 'content_id'))
        ->query()
        ->fetchAll();
        
        $content_count = count($old_page_content);
        for($i = 0; $i < $content_count; $i++){
          $contentRow = $contentTable->createRow();
          $contentRow->page_id = $new_page_id;
          $contentRow->type = $old_page_content[$i]['type'];
          $contentRow->name = $old_page_content[$i]['name'];
          if( $old_page_content[$i]['parent_content_id'] != null ) {
            $contentRow->parent_content_id = $content_id_array[$old_page_content[$i]['parent_content_id']];            
          }
          else{
            $contentRow->parent_content_id = $old_page_content[$i]['parent_content_id'];
          }
          $contentRow->order = $old_page_content[$i]['order'];
          $contentRow->params = $old_page_content[$i]['params'];
          $contentRow->attribs = $old_page_content[$i]['attribs'];
          $contentRow->save();
          $content_id_array[$old_page_content[$i]['content_id']] = $contentRow->content_id;
        }        
      }
      else{
        // Create Empty Content Rows
        $contentRow = $contentTable->createRow();
        $contentRow->type = 'container';
        $contentRow->name = 'main';
        $contentRow->page_id = $pageObject->page_id;
        // explicitly setting parent_content_id to null to prevent pages
        // to pass validation check after being created
        $contentRow->parent_content_id = NULL;
        $contentRow->save();

        $contentRow2 = $contentTable->createRow();
        $contentRow2->type = 'container';
        $contentRow2->name = 'middle';
        $contentRow2->page_id = $pageObject->page_id;
        $contentRow2->parent_content_id = $contentRow->content_id;
        $contentRow2->save();
      }
    }
    $table = Engine_Api::_()->getDbtable('definedlayouts', 'sitepage');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {

      $row=$table->createRow();
      $row->setFromArray($values);
      $row->page_id = $pageObject->page_id;
      if( !empty($values['photo']) ) {
        $row->setPhoto($form->photo);
      }
      $row->save();
      $db->commit();
    } catch( Exception $e ) {
      return $this->exceptionWrapper($e, $form, $db);
    }
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRedirect' => $this->view->url(array('controller' => 'content', 'action' => 'index', 'page' => $pageObject->page_id), 'admin_default', true),
      'parentRedirectTime' => 10,
          //'format' => 'smoothbox',
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('<ul class="form-notices" style="margin:10px auto;width:500px;float:none;"><li style="float:none;">Layout created</li></ul>'))
      ));
  }

  public function deleteAction() {

    $this->view->layout_id = $layout_id = $this->_getParam('layout_id');

    try {
      $this->view->form = $form = new Sitepage_Form_Admin_Definedlayout_Delete();
      if( !$this->getRequest()->isPost() ) {
        return;
      }
       if( !$form->isValid($this->getRequest()->getPost()) ) {
        return ;
      }
      Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->deleteLayout($layout_id);
    } catch( Exception $e ) {
      return $this->exceptionWrapper($e, $form, $db);
    }
        //SUCCESS
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout successfully Deleted'))
      ));
  }
  public function editAction()
  {
    $table = Engine_Api::_()->getItemTable('sitepage_definedlayout');
    $this->view->layout_id = $layout_id = $this->_getParam('layout_id');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
            //edit layout
      $this->view->form = $form = new Sitepage_Form_Admin_Definedlayout_Edit();

      $row = Engine_Api::_()->getItemTable('sitepage_definedlayout')->getLayout($layout_id);
      $row['name'] = Engine_Api::_()->getItemTable('sitepage_definedlayout')->getPageName($row['page_id']);
      $form->populate($row);
      $table = Engine_Api::_()->getItemTable('sitepage_definedlayout');
      $select = $table->select()->where('definedlayout_id=?',$layout_id);
      $row = $table->fetchRow($select);
      $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
      $pageSelect = $pageTable->select()->where('page_id=?',$row->page_id);
      $pageRow = $pageTable->fetchRow($pageSelect);
      if( !$this->getRequest()->isPost() ) {
        return;
      }
      if( !$form->isValid($this->getRequest()->getPost()) ) {
        return ;
      }

      $value=$form->getValues();
      $row->setFromArray($value);
      $pageRow->displayname = $value['name'];
      $pageRow->title = $value['title'];
      $pageRow->save();
      if( !empty($value['photo']) ) {
        $row->setPhoto($form->photo);
      }
      $row->save();

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      return $this->exceptionWrapper($e, $form, $db);
    }

    //SUCCESS
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 500,
      'parentRefresh' => 500,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout successfully Saved'))
      ));
  }
}
