<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _watchlater.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$viewer = Engine_Api::_()->user()->getViewer();
if (!$viewer)
    return;
?>
<?php
//Checking for "Watchlater" is enabled for this site
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) :
    ?>
    <a href="javascript:void(0);" class="sitevideo_watchlater" onclick="addToWatchlater()"><?php echo $this->translate(array('Watch Later')) ?></a>
    <script type="text/javascript">

        function addToWatchlater()
        {
            actionUrl = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'watchlater', 'action' => 'add-to-watchlater'), 'default', true) ?>'
            var video_id = <?php echo $this->video->video_id; ?>;
            (new Request.JSON({
                'format': 'json',
                'url': actionUrl,
                'data': {
                    'format': 'json',
                    'video_id': video_id,
                },
                'onSuccess': function (responseJSON, responseText)
                {
                    $('message').innerHTML = responseJSON[0].message;
                    $('message').show();
                    setTimeout(function () {
                        $('message').toggle();
                    }, 3000);
                }
            })).send();

        }
    </script>
<?php endif; ?>