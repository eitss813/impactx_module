<?php 
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'cbcore',
    'version' => '4.10.2',
    'path' => 'application/modules/Cbcore',
    'title' => 'Consecutive Bytes Core',
    'description' => 'CB Core Module is required for our all themes and plugin to work. ',
    'author' => '<a href="http://consecutivebytes.com/" target="_blank">consecutivebytes.com</a>',
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
      0 => 'application/modules/Cbcore',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/cbcore.csv',
    ),
  ),
); ?>