<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _advlistView2.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<li class="sesblog_list_fourth_blog sesbasic_clearfix clear">
  <div class="sesblog_list_full_thumb sesblog_list_thumb sesblog_thumb">
    <a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesblog_chanel' ? 'sesblog_thumb_img' : 'sesblog_thumb_nolightbox' ?>">
      <img src="<?php echo $item->getPhotoUrl(); ?>" alt="" align="left" style="max-height:350px;" /></a>
      
      <?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
    </div>
    <div class="sesblog_list_full_view_info">
    <div class="sesblog_list_fourth_blog_meta">
      <ul>
        <li class="sesblog_list_fourth_blog_meta_owner"><?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2)); ?></li>
        
        <li class="sesblog_list_fourth_blog_meta_category"><?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'withIcon' => 1)); ?></li>
        
        <li class="sesblog_list_fourth_blog_meta_location"><?php echo $this->partial('widgets-data/_location.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?></li>
        
        <li class="sesblog_list_fourth_blog_meta_read"><?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?></li>
      </ul>
    </div>
    
    <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_list'], 'allParams' => $allParams, 'divclass'=>'sesblog_list_info_title'));?>
    
    <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
			<?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => ''));?>
		<?php endif;?> 
		
    <div class="sesblog_list_stats sesblog_list_four_stats sesbasic_text_light">
      <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
		</div>
		
		<?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_list'], 'showDes' => 'descriptionlist')); ?>
    
    <?php echo $this->partial('widgets-data/_readmore.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
    
		<?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_listview4plusicon'], 'sharelimit' => $allParams['socialshare_icon_listview4limit']));?>
		
  </div>
</li>
