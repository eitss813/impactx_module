<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitevideo_dashboard_content">
    <?php echo $this->partial('application/modules/Sitevideo/views/scripts/dashboard/header.tpl', array('channel' => $this->channel)); ?>
    <div class="sitevideo_video_form">
        <div class="global_form">
            <div>
                <div>
                    <h3><?php echo $this->translate("Edit Channel Photos"); ?></h3>
                    <p class="form-description"><?php echo $this->translate("Edit and manage the photos of your channel below."); ?>
                        <?php if (!empty($this->upload_photo)): ?>
                        <div class="clr">
                            <?php echo $this->htmlLink(array('route' => "sitevideo_photoalbumupload", 'album_id' => $this->album_id, 'channel_id' => $this->channel_id), $this->translate('Add New Photos'), array('class' => 'buttonlink icon_photos_new')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->paginator->count() > 0): ?>
                        <?php echo $this->paginationControl($this->paginator); ?>
                    <?php endif; ?>

                    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
                        <?php echo $this->form->album_id; ?>
                        <ul class='sitevideo_edit_media' id="photo">
                            <?php if (!empty($this->count)): ?>
                                <?php foreach ($this->paginator as $photo): ?>
                                    <li>
                                        <div class="sitevideo_edit_media_thumb"> <?php echo $this->itemPhoto($photo, 'thumb.normal') ?> </div>
                                        <div class="sitevideo_edit_media_info">
                                            <?php
                                            $key = $photo->getGuid();
                                            echo $this->form->getSubForm($key)->render($this);
                                            ?>
                                            <div class='sitevideo_edit_media_options'>
                                                <div class="sitevideo_edit_media_options_check">
                                                    <input id="main_photo_id_<?php echo $photo->photo_id ?>" type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if ($this->channel->file_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
                                                </div>
                                                <div class="sitevideo_edit_media_options_label">
                                                    <label for="main_photo_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Main Photo'); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?><br />
                                <div class="tip">
                                    <span>
                                        <?php $url = $this->url(array('channel_id' => $this->channel_id), 'sitevideo_photoalbumupload', true); ?>
                                        <?php echo $this->translate('There are currently no photos in this channel. %1$sClick here%2$s to add photos now!', "<a href='$url'>", "</a>"); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </ul>
                        <?php if (!empty($this->count)): ?>
                            <div class="sitevideo_edit_photos_button">
                                <?php echo $this->form->submit->render(); ?>
                            </div>
                        <?php endif; ?>
                    </form>
                    <?php if ($this->paginator->count() > 0): ?>
                        <br />
                        <?php echo $this->paginationControl($this->paginator); ?>
                    <?php endif; ?>
                </div>		
            </div>
        </div>
    </div>			
</div></div>