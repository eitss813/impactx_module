<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _list_view.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$identity = $this->idenity;
if ($this->id) :
    $identity = $this->id;
endif;
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php if ($this->showCreatePlaylistLink) : ?>
    <a href='<?php echo $this->url(array('action' => 'create'), 'sitevideo_playlist_general', true); ?>' class="site_video_create_playlist"><?php echo $this->translate("Create New Playlist"); ?></a>
<?php endif; ?>
<?php if ($this->paginator->getTotalItemCount() > 0) : ?>
    <ul class='videos_manage siteevideo_list_view siteevideo_videos_playlist_view siteevideo_videos_playlist_list_view' id='videos_manage<?php echo "_" . $identity; ?>'>
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="sitevideo_thumb_wrapper">
                    <div class="siteevideo_videos_playlist_list_thumb">
                        <?php $url = $item->getHref(); ?>
                        <?php if ($item->video_count > 0): ?>
                            <?php $url = $this->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'playall', 'playlist_id' => $item->playlist_id), 'sitevideo_playlist'); ?>
                        <?php endif; ?>
                        <a href="<?php echo $url; ?>" >
                            <span class='video_overlay'></span> 
                            <?php if ($item->video_count > 0): ?>
                                <span class='play_icon'>Play All</span>
                            <?php endif; ?>
                            <?php if (in_array('videosCount', $this->playlistOption)) : ?>
                                <div class='playlist_videos_count'>
                                    <span>
                                        <?php echo $this->translate(array('%s video', '%s videos', $item->video_count), $this->locale()->toNumber($item->video_count)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php
                            //CHECKING FOR PLAYLIST THUMBNAIL
                            if ($item->file_id) :
                                ?>
                                <?php echo $this->itemBackgroundPhoto($item, 'thumb.main', $item->getTitle(), array('tag' => 'i')); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="sitevideo_info sitevideo_list_video_info">
                        <div class="sitevideo_bottom_info sitevideo_grid_bott_info">
                            <h3>
                                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                            </h3>
                            <div class="sitevideo_stats">
                                <span class="video_views">
                                    <span class="site_video_author_name">
                                        <?php if (in_array('owner', $this->playlistOption)) : ?>
                                            <?php echo $this->translate("by %s", $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())); ?>
                                        <?php endif; ?>
                                    </span>
                                </span>
                            </div>
                            <div class="sitevideo_playlist_items">
                                <?php
                                //FETCHING THE VIDEOS OF THIS PLAYLIST 
                                $lists = $item->getPlaylistMap(array('limit' => $this->videoShowLinkCount, 'orderby' => $this->playlistVideoOrder));
                                $count = $lists->getTotalItemCount();
                                ?>
                                <?php foreach ($lists as $map): ?>
                                    <?php $video = $map->getVideoDetail(); ?>
                                    <?php if ($video) : ?>
                                        <?php $duration = ""; ?>
                                        <?php if ($video->duration): ?>
                                            <?php
                                            if ($video->duration >= 3600) {
                                                $duration = gmdate("H:i:s", $video->duration);
                                            } else {
                                                $duration = gmdate("i:s", $video->duration);
                                            }
                                            ?>
                                        <?php endif; ?>
                                        <?php $url = $this->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'playall', 'playlist_id' => $item->playlist_id, 'video_id' => $video->video_id), 'sitevideo_playlist'); ?>
                                        <div class="sitevideo_url">
                                            <?php echo $this->htmlLink($url, $video->getTitle()) ?>
                                            <span class="playlist_video_timings"><?php echo " " . $duration; ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </div>
                            <?php if ($count > 0) : ?>
                                <div class="sitevideo_view_playlist">
                                    <?php echo $this->htmlLink($item->getHref(), 'View Full Playlist') ?>
                                </div>
                            <?php endif; ?>
                            <div class="sitevideo_playlist_like">
                                <?php if (in_array('like', $this->playlistOption) && $this->viewer->getIdentity()): ?>  
                                    <?php $this->shareLinks($item, $this->playlistOption, true); ?>
                                <?php endif; ?>
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
            <?php echo $this->message; ?>
            <?php if ($this->viewer->getIdentity()): ?>
                <?php echo $this->translate('Get started by %1$screating%2$s a new playlist.', '<a href="' . $this->url(array('action' => 'create'), 'sitevideo_playlist_general', true) . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>

<?php endif; ?>
<?php if (empty($this->is_ajax)) : ?>
    <script type="text/javascript">
        function viewMorePlaylist()
        {
            $('seaocore_view_more').style.display = 'none';
            $('loding_image').style.display = '';
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
                    loaded_by_ajax: true
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('hideResponse_div').innerHTML = responseHTML;
                    var videocontainer = $('hideResponse_div').getElement('#videos_manage<?php echo "_" . $identity; ?>').innerHTML;
                    $('videos_manage<?php echo "_" . $identity; ?>').innerHTML = $('videos_manage<?php echo "_" . $identity; ?>').innerHTML + videocontainer;
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
        window.location.href = en4.core.baseUrl + 'sitevideo/playlist/manage/page/' + page;
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
