<?php
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

return array(
    array(
        'title' => $view->translate('Connect to Social Sites'),
        'description' => $view->translate('Connect your profile, with various social sites.'),
        'category' => $view->translate('Social Connect & Profile Sync Extension'),
        'type' => 'widget',
        'name' => 'siteloginconnect.connect-social-login',
        'adminForm' => array(
        					"elements" => array(
        									array(
											    'MultiCheckbox',
											    'enabled_socialsites',
											    array(
											        'label' => 'Choose social media sites you want to show to users so they can connect to those sites.',
											        'multiOptions' => array(
											        	'facebook'=>'Facebook',
											        	'linkedin'=>'LinkedIn',
											        	'twitter'=>'Twitter',
											        	'instagram'=>'Instagram',
											        	'google'=>'Google',
											        	'yahoo'=>'Yahoo', 
											        	'outlook'=>'Outlook',
											        	'pinterest'=>'Pinterest',
            											'flickr'=>'Flickr',
            											'vk'=>'Vkontakte'
            											),
											    ),
        								),
        				)
    	)
	),
    array(
        'title' => $view->translate('Sync Profile Data with Social Sites'),
        'description' => $view->translate('Fetch your profile data, from social sites'),
        'category' => $view->translate('Social Connect & Profile Sync Extension'),
        'type' => 'widget',
        'name' => 'siteloginconnect.fetch-social-data',
        'adminForm' => array(
        					"elements" => array(
        									array(
											    'MultiCheckbox',
											    'enabled_socialsites',
											    array(
											        'label' => 'Choose social media sites you want to show to users for profile synchronization.',
											        'multiOptions' => array(
											        	'facebook'=>'Facebook',
											        	'linkedin'=>'LinkedIn',
											        	'twitter'=>'Twitter',
											        	'instagram'=>'Instagram',
											        	
											        	),
											    ),
        								),
        				)
    	)
	),
);
?>