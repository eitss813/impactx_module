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

<?php $allParams=$this->allParams; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/jquery.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/owl.carousel.js') ?>

<div class="slide sesbasic_clearfix sesbasic_bxs sesblog_blogs_carousel_wrapper <?php echo $this->isfullwidth ? 'isfull_width' : '' ; ?>" style="height:<?php echo $this->height ?>px;">
  <div class="blogslide_<?php echo $this->identity; ?> owl-carousel sesblog_featured_sponsored_carousel"  style="height:<?php echo $this->height ?>px;">
    <?php foreach( $this->paginator as $item): ?>
    <div class="sesblog_grid_inside sesbasic_clearfix sesblog_grid_btns_wrap" style="height:<?php echo $this->height ?>px;">
    	<div class="sesblog_grid_inside_inner">
      <div class="sesblog_featured_sponsored_item_thumb sesblog_thumb">       
        <?php
        $href = $item->getHref();
        $imageURL = $item->getPhotoUrl();
        ?>
        <a href="<?php echo $href; ?>" class="sesblog_list_thumb_img"  style="height:<?php echo $this->height ?>px;">
          <span style="background-image:url(<?php echo $imageURL; ?>);"></span>
        </a>
        <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)):?>
          <div class="sesblog_list_labels">
            <?php if(isset($this->sponsoredLabelActive) && $item->sponsored == 1):?>
              <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
            <?php endif;?>
            <?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
              <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
            <?php endif;?>
          </div>
        <?php endif;?>
		<?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive)):?>
				<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
					<div class="sesblog_list_share_btns"> 
						<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
              <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

						<?php endif;?>
						<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ):?>
							<?php $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
							<?php if(isset($this->likeButtonActive) && $canComment):?>
								<!--Like Button-->
								<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($item->blog_id,$item->getType()); ?>
								<a href="javascript:;" data-url="<?php echo $item->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_blog_<?php echo $item->blog_id ?> sesblog_like_sesblog_blog <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
							<?php endif;?>
							<?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)): ?>
								<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$item->blog_id)); ?>
								<a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog_<?php echo $item->blog_id ?> sesblog_favourite_sesblog_blog <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->blog_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
							<?php endif;?>
						<?php endif;?>
					</div>
			<?php endif;?> 
        </div>
         <?php if(isset($this->creationDateActive)){ ?>
				<div class="sesblog_date"><?php echo ' '.date('d M',strtotime($item->creation_date));?></div>
			<?php } ?>
				<div class="sesblog_grid_inside_info sesbasic_clearfix ">
					<?php if(isset($this->categoryActive)){ ?>
						<?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)):?> 
							<?php $categoryItem = Engine_Api::_()->getItem('sesblog_category', $item->category_id);?>
							<?php if($categoryItem):?>
								<div class="category_tag sesbasic_clearfix">
										<a href="<?php echo $categoryItem->getHref(); ?>"><?php echo $categoryItem->category_name; ?></a>
								</div>
							<?php endif;?>
						<?php endif;?>
					<?php } ?>
          <?php if(isset($this->titleActive) ){ ?>
            <span class="sesblog_category_carousel_item_info_title">
              <?php if(strlen($item->getTitle()) > $this->title_truncation){ 
                $title = mb_substr($item->getTitle(),0,$this->title_truncation).'...';
                echo $this->htmlLink($item->getHref(),$title) ?>
              <?php }else{ ?>
              	<?php echo $this->htmlLink($item->getHref(),$item->getTitle() ) ?>
              <?php } ?>
              <?php if(isset($this->verifiedLabelActive) && $item->verified == 1):?>
						<i class="sesbasic_verified_icon" title="Verified"></i>
					<?php endif;?>
            </span>
          <?php } ?>
          <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && isset($this->ratingStarActive)):?>
								<?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting'));?>
							<?php endif;?>
          <div class="sesblog_fs_stats">
          <?php if(isset($this->byActive)){ ?>
				<div class="sesblog_owner">
        <?php $owner = $item->getOwner(); ?>
					<span>
            <?php echo $this->htmlLink($item->getOwner()->getParent(), $this->itemPhoto($item->getOwner()->getParent(), 'thumb.icon')); ?>
						<?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?>
					</span>
				</div>
			<?php } ?>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->readtimeActive) && !empty($item->readtime)) { ?>
        <div class="sesblog_list_stats">
          <span><i class="far fa-clock"></i><?php echo $item->readtime ?>. <?php echo $this->translate("read"); ?></span>
        </div>
      <?php } ?>
      </div>
      <div class="sesblog_list_stats">
			<?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
				<span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="sesbasic_icon_like_o"></i><?php echo $item->like_count; ?></span>
			<?php } ?>
			<?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
				<span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="sesbasic_icon_comment_o"></i><?php echo $item->comment_count;?> </span>
			<?php } ?>
			<?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
				<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="sesbasic_icon_favourite_o"></i>
        <?php echo $item->favourite_count;?> </span>
			<?php } ?>
			<?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
				<span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="sesbasic_icon_view"></i><?php echo $item->view_count; ?></span>
			<?php } ?>
			<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/_blogRatingStat.tpl';?>
		</div>
        </div>
        </div>
    	</div>
    <?php endforeach; ?>
  </div>
</div>
<script type="text/javascript">
	<?php if($allParams['autoplay']){ ?>
		var autoplay_<?php echo $this->identity; ?> = true;
	<?php }else{ ?>
		var autoplay_<?php echo $this->identity; ?> = false;
	<?php } ?>
  sesblogJqueryObject(".sesblog_featured_sponsored_carousel").owlCarousel({
  nav:true,
  dots:false,
  items:1,
	margin:10,
	rewind: true,
  responsiveClass:true,
	autoplaySpeed: <?php echo $allParams['speed']; ?>,
	autoplay: autoplay_<?php echo $this->identity; ?>,
  responsive:{
    0:{
        items:1,
    },
    600:{
        items:3,
    },
  }
});
sesblogJqueryObject(".owl-prev").html('<i class="fa fa-angle-left"></i>');
sesblogJqueryObject(".owl-next").html('<i class="fa fa-angle-right"></i>');
</script>
