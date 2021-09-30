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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)):?>
	<?php $randonNumber = $this->identityForWidget;?>
<?php else:?>
	<?php $randonNumber = $this->identity;?>
<?php endif;?>
<?php if(!$this->is_ajax){ ?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php if(isset($this->category->thumbnail) && !empty($this->category->thumbnail)){ ?>
  <div class="sesblog_category_cover sesbasic_bxs sesbm">
 		<div class="sesblog_category_cover_inner" style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($this->category->thumbnail)->getPhotoUrl('thumb.thumb'); ?>">
    	<div class="sesblog_category_cover_content">
        <div class="sesblog_category_cover_breadcrumb">
          <!--breadcrumb -->
          <a href="<?php echo $this->url(array('action' => 'browse'), "sesblog_category"); ?>"><?php echo $this->translate("Categories"); ?></a>&nbsp;&raquo;
          <?php if(isset($this->breadcrumb['category'][0]->category_id)){ ?>
             <?php if($this->breadcrumb['subcategory']) { ?>
              <a href="<?php echo $this->breadcrumb['category'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?></a>
             <?php }else{ ?>
               <?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?>
             <?php } ?>
             <?php if($this->breadcrumb['subcategory']) echo "&nbsp;&raquo"; ?>
          <?php } ?>
          <?php if(isset($this->breadcrumb['subcategory'][0]->category_id)){ ?>
            <?php if($this->breadcrumb['subSubcategory']) { ?>
              <a href="<?php echo $this->breadcrumb['subcategory'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?></a>
            <?php }else{ ?>
              <?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?>
            <?php } ?>
            <?php if($this->breadcrumb['subSubcategory']) echo "&nbsp;&raquo"; ?>
          <?php } ?>
          <?php if(isset($this->breadcrumb['subSubcategory'][0]->category_id)){ ?>
            <?php echo $this->translate($this->breadcrumb['subSubcategory'][0]->category_name) ?>
          <?php } ?>
        </div>

        <div class="sesblog_category_cover_blocks">
          <div class="sesblog_category_cover_block_img">
            <span style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($this->category->thumbnail)->getPhotoUrl('thumb.thumb'); ?>"></span>
          </div>
          <div class="sesblog_category_cover_block_info">
            <?php if(isset($this->category->title) && !empty($this->category->title)): ?>
              <div class="sesblog_category_cover_title"> 
                <?php echo $this->category->title; ?>
              </div>
            <?php endif; ?>
            <?php if(isset($this->category->description) && !empty($this->category->description)): ?>
              <div class="sesblog_category_cover_des clear sesbasic_custom_scroll">
                <?php echo $this->category->description;?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>  
<?php } else { ?>
  <div class="sesvide_breadcrumb clear sesbasic_clearfix">
    <!--breadcrumb -->
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesblog_category"); ?>"><?php echo $this->translate("Categories"); ?></a>&nbsp;&raquo;
    <?php if(isset($this->breadcrumb['category'][0]->category_id)){ ?>
       <?php if($this->breadcrumb['subcategory']) { ?>
        <a href="<?php echo $this->breadcrumb['category'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?></a>
       <?php }else{ ?>
         <?php echo $this->translate($this->breadcrumb['category'][0]->category_name) ?>
       <?php } ?>
       <?php if($this->breadcrumb['subcategory']) echo "&nbsp;&raquo"; ?>
    <?php } ?>
    <?php if(isset($this->breadcrumb['subcategory'][0]->category_id)){ ?>
      <?php if($this->breadcrumb['subSubcategory']) { ?>
        <a href="<?php echo $this->breadcrumb['subcategory'][0]->getHref(); ?>"><?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?></a>
      <?php }else{ ?>
        <?php echo $this->translate($this->breadcrumb['subcategory'][0]->category_name) ?>
      <?php } ?>
      <?php if($this->breadcrumb['subSubcategory']) echo "&nbsp;&raquo"; ?>
    <?php } ?>
    <?php if(isset($this->breadcrumb['subSubcategory'][0]->category_id)){ ?>
      <?php echo $this->translate($this->breadcrumb['subSubcategory'][0]->category_name) ?>
    <?php } ?>
  </div>
  <div class="sesblog_browse_cat_top sesbm">
    <?php if(isset($this->category->title) && !empty($this->category->title)): ?>
      <div class="sesblog_catview_title"> 
        <?php echo $this->category->title; ?>
      </div>
    <?php endif; ?>
    <?php if(isset($this->category->description) && !empty($this->category->description)): ?>
      <div class="sesblog_catview_des">
        <?php echo $this->category->description;?>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>

<!-- category subcategory -->
<?php if($this->show_subcat == 1 && count($this->innerCatData)>0){ ?>
  <div>
    <ul class="sesblog_cat_iconlist_container sesbasic_clearfix clear sesbasic_bxs">	
      <?php foreach( $this->innerCatData as $item ):  ?>
        <li class="sesblog_cat_iconlist" style="height:<?php echo is_numeric($this->heightSubcat) ? $this->heightSubcat.'px' : $this->heightSubcat ?>;width:<?php echo is_numeric($this->widthSubcat) ? $this->widthSubcat.'px' : $this->widthSubcat ?>;">
          <a href="<?php echo $item->getHref(); ?>" class="link_img img_animate" style="height:<?php echo is_numeric($this->heightSubcat) ? $this->heightSubcat.'px' : $this->heightSubcat ?>;">
            <?php if($item->thumbnail != '' && !is_null($item->thumbnail) && intval($item->thumbnail)){ ?>
                <img class="list_main_img"  src="<?php echo  Engine_Api::_()->storage()->get($item->thumbnail)->getPhotoUrl('thumb.thumb'); ?>"/>
              <?php } ?>
			  <div class="sesblog_category_icon_block">
              <div>
                  <?php if(isset($this->titleSubcatActive)){ ?>
                  <p class="sesblog_cat_iconlist_title"><?php echo $this->translate($item->category_name); ?></p>
                  <?php } ?>
                  <?php if(isset($this->countBlogSubcatActive)){ ?>
                    <p class="sesblog_cat_iconlist_count"><?php echo $this->translate(array('%s blog', '%s blogs', $item->total_blogs_categories), $this->locale()->toNumber($item->total_blogs_categories))?></p>
                  <?php } ?>
              </div>
			   <span class="sesblog_cat_iconlist_icon" style="background-color:<?php echo $item->color ? '#'.$item->color : '#000'; ?>;height:<?php echo is_numeric($this->heightSubcat) ? $this->heightSubcat.'px' : $this->heightSubcat ?>;">
          <?php if($item->cat_icon != '' && !is_null($item->cat_icon) && intval($item->cat_icon)) { ?>
            <?php $cat_icon = Engine_Api::_()->storage()->get($item->cat_icon); ?>
            <?php if($cat_icon) { ?>
              <img src="<?php echo $cat_icon->getPhotoUrl(); ?>" />
            <?php } ?>
          <?php } ?>
        </span>
            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
   </div>
<?php } ?> 

<div class="sesblog_subcat_list_head clear sesbasic_clearfix">
	<p><?php echo $this->translate($this->textBlog);?></p>
</div>
<div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix sesbasic_bxs clear">  
<?php } ?>
   <?php if($this->viewType == 'list'):?>
		<?php if(!$this->is_ajax){ ?>
		<ul class="sesblog_blog_listing sesbasic_clearfix clear" id="tabbed-widget_<?php echo $randonNumber; ?>" style="display:block;">
	<?php } ?>
			<?php foreach($this->paginator as $key=>$blog):?>
				<li class="sesblog_list_blog_view sesbasic_clearfix clear">
					<div class="sesblog_list_thumb sesblog_thumb"  style="width:<?php echo $this->width.'px'; ?>;height:<?php echo $this->height.'px'; ?>">
						<a href="<?php echo $blog->getHref(); ?>" data-url = "<?php echo $blog->getType() ?>" class="sesblog_thumb_img">
							<span class="" style="background-image:url(<?php echo $blog->getPhotoUrl(); ?>);"></span>
						</a>
						<?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
							<div class="sesblog_list_labels ">
								<?php if(isset($this->featuredLabelActive) && $blog->featured == 1){ ?>
									<p class="sesblog_label_featured"><?php echo $this->translate('FEATURED'); ?></p>
								<?php } ?>
								<?php if(isset($this->sponsoredLabelActive) && $blog->sponsored == 1){ ?>
									<p class="sesblog_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></p>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
					<div class="sesblog_list_info">
          <?php if(isset($this->titleActive)){ ?>
						<div class="sesblog_list_info_title">
							<a href="<?php echo $blog->getHref(); ?>"><?php echo $blog->getTitle(); ?></a>
						</div>
				  <?php } ?>
						<div class="sesblog_admin_list">
							<div class="sesblog_stats_list sesbasic_text_light">
								<?php if(isset($this->byActive)){ ?>
									<?php $owner = $blog->getOwner();?>
									<span>
											<?php echo $this->translate('Posted by');?>
                      <a href="<?php echo $owner->getHref();?>"><?php echo $this->itemPhoto($owner, 'thumb.icon');?></a>
										<a href="<?php echo $owner->getHref();?>"><?php echo $this->translate(' %1$s', $owner->getTitle()); ?></a>
									</span>
								<?php } ?>
							</div>
							<?php if(isset($this->creationDateActive)):?>
								<div class="sesblog_stats_list sesbasic_text_dark">
									<span>
										on <?php echo date('M d, Y',strtotime($blog->publish_date));?>		
									</span>
								</div>
							<?php endif;?>
						</div>
						<div class="sesblog_list_contant">
							<?php if(isset($this->descriptionActive)){ ?>
                              <?php echo $blog->getDescription($this->description_truncation);?>
							<?php } ?> 
						</div>
            <?php if(isset($this->readmoreActive)){ ?>
							<div class="sesblog_list_readmore"><a href="<?php echo $blog->getHref();?>" class="sesblog_animation"><?php echo $this->translate('More'); ?></a></div>
						<?php } ?>
            <div class="sesblog_cat_list_footer">
            <div class="entry_meta-time sesbasic_text_light floatL">
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->readtimeActive) && !empty($item->readtime)) { ?>
                <span><i class="far fa-clock"></i><?php echo $item->readtime ?>. <?php echo $this->translate("read"); ?></span>
              <?php } ?>
            </div> 
						<div class="sesblog_list_stats sesbasic_text_light">
							<?php if(isset($this->likeActive) && isset($blog->like_count)) { ?>
								<span title="<?php echo $this->translate(array('%s like', '%s likes', $blog->like_count), $this->locale()->toNumber($blog->like_count)); ?>"><i class="sesbasic_icon_like_o"></i><?php echo $blog->like_count; ?></span>
							<?php } ?>
							<?php if(isset($this->commentActive) && isset($blog->comment_count)) { ?>
								<span title="<?php echo $this->translate(array('%s comment', '%s comments', $blog->comment_count), $this->locale()->toNumber($blog->comment_count))?>"><i class="sesbasic_icon_comment_o"></i><?php echo $blog->comment_count;?></span>
							<?php } ?>
							<?php if(isset($this->viewActive) && isset($blog->view_count)) { ?>
								<span title="<?php echo $this->translate(array('%s view', '%s views', $blog->view_count), $this->locale()->toNumber($blog->view_count))?>"><i class="sesbasic_icon_view"></i><?php echo $blog->view_count; ?></span>
							<?php } ?>
							<?php if(isset($this->favouriteActive) && isset($blog->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
								<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $blog->favourite_count), $this->locale()->toNumber($blog->favourite_count))?>"><i class="sesbasic_icon_favourite_o"></i><?php echo $blog->favourite_count; ?></span>
							<?php } ?>
							<?php if(isset($this->ratingActive) && isset($blog->rating) && $blog->rating > 0 && Engine_Api::_()->sesbasic()->getViewerPrivacy('sesblog_review', 'view')): ?>
								<span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($blog->rating,1)), $this->locale()->toNumber(round($blog->rating,1)))?>">
									<i class="fa fa-star"></i><?php echo round($blog->rating,1).'/5';?><?php echo round($item->rating,1).'/5';?><?php echo $this->translate(' Ratings');?>
								</span>
							<?php endif; ?>
						</div>
            </div>
						<?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && isset($this->ratingStarActive)):?>
							<?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $blog->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting floatL', 'style' => ''));?>
						<?php endif;?>
					</div>
				</li>  
		<?php endforeach;?>
		<?php  if(count($this->paginator) == 0){  ?>
			<div class="tip">
				<span>
					<?php echo $this->translate("No blog in this  category."); ?>
					<?php if (!$this->can_edit):?>
						<?php echo $this->translate('Be the first to %1$spost%2$s one in this category!', '<a href="'.$this->url(array('action' => 'create'), "sesblog_general").'">', '</a>'); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php } ?>    
		<?php if($this->loadOptionData == 'pagging'): ?>
			<?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesblog"),array('identityWidget'=>$randonNumber)); ?>
		<?php endif; ?>
	<?php if(!$this->is_ajax){ ?> 
	</ul>
	<?php } ?>
 <?php else:?>
	<?php if(!$this->is_ajax){ ?> 
	<ul class="sesblog_blog_listing sesbasic_clearfix clear" id="tabbed-widget_<?php echo $randonNumber; ?>">
	<?php } ?>
			<?php foreach($this->paginator as $key=>$blog): ?>
				<li class="sesblog_grid sesblog_catogery_grid_view sesbasic_bxs" style="width:<?php echo $this->width.'px'; ?>">
						<div class="sesblog_grid_inner sesblog_thumb"> 
							<div class="sesblog_grid_thumb" style="height:<?php echo $this->height.'px'; ?>">
								<a href="<?php echo $blog->getHref(); ?>" data-url = "<?php echo $blog->getType() ?>" class="sesblog_thumb_img">
									<span class="" style="background-image:url(<?php echo $blog->getPhotoUrl(); ?>);"></span>
								</a>
								<?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
									<div class="sesblog_list_labels ">
										<?php if(isset($this->featuredLabelActive) && $blog->featured == 1){ ?>
											<p class="sesblog_label_featured"><?php echo $this->translate('FEATURED'); ?></p>
										<?php } ?>
										<?php if(isset($this->sponsoredLabelActive) && $blog->sponsored == 1){ ?>
											<p class="sesblog_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></p>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						<div class="sesblog_grid_info clear clearfix sesbm">
						<?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && isset($this->ratingStarActive)):?>
							<?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $blog->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting floatR', 'style' => 'margin-bottom:5px; margin-top:5px;'));?>
						<?php endif;?>
          	<?php if(isset($this->titleActive)){ ?>
							<div class="sesblog_grid_info_title">
								<a href="<?php echo $blog->getHref();?>"><?php echo $blog->getTitle(); ?></a>
							</div>
							<?php } ?>
					
						<div class="sesblog_grid_meta_block">
							<div class="sesblog_list_stats sesbasic_text_light">
								<?php if(isset($this->byActive)){ ?>
									<?php $owner = $blog->getOwner();?>
                    <span>
                        <?php echo $this->translate(' Posted by');?>
                         <a href="<?php echo $owner->gethref();?>"><?php echo $this->itemPhoto($owner, 'thumb.icon');?></a>
                      <a href="<?php echo $owner->getHref();?>"><?php echo $this->translate(' %1$s', $owner->getTitle()); ?></a>
                    </span>
									<?php } ?>
									<?php if(isset($this->creationDateActive)) { ?>
                    <span>
                      on <?php echo date('M d, Y',strtotime($blog->publish_date));?>	
                    </span>
								<?php } ?>
							</div>
						</div>
					</div>
          <div class="sesblog_list_contant">
						<?php if(isset($this->descriptionActive)){ ?>
							<?php echo $blog->getDescription($this->description_truncation);?>
						<?php } ?> 
					</div>
          <?php if(isset($this->readmoreActive)){ ?>
						<div class="sesblog_grid_read_btn"><a href="<?php echo $blog->getHref();?>" class="sesblog_animation"><?php echo $this->translate('More '); ?></a></div>
					<?php } ?>
					<div class="sesblog_grid_stats_footer">  
            <div class="entry_meta-time sesbasic_text_light">
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->readtimeActive) && !empty($item->readtime)) { ?>
                <span><i class="far fa-clock"></i><?php echo $item->readtime ?>. <?php echo $this->translate("read"); ?></span>
              <?php } ?>
            </div> 
						<div class="sesblog_list_stats sesbasic_text_light">
							<?php if(isset($this->likeActive) && isset($blog->like_count)) { ?>
								<span title="<?php echo $this->translate(array('%s like', '%s likes', $blog->like_count), $this->locale()->toNumber($blog->like_count)); ?>"><i class="sesbasic_icon_like_o"></i><?php echo $blog->like_count; ?></span>
							<?php } ?>
							<?php if(isset($this->commentActive) && isset($blog->comment_count)) { ?>
								<span title="<?php echo $this->translate(array('%s comment', '%s comments', $blog->comment_count), $this->locale()->toNumber($blog->comment_count))?>"><i class="sesbasic_icon_comment_o"></i><?php echo $blog->comment_count;?></span>
							<?php } ?>
							<?php if(isset($this->viewActive) && isset($blog->view_count)) { ?>
								<span title="<?php echo $this->translate(array('%s view', '%s views', $blog->view_count), $this->locale()->toNumber($blog->view_count))?>"><i class="sesbasic_icon_view"></i><?php echo $blog->view_count; ?></span>
							<?php } ?>
							<?php if(isset($this->favouriteActive) && isset($blog->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
								<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $blog->favourite_count), $this->locale()->toNumber($blog->favourite_count))?>"><i class="sesbasic_icon_favourite_o"></i><?php echo $blog->favourite_count; ?></span>
							<?php } ?>
							<?php if(isset($this->ratingActive) && isset($blog->rating) && $blog->rating > 0 ): ?>
								<span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($blog->rating,1)), $this->locale()->toNumber(round($blog->rating,1)))?>">
								<i class="fa fa-star"></i><?php echo round($blog->rating,1).'/5';?>
								</span>
							<?php endif; ?>
						</div>
					</div>
					</div>
				</li>
			<?php endforeach;?>
			<?php  if(  count($this->paginator) == 0){  ?>
				<div class="tip">
					<span>
						<?php echo $this->translate("No blog in this  category."); ?>
						<?php if (!$this->can_edit):?>
							<?php echo $this->translate('Be the first to %1$spost%2$s one in this category!', '<a href="'.$this->url(array('action' => 'create'), "sesblog_general").'">', '</a>'); ?>
						<?php endif; ?>
					</span>
				</div>
			<?php } ?>    
			<?php if($this->loadOptionData == 'pagging'){ ?>
				<?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesblog"),array('identityWidget'=>$randonNumber)); ?>
			<?php } ?>
	<?php if(!$this->is_ajax){ ?> 
	</ul>
	<?php } ;?>
	<?php endif;?>
	<?php if(!$this->is_ajax){ ?>
 </div>
 <?php if($this->loadOptionData == 'button'){ ?>
  <div class="sesbasic_view_more"  id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading"  id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
  <?php } ?>
  <script type="application/javascript">
function paggingNumber<?php echo $randonNumber; ?>(pageNum){
	 sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','block');
	 var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: pageNum,    
				params :<?php echo json_encode($this->params); ?>, 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
				type:'<?php echo $this->view_type; ?>'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','none');
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
				dynamicWidth();
      }
    }));
    return false;
}
</script>
  <?php } ?>
<script type="text/javascript">
var valueTabData ;
// globally define available tab array
	var availableTabs_<?php echo $randonNumber; ?>;
	var requestTab_<?php echo $randonNumber; ?>;
  availableTabs_<?php echo $randonNumber; ?> = <?php echo json_encode($this->defaultOptions); ?>;
<?php if($this->loadOptionData == 'auto_load'){ ?>
		window.addEvent('load', function() {
		 sesJqueryObject(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
var defaultOpenTab ;
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: <?php echo $this->page + 1; ?>,    
				params :<?php echo json_encode($this->params); ?>, 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				dynamicWidth();
      }
    }));
    return false;
  }
<?php if(!$this->is_ajax){ ?>
function dynamicWidth(){
	var objectClass = sesJqueryObject('.sesblog_cat_blog_list_info');
	for(i=0;i<objectClass.length;i++){
			sesJqueryObject(objectClass[i]).find('div').find('.sesblog_cat_blog_list_content').find('.sesblog_cat_blog_list_title').width(sesJqueryObject(objectClass[i]).width());
	}
}
dynamicWidth();
<?php } ?>
</script>
