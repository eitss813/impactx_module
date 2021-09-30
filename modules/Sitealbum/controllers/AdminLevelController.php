<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitealbum_AdminLevelController extends Core_Controller_Action_Admin {

  public function indexAction() {
  
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_level');

    // Get level id
    if (null !== ($id = $this->_getParam('level_id', $this->_getParam('id')))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Sitealbum_Form_Admin_Settings_Level(array(
        'public' => ( in_array($level->type, array('public')) ),
        'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    // Check post
    if (!$this->getRequest()->isPost()) {
      $form->populate($permissionsTable->getAllowed('album', $id, array_keys($form->getValues())));
      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license2.php';
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }
}