<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _simplegridView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php   $user = $item->getOwner();
        $oldTimeZone = date_default_timezone_get();
        $convert_date = strtotime($item->creation_date);
        date_default_timezone_set($user->timezone);
        $month = date('F',$convert_date);
        $year = date('Y',$convert_date);
        $day = date('j',$convert_date);
        
        ?>
<li class="sesblog_grid_four sesbasic_bxs <?php if((isset($this->my_blogs) && $this->my_blogs)){ ?>isoptions<?php } ?>" style="width:<?php echo is_numeric($allParams['width_supergrid']) ? $allParams['width_supergrid'].'px' : $allParams['width_supergrid'] ?>;">
	<div class="sesblog_grid_inner">
		<div class="sesblog_grid_thumb sesblog_thumb" style="height:<?php echo is_numeric($allParams['height_supergrid']) ? $allParams['height_supergrid'].'px' : $allParams['height_supergrid'] ?>;">
			<a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="sesblog_thumb_img"><span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span></a>
		</div>
		<div class="sesblog_grid_info clear clearfix sesbm">
      <div class="sesblog_grid_info_inner">
        <div class="sesblog_grid_four_header">
         <div class="sesblog_grid_date_blog sesbasic_text_light">
      <?php if($item->publish_date): ?>
        <i class="far fa-calendar"></i> <?php echo date('M d',$convert_date);?>
      <?php else: ?>
        <?php echo date('M d',$convert_date);?>
      <?php endif; ?>
    </div>
          
          <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
        </div>
        
        <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_supergrid'], 'allParams' => $allParams, 'divclass' => 'sesblog_second_grid_info_title'));?>

        <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
          <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => ''));?>
        <?php endif;?>
        
        <?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_supergrid'], 'showDes' => 'descriptionsupergrid')); ?>
        
        <div class="sesblog_grid_meta_block">
          <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 3)); ?>
          <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
        </div>
        <div class="sesblog_grid_four_stats sesblog_list_stats sesbasic_text_light">
          <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
        </div>
      </div>
      <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview4plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview4limit']));?>
		</div>
	</div>
</li>
<?php date_default_timezone_set($oldTimeZone); ?>
