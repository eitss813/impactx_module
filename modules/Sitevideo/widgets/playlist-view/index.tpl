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
$this->params['identity'] = $this->identity;
if (!$this->id)
    $this->id = $this->identity;
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<div class="sitevideo_playlist_view_top siteevideo_list_view siteevideo_videos_playlist_view siteevideo_videos_playlist_list_view">

    <div class="sitevideo_thumb_wrapper">
        <?php $video_count = ($this->playlist->video_count) > 1 ? ($this->playlist->video_count . " videos") : ($this->playlist->video_count . " video"); ?>

        <div class="siteevideo_videos_playlist_list_thumb">
            <?php
            $url = $this->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'playall', 'playlist_id' => $this->playlist->playlist_id), 'sitevideo_playlist');
            //CHECKING FOR PLAYLIST THUMNAIL
            if ($this->playlist->file_id) {
                if ($this->playlist->video_count > 0):
                    echo $this->htmlLink($url, "<span class='video_overlay'></span> <span class='play_icon'>Play all</span>" . $this->itemPhoto($this->playlist, 'thumb.main'));
                else :
                    echo $this->htmlLink(null, "<span class='video_overlay'></span>" . $this->itemPhoto($this->playlist, 'thumb.main'));
                endif;
            } else {
                if ($this->playlist->video_count > 0):
                    echo $this->htmlLink($url, "<span class='video_overlay'></span> <span class='play_icon'>Play all</span>");
                else :
                    echo $this->htmlLink(null, "<span class='video_overlay'></span>");
                endif;
            }
            ?>
        </div>
        <div class="sitevideo_info sitevideo_list_video_info">
            <div class="sitevideo_bottom_info">
                <h3><?php echo $this->htmlLink($this->playlist->getHref(), $this->playlist->getTitle()); ?></h3>
                <div class="sitevideo_stats clr">
                    <span class="video_views">

                        <span class="sitevideo_playlist_author_name">
                            <?php echo $this->translate('by %s', $this->htmlLink($this->playlist->getOwner()->getHref(), $this->playlist->getOwner()->getTitle())); ?>
                        </span>
                        <span class="sitelist_pipe">|</span>
                        <span class="sitevideo_playlist_count mright5"><?php echo $video_count; ?></span>
                        <span class="sitevideo_bottom_info_views"><?php echo $this->translate(array('%s view', '%s views', $this->playlist->view_count), $this->locale()->toNumber($this->playlist->view_count)); ?></span>
                        <span class="sitevideo_bottom_info_likes"><?php echo $this->translate(array('%s like', '%s likes', $this->playlist->like_count), $this->locale()->toNumber($this->playlist->like_count)); ?></span>
                    </span>
                </div>
                <div class="sitevideo_list_desc">
                    <?php echo $this->playlist->description; ?>
                </div>
                <div class='sitevideo_playlist_view_options'>
                    <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                        <?php
                        echo $this->htmlLink(array(
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'share',
                            'route' => 'default',
                            'type' => 'sitevideo_playlist',
                            'id' => $this->playlist->getIdentity(),
                            'format' => 'smoothbox'
                                ), $this->translate("Share"), array(
                            'class' => 'sitevideo_playlist_share smoothbox'
                                //'class' => 'buttonlink smoothbox icon_comments'
                        ));
                        ?>
                    <?php endif; ?>
                    <?php if ($this->edit) : ?>
                        <?php
                        echo $this->htmlLink(array(
                            'route' => 'sitevideo_playlist_general',
                            'action' => 'edit',
                            'playlist_id' => $this->playlist->playlist_id
                                ), $this->translate('Edit Playlist'), array(
                            'class' => 'sitevideo_playlist_edit smoothbox'
                        ))
                        ?>
                        <?php
                        echo $this->htmlLink(array(
                            'route' => 'sitevideo_playlist_general',
                            'action' => 'delete',
                            'playlist_id' => $this->playlist->playlist_id), $this->translate('Delete Playlist'), array(
                            'class' => 'sitevideo_playlist_delete smoothbox'
                        ));
                        ?>
                    <?php endif; ?>
                    <?php
                    if ($this->playlist->video_count > 0):
                        echo $this->htmlLink($url, 'Play All', array('class' => 'sitevideo_playlist_play'));
                    endif;
                    ?>
                </div>
            </div>
        </div>

    </div>

