<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _managequicklinks.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isChannelAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1);
$type = 'channel';
if($this->controllerName=='video' && $this->actionName='manage')
    $type = 'video';
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<div class="sitevideo_myvideos_top o_hidden b_medium">
    <div class="fleft">
        <?php if(in_array('video',$this->topNavigationLink)) : ?>
        <span class="sitevideo_link_wrap fleft">
            <i class="sitevideo_icon item_icon_sitevideo_video"></i>
            <a href="<?php echo $this->url(array('action' => 'manage'), 'sitevideo_video_general', true); ?>"  class="bold my_videos_video  <?php echo  ($type=='video')?'active':''; ?>" id="list_videos_link">
                <?php
                echo $this->translate('Videos');
                ?>
            </a>
        </span>&nbsp;&nbsp;
        <?php endif; ?>
        <?php if(in_array('channel',$this->topNavigationLink) && $isChannelAllowed) : ?>
        <span class="sitevideo_link_wrap fleft">
            <i class="sitechannel_icon item_icon_sitechannel_channel"></i>
            <a href="<?php echo $this->url(array('action' => 'manage'), 'sitevideo_channel_general', true); ?>"  class="bold  my_videos_channel <?php echo  ($type=='channel')?'active':''; ?>" id="list_videos_link">
                <?php
                echo $this->translate('Channels') . '</a>';
                ?>

        </span>&nbsp;&nbsp;
        <?php endif; ?>
    </div>
    <div class="fright my_videos_top_links_right">
        <?php if(in_array('createVideo',$this->topNavigationLink) && $this->canUploadVideo) : ?>
        <span class="sitevideo_link_wrap">
            <i class="sitevideo_icon item_icon_sitevideo_video"></i>
            <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true); ?>" data-smoothboxseaoclass="seao_add_video_lightbox"  class="seao_smoothbox bold upload_new_video" id="list_videos_link">
                <?php
                echo $this->translate('Post New Video') . '</a>';
                ?>
            <?php else:?>
                <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true); ?>" class="bold upload_new_video" id="list_videos_link">
                <?php
                    echo $this->translate('Post New Video') . '</a>';
                ?>
            <?php endif;?>
        </span>&nbsp;&nbsp;
        <?php endif; ?>
        <?php if(in_array('createChannel',$this->topNavigationLink) && $isChannelAllowed && $this->canCreateChannel) : ?>
        <span class="sitevideo_link_wrap fright">
            <i class="sitechannel_icon item_icon_sitechannel_channel"></i>
            <a href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_general', true); ?>"  class="bold seaocore_icon_add <?php echo  ($type=='channel')?'active':''; ?>" id="list_videos_link">
                <?php
                echo $this->translate('Create New Channel') . '</a>';
                ?>

        </span>&nbsp;&nbsp;
        <?php endif;?>
    </div>
</div>