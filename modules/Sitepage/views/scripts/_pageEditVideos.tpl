<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-video.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div>

    <div class="clr">
        <?php if ($this->upload_video): ?>
            <?php if (!$this->integratedWithVideo): ?>
                <?php echo $this->htmlLink(array('route' => "video_general", 'action' => 'create', 'parent_type' => $this->sitepage->getType(), 'parent_id' => $this->sitepage->page_id), $this->translate('Add New Video'), array('class' => 'icon seaocore_icon_add')) ?>
            <?php else: ?>
                <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                    <?php echo $this->htmlLink(array('route' => "sitevideo_video_general", 'action' => 'create', 'parent_type' => $this->sitepage->getType(), 'parent_id' => $this->sitepage->page_id), $this->translate('Add New Video'), array('data-SmoothboxSEAOClass' => 'seao_add_video_lightbox', 'class' => 'seao_smoothbox icon seaocore_icon_add')) ?>
                <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => "sitevideo_video_general", 'action' => 'create', 'parent_type' => $this->sitepage->getType(), 'parent_id' => $this->sitepage->page_id), $this->translate('Add New Video'), array('class' => 'icon seaocore_icon_add')) ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <form action="<?php echo $this->escape($this->editVideoForm->getAction()) ?>" method="<?php echo $this->escape($this->editVideoForm->getMethod()) ?>">
        <div>
            <ul class='sitecrowdfunding_edit_media' id="video">
                <input name="form_type" type="hidden" value="edit_videos" />
                <?php if (!empty($this->count)): ?>
                    <?php foreach ($this->videos as $item): ?>
                        <li>
                            <div class="sitecrowdfunding_edit_media_thumb">
                                <?php
                                if ($item->photo_id)
                                    echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array());
                                else
                                    echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
                                ?>
                                <?php if ($item->duration): ?>
                                    <span class="sitecrowdfunding_video_length fright">
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
                                <?php endif; ?>
                            </div>
                            <div class="sitecrowdfunding_edit_media_info">
                                <?php
                                $key = $item->getGuid();
                                echo $this->editVideoForm->getSubForm($key)->render($this);
                                ?>
                                <div class='sitecrowdfunding_edit_media_options'>
                                    <div class="sitecrowdfunding_edit_media_options_check fleft">
                                        <input id="main_video_id_<?php echo $item->video_id ?>" type="radio" name="cover" value="<?php echo $item->video_id ?>" <?php if ($this->project->video_id == $item->video_id): ?> checked="checked"<?php endif; ?> />
                                    </div>
                                    <div class="sitecrowdfunding_edit_media_options_label fleft">
                                        <label for="main_video_id_<?php echo $item->video_id ?>"><?php echo $this->translate('Main Video'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?><br />
                    <div class="tip">
                        <span>
                            <?php if (!$this->integratedWithVideo): ?>
                                <?php $url = $this->url(array('action' => 'create', 'project_id' => $this->sitepage->page_id, 'content_id' => $this->identity), "video_general", true); ?>
                                <?php echo $this->translate('There are currently no videos in this page. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                            <?php else: ?>
                            <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                                <?php $url = $this->url(array('action' => 'create', 'parent_type' => $this->sitepage->getType(), 'parent_id' => $this->sitepage->page_id, 'content_id' => $this->identity), "sitevideo_video_general", true); ?>
                                <?php echo $this->translate('There are currently no videos in this page. %1$sClick here%2$s to add your first video.', "<a class='seao_smoothbox' data-SmoothboxSEAOClass='seao_add_video_lightbox' href='$url'>", "</a>"); ?>
                            <?php else: ?>
                                <?php $url = $this->url(array('action' => 'create', 'parent_type' => $this->sitepage->getType(), 'parent_id' => $this->sitepage->page_id, 'content_id' => $this->identity), "sitevideo_video_general", true); ?>
                                <?php echo $this->translate('There are currently no videos in this page. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                            <?php endif; ?>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
            </ul>
            <?php if (!empty($this->count)): ?>
                <div class="sitecrowdfunding_edit_videos_button">
                    <?php echo $this->editVideoForm->button ?>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>