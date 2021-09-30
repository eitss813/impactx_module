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
$width = ($this->channelWidth * 2) + 18;
$height = ($this->channelHeight * 2) + 24;
$maskHeight = ($this->channelHeight * 2) + 31;
$id = $this->identity;
$class = "slide_box_" . $id;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<div class='categories_manage sitevideo_horizontal_carausel' id='categories_manage' >
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $maskHeight; ?>px;">
            <div id="sitevideo_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <div class='featured_slidebox sitevideo_featured_horizontal_slidebox <?php echo $class; ?>'>
                    <div class='featured_slidshow_content sitevideo_featured_slidebox_content'>
                        <h3></h3>
                        <ul id="sitevideo_featured_slidebox_block_wrap_<?php echo $this->identity; ?>">
                            <?php $i = 1; ?>
                            <?php foreach ($this->channels as $channel) : ?>
                                <?php $content = '<span class="sitevideo_stats sitevideo_grid_stats">'; ?>
                                <?php if (in_array('subscribe', $this->channelOption)) : ?>
                                    <?php $count = $this->locale()->toNumber($channel->subscribe_count); ?>
                                    <?php $countText = $this->translate(array('%s subscriber', '%s subscribers', $channel->subscribe_count), $count); ?>
                                    <?php $content .='<span class="sitevideo_bottom_info_subscribers" title="' . $countText . '">' . $count . '</span>'; ?> 
                                <?php endif; ?>
                                <?php if (in_array('like', $this->channelOption)) : ?>
                                    <?php $count = $this->locale()->toNumber($channel->likes()->getLikeCount()); ?>
                                    <?php $countText = $this->translate(array('%s like', '%s likes', $channel->like_count), $count); ?>
                                    <?php $content .='<span class="sitevideo_bottom_info_likes" title="' . $countText . '">' . $count . '</span>' ?>    
                                <?php endif; ?>
                                <?php if (in_array('comment', $this->channelOption)) : ?>
                                    <?php $count = $this->locale()->toNumber($channel->comments()->getCommentCount()); ?>
                                    <?php $countText = $this->translate(array('%s comment', '%s comments', $channel->comment_count), $count) ?>
                                    <?php $content .='<span class="sitevideo_bottom_info_comment" title="' . $countText . '">' . $count . "</span>" ?>   
                                <?php endif; ?>
                                <?php $content .="</span>"; ?>
                                <?php if ($i == 1) : ?>
                                    <li class="sitevideo_featured_slidebox_block">
                                        <div id="channel_<?php echo $channel->channel_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_first sitevideo_thumb_wrapper" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;">
                                            <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_channelInfo.tpl'; ?>
                                        </div></li>
                                <?php elseif ($i == 6): break; ?>
                                <?php else : ?>      
                                    <?php if ($i % 2 == 0) : ?>
                                        <li class="sitevideo_featured_slidebox_block">
                                        <?php endif; ?>
                                        <div id="channel_<?php echo $channel->channel_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_other sitevideo_thumb_wrapper" style="width:<?php echo $this->channelWidth; ?>px;height:<?php echo $this->channelHeight; ?>px;">
                                            <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_channelInfo.tpl'; ?>
                                        </div>
                                        <?php if ($i % 2 == 1) : ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                            <?php if ($i < 6 && ($i - 1) % 2 == 0): ?>
                                </li> 
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='categories_manage sitevideo_horizontal_carausel' id='categories_manage' >
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>" class="featured_slideshow_wrapper">
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask" style="height:<?php echo $maskHeight; ?>px;">
            <div id="sitevideo_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box">
                <div class='featured_slidebox sitevideo_featured_horizontal_slidebox <?php echo $class; ?>'>
                    <div class='featured_slidshow_content sitevideo_featured_slidebox_content'>
                        <h3></h3>
                        <ul id="sitevideo_featured_slidebox_block_wrap_<?php echo $this->identity; ?>">
                            <?php
                            $i = 1;
                            $k = 1;
                            $x = 1;
                            ?>
                            <?php foreach ($this->channels as $channel) : ?>
                                <?php
                                if ($k <= 5) : $k++;
                                    continue;
                                endif;
                                ?>
                                <?php $content = '<span class="sitevideo_stats sitevideo_grid_stats">'; ?>
                                <?php if (in_array('subscribe', $this->channelOption)) : ?>
                                    <?php $count = $this->locale()->toNumber($channel->subscribe_count); ?>
                                    <?php $countText = $this->translate(array('%s subscriber', '%s subscribers', $channel->subscribe_count), $count); ?>
                                    <?php $content .='<span class="sitevideo_bottom_info_subscribers" title="' . $countText . '">' . $count . '</span>'; ?> 
                                <?php endif; ?>
                                <?php if (in_array('like', $this->channelOption)) : ?>
                                    <?php $count = $this->locale()->toNumber($channel->likes()->getLikeCount()); ?>
                                    <?php $countText = $this->translate(array('%s like', '%s likes', $channel->like_count), $count); ?>
                                    <?php $content .='<span class="sitevideo_bottom_info_likes" title="' . $countText . '">' . $count . '</span>' ?>    
                                <?php endif; ?>
                                <?php if (in_array('comment', $this->channelOption)) : ?>
                                    <?php $count = $this->locale()->toNumber($channel->comments()->getCommentCount()); ?>
                                    <?php $countText = $this->translate(array('%s comment', '%s comments', $channel->comment_count), $count) ?>
                                    <?php $content .='<span class="sitevideo_bottom_info_comment" title="' . $countText . '">' . $count . "</span>" ?>   
                                <?php endif; ?>
                                <?php $content .="</span>"; ?>
                                <?php if ($i == 3) : ?>
                                    <li class="sitevideo_featured_slidebox_block">
                                        <div id="channel_<?php echo $channel->channel_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_first sitevideo_thumb_wrapper" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;">
                                            <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_channelInfo.tpl'; ?>
                                        </div></li>
                                <?php elseif ($i == 6): break; ?>
                                <?php else : ?>      
                                    <?php if ($x % 2 == 1) : ?>
                                        <li class="sitevideo_featured_slidebox_block">
                                        <?php endif; ?>
                                        <div id="channel_<?php echo $channel->channel_id; ?>" class="sitevideo_featured_block sitevideo_featured_block_other sitevideo_thumb_wrapper" style="width:<?php echo $this->channelWidth; ?>px;height:<?php echo $this->channelHeight; ?>px;">
                                            <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_channelInfo.tpl'; ?>
                                        </div>   
                                        <?php if ($x % 2 == 0) : ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php $x++; ?>
                                <?php endif; ?>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                            <?php if ($i < 6 && ($i == 2 || $i == 5)): ?>
                                </li> 
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($this->showLink) :
    if ($this->category_id) :
        ?>
        <div class="widthfull txt_center mtop10"><button onClick="window.location = '<?php echo $this->category->getHref(); ?>'">
                <?php if (!empty($this->buttonTitle)) : ?>
                    <?php echo $this->translate($this->buttonTitle); ?>
                <?php else : ?>
                    <?php echo $this->translate("See all %s", $this->category->getTitle()) ?>
                <?php endif; ?>
            </button> </div>
    <?php else : ?>
        <div class="widthfull txt_center mtop10"><button onClick="window.location = '<?php echo $this->url(array('action' => 'browse'), 'sitevideo_general', true); ?>'">
                <?php if (!empty($this->buttonTitle)) : ?>
                    <?php echo $this->translate($this->buttonTitle); ?>
                <?php else : ?>
                    <?php echo $this->translate("Browse Channels") ?>
                <?php endif; ?>
            </button> </div>
    <?php endif; ?>
<?php endif; ?>