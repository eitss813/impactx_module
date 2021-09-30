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

<?php  $view=Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;  ?>
<?php $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/infinite-scroll.js'); ?>
<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $sesblog->getHref()); ?>
<?php $isBlogAdmin = Engine_Api::_()->sesblog()->isBlogAdmin($sesblog, 'edit');?>
<?php $reviewCount = Engine_Api::_()->sesblog()->getTotalReviews($sesblog->blog_id);?>
<?php $canComment =  $sesblog->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($sesblog->blog_id,$sesblog->getType()); ?> 
<?php $likeClass = (!$LikeStatus) ? 'fa-thumbs-up' : 'fa-thumbs-down' ;?>
<?php $likeText = ($LikeStatus) ?  $view->translate('Unlike') : $view->translate('Like');?>
<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$sesblog->blog_id)); ?>
<?php $isAllowReview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.review', 1);?>
<?php $enableSharng = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1);?>
<?php if($sesblog->style == 1):?>
	<div class="sesblog_layout_contant sesbasic_clearfix sesbasic_bxs">
	  <?php if(isset($params['titleActive'])):?>
			<h2><?php echo $sesblog->getTitle() ?></h2>
		<?php endif;?>
		<div class="sesblog_entrylist_entry_date">
    	<p><?php echo $view->translate('<i>Posted by -</i>');?> <?php echo $view->htmlLink($sesblog->getOwner()->getHref(), $sesblog->getOwner()->getTitle()) ?> &nbsp;-&nbsp;</p>
			<p><?php echo $view->translate('<i>on - </i>') ?><?php echo $view->timestamp($sesblog->creation_date) ?><?php if( $category ): ?>&nbsp;-&nbsp;</p>
				<p><?php echo $view->translate('<i>Filed in - </i>') ?>
				<a href="<?php echo $category->getHref(); ?>"><?php echo $view->translate($category->category_name) ?></a><?php endif; ?>&nbsp;-&nbsp;</p>

          <?php   if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($sesblog->readtime) && !empty($sesblog->readtime)) {  ?>
            <p><?php echo $view->translate('<i>Estimated Reading Time - </i>') ?>
              <?php  echo $sesblog->readtime; ?>
              <?php  } ?>

            <p><?php if (count($sesblog->tags()->getTagMaps() )):?>
				<?php foreach ($sesblog->tags()->getTagMaps() as $tag): ?>
					<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if(isset($params['staticsActive'])):?>
				&nbsp;-&nbsp;</p>

				<p>
					<?php if(isset($params['viewActive'])):?>
						<span><?php echo $view->translate(array('%s View', '%s Views', $sesblog->view_count), $view->locale()->toNumber($sesblog->view_count)) ?>&nbsp;-&nbsp;</span>
					<?php endif;?>
					<?php if(isset($params['commentActive'])):?>
						<span><?php echo $view->translate(array('%s Comment', '%s Comments', $sesblog->comment_count), $view->locale()->toNumber($sesblog->comment_count)) ?>&nbsp;-&nbsp;</span>
					<?php endif;?>
					<?php if(isset($params['likeActive'])):?>
						<span><?php echo $view->translate(array('%s Like', '%s Likes', $sesblog->like_count), $view->locale()->toNumber($sesblog->like_count)) ?></span>
					<?php endif;?>
					<?php if($isAllowReview && isset($params['reviewActive'])):?>
                        &nbsp;-&nbsp;
						<span><?php echo $view->translate(array('%s Review', '%s Reviews', $reviewCount), $view->locale()->toNumber($reviewCount)) ?></span>
					<?php endif;?>
				</p>
			<?php endif;?>
		</div>
		<div class="sesblog_entrylist_entry_body">
		  <?php if(isset($params['photoActive']) && $sesblog->photo_id):?>
				<div class="sesblog_blog_image clear" style="height: <?php echo $params['image_height']; ?>px;overflow: hidden;">
					<img src="<?php echo Engine_Api::_()->storage()->get($sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt="">
				</div>
			<?php endif;?>
			<?php if(isset($params['descriptionActive'])):?>
				<?php if($sesblog->cotinuereading){
					$check = true;
					$style = 'style="height:400px; overflow:hidden;"';
				}else{
					$check = false;
					$style = '';
				} ?>
				<div class="rich_content_body" style="visibility:hidden"><?php echo htmlspecialchars_decode($sesblog->body);?></div>
				<?php if($check): ?>
					<div class="sesblog_morebtn" style="display:none"><a href="javascript:void(0);" onclick="continuereading();"><?php echo $view->translate("Continue Reading"); ?></a></div>
				<?php endif; ?>
			<?php endif;?>
		</div>
    <div class="sesblog_footer_two_blog clear">
      <?php if(isset($params['ratingActive'])):?>
				<div class="sesbasic_rating_star floatL">
					<?php $ratingCount = $sesblog->rating; $x=0; ?>
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
			<div class="sesblog_deshboard_blog floatR">
				<ul>
					<?php if(isset($params['ownerOptionsActive']) && $isBlogAdmin):?>
          	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.subblog', 1)){ ?>
						<li><a href="<?php echo $view->url(array('action' => 'create', 'parent_id' => $sesblog->blog_id), 'sesblog_general', 'true');?>"><i class="fa fa-edit"></i><?php echo $view->translate('Create Sub Blog');?></a></li>
           <?php } ?>
						<li><a href="<?php echo $view->url(array('action' => 'edit', 'blog_id' => $sesblog->custom_url), 'sesblog_dashboard', 'true');?>"><i class="fa fa-edit"></i><?php echo $view->translate('Dashboard');?></a></li>
						<li><a href="<?php echo $view->url(array('action' => 'delete', 'blog_id' => $sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $view->translate('Delete This Blog');?></a></li>
					<?php endif;?>
					<?php if($params['viewer_id'] && isset($params['smallShareButtonActive']) && $enableSharng):?>
						<li><a href="<?php echo $view->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $sesblog->getType(), "id" => $sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i> <?php echo $view->translate('Share');?></a></li>
					<?php endif;?>

                    <?php if($params['viewer_id']){  ?>
					<?php if($params['viewer_id'] && $params['viewer_id'] != $sesblog->owner_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)):?>
						<li><a href="<?php echo $view->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
					<?php endif;?>

                  <?php  } else { ?>
                  <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)) { ?>
                        <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
                  <?php  } ?>
                <?php } ?>

                 <?php if($params['viewer_id']){  ?>
					<?php if(isset($params['postCommentActive']) && $canComment):?>
						<li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
                   <?php endif;?>
                   <?php  } else { ?>
                   <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($params['postCommentActive'])) { ?>
                         <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
                   <?php  } ?>
                   <?php  } ?>
				</ul>
			</div>
      <div class="sesblog_shear_blog sesbasic_bxs">
        <?php if(isset($params['socialShareActive']) && $enableSharng):?>
        
          <?php echo $view->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $sesblog, 'socialshare_enable_plusicon' => $params['socialshare_enable_plusicon'], 'socialshare_icon_limit' => $params['socialshare_icon_limit'])); ?>
			  <?php endif;?>
				<?php if($params['viewer_id'] && $enableSharng && isset($params['shareButtonActive'])):?>
						<a href="<?php echo $view->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $sesblog->getType(), "id" => $sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="share_icon sesbasic_icon_btn smoothbox"><i class="fa fa-share "></i><span><?php echo $view->translate('Share');?></span></a>
				<?php endif;?>
			<?php if($params['viewer_id']) { ?>
					<?php if(isset($params['likeButtonActive']) && $canComment):?>
							<a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i><span><?php echo $view->translate($likeText);?></span></a>
					<?php endif;?>
					<?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)):?>
							<a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>
					<?php endif;?>
        <?php } else {  ?>
              <?php if(isset($params['likeButtonActive']) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
                    <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i><span><?php echo $view->translate($likeText);?></span></a>
              <?php } ?>
              <?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
                    <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>
              <?php } ?>

        <?php   } ?>
			</div>
		</div>
	</div>
