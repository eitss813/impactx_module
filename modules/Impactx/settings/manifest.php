<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'impactx',
    'version' => '5.0.0',
    'sku' => 'impactx',
    'path' => 'application/modules/Impactx',
    'title' => 'Impactx Customization',
    'description' => 'Impactx Customization',
    'author' => 'Deepak Sharma',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Impactx',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/impactx.csv',
    ),
  ),
); ?>