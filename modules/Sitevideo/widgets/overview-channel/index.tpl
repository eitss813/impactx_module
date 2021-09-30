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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitevideo_overview_channel')
        }
        en4.sitevideo.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>
<?php if ($this->showContent): ?>
    <?php if (!empty($this->overview) && $this->channel->owner_id == $this->viewer_id): ?>
        <div class="seaocore_add">
            <a href='<?php echo $this->url(array('action' => 'overview', 'channel_id' => $this->channel->channel_id), "sitevideo_specific", true) ?>'  class="seaocore_icon_edit buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
        </div>
    <?php endif; ?>
    <div>
        <?php if (!empty($this->overview)): ?>
            <div class="sitevideo_profile_overview">
                <?php echo $this->overview ?>
            </div>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php $url = $this->url(array('action' => 'overview', 'channel_id' => $this->channel->channel_id), "sitevideo_specific", true) ?>
                    <?php echo $this->translate('You have not composed an overview for your channel. %1$sClick here%2$s to compose it from the Dashboard of your channel.', "<a href='$url'>", "</a>"); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listNestedComment.tpl'; ?>