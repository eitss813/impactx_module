<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>
<?php $className = 'sitealbum_photo_of_the_day' . $this->identity; ?>
<style type="text/css">
  .<?php echo $className ?> {
    width: <?php echo $this->photoWidth; ?>px !important; 
    height:  <?php echo $this->photoHeight; ?>px!important; 
    background-size: cover !important;
  }
</style>
<?php
$photoSettings = array();
$photoSettings['class'] = 'thumb';
$photoSettings['title'] = $this->photoOfDay->getTitle();
if ($this->showLightBox):
  $photoSettings["onclick"] = "openLightBoxAlbum('" . $this->photoOfDay->getPhotoUrl() . "','" . Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photoOfDay) . "');return false;";
  include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<?php
  if ($this->photoWidth > $this->normalLargePhotoWidth):
    $photo_type = 'thumb.main';
  elseif ($this->photoWidth > $this->normalPhotoWidth):
    $photo_type = 'thumb.medium';
  else:
    $photo_type = 'thumb.normal';
  endif;
?>
<ul class="sitealbum_thumbs thumbs_nocaptions">
  <li>
    <div class="prelative">
      <a class="thumbs_photo" href="<?php echo $this->photoOfDay->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick="openLightBoxAlbum('<?php echo $this->photoOfDay->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photoOfDay) ?>');
            return false;" <?php endif; ?> >
      <?php 
        echo $this->itemBackgroundPhoto($this->photoOfDay, ($this->photoOfDay->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $this->photoOfDay->getTitle(), array('class' => "$className"));
      ?>
      </a> 

      <?php if (!empty($this->photoInfo)): ?>
        <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
          <span class="show_photo_des"> 
            <?php
            $owner = $this->photoOfDay->getOwner();
            $parent = $this->photoOfDay->getParent();
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
                  <span class="photo_like">  <?php echo $this->photoOfDay->like_count; ?></span> 
                  <span class="photo_comment">  <?php echo $this->photoOfDay->comment_count; ?></span>
                </span>
              </div>
            <?php endif; ?>
          </span>   
        <?php endif; ?>
      </div>

      <?php if (in_array('photoTitle', $this->photoInfo)): ?>
        <span class="thumbs_title bold">
          <?php echo $this->htmlLink($this->photoOfDay, Engine_Api::_()->seaocore()->seaocoreTruncateText($this->photoOfDay->getTitle(), $this->photoTitleTruncation)); ?>
        </span>
      <?php endif; ?>
      <?php echo $this->albumInfo($this->photoOfDay, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>
    <?php endif; ?>
  </li>  
</ul>