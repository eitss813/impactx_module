<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addToPlaylist.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$viewer = Engine_Api::_()->user()->getViewer();
if (!$viewer)
    return;
$playlists = new Sitevideo_Model_DbTable_Playlists();
$rows = $playlists->fetchAll($playlists->select()
                ->where('owner_id = ?', $viewer->getIdentity())
                ->where('owner_type = ?', 'user'));
$this->playlists = $rows;
?>
<?php
//Checking for "Playlist" is enabled for this site
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) :
    ?>
    <span class="<?php echo $this->isLightBox ? 'lightbox_btm_bl_btn' : ''; ?>" id="addTo" onclick="showPlaylist(<?php echo $this->isLightBox ? 1 : 0; ?>)"><?php echo $this->translate('Add to '); ?></span>
    <div id="playlists<?php echo $this->isLightBox ? '_1' : '_0' ?>" style="display:none;" class="sitevideo_options_addtoplaylist">
        <div class="sitevideo_options_addtoplaylist_head"><?php echo $this->translate('Add to Playlist'); ?></div>
        <?php if (count($this->playlists) > 0) : ?>
            <ul>
                <?php
                foreach ($this->playlists as $playlist) {
                    ?>
                    <li class="<?php echo $playlist->privacy ?>" title="<?php echo ucfirst($playlist->privacy) ?>">
                        <input type="checkbox" name="checkbox_<?php echo $playlist->playlist_id ?>" value="<?php echo $playlist->playlist_id ?>" onclick="playlistHandler(this)" <?php echo $playlist->isVideoAdded($this->video->video_id) ? 'checked' : ''; ?>> <label><?php echo $playlist->title ?></label>
                    </li>
                    <?php
                }
                ?>
            </ul>
        <?php endif; ?>
        <div class="sitevideo_create_playlist seaocore_icon_add">
            <?php
            echo $this->htmlLink(array(
                'route' => 'sitevideo_playlist_general',
                'action' => 'create',
                    ), $this->translate('Create a New Playlist'));
            ?>
        </div>
    </div>
    <script type="text/javascript">
        function showPlaylist(isLightBox)
        {
            if (isLightBox == 1)
                id = "playlists_1";
            else
                id = "playlists_0";
            styleVal = $(id).getStyle("display");
            $(id).setStyle("display", (styleVal == "none" ? "block" : "none"));
        }
        function playlistHandler(obj)
        {
            if (obj.checked)
                actionUrl = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'add-to-playlist'), 'default', true) ?>'
            else
                actionUrl = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'remove-from-playlist'), 'default', true) ?>'
            var video_id = <?php echo $this->video->video_id; ?>;
            var playlist_id = obj.value;
            (new Request.JSON({
                'format': 'json',
                'url': actionUrl,
                'data': {
                    'format': 'json',
                    'video_id': video_id,
                    'playlist_id': playlist_id
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