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
$width = ($this->videoWidth * 2) + 18;
$height = ($this->videoHeight * 2) + 24;
$maskHeight = ($this->videoHeight * 2) + 31;
$id = $this->identity;
$class = "slide_box_" . $id;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<style type="text/css">
    .sitevideo_horizontal_carausel_nav {
        height:<?php echo $maskHeight; ?>px;
    }
</style>

<div class='categories_manage sitevideo_horizontal_carausel sitevideo_horizontal_video_carausel' id='categories_manage' style="height: <?php echo $this->height; ?>px;" >
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper">
        <div  id="sitevideo_featured_video_prev8_div_<?php echo $id; ?>" class="sitevideo_horizontal_carausel_nav sitevideo_horizontal_carausel_nav_left" style="display:<?php echo $this->showPagination ? 'block' : 'none' ?> " >
            <span id="sitevideo_featured_video_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
        </div>
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $maskHeight; ?>px;">
            <div id="sitevideo_featured_video_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php
                $i = 1;
                $firstItem = true;
                ?>
                <?php $liBuildCond = 1; ?>
                <?php $liExitCond = 0; ?>
                <?php
                $limit = $this->rowLimit;
                $rowLimit = $limit;
                if ($this->rowLimit > 3) :
                    $rowLimit = ($this->rowLimit) - 3;
                    $limit = $this->rowLimit;
                endif;
                ?>
                <?php foreach ($this->videos as $video) : ?>
                    <?php $content = "<div class='sitevideo_stats sitevideo_grid_stats'>"; ?>
                    <?php if (in_array('creationDate', $this->videoOption)) : ?>
                        <?php
                        $content .= $this->timestamp(strtotime($video->creation_date));
                        ?>
                    <?php endif; ?>
                    <?php if (in_array('view', $this->videoOption)) : ?>
                        <?php $count = $this->locale()->toNumber($video->view_count); ?>
                        <?php $countText = $this->translate(array('%s view', '%s views', $video->view_count), $count); ?>
                        <?php
                        $content .= '<span class="sitevideo_bottom_info_views" title="' . $countText . '">';
                        $content .= $count;
                        $content .='</span>';
                        ?>
                    <?php endif; ?>

                    <?php if (in_array('like', $this->videoOption)) : ?>
                        <?php
                        $count = $this->locale()->toNumber($video->likes()->getLikeCount());
                        $countText = $this->translate(array('%s like', '%s likes', $video->like_count), $count);
                        $content .= '<span class="sitevideo_bottom_info_likes" title="' . $countText . '">';
                        $content .= $count;
                        $content .= ' </span>';
                        ?>
                    <?php endif; ?>
                    <?php if (in_array('comment', $this->videoOption)) : ?>
                        <?php $count = $this->locale()->toNumber($video->comments()->getCommentCount()); ?>
                        <?php $countText = $this->translate(array('%s comment', '%s comments', $video->comment_count), $count); ?>
                        <?php
                        $content .= ' <span class="sitevideo_bottom_info_comment" title="' . $countText . '">';
                        $content .= $count;
                        $content .= '</span>';
                        ?>
                    <?php endif; ?>
                    <?php $content .= '</div>'; ?>
                    <?php if ($i == 1): ?>
                        <div class='featured_slidebox sitevideo_featured_horizontal_slidebox <?php echo $class; ?>'>
                            <div class='featured_slidshow_content sitevideo_featured_slidebox_content'>
                                <h3></h3>
                                <ul id="sitevideo_featured_slidebox_block_wrap_<?php echo $this->identity; ?>">
                                <?php endif; ?>
                                <?php if ($firstItem) : ?>
                                    <?php $isFirstItem = true; ?>
                                    <?php $firstItem = false; ?>
                                    <?php $liBuildCond = 0; ?>
                                    <?php $liExitCond = 1; ?>
                                    <li class="sitevideo_featured_slidebox_block">
                                        <div id="video_<?php echo $video->video_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_first sitevideo_thumb_wrapper sitevideo_thumb_viewer" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;">
                                            <?php
                                                echo $this->htmlLink($video->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $fsContent . $fsDuration . $this->itemBackgroundPhoto($video, 'thumb.main', '', array('tag' => 'i')) . $content);
                                            ?>
                                            <div class="sitevideo_featured_slidebox_info"> 
                                                <span class="sitevideo_featured_slidebox_info_left">                   
                                                    <span class="sitevideo_featured_slidebox_info_title">                                   
                                                        <?php if (in_array('title', $this->videoOption)) : ?>
                                                            <?php echo $this->htmlLink($video->getHref(), $this->string()->truncate($this->string()->stripTags($video->getTitle()), $this->titleTruncation)) ?>

                                                        <?php endif; ?>
                                                    </span>
                                                    <span class="site_video_author_name">
                                                        <?php if (in_array('owner', $this->videoOption)) : ?>
                                                            <?php
                                                            $owner = $video->getOwner();
                                                            ?>

                                                            <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()); ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </span>
                                                <?php if ($video->duration && in_array('duration', $this->videoOption)): ?>
                                                    <span class="video_length">
                                                        <?php
                                                        if ($video->duration >= 3600) {
                                                            $duration = gmdate("H:i:s", $video->duration);
                                                        } else {
                                                            $duration = gmdate("i:s", $video->duration);
                                                        }
                                                        ?>
                                                        <?php echo $duration; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="sitevideo_info">
                                                <div class="sitevideo_desc">
                                                    <?php echo $this->shareLinks($video, $this->videoOption); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php else : ?>
                                    <?php if ($i % 2 == $liBuildCond) : ?>
                                        <?php $k = true; ?>
                                        <li class="sitevideo_featured_slidebox_block">
                                        <?php endif; ?>
                                        <div id="video_<?php echo $video->video_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_other sitevideo_thumb_wrapper sitevideo_thumb_viewer" style="width:<?php echo $this->videoWidth; ?>px;height:<?php echo $this->videoHeight; ?>px;">
                                            <?php
                                                echo $this->htmlLink($video->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $fsContent . $fsDuration . $this->itemBackgroundPhoto($video, 'thumb.main', '', array('tag' => 'i')) . $content);
                                            ?>
                                            <div class="sitevideo_featured_slidebox_info"> 
                                                <span class="sitevideo_featured_slidebox_info_left">                   
                                                    <span class="sitevideo_featured_slidebox_info_title">                                   
                                                        <?php if (in_array('title', $this->videoOption)) : ?>
                                                            <?php echo $this->htmlLink($video->getHref(), $this->string()->truncate($this->string()->stripTags($video->getTitle()), $this->titleTruncation)) ?>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span class="site_video_author_name">
                                                        <?php if (in_array('owner', $this->videoOption)) : ?>
                                                            <?php
                                                            $owner = $video->getOwner();
                                                            ?>
                                                            <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()); ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </span>
                                                <?php if ($video->duration && in_array('duration', $this->videoOption)): ?>
                                                    <span class="video_length">
                                                        <?php
                                                        if ($video->duration >= 3600) {
                                                            $duration = gmdate("H:i:s", $video->duration);
                                                        } else {
                                                            $duration = gmdate("i:s", $video->duration);
                                                        }
                                                        ?>
                                                        <?php echo $duration; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="sitevideo_info">
                                                <div class="sitevideo_desc">
                                                    <?php echo $this->shareLinks($video, $this->videoOption); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($i % 2 == $liExitCond) : ?>
                                            <?php $k = false; ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($i == $rowLimit): ?>
                                    <?php $rowLimit = $limit; ?>
                                    <?php if ($k) : ?>
                                        <div class="sitevideo_featured_block sitevideo_featured_block_other sitevideo_thumb_wrapper sitevideo_blank sitevideo_thumb_viewer" style="width:<?php echo $this->videoWidth; ?>px;height:<?php echo $this->videoHeight; ?>px;">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($isFirstItem == false && ($i % 2 == $liExitCond)) : ?>
                                        <?php echo "</li>"; ?>
                                    <?php endif; ?>
                                    <?php $i = 0; ?>
                                    <?php $liBuildCond = 1; ?>
                                    <?php $liExitCond = 0; ?>
                                    <?php $isFirstItem = false; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?> 
                    <?php $i++; ?>
                <?php endforeach; ?>
                <?php if ($i > 1): ?>
                    <?php if ($i <= $rowLimit) : ?>
                        <?php echo "</ul></div></div>"; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div id="sitevideo_featured_video_next8_div_<?php echo $id; ?>" class="sitevideo_horizontal_carausel_nav sitevideo_horizontal_carausel_nav_right" style="display:<?php echo $this->showPagination ? 'block' : 'none' ?> " >
            <span id="sitevideo_featured_video_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
        </div>
    </div>
