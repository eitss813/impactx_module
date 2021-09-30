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
<?php
$edge = $this->viewType ? 'height' : 'width';
$sliderMode = 'horizontal';
$id = $this->identity;
$class = "slide_box_" . $id;
$margin = 20;
$carauselHeight = $this->projectHeight;
    ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()->appendFile($baseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<style type="text/css">
    .sitecrowdfunding_horizontal_carausel_nav {
        height:<?php echo $this->projectHeight; ?>px;
    }
</style>
<div class='categories_manage sitecrowdfunding_horizontal_carausel sitecrowdfunding_horizontal_project_carausel' id='categories_manage' style="height: auto;" >
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper">
        <div  id="sitecrowdfunding_featured_project_prev8_div_<?php echo $id; ?>" class="sitecrowdfunding_horizontal_carausel_nav sitecrowdfunding_horizontal_carausel_nav_left" style="display:<?php echo $this->showPagination ? 'block' : 'none' ?> ;" >
            <span id="sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
        </div>
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $this->projectHeight+10; ?>px;">
            <div id="sitecrowdfunding_featured_project_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <?php
                $i = 1;
                ?>
                <?php
                $limit = $this->rowLimit;
                $rowLimit = $limit;
                ?>
                <?php foreach ($this->projects as $project) : ?>
                    <?php $content = "<div class='sitecrowdfunding_stats sitecrowdfunding_grid_stats sitecrowdfunding_likes_comment_wrapper txt_center'>"; ?>
                    <?php if (in_array('like', $this->projectOption)) : ?>
                        <?php
                        $count = $this->locale()->toNumber($project->likes()->getLikeCount());
                        $countText = $this->translate(array('%s like', '%s likes', $project->like_count), $count);
                        $content .= '<span class="seaocore_icon_like" title="' . $countText . '">';
                        $content .= $count;
                        $content .= ' </span>';
                        ?>
                    <?php endif; ?>
                    <?php if (in_array('comment', $this->projectOption)) : ?>
                        <?php $count = $this->locale()->toNumber($project->comments()->getCommentCount()); ?>
                        <?php $countText = $this->translate(array('%s comment', '%s comments', $project->comment_count), $count); ?>
                        <?php
                        $content .= ' <span class="seaocore_icon_comment" title="' . $countText . '">';
                        $content .= $count;
                        $content .= '</span>';
                        ?>
                    <?php endif; ?>
                    <?php $content .= '</div>'; ?>
                    <?php if ($i == 1): ?>
                        <div class='featured_slidebox sitecrowdfunding_featured_horizontal_slidebox <?php echo $class; ?>'>
                            <div class='featured_slidshow_content sitecrowdfunding_featured_slidebox_content'>
                                <h3></h3>
                                <ul id="sitecrowdfunding_featured_slidebox_block_wrap_<?php echo $this->identity; ?>">
                                <?php endif; ?>
                                <?php $k = true; ?>
                                <li class="sitecrowdfunding_featured_slidebox_block">
                                    <div id="project_<?php echo $project->project_id; ?>" class="sitecrowdfunding_featured_block sitecrowdfunding_featured_block_other sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer" style="width:<?php echo $this->projectWidth; ?>px;height:<?php echo $this->projectHeight; ?>px;">
                                        <?php
                                        if ($project->photo_id) {
                                            echo $this->htmlLink($project->getHref(), "  " . $this->itemBackgroundPhoto($project, null, null, array('tag' => 'i')));
                                        } else {
                                            $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                                            echo $this->htmlLink($project->getHref(), "  " . "<i style='background-image:url(" . $url . ")'></i>");
                                        }
                                        ?>
                                        <span class="site_crowdfunding_featured" style="background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828'); ?>">
                                            <?php if (in_array('featured', $this->projectOption) && $project->featured) : ?>
                                                <span><?php echo $this->translate('Featured'); ?></span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="site_crowdfunding_sponsored" style="background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', ''); ?>">
                                            <?php if (in_array('sponsored', $this->projectOption) && $project->sponsored) : ?>
                                                <span><?php echo $this->translate('Sponsored') ?></span>
                                            <?php endif; ?>
                                        </span>

                                        <div class="sitecrowdfunding_featured_slidebox_info">
                                            <span class="sitecrowdfunding_featured_slidebox_info_center">
                                                <span class="sitecrowdfunding_featured_slidebox_info_title">                                   
                                                    <?php if (in_array('title', $this->projectOption)) : ?>
                                                        <?php echo $this->htmlLink($project->getHref(), $this->string()->truncate($this->string()->stripTags($this->translate($project->getTitle())), $this->titleTruncation), array('title' => $project->getTitle())) ?>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="sitecrowdfunding_author_name">
                                                    <?php if (in_array('owner', $this->projectOption)) : ?>
                                                        <?php
                                                        $owner = $project->getOwner();
                                                        ?>
                                                        <?php echo $this->translate("by ") . $this->htmlLink($owner->getHref(), $this->translate($owner->getTitle())); ?>
                                                    <?php endif; ?>
                                                </span>
                                                <div class="sitecrowdfunding_info">
                                                    <div class="sitecrowdfunding_stats">
                                                        <?php echo $this->sitecrowdfundingShareLinks($project, $this->projectOption); ?>
                                                    </div>
                                                </div>
                                                <?php echo $this->htmlLink($project->getHref(), "  " . $content); ?>
                                                <div class="sitecrowdfunding_featured_slidebox_btn txt_center">
                                                    <a href="<?php echo $project->getHref(); ?>" class="common_btn view_btn">View </a>
                                                </div>
                                            </span>

                                            <span class="sitecrowdfunding_featured_slidebox_info_left">
                                                <?php $count = $project->backer_count; ?>
                                                <?php $countText = $this->translate(array('%s backer', '%s backers', $count), $this->locale()->toNumber($count)) ?>
                                                <span class="site_crowdfunding_backer_count" title="<?php echo $countText ?>">
                                                    <?php if (in_array('backer', $this->projectOption)) : ?>
                                                        <?php echo $this->translate($countText); ?>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="sitecrowdfunding_pledged">
                                                    <?php
                                                    $fundedAmount = $project->getFundedAmount();
                                                    $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                                                    echo $this->translate("%s Backed",  $fundedAmount);
                                                    ?>
                                                </span>
                                            </span>
                                            <span class="sitecrowdfunding_featured_slidebox_info_right">
                                                <span style="text-align: right;">
                                                    <?php if (in_array('endDate', $this->projectOption)) : ?>
                                                        <?php echo $project->getRemainingDays(); ?>
                                                    <?php endif; ?>
                                                    <span>
                                                    </span>
                                                    </div>
                                                    </div>
                                                    <?php $k = false; ?>
                                                    </li>
                                                    <?php if ($i == $rowLimit): ?>
                                                        <?php $rowLimit = $limit; ?>
                                                        <?php echo "</li>"; ?>
                                                        <?php $i = 0; ?>
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
                                                <div id="sitecrowdfunding_featured_project_next8_div_<?php echo $id; ?>" class="sitecrowdfunding_horizontal_carausel_nav sitecrowdfunding_horizontal_carausel_nav_right" style="display:<?php echo $this->showPagination ? 'block' : 'none' ?>;" >
                                                    <span id="sitecrowdfunding_featured_project_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
                                                </div>
                                                </div>
                                                </div>
                                                <?php
                                                if ($this->showLink == 0) :
                                                    if ($this->category_id) :
                                                        ?>
                                                        <div class="widthfull txt_center mtop10"><button onClick="window.location = '<?php echo $this->category->getHref(); ?>'"><?php echo $this->translate("More From %s", $this->category->getTitle()) ?></button> </div>
                                                    <?php else : ?>
                                                        <div class="widthfull txt_center mtop10"><button onClick="window.location = '<?php echo $this->url(array('action' => 'browse'), 'sitecrowdfunding_general', true); ?>'"><?php echo $this->translate("Popular Projects") ?></button> </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <script type="text/javascript">
<?php if ($this->showPagination) : ?>
                                                        document.getElementById("sitecrowdfunding_featured_project_next8_<?php echo "$id"; ?>").style.display = "block";
                                                        document.getElementById("sitecrowdfunding_featured_project_prev8_<?php echo "$id"; ?>").style.display = "block";
<?php else : ?>
                                                        document.getElementById("sitecrowdfunding_featured_project_next8_<?php echo "$id"; ?>").style.display = "none";
                                                        document.getElementById("sitecrowdfunding_featured_project_prev8_<?php echo "$id"; ?>").style.display = "none";
<?php endif; ?>

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
                                                            this.<?php echo $edge; ?> = 0;
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
                                                                var globalContentElement = en4.seaocore.getDomElements('content');
                                                                this.noOfSlideShow = arg.noOfSlideShow;
                                                                this.id = arg.id;
                                                                this.interval = arg.interval;
                                                                this.slideBox = arg.slideBox;
                                                                this.width = $(globalContentElement).getElement("#featured_slideshow_wrapper_" + this.id).clientWidth;
                                                                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.<?php echo $edge; ?> = (this.<?php echo $edge; ?>) + "px";
                                                                $(globalContentElement).getElement("#sitecrowdfunding_featured_slidebox_block_wrap_" + this.id).style.<?php echo $edge; ?> = (this.<?php echo $edge; ?>) + "px";
                                                                this.slideElements = document.getElementsByClassName(this.slideBox);
                                                                for (var i = 0; i < this.slideElements.length; i++)
                                                                    this.slideElements[i].style.<?php echo $edge; ?> = (this.<?php echo $edge; ?>) + "px";
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
                                                                    box: $('sitecrowdfunding_featured_project_im_te_advanced_box_' + this.id),
                                                                    items: $$('#sitecrowdfunding_featured_project_im_te_advanced_box_' + this.id + ' h3'),
                                                                    size: (this.<?php echo $edge; ?>),
                                                                    handles: this.handles8,
                                                                    addButtons: {previous: $('sitecrowdfunding_featured_project_prev8_' + this.id), next: $('sitecrowdfunding_featured_project_next8_' + this.id)},
                                                                    interval: this.interval,
                                                                    fxOptions: {
                                                                        duration: 500,
                                                                        transition: '',
                                                                        wait: false,
                                                                    },
                                                                    autoPlay: this.autoPlay,
                                                                    mode: '<?php echo "$sliderMode"; ?>',
                                                                    onWalk: function (currentItem, currentHandle) {
                                                                        if(this.items.length == 1) {
                                                                            $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                                                                            $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                                                                        } else {
                                                                            if(this.currentIndex == 0) {
                                                                                $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                                                                                $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow'); 
                                                                            } else if(this.currentIndex == this.items.length - 1) { 
                                                                                $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').addClass('nocontent_slider_arrow');
                                                                                $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow');
                                                                            } else { 
                                                                                $('sitecrowdfunding_featured_project_prev8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow');
                                                                                $('sitecrowdfunding_featured_project_next8_<?php echo $id; ?>').removeClass('nocontent_slider_arrow');
                                                                            }
                                                                        } 
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

                                                <style>

                                                </style>