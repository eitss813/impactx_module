<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/slideitmoo-1.1_full_source.js');
?>
<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<a id="" class="pabsolute"></a>
<?php $navsPRE = 'sitevideo_SlideItMoo_' . $this->identity; ?>
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
                        duration:'<?php echo $this->interval; ?>',
                        itemsSelector: '<?php echo $this->vertical ? '.sitevideo_carousel_content_item' : '.sitevideo_carousel_content_item'; ?>',
                        itemsSelectorLoading:'<?php echo $this->vertical ? 'sitevideo_carousel_loader' : 'sitevideo_carousel_loader'; ?>',
                        itemWidth:<?php echo $this->vertical ? ($this->blockWidth) : ($this->blockWidth + 10); ?>,
                        itemHeight:<?php echo ($this->blockHeight + 10) ?>,
                        showControls:1,
                        slideVertical: <?php echo $this->vertical ?>,
                        startIndex:1,
                        totalCount:'<?php echo $this->totalCount; ?>',
                        contentstartIndex: - 1,
                        url:en4.core.baseUrl + 'sitevideo/index/homesponsored',
                        params:{
                        vertical:<?php echo $this->vertical ?>,
                                videoOption:<?php
    if ($this->videoOption): echo json_encode($this->videoOption);
    else:
        ?>  {'no':1} <?php endif; ?>,
                                orderby:'<?php echo $this->orderby ?>',
                                category_id:'<?php echo $this->category_id ?>',
                                subcategory_id:'<?php echo $this->subcategory_id ?>',
                                subsubcategory_id:'<?php echo $this->subsubcategory_id ?>',
                                blockHeight: '<?php echo $this->blockHeight ?>',
                                blockWidth: '<?php echo $this->blockWidth ?>',
                        },
                        navs:{
                        fwd:'<?php echo $navsPRE . ($this->vertical ? "_forward" : "_right") ?>',
                                bk:'<?php echo $navsPRE . ($this->vertical ? "_back" : "_left") ?>'
                        },
                        transition: Fx.Transitions.linear, 
                        onChange: function() {
                        }
                });
                });</script>
<?php endif; ?>


<?php if ($this->vertical): ?> 
    <ul class="seaocore_sponsored_widget">
        <li>
            <?php $sitevideo_advsitevideo = true; ?>
            <div id="<?php echo $navsPRE ?>_outer" class="sitevideo_carousel_vertical sitevideo_carousel">
                <div id="<?php echo $navsPRE ?>_inner" class="sitevideo_carousel_content b_medium" style="width:<?php echo $this->blockWidth + 2; ?>px;">
                    <ul id="<?php echo $navsPRE ?>_items" class="sitevideo_carousel_items_wrapper">
                        <?php foreach ($this->videos as $sitevideo): ?>
                            <?php
                            echo $this->partial(
                                    'list_carousel.tpl', 'sitevideo', array(
                                'sitevideo' => $sitevideo,
                                'vertical' => $this->vertical,
                                'blockHeight' => $this->blockHeight,
                                'blockWidth' => $this->blockWidth,
                                'videoOption' => $this->videoOption,
                            ));
                            ?>	     
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php if (!empty($this->showPagination)) : ?>
                    <div class="sitevideo_carousel_controller">
                        <div class="sitevideo_carousel_button sitevideo_carousel_up" id="<?php echo $navsPRE ?>_back" style="display:none;">
                            <i></i>
                        </div>
                        <div class="sitevideo_carousel_button sitevideo_carousel_up_dis" id="<?php echo $navsPRE ?>_back_dis" style="display:block;">
                            <i></i>
                        </div>

                        <div class="sitevideo_carousel_button sitevideo_carousel_down fright" id ="<?php echo $navsPRE ?>_forward">
                            <i></i>
                        </div>
                        <div class="sitevideo_carousel_button sitevideo_carousel_down_dis fright" id="<?php echo $navsPRE ?>_forward_dis" style="display:none;">
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
    <div id="<?php echo $navsPRE ?>_outer" class="sitevideo_carousel sitevideo_carousel_horizontal" style="width: <?php echo (($this->limit <= $this->totalCount ? $this->limit : $this->totalCount) * ($this->blockWidth + 24)) + 60 ?>px; height: <?php echo ($this->blockHeight + 10) ?>px;">
        <?php if (!empty($this->showPagination)) : ?>  
            <div class="sitevideo_carousel_button sitevideo_carousel_left" id="<?php echo $navsPRE ?>_left" style="display:none;">
                <i></i>
            </div>
            <div class="sitevideo_carousel_button sitevideo_carousel_left_dis" id="<?php echo $navsPRE ?>_left_dis" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
                <i></i>
            </div>
        <?php endif; ?>
        <div id="<?php echo $navsPRE ?>_inner" class="sitevideo_carousel_content" style="height: <?php echo ($this->blockHeight + 5) ?>px;">
            <ul id="<?php echo $navsPRE ?>_items" class="sitevideo_carousel_items_wrapper">
                <?php $i = 0; ?>
                <?php foreach ($this->videos as $sitevideo): ?>
                    <?php
                    echo $this->partial(
                            'list_carousel.tpl', 'sitevideo', array(
                        'sitevideo' => $sitevideo,
                        'vertical' => $this->vertical,
                        'blockHeight' => $this->blockHeight,
                        'blockWidth' => $this->blockWidth,
                        'videoOption' => $this->videoOption,
                    ));
                    ?>	
                    <?php $i++; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if (!empty($this->showPagination)) : ?>
            <div class="sitevideo_carousel_button sitevideo_carousel_right" id ="<?php echo $navsPRE ?>_right" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
                <i></i>
            </div>
            <div class="sitevideo_carousel_button sitevideo_carousel_right_dis" id="<?php echo $navsPRE ?>_right_dis" style="display:none;">
                <i></i>
            </div>
        <?php endif; ?>  
    </div>
<?php endif; ?>
