<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "pagevideos";
$module = null;
$controller = null;
$action = null;
$getURL = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
  $getURL = $request->getRequestUri();
}
if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.manifestUrl', "page-videos");
}
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'sitepagevideo',
        'version' => '4.10.3',
        'path' => 'application/modules/Sitepagevideo',
        'repository' => 'null',
        'title' => 'Directory / Pages - Videos Extension',
        'description' => 'Directory / Pages - Videos Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitepagevideo/settings/install.php',
            'class' => 'Sitepagevideo_Installer',
        ),
        'directories' => array(
            'application/modules/Sitepagevideo',
        ),
        'files' => array(
            'application/languages/en/sitepagevideo.csv',
        ),
    ),
    'sitemobile_compatible' =>true,
    // hooks
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitepagevideo_Plugin_Core',
        )
    ),
    // Compose
    'composer' => array(
        'sitepagevideo' => array(
            'script' => array('_composeSitepageVideo.tpl', 'sitepagevideo'),
            'plugin' => 'Sitepagevideo_Plugin_Composer',
            'auth' => array('sitepage_page', 'svcreate'),
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitepagevideo_video'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitepagevideo_general' => array(
            'route' => $routeStart . '/:action/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(index|create)',
            )
        ),
        'sitepagevideo_create' => array(
            'route' => $routeStart . '/create/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepagevideo_edit' => array(
            'route' => $routeStart . '/edit/:video_id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'edit'
            )
        ),
        'sitepagevideoadmin_delete' => array(
            'route' => $routeStart . '/admin/delete/:video_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'page_id' => '\d+'
            )
        ),
        'sitepagevideo_delete' => array(
            'route' => $routeStart . '/delete/:video_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'page_id' => '\d+'
            )
        ),
        'sitepagevideo_view' => array(
            'route' => $routeStart . '/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'sitepagevideo_featured' => array(
            'route' => $routeStart . '/featured/:video_id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'featured'
            ),
            'reqs' => array(
                'video_id' => '\d+'
            )
        ),

        'sitepagevideo_highlighted' => array(
            'route' => $routeStart . '/highlighted/:video_id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'highlighted'
            ),
            'reqs' => array(
                'video_id' => '\d+'
            )
        ), 

         'sitepagevideo_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),

         'sitepagevideo_home' => array(
            'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'home',
            ),
        ), 

        'sitepagevideo_featuredvideo' => array(
            'route' => $routeStart .'/admin/featuredvideo/:id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'admin-manage',
                'action' => 'featuredvideo',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitepagevideo_highlightedvideo' => array(
            'route' => $routeStart .'/admin/highlightedvideo/:id/*',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'admin-manage',
                'action' => 'highlightedvideo',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

         'sitepagevideo_tags' => array(
            'route' => $routeStart . '/tagscloud/:page/',
            'defaults' => array(
                'module' => 'sitepagevideo',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'page' => 1
            )
        ),
    )
)
?>
