<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegateway_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */

        if (!empty($method) && $method == 'Sitegateway_Form_Admin_Settings_Global') {
            
        }
        return true;
    }
    
    public function indexAction() {
        include_once APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license1.php';
    }
    
    public function faqAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegateway_admin_main', array(), 'sitegateway_admin_main_faqs');
    }

    public function readmeAction() {
        
    }

}
