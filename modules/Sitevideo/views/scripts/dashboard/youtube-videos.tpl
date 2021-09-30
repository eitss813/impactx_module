<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: youtube-videos.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitevideo_dashboard_content">
    <?php echo $this->partial('application/modules/Sitevideo/views/scripts/dashboard/header.tpl', array('channel' => $this->channel)); ?>
    <div class="global_form">
        <?php echo $this->form->render($this) ?>
    </div>
    <div class="channel-video-list" >
        <div class="seaocore_view_more" id="loding_image" style="display: none;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
        <div id="chk-select-div" style="display:none;">
            <br />
            <input id="chk-select"  type="checkbox" onclick="en4.sitevideo.youtubeChannel.selectAll(this)"><label>Select All Videos</label>
        </div>
        <ul id="channel_videos">

        </ul>
        <div id="prev_button" style="display:none;" onclick="en4.sitevideo.youtubeChannel.previousPage()"><i class="fa fa-angle-double-left"></i>Prev</div><div id="next_button" style="display:none;" onclick="en4.sitevideo.youtubeChannel.nextPage()">Next<i class="fa fa-angle-double-right"></i></div>
    </div>
    <div id="channel_list_div" style="display:none;" class="popup_element">
        <ul id="channel_list">

        </ul>
        <div id="channel_prev_button" style="display:none;" onclick="en4.sitevideo.youtubeChannel.previousPage()"><i class="fa fa-angle-double-left"></i>Prev</div><div id="channel_next_button" style="display:none;" onclick="en4.sitevideo.youtubeChannel.nextPage()">Next<i class="fa fa-angle-double-right"></i></div>
    </div>
</div>


</div>
<?php
$videoIds = array();
$totalCount = $this->paginator->getTotalItemCount();
?>
<?php if ($totalCount > 0): ?>
    <?php foreach ($this->paginator as $item): ?>
        <?php
        if (!$item->checkType('youtube') || empty($item->code)) {
            continue;
        }
        ?>
        <?php $videoIds[] = $item->code; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php
if (!empty($this->channel->pending_video)) {
    $pVideos = explode(",", $this->channel->pending_video);
    $videoIds = array_merge($videoIds, $pVideos);
}
?>
<script>
    window.addEvent('domready', function () {
        //Create Search Button for Youtube videos
        if ($('youtube_channel_url-element')) {
            var channelurlcontainer = $('youtube_channel_url-element');
            var language = '<?php echo $this->string()->escapeJavascript($this->translate('Get Channel')) ?>';
            var newdiv = document.createElement('div');
            newdiv.id = 'youtube_channel_seach_div';
            newdiv.innerHTML = "<a href='javascript:void(0);'  name='youtube_channel_seach' id='youtube_channel_seach' onclick='en4.sitevideo.youtubeChannel.searchVideo();return false;' class='check_availability_button'>" + language + "</a> <br />";
            channelurlcontainer.insertBefore(newdiv, channelurlcontainer.childNodes[2]);
        }
        //Create Search Button for Youtube channels
        if ($('youtube_channel_keyword-element')) {
            var channelurlcontainer = $('youtube_channel_keyword-element');
            var language = '<?php echo $this->string()->escapeJavascript($this->translate('Find Channel')) ?>';
            var newdiv = document.createElement('div');
            newdiv.id = 'youtube_channel_keyword_seach_div';
            newdiv.innerHTML = "<a href='javascript:void(0);'  name='youtube_channel_seach' id='youtube_channel_keyword_seach' onclick='en4.sitevideo.youtubeChannel.searchChannel();return false;' class='check_availability_button'>" + language + "</a> <br />";
            channelurlcontainer.insertBefore(newdiv, channelurlcontainer.childNodes[2]);
        }
        en4.sitevideo.youtubeChannel.checkChannelUrl = '<?php echo $this->url(array('action' => 'channel-exists'), 'sitevideo_general', true) ?>';
        en4.sitevideo.youtubeChannel.id = '<?php echo $this->channel->channel_id; ?>';
        en4.sitevideo.youtubeChannel.api_key = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey'); ?>';
        en4.sitevideo.youtubeChannel.videos = <?php echo json_encode($videoIds); ?>;
        if ($('youtube_channel_url').value != '') {
            en4.sitevideo.youtubeChannel.searchVideo();
        }
    });
</script>
