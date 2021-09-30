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
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/wow.js')
?>

 <script>
	  new WOW().init();
</script>

<?php if ($this->is_ajax_load): ?>
  <?php $className = 'sitealbum_list_popular_photos' . $this->identity; ?>
  <style type="text/css">
    .<?php echo $className ?> {
      width: <?php echo $this->photoWidth; ?>px !important; 
      height:  <?php echo $this->photoHeight; ?>px!important; 
      background-size: cover !important;
    }
  </style>
  <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

  <?php
  if ($this->photoWidth > $this->normalLargePhotoWidth):
    $photo_type = 'thumb.main';
  elseif ($this->photoWidth > $this->normalPhotoWidth):
    $photo_type = 'thumb.medium';
  else:
    $photo_type = 'thumb.normal';
  endif;
  ?>
   
<?php if( !empty($this->description) ) : ?>
    <div class="widgets_title_border">
	   <span></span>
	   <i></i>
	   <span></span>
   </div>
  <div class="widgets_title_description">
    <?php echo $this->description; ?>
  </div>
<?php endif; ?>
  <ul class="thumbs thumbs_nocaptions <?php if($this->infoOnHover):?> sitealbum_view_onhover <?php endif;?>">
    <?php foreach ($this->paginator as $item): ?>
      <li class="wow animated zoomIn">
        <div class="photo">
          <?php if ($item->photo_id): ?>
            <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" >
              <?php 
                echo $this->itemBackgroundPhoto($item,($item->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle(), array('class' => "$className"));
                ?> 
            </a>
              <?php else: ?>
            <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" >   
              <?php echo $this->itemBackgroundPhoto($item,'thumb.normal', $item->getTitle(), array('class' => "$className")); ?>
            </a>
          <?php endif; ?>
        </div>

        <?php if (!empty($this->albumInfo)): ?>
          <div class="sitealbum_thumb_info" <?php if($this->infoOnHover):?> onclick="openAlbumViewPage('<?php echo $item->getHref(); ?>');" <?php endif;?> style="width: <?php echo $this->photoWidth; ?>px ; ">
            <div class="thumbs_info mtop5">
              <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                <span class="thumbs_title">
                  <?php echo $this->htmlLink($item->getHref(), $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->albumTitleTruncation))); ?>
                </span>
              <?php endif; ?>	

              <?php if (in_array('ownerName', $this->albumInfo)): ?>
                <span class="thumbs_author dblock mtop5">
                  <?php echo $this->translate('by %1$s', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())); ?>
                </span>
              <?php endif; ?>
            </div>

            <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
              <div class="seao_listings_stats">
                <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
                <div title="<?php echo $this->translate(array('%s photo', '%s photos', $item->photos_count), $this->locale()->toNumber($item->photos_count)) ?>" class="o_hidden">
                  <?php echo $this->translate(array('%s photo', '%s photos', $item->photos_count), $this->locale()->toNumber($item->photos_count)); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php echo $this->albumInfo($item, $this->albumInfo, array('truncationLocation' => $this->truncationLocation)); ?>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
  
<?php else: ?>

  <div id="layout_sitealbum_list_popular_albums_<?php echo $this->identity; ?>">
  </div>

  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitealbum_list_popular_albums_<?php echo $this->identity; ?>',
      requestParams: requestParams
    };

    en4.seaocore.locationBased.startReq(params);
  </script>  
<?php endif; ?>
