<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _channelInfo.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
if ($channel->file_id) {
    echo $this->htmlLink($channel->getHref(), "<span class='video_overlay'></span><span class='watch_now_btn'>". $this->translate('watch now')."</span>" . $this->itemBackgroundPhoto($channel, 'thumb.main', $channel->getTitle(), array('tag' => 'i')) . $content);
} else {
    echo $this->htmlLink($channel->getHref(), "<span class='video_overlay'></span><span class='watch_now_btn'>". $this->translate('watch now')."</span>" . $content);
}
?>
<div class="sitevideo_channel_horizontal_info">
    <?php if (in_array('title', $this->channelOption)) : ?>
        <h4 class="sitevideo_channel_horizontal_title">
            <?php $titleTruncationLimit = $this->titleTruncation; ?>
            <?php echo $this->htmlLink($channel->getHref(), $this->string()->truncate($this->string()->stripTags($channel->getTitle()), $titleTruncationLimit)); ?>

        </h4>
    <?php endif; ?>
    <span class="site_video_author_name">
        <?php if (in_array('owner', $this->channelOption)) : ?>
            <?php
            $owner = $channel->getOwner();
            ?>

            <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()); ?>


        <?php endif; ?>
    </span>
</div>
<div class="sitevideo_info">
    <div class="sitevideo_desc">
        <?php echo $this->shareLinks($channel, $this->channelOption); ?>
    </div>
</div>