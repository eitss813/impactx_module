<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php 
    if( !empty($this->supportingModules)) :
			foreach( $this->supportingModules as $modName ) {
				echo "<div class='tip'><span>" . $this->translate("You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Directory / Pages - Page Members Extension.", ucfirst($modName)) . "</span></div>";
			}
    endif;
    
    ?>
  </div>
</div>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
//   var display_msg=0;
  window.addEvent('domready', function() {
		showInviteOption('<?php echo $coreSettings->getSetting('pagemember.invite.option', 1) ?>');
		showApprovalOption('<?php echo $coreSettings->getSetting('pagemember.member.approval.option', 1) ?>');
    showAnnouncements('<?php echo $coreSettings->getSetting('sitepagemember.tinymceditor', 1) ?>');
    notificationEmail('none');
  });

  function showInviteOption(option) {
    if($('pagemember_invite_automatically-wrapper')) {
      if(option == 1) {
        $('pagemember_invite_automatically-wrapper').style.display='none';
      } else{
        $('pagemember_invite_automatically-wrapper').style.display='block';
      }
    }
  }
  
  function showApprovalOption(option) {
    if($('pagemember_member_approval_automatically-wrapper')) {
      if(option == 1) {
        $('pagemember_member_approval_automatically-wrapper').style.display='none';
      } else{
        $('pagemember_member_approval_automatically-wrapper').style.display='block';
      }
    }
  }

 function showAnnouncements(option) {
    if($('sitepagemember_tinymceditor-wrapper')) {
      if(option == 1) {
        $('sitepagemember_tinymceditor-wrapper').style.display='block';
      } else{
        $('sitepagemember_tinymceditor-wrapper').style.display='none';
      }
    }
 }
 
  function showGroupSettings(option) {
		if($('sitepagemember_group_settings').checked == true) {
			notificationEmail('block');
		} else {
			notificationEmail('none');
		}
 }
  function notificationEmail(display) {
		if($('sitepagemember_settings-wrapper')) {
			$('sitepagemember_settings-wrapper').style.display=display;
		}
		
		if($('sitepagemember_settingsforlayout-wrapper')) {
			$('sitepagemember_settingsforlayout-wrapper').style.display=display;
		}

  }
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Page Members Extension') ?></h2>


<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
</div>
<?php endif; ?>

<?php if(!$this->hasLanguageDirectoryPermissions):?>
<div class="seaocore_tip">
  <span>
    <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for  change the pharse pages and page." ?>
  </span>
</div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>