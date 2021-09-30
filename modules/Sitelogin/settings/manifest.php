<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2017-03-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
  'package' =>
  array(
    'type' => 'module',
    'name' => 'sitelogin',
    'seao-sku' => 'seao-sitelogin',
    'version' => '5.0.0p1',
    'path' => 'application/modules/Sitelogin',
    'title' => 'Social Login and Sign-up Plugin',
    'description' => 'Social Login and Sign-up Plugin',
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' => array(
      'path' => 'application/modules/Sitelogin/settings/install.php',
      'class' => 'Sitelogin_Installer',
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
      'application/modules/Sitelogin',
      'application/libraries/Google',
    ),
    'files' =>
    array(
      0 => 'application/languages/en/sitelogin.csv',
      1 => 'application/modules/Seaocore/Plugin/Signup/Account.php',
    ),
  ),
  'sitemobile_compatible' => true,
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Sitelogin_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutMobileDefault',
      'resource' => 'Sitelogin_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutMobileDefaultSimple',
      'resource' => 'Sitelogin_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutDefaultSimple',
      'resource' => 'Sitelogin_Plugin_Core',
    ),
    array(
      'event' => 'onUserSignupAfter',
      'resource' => 'Sitelogin_Plugin_Core',
    ),   
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Sitelogin_Plugin_Core',
    ),
    array(
      'event' => 'onUserLogoutBefore',
      'resource' => 'Sitelogin_Plugin_Core'
    ),
  ),
);
?>
