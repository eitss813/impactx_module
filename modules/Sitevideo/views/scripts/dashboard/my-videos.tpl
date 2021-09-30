<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my-videos.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    .fade
    {
        opacity: 0.4;
        filter: alpha(opacity=40);
    }
</style>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul id="my-channel-favourite-videos" >
        <?php foreach ($this->paginator as $item): ?>
            <?php $class = "normal"; ?>
            <li id="f_<?php echo $item->video_id ?>">
                <?php if (in_array($item->video_id, $this->videoIds)) : ?>
                    <?php $class = "fade"; ?>
                <?php else : ?>
                    <span class="video_length bold" id="s_<?php echo $item->video_id; ?>" onclick="videoObj.add('<?php echo $item->video_id ?>')">+</span>
                <?php endif; ?>
                <?php
                if ($item->photo_id) {
                    echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'), array('class' => $class, 'id' => 'l_' . $item->video_id));
                } else {
                    echo $this->htmlLink($item->getHref(), '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/video_default.png" >', array('class' => $class, 'id' => 'l_' . $item->video_id));
                }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php  else : ?>
<div class="tip">
    <span>
        No videos found in selected criteria.
    </span>
</div>
<?php endif; ?>