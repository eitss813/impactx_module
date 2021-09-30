<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->direction == 1) { ?>
  <?php $j = 0; ?>
  <?php foreach ($this->photos as $photo): ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitealbum', array(
        'photo' => $photo,
        'photoInfo' => $this->photoInfo,
        'blockHeight' => $this->blockHeight,
        'photoWidth' => $this->photoWidth,
        'photoHeight' => $this->photoHeight,
        'photoTitleTruncation' => $this->photoTitleTruncation,
        'truncationLocation' => $this->truncationLocation,
        'showLightBox' => $this->showLightBox,
        'normalPhotoWidth' => $this->normalPhotoWidth,
        'photo_type' => $this->photo_type,
        'sitealbum_last_photoid' => $this->sitealbum_last_photoid,
        'params' => $this->params
    ));
    ?>
  <?php endforeach; ?>
  <?php if ($j < ($this->sponserdSitealbumsCount)): ?>
    <?php for ($j; $j < ($this->sponserdSitealbumsCount); $j++): ?>
      <li class="sitemember_grid_view sitemember_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
      </li>
    <?php endfor; ?>
  <?php endif; ?>
<?php } else { ?>

  <?php for ($i = $this->sponserdSitealbumsCount; $i < Count($this->photos); $i++): ?>
    <?php $photo = $this->photos[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitealbum', array(
        'photo' => $photo,
        'photoInfo' => $this->photoInfo,
        'blockHeight' => $this->blockHeight,
        'photoWidth' => $this->photoWidth,
        'photoHeight' => $this->photoHeight,
        'photoTitleTruncation' => $this->photoTitleTruncation,
        'truncationLocation' => $this->truncationLocation,
        'showLightBox' => $this->showLightBox,
        'normalPhotoWidth' => $this->normalPhotoWidth,
        'photo_type' => $this->photo_type,
        'sitealbum_last_photoid' => $this->sitealbum_last_photoid,
        'params' => $this->params
    ));
    ?>
  <?php endfor; ?>

  <?php for ($i = 0; $i < $this->sponserdSitealbumsCount; $i++): ?>
    <?php $photo = $this->photos[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitealbum', array(
        'photo' => $photo,
        'photoInfo' => $this->photoInfo,
        'blockHeight' => $this->blockHeight,
        'photoWidth' => $this->photoWidth,
        'photoHeight' => $this->photoHeight,
        'photoTitleTruncation' => $this->photoTitleTruncation,
        'truncationLocation' => $this->truncationLocation,
        'showLightBox' => $this->showLightBox,
        'normalPhotoWidth' => $this->normalPhotoWidth,
        'photo_type' => $this->photo_type,
        'sitealbum_last_photoid' => $this->sitealbum_last_photoid,
        'params' => $this->params
    ));
    ?>
  <?php endfor; ?>
<?php } ?>

