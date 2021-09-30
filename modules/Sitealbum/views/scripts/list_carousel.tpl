<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$photo = $this->photo;
?>
<li class="sitealbum_grid_view sitealbum_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->photoWidth) ?>px;">
  <div class="sitealbum_grid_thumb">

    <div class="prelative">
      <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick="openLightBoxAlbum('<?php echo $photo->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, $this->params) ?>');
            return false;" <?php endif; ?> >
       <?php
        $attributeArray=array( 'height:'.$this->photoHeight.'px !important;', 'width:'.$this->photoWidth.'px !important;','background-size:cover !important;');
        echo $this->itemBackgroundPhoto($photo, ($photo->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $this->photo_type, $photo->getTitle(), array('style' => $attributeArray));
      
      ?>
      
      
      </a>

      <?php if (!empty($this->photoInfo)): ?>
        <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
          <span class="show_photo_des"> 
            <?php
            $owner = $photo->getOwner();
            $parent = $photo->getParent();
            if (in_array('albumTitle', $this->photoInfo)):
              ?>
              <div class="photo_title">
                <?php echo $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(), 25)) ?>
              </div>
            <?php endif; ?>
            <?php if (in_array('ownerName', $this->photoInfo)): ?>
              <div>
                <span class="photo_owner fleft"><?php echo $this->translate('by %1$s', $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(), 25))); ?></span>
                <span class="fright sitealbum_photo_count">
                  <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                  <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                </span>
              </div>
            <?php endif; ?>
          </span>
        <?php endif; ?>
      </div>

      <div class="sitealbum_grid_info">
        <?php if (in_array('photoTitle', $this->photoInfo)): ?>
          <span class="thumbs_title bold">
            <?php echo $this->htmlLink($photo, Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation)) ?>
          </span>
        <?php endif; ?>
        <?php echo $this->albumInfo($photo, $this->photoInfo, array('truncationLocation' => $this->truncationLocation,)); ?>
      </div>
    <?php endif; ?>
</li>