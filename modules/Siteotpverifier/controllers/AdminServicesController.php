<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecredit
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_AdminServicesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteotpverifier_admin_main', array(), 'siteotpverifier_admin_main_integration');
    $this->view->notFoundServices = $this->checkServicesLibrary();
  }

  public function enableAction()
  {
    $enable_module = $this->_getParam('enable_service');
    if( $enable_module === 'amazon' && !Engine_Api::_()->siteotpverifier()->amazonIntegrationEnabled() ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'amazon'), 'admin_default', true);
    }
    if( $enable_module === 'twilio' && !Engine_Api::_()->siteotpverifier()->twilioIntegrationEnabled() ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'twilio'), 'admin_default', true);
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if( $settings->hasSetting('siteotpverifier.integration') ) {
      $settings->removeSetting('siteotpverifier.integration');
    }
    $settings->setSetting('siteotpverifier.integration', $enable_module);
    return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'services', 'action' => 'index'), 'admin_default', true);
  }

  public function downloadAction()
  {
    $service = $this->_getParam('service');
    Engine_Api::_()->siteotpverifier()
      ->downloadFiles($service);
    return $this->_helper->redirector->gotoRoute(array('module' => 'siteotpverifier', 'controller' => 'services', 'action' => 'index'), 'admin_default', true);
  }

  private function checkServicesLibrary()
  {
    $results = array();
    if( !file_exists(APPLICATION_PATH . '/application/libraries/SEAO/aws/aws-autoloader.php') ) {
      $results[] = 'amazon';
    }
    if( !file_exists(APPLICATION_PATH . '/application/libraries/SEAO/Twilio/autoload.php') ) {
      $results[] = 'twilio';
    }
    return $results;
  }

}
