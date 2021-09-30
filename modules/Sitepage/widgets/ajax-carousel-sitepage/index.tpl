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
	$this->headLink()
					->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_carousel.css');  
	$this->headScript()
					->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/slideitmoo-1.1_full_source.js');  
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<a id="" class="pabsolute"></a>
<?php $navsPRE = 'Sr_SlideItMoo_' . $this->identity; ?>
<script language="javascript" type="text/javascript">
  var seaoslideshow;
  window.addEvents ({
    'domready': function() {
      seaoslideshow = new SeaocoreSlideItMoo({
        overallContainer: '<?php echo $navsPRE ?>_outer',
        elementScrolled: '<?php echo $navsPRE ?>_inner',
        thumbsContainer: '<?php echo $navsPRE ?>_items',
        thumbsContainerOuter: '<?php echo $navsPRE ?>_outer',
        itemsVisible:'<?php echo $this->limit; ?>',
        elemsSlide:'<?php echo $this->limit; ?>',
        duration:<?php echo $this->interval; ?>,
        itemsSelector: '<?php echo $this->vertical ? '.seaocore_carousel_content_item_wrapper' : '.seaocore_carousel_content_item_wrapper'; ?>' ,
        itemsSelectorLoading:'<?php echo $this->vertical ? 'seaocore_carousel_loader' : 'seaocore_carousel_loader'; ?>' ,
        itemWidth:<?php echo $this->vertical ? ($this->blockWidth) : ($this->blockWidth + 24); ?>,
        itemHeight:<?php echo ($this->blockHeight + 6) ?>,
        showControls:1,
        slideVertical: <?php echo $this->vertical ?>,
        startIndex:1,
        totalCount:'<?php echo $this->totalCount; ?>',
        contentstartIndex:-1,
        url:en4.core.baseUrl+'sitepage/index/ajax-carousel',
        
        params:{
          vertical:<?php echo $this->vertical ?>,
          fea_spo:'<?php echo $this->fea_spo ?>',
          popularity:'<?php echo $this->popularity ?>',
          category_id:'<?php echo $this->category_id ?>',
          title_truncation:'<?php echo $this->title_truncation ?>',
          featuredIcon:'<?php echo $this->featuredIcon ?>',
          sponsoredIcon:'<?php echo $this->sponsoredIcon ?>',
          showOptions:<?php if($this->showOptions): echo  json_encode($this->showOptions); else: ?>  {'no':1} <?php endif;?>,
          blockHeight: '<?php echo $this->blockHeight ?>',
          blockWidth: '<?php echo $this->blockWidth ?>',
          statistics: '<?php echo json_encode($this->statistics) ?>',
          newIcon:'<?php echo $this->newIcon ?>'
        },
        navs:{
          fwd:'<?php echo $navsPRE . ($this->vertical ? "_forward" : "_right") ?>',
          bk:'<?php echo $navsPRE . ($this->vertical ? "_back" : "_left") ?>'
        },
        transition: Fx.Transitions.linear, /* transition */
        onChange: function() { 
        }
      });
    }
  });
</script>

<?php if ($this->vertical): ?> 
  <ul class="seaocore_sponsored_widget">
    <li>
      <?php $sitepage_advsitepage = true; ?>
      <div id="<?php echo $navsPRE ?>_outer" class="seaocore_carousel_vertical seaocore_carousel">
        <div id="<?php echo $navsPRE ?>_inner" class="seaocore_carousel_content b_medium" style="width:<?php echo $this->blockWidth + 8; ?>px;">
          <ul id="<?php echo $navsPRE ?>_items" class="seaocore_carousel_grid_view">
            <?php foreach ($this->listings as $sitepage): ?>
              <?php
              echo $this->partial(
                      'list_carousel.tpl', 'sitepage', array(
                  'sitepage' => $sitepage,
                  'title_truncation' => $this->title_truncation,
                  'vertical' => $this->vertical,
                  'featuredIcon' => $this->featuredIcon,
                  'sponsoredIcon' => $this->sponsoredIcon,
                  'showOptions' => $this->showOptions,
                  'blockHeight' => $this->blockHeight,
                  'blockWidth' => $this->blockWidth,
                  'statistics' => $this->statistics,
                  'newIcon' => $this->newIcon
              ));
              ?>	     
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="seaocore_carousel_controller">
          <div class="seaocore_carousel_button seaocore_carousel_up" id="<?php echo $navsPRE ?>_back" style="display:none;">
            <i></i>
          </div>
          <div class="seaocore_carousel_button seaocore_carousel_up_dis" id="<?php echo $navsPRE ?>_back_dis" style="display:block;">
            <i></i>
          </div>

          <div class="seaocore_carousel_button seaocore_carousel_down fright" id ="<?php echo $navsPRE ?>_forward">
            <i></i>
          </div>
          <div class="seaocore_carousel_button seaocore_carousel_down_dis fright" id="<?php echo $navsPRE ?>_forward_dis" style="display:none;">
            <i></i>
          </div>
        </div>  
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    </li>
  </ul>
<?php else: ?>
  <div id="<?php echo $navsPRE ?>_outer" class="seaocore_carousel seaocore_carousel_horizontal" style="width: <?php echo (($this->limit <= $this->totalCount ? $this->limit : $this->totalCount) * ($this->blockWidth + 24)) + 60 ?>px; height: <?php echo ($this->blockHeight + 10) ?>px;">
    <div class="seaocore_carousel_button seaocore_carousel_left" id="<?php echo $navsPRE ?>_left" style="display:none;">
      <i></i>
    </div>
    <div class="seaocore_carousel_button seaocore_carousel_left_dis" id="<?php echo $navsPRE ?>_left_dis" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
      <i></i>
    </div>
    <div id="<?php echo $navsPRE ?>_inner" class="seaocore_carousel_content" style="height: <?php echo ($this->blockHeight + 5) ?>px;">
      <ul id="<?php echo $navsPRE ?>_items" class="seaocore_carousel_grid_view">
        <?php $i = 0; ?>
        <?php foreach ($this->listings as $sitepage): ?>
          <?php
          echo $this->partial(
                  'list_carousel.tpl', 'sitepage', array(
              'sitepage' => $sitepage,
              'title_truncation' => $this->title_truncation,
              'vertical' => $this->vertical,
              'featuredIcon' => $this->featuredIcon,
              'sponsoredIcon' => $this->sponsoredIcon,
              'showOptions' => $this->showOptions,
              'blockHeight' => $this->blockHeight,
              'blockWidth' => $this->blockWidth,
              'statistics' => $this->statistics,
              'newIcon' => $this->newIcon
          ));
          ?>	
          <?php $i++; ?>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="seaocore_carousel_button seaocore_carousel_right" id ="<?php echo $navsPRE ?>_right" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
      <i></i>
    </div>
    <div class="seaocore_carousel_button seaocore_carousel_right_dis" id="<?php echo $navsPRE ?>_right_dis" style="display:none;">
      <i></i>
    </div>
  </div>
<?php endif; ?>