<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_AdminSettingsController extends Core_Controller_Action_Admin {

  public function __call($method, $params) {
      /*
       * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
       * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
       * REMEMBER:
       *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
       *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
       */
      if (!empty($method) && $method == 'Sitestaticpage_Form_Admin_Global') {

      }
      return true;
  }
  
  public function indexAction() {
    if (isset($_POST['sitestaticpage_lsettings']) && !empty($_POST['sitestaticpage_lsettings'])) {
      $_POST['sitestaticpage_lsettings'] = trim($_POST['sitestaticpage_lsettings']);
    }
    include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license1.php';
  }

  public function faqAction() {

    // GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_faq');
  }
  
  public function readmeAction() {
  }

}