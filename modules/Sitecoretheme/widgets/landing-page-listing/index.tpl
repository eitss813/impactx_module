<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if( $this->crousalView ): ?>
  <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/owl.carousel/owl.carousel.min.css'); ?>
  <?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');

  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/owl.carousel.min.js');
  ?>
<?php endif; ?>
<div class="sitecoretheme_content_listing sitecoretheme_rounded_listing <?php if ($this->identity): ?>sitecoretheme_rounded_listing_wapper<?php endif;?> <?php if ($this->viewType): ?>sitecoretheme_listing_<?php echo $this->viewType ?>_wapper<?php endif;?>">
  <div class="owl-nav _navigation"></div>
  <div class="_items_wapper <?php if( $this->crousalView ): ?>owl-carousel<?php endif; ?> owl-carousel_list_<?php echo $this->content_id; ?>">
    <?php foreach( $this->results as $item ): ?>
      <div class="_item_list">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile', $item->getTitle()), array('class' => 'sitecoretheme_rounded_listing_thumb', 'title' => $item->getTitle())) ?>
        <?php if ($this->showInfo): ?>
        <div class="_info">
          <span class="_title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title' => $item->getTitle())); ?></span>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php if( $this->crousalView ): ?>
<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery('.owl-carousel_list_<?php echo $this->content_id; ?>').owlCarousel({
      items: 3,
      nav: true,
      center: false,
      margin: 20,
      autoWidth: false,
      responsiveClass: true,
      lazyLoad: true,
      dots: false,
      navContainer: jQuery('.owl-carousel_list_<?php echo $this->content_id; ?>').parent().find('._navigation'),
      navText: [
        '<i class="fa fa-arrow-circle-left"></i>',
        '<i class="fa fa-arrow-circle-right"></i>'
      ],
      navContainerClass: 'owl-nav _navigation',
      navClass: [
        'owl-prev _prev',
        'owl-next _next'
      ],
      responsive: {
        <?php if ($this->identity): ?>
        0: {
          items: 3,
          slideBy:3
        },
        767: {
          items: 6,
          slideBy:6
        }
        <?php else: ?>
        0: {
          items: 2,
          slideBy:2
        },
        1100: {
          items: 3,
          slideBy:3
        }
        <?php endif; ?>
      }
    });
  });
</script>
<?php endif; ?>