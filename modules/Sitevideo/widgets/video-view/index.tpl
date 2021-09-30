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
<style>
    div.sitevideo_video_view_top .video_embed .vimeo_iframe_big, .video_embed iframe, .video_embed > span  {
        <?php if (!in_array($this->video->type, array('instagram','twitter', 'stream'))): ?>
            height: <?php echo $this->height; ?>px !important;
        <?php endif; ?>
        width: <?php echo ($this->width > 0) ? $this->width . "px" : '100%' ?> !important;
    }
</style>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $this->headLink()->prependStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php $this->headLink()->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>

<?php
if (!$this->video || $this->video->status != 1):
    echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
    return; // Do no render the rest of the script in this mode
endif;
?>

<?php
$itemTypeValue = $this->video->parent_type;
$videoOwnerLeader = 0;
if (strpos($this->video->parent_type, "sitereview_listing") !== false) {
    $contentitem = Engine_Api::_()->getItem('sitereview_listing', $this->video->parent_id);
    $itemTypeValue = $itemTypeValue . $contentitem->listingtype_id;
    $videoOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitevideo.video.leader.owner.$itemTypeValue", 1);
} elseif ($itemTypeValue && $itemTypeValue != 'user' && Engine_Api::_()->hasItemType($this->video->parent_type)) {
    $contentitem = Engine_Api::_()->getItem($this->video->parent_type, $this->video->parent_id);
    $videoOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitevideo.video.leader.owner.$itemTypeValue", 1);
}
if (!$videoOwnerLeader)
    $itemTypeValue = 'user';
?>

<?php
if ($this->video->checkType('upload') && $this->video_extension == 'mp4')
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');
?>
<?php
if ($this->video->checkType('upload') && $this->video_extension == 'flv'):

    $flowplayerJS = !Engine_Api::_()->sitevideo()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version, '4.8.10') ? 'flashembed-1.0.1.pack.js' : 'flowplayer-3.2.13.min.js';

    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/' . $flowplayerJS);
    ?>
    <?php
    //GET CORE VERSION
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

    $flowplayerSwf = !Engine_Api::_()->sitevideo()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version, '4.8.10') ? 'flowplayer-3.1.5.swf' : 'flowplayer-3.2.18.swf';
    ?>
    <script type='text/javascript'>

        en4.core.runonce.add(function () {

            flashembed("video_embed", {
                src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/<?php echo $flowplayerSwf; ?>",
                            width: 1170,
                            height: 658,
                            wmode: 'transparent'
                        }, {
                            config: {
                                clip: {
                                    url: "<?php echo $this->video_location; ?>",
                                    autoPlay: false,
                                    duration: "<?php echo $this->video->duration ?>",
                                    autoBuffering: true
                                },
                                plugins: {
                                    controls: {
                                        background: '#000000',
                                        bufferColor: '#333333',
                                        progressColor: '#444444',
                                        buttonColor: '#444444',
                                        buttonOverColor: '#666666'
                                    }
                                },
                                canvas: {
                                    backgroundColor: '#000000'
                                }
                            }
                        });
                    });

    </script>
