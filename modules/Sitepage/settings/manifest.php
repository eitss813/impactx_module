<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStartP = "pageitems";
$routeStartS = "pageitem";
$module=null;$controller=null;$action=null;$getURL = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
  $getURL = $request->getRequestUri();
}
if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlP', "pageitems");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlS', "pageitem");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitepage',
        'version' => '4.10.5p6',
        'path' => 'application/modules/Sitepage',
        'title' => 'Directory / Pages',
        'description' => 'Directory / Pages',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thursday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitepage/settings/install.php',
            'class' => 'Sitepage_Installer',
            'priority' => 1980,
        ),
        'directories' => array(
            'application/modules/Sitepage',
        ),
        'files' => array(
            'application/languages/en/sitepage.csv',
        ),
    ),
    'sitemobile_compatible' =>true,
// Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitepage_Plugin_Core'
        ),
        array(
            'event' => 'onRenderLayoutDefaultSimple',
            'resource' => 'Sitepage_Plugin_Core'
        ),
        array(
            'event' => 'onStatistics',
            'resource' => 'Sitepage_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitepage_Plugin_Core',
        ),
         array(
            'event' => 'getActivity',
            'resource' => 'Sitepage_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Sitepage_Plugin_Core',
        ),
        array(
					'event' => 'onActivityActionCreateAfter',
					'resource' => 'Sitepage_Plugin_Core',
			  ),

// 			  array(
// 					'event' => 'onActivityCommentCreateAfter',
// 					'resource' => 'Sitepage_Plugin_Core',
// 			  ),
    ),
// Items ---------------------------------------------------------------------
    'items' => array(
        'sitepage_page',
        'sitepage_album',
        'sitepage_photo',
        'sitepage_category',
        'sitepage_verify',
        'sitepage_topic',
        'sitepage_post',
        'sitepage_profilemap',
        'sitepage_claim',
        'sitepage_package',
        'sitepage_gateway',
        'sitepage_location',
        'sitepage_listmemberclaims',
        'sitepage_itemofthedays',
        'sitepage_transaction',
        'sitepage_service',
        'sitepage_timing',
        'sitepage_button',
        'sitepage_definedlayout',
        'sitepage_import',
        'sitepage_membership',
        'sitepage_announcements',
        'sitepage_list',
        'sitepage_importfile',
        'sitepage_partner',
        'sitepage_api',
        'sitepage_adminnote',
        'sitepage_initiative',
        'sitepage_initiativemetric',
        'sitepage_initiativequestion',
        'sitepage_projectpayment',
        'sitepage_metric',
    ),
