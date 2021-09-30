<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="generic_layout_container layout_middle">
	<div class="generic_layout_container layout_core_content">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
		<div class="layout_middle">
			<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
			<div class="sitepage_edit_content">
				<div class="sitepage_edit_header">
					<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
					<?php if($this->sitepage->draft == 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $this->sitepage->page_id), $this->translate('Mark As Live'), array('class'=>'smoothbox')) ?>
					<h3><?php echo $this->translate('Dashboard: ') . $this->sitepage->title; ?></h3>
				</div>
				<div id="show_tab_content">
					<h3> <?php echo $this->translate('Manage Linked Pages'); ?> </h3>
					<p class="form-description"><?php echo $this->translate("Below, you can manage linked pages") ?></p>
					<br />
					<div class="sitepage_getstarted_btn">
						<?php echo $this->htmlLink(array('action' => 'favourite', 'page_id' => $this->page_id),
							$this->translate('Link New Page'),
							array('class' => 'smoothbox')) ?>
						</div>
						<?php if (count($this->userListings) > 0) : ?>
							<ul class="sitepage_sidebar_list">
								<?php  foreach ($this->userListings as $sitepage): ?>
									<div class="">
										<li>
											<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id_for, $sitepage->owner_id,$sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon'),array('title' => $sitepage->getTitle())) ?>
											<div class='sitepage_sidebar_list_info'>
												<div class='sitepage_sidebar_list_title'>
													<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id_for, $sitepage->owner_id,$sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle()), array('title' => $sitepage->getTitle())) ?>
												</div>
												<div class="sitepage_manage_announcements_option">
												<a href='<?php echo  $this->url(array('action' => 'unlink-page', 'page_id' => $sitepage->page_id, 'page_id_for' => $sitepage->page_id_for));?>', '<?php echo $this->page_id ?>')"; class="buttonlink seaocore_icon_delete smoothbox" ><?php echo $this->translate('Remove');?></a>
												</div>
											</div>
										</li>
									</div>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<br />
							<div class="tip">
								<span><?php echo $this->translate('No Linking have been done for this page yet.'); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
