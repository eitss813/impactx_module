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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)):?>
<?php $randonNumber = $this->identityForWidget;?>
<?php else:?>
<?php $randonNumber = $this->identity;?>
<?php endif;?>
<?php if(!$this->is_ajax):?>

<div id="scrollHeightDivSes_<?php echo $randonNumber;?>" class="sesbasic_bxs">
  <div id="category-blog-widget_<?php echo $randonNumber; ?>" class="sesbasic_clearfix">
    <?php endif;?>
    <?php foreach($this->paginatorCategory as $item): ?>
    <div class="sesblog_category_blog sesbasic_clearfix">
      <div class="sesblog_category_header sesbasic_clearfix">
        <p class="floatL" <?php echo $this->allignment_seeall == 'right' ?  'class="floatL"' : ''; ?>><a href="<?php echo $item->getBrowseBlogHref(); ?>?category_id=<?php echo $item->category_id ?>" title="<?php echo $this->translate($item->category_name); ?>"><?php echo $this->translate($item->category_name); ?></a></p>
        <?php if(isset($this->seemore_text) && $this->seemore_text != ''): ?>
        <span <?php echo $this->allignment_seeall == 'right' ?  'class="floatR"' : ''; ?>><a href="<?php echo $item->getBrowseBlogHref(); ?>?category_id=<?php echo $item->category_id ?>">
        <?php $seemoreTranslate = $this->translate($this->seemore_text); ?>
        <?php echo str_replace('[category_name]',$this->translate($item->category_name),$seemoreTranslate); ?></a></span>
        <?php endif;?>
      </div>
      <?php	foreach($this->resultArray['blog_data'][$item->category_id] as $item):?>
      <?php   
                    $user = $item->getOwner();
                    $oldTimeZone = date_default_timezone_get();
                    $convert_date = strtotime($item->creation_date);
                    date_default_timezone_set($user->timezone);
                ?>
      <div class="sesblog_category_item sesbasic_clearfix" style="width:<?php echo $this->width ?>px;">
        <div class="wrapper_list sesbasic_clearfix">
          <div class="sesblog_entry_img" style="height:<?php echo $this->height ?>px;"> <a href="<?php echo $item->getHref();?>"><img src="<?php echo $item->getPhotoUrl('thumb.main'); ?>" /></a>
            <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->verifiedLabel)):?>
            <div class="sesblog_list_labels">
              <?php if(isset($this->sponsoredLabelActive) && $item->sponsored == 1):?>
              <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
              <?php endif;?>
              <?php if(isset($this->featuredLabelActive) && $item->featured == 1):?>
              <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
              <?php endif;?>
            </div>
            <?php endif;?>
          </div>
          <div class="sesblog_cat_info">
            <?php if(isset($this->titleActive)):?>
            <a href="<?php echo $item->getHref();?>">
            <p class="title"><?php echo $item->getTitle();?></p>
            </a>
            <?php endif;?>
            <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && isset($this->ratingStarActive)):?>
            <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => 'margin-bottom:0px;'));?>
            <?php endif;?>
            <?php if(isset($this->creationDateActive)):?>
            <div class="entry_meta-date sesbasic_text_light"> Posted on <?php echo date('M d, Y',$convert_date);?> </div>
            <?php endif; ?>
            <div class="sesblog_list_readmore"><a href="#" class="sesblog_animation">More</a></div>
            <div class="entry_meta">
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && isset($this->readtimeActive) && !empty($item->readtime)) { ?>
              <div class="entry_meta-time sesbasic_text_light floatL"> <span><i class="far fa-clock"></i><?php echo $item->readtime ?>. <?php echo $this->translate("read"); ?></span> </div>
              <?php } ?>
              <div class="entry_meta-comment sesbasic_text_light  floatR">
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
            </div>
          </div>
        </div>
      </div>
      <?php date_default_timezone_set($oldTimeZone); ?>
      <?php endforeach;?>
    </div>
    <?php endforeach;?>
    <?php if($this->paginatorCategory->getTotalItemCount() == 0 && !$this->is_ajax):?>
    <div class="tip"> <span> <?php echo $this->translate('Nobody has created an blog yet.');?>
      <?php if ($this->can_create):?>
      <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create','module'=>'sesblog'), "sesblog_general",true).'">', '</a>'); ?>
      <?php endif; ?>
      </span> </div>
    <?php endif; ?>
    <?php if($this->loadOptionData == 'pagging'): ?>
    <?php echo $this->paginationControl($this->paginatorCategory, null, array("_pagging.tpl", "sesblog"),array('identityWidget'=>$randonNumber)); ?>
    <?php endif; ?>
    <?php if(!$this->is_ajax){ ?>
  </div>
</div>
<?php if($this->loadOptionData != 'pagging') { ?>
<div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
<div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
<?php  } ?>
<?php } ?>
<script type="text/javascript">
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
	viewMoreHide_<?php echo $randonNumber; ?>();
	function viewMoreHide_<?php echo $randonNumber; ?>() {
		if ($('view_more_<?php echo $randonNumber; ?>'))
		$('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginatorCategory->count() == 0 ? 'none' : ($this->paginatorCategory->count() == $this->paginatorCategory->getCurrentPageNumber() ? 'none' : '' )) ?>";
	}
	function viewMore_<?php echo $randonNumber; ?> (){
		document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
		document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
		en4.core.request.send(new Request.HTML({
			method: 'post',
			'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>",
			'data': {
			format: 'html',
			page: <?php echo $this->page + 1; ?>,    
			params :'<?php echo json_encode($this->params); ?>', 
			is_ajax : 1,
			identity : '<?php echo $randonNumber; ?>',
			},
			onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('category-blog-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('category-blog-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
        jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
          });
        
			}
		}));
		return false;
	}
	<?php if(!$this->is_ajax){ ?>
		function paggingNumber<?php echo $randonNumber; ?>(pageNum){
			sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','block');
			en4.core.request.send(new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>",
				'data': {
					format: 'html',
					page: pageNum,    
					params :'<?php echo json_encode($this->params); ?>', 
					is_ajax : 1,
					identity : '<?php echo $randonNumber; ?>',
					type:'<?php echo $this->view_type; ?>'
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','none');
					document.getElementById('category-blog-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
          jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
          });
					dynamicWidth();
				}
			}));
			return false;
		}
	<?php } ?>
</script> 
