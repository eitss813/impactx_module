<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _ratingOptions.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $item = $this->subject; $viewer = $this->viewer;?>
<div class="sesblog_review_listing_footer clear sesbasic_clearfix">
	<?php if(in_array('socialSharing', $this->stats) || in_array('likeButton', $this->stats)):?>
		<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
		<div class="sesblog_list_share_btns floatL"> 
		  <?php $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
			<?php if(in_array('likeButton', $this->stats) && $canComment):?>
				<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($item->review_id,$item->getType()); ?>
				<a href="javascript:;" data-url="<?php echo $item->review_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_like_sesblog_review_<?php echo $item->review_id ?> sesblog_like_sesblog_review <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
			<?php endif;?>
			<?php if(in_array('socialSharing', $this->stats)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
        
        <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

			<?php endif;?>
		</div>  
	<?php endif;?>
  <?php $ownerSelf = $viewer->getIdentity() == $item->owner_id ? true : false; ?>
	<div class="sesblog_review_listing_btn_right floatR">
		<?php if($item->authorization()->isAllowed($viewer, 'edit')) { ?>     
			<a class="fa fa-edit sesbasic_button sesbasic_button_icon <?php if($ownerSelf) { echo 'sesblog_own_update_review'; } ?>" href="<?php echo $this->url(array('route' => 'sesblogreview_view', 'action' => 'edit', 'review_id' => $item->review_id,'format' => 'smoothbox'),'sesblogreview_view',true); ?>" <?php if(!$ownerSelf) { ?> onclick='return opensmoothboxurl(this.href);' <?php  } ?> ><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Edit Review'); ?></span></a>
		<?php } ?>
		<?php if($item->authorization()->isAllowed($viewer, 'delete')) { ?>     
		<a class="fa fa-trash sesbasic_button sesbasic_button_icon" href="<?php echo $this->url(array('route' => 'sesblogreview_view', 'action' => 'delete', 'review_id' => $item->review_id,'format' => 'smoothbox'),'sesblogreview_view',true); ?>" onclick='return opensmoothboxurl(this.href);'><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Delete Review'); ?></span></a>
		<?php } ?>
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.show.report', 1) && $viewer->getIdentity() && in_array('report', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1)): ?>
		<a class="fa fa-flag sesbasic_button sesbasic_button_icon" href="<?php echo $this->url(array('route' => 'default', 'module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $item->getGuid(), 'format' => 'smoothbox',),'default',true); ?>" onclick='return opensmoothboxurl(this.href);'><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Report');?></span></a>
		<?php endif; ?>
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.share', 1) && $viewer->getIdentity() && in_array('share', $this->stats)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)): ?>
		<a class="fa fa-share sesbasic_button sesbasic_button_icon" href="<?php echo $this->url(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $item->getType(), 'id' => $item->getIdentity(), 'format' => 'smoothbox'),'default',true); ?>" onclick='return opensmoothboxurl(this.href);'><span><i class="fa fa-caret-down"></i><?php echo $this->translate('Share Review');?></span></a> 
		<?php endif; ?>
		</div>
</div>
