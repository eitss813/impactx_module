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

<ul class="sesblog_cat_iconlist_container sesbasic_clearfix clear sesbasic_bxs <?php echo ($allParams['alignContent'] == 'left' ? 'gridleft' : ($allParams['alignContent'] == 'right' ? 'gridright' : '')) ?>">	
  <li class="sesblog_cat_iconlist_head"><?php echo $this->translate($allParams['titleC']); ?></li>
  <?php foreach( $this->paginator as $item ): ?>
    <li class="sesblog_cat_iconlist"  style="height:<?php echo is_numeric($allParams['height']) ? $allParams['height'].'px' : $allParams['height'] ?>;width:<?php echo is_numeric($allParams['width']) ? $allParams['width'].'px' : $allParams['width'] ?>;">
      <a href="<?php echo $item->getHref(); ?>" style="height:<?php echo is_numeric($allParams['height']) ? $allParams['height'].'px' : $allParams['height'] ?>;">
        <?php if($item->thumbnail != '' && !is_null($item->thumbnail) && intval($item->thumbnail)): ?>
          <?php $thumbnail = Engine_Api::_()->storage()->get($item->thumbnail); ?>
          <?php if($thumbnail) { ?>
            <img class="list_main_img" src="<?php echo $thumbnail->getPhotoUrl('thumb.thumb'); ?>">
          <?php } ?>
        <?php endif;?>
        <div class="sesblog_category_icon_block">
          <div>
          <?php if(in_array('title', $allParams['show_criteria'])){ ?>
            <span class="sesblog_cat_iconlist_title"><?php echo $item->category_name; ?></span>
          <?php } ?>
          <?php if(in_array('countBlogs', $allParams['show_criteria'])){ ?>
            <span class="sesblog_cat_iconlist_count"><?php echo $this->translate(array('%s blog', '%s blogs', $item->total_blogs_categories), $this->locale()->toNumber($item->total_blogs_categories))?></span>
          <?php } ?>
          </div>
        <span class="sesblog_cat_iconlist_icon" style="background-color:<?php echo $item->color ? '#'.$item->color : '#000'; ?>;height:<?php echo is_numeric($allParams['height']) ? $allParams['height'].'px' : $allParams['height'] ?>;">
          <?php if($item->cat_icon != '' && !is_null($item->cat_icon) && intval($item->cat_icon)) { ?>
            <?php $cat_icon = Engine_Api::_()->storage()->get($item->cat_icon); ?>
            <?php if($cat_icon) { ?>
              <img src="<?php echo $cat_icon->getPhotoUrl(); ?>" />
            <?php } ?>
          <?php } ?>
        </span>
       </div>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
