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
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<div class="o_hidden">
    <?php $isUploadAllowed = $this->allow_upload_video && $this->viewerId && ($this->channel->owner_id == $this->viewerId); ?>
    <?php if ($isUploadAllowed): ?>
        <?php $url = $this->url(array('action' => 'create', 'channel_id' => $this->channelId), "sitevideo_video_general", true); ?>
    <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
        <div class="seaocore_add">
            
            <a href='<?php echo $url ?>' data-SmoothboxSEAOClass="seao_add_video_lightbox" class='seaocore_icon_add seao_smoothbox' ><?php echo $this->translate('Add Video'); ?></a>
        </div>
     <?php else: ?>
      <div class="seaocore_add">
            
            <a href='<?php echo $url ?>'  class='seaocore_icon_add'><?php echo $this->translate('Add Video'); ?></a>
        </div>
     <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <ul class='videos_manage siteevideo_videos_grid_view mtop10' id='videos_manage'>
            <?php foreach ($this->paginator as $item): ?>
                <li >
                    <div class="sitevideo_thumb_wrapper" style="width:<?php echo $this->videoWidth; ?>px; height:<?php echo $this->videoHeight; ?>px;">
                        <?php $fsDuration = ''; ?>
                        <?php if ($item->duration): ?>

                            <?php $fsDuration .='<span class="video_length">'; ?>
                            <?php
                            if ($item->duration >= 3600) {
                                $duration = gmdate("H:i:s", $item->duration);
                            } else {
                                $duration = gmdate("i:s", $item->duration);
                            }
                            $fsDuration .=$duration;
                            ?>
                            <?php $fsDuration .= "</span>"; ?>
                        <?php endif; ?>
                        <?php $fsContent = ""; ?>
                        <?php if ($item->featured): ?>
                            <?php $fsContent .= '<div class="sitevideo_featured">' . $this->translate('Featured') . '</div>'; ?>
                        <?php endif; ?>
                        <?php if ($item->sponsored): ?>
                            <?php $fsContent .= '<div class="sitevideo_sponsored">' . $this->translate('Sponsored') . '</div>'; ?>
                        <?php endif; ?>

                        <?php $content = "<div class='sitevideo_stats sitevideo_grid_stats'>"; ?>
                        <?php if (in_array('creationDate', $this->videoOption)) : ?>
                            <?php
                            $content .= $this->timestamp(strtotime($item->creation_date));
                            ?>
                        <?php endif; ?>
                        <?php if (in_array('like', $this->videoOption)) : ?>
                            <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                            <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                            <?php
                            $content .= '<span class="sitevideo_bottom_info_likes" title="' . $countText . '">';
                            $content .= $count;
                            $content .= ' </span>';
                            ?>
                        <?php endif; ?>
                        <?php if (in_array('comment', $this->videoOption)) : ?>
                            <?php $count = $this->locale()->toNumber($item->comments()->getCommentCount()); ?>
                            <?php $countText = $this->translate(array('%s comment', '%s comments', $item->comment_count), $count); ?>
                            <?php
                            $content .= ' <span class="sitevideo_bottom_info_comment" title="' . $countText . '">';
                            $content .= $count;
                            $content .= '</span>';
                            ?>
                        <?php endif; ?>
                        <?php $content .= '</div>'; ?>

                        <?php
                        if ($item->photo_id) {
                            echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $fsContent . $fsDuration . $this->itemPhoto($item, $this->gridViewThumbnailType) . $content);
                        } else {
                            echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $fsContent . $fsDuration);
                        }
                        ?>                
                        <div class="sitevideo_info">
                            <div class="sitevideo_bottom_info sitevideo_grid_bott_info">
                                <?php if (in_array('title', $this->videoOption)) : ?>
                                    <h3>
                                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                                    </h3>
                                <?php endif; ?>
                                <div class="sitevideo_grid_bottom_info">
                                    <?php $owner = $item->getOwner(); ?>
                                    <?php
                                   
                                        echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'), array('class' => 'sitevideo_author_logo'));
                                    
                                    ?>
                                    <div class="sitevideo_stats">
                                        <span class="video_views">
                                            <span class="site_video_author_name">
                                                <?php
                                                echo $this->translate('by %s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
                                                ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <?php if (in_array('ratings', $this->videoOption)) : ?>
                                    <div class="sitevideo_ratings">
                                        <?php echo $this->RatingInfo($item, array()); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="sitevideo_desc">
                                <?php echo $this->shareLinks($item, $this->videoOption); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if (empty($this->is_ajax)) : ?>
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
                <?php if ($isUploadAllowed) : ?>
                    <?php echo $this->translate('You have not added any video in your channel. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                <?php else : ?>
                    <?php echo $this->translate('There is no video in this channel yet.'); ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
    <?php if (empty($this->is_ajax)) : ?>
        <script type="text/javascript">
            function viewMorePlaylist(viewFormat)
            {
                $('seaocore_view_more').style.display = 'none';
                $('loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'get',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitevideo/name/channel-view',
                    data: $merge(params.requestParams, {
                        format: 'html',
                        subject: en4.core.subject.guid,
                        page: getNextPage(),
                        is_ajax: 1,
                        loaded_by_ajax: true,
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var videocontainer = $('hideResponse_div').getElement('.videos_manage').innerHTML;
                        $('videos_manage').innerHTML = $('videos_manage').innerHTML + videocontainer;
                        $('loding_image').style.display = 'none';
                        $('hideResponse_div').innerHTML = "";
                    }
                }));
                return false;
            }
        </script>
    <?php endif; ?>

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
    <?php else: ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('seaocore_view_more').style.display = 'none';
            });
        </script>
        <?php
        echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitevideo"), array("orderby" => $this->orderby));
        ?>
    <?php endif; ?>
</div>
<script type="text/javascript">

    var pageAction = function (page) {
        window.location.href = en4.core.baseUrl + 'sitevideo/channel/view/page/' + page + '/channel_id/' + '<?php echo $this->channelId ?>/tab/' + '<?php echo $this->identity ?>';
    }

    function getNextPage() {
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }

    function hideViewMoreLink(showContent) {
        if (showContent == 3) {
            $('seaocore_view_more').style.display = 'none';
            var totalCount = '<?php echo $this->paginator->count(); ?>';
            var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

            function doOnScrollLoadChannel()
            {
                if (typeof ($('seaocore_view_more').offsetParent) != 'undefined') {
                    var elementPostionY = $('seaocore_view_more').offsetTop;
                } else {
                    var elementPostionY = $('seaocore_view_more').y;
                }
                if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                    if ((totalCount != currentPageNumber) && (totalCount != 0))
                        viewMorePlaylist();
                }
            }
            window.onscroll = doOnScrollLoadChannel;

        } else if (showContent == 2) {
            var view_more_content = $('seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                viewMorePlaylist();
            });
        }
    }
</script>
<script type="text/javascript">
    $$('.core_main_video').getParent().addClass('active');
</script>