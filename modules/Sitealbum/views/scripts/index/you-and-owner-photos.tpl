<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: you-and-owner-photos.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
if($this->showLightBox):
include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<h2><?php echo $this->translate("You and %s", $this->owner->getTitle()) ?></h2>
<div class="layout_middle">
  <ul class="thumbs thumbs_nocaptions">
    <?php	foreach( $this->youAndOwner as $value ):
    $photo=Engine_Api::_()->getItem('album_photo', $value->resource_id); ?>
      <li id="thumbs-photo-<?php echo $photo->photo_id ?>">
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl()?>","<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo); ?>");return false;' <?php endif; ?>>
          <?php echo $this->itemBackgroundPhoto($photo, 'thumb.normal', $photo->getTitle(), array());
            ?>
        </a>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->youAndOwner->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->youAndOwner); ?>
  <?php endif; ?>
</div>
