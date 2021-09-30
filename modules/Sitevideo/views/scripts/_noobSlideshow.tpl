<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _noobSlideshow.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->widget_place = 'middle1';
?>
<div class="layout_advancedslideshow_<?php echo $this->widget_place; ?>_advancedslideshows"> <?php
    $slide_caption = '';
    $image_text_var = '';
    $image_count = 1;
    $baseUrl = $this->layout()->staticBaseUrl;
    $this->num_of_slideshow = $this->total_images;
    $slidesPerRow = floor($this->width / 50);
    $totalRow = ceil($this->total_images / $slidesPerRow);

    $slidesPerColumn = floor($this->height / 50);
    $totalColumn = ceil($this->total_images / $slidesPerColumn);
    if (!$this->autoPlay)
        $this->autoPlay = false;
    else {
        $this->autoPlay = true;
    }

    if (!$this->showController)
        $showControllerInNoob = false;
    else
        $showControllerInNoob = true;

    if ($this->num_of_slideshow):
        // Starting work for "Slide Show".
        foreach ($this->paginator as $item) {
            $temp_target = 'target="_blank"';
            $slideContent = '<a href="' . $item->getHref() . '" ' . $temp_target . '><img alt="" src="' . $item->getPhotoUrl() . '" width=" ' . $this->width . ' " height=" ' . $this->height . ' "></a>';
            $caption = Engine_Api::_()->seaocore()->seaocoreTruncateText($item->description, $this->captionTruncation);
            if ($this->caption == 1) {
                if (!empty($item->description)) {
                    $slide_caption = '<div class="noob_slidebox_caption">' . $caption . '</div> ';
                } else {
                    $slide_caption = '';
                }
            }

            $image_text_var .= '<div class="noob_slidebox" style="width: ' . $this->width . 'px; height: ' . $this->height . 'px;">

  <div class="noob_slidebox_video">
    ' . $slideContent . $slide_caption . '            
  </div>
</div>     
<div class="featured_slidshow_content">
  <h3 style="display:none">
    <span>' . $image_count++ . '_caption_title:' . $caption . ' _caption_link:</span>
  </h3>
</div>';
        }
        ?>

        <script type="text/javascript">
            en4.core.runonce.add(function () {
                var height = '<?php echo $this->height ?>';
                var width = '<?php echo $this->width ?>';
                var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
                var handles8_more = $$('.handles8_more span');

    <?php if ($this->mouseEnterEvent): ?>
                    var handle_event = 'mouseenter';
    <?php else: ?>
                    var handle_event = '';
    <?php endif; ?>

                handles8_more = document.getElement('.layout_advancedslideshow_<?php echo $this->widget_place; ?>_advancedslideshows').getElements('.handles8_more span');

                var nS8 = new noobSlide({
                    box: $('noob_featured_im_te_advanced_box'),
                    items: $$('#noob_featured_im_te_advanced_box h3'),
                    size: (width),
                    handles: $$('#handles8 span'),
                    handle_event: handle_event,
                    addButtons: {previous: $('noob_featured_prev8'), next: $('noob_featured_next8'), stop: $('noob_featured_stop8'), play: $('noob_featured_play8')},
                    interval: '<?php echo $this->delay; ?>',
                    fxOptions: {
                        duration: '<?php echo $this->duration; ?>',
                        transition: '',
                        wait: false
                    },
                    autoPlay: '<?php echo $this->autoPlay ?>',
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {
                        // Finding the current number of index.
                        var current_index = this.items[this.currentIndex].innerHTML;
                        var current_start_title_index = current_index.indexOf(">");
                        var current_last_title_index = current_index.indexOf("</span>");
                        // This variable containe "Index number" and "Title" and we are finding index.
                        var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
                        // Find out the current index id.
                        var current_index = current_title.indexOf("_");
                        // "current_index" is the current index.
                        current_index = current_title.substr(0, current_index);

                        handles8_more.removeClass('checked');
                        if ($('handles8_more_' + current_index)) {
                            $('handles8_more_' + current_index).getElement('span').addClass('checked');
                        }
                    }
                });

                //more handle buttons
                nS8.addHandleButtons(handles8_more);
                //walk to item 3 witouth fx
                nS8.walk(0, false, true);
            });
        </script>

        <?php if ($this->thumb): ?>

            <?php if ($this->thumbPosition == 'bottom'): ?>
                <div class="sr_media_slideshow_wrapper" style="width:<?php echo $this->width; ?>px;height:<?php echo $totalRow * 50 + 10 + $this->height ?>px">
                <?php elseif ($this->thumbPosition == 'left'): ?>
                    <div class="sr_media_slideshow_wrapper sr_media_slideshow_thumbs_left" style="width:<?php echo $totalColumn * 50 + 5 + $this->width; ?>px;height:<?php echo $this->height ?>px">
                    <?php elseif ($this->thumbPosition == 'right'): ?>
                        <div class="sr_media_slideshow_wrapper sr_media_slideshow_thumbs_right" style="width:<?php echo $totalColumn * 50 + 5 + $this->width; ?>px;height:<?php echo $this->height ?>px"> 
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="sr_media_slideshow_wrapper">
                        <?php endif; ?>  

                        <div id="slideshow_wrapper" class="noob_slideshow" style="width:<?php echo $this->width; ?>px;height:<?php echo $this->height; ?>px;">
                            <div class="noob_slideshow_mask" style="width: <?php echo $this->width; ?>px; height:<?php echo $this->height; ?>px;">
                                <div id="noob_featured_im_te_advanced_box" class="featured_slideshow_advanced_box">
                                    <?php echo $image_text_var; ?>
                                </div>
                            </div>

                            <div class="noob_slideshow_options"  style="<?php if ($this->num_of_slideshow < 2 || empty($showControllerInNoob)): ?> display:none;<?php endif; ?>"  >
                                <ul class="buttons" >
                                    <li id="noob_featured_prev8" class="noob_pre noob_pre_nxt noob_slideshow_controller" title="<?php echo $this->translate("Previous") ?>" ><i></i></li>
                                    <li id="noob_featured_stop8" class="noob_stop noob_stop_play  noob_slideshow_controller" title="Stop"><i></i></li>
                                    <li id="noob_featured_play8" class="noob_play noob_stop_play noob_slideshow_controller" title="Play"><i></i></li>
                                    <li id="noob_featured_next8" class="noob_nxt noob_pre_nxt noob_slideshow_controller" title="<?php echo $this->translate("Next") ?>"><i></i></li>
                                </ul>
                            </div>

                            <?php if ($this->thumb == 1): ?>
                                <?php if ($this->thumbPosition == 'bottom'): ?>
                                    <div class="sr_media_slideshow_paging_thumbs" style='width:<?php echo $this->width; ?>px;bottom:-<?php echo $totalRow * 50 + 10 ?>px;'>
                                    <?php elseif ($this->thumbPosition == 'left') : ?>
                                        <div class="sr_media_slideshow_paging_thumbs"  style='width:<?php echo $totalColumn * 50; ?>px;height:<?php echo $this->height; ?>px;left:-<?php echo $totalColumn * 50 + 5; ?>px;top:0;'>
                                        <?php elseif ($this->thumbPosition == 'right') : ?>
                                            <div class="sr_media_slideshow_paging_thumbs" style='width:<?php echo $totalColumn * 50; ?>px;height:<?php echo $this->height; ?>px;right:-<?php echo $totalColumn * 50 + 5 ?>px;top:0;'>
                                            <?php endif; ?>
                                            <div class="wrapper" style="<?php if ($this->num_of_slideshow < 2): ?> display:none;<?php endif; ?>" >
                                                <?php
                                                $i = 1;
                                                foreach ($this->paginator as $item):
                                                    ?>
                                                    <p  class="handles8_more" id="handles8_more_<?php echo $i; ?>">  <?php
                                                        $html_thumbnails = $item->getPhotoUrl();
                                                        $itemPhoto = '<img class="" alt="" src="' . $html_thumbnails . '" width="48" height="48">';
                                                        ?>
                                                        <span class="<?php echo $i == 1 ? 'checked' : ''; ?>">
                                                            <?php echo $itemPhoto; ?>
                                                        </span>
                                                    </p>
                                                    <?php
                                                    $i++;
                                                endforeach;
                                                ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="noob_slideshow_paging_buttons <?php echo "fleft"; ?>" style="margin-top:<?php echo "-30";
                                        ?>px;
                                             ">
                                            <div class="wrapper" style="<?php if ($this->num_of_slideshow < 2): ?> display:none;<?php endif; ?>" >
                                                <?php for ($i = 1; $i <= $this->num_of_slideshow; $i++): ?>
                                                    <p  class="handles8_more" id="handles8_more_<?php echo $i; ?>">
                                                        <span class="<?php echo $i == 1 ? 'checked' : ''; ?>" style="height:<?php echo 10; ?>px;width:<?php echo 10; ?>px;"></span>
                                                    </p>
                                                <?php endfor; ?>
                                            </div>
                                        </div>    
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <style type="text/css">
                    .noob_slidebox_caption{
                        background:#000000;
                        <?php
                        if ($this->thumb == 1)
                            echo "bottom: 63px;";
                        else
                            echo "bottom: 30px;";
                        ?>
                        ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=75)";
                        filter: alpha(opacity=75);
                        -khtml-opacity:0.75;
                        -moz-opacity: 0.75;
                        opacity: 0.75;
                        position:absolute;
                        white-space: nowrap;
                        width:100%;
                        padding:3px 10px;
                    }
                    .noob_slidebox_caption p{
                        color: White;
                        font-size:15px;
                        overflow:hidden;
                        text-overflow: ellipsis;
                    }
                </style>

                <style type="text/css">
                    .noob_slidebox_video td img {
                        max-height: <?php echo $this->height; ?>px;
                        max-width: <?php echo $this->width; ?>px;
                    }
                    .noob_slidebox_video{
                        height:100%;
                        position:relative;
                    }
                    .noob_slideshow_mask{
                        height : <?php echo $this->height; ?>px;
                        width : <?php echo $this->width; ?>px;
                    }

                    .noob_slidebox_video td{
                        text-align:center;
                        vertical-align:middle;
                        height:100%;
                        width:100%;
                    }
                    .noob_slidebox_video td img{
                        border:none;
                        max-height:<?php echo $this->height; ?>px;
                        max-width:<?php echo $this->width; ?>px;
                    }
                    .noob_slideshow_paging_thumbs .wrapper p span{
                        margin: 2px;
                        border: 2px solid 1;
                    }
                    .noob_slideshow_paging_thumbs{
                        background-color:#000000;
                        opacity : 9 ;
                    }
                    .noob_slideshow_paging_thumbs .wrapper p span.checked{
                        border: 2px solid #5F93B4;
                    }
                    .noob_slideshow_paging_buttons{
                        text-align:center;
                    }

                    .featured_slideshow_advanced_box{
                        position: absolute;
                    }

                    .noob_slideshow_paging_buttons .wrapper p span:hover,
                    .noob_slideshow_paging_buttons .wrapper p span.checked{
                        background: #000 ;
                        opacity:.9;
                        filter:alpha(opacity=900);
                    }
                    .noob_slideshow_paging_buttons .wrapper p span{
                        background-color: #000 ;
                        border-radius:8px;
                        box-shadow:0 1px 0 #FFFFFF;
                        cursor:pointer;
                        float:left;
                        font-size:0;
                        height:12px;
                        margin:0 1px;
                        width:12px;
                    }

                    .sr_media_slidebox_fullscreen{
                        position:absolute;
                        right:10px;
                        top:10px;
                        z-index:1;
                    }
                    .sr_media_slidebox_fullscreen i{
                        background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/fullscreen.png);
                        display:block;
                        height:15px;
                        width:15px;
                    }
                    /*Media Slidshow Css starts*/
                    .sr_media_slideshow_wrapper{
                        margin:0 auto 15px;
                    }
                    .sr_media_slideshow_thumbs_left .noob_slideshow{
                        float:right;
                    }
                    .sr_media_slideshow_thumbs_right .noob_slideshow{
                        float:left;
                    }

                    /*Paging  Thumbs*/
                    .sr_media_slideshow_paging_thumbs{
                        position:absolute;
                        text-align:center;
                    }
                    .sr_media_slideshow_paging_thumbs .wrapper,
                    .sr_media_slideshow_paging_thumbs .wrapper p{
                        display:inline-block;
                        padding: 0;
                        margin: 0;
                        font-size:0;
                    }
                    .sr_media_slideshow_paging_thumbs .wrapper p{
                        margin: 1px;
                    }
                    .sr_media_slideshow_paging_thumbs .wrapper p span{
                        background-color:#000;
                        cursor:pointer;
                        float:left;
                        height:48px;
                        width:48px;
                        opacity:0.5;
                        filter:alpha(opacity=50);
                    }
                    .sr_media_slideshow_paging_thumbs .wrapper p span img{
                        border:none;
                    }
                    .sr_media_slideshow_paging_buttons .wrapper p span,
                    .sr_media_slideshow_paging_thumbs .wrapper p span{
                        opacity:0.5;
                        filter:alpha(opacity=50);
                    }
                    .sr_media_slideshow_paging_buttons .wrapper p span:hover,
                    .sr_media_slideshow_paging_buttons .wrapper p span.checked,
                    .sr_media_slideshow_paging_thumbs .wrapper p span:hover,
                    .sr_media_slideshow_paging_thumbs .wrapper p span.checked{
                        background:#FF0000;
                        opacity:.9;
                        filter:alpha(opacity=900);
                    }
                </style>