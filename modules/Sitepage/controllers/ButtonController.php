<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_ButtonController extends Seaocore_Controller_Action_Standard
{
	public function addAction()
	{
		if ( $this->getRequest()->isPost() ) {
			$values = $_POST;
			if(empty($_POST['label']))
				$values['label'] = $_POST['label1'];
			$values['page_id'] = $this->_getParam('page_id');
			$table = Engine_Api::_()->getItemTable('sitepage_button');
			$db = $table->getAdapter();
			$db->beginTransaction();

			try {
				$row = $table->createRow();
				$row->setFromArray($values);
				$row->save();
				$db->commit();
			} catch( Exception $e ) {
				$db->rollBack();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => 100,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Data Saved'))
				));
		}
	}
	public function editAction()
	{
		$page_id = $this->_getParam('page_id');
		$table = Engine_Api::_()->getItemTable('sitepage_button');
		$select = $table->select()->where('page_id = ?', $page_id);
		$row = $table->fetchRow($select);
		if ( !$this->getRequest()->isPost() ) {
			$this->view->button = $row;			
		} else {
			$values = $_POST;
			if(empty($_POST['label']))
				$values['label'] = $_POST['label1'];
			$values['page_id'] = $page_id;
			$row->setFromArray($values);
			$row->save();
			$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => 100,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Data Saved'))
				));
		}
	}
	public function deleteAction()
	{
		$this->_helper->layout->setLayout('default-simple');
		if( !$this->_helper->requireUser()->isValid() ) return;
        // Process
		$page_id = $this->_getParam('page_id');
		$this->view->form = $form = new Sitepage_Form_Deletebutton();
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		Engine_Api::_()->getItemTable('sitepage_button')->deletePageButton($page_id);

        //SUCCESS
		$this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => 100,
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Button successfully Deleted'))
			));
	}
}
?>