<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: show-details.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<div class="sesblog_view_stats_popup">
  <div class="sesblog_view_popup_con">
    <?php $blogItem = Engine_Api::_()->getItem('sesblog_blog', $this->claimItem->blog_id);?>
    <?php $userItem = Engine_Api::_()->getItem('user', $this->claimItem->user_id);?>
    <div class="sesblog_popup_img_blog">
      <p class="popup_img"><?php echo $this->itemPhoto($blogItem, 'thumb.icon') ?></p>
      <p class="popup_title"><?php echo $blogItem->getTitle();?></p>
    	<p class="owner_title"><b>Blog Owner :</b><span class="owner_des"><?php echo $blogItem->getOwner()->getTitle();?></span></p>
			 <p class="owner_title"><b>Claimed by &nbsp;:</b><span class="owner_des"><?php echo $userItem->getTitle();?></span></p>
    </div>
    <div class="sesblog_popup_owner_blog">
      <p class="owner_title"><b>Reason for Claim:</b></p>
      <p class="owner_des"><?php echo $this->claimItem->description;?></p>
    </div>
  </div>
</div>
