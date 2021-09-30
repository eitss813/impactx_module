<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminIntegrateothermodulesController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_AdminIntegrateothermodulesController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_integrateothermodule');

    $this->view->enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    $select = Engine_Api::_()->getDbtable('integrateothersmodules', 'sesnewsletter')->select();

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  //Add New Plugin entry
  public function addmoduleAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_integrateothermodule');

    $this->view->form = $form = new Sesnewsletter_Form_Admin_AddModules();

    $this->view->type = $type = $this->_getParam('type');

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      $integrateothersmodulesTable = Engine_Api::_()->getDbtable('integrateothersmodules', 'sesnewsletter');

      $is_module_exists= $integrateothersmodulesTable->fetchRow(array('content_type = ?' => $values['content_type'], 'module_name = ?' => $values['module_name']));

      if (!empty($is_module_exists)) {
        $error = Zend_Registry::get('Zend_Translate')->_("This Module already exist in our database.");
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      $contentTypeItem = Engine_Api::_()->getItemTable($values['content_type']);

			//get current content type item id
      $primaryId = current($contentTypeItem->info("primary"));

			//get primary key for content type
      if (!empty($primaryId))
        $values['content_id'] = $primaryId;

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $dbInsert = Engine_Db_Table::getDefaultAdapter();
      try {
        $row = $integrateothersmodulesTable->createRow();
        $values['type'] = $type;
        $row->setFromArray($values);
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //Delete entry
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $inttable = Engine_Api::_()->getItem('sesnewsletter_integrateothersmodule', $this->_getParam('integrateothersmodule_id'));
        $pageName = "sesnewsletter_index_".$this->_getParam('integrateothersmodule_id');
        if (!empty($pageName)) {
          $page_id = $db->select()
                  ->from('engine4_core_pages', 'page_id')
                  ->where('name = ?', $pageName)
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
          if($page_id) {
            Engine_Api::_()->getDbTable('content', 'core')->delete(array('page_id =?' => $page_id));
            Engine_Api::_()->getDbTable('pages', 'core')->delete(array('page_id =?' => $page_id));
          }
        }

        $integrateothersmodules = Engine_Api::_()->getItem('sesnewsletter_integrateothersmodule', $this->_getParam('integrateothersmodule_id'));
        $integrateothersmodules->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('You have successfully delete entry.')
      ));
    }
    $this->renderScript('admin-integrateothermodules/delete.tpl');
  }

  //Enable / Disable Action
  public function enabledAction() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $content = Engine_Api::_()->getItemTable('sesnewsletter_integrateothersmodule')->fetchRow(array('integrateothersmodule_id = ?' => $this->_getParam('integrateothersmodule_id')));
    try {

      $content->enabled = !$content->enabled;
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}