<?php elseif($sesblog->style == 2):?>
	<!--second profile blog start-->
	<div class="sesblog_profile_layout_second sesbasic_clearfix sesbasic_bxs">

    <?php if(isset($params['photoActive']) && $sesblog->photo_id):?>
      <div class="sesblog_profile_layout_second_image clear" >
          <a href="<?php echo $sesblog->getHref(); ?>"><img  src="<?php echo Engine_Api::_()->storage()->get($sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt=""></a>
      </div>
    <?php endif;?>
		
	  <?php if( $category ): ?>
    				<?php echo $view->translate('') ?>
  	<div class="sesblog_category_teg">
     <p>   
				<a href="<?php echo $category->getHref(); ?>"><?php echo $view->translate($category->category_name) ?></a>
			</p>
		</div><?php endif; ?>
		<?php if(isset($params['titleActive'])):?>
			<h2><?php echo $sesblog->getTitle() ?></h2>
		<?php endif;?>
		<div class="sesblog_entrylist_entry_date">
			<p><?php echo $view->translate('<i>Posted by -</i>');?> <?php echo $view->htmlLink($sesblog->getOwner()->getHref(), $sesblog->getOwner()->getTitle()) ?> &nbsp;\&nbsp;</p>
      <p><?php echo $view->translate('<i class="far fa-calendar"></i>') ?>&nbsp;
			<?php echo $view->timestamp($sesblog->publish_date) ?>
			<?php  ?>
			<?php if (count($sesblog->tags()->getTagMaps() )):?> &nbsp;\&nbsp;
				</p>
        <p><?php echo $view->translate('<i>Filed in - </i>') ?>
				<a href="<?php echo $category->getHref(); ?>"><?php echo $view->translate($category->category_name) ?></a>
        &nbsp;\&nbsp;</p>
        <p>
        <?php foreach ($sesblog->tags()->getTagMaps() as $tag): ?>
					<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if(isset($params['staticsActive'])):?>
				&nbsp;\&nbsp;</p>
				<p>
				<?php if(isset($params['viewActive'])):?>
					<span><i class="fa fa-eye"></i>&nbsp;
					<?php echo $view->translate(array('%s view', '%s views', $sesblog->view_count), $view->locale()->toNumber($sesblog->view_count)) ?>&nbsp;\&nbsp;</span>
				<?php endif;?>
				<?php if(isset($params['commentActive'])):?>
					<span><i class="fa fa-comment"></i>&nbsp;<?php echo $view->translate(array('%s comment', '%s comments', $sesblog->comment_count), $view->locale()->toNumber($sesblog->comment_count)) ?>&nbsp;\&nbsp;</span>
				<?php endif;?>
				<?php if(isset($params['likeActive'])):?>
					<span><i class="fa fa-thumbs-up"></i>&nbsp;<?php echo $view->translate(array('%s like', '%s likes', $sesblog->like_count), $view->locale()->toNumber($sesblog->like_count)) ?></span>
				<?php endif;?>
				<?php if($isAllowReview && isset($params['reviewActive'])):?>
                    &nbsp;\&nbsp;
					<span><i class="fa fa-edit"></i>&nbsp;<?php echo $view->translate(array('%s review', '%s reviews', $reviewCount), $view->locale()->toNumber($reviewCount)) ?></span>
				<?php endif;?>
				</p>
      <?php endif;?>
		</div>
		<?php if(isset($params['descriptionActive'])):?>
			<?php if($sesblog->cotinuereading){
					$check = true;
					$style = 'style="height:400px; overflow:hidden;"';
				}else{
					$check = false;
					$style = '';
				} ?>
				<div class="rich_content_body" style="visibility:hidden"><?php echo htmlspecialchars_decode($sesblog->body);?></div>
				<?php if($check): ?>
					<div class="sesblog_entrylist_entry_body sesblog_morebtn" style="display:none"><a href="javascript:void(0);" onclick="continuereading();"><?php echo $view->translate("Continue Reading"); ?></a></div>
				<?php endif; ?>
		<?php endif;?>
    <div class="sesblog_view_footer_top clear sesbasic_clearfix">
      <?php if(isset($params['ratingActive'])):?>
				<div class="sesbasic_rating_star floatL">
					<?php $ratingCount = $sesblog->rating; $x=0; ?>
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
      <div class="sesblog_view_footer_links floatR">
				<ul>
					<?php if(isset($params['ownerOptionsActive']) && $isBlogAdmin):?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.subblog', 1)){ ?>
						<li><a href="<?php echo $view->url(array('action' => 'create', 'parent_id' => $sesblog->blog_id), 'sesblog_general', 'true');?>"><i class="fa fa-edit"></i><?php echo $view->translate('Create Sub Blog');?>
            </a></li>
          <?php } ?>
						<li><a href="<?php echo $view->url(array('action' => 'edit', 'blog_id' => $sesblog->custom_url), 'sesblog_dashboard', 'true');?>"><i class="fa fa-edit"></i><?php echo $view->translate('Dashboard');?></a></li>
						<li><a href="<?php echo $view->url(array('action' => 'delete', 'blog_id' => $sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $view->translate('Delete This Blog');?></a></li>
					<?php endif;?>
					<?php if($params['viewer_id']):?>
						<li><a href="<?php echo $view->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $sesblog->getType(), "id" => $sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i><?php echo $view->translate('Share');?></a></li>
					<?php endif;?>
                  <?php if($params['viewer_id']){ ?>
					<?php if($params['viewer_id'] && $params['viewer_id'] != $sesblog->owner_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)):?>
						<li><a href="<?php echo $view->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
					<?php endif;?>
				    <?php  } else { ?>
                      <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)) { ?>
                          <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
                    <?php  } ?>
				    <?php } ?>
				</ul>
			</div>
    </div>
    <div class="sesblog_view_footer_top_bottom clear sesbasic_clearfix">
			<div class="sesblog_view_footer_links floatL">
        <ul>
          <?php if($params['viewer_id']){ ?>
						<?php if(isset($params['likeButtonActive']) && $canComment):?>
							<li><a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesblog_like_link  sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view"><i class="<?php if($LikeStatus):?>fa fa-thumbs-down<?php else:?>fa fa-thumbs-up<?php endif;?>"></i><span><?php if($LikeStatus):?><?php echo $view->translate('Unlike');?><?php else:?><?php echo $view->translate('Like');?><?php endif;?></span></a>&nbsp;/&nbsp;</li>
						<?php endif;?>
						<?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)):?>
							<li><a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesblog_fav_link sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>&nbsp;/&nbsp;</li>
						<?php endif;?>
					<?php } else {  ?>

            <?php if(isset($params['likeButtonActive']) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
            <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i><span><?php echo $view->translate($likeText);?></span></a>
          <?php } ?>
          <?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
            <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>
          <?php } ?>

              <?php } ?>

          <?php if($params['viewer_id']){  ?>
            <?php if(isset($params['postCommentActive']) && $canComment):?>
                  <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
            <?php endif;?>
          <?php  } else { ?>
            <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($params['postCommentActive'])) { ?>
                  <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
            <?php  } ?>
          <?php  } ?>
        </ul>
			</div>
			<?php if(isset($params['socialShareActive'])):?>
				<div class="sesblog_view_footer_social_share floatR">
					<?php  echo $view->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $sesblog, 'socialshare_enable_plusicon' => $params['socialshare_enable_plusicon'], 'socialshare_icon_limit' => $params['socialshare_icon_limit'])); ?>
				</div>
			<?php endif;?>
		</div>
	</div>
	<!--second profile blog end-->
