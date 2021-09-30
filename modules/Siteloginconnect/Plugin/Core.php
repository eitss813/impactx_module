<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Siteloginconnect
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.Seaocores.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z Siteloginconnect $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if($module === "user" && $controller === "settings" && $action === "general")   {
            $request->setModuleName('siteloginconnect');
        }
    }
}