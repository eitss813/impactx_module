<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<ul class="sesblog_blog_listing sesbasic_bxs sesbasic_clearfix">
<?php foreach( $this->results as $review ): ?>
    <?php $reviewOwner = Engine_Api::_()->getItem('user', $review->owner_id);?>
   <li class="sesblog_grid sesblog_review_of_the_day sesbasic_clearfix sesbasic_bxs sesblog_grid_btns_wrap">
    <div class="sesblog_grid_thumb" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
      <?php $href = $reviewOwner->getHref(); ?>
      <?php 
          if($reviewOwner->photo_id ){
            $profileImg = $reviewOwner->getPhotoUrl('thumb.profile');
          } else {
            $profileImg = $this->baseUrl() . '/application/modules/User/externals/images/nophoto_user_thumb_profile.png';
          }
      ?>
      <a href="<?php echo $href; ?>" class="sesblog_thumb_img">
        <span class="floatL" style="background-image:url(<?php echo $profileImg; ?>);"></span>
      </a>
      <?php if(isset($this->featuredLabelActive)){ ?>
      <div class="sesblog_list_labels">
        <?php if(isset($this->featuredLabelActive) && $review->featured){ ?>
          <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
        <?php } ?>
         <?php if(isset($this->verifiedLabelActive) && $review->verified == 1){ ?>
           <p class="sesblog_label_verified"><?php echo $this->translate('Verified');?></p>
            <?php } ?>
      </div>
      <?php } ?>
         </div>
      <div class="sesblog_review_grid_thumb_cont">
      	<?php if(isset($this->titleActive) ){ ?>
          <div class="sesblog_grid_info_title">
            <?php if(strlen($review->getTitle()) > $this->title_truncation){ 
            $title = mb_substr($review->getTitle(),0,($this->title_truncation - 3)).'...';
            echo $this->htmlLink($review->getHref(),$title, array('class' => '', 'data-src' => $review->getGuid()) ) ?>
            <?php }else{ ?>
            <?php echo $this->htmlLink($review->getHref(),$review->getTitle(), array('class' => '', 'data-src' => $review->getGuid())) ?>
            <?php } ?>
          </div>
      	<?php } ?>
      </div>
		<div class="sesblog_review_grid_info sesbasic_clearfix clear">
			<?php $reviewCrator = Engine_Api::_()->getItem('user', $review->owner_id);?> 
			<?php $reviewTaker = Engine_Api::_()->getItem('sesblog_blog', $review->blog_id);?> 
			<div class="sesblog_review_grid_stat sesblog_sidebar_image_rounded sesbasic_text_light">
				<div class="sesblog_review_title_block">
				  <?php if(isset($this->byActive)):?>
						<p><?php echo 'Posted by '.$this->htmlLink($reviewCrator, $reviewCrator->getTitle());?></p>
					<?php endif;?>
					<p><?php echo $this->htmlLink($reviewTaker, $this->itemPhoto($reviewTaker, 'thumb.icon'), array('class' => 'sesblog_reviw_prof_img')); ?><?php echo 'For '.$this->htmlLink($reviewTaker, $reviewTaker->getTitle(), array('class' => 'ses_tooltip', 'data-src' => $reviewTaker->getGuid()));?></p>
					<div class="sesblog_list_stats sesblog_review_grid_stat clear">
						<?php if(isset($this->likeActive) && isset($review->like_count)) { ?>
						<span title="<?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>"><i class="sesbasic_icon_like_o sesbasic_text_light"></i><?php echo $review->like_count; ?></span>
						<?php } ?>
						<?php if(isset($this->viewActive) && isset($review->view_count)) { ?>
						<span title="<?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count))?>"><i class="sesbasic_icon_view sesbasic_text_light"></i><?php echo $review->view_count; ?></span>
						<?php } ?>
						<?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && $this->ratingActive){
						echo '<span title="'.$this->translate(array('%s rating', '%s ratings', $review->rating), $this->locale()->toNumber($review->rating)).'"><i class="far fa-star sesbasic_text_light"></i>'.round($review->rating,1).'/5'. '</span>';
						} ?>
					</div>
				</div>
				<?php if(isset($this->ratingActive)): ?>
					<div class="sesblog_list_rating sesblog_review_sidebar_list_stat">
						<?php $ratingCount = $review->rating; $x=0; ?>
						<?php if( $ratingCount > 0 ): ?>
							<?php for( $x=1; $x<=$ratingCount; $x++ ): ?>
								<span id="" class="sesblog_rating_star_small"></span>
							<?php endfor; ?>
							<?php if( (round($ratingCount) - $ratingCount) > 0){ ?>
								<span class="sesblog_rating_star_small sesblog_rating_star_small_half"></span>
							<?php }else{ $x = $x - 1;} ?>
							<?php if($x < 5){ 
								for($j = $x ; $j < 5;$j++){ ?>
								<span class="sesblog_rating_star_small sesblog_rating_star_disable"></span>
								<?php }   	
							} ?>
						<?php endif; ?>
					</div>
				<?php endif ?> 
		  </div>
      <?php if($this->descriptionActive && $review->getDescription()):?>
				<div class="sesblog_review_sidebar_list_body clear">
					<?php if(strlen($this->string()->stripTags($review->getDescription())) > $this->description_truncation):?>
						<?php $description = mb_substr($this->string()->stripTags($review->getDescription()),0,($this->description_truncation-3)).'...';?>
						<?php echo $description;?>
					<?php else: ?>
						<?php  echo $this->string()->stripTags($review->getDescription()); ?>
					<?php endif; ?>
				</div>
			<?php endif;?>
    </div>
     <?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive)) {
      $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $review->getHref()); ?>
        <div class="sesblog_list_share_btns"> 
          <?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
            
          <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $review, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

          <?php }  ?>			
        </div>
      <?php } ?>
  </li>
  <?php endforeach; ?>
</ul>
