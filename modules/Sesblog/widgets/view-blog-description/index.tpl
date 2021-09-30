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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/infinite-scroll.js'); ?>
<?php if($this->sesblog->style == 3):?>
<style>
  .sesblog_shear_blog{display:none !important;}
</style>
<?php endif;?>
<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->sesblog->getHref()); ?>
<?php $isBlogAdmin = Engine_Api::_()->sesblog()->isBlogAdmin($this->sesblog, 'edit');?>
<?php $reviewCount = Engine_Api::_()->sesblog()->getTotalReviews($this->sesblog->blog_id);?>
<?php $canComment =  $this->sesblog->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($this->sesblog->blog_id,$this->sesblog->getType()); ?> 
<?php $likeClass = (!$LikeStatus) ? 'fa-thumbs-up' : 'fa-thumbs-down' ;?>
<?php $likeText = ($LikeStatus) ?  $this->translate('Unlike') : $this->translate('Like');?>
<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$this->sesblog->blog_id)); ?>
<?php $isAllowReview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.review', 1);?>
<?php $enableSharng = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1);?>
   <div class="sesblog_profile_layout_second">
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
		<?php endif;?>
		<?php if($this->tagsActive) { ?>
      <p class="sesblog_profile_tags">
      <?php foreach ($this->sesblogTags as $tag): ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
          <?php endforeach; ?>
      </p>
    <?php } ?>
      <div class="sesblog_shear_blog sesbasic_bxs">
        <div class="sesblog_second_footer">
        <?php if(isset($this->socialShareActive) && $enableSharng):?>
        
          <?php echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $this->sesblog, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
			  <?php endif;?>
				<?php if($this->viewer_id && $enableSharng && isset($this->shareButtonActive)):?>
						<a href="<?php echo $this->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $this->sesblog->getType(), "id" => $this->sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="share_icon sesbasic_icon_btn smoothbox"><i class="fa fa-share "></i></a>
				<?php endif;?>
			<?php if($this->viewer_id) { ?>
					<?php if(isset($this->likeButtonActive) && $canComment):?>
							<a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i></a>
					<?php endif;?>
					<?php if(isset($this->favouriteButtonActive) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)):?>
							<a href="javascript:;" data-url="<?php echo $this->sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a>
					<?php endif;?>
        <?php } else {  ?>
              <?php if(isset($this->likeButtonActive) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
                    <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i></a>
              <?php } ?>
              <?php if(isset($this->favouriteButtonActive) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
                    <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $this->sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a>
              <?php } ?>

        <?php   } ?>
        </div>
			</div>
      </div>
  
