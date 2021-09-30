<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Bootstrap.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Bootstrap extends Engine_Application_Bootstrap_Abstract {
    
    public function __construct($application) {

        parent::__construct($application);
        $this->initViewHelperPath();
    }
    protected function _initFrontController() {

    $this->initViewHelperPath();
    $this->initActionHelperPath();
    //Initialize helper
    Zend_Controller_Action_HelperBroker::addHelper(new Sitelogin_Controller_Action_Helper_Sociallogins());
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitelogin_Plugin_Core);
  }

}
