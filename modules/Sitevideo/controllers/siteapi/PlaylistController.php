<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    indexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_PlaylistController extends Siteapi_Controller_Action_Standard {

    public function init() {

        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

// only show playlist if authorized
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            $this->respondWithError("unauthorized");
        }

        if ($this->getRequestParam("playlist_id") && (0 !== ($playlist_id = (int) $this->getRequestParam("playlist_id")) &&
                null !== ($playlist = Engine_Api::_()->getItem('sitevideo_playlist', $playlist_id)))) {
            Engine_Api::_()->core()->setSubject($playlist);
        }
        else if ($this->getRequestParam("playlist_id") && (null !== ($playlist_url = (string) $this->getRequestParam("playlist_id")))) {
            $playlist = Engine_Api::_()->getApi('Core','siteapi')->getSubjectByModuleUrl('sitevideo','playlist','playlist_url',$playlist_url);  
            Engine_Api::_()->core()->setSubject($playlist);
        }
    }

    /*
     * Calling of adv search form
     * 
     * @return array
     */

    public function searchFormAction() {
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');

        try {
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->playlistBrowse(), true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function browseAction() {

        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');

        $params = $this->getRequestAllParams;
        $params['owner_id'] = $this->getRequestParam('user_id', null);
        $params['browsePrivacy'] = 'public';

        $response = array();
        $response['canCreate'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1);

        try {
            $paginator = Engine_Api::_()->getDbTable('playlists', 'sitevideo')->getPlaylistPaginator($params);

            $items_count = $this->getRequestParam("limit", 20);
            $paginator->setItemCountPerPage($items_count);
            $requestPage = $this->getRequestParam('page', 1);
            $paginator->setCurrentPageNumber($requestPage);

            foreach ($paginator as $playlist) {
                $browsePlayList = $playlist->toArray();
                $ratingParams = array();
                $ratingParams['resource_id'] = $browseChannel['channel_id'];
                $ratingParams['resource_type'] = 'channel';

// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
                $browsePlayList = array_merge($browsePlayList, $getContentImages);

// Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
                $browsePlayList = array_merge($browsePlayList, $getContentImages);

                $browsePlayList["owner_title"] = $playlist->getOwner()->getTitle();
                $isAllowedView = $playlist->authorization()->isAllowed($viewer, 'view');
                $browsePlayList["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browsePlayList["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($playlist);

                $response['response'][] = $browsePlayList;
            }

            $response['totalItemCount'] = $paginator->getTotalItemCount();
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function manageAction() {

        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized');
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');

        $params = $this->getRequestAllParams;
        $params['owner_id'] = $this->getRequestParam('user_id', $viewer->getIdentity());

        $response = array();
        $response['canCreate'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1);

        try {
            $paginator = Engine_Api::_()->getDbTable('playlists', 'sitevideo')->getPlaylistPaginator($params);

            $items_count = $this->getRequestParam("limit", 20);
            $paginator->setItemCountPerPage($items_count);
            $requestPage = $this->getRequestParam('page', 1);
            $paginator->setCurrentPageNumber($requestPage);

            foreach ($paginator as $playlist) {
                $browsePlayList = $playlist->toArray();
// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
                $browsePlayList = array_merge($browsePlayList, $getContentImages);

// Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
                $browsePlayList = array_merge($browsePlayList, $getContentImages);

                $browsePlayList["owner_title"] = $playlist->getOwner()->getTitle();
                $isAllowedView = $playlist->authorization()->isAllowed($viewer, 'view');
                $browsePlayList["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browsePlayList["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($playlist);
                $menus= array();
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Playlist'),
                        'name' => 'delete',
                        'url' => 'advancedvideo/playlist/delete/' . $playlist->getIdentity(),
                    );



                    $menus[] = array(
                        'label' => $this->translate('Edit Playlist'),
                        'name' => 'edit',
                        'url' => 'advancedvideo/playlist/edit/' . $playlist->getIdentity(),
                    );
                    $browsePlayList['menu'] = $menus;
                }


                $response['response'][] = $browsePlayList;
            }

            $response['totalItemCount'] = $paginator->getTotalItemCount();
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function viewAction() {

        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject())
            $playlist = Engine_Api::_()->core()->getSubject('sitevideo_playlist');

        if (empty($playlist))
            $this->respondWithError('no_record');


        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            $module_error_type = @ucfirst($playlist->getShortType());
            $this->respondWithError('unauthorized', 0, $module_error_type);
        }
        $tempval = array();
        if ($this->getRequestParam('gutter_menu', true))
            $tempval['gutterMenu'] = $this->_gutterMenus($playlist);

        try {


            $tempval['response'] = $playlist->toArray();

            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist);
            $content_url = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($playlist);
            $tempval['response'] = array_merge($tempval['response'], $getContentImages);

// Add owner images
            if ($playlist->owner_id) {
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($playlist, true);
                $tempval['response'] = array_merge($tempval['response'], $getContentImages);
                 $ownerObj = $playlist->getOwner()->toArray();
                if(!empty($ownerObj))
                $tempval['response']["owner_title"] = $playlist->getOwner()->getTitle();
            }
            $isAllowedView = $playlist->authorization()->isAllowed($viewer, 'view');
            $tempval['response']["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
            $tempval['response']['canCreate'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1);

            if (!$playlist->isOwner($viewer)) {
                $playlist->view_count++;
                $playlist->save();
            }
            if (!empty($content_url['content_url']))
                $tempval['response']['content_url'] = $content_url['content_url'];

            $tempval['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($playlist);

            $playlistVideo = array();

            $playlistVideo = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getAllPlaylistVideo($playlist->playlist_id);

            foreach ($playlistVideo as $video) {

                $browseVideo = $video->toArray();
                $browseVideo['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
                unset($browseVideo['password']);
                $ratingParams = array();
                $ratingParams['resource_id'] = $browseVideo['video_id'];
                $ratingParams['resource_type'] = 'video';
// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
                $browseVideo = array_merge($browseVideo, $getContentImages);

// Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video, true);
                $browseVideo = array_merge($browseVideo, $getContentImages);

                $browseVideo["owner_title"] = $video->getOwner()->getTitle();
                $isAllowedView = $video->authorization()->isAllowed($viewer, 'view');
                $browseVideo["allow_to_view"] = empty($isAllowedView) ? 0 : 1;
                $browseVideo["like_count"] = $video->likes()->getLikeCount();
                $browseVideo["rating_count"] = Engine_Api::_()->getDbTable('ratings', 'sitevideo')->ratingCount($ratingParams);
                $browseVideo['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoURL($video);
                if ($playlist->isOwner($viewer))
                    $browseVideo['is_remove'] = 1;
                else
                    $browseVideo['is_remove'] = 0;

                $videoInfo[] = $browseVideo;
            }
            if (!empty($videoInfo))
                $tempval['videos'] = $videoInfo;
            $this->respondWithSuccess($tempval, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function createAction() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        if ($this->getRequest()->isGet()) {

            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getAddToPlaylistForm(1);
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
//form validation
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getAddToPlaylistForm(1);
            foreach ($getForm['form'] as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            $data = $values;

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getPlaylistFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }//end of form validation

            $validate = new Zend_Validate_Db_RecordExists(array('table' => Engine_Db_Table::getTablePrefix() . 'sitevideo_playlists',
                'field' => 'title'));
            $validate->getSelect()->where('owner_id = ?', $values['owner_id']);
            $validate->getSelect()->where('owner_type = ?', 'user');
            $result = $validate->isValid($values['title']);
            if ($result) {
                $this->respondWithValidationError('validation_fail', array('title' => 'This title already exists'));
            }

            $db = Engine_Api::_()->getDbtable('playlists', 'sitevideo')->getAdapter();
            $db->beginTransaction();
            try {
                $table = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
                $playlist = $table->createRow();
                $playlist->setFromArray($values);
                $playlist->save();

                if (!empty($_FILES['photo'])) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->setPhoto($_FILES['photo'], $playlist);
                }
//PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['privacy'])) {
                    $values['privacy'] = 'owner';
                }

                $viewMax = array_search($values['privacy'], $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                }

                $db->commit();
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
        $this->setRequestMethod();
        $this->_forward('view', 'playlist', 'sitevideo', array(
            'playlist_id' => $playlist->getIdentity()
        ));
    }

    public function deleteAction() {
// Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $playlist = $subject = Engine_Api::_()->core()->getSubject('sitevideo_playlist');

// RETURN IF NO SUBJECT AVAILABLE.
        if (empty($playlist))
            $this->respondWithError('no_record');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');

        $db = $playlist->getTable()->getAdapter();
        $db->beginTransaction();
        try {
// delete map video with playlist
            Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo')
                    ->delete(array(
                        'playlist_id = ?' => $channel->playlist_id
            ));

            if ($playlist->file_id)
                Engine_Api::_()->getItem('storage_file', $playlist->file_id)->remove();

            if ($playlist) {
                $playlist->delete();
            }
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    public function editAction() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        if (Engine_Api::_()->core()->hasSubject())
            $playlist = $subject = Engine_Api::_()->core()->getSubject('sitevideo_playlist');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');


// RETURN IF NO SUBJECT AVAILABLE.
        if (empty($playlist))
            $this->respondWithError('no_record');


        if ($this->getRequest()->isGet()) {

            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getAddToPlaylistForm(1);
            array_splice($response['form'], 3, 1);
            $response['formValues'] = $playlist->toArray();

            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getAddToPlaylistForm(1);
            array_splice($response['form'], 3, 1);
            foreach ($getForm['form'] as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            $values = @array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
            ));

            $data = $values;

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitevideo')->getPlaylistFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = $playlist->getTable()->getAdapter();
            $db->beginTransaction();
            try {

                $playlist->setFromArray($values);
                $playlist->save();

//PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['privacy'])) {
                    $values['privacy'] = 'owner';
                }

                $viewMax = array_search($values['privacy'], $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                }

                $db->commit();
                $this->setRequestMethod();

                $this->_forward('view', 'playlist', 'sitevideo', array(
                    'playlist_id' => $playlist->getIdentity()
                ));

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    private function _gutterMenus($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $subject->getOwner();
        $menus = array();
        $viewer_id = $viewer->getIdentity();
        $setting =Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1);
         if ( $setting && !empty($viewer_id) && $subject->isOwner($viewer)) {
            $menus[] = array(
                'label' => $this->translate('Delete Playlist'),
                'name' => 'delete',
                'url' => 'advancedvideo/playlist/delete/' . $subject->getIdentity(),
            );



            $menus[] = array(
                'label' => $this->translate('Edit Playlist'),
                'name' => 'edit',
                'url' => 'advancedvideo/playlist/edit/' . $subject->getIdentity(),
            );
        }

        if ($viewer_id) {
            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }
        return $menus;
    }

    public function removeFromPlaylistAction() {
        $this->validateRequestMethod('POST');

        if (Engine_Api::_()->core()->hasSubject())
            $playlist = Engine_Api::_()->core()->getSubject('sitevideo_playlist');

        if (empty($playlist))
            $this->respondWithError('no_record');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            $this->respondWithError('unauthorized');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id) && $viewer_id == $playlist->owner_id)
            $this->respondWithError('unauthorized');
        $values = $this->_getAllParams();
        try {
            if (!empty($values['video_id'])) {

                $playlistMapTable = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
                if ($playlist) {
                    if ($playlist->video_count > 0) {
                        $playlist->video_count = ($playlist->video_count) - 1;
                        $playlist->save();
                    }
                }
                $playlistMapTable->delete(array('playlist_id = ?' => $playlist->playlist_id, 'video_id = ?' => $values['video_id']));
                $this->successResponseNoContent('no_content', true);
            }
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

}

?>