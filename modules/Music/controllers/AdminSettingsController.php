<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminSettingsController.php 9909 2013-02-14 05:49:17Z matthew $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('music_admin_main', array(), 'music_admin_main_settings');    
  }
  
  public function indexAction()
  {
    $this->view->form = new Music_Form_Admin_Global();    
    if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
      $db->beginTransaction();
      try {
        $this->view->form->saveValues();
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
      $this->view->form->addNotice('Your changes have been saved.');
    }

  }
}
