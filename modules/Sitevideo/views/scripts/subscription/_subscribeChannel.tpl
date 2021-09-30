<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _subscribeChannel.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if(!$this->video->main_channel_id)
    return false;
$channel = Engine_Api::_()->getItem('sitevideo_channel', $this->video->main_channel_id);
$viewer = Engine_Api::_()->user()->getViewer();
if (!$viewer)
    return;
$viewer_id = $viewer->getIdentity();
?>
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1) && $this->video->main_channel_id && ($viewer_id != $channel->owner_id)) : ?>
    <?php  $isSubscribed = $this->video->isSubscribed(); ?>
    <span id="subscribe">
        <?php if ($isSubscribed) : ?>
        <span onclick="subscribeNunsubscribe(false)"><?php echo $this->translate(array('Unsubscribe')) ?></span>
    <?php else : ?>
        <span  onclick="subscribeNunsubscribe(true)"><?php echo $this->translate(array('Subscribe')) ?></span>
    <?php endif; ?>
</span>
<span class="subscription_text" id="subscription_count" ><?php echo $channel->subscribe_count; ?></span>
<span id="notificationSettings" class="subscription_text" style="display:<?php echo ($isSubscribed) ? 'block' : 'none'; ?>">
    <?php
    echo $this->htmlLink(array(
        'module' => 'sitevideo',
        'controller' => 'subscription',
        'action' => 'notification-settings',
        'route' => 'default',
        'channel_id' => $this->video->main_channel_id,
        'format' => 'smoothbox'
            ), "", array(
        'class' => 'smoothbox subscribe_notification'
            //'class' => 'buttonlink smoothbox icon_report'
    ));
    ?>
</span>
<?php endif; ?>
<script type="text/javascript">

    function subscribeNunsubscribe(opt)
    {
        if (opt)
        {
            text = '<?php echo $this->translate('Unsubscribe') ?>';
            actionUrl = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'subscription', 'action' => 'subscribe-channel'), 'default', true) ?>';
        }
        else
        {
            text = '<?php echo $this->translate('Subscribe') ?>';
            actionUrl = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'subscription', 'action' => 'unsubscribe-channel'), 'default', true) ?>';
        }
        var channel_id = '<?php echo $this->video->main_channel_id; ?>';

        (new Request.JSON({
            'format': 'json',
            'url': actionUrl,
            'data': {
                'format': 'json',
                'channel_id': channel_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $('message').innerHTML = responseJSON[0].message;
                $('subscribe').innerHTML = '<span onclick="subscribeNunsubscribe(' + (!opt) + ')">' + text + '</span>'
                if (opt)
                {
                    $('notificationSettings').setStyle("display", "block");
                    $('subscription_count').innerHTML = responseJSON[0].subscription_count;
                }
                else
                {
                   $('notificationSettings').setStyle("display", "none");
                   $('subscription_count').innerHTML = responseJSON[0].subscription_count;
               }
               $('message').show();
                    setTimeout(function(){
                        $('message').toggle();
                    },3000);
            }
        })).send();
    }
</script>