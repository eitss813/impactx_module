<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _grid_view.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$identity = $this->idenity;
if ($this->id) :
    $identity = $this->id;
endif;

if ($this->paginatorGridView) {
    $this->paginator = $this->paginatorGridView;
}
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>

    <ul class='videos_manage siteevideo_videos_grid_view' id='videos_manage<?php echo "_" . $identity; ?>'>
        <?php foreach ($this->paginator as $item): ?>
            <?php
            if (!$item->main_channel_id) {
                $item->main_channel_id = 0;
            }
            ?>
            <li>
                <div class="sitevideo_thumb_wrapper sitevideo_thumb_viewer"  style=" width:<?php echo $this->gridViewWidth; ?>px; height:<?php echo $this->gridViewHeight; ?>px;">
                    <?php $fsDuration = ''; ?>
                    <?php if ($item->duration && in_array('duration', $this->videoOption)): ?>

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
                        <?php $fsContent .= '<div class="sitevideo_featured">' . $this->translate("Featured") . '</div>'; ?>
                    <?php endif; ?>
                    <?php if ($item->sponsored): ?>
                        <?php $fsContent .= '<div class="sitevideo_sponsored"  style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                    <?php endif; ?>

                    <?php $content = "<div class='sitevideo_stats sitevideo_grid_stats'>"; ?>
                    <?php if (in_array('creationDate', $this->videoOption)) : ?>
                        <?php
                        $content .= $this->timestamp(strtotime($item->creation_date));
                        ?>
                    <?php endif; ?>
                    <?php if (in_array('view', $this->videoOption)) : ?>
                        <?php $count = $this->locale()->toNumber($item->view_count); ?>
                        <?php $countText = $this->translate(array('%s view', '%s views', $item->view_count), $count); ?>
                        <?php
                        $content .= '<span class="sitevideo_bottom_info_views" title="' . $countText . '">';
                        $content .= $count;
                        $content .='</span>';
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
                        echo $this->htmlLink($item->getHref(), "<span class='video_overlay'></span> <span class='play_icon'></span>" . $fsContent . $fsDuration . $this->itemBackgroundPhoto($item, $this->gridViewThumbnailType, '', array('tag' => 'i')) . $content, array('class'=> 'sitevideo_thumb'));
                    ?>
                    <div class="sitevideo_info">
                        <div class="sitevideo_bottom_info sitevideo_grid_bott_info">

                            <?php if (in_array('title', $this->videoOption)) : ?>
                                <h3>
                                    <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), $this->titleTruncationGridNVideoView)) ?>
                                </h3>
                            <?php endif; ?>

                            <div class="sitevideo_grid_bottom_info">
                                <?php $title = ""; ?>
                                <?php if (in_array('owner', $this->videoOption)) : ?>
                                    <?php
                                    echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('class' => 'sitevideo_author_logo'));
                                    ?>
                                    <?php $title = $this->translate('by %s', $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 17))); ?>

                                <?php else : ?>
                                
                                <?php
                                $itemTypeValue = $item->parent_type;
                                $videoOwnerLeader = 0;
                                if (strpos($item->parent_type, "sitereview_listing") !== false) {
                                    $contentitem = Engine_Api::_()->getItem('sitereview_listing', $item->parent_id);
                                    $itemTypeValue = $itemTypeValue . $contentitem->listingtype_id;
                                    $videoOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitevideo.video.leader.owner.$itemTypeValue", 1);
                                } elseif ($itemTypeValue && $itemTypeValue != 'user' && Engine_Api::_()->hasItemType($item->parent_type)) {
                                    $contentitem = Engine_Api::_()->getItem($item->parent_type, $item->parent_id);
                                    $videoOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitevideo.video.leader.owner.$itemTypeValue", 1);
                                }
                                if (!$videoOwnerLeader)
                                    $itemTypeValue = 'user';
