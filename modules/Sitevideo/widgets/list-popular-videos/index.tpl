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
<?php $className = 'sitevideo_list_popular_videos' . $this->identity; ?>
<?php $i = 0; ?>
<ul class='videos_manage siteevideo_videos_grid_view o_hidden' id='videos_manage'>
    <?php if ($this->titleLink && $this->titleLinkPosition == 'top') : ?>
        <li>
            <span class="seaocore_sidebar_more_link">
                <?php echo $this->translate($this->titleLink); ?>
            </span>
        </li>
    <?php endif; ?>
    <?php foreach ($this->paginator as $item): ?>
        <?php
        if (!$item->main_channel_id) {
            $item->main_channel_id = 0;
        }
        ?>
        <li>
            <div class="sitevideo_thumb_wrapper sitevideo_thumb_viewer"  style=" width:<?php echo $this->videoWidth; ?>px; height:<?php echo $this->videoHeight; ?>px;">
                <?php $fsDuration = ''; ?>
                <?php if ($item->duration && in_array('duration', $this->videoInfo)): ?>

                    <?php $fsDuration .='<span class="video_length">'; ?>
                    <?php
                    if ($item->duration >= 3600) {
                        $duration = gmdate("H:i:s", $item->duration);
                    } else {
                        $duration = gmdate("i:s", $item->duration);
                    }
                    $fsDuration .=$duration;
                    ?>
                    <?php $fsDuration .= "</span>"; ?>
                <?php endif; ?>


                <?php $fsContent = ""; ?>
                <?php if ($item->featured): ?>
                    <?php $fsContent .= '<div class="sitevideo_featured">' . $this->translate("Featured") . '</div>'; ?>
                <?php endif; ?>
                <?php if ($item->sponsored): ?>
                    <?php $fsContent .= '<div class="sitevideo_sponsored" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                <?php endif; ?>

                <?php $content = "<div class='sitevideo_stats sitevideo_grid_stats'>"; ?>
                <?php if (in_array('creationDate', $this->videoInfo)) : ?>
                    <?php
                    $content .= $this->timestamp(strtotime($item->creation_date));
                    ?>
                <?php endif; ?>
                <?php if (in_array('view', $this->videoInfo)) : ?>
                    <?php $count = $this->locale()->toNumber($item->view_count); ?>
                    <?php $countText = $this->translate(array('%s view', '%s views', $item->view_count), $count); ?>
                    <?php
                    $content .= '<span class="sitevideo_bottom_info_views" title="' . $countText . '">';
                    $content .= $count;
                    $content .='</span>';
                    ?>
                <?php endif; ?>

                <?php if (in_array('like', $this->videoInfo)) : ?>
                    <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                    <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                    <?php
                    $content .= '<span class="sitevideo_bottom_info_likes" title="' . $countText . '">';
                    $content .= $count;
                    $content .= ' </span>';
                    ?>
                <?php endif; ?>
                <?php if (in_array('comment', $this->videoInfo)) : ?>
                    <?php $count = $this->locale()->toNumber($item->comments()->getCommentCount()); ?>
                    <?php $countText = $this->translate(array('%s comment', '%s comments', $item->comment_count), $count); ?>
                    <?php
                    $content .= ' <span class="sitevideo_bottom_info_comment" title="' . $countText . '">';
                    $content .= $count;
                    $content .= '</span>';
                    ?>
                <?php endif; ?>
                <?php $content .= '</div>'; ?>
                <?php
                    echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $fsContent . $fsDuration . $this->itemBackgroundPhoto($item, $this->thumbnailType, '', array('tag' => 'i')) . $content);
                ?>
                <div class="sitevideo_info">
                    <div class="sitevideo_bottom_info sitevideo_grid_bott_info">
                        <?php if (in_array('title', $this->videoInfo)) : ?>
                            <h3>
                                <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->videoTitleTruncation)) ?>
                            </h3>
                        <?php endif; ?>
                        <?php if (in_array('location', $this->videoInfo)) : ?>
                            <?php echo $this->videoInfo($item, array('location'), array('truncationLocation' => $this->truncationLocation)); ?>
                        <?php endif; ?>
                        <div class="sitevideo_grid_bottom_info">
                            <?php if (in_array('owner', $this->videoInfo)) : ?>
                                <?php
                             
                                    echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('class' => 'sitevideo_author_logo'));
                               
                                ?>
                            <?php endif; ?>
                            <div class="sitevideo_stats">
                                <span class="video_views">
                                    <?php if (in_array('owner', $this->videoInfo)) : ?>
                                        <span class="site_video_author_name"><?php echo $this->translate("by %s", $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())); ?>    </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if (in_array('rating', $this->videoInfo)) : ?>
                                <div class="sitevideo_ratings">
                                    <?php echo $this->ratingInfo($item, array('widget_id' => $this->identity . '_a')); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="sitevideo_desc">
                        <?php echo $this->shareLinks($item, $this->videoInfo); ?>
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
