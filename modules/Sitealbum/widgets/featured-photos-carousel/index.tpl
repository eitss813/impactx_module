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
$baseUrl = $this->layout()->staticBaseUrl;

$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

$this->headScript()->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/slideitmoo-1.1_full_source.js');
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

<?php if ($this->is_ajax_load): ?>
  <a id="" class="pabsolute"></a>
  <?php $navsPRE = 'sitealbum_SlideItMoo_' . $this->identity; ?>
  <?php if (!empty($this->showPagination)) : ?>
    <script language="javascript" type="text/javascript">
      var slideshow;
              en4.core.runonce.add(function() {
      slideshow = new SlideItMoo({
      overallContainer: '<?php echo $navsPRE ?>_outer',
              elementScrolled: '<?php echo $navsPRE ?>_inner',
              thumbsContainer: '<?php echo $navsPRE ?>_items',
              thumbsContainerOuter: '<?php echo $navsPRE ?>_outer',
              itemsVisible:'<?php echo $this->limit; ?>',
              elemsSlide:'<?php echo $this->limit; ?>',
              duration:<?php echo $this->interval; ?>,
              itemsSelector: '<?php echo $this->vertical ? '.sitealbum_carousel_content_item' : '.sitealbum_carousel_content_item'; ?>',
              itemsSelectorLoading:'<?php echo $this->vertical ? 'sitmember_carousel_loader' : 'sitealbum_carousel_loader'; ?>',
              itemWidth:<?php echo $this->vertical ? ($this->photoWidth) : ($this->photoWidth + 10); ?>,
              itemHeight:<?php echo ($this->blockHeight + 10) ?>,
              showControls:1,
              slideVertical: <?php echo $this->vertical ?>,
              startIndex:1,
              totalCount:'<?php echo $this->totalCount; ?>',
              contentstartIndex: - 1,
              url:en4.core.baseUrl + 'sitealbum/index/featured-photos-carousel',
              params:{
      vertical:<?php echo $this->vertical ?>,
              featured:'<?php echo $this->featured ?>',
              orderby:'<?php echo $this->orderby ?>',
              category_id:'<?php echo $this->category_id; ?>',
              subcategory_id: '<?php echo $this->subcategory_id; ?>',
              photoTitleTruncation: '<?php echo $this->photoTitleTruncation; ?>',
              truncationLocation: '<?php echo $this->truncationLocation; ?>',
              detactLocation:'<?php echo $this->detactLocation; ?>',
              defaultLocationDistance: '<?php echo $this->defaultLocationDistance; ?>',
              latitude: '<?php echo $this->latitude; ?>',
              longitude: '<?php echo $this->longitude; ?>',
              photoInfo:<?php
    if ($this->photoInfo): echo json_encode($this->photoInfo);
    else:
      ?>  {'no':1} <?php endif; ?>,
              blockHeight: '<?php echo $this->blockHeight ?>',
              photoWidth: '<?php echo $this->photoWidth ?>',
              photoHeight: '<?php echo $this->photoHeight ?>',
              showPagination: '<?php echo $this->showPagination ?>',
              normalPhotoWidth : '<?php echo $this->normalPhotoWidth ?>',
              photo_type : '<?php echo $photo_type ?>',
              sitealbum_last_photoid : '<?php echo $this->sitealbum_last_photoid ?>',
              params:<?php
    if ($this->params): echo json_encode($this->params);
    else:
      ?>  {'no':1} <?php endif; ?>,
              showLightBox: '<?php echo $this->showLightBox ?>'
      },
              navs:{
      fwd:'<?php echo $navsPRE . ($this->vertical ? "_forward" : "_right") ?>',
              bk:'<?php echo $navsPRE . ($this->vertical ? "_back" : "_left") ?>'
      },
              transition: Fx.Transitions.linear, /* transition */
              onChange: function() {
      }
      });
      });</script>
  <?php endif; ?>

  <?php
  if ($this->showLightBox):
    include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
  endif;
  ?>

  <?php if ($this->vertical): ?>
    <ul class="seaocore_sponsored_widget">
      <li>
        <div id="<?php echo $navsPRE ?>_outer" class="sitealbum_carousel_vertical sitealbum_carousel">
          <div id="<?php echo $navsPRE ?>_inner" class="sitealbum_carousel_content b_medium" style="width:<?php echo $this->photoWidth + 2; ?>px;">
            <ul id="<?php echo $navsPRE ?>_items" class="sitealbum_thumbs sitealbum_carousel_items_wrapper">
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
                    'photo_type' => $photo_type,
                    'sitealbum_last_photoid' => $this->sitealbum_last_photoid,
                    'params' => $this->params
                ));
                ?>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php if (!empty($this->showPagination)) : ?>
            <div class="sitealbum_carousel_controller">
              <div class="sitealbum_carousel_button sitealbum_carousel_up" id="<?php echo $navsPRE ?>_back" style="display:none;">
                <i></i>
              </div>
              <div class="sitealbum_carousel_button sitealbum_carousel_up_dis" id="<?php echo $navsPRE ?>_back_dis" style="display:block;">
                <i></i>
              </div>

              <div class="sitealbum_carousel_button sitealbum_carousel_down fright" id ="<?php echo $navsPRE ?>_forward">
                <i></i>
              </div>
              <div class="sitealbum_carousel_button sitealbum_carousel_down_dis fright" id="<?php echo $navsPRE ?>_forward_dis" style="display:none;">
                <i></i>
              </div>
            </div>
          <?php endif; ?>
          <div class="clr"></div>
        </div>
        <div class="clr"></div>
      </li>
    </ul>
  <?php else: ?>
    <div id="<?php echo $navsPRE ?>_outer" class="sitealbum_carousel sitealbum_carousel_horizontal" style="width: <?php echo (($this->limit <= $this->totalCount ? $this->limit : $this->totalCount) * ($this->photoWidth + 24)) + 60 ?>px; height: <?php echo ($this->blockHeight + 10) ?>px;">
      <?php if (!empty($this->showPagination)) : ?>
        <div class="sitealbum_carousel_button sitealbum_carousel_left" id="<?php echo $navsPRE ?>_left" style="display:none;">
          <i></i>
        </div>
        <div class="sitealbum_carousel_button sitealbum_carousel_left_dis" id="<?php echo $navsPRE ?>_left_dis" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
          <i></i>
        </div>
      <?php endif; ?>
      <div id="<?php echo $navsPRE ?>_inner" class="sitealbum_carousel_content" style="height: <?php echo ($this->blockHeight + 5) ?>px;">
        <ul id="<?php echo $navsPRE ?>_items" class="sitealbum_thumbs sitealbum_carousel_items_wrapper">
          <?php $i = 0; ?>
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
                'popularType' => $this->popularType,
                'showLightBox' => $this->showLightBox,
                'normalPhotoWidth' => $this->normalPhotoWidth,
                'photo_type' => $photo_type,
                'sitealbum_last_photoid' => $this->sitealbum_last_photoid,
                'params' => $this->params));
            ?>
            <?php $i++; ?>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php if (!empty($this->showPagination)) : ?>
        <div class="sitealbum_carousel_button sitealbum_carousel_right" id ="<?php echo $navsPRE ?>_right" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
          <i></i>
        </div>
        <div class="sitealbum_carousel_button sitealbum_carousel_right_dis" id="<?php echo $navsPRE ?>_right_dis" style="display:none;">
          <i></i>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
<?php else: ?>

  <div id="layout_sitealbum_featured_photos_carousel<?php echo $this->identity; ?>">
    <!--    <div class="seaocore_content_loader"></div>-->
  </div>

  <script type="text/javascript">
            var requestParams = $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>'})
            var params = {
    'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer' : 'layout_sitealbum_featured_photos_carousel<?php echo $this->identity; ?>',
            requestParams: requestParams
    };
            en4.seaocore.locationBased.startReq(params);
  </script>

<?php endif; ?>