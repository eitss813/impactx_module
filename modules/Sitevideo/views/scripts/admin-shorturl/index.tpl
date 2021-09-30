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

<h2><?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php if (count($this->subnavigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?>
    </div>
<?php endif; ?>

<?php $is_element = 1?>
<?php if (empty($is_element)): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('This plugin enables you to set a limit for the number of Likes for a Channel before the simplified short URL is assigned to it. This solves 2 purposes: One, more Likes of a Channel would be indicative of its genuineness, and thus validity of its short URL. Second, a limit on Likes for these URLs to be valid for the respective Channels will motivate the Channel Owners to gather more Likes for their Channels on your site.
If the short URL of any Channel on your site is similar to the URL of a standard plugin channel, then that URL will open that Channel profile and not the standard plugin channel. To avoid such a situation, edit the URL of such a Channel using the “Manage Banned Channel URLs” section.'); ?>
        </span>
    </div>
<?php endif; ?>




<div class='clear sitevideo_channel_settings_form seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>
<?php if (!empty($is_element)): ?>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            showurl("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.change.url', 1) ?>");
            showediturl("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.showurl.column', 1) ?>");
        });

        function showurl(option) {
            if (option == 1) {
                $('sitevideo_channel_likelimit_forurlblock-wrapper').style.display = 'block';
            }
            else {
                $('sitevideo_channel_likelimit_forurlblock-wrapper').style.display = 'none';
            }
        }

        function showediturl(option) {
            if (option == 1) {
                $('sitevideo_channel_edit_url-wrapper').style.display = 'block';
            }
            else {
                $('sitevideo_channel_edit_url-wrapper').style.display = 'none';
            }
        }

    </script>
<?php endif; ?>