<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: common_style_css.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup'))) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business_group.css');
	}
	elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business.css');
	} 
	elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_group.css');

	}
  else {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
  }
?>