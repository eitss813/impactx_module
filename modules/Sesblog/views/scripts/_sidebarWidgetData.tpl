<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _sidebarWidgetData.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); 
?>
<?php $i = 1; ?>
<?php foreach( $this->results as $item ): ?>
    <?php   $user = $item->getOwner();
    $oldTimeZone = date_default_timezone_get();
    $convert_date = strtotime($item->creation_date);
    date_default_timezone_set($user->timezone);
    ?>
	<?php if($this->view_type == 'list'):?>
		    <li class="sesblog_vertical_list sesbasic_clearfix">
			<div class="sesblog_vertical_list_img <?php if($this->image_type == 'rounded'):?>sesblog_sidebar_image_rounded<?php endif;?> sesblog_list_thumb sesblog_thumb" style="height:<?php echo is_numeric($this->height_list) ? $this->height_list.'px' : $this->height_list ?>;width:<?php echo is_numeric($this->width_list) ? $this->width_list.'px' : $this->width_list ?>;">
				<?php $href = $item->getHref();$imageURL = $item->getPhotoUrl('thumb.profile');?>
				<a href="<?php echo $href; ?>" class="sesblog_thumb_img">
					<span class="floatL" style="background-image:url(<?php echo $imageURL; ?>);"></span>
				</a>     
			</div>
      <div class="sesblog_vertical_number"><?php echo $i; $i++; ?></div>
			<div class="sesblog_vertical_list_cont">
				<?php if(isset($this->titleActive)): ?>
					<div class="sesblog_vertical_list_title sesblog_list_info_title">
						<?php if(strlen($item->getTitle()) > $this->title_truncation):?>
							<?php $title = mb_substr($item->getTitle(),0,$this->title_truncation).'...';?>
							<?php echo $this->htmlLink($item->getHref(),$title, array('class' => 'ses_tooltip', 'data-src' => $item->getGuid()));?>
						<?php  else : ?>
							<?php echo $this->htmlLink($item->getHref(),$item->getTitle(), array('class' => 'ses_tooltip', 'data-src' => $item->getGuid())) ?>
						<?php endif; ?>
					</div>
          <?php endif; ?>
          <?php if($this->creationDateActive) { ?>
            <div class="entry_meta-date sesbasic_text_light">
              <i class="far fa-clock"></i> <?php echo date('M d, Y',$convert_date);?>
            </div>
          <?php } ?>
      </div>
   </li>
	<?php else :?>
		<li class="sesblog_sidebar_grid sesblog_grid sesbasic_clearfix sesbasic_bxs sesbm" style="width:<?php echo is_numeric($this->width_grid) ? $this->width_grid.'px' : $this->width_grid ?> ; ">
			<div class="sesblog_grid_inner sesblog_list_thumb sesblog_thumb">
				<div class="sesblog_grid_thumb sesblog_thumb" style="height:<?php echo is_numeric($this->height_grid) ? $this->height_grid.'px' : $this->height_grid ?>;">
					<?php $href = $item->getHref();$imageURL = $item->getPhotoUrl('thumb.profile');?>
					<a href="<?php echo $href; ?>" class="sesblog_item_grid_thumb_img floatL">
						<span class="floatL" style="background-image:url(<?php echo $imageURL; ?>);"></span>
					</a>
					<?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)): ?>
						<div class="sesblog_list_labels ">
							<?php if(isset($this->featuredLabelActive) && $item->featured): ?>
								<p class="sesblog_label_featured"><?php echo $this->translate('FEATURED');?></p>
							<?php endif; ?>
							<?php if(isset($this->sponsoredLabelActive) && $item->sponsored): ?>
								<p class="sesblog_label_sponsored"><?php echo $this->translate('SPONSORED');?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if(isset($this->verifiedLabelActive) && $item->verified == 1): ?>
						<div class="sesblog_verified_label" title="<?php echo $this->translate("VERIFIED"); ?>" style=""><i class="fa fa-check"></i></div>
					<?php endif; ?>
					<?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive)):?>
						<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
						<div class="sesblog_list_thumb_over"> 
							<a href="<?php echo $href; ?>" data-url = "<?php echo $item->getType() ?>"></a>
							<div class="sesblog_list_grid_thumb_btns"> 
								<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)): ?>
                  
                  <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item)); ?>

								<?php endif;?>
								<?php $itemtype = 'sesblog_blog';?>
								<?php $getId = 'blog_id';?>
								<?php $canComment =  $item->authorization()->isAllowed($this->viewer, 'comment');?>
								<?php if(isset($this->likeButtonActive) && $canComment):?>
									
									<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatusBlog($item->$getId, $item->getType()); ?>
									<a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_<?php echo $itemtype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
								<?php endif; ?>
								<?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && $this->viewer_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)):?>
									<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$item->blog_id));
									$favClass = ($favStatus)  ? 'button_active' : '';?>
									<?php $shareOptions = "<a href='javascript:;' class='sesbasic_icon_btn sesbasic_icon_btn_count sesblog_favourite_sesblog_blog_". $item->blog_id." sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog ".$favClass ."' data-url=\"$item->blog_id\"><i class='fa fa-heart'></i><span>$item->favourite_count</span></a>";?>
									<?php echo $shareOptions;?>
								<?php endif;?>
								<?php if(isset($this->listButtonActive) && $this->viewer_id): ?>
									<a href="javascript:;" onclick="opensmoothboxurl('<?php echo $this->url(array('action' => 'add','module'=>'sesblog','controller'=>'list','blog_id'=>$item->blog_id),'default',true); ?>')" class="sesbasic_icon_btn  sesblog_add_list"  title="<?php echo  $this->translate('Add To List'); ?>" data-url="<?php echo $item->blog_id ; ?>"><i class="fa fa-plus"></i></a>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="sesblog_grid_info clear sesbasic_clearfix sesbm">
					<?php if(isset($this->titleActive) ): ?>
						<div class="sesblog_grid_info_title">
							<?php if(strlen($item->getTitle()) > $this->title_truncation_grid):?>
								<?php $title = mb_substr($item->getTitle(),0,$this->title_truncation_grid).'...';?>
								<?php echo $this->htmlLink($item->getHref(),$title, array('class' => 'ses_tooltip', 'data-src' => $item->getGuid())) ?>
							<?php else: ?>
								<?php echo $this->htmlLink($item->getHref(),$item->getTitle(), array('class' => 'ses_tooltip', 'data-src' => $item->getGuid())) ?>
							<?php endif; ?>
							<?php if(isset($this->verifiedLabelActive) && $item->verified == 1): ?>
								<i class="sesblog_verified_sign fa fa-check-circle"></i>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if(isset($this->byActive)): ?>
						<div class="sesblog_admin sesbasic_clearfix">
							<?php $owner = $item->getOwner(); ?>
							<p>
								<a href="<?php echo $owner->getHref();?>"><?php echo $this->itemPhoto($owner, 'thumb.icon');?></a>
							  <?php echo $this->translate('By');?> <?php echo $this->htmlLink($owner->getHref(),$owner->getTitle()) ?>
							</p>
						</div>
					<?php endif; ?>
          <?php if(isset($this->creationDateActive)): ?>
       <div class="sesblog_admin sesbasic_clearfix"> <span><?php if($item->publish_date): ?>
              <i class="far fa-calendar"></i> <?php echo date('M d, Y',$convert_date);?>
						<?php else: ?>
              <i class="far fa-calendar"></i> <?php echo date('M d, Y',$convert_date);?>
						<?php endif; ?></span> </div>
     <?php endif;?>
					<?php if(isset($this->locationActive) && $item->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.location', 1)):?>
						<div class="sesblog_list_location">
							<span class="widthfull">
                <i class="fa fa-map-marker sesbasic_text_light" title="<?php echo $this->translate('Location');?>"></i>
								<span title="<?php echo $item->location; ?>">
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?>
                    <a href="<?php echo $this->url(array('resource_id' => $item->blog_id,'resource_type'=>'sesblog_blog','action'=>'get-direction'), 'sesbasic_get_direction', true); ?>" class="openSmoothbox"><?php echo $item->location ?></a>
                  <?php } else { ?>
                    <?php echo $item->location ?>
                  <?php } ?>
                </span>
							</span>
						</div>
					<?php endif; ?>
					<?php if(isset($this->categoryActive)): ?>
						<?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)):?>
							<?php $categoryItem = Engine_Api::_()->getItem('sesblog_category', $item->category_id);?>
							<?php if($categoryItem): ?>
                <div class="sesblog_admin sesbasic_clearfix">
                  <span>
                    <i class="fa fa-folder-open sesbasic_text_light" title="<?php echo $this->translate('Category:'); ?>"></i>
                    <a href="<?php echo $categoryItem->getHref(); ?>"><?php echo $categoryItem->category_name; ?></a>
                    <?php $subcategory = Engine_Api::_()->getItem('sesblog_category',$item->subcat_id); ?>
                    <?php if($subcategory && $item->subcat_id) { ?>
                    &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
                    <?php } ?>
                    <?php $subsubcategory = Engine_Api::_()->getItem('sesblog_category',$item->subsubcat_id); ?>
                    <?php if($subsubcategory && $item->subsubcat_id) { ?>
                    &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
                    <?php } ?>
                  </span>
                </div>
              <?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
					<div class="sesblog_list_stats">
						<?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
							<span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="sesbasic_icon_like_o sesbasic_text_light"></i><?php echo $item->like_count; ?></span>
						<?php } ?>
						<?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
							<span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="sesbasic_icon_comment_o sesbasic_text_light"></i><?php echo $item->comment_count;?></span>
						<?php } ?>
						<?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
							<span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="sesbasic_icon_view sesbasic_text_light"></i><?php echo $item->view_count; ?></span>
						<?php } ?>
						<?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
							<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="sesbasic_icon_favourite_o sesbasic_text_light"></i><?php echo $item->favourite_count; ?></span>
						<?php } ?>
					</div>
          <?php if(isset($this->ratingActive) && Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating()): ?>
            <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => ''));?>
          <?php endif;?>
				</div>
			</div>
		</li>
	<?php endif; ?>
	<?php date_default_timezone_set($oldTimeZone); ?>
<?php endforeach; ?> 
