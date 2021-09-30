<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onStatistics($event) {

        $table = Engine_Api::_()->getDbTable('videos', 'sitevideo');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'video');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            $table = Engine_Api::_()->getDbTable('channels', 'sitevideo');
            $select = new Zend_Db_Select($table->getAdapter());
            $select->from($table->info('name'), 'COUNT(*) AS count');
            $event->addResponse($select->query()->fetchColumn(0), 'channel');
        }
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        if ((substr($request->getPathInfo(), 1, 5) == "admin")) {
            return;
        }
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            return;
        }
        // Code for Mobile Compatibilty Plugins. We are not excuting the our plugin code in case of mode='mobile' or mode === 'touch'.
        $session = new Zend_Session_Namespace('standard-mobile-mode');
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('touch')) {
            if ($session->__isset('mode')) {
                $mode = $session->__get('mode');
                if ($mode === 'mobile')
                    return;
                elseif ($mode === 'touch')
                    return;
            }elseif (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
                // Reference from "Detect mobile browser (smartphone)" and URL : http://www.serveradminblog.com/2011/01/detect-mobile-browser-smartphone/
                $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
                if
                (
                        preg_match('/imageuploader|android|blackberry|compal|fennec|hiptop|iemobile/i', $useragent) ||
                        preg_match('/ip(hone|od)|kindle|lge|maemo|midp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\//i', $useragent) ||
                        preg_match('/pocket|psp|symbian|treo|up\.(browser|link)|vodafone|windows (ce|phone)|xda/i', $useragent)
                )
                    return;
                if (preg_match('/imageuploader|android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
                    return;
            }
        }

        $mobile = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi')) {
            $mobile = $request->getParam("mobile");
            $session = new Zend_Session_Namespace('mobile');

            if ($mobile == "1") {
                $mobile = true;
                $session->mobile = true;
            } elseif ($mobile == "0") {
                $mobile = false;
                $session->mobile = false;
            } else {
                if (isset($session->mobile)) {
                    $mobile = $session->mobile;
                } else {
                    // CHECK TO SEE IF MOBILE
                    if (Engine_Api::_()->mobi()->isMobile()) {
                        $mobile = true;
                        $session->mobile = true;
                    } else {
                        $mobile = false;
                        $session->mobile = false;
                    }
                }
            }
        }



        if ($module == 'video' && $controller == 'index' && $action == 'view') {
            $request->setModuleName('sitevideo');
            $request->setControllerName('video');
            $request->setActionName('view');
        } else if ($module == 'video' && $controller == 'index' && $action == 'browse') {
            $request->setModuleName('sitevideo');
            $request->setControllerName('video');
            $request->setActionName('index');
        } else if ($module == 'video' && $controller == 'index' && $action == 'manage') {
            $request->setModuleName('sitevideo');
            $request->setControllerName('video');
            $request->setActionName('manage');
        } else if ($module == 'video' && $controller == 'index' && $action == 'create') {
            $request->setModuleName('sitevideo');
            $request->setControllerName('video');
            $request->setActionName('create');
        }

        $lightbox_type = $request->getParam('lightbox_type', null);
        if (!empty($lightbox_type) && $lightbox_type == 'sitevideolightboxview') {
            $module_name = $request->getModuleName();
            $request->setModuleName('sitevideo');
            $request->setControllerName('lightbox');
            $actionName = 'index';
            if ($module_name === 'videofeed') {
                $actionName = 'videofeed-profile';
            }
            $request->setActionName($actionName);
            $request->setParam("module_name", $module_name);
        }
        // SITEVIDEOURL WORK START
        if ((($module == 'sitevideo' && ($controller == 'channel' || $controller == 'mobi') && $action == 'view') || ($module == 'core'))) {
            $front = Zend_Controller_Front::getInstance();

            // GET THE URL OF CHANNEL
            $urlO = $request->getRequestUri();
            $channelurl = '';

            // GET THE ROUTE BY WHICH CHANNEL WILL BE OPEN IF SHORTEN CHANNELURL IS DISABLED
            $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.manifestUrlS', "channel");

            // GET THE BASE URL
            $base_url = $front->getBaseUrl();

            // MAKE A STRING OF BASEUL WITH ROUTESTART
            $string_url = $base_url . '/' . $routeStartS . '/';

            // FIND OUT THE POSITION OF ROUTESTART IF EXIST
            $pos_routestart = strpos($urlO, $string_url);

            if ($pos_routestart === false) {
                $index_routestart = 0;
                $channelurlArray = explode($base_url . '/', $urlO);

                $mainChannelurl = strstr($channelurlArray[1], '/');

                // CHECK BASEDIRECTORY IS EXIST OR NOT
                if (empty($mainChannelurl)) {
                    if (isset($channelurlArray[1])) {
                        $channelurl = $channelurlArray[1];
                    }
                } else {
                    $channelurl = $mainChannelurl;
                }
            } else {
                $index_routestart = 1;
                $channelurlArray = explode($string_url, $urlO);

                $final_url = $channelurlArray[1];
                $mainChannelurl = explode('/', $final_url);

                if (isset($mainChannelurl[1]))
                    $channelurl = $mainChannelurl[1];
            }

            // GET THE CHANNEL LIKES AFTER WHICH SHORTEN CHANNELURL WILL BE WORK 
            $channel_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.likelimit.forurlblock', "0");
            $params_array = array();
            if ($front->getBaseUrl() == '' && empty($index_routestart)) {
                $params_array = $channelurlArray;
                $params_array[0] = NULL;
                array_shift($params_array);
            } else {
                $params_array = explode('/', $channelurlArray[1]);
            }

            if (!empty($index_routestart)) {
                if (isset($params_array['1']))
                    $channelurl = $params_array['1'];
            }
            else {
                $channelurl = $params_array['0'];
            }

            $channelurl = explode('?', $channelurl);
            $channelurl = $channelurl[0];
            // MAKE THE OBJECT OF SITEVIDEO
            $sitevideoObject = Engine_Api::_()->getItem('sitevideo_channel', Engine_Api::_()->sitevideo()->getChannelId($channelurl));
            $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
            // GET THE ARRAY OF BANNED CHANNELURLS
            $urlArray = $bannedChannelurlsTable->select()->from($bannedChannelurlsTable, 'word')
                            ->where('word = ?', $channelurl)
                            ->query()->fetchColumn();
            $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.change.url', 1);

            if (empty($urlArray) && (!empty($change_url)) && !empty($sitevideoObject) && ($sitevideoObject->like_count >= $channel_likes)) {
                if ((!empty($index_routestart))) {
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    unset($params_array[0]);
                    $redirector->gotoUrl(implode("/", $params_array));
                }
                $request->setModuleName('sitevideo');
                $request->setControllerName('channel');
                $request->setActionName('view');
                $request->setParam("channel_url", $channelurl);
                $count = count($params_array);
                for ($i = 1; $i <= $count; $i++) {
                    if (array_key_exists($i, $params_array)) {
                        if (!empty($params_array[$i])) {
                            $request->setParam($params_array[$i], $params_array[++$i]);
                        }
                    }
                }
            }
        }


        if ($mobile && $module == 'sitevideo' && $controller == "index" && $action == "index") {
            $request->setControllerName('mobi');
        }
    }

    public function onUserDeleteAfter($event) {
        $payload = $event->getPayload();
        $user_id = $payload['identity'];
        //DELETE PLAYLISTS
        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
        $playlistSelect = $playlistTable->select()
                ->where('owner_id = ?', $user_id)
                ->where('owner_type = ?', 'user');
        foreach ($playlistTable->fetchAll($playlistSelect) as $playlist) {
          $playlist->delete();
        }

        $table = Engine_Api::_()->getDbTable('channels', 'sitevideo');
        $select = $table->select()->where('owner_id = ?', $user_id)
                ->where('owner_type = ?', 'user');
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $item = Engine_Api::_()->getItem('sitevideo_channel', $row['channel_id']);
            Engine_Api::_()->sitevideo()->deleteChannel($item);
        }
        $table = Engine_Api::_()->getDbTable('videos', 'sitevideo');
        $select = $table->select()->where('owner_id = ?', $user_id)
                ->where('owner_type = "user" OR owner_type = "" OR owner_type IS NULL');
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $item = Engine_Api::_()->getItem('sitevideo_video', $row['video_id']);
            Engine_Api::_()->sitevideo()->deleteVideo($item);
        }
    }

    public function onRenderLayoutDefault($video, $mode = null) {
        $view = $video->getPayload();
        $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
        if ($apiKey)
            $view->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
    }

    public function onUserSignupAfter($video) {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return false;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.creation', 0)) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, 'create')) {
            return false;
        }

        $table = Engine_Api::_()->getItemTable('sitevideo_channel');
        $sitevideoChannel = $table->createRow();
        $sitevideoChannel->title = $viewer->displayname;
        $sitevideoChannel->channel_url = $viewer->username . '_' . $viewer->user_id;
        $sitevideoChannel->owner_id = $viewer->getIdentity();
        $sitevideoChannel->owner_type = 'user';
        $sitevideoChannel->save();
        $tableOtherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitevideo');
        $row = $tableOtherinfo->getOtherinfo($sitevideoChannel->channel_id);
        if (empty($row)) {
            $tableOtherinfo->insert(array(
                'channel_id' => $sitevideoChannel->channel_id,
                'overview' => ''
            ));
        }

        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        $values['auth_view'] = 'everyone';
        $values['auth_comment'] = 'everyone';
        $values['auth_topic'] = 'everyone';
        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);
        $topicMax = array_search($values['auth_topic'], $roles);
        foreach ($roles as $i => $role) {
            $auth->setAllowed($sitevideoChannel, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($sitevideoChannel, $role, 'comment', ($i <= $commentMax));
            $auth->setAllowed($sitevideoChannel, $role, 'topic', ($i <= $topicMax));
        }
    }

    public function addActivity($event)
    {
      if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1) ) {
        return;
      }
      $payload = $event->getPayload();
      $object = $payload['object'];

      // Only for object=video channel
      if( $object instanceof Sitevideo_Model_Channel ) {
        $event->addResponse(array(
          'type' => 'sitevideo_channel_subscriber',
          'identity' => $object->getIdentity()
        ));
      }
    }

    public function getActivity($event)
    {
      if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1) ) {
        return;
      }
      // Detect viewer and subject
      $payload = $event->getPayload();
      $user = null;
      $subject = null;
      if( $payload instanceof User_Model_User ) {
        $user = $payload;
      } else if( is_array($payload) ) {
        if( isset($payload['for']) && $payload['for'] instanceof User_Model_User ) {
          $user = $payload['for'];
        }
        if( isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract ) {
          $subject = $payload['about'];
        }
      }

      if( null === $user ) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( $viewer->getIdentity() ) {
          $user = $viewer;
        }
      }
      if( null === $subject && Engine_Api::_()->core()->hasSubject() ) {
        $subject = Engine_Api::_()->core()->getSubject();
      }

      if ($subject instanceof Sitevideo_Model_Channel) {
        $event->addResponse(array(
            'type' => 'sitevideo_channel_subscriber',
            'data' => $subject->getIdentity(),
        ));
        return;
      }

      // Get event memberships
      if( $user ) {
        $data = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo')->getIdsOfSubscriberChannels($user);
        if( !empty($data) && is_array($data) ) {
          $event->addResponse(array(
            'type' => 'sitevideo_channel_subscriber',
            'data' => $data,
          ));
        }
      }
    }
}
