<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


$routeStart = "pagemembers";
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
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.manifestUrl', "page-members");
}
return array (
  'package' => array (
    'type' => 'module',
    'name' => 'sitepagemember',
    'version' => '4.10.3p4',
    'path' => 'application/modules/Sitepagemember',
    'title' => 'Directory / Pages - Page Members Extension',
    'description' => 'Directory / Pages - Page Members Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
		'callback' => array(
			'path' => 'application/modules/Sitepagemember/settings/install.php',
			'class' => 'Sitepagemember_Installer',
    ),
    'actions' => array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => array (
      0 => 'application/modules/Sitepagemember',
    ),
    'files' =>array (
      0 => 'application/languages/en/sitepagemember.csv',
    ),
  ),
	// Hooks ---------------------------------------------------------------------
	'hooks' => array(
		array(
			'event' => 'onActivityActionCreateAfter',
			'resource' => 'Sitepagemember_Plugin_Core',
		),
	),
  // Items ---------------------------------------------------------------------
  'items' => array (
    'sitepagemember_roles'
  ) ,
	// COMPATIBLE WITH MOBILE / TABLET PLUGIN --------------------------------------------------------------------
	 'sitemobile_compatible' =>true,
  // Route--------------------------------------------------------------------
	'routes' => array(
		'sitepage_profilepagemember' => array(
			'route' => $routeStart.'/member/:action/*',
			'defaults' => array(
					'module' => 'sitepagemember',
					'controller' => 'member',
					//'action' => 'index',
			),
			'reqs' => array(
					'action' => '(join|leave|request|cancel|invite|reject|accept|invite-members|joined-more-pages|respond)',
			),
		),

	  'sitepagemember_approve' => array(
			'route' => $routeStart.'/index/:action/*',
			'defaults' => array(
					'module' => 'sitepagemember',
					'controller' => 'index',
					//'action' => 'index',
			),
			'reqs' => array(
					'action' => '(approve|remove|featured|highlighted|reject|edit|request-member|page-join|get-item|member-join|edittitle|create-announcement|delete-announcement|edit-announcement|notification-settings)',
			),
		),
		'sitepagemember_browse' => array(
			'route' => $routeStart.'/browse/*',
			'defaults' => array(
				'module' => 'sitepagemember',
				'controller' => 'index',
				'action' => 'browse',
			),
		),
		'sitepagemember_home' => array(
			'route' => $routeStart.'/home/*',
			'defaults' => array(
					'module' => 'sitepagemember',
					'controller' => 'index',
					'action' => 'home',
			),
		),
        'sitepagemember_extended' => array(
            'route' => $routeStart . '/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitepagemember',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
	),
);