<?php elseif($sesblog->style == 3):?>
	<!--three profile blog start-->
	<div class="sesblog_profile_layout_three sesbasic_clearfix sesbasic_bxs">
		<?php if(isset($params['ratingActive'])):?>
			<div class="sesbasic_rating_star floatR">
				<?php $ratingCount = $sesblog->rating; $x=0; ?>
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
    <?php if(isset($params['titleActive'])):?>
			<h2><?php echo $sesblog->getTitle() ?></h2>
		<?php endif;?>
		<div class="sesblog_entrylist_entry_date">
      <?php if( $category ): ?>
				<p class="catogery floatR">
				<?php echo $view->translate('<i class="fa fa-folder"></i>') ?>
				<a href="<?php echo $category->getHref(); ?>"><?php echo $view->translate($category->category_name) ?></a>
				</p>
      <?php endif; ?>
			<p class="">
      	<span><i class=" fa fa-user"></i> <?php echo $view->translate('');?> <?php echo $view->htmlLink($sesblog->getOwner()->getHref(), $sesblog->getOwner()->getTitle()) ?></span>
      	<span><?php echo $view->translate('<i class="far fa-calendar"></i>') ?>&nbsp;<?php echo $view->timestamp($sesblog->publish_date) ?></span>
      	<?php if(isset($params['staticsActive'])):?>
      	  <?php if(isset($params['viewActive'])):?>
						<span><i class="fa fa-eye"></i> <?php echo $view->translate(array('%s view', '%s views', $sesblog->view_count), $view->locale()->toNumber($sesblog->view_count)) ?></span>
					<?php endif;?>
					<?php if(isset($params['commentActive'])):?>
						<span><i class="fa fa-comment"></i><?php echo $view->translate(array('%s Comment', '%s Comments', $sesblog->comment_count), $view->locale()->toNumber($sesblog->comment_count)) ?></span>
					<?php endif;?>
					<?php if(isset($params['likeActive'])):?>
						<span><i class="fa fa-thumbs-up"></i><?php echo $view->translate(array('%s Like', '%s Likes', $sesblog->like_count), $view->locale()->toNumber($sesblog->like_count)) ?></span>
					<?php endif;?>
					<?php if($isAllowReview && isset($params['reviewActive'])):?>
					<span><i class="fa fa-edit"></i><?php echo $view->translate(array('%s Review', '%s Reviews', $reviewCount), $view->locale()->toNumber($reviewCount)) ?></span>
					<?php endif;?>
				<?php endif;?>
				<?php if (count($sesblog->tags()->getTagMaps() )):?>
					<span>
						<i class="fa fa-tag"></i>
						<?php foreach ($sesblog->tags()->getTagMaps() as $tag): ?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
					<?php endforeach; ?>
					</span>
				<?php endif; ?>
			</p>
		</div>
		<div class="sesblog_entrylist_entry_body">
		  <?php if(isset($params['photoActive']) && $sesblog->photo_id):?>
				<div class="sesblog_blog_image clear" style="height: <?php echo $params['image_height'] ?>px;overflow: hidden;">
					<img src="<?php echo Engine_Api::_()->storage()->get($sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt="">
				</div>
			<?php endif;?>
			<?php if(isset($params['descriptionActive'])):?>
				<?php if($sesblog->cotinuereading){
					$check = true;
					$style = 'style="height:400px; overflow:hidden;"';
				}else{
					$check = false;
					$style = '';
				} ?>
				<div class="rich_content_body" style="visibility:hidden"><?php echo htmlspecialchars_decode($sesblog->body);?></div>
				<?php if($check): ?>
					<div class="sesblog_entrylist_entry_body sesblog_morebtn" style="display:none"><a href="javascript:void(0);" onclick="continuereading();"><?php echo $view->translate("Continue Reading"); ?></a></div>
				<?php endif; ?>
			<?php endif;?>
		</div>
		<div class="sesblog_three_blog_footer">
    	<div class="sesblog_three_blog_footer_links floatL">
      <ul>

      <?php if($params['viewer_id']){  ?>
            <?php if(isset($params['likeButtonActive']) && $canComment):?>
                        <li><a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesblog_like_link sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view"><i class="<?php if($LikeStatus):?>fa fa-thumbs-down<?php else:?>fa fa-thumbs-up<?php endif;?>"></i><span><?php if($LikeStatus):?><?php echo $view->translate('Unlike');?><?php else:?><?php echo $view->translate('Like');?><?php endif;?></span></a>&nbsp;|&nbsp;</li>
            <?php endif;?>
            <?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)):?>
                        <li><a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesblog_fav_link sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>&nbsp;|&nbsp;	</li>
            <?php endif;?>
        <?php }  else {  ?>

        <?php if(isset($params['likeButtonActive']) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
              <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i><span><?php echo $view->translate($likeText);?></span></a>
        <?php } ?>
        <?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
              <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>
        <?php } ?>

      <?php } ?>


        <?php if($params['viewer_id']){  ?>
          <?php if(isset($params['postCommentActive']) && $canComment):?>
                <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
          <?php endif;?>
        <?php  } else { ?>
          <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($params['postCommentActive'])) { ?>
                <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
          <?php  } ?>
        <?php  } ?>
      </ul>
			</div>
      <div class="sesblog_three_blog_footer_links floatR">
				<ul>
					<?php if(isset($params['ownerOptionsActive']) && $isBlogAdmin):?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.subblog', 1)){ ?>
						<li><a href="<?php echo $view->url(array('action' => 'create', 'parent_id' => $sesblog->blog_id), 'sesblog_general', 'true');?>" class=""><i class="fa fa-edit"></i><?php echo $view->translate('Create Sub Blog');?>
            </a>&nbsp;|&nbsp;</li>
          <?php } ?>
						<li><a href="<?php echo $view->url(array('action' => 'edit', 'blog_id' => $sesblog->custom_url), 'sesblog_dashboard', 'true');?>" class=""><i class="fa fa-edit"></i><?php echo $view->translate('Dashboard');?></a>&nbsp;|&nbsp;</li>
						<li><a href="<?php echo $view->url(array('action' => 'delete', 'blog_id' => $sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox"><i class="fa fa-trash "></i><?php echo $view->translate('Delete This Blog');?></a>&nbsp;|&nbsp;</li>
					<?php endif;?>
					<?php if($params['viewer_id']):?>
						<li><a href="<?php echo $view->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $sesblog->getType(), "id" => $sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox share_icon"><i class="fa fa-share "></i><?php echo $view->translate('Share');?></a></li>
					<?php endif;?>
                <?php if($params['viewer_id']){  ?>
					<?php if($params['viewer_id'] && $params['viewer_id'] != $sesblog->owner_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)):?>
						<li><a href="<?php echo $view->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $sesblog->getGuid()),'default', true);?>" class="smoothbox report_link"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
					<?php endif;?>
              <?php  } else { ?>
              <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)) { ?>
                    <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
              <?php  } ?>
            <?php } ?>
				</ul>
			</div>
    </div>
    <?php if(isset($params['socialShareActive'])):?>
			<div class="sesblog_footer_blog clear">
				<p><?php echo $view->translate('SHARE THIS STORY');?></p>
				<div class="sesblog_footer_blog_social_share sesbasic_clearfix">
            <?php  echo $view->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $sesblog, 'socialshare_enable_plusicon' => $params['socialshare_enable_plusicon'], 'socialshare_icon_limit' => $params['socialshare_icon_limit'])); ?>
				</div>
			</div>
		<?php endif;?>
	</div>
	<!--three profile blog start-->
