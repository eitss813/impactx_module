<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js'); ?>
<script type="text/javascript">
var circularImage = '<?php echo $this->circularImage;?>';    
</script>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl';
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/slideitmoo-1.1_full_source.js');
?>
<?php
if ($this->is_ajax_load):
  ?>
 <?php  if(!empty($this->complimentItem)) : ?>
 <span id='compliment_widget_<?php echo $this->identity ?>' class="compliment_widget_title_icon" style="display: none">
        <?php  echo $this->itemPhoto($this->complimentItem, 'thumb.icon', '', array()); ?>
        <?php  echo $this->title;  ?>
 </span>
 <?php endif; ?>
  <!--<a id="" class="pabsolute"></a> -->
  <?php $navsPRE = 'sitemember_SlideItMoo_' . $this->identity; ?>
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
              itemsSelector: '<?php echo $this->vertical ? '.sitemember_carousel_content_item' : '.sitemember_carousel_content_item'; ?>',
              itemsSelectorLoading:'<?php echo $this->vertical ? 'sitmember_carousel_loader' : 'sitemember_carousel_loader'; ?>',
              itemWidth:<?php echo $this->vertical ? ($this->blockWidth) : ($this->blockWidth + ($this->circularImage ? 24 : 10)); ?>,
              itemHeight:<?php echo ($this->blockHeight + 10) ?>,
              showControls:1,
              slideVertical: <?php echo $this->vertical ?>,
              startIndex:1,
              totalCount:'<?php echo $this->totalCount; ?>',
              contentstartIndex: - 1,
              url:en4.core.baseUrl + 'sitemember/compliment/compliments',
              params:{
                vertical:<?php echo $this->vertical ?>,
                itemViewType:<?php echo $this->itemViewType ?>,
                compliment_category:<?php echo $this->compliment_category ?>,
              links: <?php
    if ($this->links): echo json_encode($this->links);
    else:
      ?>  {'no':1} <?php endif; ?>,
              title_truncation:'<?php echo $this->title_truncation ?>',
              customParams:'<?php echo $this->customParams ?>',
              showOptions:<?php
    if ($this->showOptions): echo json_encode($this->showOptions);
    else:
      ?>  {'no':1} <?php endif; ?>,
              blockHeight: '<?php echo $this->blockHeight ?>',
              blockWidth: '<?php echo $this->blockWidth ?>',
              showPagination: '<?php echo $this->showPagination ?>',
              titlePosition: '<?php echo $this->titlePosition ?>',
              has_photo: '<?php echo $this->has_photo ?>',
              itemViewType:<?php echo $this->itemViewType ?>,
              compliment_category:<?php echo $this->compliment_category ?>,
              circularImage: '<?php echo $this->circularImage ?>',
              circularImageHeight: '<?php echo $this->circularImageHeight?>',
              
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

  <?php if ($this->vertical): ?>
    <ul class="seaocore_sponsored_widget">
      <li>
        <div id="<?php echo $navsPRE ?>_outer" class="sitemember_carousel_vertical sitemember_carousel <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
          <div id="<?php echo $navsPRE ?>_inner" class="sitemember_carousel_content b_medium" style="width:<?php echo $this->blockWidth + 2; ?>px;">
            <ul id="<?php echo $navsPRE ?>_items" class="sitemember_carousel_items_wrapper">
              <?php foreach ($this->members as $sitemember): ?>
                <?php
                echo $this->partial(
                    'list_carousel_compliment.tpl', 'sitemember', array(
                    'sitemember' => $sitemember,
                    'title_truncation' => $this->title_truncation,
                    'vertical' => $this->vertical,
                    'showOptions' => $this->showOptions,
                    'blockHeight' => $this->blockHeight,
                    'blockWidth' => $this->blockWidth,
                    'titlePosition' => $this->titlePosition,
                    'customParams' => $this->customParams,
                    'links' => $this->links,
                    'circularImage' => $this->circularImage,
                    'circularImageHeight' => $this->circularImageHeight,
                    'compliment'=>$this->complimentTable,
                    'complimentItem' =>$this->complimentItem,
                    'itemViewType' =>$this->itemViewType
                ));
                ?>
    <?php endforeach; ?>
            </ul>
          </div>
    <?php if (!empty($this->showPagination)) : ?>
            <div class="sitemember_carousel_controller">
              <div class="sitemember_carousel_button sitemember_carousel_up" id="<?php echo $navsPRE ?>_back" style="display:none;">
                <i></i>
              </div>
              <div class="sitemember_carousel_button sitemember_carousel_up_dis" id="<?php echo $navsPRE ?>_back_dis" style="display:block;">
                <i></i>
              </div>

              <div class="sitemember_carousel_button sitemember_carousel_down" id ="<?php echo $navsPRE ?>_forward">
                <i></i>
              </div>
              <div class="sitemember_carousel_button sitemember_carousel_down_dis" id="<?php echo $navsPRE ?>_forward_dis" style="display:none;">
                <i></i>
              </div>
            </div>
    <?php endif; ?>
          <div class="clr"></div>
        </div>
        <div class="clr"></div>
      </li>
    </ul>
    <?php else:?>
    <div id="<?php echo $navsPRE ?>_outer" class="sitemember_carousel sitemember_carousel_horizontal <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" style="width: <?php echo (($this->limit <= $this->totalCount ? $this->limit : $this->totalCount) * ($this->blockWidth + 24)) + 60 ?>px; height: <?php echo ($this->blockHeight + 10) ?>px;">
    <?php if (!empty($this->showPagination)) : ?>
        <div class="sitemember_carousel_button sitemember_carousel_left" id="<?php echo $navsPRE ?>_left" style="display:none;">
          <i></i>
        </div>
        <div class="sitemember_carousel_button sitemember_carousel_left_dis" id="<?php echo $navsPRE ?>_left_dis" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
          <i></i>
        </div>
    <?php endif; ?>
      <div id="<?php echo $navsPRE ?>_inner" class="sitemember_carousel_content" style="height: <?php echo ($this->blockHeight + 5) ?>px;">
        <ul id="<?php echo $navsPRE ?>_items" class="sitemember_carousel_items_wrapper">
          <?php $i = 0; ?>
          <?php foreach ($this->members as $sitemember): ?>
            <?php
            echo $this->partial(
                'list_carousel_compliment.tpl', 'sitemember', array(
                'sitemember' => $sitemember,
                'title_truncation' => $this->title_truncation,
                'vertical' => $this->vertical,
                'showOptions' => $this->showOptions,
                'blockHeight' => $this->blockHeight,
                'blockWidth' => $this->blockWidth,
                'titlePosition' => $this->titlePosition,
                'customParams' => $this->customParams,
                'links' => $this->links,
                'circularImage' => $this->circularImage,
                'circularImageHeight' => $this->circularImageHeight,
                'compliment'=>$this->complimentTable,
                'complimentItem' =>$this->complimentItem,
                'itemViewType' =>$this->itemViewType
                
            ));
            ?>
            <?php $i++; ?>
    <?php endforeach; ?>
        </ul>
      </div>
    <?php if (!empty($this->showPagination)) : ?>
        <div class="sitemember_carousel_button sitemember_carousel_right" id ="<?php echo $navsPRE ?>_right" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
          <i></i>
        </div>
        <div class="sitemember_carousel_button sitemember_carousel_right_dis" id="<?php echo $navsPRE ?>_right_dis" style="display:none;">
          <i></i>
        </div>
    <?php endif; ?>
    </div>
  <?php endif; ?>

<?php else: ?>

  <div id="layout_sitemember_ajax_carousel_sitemember<?php echo $this->identity; ?>">
  </div>

  <script type="text/javascript">
            var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
            var params = {
    'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer' : 'layout_sitemember_ajax_carousel_sitemember<?php echo $this->identity; ?>',
            requestParams: requestParams
    };
            en4.seaocore.locationBased.startReq(params);
  </script>

<?php endif; ?>

<script>
            en4.core.runonce.add(function(){ 
            var elements = document.getElementsByClassName("layout_sitemember_ajax_carousel_compliments_sitemember");
            var set = true;
                for(i=0; i<elements.length; i++){
                    if(!elements[i].getElement('h3').getElement('img') && set) {
                        set = false;
                        elements[i].getElement('h3').innerHTML=$('compliment_widget_<?php echo $this->identity ?>').innerHTML;
                    }
                 }
            });
</script>