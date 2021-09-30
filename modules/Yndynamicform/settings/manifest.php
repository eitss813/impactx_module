<?php return array(
    'package' =>
        array(
            'type' => 'module',
            'name' => 'yndynamicform',
            'version' => '4.01p1',
            'path' => 'application/modules/Yndynamicform',
            'title' => 'YNC - Dynamic Form',
            'description' => '',
            'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
            'dependencies' => array(
                array(
                    'type' => 'module',
                    'name' => 'younet-core',
                    'minVersion' => '4.02p13',
                ),
            ),
            'callback' => array(
                'path' => 'application/modules/Yndynamicform/settings/install.php',
                'class' => 'Yndynamicform_Installer',
            ),
            array(
                'class' => 'Engine_Package_Installer_Module',
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
                    0 => 'application/modules/Yndynamicform',
                ),
            'files' =>
                array(
                    0 => 'application/languages/en/yndynamicform.csv',
                ),
        ),
    'items' => array(
        'yndynamicform_form',
        'yndynamicform_category',
        'yndynamicform_confirmation',
        'yndynamicform_notification',
        'yndynamicform_entry',
    ),

    'hooks' => array(
        array(
            'event' => 'onItemDeleteBefore',
            'resource' => 'Yndynamicform_Plugin_Core',
        ),
        array(
            'event' => 'onUserSignupAfter',
            'resource' => 'Yndynamicform_Plugin_Core',
        ),
    ),

    'routes' => array(
        'yndynamicform_general' => array(
            'route' => 'dynamic-form/:action/*',
            'defaults' => array(
                'module' => 'yndynamicform',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'yndynamicform_entry_general' => array(
            'route' => 'dynamic-form/entries/:action/*',
            'defaults' => array(
                'module' => 'yndynamicform',
                'controller' => 'entries',
                'action' => 'manage',
            ),
            'reqs' => array(
//                'form_id' => '\d+',
                'action' => '\D+',
            )
        ),
        'yndynamicform_entry_specific' => array(
            'route' => 'dynamic-form/entry/:action/:entry_id/*',
            'defaults' => array(
                'module' => 'yndynamicform',
                'controller' => 'entries',
                'action' => 'index',
            ),
            'reqs' => array(
                'entry_id' => '\d+',
                'action' => '\D+',
            )
        ),

        'yndynamicform_form_general' => array(
            'route' => 'dynamic-form/form/:action/*',
            'defaults' => array(
                'module' => 'yndynamicform',
                'controller' => 'form',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
        //'route' => 'dynamic-form/entry/create/67/form_id/:form_id/project_id/:project_id/*',
        'yndynamicform_project_form_detail' => array(
            'route' => 'dynamic-form/project-form/:form_id/:project_id/:slug/*',
            'defaults' => array(
                'module' => 'yndynamicform',
                'controller' => 'project-form',
                'action' => 'detail',
                'slug' => '',
            ),
            'reqs' => array(
                'form_id' => '\d+',
                'project_id' => '\d+'
            )
        ),

        'yndynamicform_form_detail' => array(
            'route' => 'dynamic-form/form/:form_id/:slug/*',
            'defaults' => array(
                'module' => 'yndynamicform',
                'controller' => 'form',
                'action' => 'detail',
                'slug' => '',
            ),
            'reqs' => array(
                'form_id' => '\d+'
            )
        ),
    )
); ?>