<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _video_view.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$identity = $this->idenity;
if ($this->id) :
    $identity = $this->id;
endif;
if ($this->paginatorVideoView) {
    $this->paginator = $this->paginatorVideoView;
}
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>

    <ul class='videos_manage siteevideo_videos_view siteevideo_channels_video_view' id='channels_manage<?php echo "_" . $identity; ?>'>
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="sitevideo_thumb_wrapper" style=" width:<?php echo $this->videoViewWidth; ?>px; height:<?php echo $this->videoViewHeight; ?>px;">

                    <?php $fsContent = ""; ?>
                    <?php if ($item->featured): ?>
                        <?php $fsContent .= '<div class="sitevideo_featured">' . $this->translate("Featured") . '</div>'; ?>
                    <?php endif; ?>
                    <?php if ($item->sponsored): ?>
                        <?php $fsContent .= '<div class="sitevideo_sponsored" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                    <?php endif; ?>
                    <?php if (in_array('numberOfVideos', $this->channelOption)) : ?>
                        <?php $fsContent .= '<div class="sitevideo_channels_videos_count">' . $this->translate(array('%s video', '%s videos', $item->videos_count), $this->locale()->toNumber($item->videos_count)) . '</div>' ?>
                    <?php endif; ?>
                    <?php
                    if ($item->file_id) {
                        echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='watch_now_btn'>" . $this->translate('watch now') . "</span>" . $fsContent . $this->itemBackgroundPhoto($item, $this->videoViewThumbnailType, $item->getTitle(), array('tag' => 'i')));
                    } else {
                        echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='watch_now_btn'>" . $this->translate('watch now') . "</span>" . $fsContent);
                    }
                    ?>
                    <div class="sitevideo_info">
                        <div class="sitevideo_bottom_info">
                            <?php if (in_array('title', $this->channelOption)) : ?>
                                <h3>
                                    <?php $titleTruncationLimit = ($this->titleTruncationGridNVideoView ? $this->titleTruncationGridNVideoView : $this->titleTruncation); ?>
                                    <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $titleTruncationLimit)); ?>
                                </h3>
                            <?php endif; ?>


                            <div class='site_video_author_name clr'>
                                <?php if (in_array('owner', $this->channelOption)) : ?>
                                    <?php
                                    $owner = $item->getOwner();
                                    ?>
                                    <?php echo $this->translate('by %s', $this->htmlLink($owner->getHref(), $owner->getTitle())); ?><?php endif; ?>

                            </div>
                            <div class="sitevideo_stats widthfull clr">
                                <span class="video_views">
                                    <?php if (in_array('subscribe', $this->channelOption)) : ?>
                                        <?php $count = $this->locale()->toNumber($item->subscribe_count); ?>
                                        <?php $countText = $this->translate(array('%s subscriber', '%s subscribers', $item->subscribe_count), $count); ?>
                                        <span class="sitevideo_bottom_info_subscribers" title="<?php echo $countText; ?>">
                                            <?php echo $count ?> 
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('like', $this->channelOption)) : ?>
                                        <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                                        <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                                        <span class="sitevideo_bottom_info_likes" title="<?php echo $countText; ?>">
                                            <?php echo $count; ?>    
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('comment', $this->channelOption)) : ?>

                                        <?php $count = $this->locale()->toNumber($item->comments()->getCommentCount()); ?>
                                        <?php $countText = $this->translate(array('%s comment', '%s comments', $item->comment_count), $count); ?>
                                        <span class="sitevideo_bottom_info_comment" title="<?php echo $countText; ?>">
                                            <?php echo $count ?>   
                                        </span>
                                    <?php endif; ?>
                                </span>
                                <?php if (in_array('rating', $this->channelOption)) : ?>
                                    <div class="sitevideo_ratings"><?php echo $this->ratingInfo($item, array('widget_id' => $identity . '_a')); ?></div>
                                <?php endif; ?>
                                <?php if (($item->authorization()->isAllowed($this->viewer(), 'edit') || $item->authorization()->isAllowed($this->viewer(), 'delete')) && $this->showEditDeleteOption): ?>
                                    <div class='sitevideo_options'>
                                        <span class="dot"></span>
                                        <span class="dot"></span>
                                        <span class="dot"></span>
                                        <ul class="sitevideo_options_dropdown">
                                            <?php if ($item->authorization()->isAllowed($this->viewer(), 'edit')): ?>
                                                <li> 
                                                    <?php
                                                    echo $this->htmlLink(array(
                                                        'route' => 'sitevideo_specific',
                                                        'action' => 'edit',
                                                        'channel_id' => $item->channel_id
                                                            ), $this->translate('Edit Channel'), array(
                                                        'class' => 'buttonlink icon_video_edit'
                                                    ));
                                                    ?> 
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($item->authorization()->isAllowed($this->viewer(), 'delete')): ?>
                                                <li>
                                                    <?php
                                                    echo $this->htmlLink(array(
                                                        'route' => 'sitevideo_specific',
                                                        'action' => 'delete',
                                                        'channel_id' => $item->channel_id
                                                            ), $this->translate('Delete Channel'), array(
                                                        'class' => 'buttonlink smoothbox icon_video_delete'
                                                    ));
                                                    ?> 

                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                </span>
                            </div>
                        </div>
                        <div class="sitevideo_desc clr">
                            <?php echo $this->shareLinks($item, $this->channelOption); ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (empty($this->is_ajax) && $this->isViewMoreButton) : ?>
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => '', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="seaocore_view_more" id="loding_image" style="display: none;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
        <div id="hideResponse_div"> </div>
    <?php endif; ?>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate($this->message); ?>
            <?php if ($this->can_create) : ?>
                <?php echo $this->translate('Get started by %1$screating%2$s a new channel.', '<a href="' . $this->url(array('action' => 'create'), 'sitevideo_general', true) . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>
<?php if (empty($this->is_ajax) && $this->isViewMoreButton) : ?>
    <script type="text/javascript">
        function viewMoreChannels(viewFormat)
        {
            $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#seaocore_view_more').style.display = 'none';
            $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#loding_image').style.display = '';
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>
            };
            en4.core.request.send(new Request.HTML({
                method: 'get',
                'url': en4.core.baseUrl + '<?php echo $this->widgetPath ?>',
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: getNextPage(),
                    is_ajax: 1,
                    loaded_by_ajax: true,
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#hideResponse_div').innerHTML = responseHTML;
                    var videocontainer = $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#hideResponse_div').getElement('#channels_manage<?php echo "_" . $identity; ?>').innerHTML;
                    $('channels_manage<?php echo "_" . $identity; ?>').innerHTML = $('channels_manage<?php echo "_" . $identity; ?>').innerHTML + videocontainer;
                    $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#loding_image').style.display = 'none';
                    $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#hideResponse_div').innerHTML = "";
                }
            }));
            return false;
        }
    </script>
