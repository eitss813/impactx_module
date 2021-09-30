<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
$module1 = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module1 = $request->getModuleName();
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
$aboutus_route = "about-us";
if (empty($request) || !($module1 == 'default' && (strpos($_SERVER['REQUEST_URI'], '/install/') !== false))) {
  $aboutus_route = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.aboutusurlmanifest', "about-us");
}
return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'sesmultipleform',
    //'sku' => 'sesmultipleform',
    'version' => '5.3.0',
    'dependencies' => array(
        array(
            'type' => 'module',
            'name' => 'core',
            'minVersion' => '5.0.0',
        ),
    ),
    'path' => 'application/modules/Sesmultipleform',
    'title' => 'SES - All in One Multiple Forms Plugin - Advanced Contact Us, Feedback, Query Forms, etc',
    'description' => 'SES - All in One Multiple Forms Plugin - Advanced Contact Us, Feedback, Query Forms, etc',
     'author' => '<a href="https://socialnetworking.solutions" style="text-decoration:underline;" target="_blank">SocialNetworking.Solutions</a>',
    'callback' =>
    array (
      'path' => 'application/modules/Sesmultipleform/settings/install.php',
      'class' => 'Sesmultipleform_Installer',
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
      0 => 'application/modules/Sesmultipleform',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/sesmultipleform.csv',
    ),
  ),

    // Items ---------------------------------------------------------------------
  'items' => array(
    'sesmultipleform','sesmultipleform_category','sesmultipleform_form','sesmultipleform_entry','sesmultipleform_setting','sesmultipleform_keycontact'
  ),
  // Routes --------------------------------------------------------------------
    'routes' => array(
        'sesmultipleform_aboutus' => array(
            'route' => $aboutus_route . "/:action",
            'defaults' => array(
                'module' => 'sesmultipleform',
                'controller' => 'index',
                'action' => 'aboutus'
            ),
        ),
    ),
); ?>
