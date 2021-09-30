<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminLevelController extends Core_Controller_Action_Admin {

  //ACTION FOR LEVEL SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_level');

    //GET LEVEL ID
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    //LEVEL AUTHORIZATION
    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    //GET LEVEL ID
    $id = $level->level_id;

    //FORM GENERATION
    $this->view->form = $form = new Sitepage_Form_Admin_Settings_Level(array(
                'public' => ( in_array($level->type, array('public')) ),
                'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
            ));

    $form->level_id->setValue($id);

    //POPULATE DATA
    $this->view->isEnabledPackage = Engine_Api::_()->sitepage()->hasPackageEnable();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $formValue = $permissionsTable->getAllowed('sitepage_page', $id, array_keys($form->getValues()));
    $form->populate($formValue);

    if (isset($formValue['profile'])) {

      if ($formValue['profile'] == 2) {
        $profileFields = Engine_Api::_()->sitepage()->getLevelProfileFields($id);
        $session = new Zend_Session_Namespace('profileFields');
        $session->profileFields = $profileFields;
      }
    }

    //FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }


    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();
    $profileFields = array();
    if (isset($values['profile']) && $values['profile'] == 2) {
      foreach ($_POST as $key => $value) {
        if (@strstr($key, '_profilecheck_') != null && $value) {
          $tc = @explode("_profilecheck_", $key);
          $profileFields[] = "1_" . $tc[0] . "_" . $value;
        }
      }

      if (empty($profileFields)) {
        $session->profileFields = $profileFields;
        $error = Zend_Registry::get('Zend_Translate')->_('Please select atleast one profile field.');
        return $form->addError($error);
      }
    }


    $values['profilefields'] = serialize($profileFields);

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {
      $permissionsTable->setAllowed('sitepage_page', $id, $values);
      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    if (isset($values['profile']) && $values['profile'] == 2) {
      $profileFields = Engine_Api::_()->sitepage()->getLevelProfileFields($id);
      $session->profileFields = $profileFields;
    }
    $form->addNotice('Your changes have been saved.');
  }

}

?>