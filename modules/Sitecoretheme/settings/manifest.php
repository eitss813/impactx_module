<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitecoretheme',
        'version' => '4.10.5p5',
        'path' => 'application/modules/Sitecoretheme',
        'title' => 'Versatile - Responsive Multi-Purpose Theme',
        'description' => 'Versatile - Responsive Multi-Purpose Theme',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'seao-sku' => 'seao-sitecoretheme',
        'callback' =>
        array(
            'path' => 'application/modules/Sitecoretheme/settings/install.php',
            'class' => 'Sitecoretheme_Installer',
        ),
        'dependencies' => array(
          array(
            'type' => 'module',
            'name' => 'core',
            'minVersion' => '4.10.3',
          ),
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
            0 => 'application/modules/Sitecoretheme',
            1 => 'application/themes/sitecoretheme',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitecoretheme.csv',
        ),
    ),
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitecoretheme_Plugin_Core'
        ),
        array(
            'event' => 'onRenderLayoutDefaultSimple',
            'resource' => 'Sitecoretheme_Plugin_Core',
        ),
    ),
    //Items ---------------------------------------------------------------------
    'items' => array(
        'sitecoretheme_image',
        'sitecoretheme_banner',
        'sitecoretheme_service',
        'sitecoretheme_highlight',
    )
);
?>