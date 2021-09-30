<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminLevelController.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitestaticpage_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('level_id', $this->_getParam('id'))) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Sitestaticpage_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    if($public_level_id) {
			if (isset($form->level_id)) 
		  $form->level_id->removeMultiOption($public_level_id);
    }
    
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $form->populate($permissionsTable->getAllowed('sitestaticpage_page', $id, array_keys($form->getValues())));
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set permissions
      $permissionsTable->setAllowed('sitestaticpage_page', $id, $values);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }
}