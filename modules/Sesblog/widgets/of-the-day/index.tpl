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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php if($this->type == 'grid1'):?>
	<div class="sesblog_blog_of_the_day">
		<ul class="sesblog_album_listing sesbasic_bxs">
			<?php $limit = 0;?>
			<?php $itemBlog = Engine_Api::_()->getItem('sesblog_blog',$this->blog_id);?>
			<?php if($itemBlog):?>
				<li class="sesblog_grid sesblog_list_grid_thumb sesblog_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
				   <article>	
					<div class="sesblog_grid_inner sesblog_thumb">
						<div class="sesblog_grid_thumb sesblog_thumb" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;"> <a class="sesblog_thumb_img" href="<?php echo $itemBlog->getHref(); ?>"> <span class="main_image_container" style="background-image: url(<?php echo $itemBlog->getPhotoUrl('thumb.normal'); ?>);"></span> </a>
							<?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->verifiedLabelActive)){ ?>
								<div class="sesblog_list_labels">
									<?php if(isset($this->featuredLabelActive) && $itemBlog->featured == 1){ ?>
										<p class="sesblog_label_featured"><?php echo $this->translate("Featured"); ?></p>
									<?php } ?>
									<?php if(isset($this->sponsoredLabelActive)  && $itemBlog->sponsored == 1){ ?>
										<p class="sesblog_label_sponsored"><?php echo $this->translate("Sponsored"); ?></p>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<?php if(isset($this->likeActive) || isset($this->commentActive) || isset($this->viewActive) || isset($this->titleActive) || isset($this->favouriteActive) || isset($this->byActive)){ ?>
							<div class="sesblog_grid_info clear sesbasic_clearfix sesbm">
								<?php if(isset($this->titleActive)) { ?>
									<div class="sesblog_grid_info_title"> 
									<?php echo $this->htmlLink($itemBlog, $this->string()->truncate($itemBlog->getTitle(), $this->title_truncation),array('title'=>$itemBlog->getTitle())) ; ?> 
									<?php if(isset($this->verifiedLabelActive) && $itemBlog->verified == 1):?>
									<i class="sesbasic_verified_icon" title="Verified"></i>
								<?php endif;?>
									</div>
								<?php } ?>
								<div class="sesblog_list_grid_info sesbasic_clearfix">
									<div class="sesblog_list_stats">
										<?php if(isset($this->byActive)) { ?>
											<span class="sesblog_list_grid_owner"> <a href="<?php echo $itemBlog->getOwner()->getHref();?>"><?php echo $this->itemPhoto($itemBlog->getOwner(), 'thumb.icon');?></a> <?php echo $this->translate('By');?> <?php echo $this->htmlLink($itemBlog->getOwner()->getHref(), $itemBlog->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?> </span>
										<?php }?>
									</div>
									<div class="sesblog_list_stats sesblog_list_location sesbasic_text_light"> <span> <i class="fa fa-map-marker"></i><?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?><a href="<?php echo $this->url(array('resource_id' => $itemBlog->blog_id,'resource_type'=>'sesblog_blog','action'=>'get-direction'), 'sesbasic_get_direction', true) ;?>" class="opensmoothboxurl"><?php echo $itemBlog->location;?></a><?php } else { ?><?php echo $itemBlog->location;?><?php } ?></span> </div>
								</div>
							</div>
						<?php } ?>
						<div class="sesblog_grid_block">
              <?php  if(isset($this->descriptionActive)) {  ?>
							<div class="sesblog_grid_des clear"><?php echo $itemBlog->getDescription($this->description_truncation);?></div>
							<?php } ?>
							<div class="sesblog_grid_footer">
								<div class="sesblog_list_stats sesbasic_text_light">
									<?php if(isset($this->likeActive)) { ?>
										<span class="sesblog_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $itemBlog->like_count), $this->locale()->toNumber($itemBlog->like_count))?>"> <i class="fa fa-thumbs-up"></i> <?php echo $itemBlog->like_count;?> </span>
									<?php } ?>
									<?php if(isset($this->commentActive)) { ?>
										<span class="sesblog_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $itemBlog->comment_count), $this->locale()->toNumber($itemBlog->comment_count))?>"> <i class="fa fa-comment"></i> <?php echo $itemBlog->comment_count;?> </span>
									<?php } ?>
									<?php if(isset($this->viewActive)) { ?>
										<span class="sesblog_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $itemBlog->view_count), $this->locale()->toNumber($itemBlog->view_count))?>"> <i class="fa fa-eye"></i> <?php echo $itemBlog->view_count;?> </span>
									<?php } ?>
									<?php if(isset($this->favouriteActive) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
										<span class="sesblog_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $itemBlog->favourite_count), $this->locale()->toNumber($itemBlog->favourite_count))?>"> <i class="fa fa-heart"></i> <?php echo $itemBlog->favourite_count;?> </span>
									<?php } ?>
									<?php if(Engine_Api::_()->sesbasic()->getViewerPrivacy('sesblog_review', 'view') && isset($this->ratingActive) && isset($itemBlog->rating)): ?>
										<span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($itemBlog->rating,1)), $this->locale()->toNumber(round($itemBlog->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($itemBlog->rating,1).'/5';?></span>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive)):?>
							<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $itemBlog->getHref()); ?>
							<div class="sesblog_list_share_btns"> 
                <div class="sesblog_list_btns">
								<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
								
                  <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $itemBlog, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
								<?php endif;?>
								<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ):?>
									<?php $canComment =  $itemBlog->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
									<?php if(isset($this->likeButtonActive) && $canComment):?>
										<!--Like Button-->
										<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($itemBlog->blog_id,$itemBlog->getType()); ?>
										<a href="javascript:;" data-url="<?php echo $itemBlog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_blog_<?php echo $itemBlog->blog_id ?> sesblog_like_sesblog_blog <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $itemBlog->like_count; ?></span></a>
									<?php endif;?>
									<?php if(isset($this->favouriteButtonActive) && isset($itemBlog->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)): ?>
										<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$itemBlog->blog_id)); ?>
										<a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog_<?php echo $itemBlog->blog_id ?> sesblog_favourite_sesblog_blog <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $itemBlog->blog_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $itemBlog->favourite_count; ?></span></a>
									<?php endif;?>
								<?php endif;?>
							</div>
             </div>
						<?php endif;?> 
						</div>
				   </article>
				</li>
			<?php endif;?>
			<?php $limit++;?>
		</ul>
	</div>
