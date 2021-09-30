<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $sitepage = $this->sitepage; ?>

<li class="seaocore_carousel_content_item_wrapper b_medium" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
  <div class="seaocore_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;">
    <center>
        <a href="<?php echo $sitepage->getHref() ?>" class="seaocore_carousel_thumb" title="<?php echo $sitepage->getTitle()?>">
        <?php echo $this->itemBackgroundPhoto($sitepage, 'thumb.normal', $sitepage->getTitle(), array(
	                'tag' => 'span',
	             )); ?>
      </a>
    </center>
    <div class="seaocore_carousel_title">
      <?php echo $this->htmlLink($sitepage->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitepage->getTitle(), $this->title_truncation), array('title' => $sitepage->getTitle())) ?>
    </div>

    <div class="seaocore_carousel_cnt clr">
      <div class="seaocore_txt_light"> 
        <a href="<?php echo $sitepage->getCategory()->getHref() ?>"> 
          <?php echo $sitepage->getCategory()->getTitle(true) ?>
        </a>
      </div>
      
		<?php if ($this->statistics): ?>
			<?php if(in_array('likeCount', $this->statistics) || in_array('followCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('likeCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) ?>
					<?php endif; ?>
					<?php if(in_array('likeCount', $this->statistics) && in_array('followCount', $this->statistics)) : ?> - <?php endif; ?>
					<?php if(in_array('followCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) ?>	
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if(in_array('viewCount', $this->statistics) || in_array('memberCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('viewCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) ?>
					<?php endif; ?>
					<?php if(in_array('viewCount', $this->statistics) && in_array('memberCount', $this->statistics)) : ?>  - <?php endif; ?>
					<?php if(in_array('memberCount', $this->statistics)): ?>
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
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
				<div class="seaocore_txt_light">
					<?php if(in_array('commentCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) ?>
					<?php endif; ?>
					<?php if(in_array('commentCount', $this->statistics) && in_array('reviewCount', $this->statistics)) : ?> - <?php endif; ?>
					<?php if(in_array('reviewCount', $this->statistics)): ?>
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')): ?>
							<?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)) ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
    <?php endif; ?>
      
			<?php if(($this->sponsoredIcon && $sitepage->sponsored) || ($this->featuredIcon && $sitepage->featured) || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && $sitepage->rating)): ?>
				<div class="seaocore_carousel_grid_view_list_btm b_medium">
				
					<?php if((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && $sitepage->rating)): ?>
						<?php 
							$currentRatingValue = $sitepage->rating;
							$difference = $currentRatingValue- (int)$currentRatingValue;
							if($difference < .5) {
								$finalRatingValue = (int)$currentRatingValue;
							}
							else {
								$finalRatingValue = (int)$currentRatingValue + .5;
							}	
						?>
						<span class="list_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
							<?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
							<span class="rating_star_generic rating_star" ></span>
							<?php endfor; ?>
							<?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half" ></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
						
					<span class="fright">
						<?php if ($sitepage->sponsored == 1 && $this->sponsoredIcon): ?>
							<i title="<?php echo $this->translate('Sponsored');?>" class="seaocore_icon seaocore_icon_sponsored"></i>
						<?php endif; ?>
						<?php if ($sitepage->featured == 1 && $this->featuredIcon): ?>
							<i title="<?php echo $this->translate('Featured');?>" class="seaocore_icon seaocore_icon_featured"></i>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
    </div>  
  </div>
</li>
