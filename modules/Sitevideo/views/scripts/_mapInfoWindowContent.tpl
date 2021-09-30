<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mapInfoWindowContent.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div id="content">
	<div class="sitevideo_map_info_tip o_hidden">
    <div class="sitevideo_map_info_tip_top o_hidden">
      <div class="fright">
        <span >
          <?php if ($this->sitevideo->featured): ?>
              <i class="sitevideo_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
          <?php endif; ?>
        </span>
        <span>
          <?php if (!empty($this->sitevideo->sponsored)): ?>
              <i class="sitevideo_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
          <?php endif; ?>
        </span>
      </div>
      <div class="sitevideo_map_info_tip_title">
        <?php echo $this->htmlLink($this->sitevideo->getHref(), $this->sitevideo->getTitle()) ?>
      </div>
    </div>
    <div class="sitevideo_map_info_tip_photo prelative" >
        <?php
        if ($this->sitevideo->photo_id) {
            echo $this->htmlLink($this->sitevideo->getHref(), $this->itemPhoto($this->sitevideo,'thumb.normal'));
        } else {
            echo $this->htmlLink($this->sitevideo->getHref(), '');
        }
        ?>
    </div>
    
    <div class="sitevideo_map_info_tip_info">
      <?php $count = $this->locale()->toNumber($this->sitevideo->view_count); ?>
      <?php $countText = $this->translate(array('%s view', '%s views', $this->sitevideo->view_count), $count); ?>
      <div title ="<?php echo $countText; ?>"><?php echo $countText; ?></div>
  
      <?php $count = $this->locale()->toNumber($this->sitevideo->likes()->getLikeCount()); ?>
      <?php $countText = $this->translate(array('%s like', '%s likes', $this->sitevideo->like_count), $count); ?>
      <div title="<?php echo $countText; ?>"><?php echo $countText; ?></div>
      
      <?php $count = $this->locale()->toNumber($this->sitevideo->comments()->getCommentCount()); ?>
      <?php $countText = $this->translate(array('%s comment', '%s comments', $this->sitevideo->comment_count), $count); ?>
      <div title="<?php echo $countText; ?>"><?php echo $countText; ?></div>
    </div>

    </div>

</div>