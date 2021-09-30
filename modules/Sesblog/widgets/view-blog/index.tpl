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
<?php 

$allParams = $this->allParams;
$baseUrl = $this->layout()->staticBaseUrl;

$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesblog/externals/styles/styles.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Sesblog/externals/scripts/infinite-scroll.js'); 

?>
<?php if($this->sesblog->style == 1):?>

<div class="sesblog_layout_contant sesbasic_clearfix sesbasic_bxs">
  <div class="sesblog_layout_contant_header">
    <?php if(isset($allParams['show_criteria']) && in_array('category', $allParams['show_criteria'])) { ?>
    <p class="sesblog_cat"><a href="<?php echo $this->category->getHref(); ?>"><?php echo $this->translate($this->category->category_name) ?></a></p>
    <?php } ?>
    <div> <a href="javascript:;" class="sesbasic_pulldown_toggle sesblog_profile_options"><i class="fa fa-ellipsis-h"></i></a>
      <div class="sesbasic_pulldown_options">
        <ul>
          <?php if($this->coreSettings->getSetting('sesblog.enable.subblog', 1)){ ?>
          <li><a href="<?php echo $this->url(array('action' => 'create', 'parent_id' => $this->sesblog->blog_id), 'sesblog_general', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Create Sub Blog');?></a></li>
          <?php } ?>
          <?php if(isset($this->ownerOptionsActive) && $this->isBlogAdmin):?>
          <li><a href="<?php echo $this->url(array('action' => 'edit', 'blog_id' => $this->sesblog->custom_url), 'sesblog_dashboard', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Dashboard');?></a></li>
          <?php endif;?>
          <?php if($this->can_delete) { ?>
          <li><a href="<?php echo $this->url(array('action' => 'delete', 'blog_id' => $this->sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $this->translate('Delete This Blog');?></a></li>
          <?php } ?>
          <?php if($this->viewer_id && isset($this->smallShareButtonActive) && $this->enableSharng):?>
          <li><a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i><?php echo $this->translate('Share');?></a></li>
          <?php endif;?>
          <?php if($this->viewer_id){  ?>
          <?php if($this->viewer_id && $this->viewer_id != $this->sesblog->owner_id && $this->coreSettings->getSetting('sesblog.enable.report', 1)):?>
          <li><a href="<?php echo $this->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $this->sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
          <?php endif;?>
          <?php  } else { ?>
          <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && $this->coreSettings->getSetting('sesblog.enable.report', 1)) { ?>
          <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
          <?php  } ?>
          <?php } ?>
          <?php if($this->viewer_id){  ?>
          <?php if(isset($this->postCommentActive) && $this->canComment):?>
          <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
          <?php endif;?>
          <?php  } else { ?>
          <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($this->postCommentActive)) { ?>
          <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
          <?php  } ?>
          <?php  } ?>
        </ul>
      </div>
    </div>
  </div>
  <?php if(isset($this->titleActive)):?>
  <h2><?php echo $this->sesblog->getTitle() ?></h2>
  <?php endif;?>
  <div class="sesblog_entrylist_entry_date">
    <?php if($this->ownernameActive) { ?>
    <p><?php echo $this->translate('By');?> <b><?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?></b> &nbsp;-&nbsp;</p>
    <?php } ?>
    <p>
      <?php if($this->createDateActive) { ?>
      <?php echo $this->timestamp($this->sesblog->creation_date) ?>
      <?php } ?>
      <?php if( $this->category ): ?>
      &nbsp;-&nbsp;</p>
    <?php   if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->sesblog->readtime) && !empty($this->sesblog->readtime)) {  ?>
    <p>
      <?php  echo $this->sesblog->readtime; ?>
    </p>
    <?php  } ?>
    <?php if(isset($this->staticsActive)):?>
    &nbsp;-&nbsp;
    <p>
      <?php if(isset($this->viewActive)):?>
      <span><?php echo $this->translate(array('%s View', '%s Views', $this->sesblog->view_count), $this->locale()->toNumber($this->sesblog->view_count)) ?>&nbsp;</span>
      <?php endif;?>
      <?php if(isset($this->commentActive)):?>
      <span><?php echo $this->translate(array('%s Comment', '%s Comments', $this->sesblog->comment_count), $this->locale()->toNumber($this->sesblog->comment_count)) ?>&nbsp;</span>
      <?php endif;?>
      <?php if(isset($this->likeActive)):?>
      <span><?php echo $this->translate(array('%s Like', '%s Likes', $this->sesblog->like_count), $this->locale()->toNumber($this->sesblog->like_count)) ?></span>
      <?php endif;?>
      <?php if($this->isAllowReview && isset($this->reviewActive)):?>
      &nbsp; <span><?php echo $this->translate(array('%s Review', '%s Reviews', $this->reviewCount), $this->locale()->toNumber($this->reviewCount)) ?></span>
      <?php endif;?>
    </p>
    <?php endif;?>
    <?php if(isset($this->ratingActive)):?>
    &nbsp;-&nbsp;
    <p class="sesbasic_rating_star">
      <?php $ratingCount = $this->sesblog->rating; $x=0; ?>
      <?php if( $ratingCount > 0 ): ?>
      <?php for( $x=1; $x<=$ratingCount; $x++ ): ?>
      <span id="" class="sesblog_rating_star"></span>
      <?php endfor; ?>
      <?php if( (round($ratingCount) - $ratingCount) > 0){ ?>
      <span class="sesblog_rating_star sesblog_rating_star_half"></span>
      <?php }else{ $x = $x - 1;} ?>
      <?php if($x < 5){ 
						for($j = $x ; $j < 5;$j++){ ?>
      <span class="sesblog_rating_star sesblog_rating_star_disable"></span>
      <?php }   	
						} ?>
      <?php endif; ?>
    </p>
    <?php endif;?>
  </div>
  <div class="sesblog_entrylist_entry_body">
    <?php if(isset($this->photoActive) && $this->sesblog->photo_id):?>
    <div class="sesblog_blog_image clear" style="height: <?php echo $this->image_height; ?>px;overflow: hidden;"> <img src="<?php echo Engine_Api::_()->storage()->get($this->sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt="">
      <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->verifiedLabelActive)):?>
      <div class="sesblog_list_labels">
        <?php if($item->sponsored == 1):?>
        <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
        <?php endif;?>
        <?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
        <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
        <?php endif;?>
        <?php if(isset($this->verifiedLabelActive) && $item->verified == 1):?>
        <p class="sesblog_label_verified"><?php echo $this->translate('VERIFIED');?></p>
        <?php endif;?>
      </div>
      <?php endif;?>
    </div>
    <?php endif;?>
    <?php if(isset($this->descriptionActive)):?>
    <?php if($this->sesblog->cotinuereading){
					$check = true;
					$style = 'style="height:400px; overflow:hidden;"';
				}else{
					$check = false;
					$style = '';
				} ?>
    <div class="rich_content_body" style="visibility:hidden"><?php echo htmlspecialchars_decode($this->sesblog->body);?></div>
    <div class="sesblog_morebtn" id="sesblog_morebtn" style="display:none"><a href="javascript:void(0);" onclick="continuereading();"><?php echo $this->translate("Continue Reading"); ?></a></div>
    <div class="sesblog_lessbtn" id="sesblog_lessbtn" style="display:none"><a href="javascript:void(0);" onclick="showless();"><?php echo $this->translate("Show Less"); ?></a></div>
    <?php endif;?>
  </div>
  <?php if(isset($allParams['show_criteria']) && in_array('tags', $allParams['show_criteria'])) { ?>
  <p class="sesblog_profile_tags">
    <?php if (count($this->sesblogTags )):?>
    <?php foreach ($this->sesblogTags as $tag): ?>
    <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
    <?php endforeach; ?>
    <?php endif; ?>
    <?php } ?>
  <div class="sesblog_footer_two_blog clear">
    <div class="sesblog_shear_blog sesbasic_bxs">
      <?php if(isset($this->socialShareActive) && $this->enableSharng):?>
      <?php echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $this->sesblog, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
      <?php endif;?>
      <?php if($this->viewer_id && $this->enableSharng && isset($this->shareButtonActive)):?>
      <a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="share_icon sesbasic_icon_btn smoothbox"><i class="fa fa-share "></i></a>
      <?php endif;?>
      <?php if($this->viewer_id) { ?>
      <?php if(isset($this->likeButtonActive) && $this->canComment):?>
      <a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($this->LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $this->likeClass;?>"></i></a>
      <?php endif;?>
      <?php if(isset($this->favouriteButtonActive) && $this->coreSettings->getSetting('sesblog.enable.favourite', 1)):?>
      <a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a>
      <?php endif;?>
      <?php } else {  ?>
      <?php if(isset($this->likeButtonActive) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
      <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($this->LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $this->likeClass;?>"></i></a>
      <?php } ?>
      <?php if(isset($this->favouriteButtonActive) && $this->coreSettings->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
      <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a>
      <?php } ?>
      <?php   } ?>
    </div>
  </div>
</div>
<?php  endif; ?>
<?php elseif($this->sesblog->style == 2): ?>
<style>
	  #global_page_sesblog-index-view #global_wrapper{
			 padding-top:0 !important;
		}
		.layout_sesblog_view_blog{
			 padding:0 !important;
		}
	</style>
<!--second profile blog start-->
<div class="sesblog_profile_layout_second sesbasic_clearfix sesbasic_bxs">
  <?php if(isset($this->photoActive) && $this->sesblog->photo_id):?>
  <div class="sesblog_profile_layout_second_image clear" > <a href="<?php echo $this->sesblog->getHref(); ?>"><img  src="<?php echo Engine_Api::_()->storage()->get($this->sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt=""></a>
    <div class="sesblog_list_labels">
      <?php if($item->sponsored == 1):?>
      <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
      <?php endif;?>
      <?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
      <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
      <?php endif;?>
      <?php if(isset($this->verifiedLabelActive) && $item->verified == 1):?>
      <p class="sesblog_label_verified"><?php echo $this->translate('VERIFIED');?></p>
      <?php endif;?>
    </div>
    <div class="sesblog_second_options"> <a href="javascript:;" class="sesbasic_pulldown_toggle sesblog_profile_options"><i class="fa fa-ellipsis-h"></i></a>
      <div class="sesbasic_pulldown_options">
        <ul>
          <?php if(isset($this->ownerOptionsActive) && $this->isBlogAdmin):?>
          <?php if($this->coreSettings->getSetting('sesblog.enable.subblog', 1)){ ?>
          <li><a href="<?php echo $this->url(array('action' => 'create', 'parent_id' => $this->sesblog->blog_id), 'sesblog_general', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Create Sub Blog');?></a></li>
          <?php } ?>
          <li><a href="<?php echo $this->url(array('action' => 'edit', 'blog_id' => $this->sesblog->custom_url), 'sesblog_dashboard', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Dashboard');?></a></li>
          <?php if($this->can_delete) { ?>
          <li><a href="<?php echo $this->url(array('action' => 'delete', 'blog_id' => $this->sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $this->translate('Delete This Blog');?></a></li>
          <?php } ?>
          <?php endif;?>
          <?php if($this->viewer_id && isset($this->smallShareButtonActive) && $this->enableSharng):?>
          <li><a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i><?php echo $this->translate('Share');?></a></li>
          <?php endif;?>
          <?php if($this->viewer_id){  ?>
          <?php if($this->viewer_id && $this->viewer_id != $this->sesblog->owner_id && $this->coreSettings->getSetting('sesblog.enable.report', 1)):?>
          <li><a href="<?php echo $this->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $this->sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
          <?php endif;?>
          <?php  } else { ?>
          <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && $this->coreSettings->getSetting('sesblog.enable.report', 1)) { ?>
          <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
          <?php  } ?>
          <?php } ?>
          <?php if($this->viewer_id){  ?>
          <?php if(isset($this->postCommentActive) && $this->canComment):?>
          <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
          <?php endif;?>
          <?php  } else { ?>
          <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($this->postCommentActive)) { ?>
          <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
          <?php  } ?>
          <?php  } ?>
        </ul>
      </div>
    </div>
    <div class="sesblog_profile_layout_second_info">
      <?php if( $this->category && isset($allParams['show_criteria']) && in_array('category', $allParams['show_criteria'])): ?>
      <?php echo $this->translate('') ?>
      <div class="sesblog_category_teg">
        <p> <a href="<?php echo $this->category->getHref(); ?>"><?php echo $this->translate($this->category->category_name) ?></a> </p>
      </div>
      <?php endif; ?>
      <?php if(isset($this->titleActive)):?>
      <h2><?php echo $this->sesblog->getTitle() ?></h2>
      <?php endif;?>
      <?php if(isset($this->ratingActive)):?>
      <div class="sesbasic_rating_star">
        <?php $ratingCount = $this->sesblog->rating; $x=0; ?>
        <?php if( $ratingCount > 0 ): ?>
        <?php for( $x=1; $x<=$ratingCount; $x++ ): ?>
        <span id="" class="sesblog_rating_star"></span>
        <?php endfor; ?>
        <?php if( (round($ratingCount) - $ratingCount) > 0){ ?>
        <span class="sesblog_rating_star sesblog_rating_star_half"></span>
        <?php }else{ $x = $x - 1;} ?>
        <?php if($x < 5){ 
						for($j = $x ; $j < 5;$j++){ ?>
        <span class="sesblog_rating_star sesblog_rating_star_disable"></span>
        <?php }   	
						} ?>
        <?php endif; ?>
      </div>
      <?php endif;?>
      <p class="sesblog_owner">
        <?php  $owner=Engine_Api::_()->getItem('user', $this->owner);  ?>
        <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon', $owner->getTitle())); ?> <?php echo $this->translate('By');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?> </p>
      <div class="sesblog_entrylist_entry_date">
        <p><?php echo $this->translate('<i class="far fa-calendar"></i>') ?>&nbsp; <?php echo $this->timestamp($this->sesblog->publish_date) ?>
          <?php  ?>
        </p>
        <?php  if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->sesblog->readtime) && !empty($this->sesblog->readtime)) {  ?>
        <p><i class="far fa-clock"></i> <?php echo $this->sesblog->readtime; ?></p>
        <?php } ?>
        <?php if(isset($this->staticsActive)):?>
        <p>
          <?php if(isset($this->viewActive)):?>
          <span><i class="sesbasic_icon_view"></i>&nbsp; <?php echo $this->translate(array('%s view', '%s views', $this->sesblog->view_count), $this->locale()->toNumber($this->sesblog->view_count)) ?></span>
          <?php endif;?>
          <?php if(isset($this->commentActive)):?>
          <span><i class="sesbasic_icon_comment_o"></i>&nbsp;<?php echo $this->translate(array('%s comment', '%s comments', $this->sesblog->comment_count), $this->locale()->toNumber($this->sesblog->comment_count)) ?></span>
          <?php endif;?>
          <?php if(isset($this->likeActive)):?>
          <span><i class="sesbasic_icon_like_o"></i>&nbsp;<?php echo $this->translate(array('%s like', '%s likes', $this->sesblog->like_count), $this->locale()->toNumber($this->sesblog->like_count)) ?></span>
          <?php endif;?>
          <?php if($this->isAllowReview && isset($this->reviewActive)):?>
          <span><i class="far fa-star"></i>&nbsp;<?php echo $this->translate(array('%s review', '%s reviews', $this->reviewCount), $this->locale()->toNumber($this->reviewCount)) ?></span>
          <?php endif;?>
        </p>
        <?php endif;?>
      </div>
    </div>
  </div>
  <?php endif;?>
