<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manageExtensions.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.isActivate', null)): ?>
	<div class="seaocore_manage_extensions">
		<span class="bold fright">
			<a href='<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'extension'), 'admin_default', true) ?>' class="bold"><?php echo $this->translate('Go To Manage Extensions') ?></a>
		</span>
	</div>
	<br />
<?php endif; ?>
