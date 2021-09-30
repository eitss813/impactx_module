<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStartP = "projects";
$routeStartS = "project";
$module = null;
$controller = null;
$action = null;
$getURL = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
    $module = $request->getModuleName();
    $action = $request->getActionName();
    $controller = $request->getControllerName();
    $getURL = $request->getRequestUri();
}
if (empty($request) || !($module == "default" && ( strpos( $getURL, '/install') !== false))) {
    $slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.slugplural', 'projects');
    $slug_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.slugsingular', 'project');
}
$routes = array(
    'sitecrowdfunding_extended' => array(
        'route' => $slug_plural . '/:controller/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'index',
            'action' => 'home',
        ),
        'reqs' => array(
            'controller' => '\D+',
            'action' => '\D+',
        )
    ),
    'sitecrowdfunding_project_member' => array(
        'route' => '/member/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'member',
        ),
        'reqs' => array(
            'action' => '(join|leave|invite-members|accept-member|reject-member|remove-external-member)',
        ),
    ),
    'sitecrowdfunding_project_general' => array(
        'route' => $slug_plural . '/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => 'pinboard|browse|index|tagscloud|get-search-projects|manage|create|map|get-link|compose|upload-photo',
        ),
    ),
    'sitecrowdfunding_specific' => array(
        'route' => $slug_plural . '/:action/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project',
            'action' => 'edit'
        ),
        'reqs' => array(
            'action' => '(edit|editvideos|delete|order|slide-show|video-edit|editlocation|editaddress|payment-info|upload-kyc)',
        ),
    ),
    'sitecrowdfunding_dashboard' => array(
        'route' => $slug_plural . '/dashboard/:action/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'dashboard',
            'action' => 'meta-detail'
        ),
        'reqs' => array(
            'action' => '(change-photo|remove-photo|meta-detail|video-edit|my-videos|overview|additional|about-you|upload-video|project-commissions|project-transactions|set-settings|project-settings)',
            'project_id' => '\d+',
        )
    ),
    'sitecrowdfunding_photoalbumupload' => array(
        'route' => $slug_plural . '/photo/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'photo',
            'action' => 'upload',
            'project_id' => '0',
        )
    ),
    'sitecrowdfunding_albumspecific' => array(
        'route' => $slug_plural . '/album/:action/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'album',
            'action' => 'editphotos',
        ),
        'reqs' => array(
            'action' => '(delete|edit|editphotos|upload|view)',
        ),
    ),
    'sitecrowdfunding_organizationspecific' => array(
        'route' => $slug_plural . '/organization/:action/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'organization',
            'action' => 'editorganizations',
        ),
        'reqs' => array(
            'action' => '(create|delete|edit|editorganizations|view)',
        ),
    ),
    'sitecrowdfunding_organizationdelete' => array(
        'route' => $slug_plural . '/organization/:action/:type/:org_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'organization',
            'action' => 'delete',
        ),
        'reqs' => array(
            'action' => '(delete)',
        ),
    ),
    'sitecrowdfunding_milestoneedit' => array(
        'route' => $slug_plural . '/milestone/:action/:milestone_id/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'milestone',
            'action' => 'edit-milestone',
        ),
        'reqs' => array(
            'action' => '(edit-milestone)',
        ),
    ),
    'sitecrowdfunding_externalfunding_edit' => array(
        'route' => $slug_plural . '/milestone/:action/:externalfunding_id/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'funding',
            'action' => 'edit-external-funding',
        ),
        'reqs' => array(
            'action' => '(edit-external-funding)',
        ),
    ),
    'sitecrowdfunding_image_specific' => array(
        'route' => $slug_singular . '/photo/view/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'photo',
            'action' => 'view',
        ),
        'reqs' => array(
            'action' => '(view|remove)',
        ),
    ),
    'sitecrowdfunding_photo_extended' => array(
        'route' => $slug_singular . '/photo/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'photo',
            'action' => 'edit',
        ),
        'reqs' => array(
            'action' => '\D+',
        )
    ),
    'sitecrowdfunding_general_category' => array(
        'route' => $slug_plural . '/category/:categoryname/:category_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'index',
            'action' => 'category-home',
        ),
        'reqs' => array(
            'category_id' => '\d+',
        ),
    ),
    'sitecrowdfunding_general_subcategory' => array(
        'route' => $slug_plural . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'index',
            'action' => 'category-home',
        ),
    ),
    'sitecrowdfunding_general_subsubcategory' => array(
        'route' => $slug_plural . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id/:subsubcategoryname/:subsubcategory_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'index',
            'action' => 'category-home',
        ),
    ),
    'sitecrowdfunding_entry_view' => array(
        'route' => $slug_singular . '/:slug/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project',
            'action' => 'view',
            'slug' => ''
        ),
        'reqs' => array(
            'project_id' => '\d+'
        )
    ),
    'sitecrowdfunding_session_payment' => array(
        'route' => $slug_plural . '/payment/sessionpayment/',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'package',
            'action' => 'payment',
        ),
    ),
    'sitecrowdfunding_package' => array(
        'route' => $slug_plural . '/package/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'package',
            'action' => 'index',
            'package' => 1,
        ),
        'reqs' => array(
            'action' => '(index|detail|update-package|update-confirmation|cancel)',
        ),
    ),
    'sitecrowdfunding_payment' => array(
        'route' => $slug_plural . '/payment/',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'payment',
            'action' => 'index',
        ),
    ),
    'sitecrowdfunding_manage_rewards' => array(
        'route' => $slug_plural . '/manage/rewards/:project_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'dashboard',
            'action' => 'manage',
        ),
        'reqs' => array(
            'project_id' => '\d+',
        ),
    ),
    'sitecrowdfunding_process_payment' => array(
        'route' => $slug_plural . '/payment/process',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'payment',
            'action' => 'process',
        ),
    ),
    'sitecrowdfunding_backer' => array(
        'route' => $slug_plural . '/backer/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'backer',
            'action' => 'checkout',
        ),
        'reqs' => array(
            'project_id' => '\d+',
            'action' => '(checkout|place-order|payment|reward-selection|donate-to-project|view|payment-to-me|your-bill)'
        )
    ),
    'sitecrowdfunding_general' => array(
        'route' => $slug_plural . '/:action/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(categories|index|sub-category|subsub-category|backers-faq|project-owner-faq)',
        ),
    ),
    'sitecrowdfunding_create' => array(
        'route' => $slug_plural. '/create-new/:action/',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project-create',
            'action' => 'step-zero',
        ),
    ),
    'sitecrowdfunding_create_with_page' => array(
        'route' => $slug_plural. '/create-new/:page_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project-create',
            'action' => 'step-zero',
        ),
    ),
    'sitecrowdfunding_create_with_page_and_initiative' => array(
        'route' => $slug_plural. '/create-new/page_id/:page_id/initiative_id/:initiative_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project-create',
            'action' => 'step-zero',
        ),
    ),
    'sitecrowdfunding_createspecific' => array(
        'route' => $slug_plural. '/create-new/:action/:project_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'project-create',
            'action' => 'step-two',
        ),
        'reqs' => array(
            'action' => '\D+',
        )
    ),

    'sitecrowdfunding_create_temp' => array(
        'route' => $slug_plural. '/create-new-temp/:action/:project_id',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'temp',
            'action' => 'step-seven',
        ),
        'reqs' => array(
            'action' => '\D+',
        )
    ),
    'sitecrowdfunding_initiative' => array(
        'route' => $slug_plural . '/initiative/:action/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'initiative',
            'action' => 'edit-initiative-answers',
        ),
        'reqs' => array(
            'action' => '\D+',
        ),
    ),
    'sitecrowdfunding_metric' => array(
        'route' => $slug_plural . '/metrics/:action/:project_id/*',
        'defaults' => array(
            'module' => 'sitecrowdfunding',
            'controller' => 'metric',
            'action' => 'list-metric',
        ),
        'reqs' => array(
            'action' => '\D+',
        ),
    ),
);
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitecrowdfunding',
        'version' => '4.10.5',
        'path' => 'application/modules/Sitecrowdfunding',
        'title' => 'Crowdfunding / Fundraising / Donations Plugin',
        'description' => 'Crowdfunding / Fundraising / Donations Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Sitecrowdfunding/settings/install.php',
            'class' => 'Sitecrowdfunding_Installer',
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
            0 => 'application/modules/Sitecrowdfunding',
            1 => 'application/modules/Sitecrowdfundingintegration',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitecrowdfunding.csv',
            1 => 'application/languages/en/sitecrowdfundingintegration.csv'
        ),
    ),
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitecrowdfunding_Plugin_Core'
        ),
