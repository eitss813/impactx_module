<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: manifest.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

$blogsRoute = "blogs";
$module1 = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module1 = $request->getModuleName();
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !((strpos($_SERVER['REQUEST_URI'],'/install/') !== false))) {
  $setting = Engine_Api::_()->getApi('settings', 'core');
  $blogsRoute = $setting->getSetting('sesblog.blogs.manifest', 'blogs');
  $blogRoute = $setting->getSetting('sesblog.blog.manifest', 'blog');
}

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'sesblog',
    //'sku' => 'sesblog',
    'version' => '5.6.1',
    'path' => 'application/modules/Sesblog',
    'title' => 'SNS - Advanced Blog Plugin',
    'description' => 'SNS - Advanced Blog Plugin',
    'author' => '<a href="http://socialnetworking.solutions" style="text-decoration:underline;" target="_blank">SocialNetworking.Solutions</a>',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Sesblog/settings/install.php',
      'class' => 'Sesblog_Installer',
    ),
    'directories' => array(
      'application/modules/Sesblog',
      'application/modules/Sesblogpackage',
    ),
    'files' => array(
      'application/languages/en/sesblog.csv',
      'application/languages/en/sesblogpackage.csv',
    ),
  ),
  // Compose
  'composer' => array(
    'sesblog' => array(
      'script' => array('_composeBlog.tpl', 'sesblog'),
      'auth' => array('sesblog_blog', 'create'),
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Sesblog_Plugin_Core'
    ),
		array(
			'event' => 'onRenderLayoutDefault',
			'resource' => 'Sesblog_Plugin_Core',
		),
		array(
				'event' => 'onRenderLayoutDefaultSimple',
				'resource' => 'Sesblog_Plugin_Core'
		),
		array(
				'event' => 'onRenderLayoutMobileDefault',
				'resource' => 'Sesblog_Plugin_Core'
		),
		array(
				'event' => 'onRenderLayoutMobileDefaultSimple',
				'resource' => 'Sesblog_Plugin_Core'
		),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Sesblog_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'sesblog',
    'sesblog_parameter',
    'sesblog_blog',
    'sesblog_claim',
    'sesblog_category',
    'sesblog_dashboards',
    'sesblog_album',
    'sesblog_photo',
    'sesblog_review',
    'sesblog_categorymapping',
    'sesblog_integrateothermodule',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'sesblog_specific' => array(
      'route' => $blogsRoute.'/:action/:blog_id/*',
      'defaults' => array(
        'module' => 'sesblog',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'blog_id' => '\d+',
        'action' => '(delete|edit)',
      ),
    ),
    'sesblog_general' => array(
      'route' => $blogsRoute.'/:action/*',
      'defaults' => array(
        'module' => 'sesblog',
        'controller' => 'index',
        'action' => 'welcome',
      ),
      'reqs' => array(
        'action' => '(welcome|index|browse|create|manage|style|tag|upload-photo|link-blog|blog-request|tags|home|claim|claim-requests|locations|rss-feed|contributors|nonloginredirect|viewpagescroll|cancel-claim-request|package|transactions)',
      ),
    ),

            'sesblog_extended' => array(
            'route' => 'Sesblogs/:controller/:action/*',
            'defaults' => array(
                'module' => 'sesblog',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
    'sesblog_view' => array(
      'route' => $blogRoute.'/:user_id/*',
      'defaults' => array(
        'module' => 'sesblog',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),
		'sesblog_category_view' => array(
		    'route' => $blogsRoute.'/category/:category_id/*',
		    'defaults' => array(
		        'module' => 'sesblog',
		        'controller' => 'category',
		        'action' => 'index',
		    )
		),
    'sesblog_entry_view' => array(
      'route' => $blogRoute.'/:blog_id/*',
      'defaults' => array(
        'module' => 'sesblog',
        'controller' => 'index',
        'action' => 'view',
      //  'slug' => '',
      ),
      'reqs' => array(
      ),
    ),
    'sesblogreview_extended' => array(
        'route' => $blogsRoute.'/reviews/:action/:blog_id/*',
        'defaults' => array(
            'module' => 'sesblog',
            'controller' => 'review',
            'action' => 'index',
        ),
        'reqs' => array(
            'blog_id' => '\d+',
            'action' => '(create)',
        )
    ),
            'sesblog_specific_album' => array(
					'route' =>  'blog-album/:action/:album_id',
					'defaults' => array(
							'module' => 'sesblog',
							'controller' => 'album',
							'action' => 'view',
						),
							'reqs' => array(
							'album_id' => '\d+'
						)
        ),
    'sesblogreview_view' => array(
        'route' => $blogsRoute.'/reviews/:action/:review_id/:slug',
        'defaults' => array(
            'module' => 'sesblog',
            'controller' => 'review',
            'action' => 'view',
            'slug' => ''
        ),
        'reqs' => array(
            'action' => '(edit|view|delete)',
            'review_id' => '\d+'
        )
    ),
            'sesblog_dashboard' => array(
            'route' => $blogsRoute.'/dashboard/:action/:blog_id/*',
            'defaults' => array(
                'module' => 'sesblog',
                'controller' => 'dashboard',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '(edit|edit-photo|remove-photo|contact-information|style|seo|blog-role|save-blog-admin|fields|upgrade|edit-location|change-owner|search-member)',
            )
        ),
                'sesblog_review' => array(
            'route' => $blogsRoute.'/browse-review/:action/*',
            'defaults' => array(
                'module' => 'sesblog',
                'controller' => 'review',
                'action' => 'browse'
            ),
        ),
                'sesblog_category' => array(
            'route' => $blogsRoute . '/categories/:action/*',
            'defaults' => array(
                'module' => 'sesblog',
                'controller' => 'category',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse)',
            )
        ),
        'sesblog_import' => array(
      'route' => $blogsRoute.'/import/:action/*',
      'defaults' => array(
        'module' => 'sesblog',
        'controller' => 'import',
        'action' => 'index',
      ),
      'reqs' => array(
                'action' => '(index)',
            )

    ),
  ),
);
