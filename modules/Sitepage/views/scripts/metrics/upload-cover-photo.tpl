<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profilepicture.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css'); ?>

<div class="upload_cover_photo_popup">
	<div id="form_photo_cover" <?php if($this->status): ?>class="dnone"<?php endif; ?>>
		<?php echo $this->form->setAttrib('class', 'upload_cover_photo_form')->render($this) ?>
		<button name="sitepage_cancel" id="sitepage_cancel" type="button" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel"); ?></button>
	</div>
	<div id="loading_content" <?php if(!$this->status): ?>class="dnone"<?php endif; ?>>
		<div class="seaocore_content_loader"></div>
	</div>
</div>
<?php if($this->status):?>
<script type="text/javascript">
  parent.document.seaoCoverPhoto.getCoverPhoto(1);
</script>
<?php else:?>
<script type="text/javascript">
  function uploadPhoto(){
    document.getElementById('cover_photo_form').submit();
    document.getElementById('form_photo_cover').addClass('dnone');
    document.getElementById('loading_content').removeClass('dnone');
  }
</script>
<?php endif; ?>


