<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _reviewsidebarwidgetData.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php foreach( $this->results as $review ): ?>
<?php $reviewOwner = Engine_Api::_()->getItem('user', $review->owner_id);?>
<?php if($this->view_type == 'list'){ ?>
  <li class="sesblog_review_sidebar_list  <?php if($this->image_type == 'rounded'):?>sesblog_sidebar_image_rounded<?php endif;?> sesbasic_clearfix">
    <?php echo $this->htmlLink($reviewOwner, $this->itemPhoto($reviewOwner, 'thumb.icon')); ?>
    <div class="sesblog_review_sidebar_list_info">
    	<?php  if(isset($this->titleActive)){ ?>
      	<div class="sesblog_review_sidebar_list_title">
          <?php if(strlen($review->getTitle()) > $this->title_truncation){
          	$title = mb_substr($review->getTitle(),0,$this->title_truncation).'...';
						echo $this->htmlLink($review->getHref(),$title,array('title'=>$review->getTitle()));
          } else { ?>
          <?php echo $this->htmlLink($review->getHref(),$review->getTitle()) ?>
        	<?php } ?>
      	</div>
      <?php } ?>
        <?php $reviewCrator = Engine_Api::_()->getItem('user', $review->owner_id);?>
        <?php $reviewTaker = Engine_Api::_()->getItem('sesblog_blog', $review->blog_id);?> 
        <?php if($reviewCrator && $reviewTaker && isset($this->byActive)):?>
          <div class="sesblog_review_sidebar_list_stat sesbasic_text_light">
            <?php echo 'by '.$this->htmlLink($reviewCrator, $reviewCrator->getTitle());?>
            <?php echo 'For '.$this->htmlLink($reviewTaker, $reviewTaker->getTitle(), array('class' => 'ses_tooltip', 'data-src' => $reviewTaker->getGuid()));?>	
          </div>
      <?php endif;?>  
			<div class="sesblog_list_stats sesblog_review_sidebar_list_stat">
        <?php if(isset($this->likeActive) && isset($review->like_count)) { ?>
        	<span title="<?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>"><i class="fa fa-thumbs-up sesbasic_text_light"></i><?php echo $review->like_count; ?></span>
        <?php } ?>
        <?php if(isset($this->viewActive) && isset($review->view_count)) { ?>
        	<span title="<?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count))?>"><i class="fa fa-eye sesbasic_text_light"></i><?php echo $review->view_count; ?></span>
         <?php } ?>
        <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && $this->ratingActive){
          echo '<span title="'.$this->translate(array('%s rating', '%s ratings', $review->rating), $this->locale()->toNumber($review->rating)).'"><i class="fa fa-star sesbasic_text_light"></i>'.round($review->rating,1).'/5'. '</span>';
        } ?>
      </div>
      <?php if(isset($this->ratingActive)): ?>
        <div class="sesblog_list_rating sesblog_review_sidebar_list_stat clear">
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
        <?php if(strlen($this->string()->stripTags($review->getDescription())) > $this->review_description_truncation){
          $description = mb_substr($this->string()->stripTags($review->getDescription()),0,($this->review_description_truncation-3)).'...';
          echo $description;
        } else { ?>
          <?php  echo $this->string()->stripTags($review->getDescription()); ?>
        <?php } ?>
      </div>
    <?php endif;?>
     <?php if(isset($this->featuredLabelActive) || ($review->featured)  ||  ($this->verifiedLabelActive) || ($review->verified)):?>
        <div class="sesblog_review_sidebar_featured_list">
          <?php if(isset($this->featuredLabelActive) && $review->featured):?>
            <p class="featured"><?php echo $this->translate('Featured');?></p>
          <?php endif;?>
          <?php if(isset($this->verifiedLabelActive) && $review->verified):?>
              <p class="verified"><?php echo $this->translate('Verified');?></p>
          <?php endif;?>
        </div>
    <?php endif;?>
  </li>
