<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminBackupsettingsController.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */

$session = new Zend_Session_Namespace();
if(!empty($session->show_hide_ads)) {
	if($session->page_communityad_integration == 1)
		$communityad_integration = $page_communityad_integration = $session->page_communityad_integration; 
	else
	 $communityad_integration = $page_communityad_integration = 0;	
}
else {
 $communityad_integration = $page_communityad_integration = 1;
}
  
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
