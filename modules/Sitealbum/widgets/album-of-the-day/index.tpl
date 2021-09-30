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
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css')
?>
<?php $className = 'sitealbum_album_of_the_day' . $this->identity; ?>
<style type="text/css">
  .<?php echo $className ?> {
    width: <?php echo $this->photoWidth; ?>px !important; 
    height:  <?php echo $this->photoHeight; ?>px!important; 
    background-size: cover !important;
  }
</style>

<?php
  if ($this->photoWidth > $this->normalLargePhotoWidth):
    $photo_type = 'thumb.main';
  elseif ($this->photoWidth > $this->normalPhotoWidth):
    $photo_type = 'thumb.medium';
  else:
    $photo_type = 'thumb.normal';
  endif;
?>

	<ul class="thumbs thumbs_nocaptions <?php if($this->infoOnHover):?> sitealbum_view_onhover <?php endif;?>">
  <li>
    <?php if ($this->albumOfDay->photo_id): ?>
      <a class="thumbs_photo" href="<?php echo $this->albumOfDay->getHref(); ?>">
        <?php 
          echo  $this->itemBackgroundPhoto($this->albumOfDay, ($this->albumOfDay->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $this->albumOfDay->getTitle(), array('class' => $className));
        ?>  
      
      </a>
    <?php else: ?>
      <a class="thumbs_photo" href="<?php echo $this->albumOfDay->getHref(); ?>" >   
        
        <?php 
          echo  $this->itemBackgroundPhoto($this->albumOfDay,'thumb.normal', $this->albumOfDay->getTitle(), array('class' => $className,'id' => "sitealbum_".$this->albumOfDay->album_id));
        ?>       
        </a>
        <?php endif; ?>
		
    <?php if (!empty($this->albumInfo)): ?>
    <div class="sitealbum_thumb_info" <?php if($this->infoOnHover):?> onclick="openAlbumViewPage('<?php echo $this->albumOfDay->getHref(); ?>');" <?php endif;?>>
        <div class="thumbs_info mtop5">
          <?php if (in_array('albumTitle', $this->albumInfo)): ?>
            <span class="thumbs_title bold"> 
              <?php echo $this->htmlLink($this->albumOfDay->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->albumOfDay->getTitle(), $this->albumTitleTruncation), array('title' => $this->albumOfDay->getTitle())); ?>
            </span>
          <?php endif; ?>
  
          <?php if (in_array('ownerName', $this->albumInfo)): ?>
            <span class="dblock mtop5">
              <?php
              $owner = $this->albumOfDay->getOwner();
              echo $this->translate('by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
              ?> 
            </span>
          <?php endif; ?>
        </div> 
  
        <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
            <div class="seao_listings_stats">
              <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
              <div title="<?php echo $this->translate(array('%s photo', '%s photos', $this->albumOfDay->photos_count), $this->locale()->toNumber($this->albumOfDay->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $this->albumOfDay->photos_count), $this->locale()->toNumber($this->albumOfDay->photos_count)); ?></div>
            </div>
        <?php endif; ?>
  
        <?php echo $this->albumInfo($this->albumOfDay, $this->albumInfo, array('truncationLocation' => $this->truncationLocation)); ?>
    </div>
    <?php endif; ?>
  </li>
</ul>