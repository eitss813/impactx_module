<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_AdminWidgetsController extends Core_Controller_Action_Admin {

  //ACTION FOR WIDGET SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_widget_settings');

    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagevideo_admin_submain', array(), 'sitepagevideo_admin_submain_general_tab');
  }

}
?>