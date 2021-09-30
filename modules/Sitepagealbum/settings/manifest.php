<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "pagealbums";
$module = null;
$controller = null;
$action = null;
$getURL = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
  $getURL = $request->getRequestUri();
}
if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.manifestUrl', "page-albums");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitepagealbum',
        'version' => '4.10.3p2',
        'path' => 'application/modules/Sitepagealbum',
        'title' => 'Directory / Pages - Photo Albums Extension',
        'description' => 'Directory / Pages - Photo Albums Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' =>
        array(
            'path' => 'application/modules/Sitepagealbum/settings/install.php',
            'class' => 'Sitepagealbum_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitepagealbum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitepagealbum.csv',
        ),
    ),
    'sitemobile_compatible' =>true,
    // Compose -------------------------------------------------------------------
    'composer' => array(
        'sitepagephoto' => array(
            'script' => array('_composeSitepagePhoto.tpl', 'sitepagealbum'),
            'plugin' => 'Sitepagealbum_Plugin_Composer',
            'auth' => array('sitepage_page', 'spcreate'),
        ),
    ),
    'routes' => array(
        'sitepagealbumadmin_delete' => array(
            'route' => $routeStart.'/admin/delete/:id/*',
            'defaults' => array(
                'module' => 'sitepagealbum',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

        'sitepagealbum_featuredalbum' => array(
            'route' => $routeStart.'/admin/featured/:id/*',
            'defaults' => array(
                'module' => 'sitepagealbum',
                'controller' => 'admin-manage',
                'action' => 'featured'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

        'sitepagealbum_extended' => array(
            'route' => $routeStart.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'photo',
                'action' => 'index'
            ),
        ),

         'sitepagealbum_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'album',
                'action' => 'browse',
            ),
        ),

        'sitepagealbum_home' => array(
           'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'album',
                'action' => 'home',
            ),
        ),
    ),

);
?>