</div>
<div class="sitevideo_playlist_view_bottom">
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>

        <ul class='videos_manage siteevideo_list_view' id='videos_manage<?php echo "_" . $this->id; ?>'>
            <?php $c = 1; ?>
            <?php foreach ($this->paginator as $map): ?>
                <?php
                //FIND SITEVIDEO MODEL
                $item = $map->getVideoDetail();
                $url = $this->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'playall', 'playlist_id' => $this->playlist->playlist_id, 'video_id' => $map->video_id), 'sitevideo_playlist');
                ?>
                <li>
                    <div class="sitevideo_thumb_wrapper">
                        <div class="sitevideo_list_thumb">

                            <?php
                            //CHECKING FOR VIDEO THUMBNAIL
                            if ($item->photo_id) {
                                echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
                            } else {
                                echo '<a href=""></a>';
                            }
                            ?>
                            <?php if ($item->duration): ?>
                                <span class="video_length sitevideo_playlist_view_duration">
                                    <?php
                                    if ($item->duration >= 3600) {
                                        $duration = gmdate("H:i:s", $item->duration);
                                    } else {
                                        $duration = gmdate("i:s", $item->duration);
                                    }
                                    echo $duration;
                                    ?>
                                </span>
                            <?php endif; ?>
                            <div class="sitevideo_desc">
                                <?php $this->shareLinks($item, array('watchlater')); ?>
                            </div>
                        </div>
                        <div class="sitevideo_info sitevideo_list_video_info">
                            <div class="sitevideo_bottom_info">
                                <h3>
                                    <?php echo $this->htmlLink($url, $item->getTitle()) ?>
                                </h3>

                                <div class="sitevideo_stats clr">
                                    <span class="video_views">
                                        <span class="sitevideo_playlist_author_name">
                                            <?php echo $this->translate('by %s ', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())); ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="sitevideo_playlist_view_remove">
                                <?php
                                if ($this->edit):
                                    echo $this->htmlLink(array(
                                        'route' => 'default',
                                        'module' => 'sitevideo',
                                        'controller' => 'playlist',
                                        'action' => 'remove-video',
                                        'playlist_map_id' => $map->playlistmap_id,
                                        'format' => 'smoothbox'
                                            ), $this->translate(''), array(
                                        'class' => 'smoothbox',
                                        'title' => $this->translate('Delete Video')
                                    ));
                                endif;
                                ?> 
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

                <?php echo $this->translate('You do not have any videos added in this playlist.'); ?>
            </span>
        </div>
    <?php endif; ?>
    <?php if (empty($this->is_ajax)) : ?>
        <script type="text/javascript">
            function viewMoreVideos()
            {
                $('seaocore_view_more').style.display = 'none';
                $('loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'get',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitevideo/name/playlist-view',
                    data: $merge(params.requestParams, {
                        format: 'html',
                        subject: en4.core.subject.guid,
                        page: getNextPage(),
                        isajax: 1,
                        loaded_by_ajax: true
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var videocontainer = $('hideResponse_div').getElement('#videos_manage<?php echo "_" . $this->id; ?>').innerHTML;
                        $('videos_manage<?php echo "_" . $this->id; ?>').innerHTML = $('videos_manage<?php echo "_" . $this->id; ?>').innerHTML + videocontainer;
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
    <script type="text/javascript">

        var pageAction = function (page) {
            window.location.href = en4.core.baseUrl + 'sitevideo/playlist/view/page/' + page + '/playlist_id/<?php echo $this->playlist->playlist_id; ?>';
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
                            viewMoreVideos();
                    }
                }
                window.onscroll = doOnScrollLoadChannel;

            } else if (showContent == 2) {
                var view_more_content = $('seaocore_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMoreVideos();
                });
            }
        }
    </script>

