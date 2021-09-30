<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
$module = null;
$controller = null;
$action = null;
$getURL = null;
$routes = array();
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
  $getURL = $request->getRequestUri();
}

$routes['sitestaticpage_manageadmins'] = array(
    'route' => 'admin/staticpage' . '/:action/*',
    'defaults' => array(
        'module' => 'sitestaticpage',
        'controller' => 'admin-manage',
        'action' => 'index',
    ),
    'reqs' => array(
        'action' => '(index|pageurlvalidation|form-list|form-data)',
    ),
);

if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {

  $default_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.manifestUrl', 'static');
  $db = Engine_Db_Table::getDefaultAdapter();
  $pages = $db->query("SELECT page_id, page_url, title, short_url FROM `engine4_sitestaticpage_pages`")->fetchAll();
  foreach ($pages as $page) {
    $page_id = $page['page_id'];
    $page_url = $page['page_url'];
    if(!empty($page['short_url']))
    {
      $routesTypeBase = array(
        'sitestaticpage_index_index_staticpageid_' . $page_id => array(
            'route' => $page_url,
            'defaults' => array(
                'module' => 'sitestaticpage',
                'controller' => 'index',
                'action' => 'index',
                'staticpage_id' => $page_id,
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
    );
    }
    else{
      $routesTypeBase = array(
        'sitestaticpage_index_index_staticpageid_' . $page_id => array(
            'route' => $default_url . '/' . $page_url,
            'defaults' => array(
                'module' => 'sitestaticpage',
                'controller' => 'index',
                'action' => 'index',
                'staticpage_id' => $page_id,
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
    );
    }
    
    $routes = array_merge($routes, $routesTypeBase);
  }
}

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestaticpage',
        'version' => '5.0.0',
        'path' => 'application/modules/Sitestaticpage',
        'title' => 'Static Pages, HTML Blocks and Multiple Forms Plugin',
        'description' => 'Static Pages, HTML Blocks and Multiple Forms Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Monday, 02 December 2013 17:40:08 +0000',
        'copyright' => 'Copyright 2013-2014 BigStep Technologies Pvt. Ltd.',
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitestaticpage/settings/install.php',
            'class' => 'Sitestaticpage_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitestaticpage',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitestaticpage.csv',
        ),
    ),
     'sitemobile_compatible' =>true,
// Items ---------------------------------------------------------------------
    'items' => array(
        'sitestaticpage_page'
    ),
// Routes --------------------------------------------------------------------
    'routes' => $routes,
);