<?php endif ?>
<?php $videoOwner = $this->video->getOwner(); ?>
<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class="video_view video_view_container sitevideo_video_view_top">
    <?php if ($this->video->checkType('upload')): ?>
        <div id="video_embed" class="video_embed">
            <?php if ($this->video_extension !== 'flv'): ?>
                <!-- Check if auto  play of video is enabled of not. -->
                <?php $coreSettings = Engine_Api::_()->getApi('settings', 'core');
                      $getSitevideoAutoplayMycomputerVideo = $coreSettings->getSetting('sitevideo.autoplay.mycomputer.video', 1)
                 ?>
                <video id="video" controls <?php echo !empty( $getSitevideoAutoplayMycomputerVideo ) ? "autoplay" : ""  ?> preload="auto" width="1170" height="658">
                    <source type='video/mp4;' src="<?php echo $this->video_location ?>">
                </video>
            <?php endif ?>
        </div>
    <?php elseif ($this->video->checkType('stream')): ?>
        <div id="video_embed" class="video_embed">
          <?php if(Engine_Api::_()->sitevideo()->enableStreamVideo() && $this->video_location && in_array($this->video_extension, array('m3u8', 'mpd'))): ?>
            <!-- Check if auto  play of video is enabled of not. -->
            <?php $coreSettings = Engine_Api::_()->getApi('settings', 'core');
                  $getSitevideoAutoplayMycomputerVideo = $coreSettings->getSetting('sitevideo.autoplay.mycomputer.video', 1)
             ?>
                <?php echo $this->partial('_player.tpl', 'sitevod', array('type' => ($this->video_extension == 'm3u8' ? 'hls' : 'dash'), 'streamId' => 'sitevideo_view_' . $this->video->getGuid(), 'src' => $this->video_location, 'poster' => $this->video->getPhotoUrl(), 'autoPlay' => !empty($getSitevideoAutoplayMycomputerVideo))); ?>
            <?php else:?>
            <div class="tip">
              <span>
                <?php  echo $this->translate('The video you are looking for does not exist or has not been processed yet.'); ?>
              </span>
            </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="video_embed">
            <?php echo $this->videoEmbedded ?>
        </div>
    <?php endif; ?>
    <div class="sitevideo_video_view_info">

        <div class="sitevideo_view_title">
            <?php if (in_array('title', $this->viewOptions)) : ?>
                <h3><?php echo $this->htmlLink($this->video->getHref(), $this->video->getTitle()); ?></h3>
            <?php endif; ?>
            <div class="sitevideo_view_rating">
                <?php if (in_array('ratings', $this->viewOptions) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1)) : ?>
                    <?php echo $this->content()->renderWidget("sitevideo.user-ratings"); ?>
                <?php endif; ?>
            </div>
            <?php if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && in_array('lightbox', $this->viewOptions) && (!Engine_API::_()->seaocore()->isMobile() && !Engine_API::_()->seaocore()->isTabletDevice())) : ?>
                <div class="sitevideo_view_open_in_lightbox">
                    <span>
                        <a href="<?php echo $this->video->getHref(); ?>" class="item_photo_video"><?php echo $this->translate('Open in lightbox') ?></a>    
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <div class="sitevideo_view_author_line">
            <div class="sitevideo_view_author_left">


                <?php
                if ($this->video->main_channel_id) :
                    $channel = $this->video->getChannelModel();
                    ?>
                    <?php
                    if ($channel->file_id) {
                        echo $this->htmlLink($channel->getHref(), $this->itemPhoto($channel, 'thumb.icon'), array('class' => 'sitevideo_author_image'));
                    } else {
                        echo $this->htmlLink($channel->getHref(), $this->itemPhoto($channel, 'thumb.icon'), array('class' => 'sitevideo_author_image'));
                    }
                    ?>
                    <?php echo $this->htmlLink($channel, $channel->getTitle(), array('class' => 'sitevideo_author_title')); ?>
                <?php else : ?>

                    <?php if ($itemTypeValue == 'user') : ?>

                        <?php echo $this->htmlLink($this->video->getOwner()->getHref(), $this->itemPhoto($this->video->getOwner(), 'thumb.icon'), array('class' => 'sitevideo_author_image')); ?>

                        <?php echo $this->htmlLink($this->video->getOwner(), $this->video->getOwner()->getTitle(), array('class' => 'sitevideo_author_title')); ?>
                    <?php else: ?>
                        <?php $videoOwner = $contentitem; ?>

                        <?php echo $this->htmlLink($videoOwner->getHref(), $this->itemPhoto($videoOwner, 'thumb.icon'), array('class' => 'sitevideo_author_image')); ?>

                        <?php echo $this->htmlLink($videoOwner->getHref(), $videoOwner->getTitle(), array('class' => 'sitevideo_author_title')); ?>

                    <?php endif; ?>

                <?php endif; ?>



                <div class="sitevideo_subscribe_container">
                    <?php if (in_array('subscribe', $this->viewOptions) && Engine_Api::_()->user()->getViewer()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1)) : ?>
                        <?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/subscription/_subscribeChannel.tpl'; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sitevideo_view_author_right">
                <?php if (in_array('view', $this->viewOptions)) : ?>
                    <?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class='sitevideo_view_options'>
            <div class="sitevideo_view_addlist">
                <?php if (in_array('playlist', $this->viewOptions) && Engine_Api::_()->user()->getViewer()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) : ?>
                    <?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/playlist/_addToPlaylist.tpl'; ?>
                <?php endif; ?>
            </div>
            <div class="sitevideo_view_links">

                <?php if (in_array('share', $this->viewOptions)) : ?>
                    <?php $this->subject = $this->video; ?>
                    <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareVideosButtons.tpl'; ?>
                <?php endif; ?>

                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                    <?php if ($this->canComment && in_array('comment', $this->viewOptions)): ?>    
                        <a href="javascript:void(0);" onclick="focusCommentBox()" class="sitevideo_view_comment"><?php echo $this->translate("Comment"); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (in_array('watchlater', $this->viewOptions) && Engine_Api::_()->user()->getViewer()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) : ?>
                    <?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/watchlater/_watchlater.tpl'; ?>
                <?php endif; ?>
                <?php if (in_array('favourite', $this->viewOptions) && Engine_Api::_()->user()->getViewer()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) : ?>
                    <?php echo $this->shareLinks($this->video, array('favourite', 'hideShareDiv'), true) ?>
                <?php endif; ?>
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity() && $this->canDownload && in_array('download', $this->viewOptions) && strlen($this->video_location) > 0 && $this->video->checkType('upload') && $this->video->status == 1) : ?>
                    <a  href="<?php echo $this->video_location; ?>" class="seao_icon_download" title="<?php echo $this->translate("Download video"); ?>">Download Video</a>
                <?php endif; ?>
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                    <span class="sitevideo_view_more sitevideo_options">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <div class="sitevideo_more_drop_down sitevideo_options_dropdown">
                            <?php if (in_array('report', $this->viewOptions)) : ?>
                                <?php
                                echo $this->htmlLink(array(
                                    'module' => 'core',
                                    'controller' => 'report',
                                    'action' => 'create',
                                    'route' => 'default',
                                    'subject' => $this->video->getGuid(),
                                    'format' => 'smoothbox'
                                        ), $this->translate("Report"), array(
                                    'class' => 'smoothbox sitevideo_view_report'
                                ));
                                ?>
                            <?php endif; ?>

                            <?php if ($this->canEdit): ?>
                                <?php
                                echo $this->htmlLink(array(
                                    'route' => 'sitevideo_video_specific',
                                    'action' => 'edit',
                                    'video_id' => $this->video->video_id,
                                    'parent_type' => $this->video->parent_type,
                                    'parent_id' => $this->video->parent_id,
                                        ), $this->translate('Edit Video'), array(
                                    'class' => 'sitevideo_view_edit'
                                ))
                                ?>
                            <?php endif; ?>
                            <?php if ($this->canDelete && $this->video->status != 2): ?>
                                <?php
                                echo $this->htmlLink(array(
                                    'route' => 'sitevideo_video_specific',
                                    'action' => 'delete',
                                    'video_id' => $this->video->video_id,
                                    'format' => 'smoothbox',
                                    'parent_type' => $this->video->parent_type,
                                    'parent_id' => $this->video->parent_id,
                                        ), $this->translate('Delete Video'), array(
                                    'class' => 'smoothbox sitevideo_view_delete'
                                ))
                                ?>
                                <?php if ($this->can_embed): ?>
                                    <?php
                                    echo $this->htmlLink(array(
                                        'module' => 'sitevideo',
                                        'controller' => 'video',
                                        'action' => 'embed',
                                        'route' => 'default',
                                        'video_id' => $this->video->getIdentity(),
                                        'format' => 'smoothbox'
                                            ), $this->translate("Embed"), array(
                                        'class' => 'smoothbox sitevideo_view_embeded'
                                    ));
                                    ?>
                                <?php endif ?>
                                <?php
                                if ($this->allow_location) {
                                    echo $this->htmlLink(array(
                                        'module' => 'sitevideo',
                                        'controller' => 'video',
                                        'action' => 'edit-location',
                                        'route' => 'default',
                                        'subject' => $this->video->getGuid(),
                                        'format' => 'smoothbox'
                                            ), $this->translate("Edit Location"), array(
                                        'class' => 'smoothbox seao_icon_location'
                                    ));
                                }
                                ?>
                            <?php endif ?>

                        </div>
                    </span>
                <?php else : ?>

                    <?php if (in_array('report', $this->viewOptions)) : ?>
                        <?php
                        echo $this->htmlLink(array(
                            'module' => 'core',
                            'controller' => 'report',
                            'action' => 'create',
                            'route' => 'default',
                            'subject' => $this->video->getGuid(),
                            'format' => 'smoothbox'
                                ), $this->translate("Report"), array(
                            'class' => 'smoothbox sitevideo_view_report'
                        ));
                        ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if (Engine_Api::_()->seaocore()->checkEnabledNestedComment('video', array('moduleName' => 'sitevideo'))) : ?>

                <?php $getParams = Engine_Api::_()->sitevideo()->getWidgetParams('nestedcomment.comments', 'sitevideo_video_view');
                ?>
                <?php if (!$getParams->showAsLike && $getParams->showAsNested): ?>
                    <?php $dislikes = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikePaginator($this->video);
                    ?>
                    <div class="sitevideo_view_options_right">
                        <?php if (in_array('like', $this->viewOptions)): ?>
                            <a class="sitevideo_view_like smoothbox" href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $this->video->getType(), 'resource_id' => $this->video->getIdentity(), 'call_status' => 'public'), 'default', true); ?>"><?php echo $this->locale()->toNumber($this->video->like_count); ?></a>
                        <?php endif; ?>
                        <?php if (in_array('dislike', $this->viewOptions)): ?>
                            <?php if ($getParams->showDislikeUsers): ?>
                                <a class="sitevideo_view_unlike smoothbox" href="<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $this->video->getType(), 'resource_id' => $this->video->getIdentity(), 'call_status' => 'public'), 'default', true); ?>"><?php echo $this->locale()->toNumber($dislikes->getTotalItemCount()); ?></a>
                            <?php else: ?>
                                <a class="sitevideo_view_unlike"><?php echo $this->locale()->toNumber($dislikes->getTotalItemCount()); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php if (in_array('like', $this->viewOptions)): ?>
                        <div class="sitevideo_view_options_right">
                            <a class="sitevideo_view_like smoothbox" href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $this->video->getType(), 'resource_id' => $this->video->getIdentity(), 'call_status' => 'public'), 'default', true); ?>"><?php echo $this->locale()->toNumber($this->video->like_count); ?></a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <?php if (in_array('like', $this->viewOptions)): ?>
                    <div class="sitevideo_view_options_right">
                        <a class="sitevideo_view_like smoothbox" href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $this->video->getType(), 'resource_id' => $this->video->getIdentity(), 'call_status' => 'public'), 'default', true); ?>"><?php echo $this->locale()->toNumber($this->video->like_count); ?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div id="message" class="sitevideo_view_msg_tip" style="display:none;"></div>
    </div>
