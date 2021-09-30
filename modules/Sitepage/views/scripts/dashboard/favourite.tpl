<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: favourite.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css');
?>
<form method="post" class="global_form_popup sitepage_addremove_fav_popup_wrapper">
	<div class="sitepage_addremove_fav_popup_title"><?php echo $this->translate('Link ') . $this->sitepage->title. $this->translate("  to your Page:") ?></div>
		<div class="sitepage_addremove_fav_popup">
			<div class="sitepage_addremove_fav_popup_img">
				<?php echo  $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage), array('target' => '_blank')); ?>
			</div>
			<div class="sitepage_addremove_fav_popup_detail">
			<b> <?php  echo $this->translate('Select your Page to be linked to ') . $this->sitepage->title . '.' ?> </b>
			<select name="page_id" id="free_packageslist" >
				<option value = "" ></option>
				<?php foreach ($this->userListings as $package) { ?>
					<option value='<?php echo $package['page_id']; ?>' ><?php echo $package['title'] ?></option>
				<?php } ?>
			</select>
			<p>
				<!--<input type="hidden" name="confirm" value="<?php //echo $this->_id ?>"/>-->
				<button type='submit'><?php echo $this->translate("Link") ?></button> <?php echo $this->translate("or") ?>
				<a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
				<!-- <button onclick='javascript:parent.Smoothbox.close()' ><?php //echo $this->translate("Cancel") ?></button>-->
			</p>
		</div>
	</div>
</form>
<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>