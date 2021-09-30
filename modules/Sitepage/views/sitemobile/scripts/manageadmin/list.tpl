<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: listowners.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepage/externals/styles/style_sitepage_dashboard.css');
?>
<form method="post" action="<?php echo $this->url(array('action'=>'list'));?>" class="global_form global_form_popup">
	<div>
		<div>
			<h3> <?php echo $this->translate('Manage Featured Page Admins'); ?> </h3>
			<p><?php echo $this->translate("Below you can select / unselect page admins as featured.") ?></p>
			<div class="sitepage_featuredadmins_add_list">
				<table style="display:block;">
					<?php foreach($this->owners as $item) : ?>
						<tr>
							<td><input id='<?php echo $item->user_id ?>' value='<?php echo $item->featured; ?>' name='<?php echo $item->user_id; ?>' type='checkbox' class='checkbox' <?php if(!empty($item->featured)) {echo "checked"; } ?> /> </td>
							<td><?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>	</td>
							<td><span><?php echo $item->getOwner()->getTitle()?></span> </td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>	
			<div class='buttons'>
				<button type='submit'><?php echo $this->translate('Save'); ?></button>
				 <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
			</div>
		</div>
	</div>
</form>