</div>
<!--second profile blog end-->
<?php elseif($this->sesblog->style == 3):?>
<!--three profile blog start-->
<div class="sesblog_profile_layout_three sesbasic_clearfix sesbasic_bxs">
  <div class="sesblog_profile_three_main">
    <div class="sesblog_profile_three_header">
      <?php if( $this->category && isset($allParams['show_criteria']) && in_array('category', $allParams['show_criteria'])): ?>
      <p class="category"> <a href="<?php echo $this->category->getHref(); ?>"><?php echo $this->translate($this->category->category_name) ?></a> </p>
      <?php endif; ?>
      <?php if(isset($this->ratingActive)):?>
      <div class="sesbasic_rating_star">
        <?php $ratingCount = $this->sesblog->rating; $x=0; ?>
        <?php if( $ratingCount > 0 ): ?>
        <?php for( $x=1; $x<=$ratingCount; $x++ ): ?>
        <span id="" class="sesblog_rating_star"></span>
        <?php endfor; ?>
        <?php if( (round($ratingCount) - $ratingCount) > 0){ ?>
        <span class="sesblog_rating_star sesblog_rating_star_half"></span>
        <?php }else{ $x = $x - 1;} ?>
        <?php if($x < 5){ 
					for($j = $x ; $j < 5;$j++){ ?>
        <span class="sesblog_rating_star sesblog_rating_star_disable"></span>
        <?php }   	
					} ?>
        <?php endif; ?>
      </div>
      <?php endif;?>
    </div>
    <?php if(isset($this->titleActive)):?>
    <h2><?php echo $this->sesblog->getTitle() ?></h2>
    <?php endif;?>
    <div class="sesblog_entrylist_entry_date">
      <?php if(isset($this->staticsActive)):?>
      <p class="sesbasic_text_light"> <span><i class=" far fa-user"></i> <?php echo $this->translate('');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?></span> <span><?php echo $this->translate('<i class="far fa-calendar"></i>') ?>&nbsp;<?php echo $this->timestamp($this->sesblog->publish_date) ?></span>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->sesblog->readtime) && !empty($this->sesblog->readtime)) {  ?>
        <span><i class="far fa-clock"></i> <?php echo $this->sesblog->readtime; ?> </span>
        <?php } ?>
      </p>
      <?php endif;?>
      <p class="sesbasic_text_light">
        <?php if(isset($this->staticsActive)):?>
        <?php if(isset($this->viewActive)):?>
        <span><i class="sesbasic_icon_view"></i> <?php echo $this->translate(array('%s view', '%s views', $this->sesblog->view_count), $this->locale()->toNumber($this->sesblog->view_count)) ?></span>
        <?php endif;?>
        <?php if(isset($this->commentActive)):?>
        <span><i class="sesbasic_icon_comment_o"></i><?php echo $this->translate(array('%s Comment', '%s Comments', $this->sesblog->comment_count), $this->locale()->toNumber($this->sesblog->comment_count)) ?></span>
        <?php endif;?>
        <?php if(isset($this->likeActive)):?>
        <span><i class="sesbasic_icon_like_o"></i><?php echo $this->translate(array('%s Like', '%s Likes', $this->sesblog->like_count), $this->locale()->toNumber($this->sesblog->like_count)) ?></span>
        <?php endif;?>
        <?php if($this->isAllowReview && isset($this->reviewActive)):?>
        <span><i class="far fa-star"></i><?php echo $this->translate(array('%s Review', '%s Reviews', $this->reviewCount), $this->locale()->toNumber($this->reviewCount)) ?></span>
        <?php endif;?>
        <?php endif;?>
      </p>
    </div>
    <div class="sesblog_footer_three_blog clear">
    <div class="sesblog_share_blog sesbasic_bxs">
      <?php if(isset($this->socialShareActive) && $this->enableSharng):?>
      <?php echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $this->sesblog, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
      <?php endif;?>
      <?php if($this->viewer_id && $this->enableSharng && isset($this->shareButtonActive)):?>
      <a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="share_icon sesbasic_icon_btn smoothbox"><i class="fa fa-share "></i></a>
      <?php endif;?>
      <?php if($this->viewer_id) { ?>
      <?php if(isset($this->likeButtonActive) && $this->canComment):?>
      <a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($this->LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $this->likeClass;?>"></i></a>
      <?php endif;?>
      <?php if(isset($this->favouriteButtonActive) && $this->coreSettings->getSetting('sesblog.enable.favourite', 1)):?>
      <a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a>
      <?php endif;?>
      <?php } else {  ?>
      <?php if(isset($this->likeButtonActive) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
      <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($this->LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $this->likeClass;?>"></i></a>
      <?php } ?>
      <?php if(isset($this->favouriteButtonActive) && $this->coreSettings->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
      <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a>
      <?php } ?>
      <?php   } ?>
    </div>
  </div>
  </div>
  <div class="sesblog_entrylist_entry_body">
    <?php if(isset($this->photoActive) && $this->sesblog->photo_id):?>
    <div class="sesblog_blog_image clear" style="height: <?php echo $this->image_height ?>px;overflow: hidden;"> <img src="<?php echo Engine_Api::_()->storage()->get($this->sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt="">
      <div class="sesblog_list_labels">
        <?php if($item->sponsored == 1):?>
        <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
        <?php endif;?>
        <?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
        <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
        <?php endif;?>
        <?php if(isset($this->verifiedLabelActive) && $item->verified == 1):?>
        <p class="sesblog_label_verified"><?php echo $this->translate('VERIFIED');?></p>
        <?php endif;?>
      </div>
      <div> <a href="javascript:;" class="sesbasic_pulldown_toggle sesblog_profile_options"><i class="fa fa-ellipsis-h"></i></a>
        <div class="sesbasic_pulldown_options">
          <ul>
            <?php if(isset($this->ownerOptionsActive) && $this->isBlogAdmin):?>
            <?php if($this->coreSettings->getSetting('sesblog.enable.subblog', 1)){ ?>
            <li><a href="<?php echo $this->url(array('action' => 'create', 'parent_id' => $this->sesblog->blog_id), 'sesblog_general', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Create Sub Blog');?></a></li>
            <?php } ?>
            <li><a href="<?php echo $this->url(array('action' => 'edit', 'blog_id' => $this->sesblog->custom_url), 'sesblog_dashboard', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Dashboard');?></a></li>
            <?php if($this->can_delete) { ?>
            <li><a href="<?php echo $this->url(array('action' => 'delete', 'blog_id' => $this->sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $this->translate('Delete This Blog');?></a></li>
            <?php } ?>
            <?php endif;?>
            <?php if($this->viewer_id && isset($this->smallShareButtonActive) && $this->enableSharng):?>
            <li><a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i><?php echo $this->translate('Share');?></a></li>
            <?php endif;?>
            <?php if($this->viewer_id){  ?>
            <?php if($this->viewer_id && $this->viewer_id != $this->sesblog->owner_id && $this->coreSettings->getSetting('sesblog.enable.report', 1)):?>
            <li><a href="<?php echo $this->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $this->sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
            <?php endif;?>
            <?php  } else { ?>
            <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && $this->coreSettings->getSetting('sesblog.enable.report', 1)) { ?>
            <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
            <?php  } ?>
            <?php } ?>
            <?php if($this->viewer_id){  ?>
            <?php if(isset($this->postCommentActive) && $this->canComment):?>
            <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
            <?php endif;?>
            <?php  } else { ?>
            <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($this->postCommentActive)) { ?>
            <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
            <?php  } ?>
            <?php  } ?>
          </ul>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<!--three profile blog start-->
