<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _gridView2.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>

<li class="sesblog_grid_two" style="width:<?php echo is_numeric($allParams['width_advgrid']) ? $allParams['width_advgrid'].'px' : $allParams['width_advgrid'] ?>;">
  <article>
    <div class="sesblog_grid_two_inner sesblog_thumb">
      <div class="sesblog_grid_thumb" style="height:<?php echo is_numeric($allParams['height_advgrid']) ? $allParams['height_advgrid'].'px' : $allParams['height_advgrid'] ?>;">
        <a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="sesblog_thumb_img"><img src="<?php echo $item->getPhotoUrl(); ?>" alt="" align="left" /></a>
        
        <?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
        
        <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview2plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview2limit']));?>
      
      </div>
    </div>
    <div class="sesblog_grid_info">
      <div class="sesblog_grid_two_header">
        <div class="sesblog_grid_two_category_title">
        <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'showin' => 1)); ?>
         </div>
        <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>     
      </div>
     
      <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_advgrid2'], 'allParams' => $allParams, 'divclass' => 'sesblog_gird_title'));?>

      <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
      
        <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting'));?>
      <?php endif;?>
      <div class="sesblog_grid_meta_tegs">
        <ul>
          <li><?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2)); ?></li>
          <li><?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 4));?></li>
        </ul>
      </div>
      <div class="sesblog_grid_contant">
      <?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_advgrid'], 'showDes' => 'descriptionadvgrid')); ?>
      </div>
      <div class="sesblog_list_stats sesbasic_text_light">
        <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
      </div>
      
      <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview2plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview2limit']));?>
    </div>
  </article>
</li>
