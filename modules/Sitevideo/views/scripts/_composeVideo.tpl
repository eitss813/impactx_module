<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _composeVideo.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<?php $channel_id = 0; ?>
<?php if (Engine_Api::_()->core()->hasSubject('sitevideo_channel')): ?>
    <?php $channel_id = Engine_Api::_()->core()->getSubject('sitevideo_channel')->getIdentity();
    ?>
<?php endif; ?>

<script type="text/javascript">

    var openVideosInLightbox = '<?php echo Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox(); ?>';
</script>    

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/composer_video.js');
$allowedSources = array_flip(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.allowed.video', array(1, 2, 3, 4, 5)));
$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
$allowedSources_level = $permissionsTable->getAllowed('video', Engine_Api::_()->user()->getViewer()->level_id, 'source');
$allowedSources_level = array_flip($allowedSources_level);
$allowed = 0;
$user = Engine_Api::_()->user()->getViewer();
$allowed_upload = isset($allowedSources) && isset($allowedSources[4]) && isset($allowedSources_level) && isset($allowedSources_level[4]) && (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'create');
$ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$currentYouTubeApiKey = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
$youtubeEnabled = isset($allowedSources) && isset($allowedSources[1]) && isset($allowedSources_level) && isset($allowedSources_level[1]) && (bool) $currentYouTubeApiKey;

$vimeoEnabled = isset($allowedSources) && isset($allowedSources[2]) && isset($allowedSources_level) && isset($allowedSources_level[2]);
$dailymotionEnabled = isset($allowedSources) && isset($allowedSources[3]) && isset($allowedSources_level) && isset($allowedSources_level[3]);
$embedcodeEnabled = isset($allowedSources) && isset($allowedSources[5]) && isset($allowedSources_level) && isset($allowedSources_level[5]);

if ($allowed_upload && $ffmpeg_path)
    $allowed = 1;
?>
<?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {

            var videoHref = '<?php echo $this->url(array('action' => 'create', 'type' => 'upload'), 'sitevideo_video_general'); ?>';
            var embedVideoHref = '<?php echo $this->url(array('action' => 'create', 'type' => 'embedcode'), 'sitevideo_video_general'); ?>';
            var type = 'wall';
            var requestUrl = en4.core.baseUrl + 'sitevideo/video/compose-upload/format/json/c_type/' + type;
            if ('<?php echo $channel_id; ?>') {
                requestUrl = en4.core.baseUrl + 'sitevideo/video/compose-upload/format/json/c_type/' + type + '/channel_id/<?php echo $channel_id; ?>';
            }

            if (composeInstance.options.type)
                type = composeInstance.options.type;
            composeInstance.addPlugin(new Composer.Plugin.SiteVideo({
                title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
                lang: {
                    'Add Video': '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
                    'Select File': '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
                    'cancel': '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
                    'Attach': '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
                    'Loading...': '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
                    'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
                    'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
                    'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube')) ?>',
                    'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo')) ?>',
                    'To upload a video from your computer, please use our full uploader.': '<?php echo $this->string()->escapeJavascript($this->translate('To upload a video from your computer, please use our <a href="%1$s" class="seao_smoothbox" onclick="showVideoInLightboxInFeed(3);return false;" data-smoothboxseaoclass="seao_add_video_lightbox">full uploader</a>.', $this->url(array('action' => 'create', 'type' => 'upload'), 'sitevideo_video_general')),false) ?>',
                    'To embed your video click here.': '<?php echo $this->string()->escapeJavascript($this->translate('To embed your video <a class="seao_smoothbox item_icon_video" data-SmoothboxSEAOClass="seao_add_video_lightbox" href="%1$s">click here</a>.', $this->url(array('action' => 'create', 'type' => 'embedcode'), 'sitevideo_video_general'))) ?>',
                    'Dailymotion': '<?php echo $this->string()->escapeJavascript($this->translate('Dailymotion')) ?>',
                    'My Device': '<?php echo $this->string()->escapeJavascript($this->translate('My Device')) ?>'
                },
                allowed: <?php echo $allowed; ?>,
                youtubeEnabled: <?php echo (int) $youtubeEnabled ?>,
                vimeoEnabled: <?php echo (int) $vimeoEnabled ?>,
                dailymotionEnabled: <?php echo (int) $dailymotionEnabled ?>,
                embedcodeEnabled: <?php echo (int) $embedcodeEnabled; ?>,
                openVideosInLightboxMode: <?php echo (int) Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox() ?>,
                type: type,
                videoHref: videoHref,
                embedVideoHref: embedVideoHref,
                requestOptions: {
                    'url': requestUrl
                }
            }));
        });
    </script>
<?php else: ?>

    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var videoHref = '<?php echo $this->url(array('action' => 'create', 'type' => 'upload'), 'sitevideo_video_general'); ?>';
            var embedVideoHref = '<?php echo $this->url(array('action' => 'create', 'type' => 'embedcode'), 'sitevideo_video_general'); ?>';
            var type = 'wall';
            var requestUrl = en4.core.baseUrl + 'sitevideo/video/compose-upload/format/json/c_type/' + type;
            if ('<?php echo $channel_id; ?>') {
                requestUrl = en4.core.baseUrl + 'sitevideo/video/compose-upload/format/json/c_type/' + type + '/channel_id/<?php echo $channel_id; ?>';
            }
            if (composeInstance.options.type)
                type = composeInstance.options.type;
            composeInstance.addPlugin(new Composer.Plugin.SiteVideo({
                title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
                lang: {
                    'Add Video': '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
                    'Select File': '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
                    'cancel': '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
                    'Attach': '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
                    'Loading...': '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
                    'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
                    'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
                    'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube')) ?>',
                    'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo')) ?>',
                    'Embed Code': '<?php echo $this->string()->escapeJavascript($this->translate('Embed Code')) ?>',
                    'To upload a video from your computer, please use our full uploader.': '<?php echo $this->string()->escapeJavascript($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create', 'type' => 'upload'), 'sitevideo_video_general')),false) ?>',
                    'Dailymotion': '<?php echo $this->string()->escapeJavascript($this->translate('Dailymotion')) ?>',
                    'My Device': '<?php echo $this->string()->escapeJavascript($this->translate('My Device')) ?>'
                },
                allowed: <?php echo $allowed; ?>,
                youtubeEnabled: <?php echo (int) $youtubeEnabled ?>,
                vimeoEnabled: <?php echo (int) $vimeoEnabled ?>,
                dailymotionEnabled: <?php echo (int) $dailymotionEnabled ?>,
                embedcodeEnabled: <?php echo (int) $embedcodeEnabled; ?>,
                type: type,
                videoHref: videoHref,
                embedVideoHref: embedVideoHref,
                requestOptions: {
                    'url': requestUrl
                }
            }));
        });
    </script>
<?php endif; ?>


<script type="text/javascript">
    function showVideoInLightboxInFeed(type) {
        url = '<?php echo $this->url(array('action' => 'create', 'type' => 'embedcode'), 'sitevideo_video_general'); ?>';
        if (type == 3) {
            url = '<?php echo $this->url(array('action' => 'create', 'type' => 'upload'), 'sitevideo_video_general'); ?>';
        }

        SmoothboxSEAO.open({
            class: "seao_add_video_lightbox",
            request: {
                url: url
            }
        });
    }
</script>
