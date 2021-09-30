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
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
	<?php foreach ($this->sitepages as $sitepage): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon'), array('title' => $sitepage->getTitle())) ?>
			<div class='sitepage_sidebar_list_info'>
				<div class='sitepage_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle()), array('title' => $sitepage->getTitle())) ?>
				</div>
				<?php if ($this->statistics): ?>
					<?php if(in_array('likeCount', $this->statistics) || in_array('followCount', $this->statistics)) : ?>
						<div class="seaocore_browse_list_info_date">
							<?php if(in_array('likeCount', $this->statistics)): ?>
								<?php echo $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) ?><?php endif; ?><?php if(in_array('likeCount', $this->statistics) && in_array('followCount', $this->statistics)) : ?>, <?php endif; ?><?php if(in_array('followCount', $this->statistics)): ?><?php echo $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) ?>	
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if(in_array('viewCount', $this->statistics) || in_array('memberCount', $this->statistics)) : ?>
						<div class="seaocore_browse_list_info_date">
							<?php if(in_array('viewCount', $this->statistics)): ?>
								<?php echo $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) ?><?php endif; ?><?php if(in_array('viewCount', $this->statistics) && in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>, <?php endif; ?><?php if(in_array('memberCount', $this->statistics)): ?><?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
									<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
									if ($sitepage->member_title && $memberTitle) : ?>
										<?php echo $sitepage->member_count . ' ' .  $sitepage->member_title; ?>
									<?php else : ?>
									<?php echo $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
									<?php endif; ?>
								<?php endif; ?>		
							<?php endif; ?>		
						</div>
					<?php endif; ?>	
					<?php if(in_array('commentCount', $this->statistics) || in_array('reviewCount', $this->statistics)) : ?>
						<div class="seaocore_browse_list_info_date">
							<?php if(in_array('commentCount', $this->statistics)): ?>
								<?php echo $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) ?><?php endif;?><?php if(in_array('commentCount', $this->statistics) && in_array('reviewCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')):?>, <?php endif; ?>			<?php if(in_array('reviewCount', $this->statistics)): ?>
								<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')): ?>
									<?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)) ?>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
