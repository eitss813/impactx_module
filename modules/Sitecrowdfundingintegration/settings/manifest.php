<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitecrowdfundingintegration',
    'version' => '-',
    'path' => 'application/modules/Sitecrowdfundingintegration',
    'title' => '<i><span style="color:#999999">Crowdfunding - Pages, Businesses, Events, Groups, Multiple Listing Types, etc. Extension</span></i>',
    'description' => '<i><span style="color:#999999">Crowdfunding - Pages, Businesses, Events, Groups, Multiple Listing Types, etc. Extension</span></i>',
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Sitecrowdfundingintegration/settings/install.php',
      'class' => 'Sitecrowdfundingintegration_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Sitecrowdfundingintegration',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/sitecrowdfundingintegration.csv',
    ),
  ),
); ?>