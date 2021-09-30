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
            responseContainer: $$('.layout_sitevideo_channel_subscribers')
        }
        en4.sitevideo.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <script type="text/javascript">
        var sitevideoPhotoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
        var paginateSitevideoPhoto = function (page) {
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $$('.layout_sitevideo_channel_subscribers')
            }
            params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
            params.requestParams.page = page;
            en4.sitevideo.ajaxTab.sendReq(params);

        }
    </script>
    <div>
        <?php if (!empty($this->total_subscribers)): ?>
            <ul class="sitevideo_thumbs thumbs_nocaptions">
                <?php foreach ($this->paginator as $subscriber): ?>
                    <li id="thumbs-photo-<?php echo $subscriber->subscription_id ?>" style="width:<?php echo $this->width; ?>px;height:<?php echo $this->height; ?>px" >
                        <?php $owner = $subscriber->getOwner(); ?>
                        <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.profile'), array('class' => 'thumbs_photo', 'title' => $owner->getTitle())); ?>
                        <div class="p5 txt_center"><?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()); ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('There is no subscribers for this channel.'); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($this->paginator->count() > 1): ?>
        <div >
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                <div id="user_group_members_previous" class="paginator_previous">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSitevideoPhoto(sitevideoPhotoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                <div id="user_group_members_next" class="paginator_next">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSitevideoPhoto(sitevideoPhotoPage + 1)', 'class' => 'buttonlink_right icon_next')); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>