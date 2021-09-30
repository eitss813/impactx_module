<?php
$domain_name = @base64_encode($_SERVER['HTTP_HOST']);
$licensekey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.licensekey');
$licensekey = @base64_encode($licensekey);

$sesdomainauth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.sesdomainauth'); 
$seslkeyauth = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.seslkeyauth');

if(($domain_name == $sesdomainauth) && ($licensekey == $seslkeyauth)) {
	Zend_Registry::set('sesmultipleform_banner', 1);
	Zend_Registry::set('sesmultipleform_forms', 1);
	Zend_Registry::set('sesmultipleform_location', 1);
	Zend_Registry::set('sesmultipleform_popup', 1);
} else {
	Zend_Registry::set('sesmultipleform_banner', 0);
	Zend_Registry::set('sesmultipleform_forms', 0);
	Zend_Registry::set('sesmultipleform_location', 0);
	Zend_Registry::set('sesmultipleform_popup', 0);
}
