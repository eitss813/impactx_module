<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _advlistView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<li class="sesblog_list_third_blog sesbasic_clearfix clear">
  <div class="sesblog_list_full_thumb sesblog_list_thumb sesblog_thumb">
    <a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesblog_chanel' ? 'sesblog_thumb_img' : 'sesblog_thumb_nolightbox' ?>">
      <img src="<?php echo $item->getPhotoUrl(); ?>" alt="" align="left" style="max-height:400px;"/>
    </a>
    
    <?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
  </div>
  <div class="sesblog_main_cont">
  
    <?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 3)); ?>
    
    <div class="sesblog_list_full_view_info">
    
      <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_list'], 'allParams' => $allParams, 'divclass'=>'sesblog_list_info_title'));?>
        
      <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
        <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting floatR', 'style' => ''));?>
      <?php endif;?> 
    
      <div class="sesblog_list_full_meta">
      
        <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2)); ?>
        
        <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'withIcon' => 1)); ?>
        
        <?php echo $this->partial('widgets-data/_location.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>

        <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
      </div>
      
      <?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_list'], 'showDes' => 'descriptionlist')); ?>
    
      <div class="sesblog_list_third_footer">
        <div class="sesblog_list_stats sesbasic_text_light">
          <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
        </div>

        <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_listview3plusicon'], 'sharelimit' => $allParams['socialshare_icon_listview3limit']));?>
      
      </div>
    
      <?php echo $this->partial('widgets-data/_editdelete_options.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'my_blogs' => $this->my_blogs, 'can_edit' => $this->can_edit, 'can_delete' => $this->can_delete));?>

    </div>
  </div>
</li>
