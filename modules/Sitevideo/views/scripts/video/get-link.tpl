<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-link.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>

<div class="global_form_popup">
    <?php if ($this->subjectType == 'sitevideo_channel'): ?>
        <h3><?php echo $this->translate("Share Channel"); ?></h3>
    <?php else: ?>
        <h3><?php echo $this->translate("Share Video"); ?></h3> 
    <?php endif; ?>
    <div class="mtop10">
        <?php if ($this->subjectType == 'sitevideo_channel'): ?>
            <?php echo $this->translate("You can use this link to share this  video channel with anyone, even if they don't have an account on this website. Anyone with the link will be able to see your  video channel."); ?>
        <?php else: ?>
            <?php echo $this->translate("You can use this link to share this video with anyone, even if they don't have an account on this website. Anyone with the link will be able to see your video."); ?>
        <?php endif; ?>
    </div>
    <div class="mtop10">

        <textarea style="height:65px;width:450px" id="text-box" class="text-box" onclick="select_all();"> <?php echo $this->url; ?> </textarea>
    </div>

    <button  class="fright" onclick="parent.Smoothbox.close();" ><?php echo $this->translate('Okay') ?></button>
    <?php if (empty($this->noSendMessege)): ?>
        <div>
            <a href= "<?php echo $this->url(array('controller' => 'video', 'action' => 'compose', 'subject' => $this->subject->getGuid(),), 'sitevideo_video_general', true); ?>" class="buttonlink fleft sitevideo_icon_message"><?php echo $this->translate('Send in message'); ?></a>
        </div>
    <?php endif; ?>
</div>
<script>
//      window.addEvent('load', function() {
//        select_all();
//      });
    function select_all()
    {
        var text_val = document.getElementById('text-box');
        text_val.select();
    }
</script>