// Route--------------------------------------------------------------------
    'routes' => array(
        'service_specific' => array(
            'route' => '/dashboard/:action/:service_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'dashboard',
                // 'action' => 'index',
                'service_id' => '',
            ),
            'reqs' => array(
                'service_id' => '\d+',
                'action' => '(delete-service|edit-service)',
            ),
    ),
        'sitepage_api' => array(
            'route' => $routeStartP.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'manageapi',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitepage_extended' => array(
            'route' => $routeStartP.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitepage_button' => array(
            'route' => $routeStartP . '/button/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'button',
                'action' => 'add',
            ),
            'reqs' => array(
                'action' => '(add|edit|delete)',
            ),
        ),
        'sitepage_general' => array(
            'route' => $routeStartP.'/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|index|manage|create|subcategory|pageurlvalidation|get-search-pages|subsubcategory|map|pinboard-browse)',
            ),
        ),

				'sitepage_claimpages' => array(
				            'route' => $routeStartP.'/claim/:action/*',
				            'defaults' => array(
				                'module' => 'sitepage',
				                'controller' => 'claim',
				                'action' => 'index',
				            ),
				            'reqs' => array(
				                'action' => '(claim-page|get-pages|terms|my-pages|delete)',
				            ),
				        ),

			'sitepage_dashboard' => array(
			            'route' => $routeStartP.'/dashboard/:action/*',
			            'defaults' => array(
			                'module' => 'sitepage',
			                'controller' => 'dashboard',
			                //'action' => 'index',
			            ),
			            'reqs' => array(
			                'action' => '(get-started|edit-style|edit-location|overview|edit-address|profile-type|marketing|foursquare-help|contact|featured-owners|profile-picture|remove-photo|unhide-photo|app|foursquare|favourite|favourite-delete|upload-photo|wishlist|twitter|announcements|notification-settings|add-location|delete-location|all-location|manage-member-category|delete-member-category|reset-position-cover-photo|upload-cover-photo|get-albums-photos|remove-cover-photo|choose-project|service|create-service|timing|save-timing|linkpages|unlink-page)',
			            ),
			        ),
            'sitepage_transaction' => array(
                'route' => $routeStartP.'/transactions/:action/*',
                'defaults' => array(
                    'module' => 'sitepage',
                    'controller' => 'transactions',
                ),
                'reqs' => array(
                    'action' => '( get-transactions | project-transactions-details)',
                ),
            ),
			'sitepage_profilepage' => array(
			            'route' => $routeStartP.'/profilepage/:action/*',
			            'defaults' => array(
			                'module' => 'sitepage',
			                'controller' => 'profile',
			                //'action' => 'index',
			            ),
			            'reqs' => array(
			                'action' => '(message-owner|tell-a-friend|print|contact-detail|get-cover-photo|email-me)',
			            ),
			        ),
			'sitepage_like' => array(
          'route' => $routeStartP.'/like/:action/*',
          'defaults' => array(
              'module' => 'sitepage',
              'controller' => 'like',
              //'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(global-likes|like-pages|send-update|mylikes|my-joined)',
          ),
      ),

			'sitepage_packages' => array(
          'route' => $routeStartP.'/package/:action/*',
          'defaults' => array(
              'module' => 'sitepage',
              'controller' => 'package',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(detail|update-package|update-confirmation|cancel)',
          ),
      ),

			'sitepage_manageadmins' => array(
          'route' => $routeStartP.'/manageadmin/:action/*',
          'defaults' => array(
              'module' => 'sitepage',
              'controller' => 'manageadmin',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(my-pages|manage-auto-suggest|list|delete)',
          ),
      ),


        // Public
        'sitepage_entry_view' => array(
            'route' => $routeStartS.'/:page_url/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'view',
            ),
        ),
        // User

        'sitepage_delete' => array(
            'route' => $routeStartP.'/delete/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'page_id' => '\d+',
            )
        ),
        'sitepage_publish' => array(
            'route' => $routeStartP.'/publish/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'publish'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepage_close' => array(
            'route' => $routeStartP.'/close/:page_id/:closed/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'close'
            )
        ),
        'sitepage_edit' => array(
            'route' => $routeStartP.'/edit/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'edit',
                'page_id' => '0',
            )
        ),
        'sitepage_session_payment' => array(
            'route' => $routeStartP.'/payment/sessionpayment/',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'package',
                'action' => 'payment',
            ),
        ),
        'sitepage_payment' => array(
            'route' => $routeStartP.'/payment/',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'payment',
                'action' => 'index',
            ),
        ),
        'sitepage_process_payment' => array(
            'route' => $routeStartP.'/payment/process',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'payment',
                'action' => 'process',
            ),
        ),
        'sitepage_insights' => array(
            'route' => $routeStartP.'/insights/:action/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'insights',
                'action' => 'index',
                'page_id' => '0',
            )
        ),
        'sitepage_tags' => array(
            'route' => $routeStartP.'/tagscloud/:page/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'page' => 1
            )
        ),
        'sitepage_photoalbumupload' => array(
            'route' => $routeStartP.'/photo/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'photo',
                'action' => 'upload-album',
                'page_id' => '1'
            )
        ),
        'sitepage_imagephoto_specific' => array(
            'route' => $routeStartP.'/photo/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'photo',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(view|photo-edit|remove|make-page-profile-photo)',
            ),
        ),
        'sitepage_albumphoto_general' => array(
            'route' => $routeStartP.'/album/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'album',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(edit|delete|edit-photos|view-album|view|album-order)',
            ),
        ),
        'sitepage_general_category' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+'
            ),
        ),
        'sitepage_general_subcategory' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+'

            ),
         ),
        'sitepage_general_subsubcategory' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/:subsubcategory_id/:subsubcategoryname/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+'

            ),
         ),

        'sitepage_layout' => array(
            'route' => $routeStartP.'/layout/:page_id/',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'layout',
                'action' => 'layout',
            ),
            'reqs' => array(
                'page_id' => '\d+',
            )
        ),
        'sitepage_ajaxhomelist' => array(
            'route' => $routeStartP.'/ajaxhomelist/',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'ajax-home-list'
            )
        ),
        'sitepage_reports' => array(
            'route' => $routeStartP.'/insights/:action/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'insights',
                'action' => 'export-report',
                'page_id' => '0',
            )
        ),
        'sitepage_webpagereport' => array(
            'route' => $routeStartP.'/insights/:action/:page_id/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'insights',
                'action' => 'export-webpage',
                'page_id' => '0',
            )
        ),
        'sitepage_homesponsored' => array(
            'route' => $routeStartP.'/homesponsored',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'home-sponsored'
            )
        ),
        // User
        'sitepage_widget' => array(
            'route' => 'admin/pageitem/widgets/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'admin-widgets',
                'action' => 'index',
            )
        ),
        // User
        'sitepage_itemofday' => array(
            'route' => 'admin/pageitem/items/day/:page/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'admin-items',
                'action' => 'day',
                'page' => 1
            )
        ),

        'sitepage_viewmap' => array(
            'route' => $routeStartS.'/index/view-map/:id',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'index',
                'action' => 'view-map'
            )
        ),
				'sitepage_profilepagemobile' => array(
            'route' => $routeStartS . '/profile/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(upload-cover-photo|get-albums-photos|remove-cover-photo)'
            ),
        ),
        'sitepage_page_member' => array(
            'route' => $routeStart. '/member/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'dashboard',
            ),
            'reqs' => array(
                'action' => '(remove-external-member)',
            ),
        ),
        'sitepage_initiatives' => array(
            'route' => $routeStartP.'/initiatives/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'initiatives',
                'action' => 'list',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitepage_metrics' => array(
            'route' => $routeStartP.'/metrics/:metric_id/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'metrics',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitepage_projectpayment' => array(
            'route' => $routeStartP.'/project-payments/:action/*',
            'defaults' => array(
                'module' => 'sitepage',
                'controller' => 'project-payments',
                'action' => 'set-payment',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
    ),
);
?>
