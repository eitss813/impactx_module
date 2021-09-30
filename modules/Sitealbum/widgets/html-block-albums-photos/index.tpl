<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
if ($this->sitealbumLendingBlockValue):
    echo '<div id="show_help_content">' . $this->sitealbumLendingBlockValue . '</div>';
else:
    ?>
    <?php
    echo '<h2 style="text-align: center; margin-bottom: 10px; font-size: 26px; background-color: transparent; border: 0px none; padding: 10px 0px;"><strong>Wherever you go your photos will follow you.</strong></h2>
<p style="font-size: 18px; width: 80%; margin: 0px auto; text-align: center; padding: 10px 0px; line-height: 24px;">Find beautiful photos shared by a community of&nbsp; professional photographers. Share and upload personal photographs and connect with other enthusiasts.</p>';
endif;
?>

<?php if ($this->showButton): ?>
    <div class='txt_center html_block_buttons'>
        <?php if ($this->firstButton): ?>
            <a class='button' href='<?php echo $this->firstButtonTitleLink; ?>'><?php echo $this->translate($this->firstButtonTitle); ?></a>
        <?php endif; ?>
        <?php if ($this->secondButton): ?>
            <a class='button' href='<?php echo $this->secondButtonTitleLink; ?>'><?php echo $this->translate($this->secondButtonTitle); ?></a>
        <?php endif; ?>
        <?php if ($this->uploadButton): ?>
            <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
             <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" data-SmoothboxSEAOClass="seao_add_photo_lightbox" class="seao_smoothbox button"><?php echo $this->translate($this->uploadButtonTitle) ?>
             </a>
            <?php else:?>
                <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" class="button"><?php echo $this->translate($this->uploadButtonTitle) ?>                </a>
            <?php endif;?>
        <?php endif; ?>
    </div>
<?php endif; ?>