<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteotpverifier_AdminLevelController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_level');
        $this->view->form = $form = new Siteotpverifier_Form_Admin_Level();
        // Get level id
        if (null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }
        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;
        $form->level_id->setValue($id);
        if (($level->type == 'public')) {
            $form->removeElement('time');
            $form->removeElement('resettime');
            $form->removeElement('max_resend');
            $form->removeElement('reset');
            $form->removeElement('login');
            $form->addNotice('No settings are available for this member level.');
        }
        // Populate values
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $form->populate($permissionsTable->getAllowed('siteotpverifier', $id, array_keys($form->getValues())));

        // Check post
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check validitiy
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process

        $values = $form->getValues();
        
        $nonBooleanSettings = $form->nonBooleanFields();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try {
            $permissionsTable->setAllowed('siteotpverifier', $id, $values,'',$nonBooleanSettings);
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
    }
}
