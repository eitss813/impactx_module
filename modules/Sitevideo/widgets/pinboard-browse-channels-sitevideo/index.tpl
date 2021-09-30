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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php if ($this->countPage > 0): ?>
    <?php if ($this->autoload): ?>
        <div id="pinboard_<?php echo $this->identity ?>">
            <?php if (isset($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
                <div class="sitevideo_profile_loading_image"></div>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            var layoutColumn = 'middle';
            if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_left')) {
                layoutColumn = 'left';
            } else if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_right')) {
                layoutColumn = 'right';
            }
            PinBoardSeaoObject[layoutColumn].add({
                contentId: 'pinboard_<?php echo $this->identity ?>',
                widgetId: '<?php echo $this->identity ?>',
                totalCount: '<?php echo $this->totalCount ?>',
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainerClass: 'layout_sitevideo_pinboard_browse_channels_sitevideo'
            });

        </script>
    <?php else: ?>
        <?php if (!$this->autoload && !$this->is_ajax_load): ?> 
            <div id="pinboard_<?php echo $this->identity ?>"></div>
            <script type="text/javascript">
                en4.core.runonce.add(function () {
                    var pinBoardViewMore = new PinBoardSeaoViewMore({
                        contentId: 'pinboard_<?php echo $this->identity ?>',
                        widgetId: '<?php echo $this->identity ?>',
                        totalCount: '<?php echo $this->totalCount ?>',
                        viewMoreId: 'seaocore_view_more_<?php echo $this->identity ?>',
                        loadingId: 'seaocore_loading_<?php echo $this->identity ?>',
                        requestParams:<?php echo json_encode($this->params) ?>,
                        responseContainerClass: 'layout_sitevideo_pinboard_browse_channels_sitevideo'
                    });
                    PinBoardSeaoViewMoreObjects.push(pinBoardViewMore);
                });
            </script>
        <?php endif; ?>
        <?php $countButton = count($this->show_buttons); ?>
        <?php foreach ($this->paginator as $channel): ?>
            <?php
            $noOfButtons = $countButton;
            if ($this->show_buttons):
                $alllowComment = (in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $channel->authorization()->isAllowed($this->viewer(), "comment");
                if (in_array('comment', $this->show_buttons) && !$alllowComment) :
                    $noOfButtons--;
                endif;
                if (in_array('like', $this->show_buttons) && !$alllowComment) :
                    $noOfButtons--;
                endif;
            endif;
            ?>
            <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
                <div class="seaocore_board_list b_medium" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;"> 
                    <div>
                        <div class="seaocore_board_list_thumb">
                            <a href="<?php echo $channel->getHref() ?>" class="seaocore_thumb">
                                <table>
                                    <tr valign="middle">
                                        <td>
                                        	<span class='video_overlay'></span> 
                                          <span class='watch_now_btn'><?php echo $this->translate('watch now'); ?></span>
                                            <?php
                                            $options = array('align' => 'center');

                                            if (isset($this->params['withoutStretch']) && $this->params['withoutStretch']):
                                                $options['style'] = 'width:auto; max-width:' . ($this->params['itemWidth'] - 18) . 'px;';
                                            endif;
                                            ?>  
                                            <?php echo $this->itemPhoto($channel, $this->thumbnailType, '', $options); ?>
                                        </td> 
                                    </tr> 
                                </table>
                            </a>
                        </div>
                        <div class="seaocore_board_list_btm">       
                            <?php if (!empty($this->channelOption) && in_array('owner', $this->channelOption)): ?>                
                                <?php echo $this->itemPhoto($channel->getOwner(), 'thumb.icon'); ?>                 
                                <div class="o_hidden seaocore_stats seaocore_txt_light">            
                                    <b> <?php echo $this->htmlLink($channel->getOwner()->getHref(), $channel->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?> </b>     
                                </div>
                            <?php endif; ?>             

                        </div>
                        <div class="seaocore_board_list_cont">
                            <?php if (!empty($this->channelOption) && in_array('title', $this->channelOption)): ?>
                                <div class="seaocore_title">
                                    <?php echo $this->htmlLink($channel, Engine_Api::_()->seaocore()->seaocoreTruncateText($channel->getTitle(), $this->titleTruncation)) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->descriptionTruncation): ?>
                                <div class="seaocore_description">
                                    <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($channel->getDescription(), $this->descriptionTruncation) ?>
                                </div>  
                            <?php endif; ?>
                            <?php echo $this->channelInfo($channel, $this->channelOption); ?>
                        </div>
                        <?php if (!empty($this->userComment)) : ?>
                            <div class="seaocore_board_list_comments o_hidden">
                                <?php echo $this->action("list", "pin-board-comment", "seaocore", array("type" => $channel->getType(), "id" => $channel->channel_id, 'widget_id' => $this->identity)); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($this->show_buttons)): ?>
                            <div class="seaocore_board_list_action_links">
                                <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $channel->getHref()); ?>
                                <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment && !empty($this->userComment)): ?>
                                    <?php if (in_array('comment', $this->show_buttons)): ?>
                                        <a href='javascript:void(0);' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $channel->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="Comment"><!--<?php echo $this->translate('Comment'); ?>--></a> 
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php $this->pinboardShareLinks($channel, $this->show_buttons); ?>
                                <?php if (in_array('pinit', $this->show_buttons)): ?>
                                    <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $channel->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $channel->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $channel->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon" title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div> </div>
        <?php endforeach; ?>

        <?php if (!$this->autoload && !$this->is_ajax_load): ?>
            <div class="seaocore_view_more mtop10 dnone" id="seaocore_view_more_<?php echo $this->identity ?>">
                <a href="javascript:void(0);" id="" class="buttonlink icon_viewmore"><?php echo$this->translate('View More') ?></a>
            </div>
            <div class="seaocore_loading dnone" id="seaocore_loading_<?php echo $this->identity ?>" >
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" style="margin-right: 5px;">
                <?php echo $this->translate('Loading...') ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php else: ?>
    <?php if ($this->is_ajax_load): ?>
        <script type="text/javascript">
            var layoutColumn = 'middle_page_browse';
            PinBoardSeaoObject[layoutColumn].currentIndex++;
        </script>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
        <div class="tip">
            <span>
                <?php if (isset($this->formValues['tag_id']) || isset($this->formValues['category_id']) || isset($this->formValues['location']) || isset($this->formValues['search'])): ?> 
                    <?php echo $this->translate('Nobody has created an channel with that criteria.'); ?>
                <?php else: ?>  
                    <?php echo $this->translate('No channels have been created yet.'); ?>
                <?php endif; ?>  
                <?php if ($this->canCreate): ?>
                    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), "sitevideo_general") . '">', '</a>'); ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
<?php endif; ?>
