<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _grid_view.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
    echo $this->htmlLink($video->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $this->itemBackgroundPhoto($video, 'thumb.main', '', array('tag' => 'i')) . $content);
?>
<div class="sitevideo_featured_slidebox_info"> 
    <span class="sitevideo_featured_slidebox_info_left">                   
        <span class="sitevideo_featured_slidebox_info_title">                                   
            <?php if (in_array('title', $this->videoOption)) : ?>
                <?php echo $this->htmlLink($video->getHref(), $this->string()->truncate($this->string()->stripTags($video->getTitle()), $this->titleTruncation)) ?>

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
<div class="sitevideo_info">

    <div class="sitevideo_desc">
        <?php echo $this->shareLinks($video, $this->videoOption); ?>
    </div>
</div>

