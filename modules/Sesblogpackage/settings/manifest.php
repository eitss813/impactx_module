<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: manifest.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sesblogpackage',
        //'sku' => 'sesblogpackage',
        'version' => '5.4.0',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '5.0.0',
            ),
        ),
        'path' => 'application/modules/Sesblogpackage',
        'title' => '<span style="color:#DDDDDD">SNS - Advanced Blogs - Packages for Allowing Blog Creation Extension</span>',
        'description' => '<span style="color:#DDDDDD">SNS - Advanced Blogs - Packages for Allowing Blog Creation Extension</span>',
        'author' => '<a href="https://socialnetworking.solutions" style="text-decoration:underline;" target="_blank">SocialNetworking.Solutions</a>',
        'callback' => array(
            'path' => 'application/modules/Sesblogpackage/settings/install.php',
            'class' => 'Sesblogpackage_Installer',
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
            0 => 'application/modules/Sesblogpackage',
        ),
        'files' => array(
            'application/languages/en/sesblogpackage.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sesblogpackage_package',
        'sesblogpackage_orderspackage',
        'sesblogpackage_gateway',
        'sesblogpackage_transaction'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sesblogpackage_general' => array(
            'route' => 'blogpackage/:action/*',
            'defaults' => array(
                'module' => 'sesblogpackage',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(blog|confirm-upgrade|cancel)',
            )
        ),
        'sesblogpackage_payment' => array(
            'route' => 'blogpayment/:action/*',
            'defaults' => array(
                'module' => 'sesblogpackage',
                'controller' => 'payment',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|process|return|finish|charge)',
            )
        )
    ),
);
