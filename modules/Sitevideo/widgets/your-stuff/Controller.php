<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_YourStuffController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF VEWER IS EMPTY
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            return $this->setNoRender();
        }

        $this->view->isChannelEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1);
        $this->view->isWatchlaterEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1);
        $this->view->isPlaylistEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1);
        $this->view->isSubscriptionEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1);
        $this->view->showContent = $this->_getParam('statistics');
        $stats = Engine_Api::_()->sitevideo()->getUserStats($viewer_id);
        $this->view->channellikecount = $stats['channellikecount'];
        $this->view->videolikecount = $stats['videolikecount'];
        $this->view->channelfavcount = $stats['channelfavcount'];
        $this->view->videofavcount = $stats['videofavcount'];

        $param['tableName'] = 'channels';
        $param['columnName'] = 'owner_id';
        $this->view->channelcount = Engine_Api::_()->sitevideo()->yourStuff($param);

        $param['tableName'] = 'videos';
        $param['columnName'] = 'owner_id';
        $this->view->videocount = Engine_Api::_()->sitevideo()->yourStuff($param);

        $param['tableName'] = 'watchlaters';
        $param['columnName'] = 'owner_id';
        $this->view->watchcount = Engine_Api::_()->sitevideo()->yourStuff($param);

        $param['tableName'] = 'subscriptions';
        $param['columnName'] = 'owner_id';
        $this->view->subscribecount = Engine_Api::_()->sitevideo()->yourStuff($param);

        $param['tableName'] = 'playlists';
        $param['columnName'] = 'owner_id';
        $this->view->playlistcount = Engine_Api::_()->sitevideo()->yourStuff($param);

        $param['tableName'] = 'ratings';
        $param['columnName'] = 'user_id';
        $param['resourceType'] = 'sitevideo_channel';
        $this->view->channelrating = Engine_Api::_()->sitevideo()->yourStuff($param);

        $param['tableName'] = 'ratings';
        $param['columnName'] = 'user_id';
        $param['resourceType'] = 'video';
        $this->view->videorating = Engine_Api::_()->sitevideo()->yourStuff($param);
    }

}
