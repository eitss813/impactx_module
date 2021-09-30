<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagediscussion
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitepagediscussion',
        'version' => '4.9.0',
        'path' => 'application/modules/Sitepagediscussion',
        'title' => 'Directory / Pages - Discussions Extension',
        'description' => 'Directory / Pages - Discussions Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Tuesday, 17 Aug 2010 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'callback' =>
        array(
            'path' => 'application/modules/Sitepagediscussion/settings/install.php',
            'class' => 'Sitepagediscussion_Installer',
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
            0 => 'application/modules/Sitepagediscussion',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitepagediscussion.csv',
        ),
    ),
	// COMPATIBLE WITH MOBILE / TABLET PLUGIN --------------------------------------------------------------------
	'sitemobile_compatible' =>true,
);
?>