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
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php
$backgroupImage = $this->backgroupImage;
$defaultBackground = $baseUrl . 'application/modules/Sitecrowdfunding/externals/images/default-background.jpg';
$bcImage = ($backgroupImage) ? $backgroupImage : $defaultBackground;
?>
<div class='categories_manage sitecrowdfunding_categories_banner_background' id='categories_manage' style="background-image: url('<?php echo $bcImage; ?>');height:<?php echo $this->backgroundImageHeight; ?>px;"  >
    <div style="text-align: center;" id="slideshow_loading_image_<?php echo $id; ?>">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif'/>
    </div>
    <div style="display: none" id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper sitecrowdfunding_cat_banner_slideshow">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $this->backgroundImageHeight; ?>px;">
            <div id="sitecrowdfunding_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php $span = ""; ?>
                <?php foreach ($this->paginator as $category) : ?>
                    <?php
                    $span .="<span class='inactive'></span>";
                    ?>
                    <div class='featured_slidebox <?php echo $class; ?>' >
                        <span id="slideshow_stop_button_<?php echo $id; ?>" ></span>
                        <span id="slideshow_play_button_<?php echo $id; ?>" ></span>
                        <div class='featured_slidshow_content'>
                            <h3></h3>
                            <div class="sitecrowdfunding_categories_banner_container">
                                <div class="sitecrowdfunding_categories_banner_bottom" style="height:<?php echo $this->categoryImageHeight; ?>px;">
                                    <div id="slideshow_banner_images" class="sitecrowdfunding_categories_banner_image">
                                        <?php if ($category->banner_id) : ?>
                                            <?php echo $this->itemPhoto($this->storage->get($category->banner_id, ''), null, null, array()); ?>
                                        <?php else : ?>
                                            <img alt="" src='<?php echo $baseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/banner_images/nobanner_category.jpg"; ?>' />
                                        <?php endif; ?>
                                    </div>
                                    <div class="sitecrowdfunding_categories_banner_text">
                                        <div class="sitecrowdfunding_categories_banner_text_container">
                                            <div class="sitecrowdfunding_categories_banner_top">   
                                                <h4><?php echo $this->translate($category->banner_title); ?></h4>
                                                <p><?php echo $this->translate($category->banner_description); ?></p>
                                            </div>
                                            <div class="sitecrowdfunding_categories_banner_title"> 
                                                <?php if ($category->file_id) : ?>
                                                         <?php echo $this->itemPhoto($this->storage->get($category->file_id, ''), null, null, array('style' => 'width:30px; height:30px;')); ?>
                                                <?php elseif($category->font_icon): ?>
                                                    <i class="fa <?php echo $category->font_icon; ?>"></i>
                                                <?php else: ?>
                                                    <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                                                     <img src="<?php echo $src ?>" style="width: 30px; height: 30px;" alt="">
                                                <?php endif; ?>

                                                <?php echo $this->htmlLink($category->getHref(), $this->string()->truncate($this->string()->stripTags($this->translate($this->translate($category->getTitle()))), $this->titleTruncation), array('title' => $category->getTitle())); ?>
                                            </div>
                                            <div class="sitecrowdfunding_categories_banner_tagline" title="<?php echo $category->featured_tagline ?>">
                                                <?php echo $this->string()->truncate($this->string()->stripTags($this->translate($category->featured_tagline)), $this->taglineTruncation); ?>
                                            </div>
                                            <div class="sitecrowdfunding_categories_banner_explorebtn">
                                                <?php if ($this->showExporeButton) : ?>
                                                    <?php echo $this->htmlLink($category->getHref(), $this->translate('View Projects'), array('class' => 'common_btn')); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="sitecrowdfunding_cat_slide_nav" id="handles8_<?php echo $id; ?>">
            <div> 
                <?php echo $span; ?>
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
<?php else: ?>
                    this.width = $(globalContentElement).getWidth();
<?php endif; ?>
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
                    box: $('sitecrowdfunding_featured_channel_im_te_advanced_box_' + this.id),
                    items: $$('#sitecrowdfunding_featured_channel_im_te_advanced_box_' + this.id + ' h3'),
                    size: (this.width),
                    handles: this.handles8,
                    addButtons: {stop: $('slideshow_stop_button_' + this.id),play: $('slideshow_play_button_' + this.id)},
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

    $('categories_manage').addEvents({
            mouseover: function(){
                 $('slideshow_stop_button_<?php echo $id; ?>').click(); 
            },
            mouseleave: function(){
                 $('slideshow_play_button_<?php echo $id; ?>').click(); 
            }
    });

    //TO SHOW SLIDE SHOW AFTER COMPLETELY LOADED
    window.onload = function () {
        $('slideshow_loading_image_<?php echo $id; ?>').hide();
        $("featured_slideshow_wrapper_<?php echo $id; ?>").show();
    }


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
            /*$(globalContentElement).setStyles({
                'width': '100%',
                'margin-top': '-16px'
            });*/
            $(globalContentElement).addClass('global_content_fullwidth');
        });
    </script>
<?php endif; ?> 