<?php elseif($sesblog->style == 4):?>
	<div class="sesblog_profile_layout_four sesbasic_clearfix sesbasic_bxs">
	 <?php if(isset($params['ratingActive'])):?>
			<div class="sesbasic_rating_star floatR">
				<?php $ratingCount = $sesblog->rating; $x=0; ?>
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
    <?php if(isset($params['titleActive'])):?>
			<h2><?php echo $sesblog->getTitle() ?></h2>
		<?php endif;?>
		<div class="sesblog_entrylist_entry_date">
			<span class="sesblog_entry_border"></span>
			<p>
				<span>
        <?php echo $view->translate('');?>&nbsp; <?php echo $view->htmlLink($sesblog->getOwner()->getHref(), 
        $view->itemPhoto($sesblog->getOwner()),
				array('class' => 'sesblogs_gutter_photo')) ?> <?php echo $view->htmlLink($sesblog->getOwner()->getHref(), $sesblog->getOwner()->getTitle()) ?> &nbsp;-&nbsp;</span>
				<span>
					<?php echo $view->translate('<i class="far fa-calendar"></i>') ?>
					<?php echo $view->timestamp($sesblog->creation_date) ?>
					&nbsp;-&nbsp;
        </span>
        <?php  ?>
				<?php if( $category ): ?>
					<span>
					<?php echo $view->translate('<i class="fa fa-tag"></i>') ?>
					<a href="<?php echo $category->getHref(); ?>"><?php echo $view->translate($category->category_name) ?></a>
				</span>
        <?php endif; ?>
        <?php if (count($sesblog->tags()->getTagMaps() )):?>
					<span>
          <?php foreach ($sesblog->tags()->getTagMaps() as $tag): ?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'><?php echo $tag->getTag()->text?></a>&nbsp;
					<?php endforeach; ?>
					&nbsp;-&nbsp;
        </span>
        <?php endif; ?>
				<?php if(isset($params['staticsActive'])):?>
				  <?php if(isset($params['viewActive'])):?>
						<span><i class="fa fa-eye"></i>
						<?php echo $view->translate(array('%s view', '%s views', $sesblog->view_count), $view->locale()->toNumber($sesblog->view_count)) ?>
						&nbsp;-&nbsp;
						</span>
          <?php endif;?>
          <?php if(isset($params['commentActive'])):?>
						<span><i class="far fa-comment"></i><?php echo $view->translate(array('%s Comment', '%s Comments', $sesblog->comment_count), $view->locale()->toNumber($sesblog->comment_count)) ?>&nbsp;-&nbsp;</span>
					<?php endif;?>
					<?php if(isset($params['likeActive'])):?>
						<span><i class="far fa-thumbs-up"></i><?php echo $view->translate(array('%s Like', '%s Likes', $sesblog->like_count), $view->locale()->toNumber($sesblog->like_count)) ?></span>
					<?php endif;?>
					<?php if($isAllowReview && isset($params['reviewActive'])):?>
                        &nbsp;-&nbsp;
						<span><i class="fa fa-edit"></i><?php echo $view->translate(array('%s Review', '%s Reviews', $reviewCount), $view->locale()->toNumber($reviewCount)) ?></span>
					<?php endif;?>
				<?php endif;?>
			</p>
		</div>
		<div class="sesblog_entrylist_entry_body">
		  <?php if(isset($params['photoActive']) && $sesblog->photo_id):?>
				<div class="sesblog_blog_image clear" style="height: <?php echo $params['image_height']; ?>px;overflow: hidden;">
					<img src="<?php echo Engine_Api::_()->storage()->get($sesblog->photo_id)->getPhotoUrl('thumb.main'); ?>" alt="">
				</div>
			<?php endif;?>
		<div class="sesblog_social_tabs sesbasic_clearfix">
          <?php if($params['viewer_id']){  ?>
            <?php if(isset($params['postCommentActive']) && $canComment):?>
                  <li><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
            <?php endif;?>
          <?php  } else { ?>
            <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'comment') && isset($params['postCommentActive'])) { ?>
                  <li><a onclick="nonlogisession(window.location.href);" href="javascript:void(0);"><i class="sesblog_comment fa fa-commenting"></i><?php echo $view->translate('Post Comment');?></a></li>
            <?php  } ?>
          <?php  } ?>
          <?php if($params['viewer_id']){  ?>
				<?php if(isset($params['likeButtonActive'])):?>
					<a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $sesblog->like_count; ?></span></a>
				<?php endif;?>
				<?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)):?>
						<a href="javascript:;" data-url="<?php echo $sesblog->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog <?php echo ($favStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-heart"></i><span><?php echo $sesblog->favourite_count; ?></span></a>
				<?php endif;?>

              <?php }  else {  ?>

              <?php if(isset($params['likeButtonActive']) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'like')) { ?>
                    <a href="javascript:;" onclick="nonlogisession(window.location.href);" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_like_sesblog_blog_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="fa <?php echo $likeClass;?>"></i><span><?php echo $view->translate($likeText);?></span></a>
              <?php } ?>
              <?php if(isset($params['favouriteButtonActive']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1) &&  Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'favourite')) {  ?>
                    <a href="javascript:;" onclick="nonlogisession(window.location.href);"  class="sesbasic_icon_btn sesbasic_icon_fav_btn  sesblog_favourite_sesblog_blog_<?php echo $sesblog->blog_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php if($favStatus):?><?php echo $view->translate('Un-Favourite');?><?php else:?><?php echo $view->translate('Favourite');?><?php endif;?></span></a>
              <?php } ?>

            <?php } ?>
				<?php if(isset($params['socialShareActive'])):?>
          <?php  echo $view->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $sesblog, 'socialshare_enable_plusicon' => $params['socialshare_enable_plusicon'], 'socialshare_icon_limit' => $params['socialshare_icon_limit'])); ?>
				<?php endif;?>
		</div>
			<?php if(isset($params['descriptionActive'])):?>
				<?php if($sesblog->cotinuereading){
					$check = true;
					$style = 'style="height:400px; overflow:hidden;"';
				}else{
					$check = false;
					$style = '';
				} ?>
				<div class="rich_content_body" style="visibility:hidden"><?php echo htmlspecialchars_decode($sesblog->body);?></div>
				<?php if($check): ?>
					<div class="sesblog_entrylist_entry_body sesblog_morebtn" style="display:none"><a href="javascript:void(0);" onclick="continuereading();"><?php echo $view->translate("Continue Reading"); ?></a></div>
				<?php endif; ?>
			<?php endif;?>
		</div>
    <div class="sesblog_deshboard_links ">
        <?php if(isset($params['postCommentActive']) && $canComment):?>
					<p class="profile_layout_fore_post_com floatL"><a href="javascript:void(0);" class="sesblog_comment"><i class="sesblog_comment fa fa-commenting"></i><span><?php echo $view->translate('Post Comment');?></span></a></p>
				<?php endif;?>
				<ul class="floatR">
					<?php if(isset($params['ownerOptionsActive']) && $isBlogAdmin):?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.subblog', 1)){ ?>
						<li><a href="<?php echo $view->url(array('action' => 'create', 'parent_id' => $sesblog->blog_id), 'sesblog_general', 'true');?>" class="sesbasic_button "><i class="fa fa-edit"></i><?php echo $view->translate('Create Sub Blog');?>
            </a></li>
          <?php } ?>
						<li><a href="<?php echo $view->url(array('action' => 'edit', 'blog_id' => $sesblog->custom_url), 'sesblog_dashboard', 'true');?>" class="sesbasic_button "><i class="fa fa-edit"></i><?php echo $view->translate('Dashboard');?></a></li>
						<li><a href="<?php echo $view->url(array('action' => 'delete', 'blog_id' => $sesblog->getIdentity()), 'sesblog_specific', true);?>" class="smoothbox sesbasic_button "><i class="fa fa-trash "></i><?php echo $view->translate('Delete This Blog');?></a></li>
					<?php endif;?>
					<?php if($params['viewer_id']):?>
						<li><a href="<?php echo $view->url(array("module" => "activity","controller" => "index","action" => "share", "type" => $sesblog->getType(), "id" => $sesblog->getIdentity(), "format" => "smoothbox"), 'default', true);?>" class="smoothbox sesbasic_button  share_icon"><i class="fa fa-share "></i><?php echo $view->translate('Share');?></a></li>
					<?php endif;?>

                  <?php if($params['viewer_id']){ ?>
					<?php if($params['viewer_id'] && $params['viewer_id'] != $sesblog->owner_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)):?>
						<li><a href="<?php echo $view->url(array("module" => "core","controller" => "report","action" => "create", 'subject' => $sesblog->getGuid()),'default', true);?>" class="smoothbox sesbasic_button report_link"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
					<?php endif;?>
                  <?php  } else { ?>
                  <?php if(Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'claim') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)) { ?>
                        <li><a onclick="nonlogisession(window.location.href);" href="javascript:;"><i class="fa fa-flag"></i><?php echo $view->translate('Report');?></a></li>
                  <?php  } ?>
                <?php } ?>
				</ul>
			</div>
	</div>

