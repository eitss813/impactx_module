<?php
return array(
  'package' =>
  array(
    'type' => 'module',
    'name' => 'siteuseravatar',
    'seao-sku' => 'seao-siteuseravatar',
    'version' => '5.0.0',
    'path' => 'application/modules/Siteuseravatar',
    'title' => 'Member Avatars Plugin',
    'description' => 'Member Avatars Plugin',
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' =>
    array(
      'path' => 'application/modules/Siteuseravatar/settings/install.php',
      'class' => 'Siteuseravatar_Installer'
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.10.3',
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
      0 => 'application/modules/Siteuseravatar',
    ),
    'files' =>
    array(
      0 => 'application/languages/en/siteuseravatar.csv',
    ),
  ),
  'hooks' => array(
    array(
      'event' => 'onUserSignupAfter',
      'resource' => 'Siteuseravatar_Plugin_Photo',
    ),
    array(
      'event' => 'onUserUpdateAfter',
      'resource' => 'Siteuseravatar_Plugin_Photo',
    ),
  )
);
?>