<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Videos'),
        'description' => $view->translate('Forms the Videos tab of your Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.profile-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Commented Videos'),
        'description' => $view->translate("Displays list of your Page's most commented videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.comment-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Recent Videos'),
        'description' => $view->translate("Displays list of your Page's most recent videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.recent-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Recent Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Top Rated Videos'),
        'description' => $view->translate("Displays list of your Page's top rated videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.rate-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Top Rated Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Liked Videos'),
        'description' => $view->translate("Displays list of your Page's most liked videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.like-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Featured Videos'),
        'description' => $view->translate("Displays list of your Page's featured videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.featurelist-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Featured Page Videos'),
            'titleCount' => true,
        ),
    ),

    array(
        'title' => $view->translate('Page Profile Highlighted Videos'),
        'description' => $view->translate("Displays list of your Page's highlighted videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.highlightelist-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Highlighted Page Videos'),
            'titleCount' => true,
        ),
    ),

    array(
        'title' => $view->translate('Page Profile Most Viewed Videos'),
        'description' => $view->translate("Displays list of your Page's most viewed videos."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.view-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Viewed Videos'),
            'titleCount' => true,
        ),
    ),
)
?>