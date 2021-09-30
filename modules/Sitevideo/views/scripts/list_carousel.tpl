<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $video = $this->sitevideo;  ?>
<?php $content = "<div class='sitevideo_stats sitevideo_grid_stats'>"; ?>
<?php if (in_array('creationDate', $this->videoOption)) : ?>
    <?php
    $content .= $this->timestamp(strtotime($video->creation_date));
    ?>
<?php endif; ?>
<?php if (in_array('view', $this->videoOption)) : ?>
    <?php $count = $this->locale()->toNumber($video->view_count); ?>
    <?php $countText = $this->translate(array('%s view', '%s views', $video->view_count), $count); ?>
    <?php
    $content .= '<span class="sitevideo_bottom_info_views" title="' . $countText . '">';
    $content .= $count;
    $content .='</span>';
    ?>
<?php endif; ?>

<?php if (in_array('like', $this->videoOption)) : ?>
    <?php
    $count = $this->locale()->toNumber($video->likes()->getLikeCount());
    $countText = $this->translate(array('%s like', '%s likes', $video->like_count), $count);
    $content .= '<span class="sitevideo_bottom_info_likes" title="' . $countText . '">';
    $content .= $count;
    $content .= ' </span>';
    ?>
<?php endif; ?>
<?php if (in_array('comment', $this->videoOption)) : ?>
    <?php $count = $this->locale()->toNumber($video->comments()->getCommentCount()); ?>
    <?php $countText = $this->translate(array('%s comment', '%s comments', $video->comment_count), $count); ?>
    <?php
    $content .= ' <span class="sitevideo_bottom_info_comment" title="' . $countText . '">';
    $content .= $count;
    $content .= '</span>';
    ?>
<?php endif; ?>
<?php $content .= '</div>'; ?>
<li class="sitevideo_featured_slidebox_block sitevideo_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
    <div id="video_<?php echo $video->video_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_other sitevideo_thumb_wrapper sitevideo_thumb_viewer" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
        <?php
        if ($video->photo_id) {
            echo $this->htmlLink($video->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $this->itemBackgroundPhoto($video, $this->videoViewThumbnailType, $video->getTitle(), array('tag' => 'i',)) . $content);
        } else {
            echo $this->htmlLink($video->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $content);
        }
        ?>
        <div class="sitevideo_featured_slidebox_info"> 
            <span class="sitevideo_featured_slidebox_info_left">                   
                <span class="sitevideo_featured_slidebox_info_title">                                   
                    <?php if (in_array('title', $this->videoOption)) : ?>
                        <?php echo $this->htmlLink($video->getHref(), $this->string()->truncate($this->string()->stripTags($video->getTitle()), 50)) ?>
                    <?php endif; ?>
                </span>
                <span class="site_video_author_name">
                    <?php if (in_array('owner', $this->videoOption)) : ?>
                        <?php
                        $owner = $video->getOwner();
                        ?>
                        <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()); ?>
                    <?php endif; ?>
                </span>
            </span>
            <?php if ($video->duration && in_array('duration', $this->videoOption)): ?>
                <span class="video_length">
                    <?php
                    if ($video->duration >= 3600) {
                        $duration = gmdate("H:i:s", $video->duration);
                    } else {
                        $duration = gmdate("i:s", $video->duration);
                    }
                    ?>
                    <?php echo $duration; ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</li>
