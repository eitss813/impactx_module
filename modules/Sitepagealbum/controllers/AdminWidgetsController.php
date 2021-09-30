<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_AdminWidgetsController extends Core_Controller_Action_Admin {

  //ACTION FOR WIDGET SETTINGS
  public function indexAction() {
    
    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_main', array(), 'sitepagealbum_admin_widget_settings');

    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_submain', array(), 'sitepagealbum_admin_submain_general_tab');

    //GET WIDGET SETTING FORM
    //$this->view->form = $form = new Sitepagealbum_Form_Admin_Widget();

    //CHECK FORM VALIDATION
    //if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
     // $values = $form->getValues();
      //include APPLICATION_PATH . '/application/modules/Sitepagealbum/controllers/license/license2.php';
    //}
  }

}

?>