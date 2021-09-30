<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminWidgetsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    //TAB CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_widget');

		//FORM GENERATION
    //$this->view->form = $form = new Sitepage_Form_Admin_Widget();

		//PROCESS
//     if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
// 
//       $values = $form->getValues();
// 
//       Engine_Api::_()->getApi("settings", "core")->setSetting("sitepage_ajax_widgets_list", array("0" => "0", "1" => "0", "2" => "0", "3" => "0", "4" => "0"));
//       Engine_Api::_()->getApi("settings", "core")->setSetting("sitepage_ajax_widgets_layout", array("0" => "0", "1" => "0", "2" => "0"));
//       foreach ($values as $key => $value) {
	//   	if ($key != 'submit') {
	// 		Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
	//   	}
	// 	}
//     }
  }
}
?>