<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featuredowners.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href;
		Smoothbox.open(Obj_Url);
	}
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
	<?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<div class="layout_middle">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
	<?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Featured Admins', 'sectionDescription' => '')); ?>
	<div class="sitepage_edit_content">
 <div id="show_tab_content">
<?php endif; ?> 

		<div class="global_form">
			<div>
				<div>
					<h3><?php echo $this->translate('Featured Page Admins'); ?></h3>
					<p class="form-description">
						<?php echo $this->translate('Below you can see all the featured admins of this page. Featured admins are shown on the page profile.') ?>
					</p>
					<?php $featuredhistories_array = $this->featuredhistories->toarray();
						if(!empty($featuredhistories_array)) :
							$count = count($featuredhistories_array);
							echo '<div class="sitepage_featuredadmins_count">' . $this->translate(array('%s featured page admin', '%s featured page admins', $count), $this->locale()->toNumber($count)); ?></div>
							
						<div class='sitepage_featuredadmins_list'>
							<?php foreach ($this->featuredhistories as $item):?>
								<div class='sitepage_featuredadmins_thumb' id='<?php echo $item->manageadmin_id ?>_pagethumb'>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="sitepage_featuredadmins_add">
							<?php echo $this->htmlLink(array('route' => 'sitepage_manageadmins', 'action' => 'list','page_id' => $this->sitepage->page_id), $this->translate('Manage Featured Page Admins'), array('onclick' => 'owner(this);return false',)) ?>
						</div>
					<?php else : ?>
						<div class="tip">
							<span>
								<?php echo $this->translate("No featured admins have been added for this page yet."); ?>
							</span>
						</div>
						<div class="sitepage_featuredadmins_add">
							<?php echo $this->htmlLink(array('route' => 'sitepage_manageadmins', 'action' => 'list', 'page_id' => $this->sitepage->page_id), $this->translate('Add Featured Page Admins'), array('onclick' => 'owner(this);return false',)) ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			
		<?php if (empty($this->is_ajax)) : ?>
		</div>
	</div>
  </div>
    </div>
  </div>
<?php endif; ?>