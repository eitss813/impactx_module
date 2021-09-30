<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>

<div class="sesnewsletter_group_wrapper clearfix sesbasic_bxs" style="padding:20px;box-sizing:border-box;background:#<?php echo $this->bgcolor ?>;">
  <?php if(!empty($this->title)) { ?>
  <div class="sesnewsletetr_heading">
    <h3 style="color:#<?php echo $this->headingtextcolor; ?>;text-align:center;font-size:<?php echo $this->headingfontsize; ?>px;font-family:'Arial',sans-serif;margin:0;word-break:break-all;"><?php echo $this->title; ?></h3>
    <div class="border-bottom" style="width:50px;height:2px;background:#<?php echo $this->headingbordercolor; ?>;margin:6px auto 20px;"></div>
  </div>
  <?php } ?>
  <div class="sesnewsletter_group_wrapper_inner">
    <?php  foreach($this->result as $result){ ?>
    <div class="sesnewsletter_group_item wow zoomIn" style="width:48%;text-align:center;display:inline-block;margin:4px;position:relative;margin-bottom:8px;border-radius:6px;overflow:hidden;"> <a href="<?php echo $this->absoluteUrl($result->getHref()); ?>" style="text-decoration:none;">
      <div class="sesnewsletter_content_img" style="width:100%;height:150px;overflow:hidden;">
        <?php $photo = $this->absoluteUrl($result->getPhotoUrl('thumn.profile')); ?>
        <img src="<?php echo $photo; ?>" style="width:100%;height:100%;object-fit:cover;object-position:top" />
        <?php //echo $this->itemPhoto($result, 'thumb.main', $result->getTitle(), array('style' => 'width:100%;height:100%;object-fit:cover;')); ?>
      </div>
      <div class="sesnewsletter_group_cont clearfix"> <span style="text-decoration:none;font-family:'Arial',sans-serif;padding:10px 5px;text-overflow:ellipsis;white-space:nowrap;display:block;overflow:hidden;font-size:<?php echo $this->titlefontsize; ?>px;font-weight:bold;background:#<?php echo $this->titlebgcolor ?>;color:#<?php echo $this->titletextcolor ?>;"><?php echo $this->translate($result->getTitle()); ?></span> </div>
      </a> </div>
    <?php } ?>
  </div>
</div>
