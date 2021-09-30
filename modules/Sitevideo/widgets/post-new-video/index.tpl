<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$url = $this->url(array('action' => 'create'), 'sitevideo_video_general', true);
if(!empty($this->channel)) : 
    $url = $this->url(array('action' => 'create','channel_id'=>$this->channel->channel_id), 'sitevideo_video_general', true);
endif;
?>
<?php if (!$this->upload_button): ?>
    <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox() && $this->openInLightbox): ?>

        <a href="<?php echo $url; ?>" data-SmoothboxSEAOClass="seao_add_video_lightbox" class="sitevideos_video_new seao_smoothbox"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
    <?php else: ?>
        <a href="<?php echo $url; ?>" class="sitevideos_video_new"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
    <?php endif; ?>
<?php else: ?>
    <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox() && $this->openInLightbox): ?>

        <a href="<?php echo $url; ?>" data-SmoothboxSEAOClass="seao_add_video_lightbox" class="sitevideos_video_new seao_smoothbox button"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
    <?php else: ?>
        <a href="<?php echo $url; ?>" class="sitevideos_video_new button"><?php echo $this->translate($this->upload_button_title) ?>
        </a>
    <?php endif; ?>
<?php endif; ?> 