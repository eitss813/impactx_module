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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php $className = 'sitechannel_list_popular_channels' . $this->identity; ?>
<?php $i = 0; ?>
<ul class='videos_manage siteevideo_channels_grid_view siteevideo_videos_grid_view o_hidden' id='videos_manage'>
    <?php if ($this->titleLink && $this->titleLinkPosition == 'top') : ?>
        <li>
            <span class="seaocore_sidebar_more_link">
                <?php echo $this->translate($this->titleLink); ?>
            </span>
        </li>
    <?php endif; ?>

    <?php foreach ($this->paginator as $item): ?>
        <li>
            <div class="sitevideo_thumb_wrapper" style=" width:<?php echo $this->channelWidth; ?>px; height:<?php echo $this->channelHeight; ?>px;">

                <?php $fsContent = ""; ?>
                <?php if ($item->featured): ?>
                    <?php $fsContent .= '<div class="sitevideo_featured">' . $this->translate("Featured") . '</div>'; ?>
                <?php endif; ?>
                <?php if ($item->sponsored): ?>
                    <?php $fsContent .= '<div class="sitevideo_sponsored" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                <?php endif; ?>
                <?php if (in_array('numberOfVideos', $this->channelInfo)) : ?>
                    <?php $fsContent .= '<div class="sitevideo_channels_videos_count">' . $this->translate(array('%s video', '%s videos', $item->videos_count), $this->locale()->toNumber($item->videos_count)) . '</div>' ?>
                <?php endif; ?>
                <?php
                if ($item->file_id) {
                    echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='watch_now_btn'>" . $this->translate('watch now') . "</span>" . $fsContent . $this->itemBackgroundPhoto($item, $this->thumbnailType, null, array('tag' => 'i')));
                } else {
                    echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='watch_now_btn'>" . $this->translate('watch now') . "</span>" . $fsContent);
                }
                ?>
                <div class="sitevideo_info">
                    <div class="sitevideo_bottom_info">
                        <?php if (in_array('title', $this->channelInfo)) : ?>
                            <h3>
                                <?php $titleTruncationLimit = ($this->titleTruncationGridNVideoView ? $this->titleTruncationGridNVideoView : $this->titleTruncation); ?>
                                <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $titleTruncationLimit)); ?>
                            </h3>
                        <?php endif; ?>
                        <?php if (in_array('owner', $this->channelInfo)) : ?>
                            <?php
                            $owner = $item->getOwner();
                            ?>
                            <div class='site_video_author_name clr'>
                                <div class="fleft"><?php echo $this->translate('by %s', $this->htmlLink($owner->getHref(), $owner->getTitle())); ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="sitevideo_stats clr">
                            <span class="video_views">
                                <?php if (in_array('subscribe', $this->channelInfo)) : ?>
                                    <?php $count = $this->locale()->toNumber($item->subscribe_count); ?>
                                    <?php $countText = $this->translate(array('%s subscriber', '%s subscribers', $item->subscribe_count), $count); ?>
                                    <span class="sitevideo_bottom_info_subscribers"  title="<?php echo $countText; ?>">
                                        <?php echo $count; ?> 
                                    </span>
                                <?php endif; ?>
                                <?php if (in_array('like', $this->channelInfo)) : ?>
                                    <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                                    <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                                    <span class="sitevideo_bottom_info_likes" title="<?php echo $countText; ?>">
                                        <?php echo $count; ?>    
                                    </span>
                                <?php endif; ?>
                                <?php if (in_array('comment', $this->channelInfo)) : ?>
                                    <?php $count = $this->locale()->toNumber($item->comments()->getCommentCount()); ?>
                                    <?php $countText = $this->translate(array('%s comment', '%s comments', $item->comment_count), $count); ?>
                                    <span class="sitevideo_bottom_info_comment" title="<?php echo $countText; ?>">
                                        <?php echo $count; ?>   
                                    </span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if (in_array('rating', $this->channelInfo)) : ?>
                            <div class="sitevideo_ratings">
                                <?php echo $this->ratingInfo($item, array('widget_id' => $identity . '_a')); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="sitevideo_desc">
                        <?php echo $this->shareLinks($item, $this->channelInfo); ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
    <?php if ($this->titleLink && $this->titleLinkPosition == 'bottom') : ?>
        <li>
            <span class="seaocore_sidebar_more_link">
                <?php echo $this->translate($this->titleLink); ?>
            </span>
        </li>
    <?php endif; ?>   
</ul>