</div>
<div class="sitevideo_view_bottom_content sitevideo_video_view_info">
    <div class='sitevideo_view_publish'>
        <?php echo $this->translate("Posted On %s", $this->timestamp($this->video->creation_date)) ?>
    </div>
    <div class="sitevideo_view_hashtags">
        <?php if (in_array('hashtags', $this->viewOptions) && count($this->videoTags) > 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1)): ?>
            <?php foreach ($this->videoTags as $tag): ?>

                <a href='<?php echo $this->url(array('action' => 'browse'), "sitevideo_video_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>&nbsp;

            <?php endforeach; ?>
        <?php endif; ?> 
    </div>  
    <div id="videoOtherInfo" style="display:none;">
        <div class="sitevideo_view_desc">
            <?php echo $this->video->description; ?>
        </div>
        <div class="sitevideo_view_categories">
            <?php if ($this->category && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) : ?>
                <span> <?php echo $this->translate('Category'); ?> </span> : 
                <?php
                $url = $this->url(array('category_id' => $this->category->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_video_category', $this->category->category_id)->getCategorySlug()), "sitevideo_video_general_category");
                echo $this->htmlLink($url, $this->translate($this->category->category_name), array('class' => 'sitevideo_video_category')
                );
                ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($this->show_fields)) : ?>
            <h4>
                <span><?php echo $this->translate('Profile Information'); ?></span>
            </h4>
            <?php echo $this->show_fields; ?>
        <?php endif; ?>
    </div>
    <div class="sitevideo_video_view_showmore">
        <a href="javascript:void(0);" id="slink" onclick="showHideOtherInfo()">Show More</a>
    </div>
</div>
<script type="text/javascript">
    $$('.core_main_video').getParent().addClass('active');
    function showHideOtherInfo()
    {
        $('videoOtherInfo').toggle();
        if ($('videoOtherInfo').getStyle('display') == 'none')
            $("slink").innerHTML = 'Show More';
        else
            $("slink").innerHTML = 'Show Less';
    }
    function focusCommentBox()
    {
        if ($('comment-form'))
        {
            $('comment-form').style.display = '';
            $('comment-form').body.focus();
        } else
        {
            $$('div.compose-content').each(function (el, index) {
                if (index == 0)
                {
                    el.set('tabindex', '0');
                    el.focus();
                }
            });
        }
    }

    en4.core.runonce.add(function () {
        showShareVideoLinks();
    });

    function showShareVideoLinks(val) {
        $(document.body).addEvent('click', function () {
            showHideToggleVideoShareLinks();
        });
        $$('.siteevent_share_links_toggle').addEvent('click', function (event) {
            event.stop();
            //showHideToggleShareLinks();
            $(this).getParent('.siteevent_grid_footer').getElement('.siteevent_share_links').toggle();

        });
    }

    function showHideToggleVideoShareLinks() {
        //$$('.siteevent_share_links_toggle').show();
        $$('.siteevent_share_links_toggle').getParent('.siteevent_grid_footer').getElement('.siteevent_share_links').hide();
    }
</script>
