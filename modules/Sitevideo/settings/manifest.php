<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStartP = "channels";
$routeStartS = "channel";
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
    $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.manifestUrlP', "channels");
    $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.manifestUrlS', "channel");
}

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitevideo',
        'version' => '4.10.5p7',

        'path' => 'application/modules/Sitevideo',
        'title' => 'Advanced Videos / Channels / Playlists Plugin',
        'description' => 'Advanced Videos / Channels / Playlists plugin enables users to post videos from their local computers, YouTube, Vimeo and Dailymotion, create channels and create playlists.',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitevideo/settings/install.php',
            'class' => 'Sitevideo_Installer',
            'priority' => 1560,
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
            0 => 'application/modules/Sitevideo',
        ),
        'files' =>
        array(
            'application/languages/en/sitevideo.csv',
            'application/modules/Activity/Model/Helper/ItemSeaoChild.php'
        ),
    ),
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitevideo_Plugin_Core'
        ),
        array(
            'event' => 'onUserSignupAfter',
            'resource' => 'Sitevideo_Plugin_Core',
        ),
        array(
            'event' => 'onUserDeleteAfter',
            'resource' => 'Sitevideo_Plugin_Core',
        ),
        array(
            'event' => 'onStatistics',
            'resource' => 'Sitevideo_Plugin_Core',
        ),
        array(
            'event' => 'getActivity',
            'resource' => 'Sitevideo_Plugin_Core',
        ),
        array(
          'event' => 'addActivity',
          'resource' => 'Sitevideo_Plugin_Core',
        ),
        array(
          'event' => 'onSitevodStreamCompleted',
          'resource' => 'Sitevideo_Plugin_Stream'
        ),
    ),
    // Compose
    'composer' => array(
        'video' => array(
            'script' => array('_composeVideo.tpl', 'sitevideo'),
            'plugin' => 'Sitevideo_Plugin_Composer',
            'auth' => array('video', 'create'),
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitevideo_itemofthedays',
        'sitevideo_channel',
        'sitevideo_channel_category',
        'sitevideo_video_category',
        'sitevideo_video',
        'sitevideo_playlist',
        'sitevideo_playlistmap',
        'sitevideo_watchlater',
        'sitevideo_subscription',
        'sitevideo_album',
        'sitevideo_photo',
        'sitevideo_post',
        'sitevideo_topic',
        'video',
        'sitevideo'
    ),
    // COMPATIBLE WITH MOBILE / TABLET PLUGIN
    //'sitemobile_compatible' => true,
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitevideo_entry_view' => array(
            'route' => $routeStartS . '/:channel_url/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'channel',
                'action' => 'view',
            ),
        ),
        'sitevideo_playlist_view' => array(
            'route' => 'videos/playlist/:playlist_id/:slug/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'playlist',
                'action' => 'view',
                'slug' => ''
            ),
            'reqs' => array(
                'action' => '(view)',
            ),
        ),
        'sitevideo_playlist' => array(
            'route' => 'videos/playlist/:action/:playlist_id/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'playlist',
                'action' => 'playall'
            ),
            'reqs' => array(
                'action' => '(playall)',
                'playlist_id' => '\d+',
            )
        ),
        'sitevideo_badge' => array(
            'route' => $routeStartP . '/badge/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'badge',
                'action' => 'index'
            ),
            'reqs' => array(
                'action' => '(index|create|get-source)',
            ),
        ),
        'sitevideo_extended' => array(
            'route' => 'videos/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'sitevideo_specific' => array(
            'route' => $routeStartP . '/:action/:channel_id/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'channel',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(edit|editvideos|delete|order|slide-show|overview|video-edit)',
            ),
        ),
        'sitevideo_general' => array(
            'route' => $routeStartP . '/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'index',
                'action' => 'index'
            ),
            'reqs' => array(
                'action' => '(index|browse|manage|map|categories|get-search-channels|create|upload-photo|sub-category|subsub-category|channel-exists)',
            ),
        ),
        'video_general' => array(
            'route' => 'videos/:action/*',
            'defaults' => array(
                'module' => 'video',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse|create|list|manage)',
            )
        ),
        'sitevideo_video_general' => array(
            'route' => 'videos/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'index'
            ),
            'reqs' => array(
                'action' => '(create|index|browse|manage|get-search-videos|sub-category|subsub-category|categories|tagscloud|pinboard|map|edit-location|embed|check-password-protection|validation|upload-video|get-link|compose)',
            ),
        ),
        'sitevideo_playlist_general' => array(
            'route' => 'videos/playlists/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'playlist',
                'action' => 'manage'
            ),
            'reqs' => array(
                'action' => '(index|browse|manage|create|edit|delete)',
            ),
        ),
        'sitevideo_subscription_general' => array(
            'route' => 'videos/subscriptions/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'subscription',
                'action' => 'manage'
            ),
            'reqs' => array(
                'action' => '(index|browse|manage)',
            ),
        ),
        'sitevideo_watchlater_general' => array(
            'route' => 'videos/watchlaters/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'watchlater',
                'action' => 'manage'
            ),
            'reqs' => array(
                'action' => '(index|browse|manage)',
            ),
        ),
        'sitevideo_channel_general' => array(
            'route' => $routeStartP . '/channel/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'channel',
                'action' => 'manage'
            ),
            'reqs' => array(
                'action' => '(index|browse|manage|pinboard|tagscloud)',
            ),
        ),
        'sitevideo_dashboard' => array(
            'route' => $routeStartP . '/dashboard/:action/:channel_id/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'dashboard',
                'action' => 'meta-detail'
            ),
            'reqs' => array(
                'action' => '(change-photo|remove-photo|meta-detail|video-edit|my-videos|youtube-videos)',
                'channel_id' => '\d+',
            )
        ),
        'sitevideo_video_specific' => array(
            'route' => 'videos/:action/:video_id/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(delete|edit)',
            ),
        ),
        'sitevideo_image_specific' => array(
            'route' => $routeStartS . '/photo/view/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'photo',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(view|remove)',
            ),
        ),
        'sitevideo_photo_extended' => array(
            'route' => $routeStartS . '/photo/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'photo',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
        'sitevideo_photoalbumupload' => array(
            'route' => $routeStartP . '/photo/:channel_id/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'photo',
                'action' => 'upload',
                'channel_id' => '0',
            )
        ),
        'sitevideo_albumspecific' => array(
            'route' => $routeStartP . '/album/:action/:channel_id/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'album',
                'action' => 'editphotos',
            ),
            'reqs' => array(
                'action' => '(delete|edit|editphotos|upload|view)',
            ),
        ),
        'sitevideo_general_category' => array(
            'route' => $routeStartP . '/category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'sitevideo_category_home' => array(
            'route' => $routeStartP . '/category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'index',
                'action' => 'category-home',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'sitevideo_general_subcategory' => array(
            'route' => $routeStartP . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
        'sitevideo_general_subsubcategory' => array(
            'route' => $routeStartP . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id/:subsubcategoryname/:subsubcategory_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
        'sitevideo_video_general_category' => array(
            'route' => 'videos/category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'browse',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'sitevideo_video_category_home' => array(
            'route' => 'videos/category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'category-home',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'sitevideo_video_general_subcategory' => array(
            'route' => 'videos/category/:categoryname/:category_id/:subcategoryname/:subcategory_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'browse',
            ),
        ),
        'sitevideo_video_general_subsubcategory' => array(
            'route' => 'videos/category/:categoryname/:category_id/:subcategoryname/:subcategory_id/:subsubcategoryname/:subsubcategory_id',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'video',
                'action' => 'browse',
            ),
        ),
        'sitevideo_topic_extended' => array(
            'route' => $routeStartP . '/topic/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'topic',
                'action' => 'create'
            ),
        ),
        'sitevideo_post_extended' => array(
            'route' => $routeStartP . '/post/:action/*',
            'defaults' => array(
                'module' => 'sitevideo',
                'controller' => 'post',
                'action' => 'edit'
            ),
        ),
    )
);
