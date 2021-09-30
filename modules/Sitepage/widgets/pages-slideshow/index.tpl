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
<?php $id = $this->identity; ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()->appendFile($baseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<!--<div class='categories_manage sitecrowdfunding_home_featured' id='categories_manage' style="height: <?php echo $this->height; ?>px;" >-->
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper sitecrowdfunding_home_featured_wrapper">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height: <?php echo $this->height; ?>px;">
            <div id="sitecrowdfunding_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php $span = ""; ?>
                <?php foreach ($this->paginator as $sitepage) : ?>
                    <?php
                    $imageUrl = '';
                    $photoId = 0;
                    if ($sitepage->photo_id) {
                        $photoId = $sitepage->photo_id;
                        $imageUrl = $this->storage->get($photoId, 'thumb.main')->getPhotoUrl();
                    } else {
                        $imageUrl = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                    }
                    $span .="<span class='inactive'></span>";

                    // Get org'a photo
                    $sitepageObj = Engine_Api::_()->getItem('sitepage_page', $sitepage->page_id);
                    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
                    $album = $tableAlbum->getSpecialAlbum($sitepageObj, 'cover');
                    $coverTop = 0;
                    $coverLeft = 0;
                    if ($album->cover_params && isset($album->cover_params['top'])) {
                        $coverTop = $album->cover_params['top'];
                    }
                    if(!empty($sitepageObj->page_cover)){
                        $photo = Engine_Api::_()->getItem('sitepage_photo', $sitepageObj->page_cover);
                        if(!empty($photo)){
                            $imageUrl = $photo->getPhotoUrl('thumb.cover');
                        }
                    }
                    ?>
                    <?php
                    $url = $sitepage->getHref();
                    $target = "_blank";
                    ?>
                    <div class='featured_slidebox sitecrowdfunding_home_featured_slidebox' style="height: <?php echo $this->height; ?>px;">    
                        <div class="sitecrowdfunding_home_featured_banner" <?php echo $imageUrl ? "style='background-image:url(" . $imageUrl . ")'" : "style='background-color:gray;'"; ?>>
                            <div class='featured_slidshow_content'>
                                <h3></h3> 
                                <div class='channelInfo'>
                                    <div class="sitecrowdfunding_home_featured_title">
                                        <h2 title="<?php echo $sitepage->getTitle() ?>">
                                            <?php echo $this->translate($sitepage->getTitle()); ?>
                                        </h2>
                                    </div>
                                    <div class="sitecrowdfunding_home_featured_description">
                                        <h4>
                                            <?php echo $this->string()->truncate($this->string()->stripTags($this->translate($sitepage->body)), $this->descriptionTruncation); ?>
                                        </h4>
                                    </div>
                                    <div class="sitecrowdfunding_home_featured_link">
                                        <a href="<?php echo $url; ?>" class="common_btn"><?php echo $this->translate('View Organisation'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="sitecrowdfunding_cat_slide_nav" id="handles8_<?php echo $id; ?>">
                <div>
                    <?php echo $span; ?>
                </div>
            </div>
        </div>
        <div class="featured_slideshow_option_bar sitecrowdfunding_home_featured_navigations" style="display: <?php echo $this->showNavigationButton ? 'block' : 'none'; ?>">
            <span id="sitecrowdfunding_featured_channel_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
            <span id="slideshow_stop_button_<?php echo $id; ?>" ></span><span id="slideshow_play_button_<?php echo $id; ?>" ></span>
            <span id="sitecrowdfunding_featured_channel_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
        </div>
    </div>
<!-- </div> -->

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
            this.set = function (arg)
            {
                var globalContentElement = en4.seaocore.getDomElements('content');
                this.noOfSlideShow = arg.noOfSlideShow;
                this.id = arg.id;
                this.interval = arg.interval;
                if (arg.fullWidth == 1) {
                    this.width = window.getWidth();
                }
                else {
                    this.width = $(globalContentElement).getElement("#featured_slideshow_wrapper_" + this.id).clientWidth;
                }
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
                this.slideElements = document.getElementsByClassName('featured_slidebox');
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
                    addButtons: {previous: $('sitecrowdfunding_featured_channel_prev8_' + this.id), next: $('sitecrowdfunding_featured_channel_next8_' + this.id), stop: $('slideshow_stop_button_' + this.id),play: $('slideshow_play_button_' + this.id)},
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
            interval: <?php echo $this->delay; ?>,
            fullWidth:<?php echo $this->fullWidth; ?>
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


<?php if ($this->fullWidth) : ?>
        en4.core.runonce.add(function () {
            /*if(window.getWidth()<1280){
              $('global_wrapper').setStyles({'margin-top':$$('.layout_page_header').getHeight()+'px'});
            }*/
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
<?php endif; ?>

</script>

<style>
    .layout_sitepage_pages_slideshow {
        background: none !important;
        margin-bottom: 20px;
        padding: 0 !important;
    }
    .layout_sitepage_pages_slideshow .channelInfo{
        width: 60%;
        margin: 0 50px;
        background: rgba(0, 0, 0, 0.3) !important;
        padding: 20px;
        border-radius: 5px
    }
    .layout_sitepage_pages_slideshow .featured_slidshow_content {
        display: table-cell;
        vertical-align: middle;
        background: rgba(255, 255, 255, 0.19);
    }
    .layout_sitepage_pages_slideshow .sitecrowdfunding_home_featured_description h4 {
        font-size: 22px;
        line-height: normal;
        margin-bottom: 20px;
        border-bottom: 0;
        color: #fff;
        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5);
    }
    @media(max-width:767px){
        .layout_sitepage_pages_slideshow .channelInfo{
            width: auto !important;
        }
        .generic_layout_container .layout_middle{
            position: relative;
            left: 4%;
        }
        .generic_layout_container.layout_sitepage_browsenevigation_sitepage {
            width: 102% !important;
        }
    }
</style>

