<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (empty($this->error)): ?>
    <?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
    ?>
    <div class='sitevideo_badge_right'>
        <iframe scrolling="no" frameborder="0" id="badge_video_iframe" src="" style="overflow: auto; width: 300px; height: 800;" allowTransparency="true" >
        <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/loader.gif' /> </center>
        </iframe>
    </div>
    <div class='sitevideo_badge_left'>
        <?php echo $this->form->render($this) ?>
    </div>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            previewBadge(1);
        });
        function previewBadge(option) {
            var lodingImage = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/loader.gif" /></center>';
            var oneVideoHight = 96;
            var oneVideoWidth = 116;

            var width = escape($("width").value);
            var no_of_image = escape($("no_of_image").value);

            var inOneRow = (width / oneVideoWidth);
            if (oneVideoWidth > width) {
                inOneRow = 1;
            } else {
                inOneRow = parseInt(inOneRow);
            }
            width = parseInt(width) + 8;
            var no_of_row = Math.ceil((parseInt(no_of_image) + 1) / inOneRow);
            var height = parseInt(oneVideoHight * no_of_row) + 28;
            var background_color = escape($('background_color').value);
            var border_color = escape($('border_color').value);
            var text_color = escape($('text_color').value);
            var link_color = escape($('link_color').value);
            var type = "channel";
            var owner = escape($("owner").value);
            var id = '<?php echo $this->channel_id; ?>';
            var url_content = "?type=" + type + "&id=" + id + "&width=" + width + "&height=" + height + "&owner=" + owner + "&no_of_image=" + no_of_image + "&background_color=" + background_color + "&border_color=" + border_color + "&text_color=" + text_color + "&link_color=" + link_color;
            if (option == 1) {
                var srcUrl = "<?php echo $this->url(array('action' => 'index'), 'sitevideo_badge', true) ?>" + url_content;
                $('badge_video_iframe').style.width = width + "px";
                $('badge_video_iframe').style.height = height + "px";
                $('badge_video_iframe').contentWindow.document.body.innerHTML = lodingImage;

                $('badge_video_iframe').src = srcUrl;
            } else {
                var srcUrl = "<?php echo $this->url(array('action' => 'get-source'), 'sitevideo_badge', true) ?>" + url_content;

                Smoothbox.open(srcUrl);
            }
        }
    </script>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("You have not added any videos yet."); ?>
            <?php if ($this->canCreate): ?>
                <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                    <?php echo $this->translate('%1$sAdd videos%2$s now!', '<a data-smoothboxseaoclass="seao_add_video_lightbox" class="seao_smoothbox" href="' . $this->url(array('action' => 'create'), 'sitevideo_video_general', true) . '">', '</a>'); ?>
                <?php else: ?>
                    <?php echo $this->translate('%1$sAdd videos%2$s now!', '<a href="' . $this->url(array('action' => 'create'), 'sitevideo_video_general', true) . '">', '</a>'); ?>
                <?php endif; ?>
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>
<style type="text/css">
    /*Create Video Badge Page*/
    .sitevideo_badge_right{
        float:right;
        width:56%;
        margin-left:10px;
        overflow:auto;
    }
    .sitevideo_badge_left{
        overflow:hidden;
        width:42%;
    }
    #badge_create div.form-label{
        width:auto;
        margin-bottom:3px;
    }
    #badge_create div.form-element{
        clear:both;
        margin-bottom:10px;
    }
    #badge_create > div > div > h3 + p,
    #badge_create div > p{
        max-width:350px;
        margin-bottom:3px;
        margin-top:0px;
    }
</style>