<?php elseif($this->sesblog->style == 4):?>
<div class="sesblog_profile_layout_four sesbasic_clearfix sesbasic_bxs">
  <div class="sesblog_entrylist_entry_body">
    <div class="sesblog_profile_four_top">
      <div class="sesblog_second_options"> <a href="javascript:;" class="sesbasic_pulldown_toggle sesblog_profile_options"><i class="fa fa-ellipsis-h"></i></a>
        <div class="sesbasic_pulldown_options">
          <ul>
            <?php if(isset($this->ownerOptionsActive) && $this->isBlogAdmin):?>
            <?php if($this->coreSettings->getSetting('sesblog.enable.subblog', 1)){ ?>
            <li><a href="<?php echo $this->url(array('action' => 'create', 'parent_id' => $this->sesblog->blog_id), 'sesblog_general', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Create Sub Blog');?></a></li>
            <?php } ?>
            <li><a href="<?php echo $this->url(array('action' => 'edit', 'blog_id' => $this->sesblog->custom_url), 'sesblog_dashboard', 'true');?>"><i class="fa fa-edit"></i><?php echo $this->translate('Dashboard');?></a></li>
            <?php if($this->can_delete) { ?>
            <li><a href="<?php echo $this->url(array('action' => 'delete', 'blog_id' => $this->sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $this->translate('Delete This Blog');?></a></li>
            <?php } ?>
            <?php endif;?>
            <?php if($this->viewer_id && isset($this->smallShareButtonActive) && $this->enableSharng):?>
            <li><a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i><?php echo $this->translate('Share');?></a></li>
            <?php endif;?>
            <?php if($this->viewer_id){  ?>
            <?php if($this->viewer_id && $this->viewer_id != $this->sesblog->owner_id && $this->coreSettings->getSetting('sesblog.enable.report', 1)):?>
            <li><a href="<?php echo $this->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $this->sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
            <?php endif;?>
            <?php  } else { ?>
            <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && $this->coreSettings->getSetting('sesblog.enable.report', 1)) { ?>
            <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $this->translate('Report');?></a></li>
            <?php  } ?>
            <?php } ?>
            <?php if($this->viewer_id){  ?>
            <?php if(isset($this->postCommentActive) && $this->canComment):?>
            <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
            <?php endif;?>
            <?php  } else { ?>
            <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($this->postCommentActive)) { ?>
            <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-comment"></i><?php echo $this->translate('Post Comment');?></a></li>
            <?php  } ?>
            <?php  } ?>
          </ul>
        </div>
      </div>
      <div class="sesblog_profile_four_data">
        <div class="sesblog_profile_four_header">
          <?php if( $this->category && isset($allParams['show_criteria']) && in_array('category', $allParams['show_criteria'])): ?>
          <span class="sesblog_cat"> <a href="<?php echo $this->category->getHref(); ?>"><?php echo $this->translate($this->category->category_name) ?></a> </span>
          <?php endif; ?>
          <?php if(isset($this->ratingActive)):?>
          <div class="sesbasic_rating_star floatR">
            <?php $ratingCount = $this->sesblog->rating; $x=0; ?>
            <?php if( $ratingCount > 0 ): ?>
            <?php for( $x=1; $x<=$ratingCount; $x++ ): ?>
            <span id="" class="sesblog_rating_star"></span>
            <?php endfor; ?>
            <?php if( (round($ratingCount) - $ratingCount) > 0){ ?>
            <span class="sesblog_rating_star sesblog_rating_star_half"></span>
            <?php }else{ $x = $x - 1;} ?>
            <?php if($x < 5){ 
					for($j = $x ; $j < 5;$j++){ ?>
            <span class="sesblog_rating_star sesblog_rating_star_disable"></span>
            <?php }   	
					} ?>
            <?php endif; ?>
          </div>
          <?php endif;?>
        </div>
        <?php if(isset($this->titleActive)):?>
        <h2><?php echo $this->sesblog->getTitle() ?></h2>
        <?php endif;?>
        <div class="sesblog_entrylist_entry_date"> <span class="sesblog_entry_border"></span>
          <p> <span> <?php echo $this->translate('');?>&nbsp; <?php echo $this->htmlLink($this->owner->getHref(), 
        $this->itemPhoto($this->owner),
				array('class' => 'sesblogs_gutter_photo')) ?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?> &nbsp;-&nbsp;</span> <span> <?php echo $this->translate('<i class="far fa-calendar"></i>') ?> <?php echo $this->timestamp($this->sesblog->creation_date) ?> &nbsp;-&nbsp; </span>
            <?php  ?>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->sesblog->readtime) && !empty($this->sesblog->readtime)) {  ?>
          
          <p><i class="far fa-clock"></i> <?php echo $this->sesblog->readtime; ?> &nbsp;-&nbsp;</p>
          <?php  } ?>
          <?php if(isset($this->staticsActive)):?>
          <?php if(isset($this->viewActive)):?>
          <span><i class="sesbasic_icon_view"></i> <?php echo $this->translate(array('%s view', '%s views', $this->sesblog->view_count), $this->locale()->toNumber($this->sesblog->view_count)) ?> &nbsp;-&nbsp; </span>
          <?php endif;?>
          <?php if(isset($this->commentActive)):?>
          <span><i class="sesbasic_icon_comment-o"></i><?php echo $this->translate(array('%s Comment', '%s Comments', $this->sesblog->comment_count), $this->locale()->toNumber($this->sesblog->comment_count)) ?>&nbsp;-&nbsp;</span>
          <?php endif;?>
          <?php if(isset($this->likeActive)):?>
          <span><i class="sesbasic_icon_like_o"></i><?php echo $this->translate(array('%s Like', '%s Likes', $this->sesblog->like_count), $this->locale()->toNumber($this->sesblog->like_count)) ?></span>
          <?php endif;?>
          <?php if($this->isAllowReview && isset($this->reviewActive)):?>
          &nbsp;-&nbsp; <span><i class="far fa-star"></i><?php echo $this->translate(array('%s Review', '%s Reviews', $this->reviewCount), $this->locale()->toNumber($this->reviewCount)) ?></span>
          <?php endif;?>
          <?php endif;?>
          </p>
        </div>
      </div>
      <?php if(isset($this->photoActive) && $this->sesblog->photo_id):?>
      <div class="sesblog_blog_image clear" style="height: <?php echo $this->image_height; ?>px;overflow: hidden;"> <img src="<?php echo Engine_Api::_()->storage()->get($this->sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt=""> </div>
      <?php endif;?>
      <div class="sesblog_list_labels">
        <?php if($item->sponsored == 1):?>
        <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
        <?php endif;?>
        <?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
        <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
        <?php endif;?>
        <?php if(isset($this->verifiedLabelActive) && $item->verified == 1):?>
        <p class="sesblog_label_verified"><?php echo $this->translate('VERIFIED');?></p>
        <?php endif;?>
      </div>
    </div>
    <div class="sesblog_content_four">
      <div>
        <?php if(isset($this->descriptionActive)):?>
        <?php if($this->sesblog->cotinuereading){
					$check = true;
					$style = 'style="height:400px; overflow:hidden;"';
				}else{
					$check = false;
					$style = '';
				} ?>
        <div class="rich_content_body" style="visibility:hidden"><?php echo htmlspecialchars_decode($this->sesblog->body);?></div>
        <?php if($check): ?>
        <div class="sesblog_entrylist_entry_body sesblog_morebtn" id="sesblog_morebtn" style="display:none"><a href="javascript:void(0);" onclick="continuereading();"><?php echo $this->translate("Continue Reading"); ?></a></div>
        <div class="sesblog_lessbtn" id="sesblog_lessbtn" style="display:none"><a href="javascript:void(0);" onclick="showless();"><?php echo $this->translate("Show Less"); ?></a></div>
        <?php endif; ?>
        <?php if(in_array('tags', $allParams['show_criteria'])) { ?>
        <?php if (count($this->sesblogTags )):?>
        <span class="sesblog_profile_tags">
        <?php foreach ($this->sesblogTags as $tag): ?>
        <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
        <?php endforeach; ?>
        </span>
        <?php endif; ?>
        <?php } ?>
        <?php endif;?>
        <div class="sesblog_social_tabs sesbasic_clearfix">
          <?php if($this->viewer_id){  ?>
          <?php if(isset($this->likeButtonActive)):?>
          <a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog <?php echo ($this->LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $this->sesblog->like_count; ?></span></a>
          <?php endif;?>
          <?php if(isset($this->favouriteButtonActive) && $this->coreSettings->getSetting('sesblog.enable.favourite', 1)):?>
          <a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-heart"></i><span><?php echo $this->sesblog->favourite_count; ?></span></a>
          <?php endif;?>
          <?php }  else {  ?>
          <?php if(isset($this->likeButtonActive) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
          <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($this->LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $this->likeClass;?>"></i><span><?php echo $this->translate($this->likeText);?></span></a>
          <?php } ?>
          <?php if(isset($this->favouriteButtonActive) && $this->coreSettings->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
          <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span>
          <?php if($this->favStatus):?>
          <?php echo $this->translate('Un-Favourite');?>
          <?php else:?>
          <?php echo $this->translate('Favourite');?>
          <?php endif;?>
          </span></a>
          <?php } ?>
          <?php } ?>
          <?php if(isset($this->socialShareActive)):?>
          <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $this->sesblog, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
          <?php endif;?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif;?>
<script type="text/javascript">
    var allblogidjson=<?php  echo json_encode(Engine_Api::_()->getDbtable('blogs', 'sesblog')->getBlogIdForScroll($this->sesblog->blog_id,$this->sesblog->category_id));  ?>;
    var allid=JSON.parse(JSON.stringify(allblogidjson));
    window.addEvent('domready', function() {
      var height = sesJqueryObject('.rich_content_body').height();
      <?php if($this->sesblog->cotinuereading && $this->sesblog->continue_height) { ?>
      if(height > '<?php echo $this->sesblog->continue_height; ?>'){
        sesJqueryObject('.sesblog_morebtn').css("display","block");
        sesJqueryObject('.rich_content_body').css("height",'<?php echo $this->sesblog->continue_height; ?>');
        sesJqueryObject('.rich_content_body').css("overflow","hidden");
      }
      <?php } ?>
      sesJqueryObject('.rich_content_body').css("visibility","visible");
    });
  

  $$('.core_main_sesblog').getParent().addClass('active');
  sesJqueryObject('.sesblog_comment').click(function() {
    sesJqueryObject('.comments_options').find('a').eq(0).trigger('click');
    sesJqueryObject('#adv_comment_subject_btn_<?php echo $this->sesblog->blog_id; ?>').trigger('click');
  });
	
	function tagAction(tag_id){
		window.location.href = '<?php echo $this->url(array("action"=>"browse"),"sesblog_general",true); ?>'+'?tag_id='+tag_id;
	}
	var logincheck = '<?php echo $this->coreSettings->getSetting('sesblog.login.continuereading', 1); ?>';
	
	var viwerId = <?php echo $this->viewer_id ?>;
	
	function continuereading(){

    var fornonlogin='<?php echo Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'cotinuereading'); ?>';
		if(logincheck>0 && !viwerId){
      if(fornonlogin>0) {
        nonlogisession(window.location.href);
      }
      window.location.href = en4.core.baseUrl +'login';
		} else {
			sesJqueryObject('.rich_content_body').css('height', 'auto');
			sesJqueryObject('.sesblog_morebtn').hide();
			sesJqueryObject('.sesblog_lessbtn').show();
		}
	}
	
  function showless(){
    sesJqueryObject('.rich_content_body').css('height', '<?php echo $this->sesblog->continue_height; ?>');
    sesJqueryObject('.sesblog_morebtn').show();
    sesJqueryObject('.sesblog_lessbtn').hide();
	}
	
    sesJqueryObject(function(){
        sesJqueryObject(window).scroll(function(){
            if(allid.length>0) {
                var id=allid.pop()['blog_id'];
                var ajaxurl = en4.core.baseUrl + "sesblog/index/viewpagescroll";
                sesJqueryObject.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {settings: <?php  echo json_encode($this->allParams); ?>, id:id },
                    success: function (html) {
                        //sesJqueryObject(".layout_sesblog_view_blog").append(html);
                    }
                });
            }

        });

    });

</script> 
