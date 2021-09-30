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

<?php $channel_id = 0; ?>
<?php if (Engine_Api::_()->core()->hasSubject('sitevideo_channel')): ?>
    <?php $channel_id = Engine_Api::_()->core()->getSubject('sitevideo_channel')->getIdentity();
    ?>
<?php endif; ?>
<?php if (!$this->upload_button): ?>
    <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
        <?php if ($channel_id): ?>
            <div class="seaocore_button">
                <a href="<?php echo $this->url(array('action' => 'create', 'channel_id' => $channel_id), 'sitevideo_video_general', true) ?>" data-SmoothboxSEAOClass="seao_add_video_lightbox" class="sitevideos_video_new seao_smoothbox button"><span class="seaocore_icon_upload"><?php echo $this->translate($this->upload_button_title) ?></span>
                </a>
            </div>
        <?php else: ?>
            <div class="seaocore_button">
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true) ?>" data-SmoothboxSEAOClass="seao_add_video_lightbox" class="sitevideos_video_new seao_smoothbox button seaocore_icon_upload"><?php echo $this->translate($this->upload_button_title) ?>

                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($channel_id): ?>
            <div class="seaocore_button">
                <a href="<?php echo $this->url(array('action' => 'create', 'channel_id' => $channel_id), 'sitevideo_video_general', true) ?>" class="sitevideos_video_new button"><?php echo $this->translate($this->upload_button_title) ?>
                </a>
            </div>
        <?php else: ?>
            <div class="seaocore_button">
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true) ?>" class="sitevideos_video_new button"><?php echo $this->translate($this->upload_button_title) ?>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php else: ?>
    <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
        <?php if ($channel_id): ?>
            <div>
                <a href="<?php echo $this->url(array('action' => 'create', 'channel_id' => $channel_id), 'sitevideo_video_general', true) ?>" data-SmoothboxSEAOClass="seao_add_video_lightbox" class="sitevideos_video_new seao_smoothbox button seaocore_icon_upload"><?php echo $this->translate($this->upload_button_title) ?>
                </a>
            </div>
        <?php else: ?>
            <div>
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true) ?>" data-SmoothboxSEAOClass="seao_add_video_lightbox" class="sitevideos_video_new seao_smoothbox button seaocore_icon_upload"><?php echo $this->translate($this->upload_button_title) ?>
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($channel_id): ?>
            <div>
                <a href="<?php echo $this->url(array('action' => 'create', 'channel_id' => $channel_id), 'sitevideo_video_general', true) ?>" class="sitevideos_video_new button"><?php echo $this->translate($this->upload_button_title) ?>
                </a>
            </div>
        <?php else: ?>
            <div>
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true) ?>" class="sitevideos_video_new button"><?php echo $this->translate($this->upload_button_title) ?>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>