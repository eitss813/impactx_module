<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-video.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="page-videos_container">
    <ul id="page-video" class="grid_wrapper">
        <?php if (!empty($this->count)): ?>
            <?php foreach ($this->videos as $item): ?>
                <li>
                    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'), array()); ?>
                    <div class='video-name'>
                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array()); ?>
                        <?php if ($item->duration): ?>
                            <div class="video_length fright">
                                <span>
                                    <?php
                                    if ($item->duration > 360)
                                        $duration = gmdate("H:i:s", $item->duration);
                                    else
                                        $duration = gmdate("i:s", $item->duration);
                                    if ($duration[0] == '0')
                                        $duration = substr($duration, 1);
                                    echo $duration;
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No Videos'); ?>
                </span>
            </div>
        <?php endif; ?>

    </ul>
</div>