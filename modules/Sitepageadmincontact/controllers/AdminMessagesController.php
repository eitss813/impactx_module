<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMessagesController.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageadmincontact_AdminMessagesController extends Core_Controller_Action_Admin {

  //ACTION FOR MESSAGE SETTINGS
  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageadmincontact_admin_main', array(), 'sitepageadmincontact_admin_main_messages');

      $this->view->form = $form = new Sitepageadmincontact_Form_Admin_Message_Message();
    
  }

}

?>