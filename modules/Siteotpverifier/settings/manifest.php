<?php
return array(
  'package' =>
  array(
    'type' => 'module',
    'name' => 'siteotpverifier',
    'seao-sku' => 'seao-siteotpverifier',
    'version' => '5.4.1p1',
    'path' => 'application/modules/Siteotpverifier',
    'title' => 'One Time Password (OTP) Plugin',
    'description' => 'One Time Password (OTP) Plugin',
    'author' => '<a href="http://www.socialapps.tech" style="text-decoration:underline;" target="_blank">SocialApps.tech</a>',
    'callback' => array(
      'path' => 'application/modules/Siteotpverifier/settings/install.php',
      'class' => 'Siteotpverifier_Installer',
    ),
    'dependencies' => array(
          array(
            'type' => 'module',
            'name' => 'core',
            'minVersion' => '4.10.3p1',
          ),
        ),
    'actions' =>
    array(
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array(
      0 => 'application/modules/Siteotpverifier',
      1 => 'application/modules/Seaocore/Plugin/Signup/',
//      1 => 'application/libraries/aws',
//      2 => 'application/libraries/Twilio', 
    ),
    'files' =>
    array(
      0 => 'application/languages/en/siteotpverifier.csv',
    ),
  ),
  'hooks' => array(
    array(
      'event' => 'routeShutdown',
      'resource' => 'Siteotpverifier_Plugin_Core',
    ),
    array(
        'event' => 'onRenderLayoutMobileSMDefault',
        'resource' => 'Siteotpverifier_Plugin_Core',
    ),
    array(
        'event' => 'onUserDeleteAfter',
        'resource' => 'Siteotpverifier_Plugin_Core',
    ),
  ),
  'items' => array(
    'siteotpverifier_user',
  ),
  'routes' => array(
    // User - General
    'siteotpverifier_lostpassword_choose' => array(
      'route' => 'auth/forgot/reset-options/:search/*',
      'defaults' => array(
        'module' => 'siteotpverifier',
        'controller' => 'auth',
        'action' => 'choose'
      ),
//      'reqs' => array(
//        'user_id' => '\D+',
//      )
    ),
    'siteotpverifier_extended' => array(
      'route' => 'otp/:controller/:action/*',
      'defaults' => array(
        'module' => 'siteotpverifier',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
  )
);
?>