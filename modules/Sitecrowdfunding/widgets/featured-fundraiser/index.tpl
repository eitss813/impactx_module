<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php
$id = $this->identity;
$class = "slide_box_" . $id;
?>
<?php
$width = $this->projectWidth;
$height = $this->projectHeight;

$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<script type="text/javascript">var i = 1;</script>

<div id="featured_slideshow_wrapper_<?php echo $id; ?>"  >
    <div id="featured_slideshow_mask_<?php echo $id; ?>" style="width:20px" class="featured_slideshow_mask"  >
        <div id="sitecrowdfunding_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box"> 
            <?php foreach ($this->paginator as $item) : ?>

                <div class='featured_slidebox <?php echo $class; ?>' > 
                    <div style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px; position: relative;">
                        <div class='featured_slidshow_content'>
                            <!--<a href="<?php echo $item->getHref(); ?>">--><i style="background-image:url('<?php echo $item->getPhotoUrl(); ?>'); background-repeat: no-repeat;"></i><!--</a> -->
                        </div>
                        <div class='featured_slidshow_content_text'>
                            <?php if (in_array('title', $this->projectOption)) : ?>
                                <div class="site_project_title">
                                    <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($this->translate($item->getTitle())), $this->titleTruncation), array('title' => $item->getTitle())) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('owner', $this->projectOption)) : ?>
                                <span class="sitecrowdfunding_author_name"><?php echo $this->translate("by %s", $this->htmlLink($item->getOwner()->getHref(), $this->translate($item->getOwner()->getTitle()), array('title' => $item->getOwner()->getTitle()))); ?>    </span>
                            <?php endif; ?>
                            <p><?php
                                echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getDescription(), $this->descriptionTruncation);
                                ?></p><br />
                            <?php if($item->isFundingApproved()): ?>
                            <span class="backers_count">
                                <?php if (in_array('backer', $this->projectOption)) : ?>
                                    <?php $countText = $this->translate(array('%s backer', '%s backers', $item->backer_count), $this->locale()->toNumber($item->backer_count)) ?>
                                    <span class="backers_count" title="<?php echo $countText; ?>"><?php echo $countText; ?></span>
                                <?php endif; ?>
                            </span>
                            <br />
                            <?php
                            $fundedAmount = $item->getFundedAmount();
                            $fundedRatio = $item->getFundedRatio();
                            $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                            ?>
                            <?php // Add Progressvive bar ?>
                            <?php echo $this->fundingProgressiveBar($fundedRatio); ?>
                            <div class="sitecrowdfunding_funding_pledged_days_wrapper" style="position: absolute;bottom: 0px;left:0px;">
                                <span class="funded"><strong><?php echo $this->translate("%s",$fundedRatio.'%'); ?></strong><br /><?php echo $this->translate("Fundedsssssssss"); ?></span>
                                <span class="Backed"><strong><?php echo $fundedAmount; ?></strong><br/><?php echo $this->translate("Backed"); ?></span> 
                                <?php if (in_array('endDate', $this->projectOption)) : ?>
                                    <span class="days_left">
                                        <?php echo $item->getRemainingDays(); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php endif; ?> 
                            <?php if ($this->viewProjectButton) : ?>
                                <button onclick="window.location = '<?php echo $item->getHref() ?>'">
                                    <?php echo $this->translate($this->viewProjectTitle); ?>
                                </button>
                            <?php endif; ?>
                        </div>                          
                    </div>
                </div> 
            <?php endforeach; ?>
        </div>
    </div>
    <div class="sitecrowdfunding_cat_slide_nav" id="handles8_<?php echo $id; ?>">

    </div>
    <div class="featured_slideshow_option_bar" style="display:block">
        <span id="counter"></span>
        <div>
            <p class="buttons">
                <span id="sitecrowdfunding_featured_channel_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
                <span id="slideshow_stop_button_<?php echo $id; ?>" ></span><span id="slideshow_play_button_<?php echo $id; ?>" ></span>
                <span id="sitecrowdfunding_featured_channel_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
            </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function () {


        if (document.getElementsByClassName == undefined) {
            document.getElementsByClassName = function (className)
            {
                var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
                var allElements = document.getElementsByTagName("*");
                var results = [];

                var element;
                for (var i = 0; (element = allElements[i]) != null; i++) {
                    var elementClass = element.className;
                    if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
                        results.push(element);
                }
                return results;
            }
        }
        SlideShow = function ()
        {
            this.width = 0;
            this.slideElements = [];
            this.noOfSlideShow = 0;
            this.id = 0;
            this.handles8_more = '';
            this.handles8 = '';
            this.interval = 0;
            this.slideBox = '';
            this.set = function (arg)
            {
                this.noOfSlideShow = arg.noOfSlideShow;
                this.id = arg.id;
                this.interval = arg.interval;
                this.slideBox = arg.slideBox;
                var globalContentElement = en4.seaocore.getDomElements('content');
                this.width = $(globalContentElement).getWidth();
                this.height = "<?php echo $height; ?>";
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.height = (this.height) + "px";
                this.slideElements = document.getElementsByClassName(this.slideBox);
                for (var i = 0; i < this.slideElements.length; i++)
                    this.slideElements[i].style.width = (this.width) + "px";
                this.handles8_more = $$('#handles8_more_' + this.id + ' span');
                this.handles8 = $$('#handles8_' + this.id + ' span');

            }

            this.walk = function ()
            {
                var uid = this.id;
                var noOfSlideShow = this.noOfSlideShow;

                var handles8 = this.handles8;
                var nS8 = new noobSlide({
                    box: $('sitecrowdfunding_featured_channel_im_te_advanced_box_' + this.id),
                    items: $$('.slide_box_' + this.id),
                    size: (this.width),
                    handles: this.handles8,
                    addButtons: {previous: $('sitecrowdfunding_featured_channel_prev8_' + this.id), next: $('sitecrowdfunding_featured_channel_next8_' + this.id), stop: $('slideshow_stop_button_' + this.id),play: $('slideshow_play_button_' + this.id)},
                    interval: this.interval,
                    fxOptions: {
                        duration: 1000,
                        transition: '',
                        wait: false,
                    },
                    autoPlay: true,
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {

                        $$(this.handles, handles8).removeClass('active');
                        $$(currentHandle, handles8[this.currentIndex]).addClass('active');
                        $('counter').innerHTML = (this.currentIndex + 1) + "/" + this.items.length;
                        if ((this.currentIndex + 1) == (this.items.length))
                            $('sitecrowdfunding_featured_channel_next8_' + uid).hide();
                        else
                            $('sitecrowdfunding_featured_channel_next8_' + uid).show();

                        if (this.currentIndex > 0)
                            $('sitecrowdfunding_featured_channel_prev8_' + uid).show();
                        else
                            $('sitecrowdfunding_featured_channel_prev8_' + uid).hide();
                    }
                });
                //more handle buttons
                nS8.addHandleButtons(this.handles8_more);
                //walk to item 3 witouth fx
                nS8.walk(0, false, true);
            }
        }

        var slideshow = new SlideShow();
        slideshow.set({
            id: '<?php echo $id; ?>',
            noOfSlideShow: <?php echo $this->totalCount; ?>,
            interval: 5000,
            slideBox: '<?php echo $class; ?>'
        });
        slideshow.walk();
    });

    $('featured_slideshow_wrapper_<?php echo $id; ?>').addEvents({
            mouseover: function(){
                 $('slideshow_stop_button_<?php echo $id; ?>').click(); 
            },
            mouseleave: function(){
                 $('slideshow_play_button_<?php echo $id; ?>').click(); 
            }
    }); 
</script>


