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

<?php $randonNumber = $this->widgetId; ?>
<?php if(!$this->is_ajax){ ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?> 
<?php if($this->allowedCreate && $this->cancreate && $this->viewer()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.review', 1) && !$this->isReview): ?>
  <div class="sesbasic_profile_tabs_top sesbasic_clearfix">
    <?php echo $this->htmlLink(array('route' => 'sesblogreview_extended','action' => 'create', 'blog_id'=>$this->subject()->getIdentity()), $this->translate('Write a Review'), array('class' => 'sesbasic_button fa fa-plus'));?>
  </div>
<?php endif;?>
<?php } ?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	<?php if(!$this->is_ajax){ ?>
		<div class="sesblog_profile_reviews_filters sesbasic_bxs sesbasic_clearfix">
			<?php echo $this->content()->renderWidget('sesblog.browse-review-search',array('reviewSearch'=>1,'reviewStars'=>1,'reviewRecommended'=>1,'review_title'=>0,'view_type'=>'horizontal','isWidget'=>true,'blog_id'=>$this->subject->getIdentity(),'widgetIdentity'=>$this->identity,'allsetting'=>$this->allparam)); ?>
		</div>
		<ul class="sesblog_review_listing sesbasic_clearfix sesbasic_bxs" id="sesblog_review_listing">
	<?php } ?>
  <?php foreach( $this->paginator as $item ): ?>
  <li class="sesblog_owner_review sesbasic_clearfix">
    <div class="sesblog_review_listing_top sesbasic_clearfix sesblog_review_listing_left_column">
      <?php if(in_array('title', $this->stats)): ?>
      <div class='sesblog_review_listing_title'>
      	<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
      </div>
     <?php endif; ?>
      <div class="sesblog_review_listing_top_info sesbasic_clearfix">      
      <?php if(in_array('postedBy', $this->stats)): ?>
        <div class='sesblog_review_listing_top_info_img'>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
        </div>
       <?php endif; ?>
        <div class='sesblog_review_listing_top_info_cont'>
         <?php if(in_array('postedBy', $this->stats)): ?>
        	<p class="sesblog_review_listing_stats sesbasic_text_light">by <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
         <?php endif; ?>
          |
          <?php if(in_array('creationDate', $this->stats)): ?>
          <span class="sesbasic_text_light">
            <?php echo $this->translate('about');?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            </span>
          </p>
          <?php endif; ?>
          <p class="sesbasic_text_light sesblog_review_listing_stats">
           <?php if(in_array('likeCount', $this->stats)): ?>
            <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
            <?php endif; ?>
            <?php if(in_array('commentCount', $this->stats)): ?>
            <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span>
             <?php endif; ?>
             <?php if(in_array('viewCount', $this->stats)): ?>
            <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
             <?php endif; ?>
          </p>
        </div>	
      </div>
       <?php if(in_array('rating', $this->stats)): ?>
        <div class="sesblog_review_show_rating review_ratings_listing">
        <div class="sesbasic_rating_star">
          <?php $ratingCount = $item->rating;?>
          <?php for($i=0; $i<5; $i++){?>
            <?php if($i < $ratingCount):?>
              <span id="" class="sesblog_rating_star"></span>
            <?php else:?>
              <span id="" class="sesblog_rating_star sesblog_rating_star_disable"></span>
            <?php endif;?>
          <?php }?>
        </div>
        </div>
       <?php endif ?>
    </div>
    <div class="sesblog_review_listing_desc sesbasic_clearfix">
      <?php if(in_array('pros', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.show.pros', 1)): ?>
        <p class="sesblog_review_listing_body">
          <b><?php echo $this->translate("Pros: "); ?></b>
          <?php echo $this->string()->truncate($this->string()->stripTags($item->pros), 300) ?>
        </p>
      <?php endif; ?>
      <?php if(in_array('cons', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.show.cons', 1)): ?>
        <p class="sesblog_review_listing_body">
          <b><?php echo $this->translate("Cons: "); ?></b>
          <?php echo $this->string()->truncate($this->string()->stripTags($item->cons), 300) ?>
        </p>
      <?php endif; ?>
      <?php if(in_array('description', $this->stats) && $item->description): ?>
      <p class='sesblog_review_listing_body'>
        <b><?php echo $this->translate("Description: "); ?></b>
        <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
      </p>
      <?php endif; ?>
      
      <?php if(in_array('recommended', $this->stats)): ?>
        <p class="sesblog_review_view_cont_row sesblog_review_view_recommended">
          <b><?php echo $this->translate("Recommended"); ?>
          <?php if($item->recommended): ?>
            <i class="fa fa-check"></i></b>
          <?php else: ?>
            <i class="fa fa-times"></i></b>
          <?php endif; ?>
        </p>
      <?php endif; ?>
      <p class="sesblog_review_listing_more">
      	<a href="<?php echo $item->getHref(); ?>" class="floatR"><?php echo $this->translate("Continue Reading"); ?> &raquo;</a>
        </p>
		</div>
    <div class="sesblog_review_listing_footer clear sesbasic_clearfix ">
			<?php if(in_array('socialSharing', $this->stats) || in_array('likeButton', $this->stats)):?>
				<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
				<div class="sesblog_list_share_btns floatL">
				  <?php $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
				  <?php if(in_array('likeButton', $this->stats) && $canComment):?>
						<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($item->review_id,$item->getType()); ?>
						<a href="javascript:;" data-url="<?php echo $item->review_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_review_<?php echo $item->review_id ?> sesblog_like_sesblog_review <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
					<?php endif;?>
					<?php if(in_array('socialSharing', $this->stats)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
            
            <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

					<?php endif;?>
				</div>
			<?php endif;?>
			<div class='sesblog_review_listing_btn_right floatR '>
				<?php if($item->authorization()->isAllowed($this->viewer(), 'edit')) { ?>
					<a class="fa fa-edit sesbasic_button sesbasic_button_icon " href="<?php echo $this->url(array('slug' => $item->getSlug(), 'action' => 'edit', 'review_id' => $item->getIdentity()), 'sesblogreview_view', 'true');?>"><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Edit Review');?></span></a>
				<?php } ?>
				<?php if($item->authorization()->isAllowed($this->viewer(), 'delete')) { ?>
					<a class="smoothbox fa fa-trash sesbasic_button sesbasic_button_icon" href="<?php echo $this->url(array('action' => 'delete', 'review_id' => $item->getIdentity()), 'sesblogreview_view', true);?>"><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Delete Review');?></span></a>
				<?php } ?>
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.show.report', 1) && $this->viewer()->getIdentity() && in_array('report', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)): ?>
					<a class="smoothbox fa fa-flag sesbasic_button sesbasic_button_icon" href="<?php echo $this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $item->getGuid()), 'default', true);?>"><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Report');?></span></a>
				<?php endif; ?>
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.share', 1) && $this->viewer()->getIdentity() && in_array('share', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)): ?>
					<a class="smoothbox fa fa-share sesbasic_button sesbasic_button_icon" href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $item->getType(), 'id' => $item->getIdentity()), 'default', true);?>"><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Share Review');?></span></a> 
				<?php endif; ?>
		  </div>
    </div>
  </li>
  <?php endforeach; ?>
	<?php if($this->loadOptionData == 'pagging'){ ?>
		<?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesblog"),array('identityWidget'=>$randonNumber)); ?>
	<?php } ?>
  <?php if(!$this->is_ajax){ ?>
    </ul>
    <?php } ?>
<?php else: ?>
<div class="sesbasic_tip clearfix">
    <img src="application/modules/Sesblog/externals/images/reviews_icon.png" alt="">
    <span class="sesbasic_text_light">
      <?php echo $this->translate('No review have been posted in this blog yet.');?>    </span>
  </div>
<?php endif; ?>
<?php if(!$this->is_ajax){ ?>
	<?php if($this->loadOptionData != 'pagging' && !$this->is_ajax):?>
		<div class="sesbasic_load_btn" id="view_more_<?php echo $randonNumber;?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-sync')); ?> </div>
		<div class="sesbasic_load_btn sesbasic_view_more_loading_<?php echo $randonNumber;?>" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span></div>
	<?php endif;?>
<?php } ?>

<script type="application/javascript">
	var tabId_review = '<?php echo $randonNumber; ?>';
	window.addEvent('domready', function() {
		tabContainerHrefSesbasic(tabId_review);	
	});
</script>

<script type="application/javascript">
  <?php if(!$this->is_ajax):?>
		<?php if($this->loadOptionData == 'auto_load'){ ?>
  sesJqueryObject("#feed_viewmore_link_<?php echo $randonNumber;?>").text("");
  sesJqueryObject("#feed_viewmore_link_<?php echo $randonNumber;?>").removeClass('sesbasic_link_btn fa fa-repeat');
			window.addEvent('load', function() {
				sesJqueryObject(window).scroll( function() {
					var containerId = '#sesblog_review_listing';
					if(typeof sesJqueryObject(containerId).offset() != 'undefined') {
						var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject(containerId).offset().top;
						var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
						if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
							document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
						}
					}
				});
			});
		<?php } ?>
  <?php endif; ?>
	var page<?php echo $randonNumber; ?> = <?php echo $this->page + 1; ?>;
	var params<?php echo $randonNumber; ?> = '<?php echo json_encode($this->stats); ?>';
	var searchParams<?php echo $randonNumber; ?> = '';
	<?php if($this->loadOptionData != 'pagging') { ?>
		viewMoreHide_<?php echo $randonNumber; ?>();
		function viewMoreHide_<?php echo $randonNumber; ?>() {
			if ($('view_more_<?php echo $randonNumber; ?>'))
			$('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
		}
		function viewMore_<?php echo $randonNumber; ?> () {
			sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').hide();
			sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').show(); 
			requestViewMore_<?php echo $randonNumber; ?> = new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/blog-reviews",
				'data': {
					format: 'html',
					page: page<?php echo $randonNumber; ?>,    
					params : params<?php echo $randonNumber; ?>, 
					is_ajax : 1,
					limit:'<?php echo $this->limit; ?>',
					widgetId : '<?php echo $this->widgetId; ?>',
					searchParams : searchParams<?php echo $randonNumber; ?>,
					blog_id:'<?php echo $this->blog_id; ?>',
					loadOptionData : '<?php echo $this->loadOptionData ?>'
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					sesJqueryObject('#sesblog_review_listing').append(responseHTML);
					sesJqueryObject('.sesbasic_view_more_loading_<?php echo $randonNumber;?>').hide();
					sesJqueryObject('#loadingimgsesblogreview-wrapper').hide();
					viewMoreHide_<?php echo $randonNumber; ?>();
				}
			});
			requestViewMore_<?php echo $randonNumber; ?>.send();
			return false;
		}
	<?php }else{ ?>
		function paggingNumber<?php echo $randonNumber; ?>(pageNum){
			sesJqueryObject('#sesbasic_loading_cont_overlay_<?php echo $randonNumber?>').css('display','block');
			requestViewMore_<?php echo $randonNumber; ?> = (new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/blog-reviews",
				'data': {
					format: 'html',
					page: pageNum,
					blog_id:'<?php echo $this->blog_id; ?>',
					params :params<?php echo $randonNumber; ?> , 
					searchParams : searchParams<?php echo $randonNumber; ?>,
					is_ajax : 1,
					limit:'<?php echo $this->limit; ?>',
					widgetId : '<?php echo $this->widgetId; ?>',
					loadOptionData : '<?php echo $this->loadOptionData ?>'
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					sesJqueryObject('#sesblog_review_listing').html(responseHTML);
					sesJqueryObject('#sesbasic_loading_cont_overlay_<?php echo $randonNumber?>').css('display', 'none');
					sesJqueryObject('#loadingimgsesblogreview-wrapper').hide();
				}
			}));
			requestViewMore_<?php echo $randonNumber; ?>.send();
			return false;
		}
	<?php } ?>
</script>