<?php endif; ?>
<?php if ($this->isViewMoreButton) : ?>
    <?php if ($this->showContent == 3): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php elseif ($this->showContent == 2): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php endif; ?>


    <script type="text/javascript">

        function getNextPage() {
            return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }

        function hideViewMoreLink(showContent) {
            if (showContent == 3) {
                $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#seaocore_view_more').style.display = 'none';
                var totalCount = '<?php echo $this->paginator->count(); ?>';
                var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

                function doOnScrollLoadChannel()
                {
                    if (typeof ($$('.layout_sitevideo_channels_sitevideo')[0].getElement('#seaocore_view_more').offsetParent) != 'undefined') {
                        var elementPostionY = $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#seaocore_view_more').offsetTop;
                    } else {
                        var elementPostionY = $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#seaocore_view_more').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                        if ((totalCount != currentPageNumber) && (totalCount != 0))
                            viewMoreChannels();
                    }
                }
                window.onscroll = doOnScrollLoadChannel;

            } else if (showContent == 2) {
                var view_more_content = $$('.layout_sitevideo_channels_sitevideo')[0].getElement('#seaocore_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMoreChannels();
                });
            }
        }
    </script>
<?php endif; ?>
<script type="text/javascript">
    $$('.core_main_video').getParent().addClass('active');
</script>