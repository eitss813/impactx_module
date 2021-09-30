<?php
$domain_name = @base64_encode(str_replace(array('http://','https://','www.'),array('','',''),$_SERVER['HTTP_HOST']));
$licensekey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.licensekey');
$licensekey = @base64_encode($licensekey);

$sesdomainauth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.sesdomainauth');
$seslkeyauth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.seslkeyauth');

if(($domain_name == $sesdomainauth) && ($licensekey == $seslkeyauth)) {
		Zend_Registry::set('sesblog_browssesblogs', 1);
		Zend_Registry::set('sesblog_reviews', 1);
		Zend_Registry::set('sesblog_locations', 1);
		Zend_Registry::set('sesblog_topbloggers', 1);
		Zend_Registry::set('sesblog_tabbed', 1);
		Zend_Registry::set('sesblog_photos', 1);
		Zend_Registry::set('sesblog_profilsesblogs', 1);
		Zend_Registry::set('sesblog_favbutton', 1);
		Zend_Registry::set('sesblog_recentlyview', 1);
		Zend_Registry::set('sesblog_create', 1);
		Zend_Registry::set('sesblog_edit', 1);
		Zend_Registry::set('sesblog_category', 1);
} else {
    Zend_Registry::set('sesblog_browssesblogs', 0);
		Zend_Registry::set('sesblog_reviews', 0);
		Zend_Registry::set('sesblog_locations', 0);
		Zend_Registry::set('sesblog_topbloggers', 0);
		Zend_Registry::set('sesblog_tabbed', 0);
		Zend_Registry::set('sesblog_photos', 0);
		Zend_Registry::set('sesblog_profilsesblogs', 0);
		Zend_Registry::set('sesblog_favbutton', 0);
		Zend_Registry::set('sesblog_recentlyview', 0);
		Zend_Registry::set('sesblog_create', 0);
		Zend_Registry::set('sesblog_edit', 0);
		Zend_Registry::set('sesblog_category', 0);
}