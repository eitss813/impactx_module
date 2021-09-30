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
if ($this->showPhotosInJustifiedView == 1 && $this->paginator->getCurrentPageNumber() < 2):
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/justifiedGallery.css'); 
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/jquery.justifiedGallery.js');
?>
<script type="text/javascript">
  
   jQuery.noConflict();
</script>
<?php  endif; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>
<?php $className = 'sitealbum_list_popular_photos' . $this->identity; ?>
<style type="text/css">
    .<?php echo $className ?> {
        width: <?php echo $this->photoWidth; ?>px !important; 
        height:  <?php echo $this->photoHeight; ?>px!important; 
        background-size: cover !important;
    }
</style>
<?php
if ($this->showLightBox):
    include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<?php if ($this->is_ajax_load): ?>
    <?php
    $i = 0;
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
    <?php if ($this->showPhotosInJustifiedView == 1) :$photo_type = 'thumb.main'; ?>
        <div class="sitealbum_thumbs thumbs_nocaptions" id="photos_layout<?php echo $this->identity; ?>">
            <?php foreach ($this->paginator as $item): ?>
                <div class="prelative">
                    <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $item->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item, array_merge($this->params, array('offset' => $i))); ?>");
                                return false;' <?php endif; ?>>
                        <?php echo $this->itemPhoto($item,($item->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle()); ?>
                    </a>
                    <?php if (!empty($this->photoInfo)): ?>
                      <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
                          <span class="show_photo_des"> 
                              <?php
                              $owner = $item->getOwner();
                              $parent = $item->getParent();
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
                                          <span class="photo_like">  <?php echo $item->like_count; ?></span> 
                                          <span class="photo_comment">  <?php echo $item->comment_count; ?></span>
                                      </span>
                                  </div>
                              <?php endif; ?>
                          </span>
                      <?php endif; ?>
                    <?php endif; ?>   
                </div>
            <?php $i++; ?>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <ul class="sitealbum_thumbs thumbs_nocaptions">
            <?php foreach ($this->paginator as $item): ?>
                <li>
                    <div class="prelative">
                        <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick="openLightBoxAlbum('<?php echo $item->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item, array_merge($this->params, array('offset' => $i))) ?>');
                                    return false;" <?php endif; ?> >
                        <?php 
                        echo $this->itemBackgroundPhoto($item,($item->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle(), array('class' => "$className"));
                        ?>
                        </a>

                        <?php if (!empty($this->photoInfo)): ?>
                            <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
                                <span class="show_photo_des"> 
                                    <?php
                                    $owner = $item->getOwner();
                                    $parent = $item->getParent();
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
                                                <span class="photo_like">  <?php echo $item->like_count; ?></span> 
                                                <span class="photo_comment">  <?php echo $item->comment_count; ?></span>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="sitealbum_thumb_info">     
                            <?php if (in_array('photoTitle', $this->photoInfo)): ?>
                                <span class="title">
                                    <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->photoTitleTruncation)) ?>
                                </span>
                            <?php endif; ?>
                            <?php echo $this->albumInfo($item, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                        </div>
                    <?php endif; ?> 
                </li>
                <?php $i++; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php else: ?>

    <div id="layout_sitealbum_list_popular_photos_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>'});
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_sitealbum_list_popular_photos_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script> 
<?php endif; ?>
<?php if($this->showPhotosInJustifiedView==1 && $this->paginator->getCurrentPageNumber() < 2): ?>
<script type="text/javascript">
    showJustifiedView('photos_layout<?php echo $this->identity; ?>',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>,'<?php echo $this->lastRow ?>' );
</script>
<?php endif; ?>