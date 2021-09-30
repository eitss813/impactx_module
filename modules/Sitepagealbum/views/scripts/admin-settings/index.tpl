<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		lightbox_activityfeed_edit("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.photolightbox.show', 1) ?>");
	});
	function show_activitymanual(option) {
		if($('sitepagealbum_photolightbox_activityedit-0').checked) {
			if(option == 1) {
				$('sitepagealbum_show_activitymanual-wrapper').style.display = 'none';
			}
			else {
				$('sitepagealbum_show_activitymanual-wrapper').style.display = 'block';
			}
		}
		else {
			$('sitepagealbum_show_activitymanual-wrapper').style.display = 'none';
		}
	}

	function lightbox_activityfeed_edit(option) {
		if($('sitepagealbum_photolightbox_activityedit-wrapper')) {
			if(option == 1) {
				$('sitepagealbum_photolightbox_activityedit-wrapper').style.display = 'block';
				show_activitymanual(0);
			}
			else {
				$('sitepagealbum_photolightbox_activityedit-wrapper').style.display = 'none';
				show_activitymanual(1);
			}
		}
	}
</script>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Albums Extension') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

<div class='clr sitepage_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>