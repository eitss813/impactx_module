<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $allParams=$this->allParams; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/jquery.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/owl.carousel.js') ?>
<div class="slide sesbasic_clearfix sesbasic_bxs sesblog_category_carousel_wrapper <?php echo $this->isfullwidth ? 'isfull_width' : '' ; ?>" style="height:<?php echo $this->height ?>px;">
  <div class="sesblogslide_<?php echo $this->identity; ?> owl-carousel owl-theme sesblog_carousel" style="height:<?php echo $this->height ?>px;">
    <?php foreach( $this->paginator as $item): ?>
    <div class="sesblog_category_carousel_item sesbasic_clearfix">
      <div class="sesblog_category_carousel_item_thumb">       
        <?php
        $href = $item->getHref();
        $imageURL = $item->getPhotoUrl();
        ?>
        <a href="<?php echo $href; ?>" class="sesblog_list_thumb_img" style="height:<?php echo $this->width ?>px;width:<?php echo $this->width ?>px;">
          <span style="background-image:url(<?php echo $imageURL; ?>);"></span>
        </a>
        </div>
        <div class="sesblog_category_carousel_item_info sesbasic_clearfix">
            <span class="sesblog_category_carousel_item_info_title sesbasic_text_light">
              <?php if(strlen($item->getTitle()) > $this->title_truncation_grid){ 
                $title = mb_substr($item->getTitle(),0,$this->title_truncation_grid).'...';
                echo $this->htmlLink($item->getHref(),$title) ?>
              <?php }else{ ?>
              	<?php echo $this->htmlLink($item->getHref(),$item->getTitle() ) ?>
              <?php } ?>
            </span>
        </div>
    	</div>
    <?php endforeach; ?>
  </div>
</div>
<script type="text/javascript">
<?php if($allParams['autoplay']){ ?>
			var autoplay_<?php echo $this->identity; ?> = true;
		<?php }else{ ?>
			var autoplay_<?php echo $this->identity; ?> = false;
		<?php } ?>
  sesblogJqueryObject(".sesblog_carousel").owlCarousel({
  nav:true,
  dots:false,
  items:1,
	margin:10,
	rewind: true,
  responsiveClass:true,
	autoplaySpeed: <?php echo $allParams['speed']; ?>,
	autoplay: autoplay_<?php echo $this->identity; ?>,
  responsive:{
    0:{
        items:1,
    },
    600:{
        items:6,
    },
  }
});
sesblogJqueryObject(".owl-prev").html('<i class="fa fa-angle-left"></i>');
sesblogJqueryObject(".owl-next").html('<i class="fa fa-angle-right"></i>');
</script>