<?php endif;?>

<script type="text/javascript">
    var allblogid=[];
    allblogid[allblogid.length]=<?php  echo $sesblog->blog_id;  ?>;
    window.addEvent('domready', function() {
      var height = sesJqueryObject('.rich_content_body').height();
      <?php if($sesblog->cotinuereading && $sesblog->continue_height) { ?>
      if(height > '<?php echo $sesblog->continue_height; ?>'){
        sesJqueryObject('.sesblog_morebtn').css("display","block");
        sesJqueryObject('.rich_content_body').css("height",'<?php echo $sesblog->continue_height; ?>');
        sesJqueryObject('.rich_content_body').css("overflow","hidden");
      }
      <?php } ?>
      sesJqueryObject('.rich_content_body').css("visibility","visible");
    });
  

  $$('.core_main_sesblog').getParent().addClass('active');
  sesJqueryObject('.sesblog_comment').click(function() {
    sesJqueryObject('.comments_options').find('a').eq(0).trigger('click');
    sesJqueryObject('#adv_comment_subject_btn_<?php echo $sesblog->blog_id; ?>').trigger('click');
  });
	
	function tagAction(tag_id){
		window.location.href = '<?php echo $view->url(array("action"=>"browse"),"sesblog_general",true); ?>'+'?tag_id='+tag_id;
	}
	var logincheck = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.login.continuereading', 1); ?>';
	
	var viwerId = <?php echo $params['viewer_id'] ?>;
	function continuereading(){

	    var fornonlogin='<?php echo Engine_Api::_()->authorization()->getPermission(5, 'sesblog_blog', 'cotinuereading'); ?>';
		
		if(logincheck>0 && !viwerId){
		    if(fornonlogin>0) {
                nonlogisession(window.location.href);
            }
            window.location.href = en4.core.baseUrl +'login';
		}else{
			sesJqueryObject('.rich_content_body').css('height', 'auto');
			sesJqueryObject('.sesblog_morebtn').hide();
		}
	}

</script>