?>
                                    <?php
                                    $channel = $item->getChannel();
                                    if ($channel) :
                                        ?> 
                                        <?php $title = $this->htmlLink($channel->getHref(), $this->string()->truncate($this->string()->stripTags($channel->getTitle()), 17)); ?>
                                        <?php
                                        if ($channel->file_id) {
                                            echo $this->htmlLink($channel->getHref(), $this->itemPhoto($channel, 'thumb.icon'), array('class' => 'sitevideo_author_logo'));
                                        } else {
                                            echo $this->htmlLink($channel->getHref(), '', array('class' => 'sitevideo_author_logo'));
                                        }
                                        ?>
                                      <?php else:?>
                                      <?php if($itemTypeValue =='user'):?>
                                 <?php $title = $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 17)); ?>
                                      <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('class' => 'sitevideo_author_logo'));?>
                                      <?php else:?>
                                <?php $title = $this->htmlLink($contentitem->getHref(), $this->string()->truncate($this->string()->stripTags($contentitem->getTitle()), 17)); ?>
                                      <?php echo $this->htmlLink($contentitem->getHref(), $this->itemPhoto($contentitem, 'thumb.icon'), array('class' => 'sitevideo_author_logo'));?>
                                      <?php endif;?>
                                
                                    <?php endif; ?>
                                <?php endif; ?>


                                <div class="sitevideo_stats">
                                    <span class="video_views">
                                         <?php if($itemTypeValue =='user'):?>
                                        <span class="site_video_author_name"><?php echo $title ?>    </span>
                                        <?php else:?>
                                        <span class="site_video_author_name"><?php echo $title ?>    </span>
                                        <?php endif;?>
                                        <?php if (in_array('location', $this->videoOption)) : ?>
                                            <?php echo $this->videoInfo($item, array('location')); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if (in_array('rating', $this->videoOption)) : ?>
                                    <div class="sitevideo_ratings">
                                        <?php echo $this->ratingInfo($item, array('widget_id' => $identity . '_b')); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                            $canDelete = Engine_Api::_()->sitevideo()->canDeletePrivacy($item->parent_type, $item->parent_id, $item);

                            $canEdit = Engine_Api::_()->sitevideo()->isEditPrivacy($item->parent_type, $item->parent_id, $item);
                            ?>
                            <?php if (($canEdit || $canDelete) && $this->showEditDeleteOption): ?>
                                <div class='sitevideo_options'>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <ul class="sitevideo_options_dropdown">

                                        <?php if ($canEdit): ?>
                                            <li> 
                                                <?php
                                                echo $this->htmlLink(array(
                                                    'route' => 'sitevideo_video_specific',
                                                    'action' => 'edit',
                                                    'video_id' => $item->video_id,
                                                    'parent_type' => $item->parent_type,
                                                    'parent_id' => $item->parent_id,
                                                        ), $this->translate('Edit Video'), array(
                                                    'class' => 'buttonlink icon_video_edit'
                                                ))
                                                ?> 
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <?php
                                            if ($canDelete && $item->status != 2) {
                                                echo $this->htmlLink(array('route' => 'sitevideo_video_specific', 'action' => 'delete', 'video_id' => $item->video_id, 'format' => 'smoothbox', 'parent_type' => $item->parent_type, 'parent_id' => $item->parent_id,), $this->translate('Delete Video'), array(
                                                    'class' => 'buttonlink smoothbox icon_video_delete'
                                                ));
                                            }
                                            ?>
                                        </li>
                                    </ul>
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
            <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                    <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a  data-SmoothboxSEAOClass="seao_add_video_lightbox" class="seao_smoothbox" href="' . $this->url(array('action' => 'create'), 'sitevideo_video_general', true) . '">', '</a>'); ?>
            <?php else:?>
                <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="' . $this->url(array('action' => 'create'), 'sitevideo_video_general', true) . '">', '</a>'); ?>
             <?php endif;?>
        </span>
    </div>

<?php endif; ?>
<?php if (empty($this->is_ajax) && $this->isViewMoreButton) : ?>
    <script type="text/javascript">
        function viewMorePlaylist(viewFormat)
        {
            $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more').style.display = 'none';
            $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('loding_image').style.display = '';
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>
            };
            en4.core.request.send(new Request.HTML({
                method: 'get',
                'url': en4.core.baseUrl + '<?php echo $this->widgetPath; ?>',
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: getNextPage(),
                    is_ajax: 1,
                    loaded_by_ajax: true,
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('hideResponse_div').innerHTML = responseHTML;
                    var videocontainer = $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('hideResponse_div').getElement('#videos_manage<?php echo "_" . $identity; ?>').innerHTML;
                    $('videos_manage<?php echo "_" . $identity; ?>').innerHTML = $('videos_manage<?php echo "_" . $identity; ?>').innerHTML + videocontainer;
                    $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('loding_image').style.display = 'none';
                    $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('hideResponse_div').innerHTML = "";
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
    <?php else: ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more').style.display = 'none';
            });
        </script>
        <?php
        echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitevideo"), array("orderby" => $this->orderby));
        ?>
    <?php endif; ?>


    <script type="text/javascript">

        var pageAction = function (page) {
            window.location.href = en4.core.baseUrl + 'sitevideo/video/manage/page/' + page;
        }

        function getNextPage() {
            return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }

        function hideViewMoreLink(showContent) {
            if (showContent == 3) {
                $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more').style.display = 'none';
                var totalCount = '<?php echo $this->paginator->count(); ?>';
                var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

                function doOnScrollLoadChannel()
                {
                    if (typeof ($$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more').offsetParent) != 'undefined') {
                        var elementPostionY = $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more').offsetTop;
                    } else {
                        var elementPostionY = $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                        if ((totalCount != currentPageNumber) && (totalCount != 0))
                            viewMorePlaylist();
                    }
                }
                window.onscroll = doOnScrollLoadChannel;

            } else if (showContent == 2) {
                var view_more_content = $$('.layout_sitevideo_videos_sitevideo')[0].getElementById('seaocore_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMorePlaylist();
                });
            }
        }
    </script>
<?php endif; ?>
<script type="text/javascript">
    $$('.core_main_video').getParent().addClass('active');
</script>
<script type="text/javascript">
   en4.core.runonce.add(function () { 
    en4.sitevideolightboxview.attachClickEvent(Array('sitevideo_thumb'));
    });
</script>