<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editvideos.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="layout_middle">
    <h3>
        <?php echo $this->htmlLink($this->channel->getHref(), $this->channel->getTitle()) ?>
        (<?php echo $this->translate(array('%s video', '%s videos', $this->channel->videos_count), $this->locale()->toNumber($this->channel->videos_count)) ?>)
    </h3>
    <?php if ($this->paginator->count() > 0): ?>
        <?php echo $this->paginationControl($this->paginator); ?>
    <?php endif; ?>
    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
        <?php echo $this->form->channel_id; ?>
        <ul class='channels_editvideos'>
            <?php foreach ($this->paginator as $video): ?>
                <li>
                    <div class="channels_editvideos_video">
                        <?php echo $this->htmlLink($video->getHref(), $this->itemPhoto($video, 'thumb.normal')) ?>
                    </div>
                    <div class="channels_editvideos_info">
                        <?php
                        $key = $video->getGuid();
                        echo $this->form->getSubForm($key)->render($this);
                        ?>
                        <div class="channels_editvideos_cover">
                            <input id="main_video_id_<?php echo $video->video_id ?>" type="radio" name="cover" value="<?php echo $video->getIdentity() ?>" <?php if ($this->channel->video_id == $video->getIdentity()): ?> checked="checked"<?php endif; ?> />
                        </div>
                        <div class="channels_editvideos_label">
                            <label for="main_video_id_<?php echo $video->getIdentity() ?>"><?php echo $this->translate('Main Video'); ?></label>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php echo $this->form->submit->render(); ?>
    </form>
    <?php if ($this->paginator->count() > 0): ?>
        <?php echo $this->paginationControl($this->paginator); ?>
    <?php endif; ?>
</div>