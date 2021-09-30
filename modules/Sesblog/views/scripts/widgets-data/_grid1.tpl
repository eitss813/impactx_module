<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _gridView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<li class="sesblog_grid sesbasic_bxs <?php if((isset($this->my_blogs) && $this->my_blogs)){ ?>isoptions<?php } ?>" style="width:<?php echo is_numeric($allParams['width_grid']) ? $allParams['width_grid'].'px' : $allParams['width_grid'] ?>;">
  <article>
    <div class="sesblog_grid_inner sesblog_thumb">
      <div class="sesblog_grid_thumb" style="height:<?php echo is_numeric($allParams['height_grid']) ? $this->height_grid.'px' : $allParams['height_grid'] ?>;">
        <a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="sesblog_thumb_img"> <span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span> </a>
        
        <?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
      </div>
      <div>
        <div class="sesblog_grid_info clear clearfix sesbm">
          <div class="sesblog_grid_one_header">
            <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'showin' => 1)); ?>
              
            <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
          </div>
          <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_grid'], 'allParams' => $allParams, 'divclass' => 'sesblog_grid_info_title'));?>

          <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
            <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => ''));?>
          <?php endif;?>
          
          <div class="sesblog_grid_meta_block">
            <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2)); ?>
          </div>
        </div>
        <div class="sesblog_grid_one_footer">
          <?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 4));?>
        
          <div class="sesblog_list_stats sesbasic_text_light">
            <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
          </div>
        </div>
      
        <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview1plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview1limit']));?>
      </div>
  </article>
</li>
