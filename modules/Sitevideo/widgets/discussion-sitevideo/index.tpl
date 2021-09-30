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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitevideo_discussion_sitevideo')
        };
        en4.sitevideo.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php if ($this->showContent): ?>
    <?php if ($this->canPost || $this->paginator->count() > 1): ?>
        <?php $count = $this->paginator->getTotalItemCount(); ?>
        <div class="seaocore_add">
            <?php if ($this->canPost): ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => "sitevideo_topic_extended",
                    'controller' => 'topic',
                    'action' => 'create',
                    // 'subject' => $this->subject()->getGuid(),
                    'channel_id' => $this->sitevideo->channel_id,
                    'content_id' => $this->identity
                        ), $this->translate('Post New Topic'), array(
                    'class' => 'seaocore_icon_add'
                ))
                ?>
            <?php endif; ?>
            <?php if ($this->paginator->count() > 1): ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => "sitevideo_topic_extended",
                    'controller' => 'topic',
                    'action' => 'index',
                    'content_id' => $this->identity,
                    'subject' => $this->subject()->getGuid(),
                        ), $this->translate("View all %s Topics", $count), array(
                    'class' => 'buttonlink icon_viewmore'
                ))
                ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <div class="sitevideo_sitevideos_sitevideo mtop10">
            <ul class="sitevideo_sitevideos">
                <?php
                foreach ($this->paginator as $topic):
                    $lastpost = $topic->getLastPost();
                    $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
                    ?>
                    <li>
                        <div class="sitevideo_sitevideos_replies seaocore_txt_light">
                            <span>
                                <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
                            </span>
                            <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
                        </div>
                        <div class="sitevideo_sitevideos_lastreply">
                            <?php echo $this->htmlLink($lastposter->getHref(array('content_id' => $this->identity)), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
                            <div class="sitevideo_sitevideos_lastreply_info">
                                <?php echo $this->htmlLink($lastpost->getHref(array('content_id' => $this->identity)), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
                                <br />
                                <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitevideo_sitevideos_lastreply_info_date seaocore_txt_light')) ?>
                            </div>
                        </div>
                        <div class="sitevideo_sitevideos_info">
                            <p <?php if ($topic->sticky): ?> class='sitevideo_icon_sticky'<?php endif; ?>>
                                <?php echo $this->htmlLink($topic->getHref(array('content_id' => $this->identity)), $topic->getTitle()) ?>
                            </p>
                            <div class="sitevideo_sitevideos_blurb">
                                <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php
                if ($this->canPost):
                    $show_link = $this->htmlLink(array('route' => "sitevideo_topic_extended", 'controller' => 'topic', 'action' => 'create', 'channel_id' => $this->sitevideo->channel_id, 'content_id' => $this->identity), $this->translate('here'));
                    $show_label = Zend_Registry::get('Zend_Translate')->_('No discussion topics have been posted in this channel yet. Click %1$s to start a discussion.');
                    $show_label = sprintf($show_label, $show_link);
                    echo $show_label;
                endif;
                ?>
            </span>
        </div>
    <?php endif; ?>
<?php endif; ?>