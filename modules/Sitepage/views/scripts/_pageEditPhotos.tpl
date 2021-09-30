<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div>


    <form action="<?php echo $this->escape($this->albumForm->getAction()) ?>" method="<?php echo $this->escape($this->albumForm->getMethod()) ?>">
        <?php echo $this->albumForm->album_id; ?>
        <ul class='sitecrowdfunding_edit_media' id="photo">
            <input name="form_type" type="hidden" value="edit_photos" />
            <?php if (!empty($this->photosCount)): ?>
                <?php foreach ($this->paginator as $photo): ?>
                    <li>
                        <div class="sitecrowdfunding_edit_media_thumb"> <?php echo $this->itemPhoto($photo, '') ?> </div>
                        <div class="sitecrowdfunding_edit_media_info">
                            <?php
                                        $key = $photo->getGuid();
                            echo $this->albumForm->getSubForm($key)->render($this);
                            ?>
                            <div class='sitecrowdfunding_edit_media_options'>
                                <div class="sitecrowdfunding_edit_media_options_check fleft">
                                    <input id="main_photo_id_<?php echo $photo->photo_id ?>" type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if ($this->project->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
                                </div>
                                <div class="sitecrowdfunding_edit_media_options_label fleft">
                                    <label for="main_photo_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Main Photo'); ?></label>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <br />
                <div class="tip">
                    <span>
                        <?php $url = $this->url(array('project_id' => $this->project_id), 'sitecrowdfunding_photoalbumupload', true); ?>
                        <?php echo $this->translate('There are currently no photos in this project. %1$sClick here%2$s to add photos now!', "<a href='$url'>", "</a>"); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (!empty($this->photosCount)): ?>
                <div class="sitecrowdfunding_edit_photos_button">
                    <?php echo $this->albumForm->submit->render(); ?>
                </div>
            <?php endif; ?>

        </ul>
    </form>
</div>