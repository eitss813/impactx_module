<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: utility.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
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
<p>
    <?php echo $this->translate("This page contains utilities to help configure and troubleshoot the video plugin.") ?>
</p>
<br/>

<div class="settings">
    <form onsubmit="return false;">
        <h3><?php echo $this->translate("Ffmpeg Version") ?></h3>
        <?php echo $this->translate("This will display the current installed version of ffmpeg.") ?>
        <br /><br />
        <textarea style="width: 600px;"><?php echo $this->version; ?></textarea>
    </form>
</div>
<br/>
<br/>

<div class="settings">
    <form onsubmit="return false;">
        <h3><?php echo $this->translate("Supported Video Formats") ?></h3>
        <?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this page for more info.') ?>
        <br /><br />
        <textarea style="width: 600px;"><?php echo $this->format; ?></textarea>
    </form>
</div>