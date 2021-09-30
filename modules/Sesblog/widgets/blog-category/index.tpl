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

<?php 
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css');
  $allParams = $this->allParams; 
?>

<div class="sesblog_category_grid sesbasic_clearfix sesbasic_bxs">
	<ul>
	  <?php foreach( $this->paginator as $item ):?>
			<li style="width:<?php echo is_numeric($allParams['width']) ? $allParams['width'].'px' : $allParams['width']?>;">
				<div <?php if(($allParams['show_criteria'] != '')) { ?> class="sesblog_thumb_contant" <?php } ?> style="height:<?php echo is_numeric($allParams['height']) ? $allParams['height'].'px' : $allParams['height'] ?>;">
					<a href="<?php echo $item->getHref(); ?>" class="link_img img_animate">
					  <?php if($item->thumbnail != '' && !is_null($item->thumbnail) && intval($item->thumbnail)): ?>
							<img class="list_main_img" src="<?php echo  Engine_Api::_()->storage()->get($item->thumbnail)->getPhotoUrl('thumb.thumb'); ?>">
						<?php endif;?>
						<div <?php if(($allParams['show_criteria'] != '')){ ?> class="sesblog_category_hover" <?php } ?>>
            	<div>
                <?php if(in_array('icon', $allParams['show_criteria']) && $item->cat_icon != '' && !is_null($item->cat_icon) && intval($item->cat_icon)): ?>
                  <?php $icon = Engine_Api::_()->storage()->get($item->cat_icon); ?>
                  <?php if($icon) { ?>
                    <div class="sesblog_square_icon">
                      <img src="<?php echo $icon->getPhotoUrl('thumb.icon'); ?>" />
                    </div>
                  <?php } ?>
                <?php endif;?>
                <?php if(in_array('title', $allParams['show_criteria'])):?>
                  <p class="title"><?php echo $this->translate($item->category_name); ?></p>
                <?php endif;?>
                <?php if(in_array('countBlogs', $allParams['show_criteria'])):?>
                  <p class="count"><?php echo $this->translate(array('%s blog', '%s blogs', $item->total_blogs_categories), $this->locale()->toNumber($item->total_blogs_categories))?></p>
                <?php endif;?>
              </div>
						</div>
					</a>
				</div>
			</li>
		<?php endforeach;?>
	</ul>
</div>
