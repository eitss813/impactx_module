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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<div class="sitevideo_view_top">
    <?php echo $this->htmlLink($this->sitevideo->getHref(), $this->itemPhoto($this->sitevideo, 'thumb.icon', '', array('align' => 'left'))) ?>
    <p>
        <?php echo $this->sitevideo->__toString() ?>
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->sitevideo->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->topic->getTitle() ?>
    </p>
</div>

<!--FACEBOOK LIKE BUTTON START HERE-->
<?php
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
if (!empty($fbmodule)) :
    $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
    if (!empty($enable_facebookse) && !empty($fbmodule->version)) :
        $fbversion = $fbmodule->version;
        if (!empty($fbversion) && ($fbversion >= '4.1.5')) {
            ?>
            <div class="mbot10">
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
            </div>

        <?php } ?>
    <?php endif; ?>
<?php endif; ?>

<div class="sitevideo_topic_view mtop10">

    <?php $this->placeholder('sitevideotopicnavi')->captureStart(); ?>
    <div class="sitevideo_discussion_thread_options">
        <?php
        echo $this->htmlLink(array('route' => 'sitevideo_topic_extended', 'controller' => 'topic', 'action' => 'index', 'channel_id' => $this->channel->getIdentity(), 'tab' => $this->tab_selected_id), $this->translate('Back to Topics'), array(
            'class' => 'sitevideo_icon_back'
        ))
        ?>
        <?php if (($this->canPost) && (!$this->topic->closed)): ?>
            <?php
            echo $this->htmlLink($this->url(array()) . '#reply', $this->translate('Post Reply'), array(
                'class' => 'sitevideo_icon_postreply'
            ))
            ?>
        <?php endif; ?>
        <?php if ($this->viewer->getIdentity()): ?>
            <?php if (!$this->isWatching): ?>
                <?php
                echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
                    'class' => 'sitevideo_icon_watch'
                ))
                ?>
            <?php else: ?>
                <?php
                echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
                    'class' => 'sitevideo_icon_unwatch'
                ))
                ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->canEdit || $this->topic->user_id == $this->viewer()->getIdentity()): ?>
            <?php if (!$this->topic->sticky): ?>
                <?php
                echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
                    'class' => 'sitevideo_icon_sticky'
                ))
                ?>
            <?php else: ?>
                <?php
                echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
                    'class' => 'sitevideo_icon_remove'
                ))
                ?>
            <?php endif; ?>
            <?php if (!$this->topic->closed): ?>
                <?php
                echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
                    'class' => 'sitevideo_icon_closelock'
                ))
                ?>
            <?php else: ?>
                <?php
                echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
                    'class' => 'sitevideo_icon_openlock'
                ))
                ?>
            <?php endif; ?>
            <?php
            echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
                'class' => 'smoothbox sitevideo_icon_edit'
            ))
            ?>
            <?php
            echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
                'class' => 'smoothbox sitevideo_icon_delete'
            ))
            ?>
        <?php endif; ?>
        <?php if ($this->topic->closed): ?>
            <div class="sitevideo_discussion_thread_options_closed seaocore_txt_light">
                <?php echo $this->translate('This topic has been closed.'); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php $this->placeholder('sitevideotopicnavi')->captureEnd(); ?>
    <?php echo $this->placeholder('sitevideotopicnavi') ?>
    <?php
    echo $this->paginationControl(null, null, null, array(
        'params' => array(
            'post_id' => null // Remove post id
        )
    ))
    ?>

    <script type="text/javascript">
        var quotePost = function (user, href, body) {
            if ($type(body) == 'element') {
                body = $(body).getParent('li').getElement('.sitevideo_discussion_thread_body_raw').get('html').trim();
            }
            var tinyMCEEditor = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tinymceditor', 1); ?>';
            if (1) {
                tinyMCE.activeEditor.setContent('[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n');
            } else {
                $('body').value = '[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n';
            }
            $("body").focus();
            $("body").scrollTo(0, $("body").getScrollSize().y);
        };
    </script>

    <ul class='sitevideo_discussion_thread'>
        <?php foreach ($this->paginator as $post): ?>
            <li class="b_medium <?php echo $this->cycle(array("odd", "even"))->next() ?>">
                <div class="sitevideo_discussion_thread_photo">
                    <?php
                    $user = $this->item('user', $post->user_id);
                    echo $this->htmlLink($user->getHref(), $user->getTitle());
                    echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));
                    ?>
                </div>
                <div class="sitevideo_discussion_thread_info">
                    <div class="sitevideo_discussion_thread_details">
                        <div class="sitevideo_discussion_thread_details_options">
                            <?php if ($this->form): ?>
                                <?php
                                echo $this->htmlLink('javascript:void(0);', $this->translate('Quote'), array(
                                    'class' => 'buttonlink sitevideo_icon_quote',
                                    'onclick' => 'quotePost("' . $this->escape($user->getTitle()) . '", "' . $this->escape($user->getHref()) . '", this);',
                                ))
                                ?>
                            <?php endif; ?>

                            <?php if ($post->user_id == $this->viewer()->getIdentity() || $this->canEdit == 1 || $this->topic->user_id == $this->viewer()->getIdentity()): ?>
                                <?php
                                echo $this->htmlLink(array('route' => 'sitevideo_post_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox', 'event_id' => $this->sitevideo->getIdentity()), $this->translate('Edit'), array(
                                    'class' => 'smoothbox sitevideo_icon_edit'
                                ))
                                ?>
                                <?php
                                echo $this->htmlLink(array('route' => 'sitevideo_post_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox', 'event_id' => $this->sitevideo->getIdentity()), $this->translate('Delete'), array(
                                    'class' => 'smoothbox sitevideo_icon_delete'
                                ))
                                ?>
                            <?php endif; ?>
                        </div>
                        <div class="sitevideo_time_icon seaocore_txt_light"
                        <?php echo $this->translate('Posted'); ?> <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
                    </div>
                </div>
                <div class="sitevideo_discussion_thread_body">
                    <?php echo nl2br($this->BBCode($post->body, array('link_no_preparse' => true))) ?>
                </div>
                <span class="sitevideo_discussion_thread_body_raw" style="display: none;">
                    <?php echo $post->body; ?>
                </span>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php if ($this->paginator->getCurrentItemCount() > 4): ?>
    <?php
    echo $this->paginationControl(null, null, null, array(
        'params' => array(
            'post_id' => null // Remove post id
        )
    ))
    ?>
    <br />
    <?php echo $this->placeholder('sitevideotopicnavi') ?>
<?php endif; ?>

<br />

<?php if ($this->form): ?>
    <a name="reply"> </a>
    <?php echo $this->form->setAttrib('id', 'sitevideo_topic_reply')->render($this) ?>
<?php endif; ?>
</div>