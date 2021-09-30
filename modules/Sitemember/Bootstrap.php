<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initFrontController() {
        $this->initViewHelperPath();
        $this->initActionHelperPath();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Sitemember_Plugin_Core);
        Zend_Controller_Action_HelperBroker::addHelper(new Sitemember_Controller_Action_Helper_Usermemberfield());
        
        include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license.php';
    }

}