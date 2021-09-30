<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: dismiss_message.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<h2><?php echo $this->translate("Advanced Blog Plugin") ?></h2>
<div class="sesbasic_nav_btns">
  <a href="<?php echo $this->url(array('module' => 'sesblog', 'controller' => 'settings', 'action' => 'support'),'admin_default',true); ?>" class="help-btn">Help & Support</a>
</div>
<?php if(!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('sesbasic')) { ?>
  	<div class="tip"><span><?php echo $this->translate("This plugin requires \"<a href='https://socialnetworking.solutions/social-engine/socialenginesolutions-basic-required-plugin/' target='_blank'>SocialNetworking.Solutions (SNS) Basic Required Plugin </a>\" to be installed and enabled on your website for Location and various other featrures to work. Please get the plugin from <a href='https://socialnetworking.solutions/social-engine/socialenginesolutions-basic-required-plugin/' target='_blank'>here</a> to install and enable on your site."); ?></span></div>
  <?php } ?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.pluginactivated')): ?>
  <?php if( count($this->navigation) ): ?>
    <div class='sesbasic-admin-navgation'>
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
