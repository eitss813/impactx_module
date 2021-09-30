<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: app.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_dashboard.css');
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<?php $defaultURL =  $this->layout()->staticBaseUrl. "application/modules/User/externals/images/nophoto_user_thumb_profile.png" ?>
<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
	<div class="generic_layout_container layout_core_content">
		<?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
		<div class="layout_middle">
			<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
			<?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Members', 'sectionDescription' => '')); ?>
			<div class="sitepage_edit_content">
				<div id="show_tab_content">
					<?php echo $this->content()->renderWidget("sitepagemember.profile-sitepagemembers", array()); ?>
				</div>
			</div>
			<?php if(count($this->pendingInvites) > 0): ?>
			<div class="external_members_list">
				<h2><b>External Members</b></h2>
				<br/>
				<?php foreach ($this->pendingInvites as $key => $item): ?>
				<div>
					<div class="seaocore_browse_list_photo_small">
						<a href="javascript:void(0);">
							<img src="<?php echo $defaultURL?>" alt="" class="external_thumb_profile">
						</a>
					</div>
					<div class='external_member_details'>
						<div class="external_member_cancel">
								<span class="external_link_wrap mright5">
									<a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'module' => 'sitepage', 'controller' => 'dashboard', 'action' => 'remove-external-member', 'page_id' => $item['page_id'] , 'invite_id' => $item['id'] ), 'sitepage_page_member', true)); ?>">
										<span><?php echo $this->translate("Remove Member"); ?></span>
									</a>
								</span>
						</div>
						<!-- <h3>
							<?php echo $item['recipient_name'] ?>
						</h3> -->
						<h2>
							<?php echo $item['recipient'] ?>
						</h2>
						<h2>
							<?php echo $this->translate('External Member'); ?>
						</h2>
						<?php if(!empty($item['page_role'])): ?>
						<h2>
							<?php
							$roles_id = json_decode($item['page_role']);
							$roleName = array();
							foreach($roles_id as $role_id) {
								 $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRoleName($role_id);
							}
							echo implode(', ', $roleName);
							?>
						</h2>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<style type="text/css">
	.external_members_list{
		display: flex;
		flex-direction: column;
	}
	.external_thumb_profile{
		border: none;
		max-height: 100%;
		max-width: 100px;
	}
	.external_member_cancel{
		float: right;
	}
	.global_form_popup.sitepage_add_members_popup{
		min-height: 250px;
		width: unset !important;
		height: 250px;
		overflow: auto;
	}
</style>