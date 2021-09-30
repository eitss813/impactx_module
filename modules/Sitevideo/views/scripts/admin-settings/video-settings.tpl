<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: video-settings.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
?>
<h2>
    <?php echo $this->translate('Advanced Videos / Channels / Playlists Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>
<div class="clear seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<script type="text/javascript">
    window.addEvent('domready', function () {
        showProximitySearchSetting('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location',0) ?>');
    });
    function showProximitySearchSetting(options) {
        if (options == 0) {
            if ($('sitevideo_video_proximity_search_kilometer-wrapper'))
                $('sitevideo_video_proximity_search_kilometer-wrapper').style.display = 'none';
        }
        else {
            if ($('sitevideo_video_proximity_search_kilometer-wrapper'))
                $('sitevideo_video_proximity_search_kilometer-wrapper').style.display = 'block';
        }
    }
    
</script>

<script type="text/javascript">

    window.addEvent('domready', function () {
        showDefaultNetwork('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0) ?>');
    });

    function showDefaultNetwork(option) {
        if ($('sitevideo_video_default_show-wrapper')) {
            if (option == 0) {
                $('sitevideo_video_default_show-wrapper').style.display = 'block';
                showDefaultNetworkType($('sitevideo_video_default_show-1').checked);
                $('sitevideo_video_networkprofile_privacy-wrapper').style.display = 'none';
            } else {
                showDefaultNetworkType(1);
                $('sitevideo_video_default_show-wrapper').style.display = 'none';
                $('sitevideo_video_networkprofile_privacy-wrapper').style.display = 'block';
            }
        }
    }

    function showDefaultNetworkType(option) {
        if ($('sitevideo_video_networks_type-wrapper')) {
            if (option == 1) {
                $('sitevideo_video_networks_type-wrapper').style.display = 'block';
            } else {
                $('sitevideo_video_networks_type-wrapper').style.display = 'none';
            }
        }
    }

</script>