<?php }else{ ?>
  <li class="sesblog_review_grid sesbasic_clearfix sesbasic_bxs sesblog_grid_btns_wrap">
    <div class="sesblog_review_grid_thumb floatL" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
      <?php $href = $reviewOwner->getHref();$imageURL = $reviewOwner->getPhotoUrl('thumb.profile');?>
      <a href="<?php echo $href; ?>" class="sesblog_review_grid_thumb_img floatL">
        <span class="floatL" style="background-image:url(<?php echo $imageURL; ?>);"></span>
      </a>
      <?php if(isset($this->featuredLabelActive)){ ?>
      <div class="sesblog_list_labels">
        <?php if(isset($this->featuredLabelActive) && $review->featured){ ?>
          <p class="sesblog_label_featured"><?php echo $this->translate('FEATURED');?></p>
        <?php } ?>
      </div>
      <?php } ?>
      <div class="sesblog_review_grid_thumb_cont">
      	<?php if(isset($this->titleActive) ){ ?>
          <div class="sesblog_review_grid_title">
            <?php if(strlen($review->getTitle()) > $this->title_truncation){ 
            $title = mb_substr($review->getTitle(),0,($this->title_truncation - 3)).'...';
            echo $this->htmlLink($review->getHref(),$title, array('class' => '', 'data-src' => $review->getGuid()) ) ?>
            <?php }else{ ?>
            <?php echo $this->htmlLink($review->getHref(),$review->getTitle(), array('class' => '', 'data-src' => $review->getGuid())) ?>
            <?php } ?>
            <?php if(isset($this->verifiedLabelActive) && $review->verified == 1){ ?>
            <i class="sesblog_verified_sign fa fa-check-circle" title="<?php echo $this->translate('Verified');?>"></i>
            <?php } ?>
          </div>
      	<?php } ?>
      </div>
        <?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive)) {
      $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $review->getHref()); ?>
        <div class="sesblog_grid_btns"> 
          <?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
            
          <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $review, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

          <?php }  ?>			
        </div>
      <?php } ?>
    </div>
		<div class="sesblog_review_grid_info sesbasic_clearfix clear">
			<?php $reviewCrator = Engine_Api::_()->getItem('user', $review->owner_id);?> 
			<?php $reviewTaker = Engine_Api::_()->getItem('sesblog_blog', $review->blog_id);?> 
			<div class="sesblog_review_grid_stat sesblog_sidebar_image_rounded floatL sesbasic_text_light">
				<?php echo $this->htmlLink($reviewTaker, $this->itemPhoto($reviewTaker, 'thumb.icon'), array('class' => 'sesblog_reviw_prof_img floatL')); ?>
				<div class="sesblog_review_title_block">
				  <?php if(isset($this->byActive)):?>
						<p><?php echo 'by '.$this->htmlLink($reviewCrator, $reviewCrator->getTitle());?></p>
					<?php endif;?>
					<p><?php echo 'For '.$this->htmlLink($reviewTaker, $reviewTaker->getTitle(), array('class' => 'ses_tooltip', 'data-src' => $reviewTaker->getGuid()));?></p>
					<div class="sesblog_list_stats sesblog_review_grid_stat clear">
						<?php if(isset($this->likeActive) && isset($review->like_count)) { ?>
						<span title="<?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>"><i class="fa fa-thumbs-up sesbasic_text_light"></i><?php echo $review->like_count; ?></span>
						<?php } ?>
						<?php if(isset($this->viewActive) && isset($review->view_count)) { ?>
						<span title="<?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count))?>"><i class="fa fa-eye sesbasic_text_light"></i><?php echo $review->view_count; ?></span>
						<?php } ?>
						<?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && $this->ratingActive){
						echo '<span title="'.$this->translate(array('%s rating', '%s ratings', $review->rating), $this->locale()->toNumber($review->rating)).'"><i class="fa fa-star sesbasic_text_light"></i>'.round($review->rating,1).'/5'. '</span>';
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
  </li>
<?php } ?>
<?php endforeach; ?>
