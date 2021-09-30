<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _simplelistView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<li class="sesblog_list_second_blog sesbasic_clearfix clear">
	<div class="sesblog_list_thumb sesblog_thumb" style="height:<?php echo is_numeric($allParams['height_simplelist']) ? $allParams['height_simplelist'].'px' : $allParams['height_simplelist'] ?>;width:<?php echo is_numeric($allParams['width_simplelist']) ? $allParams['width_simplelist'].'px' : $allParams['width_simplelist'] ?>;">
    
    <a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="sesblog_thumb_img"><span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span></a>
    
    <?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2));?>
		
		<?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
  </div>
	<div class="sesblog_list_info">
	  <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
    	<?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting floatR', 'style' => ''));?>
    <?php endif;?>
    
    <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_simplelist'], 'allParams' => $allParams, 'divclass'=>'sesblog_list_info_title'));?>

    <div class="sesblog_admin_list">
    
      <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2)); ?>
			<?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'withIcon' => 1)); ?>
       <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
      <?php echo $this->partial('widgets-data/_location.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
			
      <div class="sesblog_list_stats sesbasic_text_light sesblog_list_stats">
        <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
      </div>
    
		</div>
		
		<?php echo $this->partial('widgets-data/_description.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'truncation' => $allParams['description_truncation_simplelist'], 'showDes' => 'descriptionsimplelist')); ?>
		
    <div class="sesblog_list_second_blog_footer">
      <?php echo $this->partial('widgets-data/_readmore.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
      <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_listview2plusicon'], 'sharelimit' => $allParams['socialshare_icon_listview2limit']));?>
    </div>
    <?php echo $this->partial('widgets-data/_editdelete_options.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'my_blogs' => $this->my_blogs, 'can_edit' => $this->can_edit, 'can_delete' => $this->can_delete));?>
	</div>
</li>
