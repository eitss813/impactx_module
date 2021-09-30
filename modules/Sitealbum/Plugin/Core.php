<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onStatistics($event) {

        $table = Engine_Api::_()->getDbTable('photos', 'sitealbum');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'photo');
    }

    // todo: 5.2.1 Upgrade => Fixed issue "Call to undefined method Sitealbum_Plugin_Core::onUserCreateAfter()" when signup done
    public function onUserCreateAfter($event) {

    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        // CHECK IF ADMIN
        $getPhotoTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.get.phototag', null);
        if ((substr($request->getPathInfo(), 1, 5) == "admin") || empty($getPhotoTag)) {
            return;
        }
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($module == 'album' && $controller == 'album' && $action == 'editphotos') {
            $album_id = $request->getParam("album_id");
            if (!empty($album_id))
                Engine_Api::_()->sitealbum()->setPhotosOrder($album_id);
        }

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

        if ($mobile && $module == 'sitealbum' && $controller == "index" && $action == "index") {
            $request->setControllerName('mobi');
        }

        if ($module == "sitealbum") {
            if ($controller == "photo" && $action == "view") {
                if ($mobile) {
                    return;
                }

                $is_ajax = $request->getParam('isajax', 0);
                if (empty($is_ajax)) {
                    // $view->headScript()->prependScript($script);
                } else {
                    $request->setActionName('ajax-photo-view');
                }
            }
        }
        $this->loadSitealbumPluginCore();
    }

    // ADDED FOR SE4.10 UPGRADE
    public function onUserPhotoUpload($event) {
        $this->onUserProfilePhotoUpload($event);
    }

    public function onUserProfilePhotoUpload($event) {
        $payload = $event->getPayload();

        if (empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $viewer = $payload['user'];
        $file = $payload['file'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'sitealbum');
        $album = $table->getSpecialAlbum($viewer, 'profile');

        $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
        ));
        $photo->save();
        $photo->setPhoto($file);

        $photo->album_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
        }

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);

        $event->addResponse($photo);

        $viewer = $payload['user'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'sitealbum');
        $photos_count = $table->select()
                ->from('engine4_album_albums', array('photos_count'))
                ->where('owner_id = ?', $viewer->getIdentity())
                ->where('type = ?', 'profile')
                ->limit(1)
                ->query()
                ->fetchColumn();

        $table->update(array('photos_count' => $photos_count + 1), array('type=?' => 'profile', 'owner_id=?' => $viewer->getIdentity()));
    }

    public function onUserDeleteAfter($event) {
        $payload = $event->getPayload();
        $user_id = $payload['identity'];
        $table = Engine_Api::_()->getDbTable('albums', 'sitealbum');
        $select = $table->select()->where('owner_id = ?', $user_id)
                ->where('owner_type = ?', 'user');
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }
        $table = Engine_Api::_()->getDbTable('photos', 'sitealbum');
        $select = $table->select()->where('owner_id = ?', $user_id)
                ->where('owner_type = ?', 'user');
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }
    }

    public function loadSitealbumPluginCore() {

        $class = 'Sitealbum_Loader';
        Engine_Loader::loadClass($class);
        $loader = new $class();

        //WE ARE LOADING OUR SITEALBUM PLUGIN CORE FORCEFULLY FOR ALBUM PLUGIN CORE
        $loader->setComponentsObject('Sitealbum_Plugin_Core', 'Album_Plugin_Core');
    }

    public function onRenderLayoutDefault($event, $mode = null) {
        $view = $event->getPayload();
        $view->headScript()
        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
         $view->headScript()
          ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
          ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
          ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
          ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
        $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
        $view->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    }
    
      public function onRenderLayoutDefaultSimple($event) {
          
      // Forward
      return $this->onRenderLayoutDefault($event, 'simple');
  }  
  
  public function onRenderLayoutMobileDefault($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }
  
  public function onRenderLayoutMobileDefaultSimple($event)
  {
    // Forward
    return $this->onRenderLayoutDefault($event);
  }  

}
