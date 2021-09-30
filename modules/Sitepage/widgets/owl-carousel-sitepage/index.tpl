<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$content_id = $this->identity;
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
->prependStylesheet($baseUrl . 'application/modules/Sitepage/externals/styles/owl-carousel.css')
->prependStylesheet($baseUrl . 'application/modules/Sitepage/externals/styles/owl.carousel.min.css')
->prependStylesheet($baseUrl . 'application/modules/Sitepage/externals/styles/owl.theme.default.min.css');
$this->headScript()
->appendFile($baseUrl . 'application/modules/Sitepage/externals/scripts/jquery.min.js')
->appendFile($baseUrl . 'application/modules/Sitepage/externals/scripts/owl.carousel.js');
?>
<?php $this->isCarousel = true; ?>
<?php $this->carouselClass = 'categorizedPageCarousel'.$content_id; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_pageView.tpl'; ?>
<script>
  var j_q = jq.noConflict();

  j_q(document).ready(function () {
   var owl = j_q('.categorizedPageCarousel<?php echo $content_id; ?>').owlCarousel({
    responsiveClass: true,
    autoplay: true,
    autoplayTimeout: <?php echo $this->interval;?>,
    responsive: {
      0: {
        items: 1,
        nav: true
      },
      479: {
        items: <?php echo $this->tabItem; ?>,
        nav: false
      },
      768: {
        items: <?php echo $this->deskItem; ?>,
        loop: false,
        margin: 20,
      }
    },
    dots: false,
    nav: true,
  });
   owl.on('changed.owl.carousel.', function(event) {
    <?php
    if (Engine_API::_()->seaocore()->isMobile()) {
      $number = 1;
    } else if (Engine_API::_()->seaocore()->isTabletDevice()) {
      $number =  $this->tabItem;
    } else {
      $number = $this->deskItem;
    }
    ?>
    if((event.item.index + (<?php echo $number; ?>)) === (event.item.count))
    {
      if((<?php echo $this->totalCount;?>) != (event.item.index)) {
        en4.core.request.send(new Request.HTML({
          'url' : en4.core.baseUrl + 'widget/index/mod/sitepage/name/owl-carousel-sitepage',
          'data' : {
            'limit': '1',
            'start' : event.item.count,
            'fea_spo':'<?php echo $this->fea_spo ?>',
            'popularity':'<?php echo $this->popularity ?>',
            'category_id':'<?php echo $this->category_id ?>',
            'title_truncation':'<?php echo $this->title_truncation ?>',
            'featuredIcon':'<?php echo $this->featuredIcon ?>',
            'sponsoredIcon':'<?php echo $this->sponsoredIcon ?>',
            'blockHeight': '<?php echo $this->blockHeight ?>',
            'blockWidth': '<?php echo $this->blockWidth ?>',
            'statistics': '<?php echo json_encode($this->statistics) ?>',
            'is_ajax':1
          },
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var data =  new DOMParser().parseFromString(responseHTML, "text/html");
            var value = data.getElementsByClassName('sitepage_owl');
            owl.trigger('add.owl.carousel', [value]);
            owl.trigger('play.owl.autoplay',[<?php echo $this->interval;?>]);
            owl.trigger('refresh.owl.carousel');
          }
        }));
      } else {
        owl.trigger('to.owl.carousel', 0);
      }
    }
  })
 }
 )
</script>