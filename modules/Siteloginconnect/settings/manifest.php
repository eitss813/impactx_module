<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'siteloginconnect',
    'version' => '4.10.5',
    'path' => 'application/modules/Siteloginconnect',
    'title' => 'Social Connect & Profile Sync Extension - Facebook, LinkedIn, Twitter and Instagram',
    'shortTitle' => 'Social Connect & Profile Sync Extension',
    'description' => 'Connect SocialEngine Profiles with existing social sites.',
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' => array(
      'path' => 'application/modules/Siteloginconnect/settings/install.php',
      'class' => 'Siteloginconnect_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Siteloginconnect',
    ),      
  ),    
  'hooks' => array(
      array(
        'event' => 'routeShutdown',
        'resource' => 'Siteloginconnect_Plugin_Core',
      ),
    ),
    'routes' => array(
    'siteloginconnect_extended' => array(
      'route' => 'sync/:controller/:action/*',
      'defaults' => array(
        'module' => 'siteloginconnect',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '(index)',
        'action' => '(selectdata)',
      )
    ),
  )
); ?>