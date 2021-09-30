<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'cbpageanalytics',
        'version' => '4.10.4',
        'path' => 'application/modules/Cbpageanalytics',
        'title' => 'CB - Page Analytics',
        'description' => 'Tracks visits on all pages.',
        'author' => 'Consecutive Bytes',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'cbcore',
                'minVersion' => '4.10.0',
            ),
        ),
        'callback' => array(
            'path' => 'application/modules/Cbpageanalytics/settings/install.php',
            'class' => 'Cbpageanalytics_Installer',
        ),
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'directories' => array(
            0 => 'application/modules/Cbpageanalytics',
        ),
        'files' => array(
            0 => 'application/languages/en/cbpageanalytics.csv',
        ),
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        // General
        'cbpageanalytics_general' => array(
            'route' => 'cbpageanalytics/',
            'defaults' => array(
                'module' => 'cbpageanalytics',
                'controller' => 'index',
                'action' => 'index'
            ),
        ),
    )
);
?>