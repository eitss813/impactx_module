<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: menifest.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitemember',
        'version' => '5.0.1',
        'path' => 'application/modules/Sitemember',
        'title' => 'Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin',
        'description' => 'Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitemember/settings/install.php',
            'class' => 'Sitemember_Installer',
            'priority' => 1880,
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
            0 => 'application/modules/Sitemember',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitemember.csv',
        ),
    ),
    // COMPATIBLE WITH MOBILE / TABLET PLUGIN
	  'sitemobile_compatible' =>true,
 
    //HOOKS ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitemember_Plugin_Core'
        ),
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Sitemember_Plugin_Core',
        ),
        array(
            'event' => 'onUserUpdateAfter',
            'resource' => 'Sitemember_Plugin_Core',
        ),
        array(
            'event' => 'onUserDeleteAfter',
            'resource' => 'Sitemember_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitemember_profilemap',
        'sitemember_ratingparam',
        'sitemember_review',
        'sitemember_compliment_category'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitemember_userspecific' => array(
            'route' => 'member/:action/:user_id/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'location',
                'action' => 'edit-location',
            ),
            'reqs' => array(
                'action' => '(edit-location|edit-address)',
                'user_id' => '\d+',
            )
        ),
        'sitemember_edituserspecific' => array(
            'route' => 'member/location/edit-location/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'location',
                'action' => 'edit-location',
            ),
        ),
        'sitemember_userbylocation' => array(
            'route' => 'member/:action',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'location',
                'action' => 'userby-locations',
            ),
        ),
        'sitemember_user_general' => array(
            'route' => 'member/reviews/:action/:user_id/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'review',
                'action' => 'create',
            ),
            'reqs' => array(
                'action' => '(create|update|delete|email|helpful|top-rated|browse)',
                'user_id' => '\d+',
            )
        ),
        'sitemember_review_browse' => array(
            'route' => 'member/reviews/:action/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'review',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(top-rated|browse|most-recommended-members|most-reviewed-members|top-reviewers|top-raters)',
                'user_id' => '\d+',
            )
        ),
        'sitemember_review_memberreviews' => array(
            'route' => 'member/reviews/:action/:user_id/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'review',
                'action' => 'member-reviews',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            ),
        ),
        'sitemember_review_ownerreviews' => array(
            'route' => 'member/reviews/:action/:user_id/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'review',
                'action' => 'owner-reviews'
            ),
            'reqs' => array(
                'user_id' => '\d+'
            ),
        ),
        'sitemember_view_review' => array(
            'route' => 'member/review/:action/:review_id/:user_id/:slug/:tab/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'review',
                'action' => 'view',
                'slug' => '',
                'tab' => ''
            ),
            'reqs' => array(
                'review_id' => '\d+',
                'user_id' => '\d+'
            ),
        ),
         'sitemember_compliment_browse' => array(
            'route' => 'member/compliments/:action/*',
            'defaults' => array(
                'module' => 'sitemember',
                'controller' => 'compliment',
                'action' => 'index',
            ),
            'reqs' => array(
                
            )
        ),
    )
);
?>
