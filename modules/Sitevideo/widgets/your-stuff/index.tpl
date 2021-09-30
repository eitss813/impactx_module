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
<?php
$statistics = '';
if (in_array('videocount', $this->showContent)) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'video'), 'sitevideo_video_general', true) . '">' . $this->translate(array('%s Video', '%s Videos ', $this->videocount), $this->locale()->toNumber($this->videocount)) . '</a>';
}
if (in_array('likedvideocount', $this->showContent)) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'liked'), 'sitevideo_video_general', true) . '">' . $this->translate(array('%s Video Liked', '%s Videos Liked', $this->videolikecount), $this->locale()->toNumber($this->videolikecount)) . '</a> ';
}
if (in_array('ratedvideocount', $this->showContent)) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'rated'), 'sitevideo_video_general', true) . '">' . $this->translate(array('%s Video Rated', '%s  Videos Rated', $this->videorating), $this->locale()->toNumber($this->videorating)) . '</a>';
}
if (in_array('favvideocount', $this->showContent)) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'favourite'), 'sitevideo_video_general', true) . '">' . $this->translate(array('%s Video Favourited', '%s Videos Favourited', $this->videofavcount), $this->locale()->toNumber($this->videofavcount)) . '</a>';
}
if (in_array('watchlatercount', $this->showContent) && $this->isWatchlaterEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'watchlater'), 'sitevideo_video_general', true) . '">' . $this->translate(array('%s Watch Later', '%s Watch Later ', $this->watchcount), $this->locale()->toNumber($this->watchcount)) . '</a>';
}
if (in_array('playlistcount', $this->showContent) && $this->isPlaylistEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'playlist'), 'sitevideo_video_general', true) . '">' . $this->translate(array('%s Playlist', '%s Playlists', $this->playlistcount), $this->locale()->toNumber($this->playlistcount)) . '</a> ';
}
if (in_array('channelscreated', $this->showContent) && $this->isChannelEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'channel'), 'sitevideo_channel_general', true) . '">' . $this->translate(array('%s Channel', '%s Channels', $this->channelcount), $this->locale()->toNumber($this->channelcount)) . '</a>';
}
if (in_array('channelsliked', $this->showContent) && $this->isChannelEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'liked'), 'sitevideo_channel_general', true) . '">' . $this->translate(array('%s Channel Liked', '%s Channels Liked', $this->channellikecount), $this->locale()->toNumber($this->channellikecount)) . '</a> ';
}
if (in_array('channelsubscribed', $this->showContent) && $this->isChannelEnabled && $this->isSubscriptionEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'subscribed'), 'sitevideo_channel_general', true) . '">' . $this->translate(array('%s Subscribed', '%s Subscribed', $this->subscribecount), $this->locale()->toNumber($this->subscribecount)) . '</a> ';
}
if (in_array('channelsrated', $this->showContent) && $this->isChannelEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'rated'), 'sitevideo_channel_general', true) . '">' . $this->translate(array('%s Channel Rated', '%s Channels Rated', $this->channelrating), $this->locale()->toNumber($this->channelrating)) . '</a>';
}
if (in_array('channelsfavourited', $this->showContent) && $this->isChannelEnabled) {
    $statistics .= '<a href="' . $this->url(array('action' => 'manage', 'tab' => 'favourite'), 'sitevideo_channel_general', true) . '">' . $this->translate(array('%s Channel Favourited', '%s Channels Favourited', $this->channelfavcount), $this->locale()->toNumber($this->channelfavcount)) . '</a>';
}
$statistics = trim($statistics);
$statistics = rtrim($statistics, ',');
?>
<?php if (!empty($statistics)) : ?>
    <div class="sitevideo_listings_stats sitevideo_your_stuff">
        <?php echo $statistics; ?>
    </div>
<?php endif; ?>    