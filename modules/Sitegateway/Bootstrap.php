<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Bootstrap.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initFrontController() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Sitegateway_Plugin_Core);
        
        include APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license.php';
    }

}
