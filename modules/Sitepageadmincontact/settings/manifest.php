<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitepageadmincontact',
        'version' => '4.9.4',
        'path' => 'application/modules/Sitepageadmincontact',
        'title' => 'Directory / Pages - Contact Page Owners Extension',
        'description' => 'Directory / Pages - Contact Page Owners Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' => array(
            'path' => 'application/modules/Sitepageadmincontact/settings/install.php',
            'class' => 'Sitepageadmincontact_Installer',
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
            0 => 'application/modules/Sitepageadmincontact',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitepageadmincontact.csv',
        ),
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitepageadmincontact_messages_general' => array(
            'route' => 'sitepageadmincontact/:action/*',
            'defaults' => array(
                'module' => 'sitepageadmincontact',
                'controller' => 'index',
                'action' => '(index)',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
    )
);
?>