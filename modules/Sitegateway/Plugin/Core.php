<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        if (substr($request->getPathInfo(), 1, 5) == "admin") {
            $module = $request->getModuleName();
            $controller = $request->getControllerName();
            $action = $request->getActionName();

            if ($module == 'payment' && $controller == 'admin-gateway' && $action == 'index') {
                $request->setModuleName('sitegateway');
                $request->setControllerName('admin-gateways');
                $request->setActionName('index');
            } elseif ($module == 'payment' && $controller == 'admin-package' && $action == 'create') {
                $request->setModuleName('sitegateway');
                $request->setControllerName('admin-package');
                $request->setActionName('create');
            }
        }
    }

}
