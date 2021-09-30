<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminVideoLevelController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminVideoLevelController extends Core_Controller_Action_Admin {

    public function indexAction() {

        // Make navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_level');
        $this->view->navigationGeneral = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_level', array(), 'sitevideo_admin_main_video_level');
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
        $this->view->form = $form = new Sitevideo_Form_Admin_Settings_Video_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $form->level_id->setValue($id);

        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

        // Check post
        if (!$this->getRequest()->isPost()) {
            $form->populate($permissionsTable->getAllowed('video', $id, array_keys($form->getValues())));
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
            include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
    }

}
