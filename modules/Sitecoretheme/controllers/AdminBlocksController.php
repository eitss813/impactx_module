<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminBlocksController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminBlocksController extends Core_Controller_Action_Admin
{

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_blocks');
  }

  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('blocks', 'sitecoretheme');
    $this->view->params = $params = array(
      'limit' => $this->_getParam('limit', 10),
      'page' => $this->_getParam('page', 1),
    );
    $this->view->paginator = $table->getBlocksPaginator($params);
  }

  public function createAction()
  {
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Blocks_Create();
    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    // Save
    $values = $form->getValues();
    $title = $values['title'];
    unset($values['title']);
    $subheading = $values['subheading'];
    unset($values['subheading']);
    $body = $values['body'];
    unset($values['body']);
    $photo = $values['photo'];
    unset($values['photo']);
    $table = Engine_Api::_()->getDbTable('blocks', 'sitecoretheme');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $block = $table->createRow();
      $block->title = $title;
      $block->body = $body;
      $block->subheading = $subheading;
      $block->params = $values;
      $block->save();
      // Set photo
      if( !empty($photo) ) {
        $block->setPhoto($form->photo);
      }
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }
    // redirect to manage page for now
    $this->_helper->redirector->gotoRoute(array('module' => 'sitecoretheme', 'controller' => 'blocks'), 'admin_default', true);
  }

  public function editAction()
  {
    $this->view->block_id = $blockId = $this->_getParam('id');
    $this->view->block = $block = Engine_Api::_()->getDbtable('blocks', 'sitecoretheme')->getBlock($blockId);
    if( !$block ) {
      throw new Core_Model_Exception('missing block');
    }

    $this->view->form = $form = new Sitecoretheme_Form_Admin_Blocks_Edit();

    // Make safe
    $blockData = $block->toArray();
    if( is_array($blockData['params']) ) {
      $blockData = array_merge($blockData, $blockData['params']);
    }

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      $form->populate($blockData);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    // Save
    $values = $form->getValues();
    $title = $values['title'];
    unset($values['title']);
    $body = $values['body'];
    unset($values['body']);
    $photo = $values['photo'];
    unset($values['photo']);
    $subheading = $values['subheading'];
    unset($values['subheading']);
    $table = Engine_Api::_()->getDbTable('blocks', 'sitecoretheme');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $block->title = $title;
      $block->body = $body;
      $block->subheading = $subheading;
      $block->params = $values;
      $block->save();
      // Set photo
      if( !empty($photo) ) {
        $block->setPhoto($form->photo);
      }
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }
    // redirect to manage page for now
    $this->_helper->redirector->gotoRoute(array('module' => 'sitecoretheme', 'controller' => 'blocks'), 'admin_default', true);
  }

  public function deleteAction()
  {
    $this->view->block_id = $blockId = $this->_getParam('id');
    $block = Engine_Api::_()->getDbtable('blocks', 'sitecoretheme')->getBlock($blockId);

    if( !$block ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Blocks_Delete();

    // Check stuff
    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    $block->delete();
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format' => 'smoothbox',
      'messages' => array(Zend_Registry::get('Zend_Translate')->_("Block Deleted"))
    ));
  }
}