<?php else :?>
<div class="sesblog_blog_two_of_the_day">
  <ul>
 <?php $limit = 0;?>
    <?php $itemBlog = Engine_Api::_()->getItem('sesblog_blog',$this->blog_id);?>
    <?php if($itemBlog):?>
  <li class="sesblog_grid sesblog_grid_two sesbasic_bxs " style="width:140px;">
    <div class="sesblog_grid_inner">
      <div class="sesblog_grid_thumb sesblog_thumb" style="height:160px;"> <a class="sesblog_thumb_img" href="<?php echo $itemBlog->getHref(); ?>"> 
      <?php if(isset($this->likeActive) || isset($this->commentActive) || isset($this->viewActive) || isset($this->titleActive) || isset($this->favouriteActive) || isset($this->byActive)){ ?>
      <span class="main_image_container" style="background-image: url(<?php echo $itemBlog->getPhotoUrl('thumb.main'); ?>);"></span> </a>
        <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
        <div class="sesblog_list_labels">
          <?php if(isset($this->featuredLabelActive) && $itemBlog->featured == 1){ ?>
          <p class="sesblog_label_featured"><?php echo $this->translate("Featured"); ?></p>
          <?php } ?>
          <?php if(isset($this->sponsoredLabelActive)  && $itemBlog->sponsored == 1){ ?>
          <p class="sesblog_label_sponsored"><?php echo $this->translate("Sponsored"); ?></p>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <div class="sesblog_grid_info clear clearfix sesbm">
        <div class="sesblog_grid_meta_block">
					<?php if($itemBlog->category_id != '' && intval($itemBlog->category_id) && !is_null($itemBlog->category_id)):?> 
						<?php $categoryItem = Engine_Api::_()->getItem('sesblog_category', $itemBlog->category_id);?>
						<?php if($categoryItem):?>
							<div class="sesblog_grid_two_category_title">
								<span>
									<a href="<?php echo $categoryItem->getHref(); ?>"><?php echo $categoryItem->category_name; ?></a>
								</span>
							</div>
						<?php endif;?>
					<?php endif;?>
          <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && isset($this->ratingStarActive)):?>
				<?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $itemBlog->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting floatR', 'style' => 'margin:0px;'));?>
			<?php endif;?>
        </div>
        <?php if(isset($this->titleActive)) { ?>
        <div class="sesblog_grid_info_title"> <?php echo $this->htmlLink($itemBlog, $this->string()->truncate($itemBlog->getTitle(), $this->title_truncation),array('title'=>$itemBlog->getTitle())) ; ?>
					<?php if(isset($this->verifiedLabelActive) && $itemBlog->verified == 1):?>
									<i class="sesbasic_verified_icon" title="Verified"></i>
					<?php endif;?>
          </div>
        <?php } ?>
        <div class="sesblog_grid_meta_block">
          <div class="sesblog_list_stats sesbasic_text_dark">
            <?php if(isset($this->byActive)) { ?>
            <span class="sesblog_list_grid_owner"> <a href="<?php $itemBlog->getOwner()->getHref();?>"><?php echo $this->itemPhoto($itemBlog->getOwner(), 'thumb.icon');?></a> <?php echo $this->translate('By');?> <?php echo $this->htmlLink($itemBlog->getOwner()->getHref(), $itemBlog->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?> </span>
            <?php }?>
           </div>
        </div>
         <?php  if(isset($this->descriptionActive)) {  ?>
        <div class="sesblog_grid_contant">
          <?php echo $itemBlog->getDescription($this->description_truncation);?>
        </div>
        <?php  } ?>
        <div class="sesblog_list_stats sesbasic_text_light">
          <?php if(isset($this->likeActive)) { ?>
          <span class="sesblog_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $itemBlog->like_count), $this->locale()->toNumber($itemBlog->like_count))?>"> <i class="sesbasic_icon_like_o"></i> <?php echo $itemBlog->like_count;?> </span>
          <?php } ?>
          <?php if(isset($this->commentActive)) { ?>
          <span class="sesblog_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $itemBlog->comment_count), $this->locale()->toNumber($itemBlog->comment_count))?>"> <i class="sesbasic_icon_comment_o"></i> <?php echo $itemBlog->comment_count;?> </span>
          <?php } ?>
          <?php if(isset($this->viewActive)) { ?>
          <span class="sesblog_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $itemBlog->view_count), $this->locale()->toNumber($itemBlog->view_count))?>"> <i class="sesbasic_icon_view"></i> <?php echo $itemBlog->view_count;?> </span>
          <?php } ?>
          <?php if(isset($this->favouriteActive) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
          <span class="sesblog_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $itemBlog->favourite_count), $this->locale()->toNumber($itemBlog->favourite_count))?>"> <i class="sesbasic_icon_favourite_o"></i> <?php echo $itemBlog->favourite_count;?> </span>
          <?php } ?>
          <?php if(Engine_Api::_()->sesbasic()->getViewerPrivacy('sesblog_review', 'view') && isset($this->ratingActive) && isset($itemBlog->rating)): ?>
							<span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($itemBlog->rating,1)), $this->locale()->toNumber(round($itemBlog->rating,1)))?>"><i class="far fa-star"></i><?php echo round($itemBlog->rating,1).'/5';?></span>
						<?php endif; ?></div>
          <?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive)):?>
						<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $itemBlog->getHref()); ?>
						<div class="sesblog_list_share_btns"> 
						  <div class="sesblog_list_btns">
							<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
                
                <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $itemBlog, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
							<?php endif;?>
							<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ):?>
								<?php $canComment =  $itemBlog->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
								<?php if(isset($this->likeButtonActive) && $canComment):?>
									<!--Like Button-->
									<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($itemBlog->blog_id,$itemBlog->getType()); ?>
									<a href="javascript:;" data-url="<?php echo $itemBlog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_blog_<?php echo $itemBlog->blog_id ?> sesblog_like_sesblog_blog <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $itemBlog->like_count; ?></span></a>
								<?php endif;?>
								<?php if(isset($this->favouriteButtonActive) && isset($itemBlog->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)): ?>
									<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$itemBlog->blog_id)); ?>
									<a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog_<?php echo $itemBlog->blog_id ?> sesblog_favourite_sesblog_blog <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $itemBlog->blog_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $itemBlog->favourite_count; ?></span></a>
								<?php endif;?>
							<?php endif;?>
						</div>
					<?php endif;?>
        </div>
      <?php } ?>
    </div>
  </li>
      <?php endif;?>
    <?php $limit++;
   ?>
   </ul>
   </div>
 <?php endif;?>
