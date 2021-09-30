<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _advgridView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<li class="sesblog_grid_three sesbasic_bxs <?php if((isset($this->my_blogs) && $this->my_blogs)){ ?>isoptions<?php } ?>" style="width:<?php echo is_numeric($allParams['width_advgrid2']) ? $allParams['width_advgrid2'].'px' : $allParams['width_advgrid2'] ?>;">
  <div class="sesblog_grid_inner">
		<div class="sesblog_grid_thumb sesblog_thumb" style="height:<?php echo is_numeric($allParams['height_advgrid2']) ? $allParams['height_advgrid2'].'px' : $allParams['height_advgrid2'] ?>;">
			<a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="sesblog_thumb_img"><span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span></a>
			
			<?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
			
			<?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview3plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview3limit']));?>
		</div>
		<div class="sesblog_grid_info clear clearfix sesbm">
			<div class="sesblog_grid_meta_block">
        <div class="sesblog_gird-top_blog">
          <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 3)); ?>
          
          <?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 5));?>
          
          <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
        
          <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
          
          <?php echo $this->partial('widgets-data/_location.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
        </div>
			</div>
			
			<?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
        <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting floatR'));?>
      <?php endif;?>
				
      <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_advgrid'], 'allParams' => $allParams, 'divclass' => 'sesblog_grid_three_info_title'));?>
			
			<?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_supergrid'], 'showDes' => 'descriptionsupergrid')); ?>
			
			<?php echo $this->partial('widgets-data/_readmore.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>

      <div class="sesblog_grid_three_footer">
        <div class="sesblog_list_stats sesbasic_text_light">
          <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
        </div>
        <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview3plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview3limit']));?>
      </div>
		</div>
	</div>
</li>
