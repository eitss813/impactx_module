<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: channel-settings.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
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
        showDefaultNetwork('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.network', 0) ?>');
    });

    function showDefaultNetwork(option) {
        if ($('sitevideo_default_show-wrapper')) {
            if (option == 0) {
                $('sitevideo_default_show-wrapper').style.display = 'block';
                showDefaultNetworkType($('sitevideo_default_show-1').checked);
                // $('sitevideo_privacybase-wrapper').style.display = 'block';
                $('sitevideo_networkprofile_privacy-wrapper').style.display = 'none';
            } else {
                showDefaultNetworkType(1);
                $('sitevideo_default_show-wrapper').style.display = 'none';
                //   $('sitevideo_privacybase-wrapper').style.display = 'none';
                $('sitevideo_networkprofile_privacy-wrapper').style.display = 'block';
            }
        }
    }

    function showDefaultNetworkType(option) {
        if ($('sitevideo_networks_type-wrapper')) {
            if (option == 1) {
                $('sitevideo_networks_type-wrapper').style.display = 'block';
            } else {
                $('sitevideo_networks_type-wrapper').style.display = 'none';
            }
        }
    }
    function addVideoOption()
    {
        $('sitevideo_add_videos_options-wrapper').style.display = 'block';
        if ($('sitevideo_channel_addvideo_other_channel-1').checked && $('sitevideo_channel_add_othermember_video-1').checked)
        {
            $('sitevideo_add_videos_options-4').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-1').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-2').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-3').getParent().setStyle('display', 'block');
        }
        else if ($('sitevideo_channel_addvideo_other_channel-1').checked && !($('sitevideo_channel_add_othermember_video-1').checked))
        {
           
            $('sitevideo_add_videos_options-1').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-2').getParent().setStyle('display', 'none');
            $('sitevideo_add_videos_options-3').getParent().setStyle('display', 'none');
            $('sitevideo_add_videos_options-4').getParent().setStyle('display', 'none');
            $('sitevideo_add_videos_options-2').set('checked',false);
            $('sitevideo_add_videos_options-3').set('checked',false);
            $('sitevideo_add_videos_options-4').set('checked',false);
        }
        else if (!($('sitevideo_channel_addvideo_other_channel-1').checked) && $('sitevideo_channel_add_othermember_video-1').checked)
        {
            
            $('sitevideo_add_videos_options-1').getParent().setStyle('display', 'none');
            $('sitevideo_add_videos_options-2').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-3').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-4').getParent().setStyle('display', 'block');
            $('sitevideo_add_videos_options-1').set('checked',false);
        }
        else
        {
            $('sitevideo_add_videos_options-wrapper').style.display = 'none';
        }
    }
    addVideoOption();
</script>