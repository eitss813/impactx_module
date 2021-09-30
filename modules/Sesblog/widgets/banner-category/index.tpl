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
  $allParams = $this->allParams;
  $baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); 
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); 
  $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); 
  $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); 
?>
<?php if(isset($allParams['sesblog_categorycover_photo']) && !empty($allParams['sesblog_categorycover_photo'])) { ?>
  <div class="sesblog_category_cover sesbasic_bxs sesbm">
    <div class="sesblog_category_cover_inner">
      <div class="sesblog_category_cover_img"  style="background-image:url(<?php echo str_replace('//','/',$baseUrl.'/'.$allParams['sesblog_categorycover_photo']); ?>);"></div>
      <div class="sesblog_category_cover_content">
        <div class="sesblog_category_cover_blocks">
          <div class="sesblog_category_cover_block_img">
            <span style="background-image:url(<?php echo str_replace('//','/',$baseUrl.'/'.$allParams['sesblog_categorycover_photo']); ?>);"></span>
          </div>
          <div class="sesblog_category_cover_block_info">
            <?php if(isset($allParams['title']) && !empty($allParams['title'])): ?>
              <div class="sesblog_category_cover_title"> 
                <?php echo $this->translate($allParams['title']); ?>
              </div>
            <?php endif; ?>
            <?php if(isset($allParams['description']) && !empty($allParams['description'])): ?>
              <div class="sesblog_category_cover_des clear sesbasic_custom_scroll">
                <?php echo $this->translate($allParams['description']);?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } else { ?>
  <div class="sesblog_browse_cat_top sesbm">
    <?php if(isset($allParams['title']) && !empty($allParams['title'])): ?>
      <div class="sesblog_catview_title"> 
        <?php echo $this->translate($allParams['title']); ?>
      </div>
    <?php endif; ?>
    <?php if(isset($allParams['description']) && !empty($allParams['description'])): ?>
      <div class="sesblog_catview_des">
        <?php echo $this->translate($allParams['description']);?>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>
