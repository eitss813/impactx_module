<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$id = $this->identity;
$class = "slide_box_" . $id;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php
$backgroupImage = $this->backgroupImage;
$defaultBackground = $baseUrl . 'application/modules/Sitevideo/externals/images/video1.jpg';
$bcImage = ($backgroupImage) ? $backgroupImage : $defaultBackground;
?>
<div class='categories_manage sitevideo_categories_banner_background' id='categories_manage' style="background-image: url('<?php echo $bcImage; ?>');height:<?php echo $this->backgroundImageHeight; ?>px;"  >
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper sitevideo_cat_banner_slideshow">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $this->backgroundImageHeight; ?>px;">
            <div id="sitevideo_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php $span = ""; ?>
                <?php foreach ($this->paginator as $category) : ?>
                    <?php
                    $span .="<span class='inactive'></span>";
                    ?>
                    <div class='featured_slidebox <?php echo $class; ?>'>
                        <div class='featured_slidshow_content'>
                            <h3></h3>
                            <div class="sitevideo_categories_banner_container">
                                <div class="sitevideo_categories_banner_top">   
                                    <h4><?php echo $category->banner_title; ?></h4>
                                    <p><?php echo $category->banner_description; ?></p>
                                </div>
                                <div class="sitevideo_categories_banner_bottom" style="height:<?php echo $this->categoryImageHeight; ?>px;">
                                    <div class="sitevideo_categories_banner_image">
                                        <?php if ($category->banner_id) : ?>
                                            <a <?php if ($category->banner_url) : ?> href="<?php echo $category->banner_url ?>" <?php endif; ?> title="<?php echo $category->banner_title ?>" <?php if ($category->banner_url_window == 1): ?> target ="_blank" <?php endif; ?>>
                                                <?php echo $this->itemPhoto($this->storage->get($category->banner_id, ''), null, null, array()); ?>
                                            </a>
                                        <?php else : ?>
                                            <img alt="" src='<?php echo $baseUrl . "application/modules/Sitevideo/externals/images/video.jpg"; ?>' />
                                        <?php endif; ?>
                                    </div>
                                    <div class="sitevideo_categories_banner_text">
                                        <div class="sitevideo_categories_banner_title">
                                            <?php if ($category->file_id) : ?>
                                                <?php echo $this->itemPhoto($this->storage->get($category->file_id, ''), null, null, array('style' => 'width:30px;height:30px;')); ?>
                                            <?php endif; ?>

                                            <?php echo $this->htmlLink($category->getHref(), $this->string()->truncate($this->string()->stripTags($category->getTitle()), $this->titleTruncation)); ?>
                                        </div>
                                        <div class="sitevideo_categories_banner_tagline">
                                            <?php echo $this->string()->truncate($this->string()->stripTags($category->featured_tagline), $this->taglineTruncation); ?>
                                        </div>
                                        <div class="sitevideo_categories_banner_explorebtn">
                                            <?php if ($this->showExporeButton) : ?>
                                                <?php echo $this->htmlLink($category->getHref(), 'Explore Now'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="sitevideo_cat_slide_nav" id="handles8_<?php echo $id; ?>">
            <div>
                <?php echo $span; ?>
            </div>
        </div>
        <div class="featured_slideshow_option_bar" style="display: <?php echo $this->showNavigationButton ? 'block' : 'none'; ?>">
            <div>
                <p class="buttons">
                    <span id="sitevideo_featured_channel_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
                    <span id="sitevideo_featured_channel_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
                </p>
            </div>
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
                 <?php if ($this->fullWidth) : ?>
                this.width = window.getWidth();
                <?php else:?>
                 this.width = $(globalContentElement).getWidth();
                <?php endif;?>
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
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
                    box: $('sitevideo_featured_channel_im_te_advanced_box_' + this.id),
                    items: $$('#sitevideo_featured_channel_im_te_advanced_box_' + this.id + ' h3'),
                    size: (this.width),
                    handles: this.handles8,
                    addButtons: {previous: $('sitevideo_featured_channel_prev8_' + this.id), next: $('sitevideo_featured_channel_next8_' + this.id)},
                    interval: this.interval,
                    fxOptions: {
                        duration: 500,
                        transition: '',
                        wait: false,
                    },
                    autoPlay: true,
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {
                        $$(this.handles, handles8).removeClass('active');
                        $$(currentHandle, handles8[this.currentIndex]).addClass('active');
                        if ((this.currentIndex + 1) == (this.items.length))
                            $('sitevideo_featured_channel_next8_' + uid).hide();
                        else
                            $('sitevideo_featured_channel_next8_' + uid).show();

                        if (this.currentIndex > 0)
                            $('sitevideo_featured_channel_prev8_' + uid).show();
                        else
                            $('sitevideo_featured_channel_prev8_' + uid).hide();
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
            interval: 4000,
            slideBox: '<?php echo $class; ?>'
        });
        slideshow.walk();
    });
</script>
<?php if ($this->fullWidth) : ?>
<script>
    en4.core.runonce.add(function () {
        var globalContentElement = en4.seaocore.getDomElements('content');
        if ($$('.layout_main')) {
            var globalContentWidth = $(globalContentElement).getWidth();
            $$('.layout_main').setStyles({
                'width': globalContentWidth,
                'margin': '0 auto'
            });
        }
        $(globalContentElement).setStyles({
            'width': '100%',
            'margin-top': '-16px'
        });
    });
</script>
<?php endif;?>
