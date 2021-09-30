<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _pinboardView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<li class="sesblog_pinboard_list sesbasic_bxs new_image_pinboard_<?php echo $randonNumber; ?>">
	<div class="sesblog_pinboard_list_item <?php if((isset($this->my_blogs) && $this->my_blogs)){ ?>isoptions<?php } ?>">
		<div class="sesblog_pinboard_list_item_top sesblog_thumb sesbasic_clearfix">
			<div class="sesblog_pinboard_list_item_thumb">
				<a href="<?php echo $item->getHref()?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesblog_chanel' ? 'sesblog_thumb_img' : 'sesblog_thumb_nolightbox' ?>"><img src="<?php echo $photoPath; ?>"><span style="background-image:url(<?php echo $photoPath; ?>);display:none;"></span></a>
			</div> 
			
			<?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
		</div>

    <div class="sesblog_pinboard_info_blog sesbasic_clearfix">
      <div class="sesblog_pinboard_list_item_cont sesbasic_clearfix">
        <div class="sesblog_pinboard_header">
          
        <?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 7));?>

          <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
          <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
        </div>
        
        <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_pinboard'], 'allParams' => $allParams, 'divclass' => 'sesblog_pinboard_title'));?>
        
        <div class="sesblog_pinboard_meta_blog">
          <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 3)); ?>
        </div>
        <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
          <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => ''));?>
        <?php endif;?>
        
        <?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_pinboard'], 'showDes' => 'descriptionpinboard')); ?>
        
        <?php echo $this->partial('widgets-data/_readmore.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
        <div class="sesblog_pinboard_list_item_btm sesbm sesbasic_clearfix">
          <div class="sesblog_list_stats sesbasic_text_light">
            <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
          </div>
        
          <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_pinviewplusicon'], 'sharelimit' => $allParams['socialshare_icon_pinviewlimit']));?>
      
          <?php echo $this->partial('widgets-data/_editdelete_options.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'my_blogs' => $this->my_blogs, 'can_edit' => $this->can_edit, 'can_delete' => $this->can_delete));?>
        </div>
      </div>
    </div>
  </div>
</li>