//        array(
//            'event' => 'onVideoCreateAfter',
//            'resource' => 'Sitecrowdfunding_Plugin_Core',
//        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitecrowdfunding_Plugin_Core',
        ),
    ),
    // Compose
    'composer' => array(
        'event' => array(
            'script' => array('_composeProject.tpl', 'sitecrowdfunding'),
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitecrowdfunding_adminnote',
        'sitecrowdfunding_milestone',
        'sitecrowdfunding_page',
        'sitecrowdfunding_organization',
        'sitecrowdfunding_category',
        'sitecrowdfunding_project',
        'sitecrowdfunding_package',
        'sitecrowdfunding_photo',
        'sitecrowdfunding_album',
        'sitecrowdfunding_list_item',
        'sitecrowdfunding_list',
        'sitecrowdfunding_announcement',
        'sitecrowdfunding_region',
        'sitecrowdfunding_reward',
        'sitecrowdfunding_topic',
        'sitecrowdfunding_post',
        'sitecrowdfunding_gateway',
        'sitecrowdfunding_projectGateway',
        'sitecrowdfunding_backer',
        'sitecrowdfunding_transaction',
        'sitecrowdfunding_rewardshippinglocation',
        'sitecrowdfunding_paymentrequest',
        'sitecrowdfunding_projectbill',
        'sitecrowdfunding_page',
        'sitecrowdfunding_membership',
        'sitecrowdfunding_roles',
        'sitecrowdfunding_outcome',
        'sitecrowdfunding_output',
        'sitecrowdfunding_goal',
        'sitecrowdfunding_externalfunding',
        'sitecrowdfunding_sdggoal',
        'sitecrowdfunding_sdgtarget',
        'sitecrowdfunding_initiativeanswer'
    ),
    'routes' => $routes,
);
?>