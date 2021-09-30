<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

return array (
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sesnewsletter',
        //'sku' => 'sesnewsletter',
        'version' => '5.2.1',
		'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '5.0.0',
            ),
        ),
        'path' => 'application/modules/Sesnewsletter',
        'title' => 'SES - Newsletter / Email Marketing Plugin',
        'description' => 'SES - Newsletter / Email Marketing Plugin',
        'author' => '<a href="https://socialnetworking.solutions" style="text-decoration:underline;" target="_blank">SocialNetworking.Solutions</a>',
        'callback' => array(
            'path' => 'application/modules/Sesnewsletter/settings/install.php',
            'class' => 'Sesnewsletter_Installer',
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
            0 => 'application/modules/Sesnewsletter',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sesnewsletter.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Sesnewsletter_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sesnewsletter_subscriber', 'sesnewsletter_campaign', 'sesnewsletter_type', 'sesnewsletter_template',
        'sesnewsletter_newsletteremail', 'sesnewsletter_integrateothersmodule'

    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sesnewsletter_unsubscribe' => array(
            'route' => 'newsletter/:controller/:action/*',
            'defaults' => array(
                'module' => 'sesnewsletter',
                'controller' => 'index',
                'action' => 'unsubcribe'
            ),
        ),
        'sesnewsletter_extended' => array(
            'route' => 'newsletter/:controller/:action/*',
            'defaults' => array(
                'module' => 'sesnewsletter',
                'controller' => 'settings',
                'action' => 'newsletter-settings'
            ),
        ),
    )
);
