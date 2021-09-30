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

<?php  if(!$this->is_ajax): ?>
	<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/styles.css'); ?>
<?php endif;?>

<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)):?>
	<?php $randonnumber = $this->identityForWidget;?>
<?php else:?>
	<?php $randonnumber = $this->identity; ?>
<?php endif;?>

<div id="widget_sesblog_<?php echo $randonnumber; ?>" class="sesblog_related_blogs sesbasic_clearfix sesbasic_bxs">
  <div class="sesbasic_loading_cont_overlay" id="sesblog_widget_overlay_<?php echo $randonnumber; ?>"></div>
   <ul class="sesblog_blog_listing sesbasic_clearfix clear">
  <?php foreach($this->paginator as $item):?>
		<li class="sesblog_grid sesbasic_clearfix" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
      <article>
		  <div class="sesblog_grid_inside_inner">
				<div class="sesblog_grid_inside_thumb sesblog_thumb" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height?>;">
					<a href="<?php echo $item->getHref();?>" class="sesblog_thumb_img" >
						<span style="background-image:url(<?php echo $item->getPhotoUrl();?>);"></span>
					</a>
				</div>
				<?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->verifiedLabel)):?>
					<div class="sesblog_list_labels ">
						<?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
							<p class="sesblog_label_featured"><?php echo $this->translate('FEATURED');?></p>
						<?php endif;?>
						<?php if(isset($this->sponsoredLabelActive) && $item->sponsored == 1):?>
							<p class="sesblog_label_sponsored"><?php echo $this->translate('SPONSORED');?></p>
						<?php endif;?>
					</div>
				<?php endif;?>
				<div class="sesblog_grid_info sesbasic_clearfix ">
				  <?php if(isset($this->categoryActive)): ?>
						<?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)):?> 
							<?php $categoryItem = Engine_Api::_()->getItem('sesblog_category', $item->category_id);?>
							<?php if($categoryItem):?>
								<div class="category_tag sesbasic_clearfix">
									<a href="<?php echo $categoryItem->getHref(); ?>"><?php echo $categoryItem->category_name; ?></a>
								</div>
							<?php endif;?>
				    <?php endif;?>
					<?php endif;?>
					<div class="sesblog_grid_info_title">
						<?php if(strlen($item->getTitle()) > $this->title_truncation_list):?>
							<?php $title = mb_substr($item->getTitle(),0,$this->title_truncation_list).'...';?>
							<?php echo $this->htmlLink($item->getHref(),$title,array('title'=>$item->getTitle()));?>
						<?php else: ?>
							<?php echo $this->htmlLink($item->getHref(),$item->getTitle(),array('title'=>$item->getTitle())  ) ?>
						<?php endif;?>      
                      <?php if(isset($this->verifiedLabelActive) && $item->verified == 1):?>
						<i class="sesbasic_verified_icon" title="Verified"></i>
					  <?php endif;?>						
					</div>
					<?php if(isset($this->byActive)):?>
						<div class="admin_teg sesblog_list_stats sesbasic_text_dark">
							<i class="far fa-user"></i>
							<?php $owner = $item->getOwner(); ?>
							<span>
								<?php echo $this->translate("by") ?> <?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?>
							</span>
						</div>
					<?php endif;?>
					<div class="sesblog_list_stats">
						<?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
							<span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="sesbasic_icon_like_o"></i><?php echo $item->like_count; ?></span>
						<?php } ?>
						<?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
							<span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="sesbasic_icon_comment_o"></i><?php echo $item->comment_count;?></span>
						<?php } ?>
						<?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
							<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="sesbasic_icon_favourite_o"></i><?php echo $item->favourite_count;?></span>
						<?php } ?>
						<?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
							<span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="sesbasic_icon_view"></i><?php echo $item->view_count; ?></span>
						<?php } ?>
						<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/_blogRatingStat.tpl';?>
					</div>
					<?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive)):?>
				  <?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
					<div class="sesblog_list_share_btns"> 
					  <div class="sesblog_list_btns">
					  <?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
              
              <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

						<?php endif;?>
						<!--Like Button-->
						<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ):?>
							<?php $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
							<?php if(isset($this->likeButtonActive) && $canComment):?>
								<!--Like Button-->
								<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($item->blog_id,$item->getType()); ?>
								<a href="javascript:;" data-url="<?php echo $item->blog_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_blog <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
							<?php endif;?>
							<?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)): ?>
								<?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_blog','resource_id'=>$item->blog_id)); ?>
								<a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesblog_favourite_sesblog_blog <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->blog_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
							<?php endif;?>
					  <?php endif;?>
					  </div>
					</div>
				<?php endif;?>
				</div>
		  </div>
     </article>
		</li>
  <?php endforeach;?>
  </ul>
  <?php if(isset($this->widgetName)){ ?>
		<div class="sidebar_privew_next_btns">
			<div class="sidebar_previous_btn">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
					'id' => "widget_previous_".$randonnumber,
					'onclick' => "widget_previous_$randonnumber()",
					'class' => 'buttonlink previous_icon'
				)); ?>
			</div>
			<div class="sidebar_next_btns">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
					'id' => "widget_next_".$randonnumber,
					'onclick' => "widget_next_$randonnumber()",
					'class' => 'buttonlink_right next_icon'
				)); ?>
			</div>
		</div>
	<?php } ?>
</div>

<?php if(isset($this->widgetName)){ ?>
  <script type="application/javascript">
		var anchor_<?php echo $randonnumber ?> = sesJqueryObject('#widget_sesblog_<?php echo $randonnumber; ?>').parent();
		function showHideBtn<?php echo $randonnumber ?> (){
			sesJqueryObject('#widget_previous_<?php echo $randonnumber; ?>').parent().css('display','<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
			sesJqueryObject('#widget_next_<?php echo $randonnumber; ?>').parent().css('display','<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');	
		}
		showHideBtn<?php echo $randonnumber ?> ();
		function widget_previous_<?php echo $randonnumber; ?>(){
			sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').show();
			new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					is_ajax: 1,
					params :'<?php echo json_encode($this->params); ?>', 
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					anchor_<?php echo $randonnumber ?>.html(responseHTML);
					sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').hide();
					showHideBtn<?php echo $randonnumber ?> ();
				}
			}).send()
		};

		function widget_next_<?php echo $randonnumber; ?>(){
			sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').show();
			new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					is_ajax: 1,
					params :'<?php echo json_encode($this->params); ?>' , 
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					anchor_<?php echo $randonnumber ?>.html(responseHTML);
					sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').hide();
					showHideBtn<?php echo $randonnumber ?> ();
				}
			}).send();
		};
	</script>
<?php } ?>
