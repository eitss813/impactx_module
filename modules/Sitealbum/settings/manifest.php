<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitealbum',
        'version' => '5.0.0p1',
        'path' => 'application/modules/Sitealbum',
        'title' => 'Advanced Photo Albums',
        'description' => 'Advanced Photo Albums',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitealbum/settings/install.php',
            'class' => 'Sitealbum_Installer',
            'priority' => 1960,
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
            0 => 'application/modules/Sitealbum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitealbum.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
          'event' => 'onRenderLayoutDefault',
          'resource' => 'Sitealbum_Plugin_Core',
        ),
        array(
          'event' => 'onRenderLayoutDefaultSimple',
          'resource' => 'Sitealbum_Plugin_Core',
        ),
        array(
          'event' => 'onRenderLayoutDefaultSimple',
          'resource' => 'Sitealbum_Plugin_Core',
        ),
        array(
          'event' => 'onRenderLayoutMobileDefaultSimple',
          'resource' => 'Sitealbum_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitealbum_itemofthedays',
        'album',
        'album_category',
        'album_photo',
        'photo'
    ),
    // COMPATIBLE WITH MOBILE / TABLET PLUGIN
    'sitemobile_compatible' => true,
    // Routes --------------------------------------------------------------------
    'routes' => array(
    'album_extended' => array(
        'route' => 'albums/:controller/:action/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'index',
            'action' => 'index'
        ),
    ),
    'album_specific' => array(
        'route' => 'albums/:action/:album_id/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'album',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(compose-upload|delete|edit|editphotos|upload|view|order)',
        ),
    ),
    'album_general' => array(
        'route' => 'albums/:action/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'index',
            'action' => 'browse'
        ),
        'reqs' => array(
            'action' => '(browse|create|list|manage|upload|upload-photo)',
        ),
    ),
    'sitealbum_entry_view' => array(
        'route' => 'albums/:slug/:album_id/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'album',
            'action' => 'view',
            'slug' => ''
        ),
        'reqs' => array(
            'action' => '(view)',
        ),
    ),
    'sitealbum_badge' => array(
        'route' => 'albums/badge/:action/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'badge',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(index|create|get-source)',
        ),
    ),
    'sitealbum_extended' => array(
        'route' => 'albums/photo/:action/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'photo',
            'action' => 'view'
        ),
    ),
    'album_photo_specific' => array(
        'route' => 'albums/photos/:action/:album_id/:photo_id/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'photo',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(view|rotate|crop|flip)',
        ),
    ),
    'sitealbum_specific' => array(
        'route' => 'albums/:action/:album_id/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'album',
            'action' => 'view'
        ),
        'reqs' => array(
            'action' => '(edit|editphotos|delete|order|slide-show|add-album-of-day)',
        ),
    ),
    'sitealbum_general_category' => array(
        'route' => 'albums/category/:categoryname/:category_id',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'category_id' => '\d+',
        ),
    ),
    'sitealbum_general_subcategory' => array(
        'route' => 'albums/category/:categoryname/:category_id/:subcategoryname/:subcategory_id',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'index',
            'action' => 'browse',
        ),
    ),
    'sitealbum_general' => array(
        'route' => 'albums/:action/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'index',
            'action' => 'index'
        ),
        'reqs' => array(
            'action' => '(upload|index|browse|manage|map|categories|get-search-albums|featured-photos-carousel|tagged-user|unhide-photo|you-and-owner-photos|edit-location|tagscloud|pinboard|photos|check-password-protection)',
        ),
    ),
    'sitealbum_compose_upload' => array(
        'route' => 'album/album/compose-upload/*',
        'defaults' => array(
            'module' => 'sitealbum',
            'controller' => 'album',
            'action' => 'compose-upload'
        ),
    )
  )
);
