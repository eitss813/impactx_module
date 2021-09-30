<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AvpCategoryLine.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Advanced Videos / Channels / Playlists Plugin'); ?></h2>


<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<p><?php echo "Here, you can create new and big thumbnail images for your already uploaded videos (YouTube and Vimeo's videos) of your site using this Synchronize feature. It is required to display your already uploaded video's thumbnail images in bigger size to fit into different layouts provided in this plugin and showcase your old videos into better layout as well. You can view the video's count that need to be synchronize on your site and can start this synchronize process by just clicking the link given in the message shown below."; ?></p>
<br />
<?php if ($this->videoCount > 0): ?>
    <div class="seaocore_settings_form">
        <div class="tip">
            <?php if ($this->autoSink) : ?>
                <span>
                    <?php echo 'Synchronization process is started. Remaining ' . $this->videoCount . ' videos need to synchronize on your site.<br />To stop synchronization process please <a onclick="stopSinkVideos()" href="javascript:void(0);">click here</a> &nbsp;&nbsp;<img src="application/modules/Seaocore/externals/images/core/loading.gif" ></img>.'; ?>
                </span>
            <?php else : ?>
                <span>
                    <?php echo 'You have ' . $this->videoCount . ' videos for which big thumbnail images need to be created on your site."'; ?>
                    <?php if($this->otherVideoCount>0 || ($this->mycomputerVideoCount>0 && !empty($this->ffmpeg_path)) ) : ?>
                    <?php echo ' Thus, <a onclick="sinkVideos(1)" href="javascript:void(0);">click here</a> to start the synchronization process in order to create big video\'s thumbnails images on your site.' ?>
                    <?php endif; ?>
                </span>

            <?php endif; ?>
            <?php if (empty($this->ffmpeg_path) && $this->mycomputerVideoCount>0) : ?>
                <span style = "color:red;">
                    <?php echo $this->mycomputerVideoCount; ?> videos out of <?php echo $this->videoCount; ?> videos are uploaded from ‘My Computer’ on your site. These videos will not synchronize until FFMPEG is installed and configured on your server. [Note: Go through the steps to install FFMPEG as mentioned in the <a target="_blank" href="admin/sitevideo/settings/faq">FAQs</a> of this plugin.] <br />
                    
                </span>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="seaocore_settings_form">
        <div class="tip">
            <span>
                <?php echo "Your site does not have any video to synchronize."; ?>
            </span>
        </div>
    </div>
<?php endif; ?>
<script>
    var autoSink = <?php echo $this->autoSink; ?>;
    var isCompleted = <?php echo $this->status; ?>;
    function sinkVideos(askConfirmation)
    {
        var isStart = 1;
        if (askConfirmation == 1)
            isStart = confirm("Do you want to start the synchronization process?");
        if (isStart)
        {
            url = '<?php echo $this->url(array('action' => 'synchronize-video', 'start' => 1)); ?>';
            window.location = url;
        }
    }
    if (autoSink == 1)
        sinkVideos(0);
    if (isCompleted == 1)
    {
        alert("Synchronization process is successfully completed.");
        stopSinkVideos();
    }
    else if (isCompleted == 2)
    {
        alert("Synchronization process is successfully completed. FFMPEG should be installed to synchronize remaining <?php echo $this->mycomputerVideoCount; ?> videos that are added from My Computer.");
        stopSinkVideos();
    }
    function stopSinkVideos()
    {
        url = '<?php echo $this->url(array('action' => 'synchronize-video')); ?>';
        window.location = url;
    }
</script>