<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: video-edit.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitevideo_dashboard_content">
    <?php echo $this->partial('application/modules/Sitevideo/views/scripts/dashboard/header.tpl', array('channel' => $this->channel)); ?>
    <div class="global_form">
        <h3 class="bold"><?php echo $this->translate('Manage Videos'); ?></h3>
        <p><?php echo $this->translate('Manage the videos of your Channel below.'); ?></p>
        <?php
        $videoIds = array();
        $totalCount = $this->paginator->getTotalItemCount();
        ?>
        <ul id="my-channel-videos" >
            <?php if ($totalCount > 0): ?>

                <?php foreach ($this->paginator as $item): ?>
                    <?php $videoIds[] = $item->video_id; ?>
                    <li id="v_<?php echo $item->video_id ?>">
                        <span class="video_length" onclick="videoObj.remove('<?php echo $item->video_id ?>')">x</span>
                        <?php
                        if ($item->photo_id) {
                            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'));
                        } else {
                            echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/video_default.png">';
                        }
                        ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <?php if ($totalCount <= 0): ?>
            <div class="tip" id="tip">
                <span>You do not have any video yet.</span>
            </div>
        <?php endif; ?>
        <?php
        $allowedSources = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.add.videos.options', array(1, 2, 3, 4));
        $option = "";
        $defaultLoad = "";
        if (in_array(1, $allowedSources)) {
            $option .='<option value="uploaded">' . $this->translate("My Uploads") . '</option>';
            if (empty($defaultLoad))
                $defaultLoad = "uploaded";
        }
        if (in_array(2, $allowedSources)) {
            $option .='<option value="favourited">' . $this->translate("My Favourites") . '</option>';
            if (empty($defaultLoad))
                $defaultLoad = "favourited";
        }
        if (in_array(3, $allowedSources)) {
            $option .='<option value="liked">' . $this->translate("My Likes") . '</option>';
            if (empty($defaultLoad))
                $defaultLoad = "liked";
        }
        if (in_array(4, $allowedSources)) {
            $option .='<option value="rated">' . $this->translate("My Rated") . '</option>';
            if (empty($defaultLoad))
                $defaultLoad = "rated";
        }
        ?>
        <?php
        $addVideoOtherChannel = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.addvideo.other.channel', 1);
        $addOtherMemberVideo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.add.othermember.video', 1);
        ?>
        <?php $addMoreOption = !empty($option) && ($addVideoOtherChannel || $addOtherMemberVideo); ?>
        <?php if ($addMoreOption): ?>
            <h3 class="addMoreVideos"><a href="javascript:void(0);" id='addMoreLink' onclick="videoObj.showAddMoreOption()" class="bold">+ Add More Videos</a></h3>
            <div id="addMoreVideos" style="display:none;">
                <h2 class="bold"><?php echo $this->translate("Add more videos to this channel"); ?></h2>
                <p><?php echo $this->translate("Select the videos you wish to add by clicking the thumbnails below."); ?></p>
                <span class="bold seaocore_txt_light mright5"><?php echo $this->translate("Add a few videos to this channel"); ?></span><span class="f_small seaocore_txt_light mright5"><?php echo $this->translate("(optional)"); ?></span>
                <select name="source" id='type' onchange="videoObj.findVideos(this.value)">
                    <?php echo $option; ?>
                </select>
                <div id="videos" class="mtop10">
                </div>
            </div>
        <?php endif; ?>
        <?php if ($totalCount > 0 || $addMoreOption) : ?>
            <?php echo $this->form->render($this) ?>
        <?php endif; ?>
    </div>
</div>

</div>
<script lang="javascript">
    var Video = function ()
    {
        this.videoIds = [];
        this.addMoreVideo = [];
        this.remove = function (videoId)
        {
            vIds = [];
            for (var index = 0; index < this.videoIds.length; index++) {
                if (this.videoIds[index] != videoId)
                    vIds[vIds.length] = this.videoIds[index];
                else
                    $("v_" + videoId).destroy();
            }
            this.videoIds = vIds;
            if (this.addMoreVideo[videoId] != undefined)
            {
                this.addMoreVideo[videoId].inject($("f_" + videoId), 'top');
                $("l_" + videoId).removeClass('fade');
                $("l_" + videoId).addClass('normal');
            }
            else {
                delVideo = $("f_" + videoId);
                if (delVideo)
                {
                    type = $('type').value;
                    videoObj.findVideos(type);
                }
            }
        }
        this.add = function (videoId)
        {
            if ($('tip'))
                $('tip').hide();
            index = this.videoIds.indexOf(videoId);
            if (index > 0)
                return false;
            this.videoIds[this.videoIds.length] = videoId;
            var li = new Element('li', {
                'id': 'v_' + videoId,
            });
            var span = new Element('span', {
                'class': 'video_length',
                'onclick': 'videoObj.remove(' + videoId + ')',
                html: 'x'
            });
            channelVideos = $('my-channel-videos');
            var link = $('l_' + videoId).clone();
            span.inject(li, 'top');
            link.inject(li, 'bottom');
            li.inject(channelVideos, 'bottom');
            this.addMoreVideo[videoId] = $("s_" + videoId).clone(true, true);
            $("s_" + videoId).destroy();
            $("l_" + videoId).addClass('fade');
        }
        this.showAddMoreOption = function ()
        {
            $('addMoreVideos').style.display = 'block';
            $('addMoreLink').style.display = 'none';
            this.findVideos('<?php echo $defaultLoad; ?>');
        }
        this.findVideos = function (type)
        {
            $('videos').innerHTML = "<img src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' /><?php echo $this->translate('Loading ...') ?>";
            url = '<?php echo $this->url(array('action' => 'my-videos', 'channel_id' => $this->channel->channel_id), 'sitevideo_dashboard', true); ?>';
            var request = new Request.HTML({
                url: url,
                data: {
                    format: 'html',
                    existingVideos: JSON.encode(this.videoIds),
                    type: type,
                    'addMyVideo': '<?php echo $addVideoOtherChannel; ?>'
                },
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('videos').innerHTML = responseHTML;
                }
            });
            request.send();
        }
        this.setVideoIds = function ()
        {
            $('video_id').value = this.videoIds.join();
            return true;
        }
    }
    var videoObj = new Video();
<?php if (count($videoIds) > 0) : ?>
        videoObj.videoIds = <?php echo json_encode($videoIds); ?>;
<?php endif; ?>

</script>


