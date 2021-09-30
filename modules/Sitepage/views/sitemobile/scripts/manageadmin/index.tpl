<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manageadmins.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
  var manage_admin_formsubmit = 1;
</script>
<script type="text/javascript">
  var viewer_id = '<?php echo  $this->viewer_id; ?>';
  var url = '<?php  echo $this->url(array(), 'sitepage_general', true) ?>';
</script>

<?php if (empty($this->is_ajax)) : ?>
	<?php //include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="layout_middle">
		<?php //include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
		<div class="sitepage_edit_content">
			<div class="sitepage_edit_header">
				<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
			</div>
		  <div id="show_tab_content">
<?php endif; ?> 

		<div class="sitepage_form">
			<div>
				<div>
					<div class="sitepage_manageadmins">
						<h3> <?php echo $this->translate('Manage Page Admins'); ?> </h3>
						<p class="form-description"><?php echo $this->translate("Below you can see all the admins who can administer and manage your page, like you can do. You can add new members as admins of this page and remove any existing ones. Note that admins selected by you for this page will get complete authority like you to manage this page, including deleting it. Thus you should be specific in selecting them.") ?></p>
						<br />
						<?php foreach ($this->manageHistories as $item):?>
							<div id='<?php echo $item->manageadmin_id ?>_page_main'  class='sitepage_manageadmins_list'>
								<div class='sitepage_manageadmins_thumb' id='<?php echo $item->manageadmin_id ?>_pagethumb'>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
								</div> 
								<div id='<?php echo $item->manageadmin_id ?>_page' class="sitepage_manageadmins_detail">
									<div class="sitepage_manageadmins_cancel">
			             <?php $url = $this->url(array('action' => 'delete'), 'sitepage_manageadmins', true);?>
										<?php if ( $this->owner_id != $item->user_id ) :?>
											<a href="javascript:void(0);" onclick="manageinfo('<?php echo $item->manageadmin_id?>',
'<?php echo $item->getOwner()->getIdentity()?>', '<?php echo $url;?>', '<?php echo $this->page_id ?>')";
><?php echo $this->translate('Remove');?></a>
                    <?php endif;?>
									</div>
									<span><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php  $item = count($this->paginator) ?>
					<input type="hidden" id='count_div' value='<?php echo $item ?>' />
					<form id='video_selected' method='post' class="global_form mtop10" action='<?php echo $this->url(array('action' => 'index', 'page_id' => $this->page_id), 'sitepage_manageadmins') ?>'>
						<div class="fleft">
							<div>
								<?php if (!empty($this->message)): ?>
								<div class="tip">
									<span>
										<?php echo $this->message; ?>
									</span>
								</div>
								<?php  endif;?>
								<div class="sitepage_manageadmins_input">
								<?php echo $this->translate("Start typing the name of the member...") ?> <br />	
									<input type="text" id="searchtext" name="searchtext" value="" />
									<input type="hidden" id="user_id" name="user_id" />
								</div>
								<div class="sitepage_manageadmins_button">	
									<button type="submit"  name="submit"><?php echo $this->translate("Add as Admin") ?></button>
								</div>	
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<br />	
		<div id="show_tab_content_child">
		</div>
<?php if (empty($this->is_ajax)) : ?>
		  </div>
	  </div>
  </div>
<?php endif; ?> 	


<style type="text/css">
.global_form > div > div{background:none;border:none;padding:0px;}
</style>