</div>
<?php
if ($this->showLink == 0) :
    if ($this->category_id) :
        ?>
        <div class="widthfull txt_center mtop10"><button onClick="window.location = '<?php echo $this->category->getHref(); ?>'"><?php echo $this->translate("More From %s", $this->category->getTitle()) ?></button> </div>
    <?php else : ?>
        <div class="widthfull txt_center mtop10"><button onClick="window.location = '<?php echo $this->url(array('action' => 'browse'), 'sitevideo_video_general', true); ?>'"><?php echo $this->translate("Popular Videos") ?></button></div>
        <?php endif; ?>
    <?php endif; ?>
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
            this.autoPlay = 0;
            this.slideBox = '';
            this.set = function (arg)
            {
                this.noOfSlideShow = arg.noOfSlideShow;
                this.id = arg.id;
                this.interval = arg.interval;
                this.slideBox = arg.slideBox;
                var globalContentElement = en4.seaocore.getDomElements('content');
                this.width = $(globalContentElement).getElement("#featured_slideshow_wrapper_" + this.id).clientWidth;
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
                $(globalContentElement).getElement("#sitevideo_featured_slidebox_block_wrap_" + this.id).style.width = (this.width) + "px";
                this.slideElements = document.getElementsByClassName(this.slideBox);
                for (var i = 0; i < this.slideElements.length; i++)
                    this.slideElements[i].style.width = (this.width) + "px";
                this.handles8_more = $$('#handles8_more_' + this.id + ' span');
                this.handles8 = $$('#handles8_' + this.id + ' span');
                this.autoPlay = arg.autoPlay;

            }
            this.walk = function ()
            {
                var uid = this.id;
                var noOfSlideShow = this.noOfSlideShow;
                var handles8 = this.handles8;
                var nS8 = new noobSlide({
                    box: $('sitevideo_featured_video_im_te_advanced_box_' + this.id),
                    items: $$('#sitevideo_featured_video_im_te_advanced_box_' + this.id + ' h3'),
                    size: (this.width),
                    handles: this.handles8,
                    addButtons: {previous: $('sitevideo_featured_video_prev8_' + this.id), next: $('sitevideo_featured_video_next8_' + this.id)},
                    interval: this.interval,
                    fxOptions: {
                        duration: 500,
                        transition: '',
                        wait: false,
                    },
                    autoPlay: this.autoPlay,
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {

                        if ((this.currentIndex + 1) == (this.items.length))
                            $('sitevideo_featured_video_next8_div_' + uid).hide();
                        else
                            $('sitevideo_featured_video_next8_div_' + uid).show();

                        if (this.currentIndex > 0)
                            $('sitevideo_featured_video_prev8_div_' + uid).show();
                        else
                            $('sitevideo_featured_video_prev8_div_' + uid).hide();
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
            interval: <?php echo $this->interval; ?>,
            autoPlay: <?php echo $this->showPagination ? 0 : 1; ?>,
            slideBox: '<?php echo $class; ?>'
        });
        slideshow.walk();
    });

</script>