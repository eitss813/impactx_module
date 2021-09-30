<?php


/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagealbum_PhotoController extends Siteapi_Controller_Action_Standard {

    /*
    * Siteenablealbum enable checks and getting subject
    *
    *
    */
    public function init() {
        // SItepagealbum enable check
        $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
        if (!$sitepagealbumEnabled) {
            $this->respondWithError('unauthorized');
        }

        // Get subject
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null !== ($photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 !== ($page_id = (int) $this->_getParam('page_id')) &&
                    null !== ($sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id))) {
                Engine_Api::_()->core()->setSubject($sitepage);
            }
        }

        // Get page id
        $page_id = $this->_getParam('page_id');

        // Package based privacy start 
        if (isset($page_id) && !empty($page_id)) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            if (isset($sitepage) && !empty($sitepage)) {
                if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum")) {
                        $this->respondWithError('unauthorized');
                    }
                } else {
                    $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
                    if (empty($isPageOwnerAllow)) {
                        $this->respondWithError('unauthorized');
                    }
                }
            }
        }
        // Package based privacy end
        else {
            if (Engine_Api::_()->core()->hasSubject() != null) {
                $photo = Engine_Api::_()->core()->getSubject();
                $album = $photo->getCollection();
                $page_id = $album->page_id;
            }
        }
    }

    /*
    * Returns the albums of Directory page
    *
    *
    */
    public function indexAction() {

        // Validate request method
        $this->validateRequestMethod();

        // Check subject
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->respondWithError('no_record');
        }

        $albums_per_page = $this->_getParam('itemCount', 10);

        // Get viewer id
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // $sitepagealbum_hasPackageEnable = Zend_Registry::isRegistered('sitepagealbum_hasPackageEnable') ? Zend_Registry::get('sitepagealbum_hasPackageEnable') : null;
        
        // Get sitepage subject
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        // Total albums
        $albumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
        $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        $canEdit = 1;
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
        $canView = 1;

        if(empty($isManageAdmin))
            $canView = 0;

        if (empty($photoCreate) && empty($albumCount) && empty($canView)) {
            $this->respondWithError('unauthorized');
        }

        $albumresponse = array();
        $photosresponse = array();
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
        $allowed_upload_photo = 0;
        if ($isManageAdmin || $canEdit) {
            $allowed_upload_photo = 1;
        }

        // Albums order
        $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1);

        // Get current page number of album
        $currentAlbumPageNumbers = $this->_getParam('page', 1);

        // Set album params
        $paramsAlbum = array();
        $paramsAlbum['page_id'] = $sitepage->page_id;

        // Get album count
        $album_count = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);
        $albumresponse['totalItemCount'] = $album_count;
        $albums_per_page = $this->_getParam('limit',20);

        // Start album pagination
        $pages_vars = Engine_Api::_()->sitepage()->makePage($album_count, $albums_per_page, $currentAlbumPageNumbers);
        $pages_array = Array();
        for ($y = 0; $y <= $pages_vars[2] - 1; $y++) {
            $links = "0";
            if ($y + 1 == $pages_vars[1]) {
                $links = "1";
            }
            $pages_array[$y] = Array('pages' => $y + 1,
                'links' => $links);
        }
        $maxpages = $pages_vars[2];
        $pstarts = 1;
        // End album pagination
        
        // Set album params
        $paramsAlbum['start'] = $albums_per_page;
        $paramsAlbum['end'] = $pages_vars[0];
        $paramsAlbum['orderby'] = 'album_id DESC';
        if (empty($albums_order)) {
            $paramsAlbum['orderby'] = 'album_id ASC';
        }
        $paramsAlbum['getSpecialField'] = 0;

        $fetchAlbums = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);
        $albumsData = array();
        if (!empty($fetchAlbums)) {
            foreach ($fetchAlbums as $album) {
                $albumarray = $album->toArray();

                $paramsPhoto = array();
                $paramsPhoto['page_id'] = $sitepage->page_id;
                $paramsPhoto['album_id'] = $album->album_id;
                $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
                $albumarray['photo_count'] = $total_photo;
                $albumarray['allow_to_view'] = (int) $canView;
                $albumarray = array_merge($albumarray , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, false));
                $menu = array();
                $menu[] = array(
                    'label' => $this->translate('Share'),
                    'name' => 'share',
                    'url' => 'activity/index/share',
                    'urlParams' => array(
                        'type' => $album->getType(),
                        'id' => $album->getIdentity(),
                    ),
                );
                $menu[] = array(
                    'label' => $this->translate('Report'),
                    'name' => 'report',
                    'url' => 'report/create/subject/'.$album->getType().'_'.$album->getIdentity(),
                );
                if($allowed_upload_photo)
                {
                    $menu[] = array(
                        'label' => $this->translate('Make Profile Photo'),
                        'name' => 'make_profile_photo',
                        "url" => "members/edit/external-photo",
                        "urlParams" => array(
                            "photo" => $album->getType().'_'.$album->getIdentity(),
                        ),
                    );
                }
                
                $albumarray['owner_title'] = $album->getOwner()->getTitle();
                $albumarray['menu'] = $menu;
                $albumsData[] = $albumarray;
            }
        }

        $albumresponse['response'] = $albumsData;

        $response = array();
        $response = $albumresponse;

        $this->respondWithSuccess($response, true);
    }
    

    
    /**
     * Returns the contents of the album (photos)
     *
     */
    public function viewalbumAction() {
        
        // Get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $album_id = $this->_getParam('album_id', 0);
        
        // Albums order
        $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1);
        
        $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
        $album_data = $album->toArray();

        $album_data = array_merge($album_data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album));
        $album_data = array_merge($album_data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album,true));
        $album_data['owner_title'] = $album->getOwner()->getTitle();

        // Getting viewer like or not to content.
        $album_data["is_like"] = (bool) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);

        $photos_per_page = $this->_getParam('itemCount_photo', 20);
        
        $paramsPhoto = array();
        $paramsPhoto['page_id'] = $sitepage->page_id;
        $paramsPhoto['album_id'] = $album_id;
        $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
        $currentPageNumbers = $this->_getParam('page', 1);

        // Start photos pagination
        $page_vars = Engine_Api::_()->sitepage()->makePage($total_photo, $photos_per_page, $currentPageNumbers);
         $page_array = Array();
        for ($x = 0; $x <= $page_vars[2] - 1; $x++) {
            if ($x + 1 == $page_vars[1]) {
                $link = "1";
            } else {
                $link = "0";
            }
            $page_array[$x] = Array('page' => $x + 1,
                'link' => $link);
        }
        $paramsPhoto['start'] = $photos_per_page;
        $paramsPhoto['end'] = $page_vars[0];
        if (empty($albums_order)) {
            $paramsPhoto['photosorder'] = 'album_id ASC';
        } else {
            $paramsPhoto['photosorder'] = 'album_id DESC';
        }
        
        $paginators = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);
        $photos = array();
        if ($paginators->count() > 0) {
            foreach ($paginators as $photo) {
                $data = $photo->toArray();
                $data = array_merge($data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false));
                $data = array_merge($data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, true));
                $data['menu'] = $this->getPhotoMenu($photo);
                $photos[] = $data;
                unset($data);
            }
        } else
            $photos = null;
        
        $response = array();
        $response['totalPhotoCount'] = $total_photo;
        $response['album'] = $album_data;
        $response['gutterMenu'] = $this->_albumGutterMenus();
        $response['albumPhotos'] = $photos;
        $this->respondWithSuccess($response, true);
    }

    /**
     * Returns the contents of the album (photos)
     *
     */
    public function viewalbumDataAction() {
        
        // Get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $album_id = $this->_getParam('album_id', 0);
        
        // Albums order
        $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1);
        
        $canUpload = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        

        $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
        // $album_data = $album->toArray();

        // $album_data = array_merge($album_data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album));
        // $album_data = array_merge($album_data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album,true));
        // $album_data['owner_title'] = $album->getOwner()->getTitle();

        // // Getting viewer like or not to content.
        // $album_data["is_like"] = (bool) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);
        
        $paramsPhoto = array();
        $paramsPhoto['page_id'] = $sitepage->page_id;
        $paramsPhoto['album_id'] = $album_id;
        $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
        $currentPageNumbers = $this->_getParam('page', 1);
        $photos_per_page = $this->_getParam('limit', 20);

        // Start photos pagination
        $page_vars = Engine_Api::_()->sitepage()->makePage($total_photo, $photos_per_page, $currentPageNumbers);
        $page_array = Array();
        for ($x = 0; $x <= $page_vars[2] - 1; $x++) {
            if ($x + 1 == $page_vars[1]) {
                $link = "1";
            } else {
                $link = "0";
            }
            $page_array[$x] = Array('page' => $x + 1,
                'link' => $link);
        }
        $paramsPhoto['start'] = $photos_per_page;
        $paramsPhoto['end'] = $page_vars[0];
        if (empty($albums_order)) {
            $paramsPhoto['photosorder'] = 'album_id ASC';
        } else {
            $paramsPhoto['photosorder'] = 'album_id DESC';
        }
        
        $paginators = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);
        $photos = array();
        if ($paginators->count() > 0) {
            foreach ($paginators as $photo) {
                $data = $photo->toArray();
                $data["is_like"] = (bool) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);
                $data = array_merge($data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false));
                $data = array_merge($data , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, true));
                $data['menu'] = $this->getPhotoMenu($photo);
                $photos[] = $data;
                unset($data);
            }
        } else
            $photos = null;
        
        $response = array();
        $response['totalPhotoCount'] = $total_photo;
        $response['canUpload'] = $canUpload;
        // $response['album'] = $album_data;
        // $response['gutterMenu'] = $this->_albumGutterMenus();
        $response['photos'] = $photos;
        $this->respondWithSuccess($response, true);
    }

    /*
    * Gutter menus for photo
    */
    private function getPhotoMenu($photo)
    {
        if(!$photo)
            return;

        $album = Engine_Api::_()->getItem('sitepage_album', $photo->album_id);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $album->page_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $menu = array();

        if(Engine_Api::_()->authorization()->isAllowed($photo, $viewer, 'edit'))
        {
            $menu[] = array(
                'label' => $this->translate("Edit Photo"),
                'name' => 'edit',
                'url' => 'sitepage/photos/editphoto/' . $sitepage->getIdentity() . '/' . $album->getIdentity() . '/' . $photo->getIdentity(),
            );

            $menu[] = array(
                'label' => $this->translate('Make Profile Photo'),
                'name' => 'make_profile_photo',
                'url' => 'sitepage/photos/profile-photo/'. $sitepage->getIdentity() . '/' . $album->getIdentity() . '/' . $photo->getIdentity(),
            );
        }

        if(Engine_Api::_()->authorization()->isAllowed($photo, $viewer, 'delete'))
        {
            $menu[] = array(
                'label' => $this->translate("Delete Photo"),
                'name' => 'delete',
                'url' => 'sitepage/photos/deletephoto/' . $sitepage->getIdentity() . '/' . $album->getIdentity() . '/' . $photo->getIdentity(),
            );
        }

        $menu[] = array(
            'label' => $this->translate('Share'),
            'name' => 'share',
            'url' => 'activity/share',
            'urlParams' => array(
                'type' => $photo->getType(),
                'id' => $photo->getIdentity(),
            ),
        );

        $menu[] = array(
            'label' => $this->translate('Report'),
            'name' => 'report',
            'url' => 'report/create/subject/album_photo_'.$photo->getIdentity(),
            'urlParams' => array(
                'type' => $photo->getType(),
                'id' => $photo->getIdentity(),
            ),
        );

        return $menu;

    }

    /*
    * Make profile picture
    */
    public function profilePhotoAction()
    {
        $album_id = $this->_getParam('album_id', 0);
        $page_id = $this->_getParam('page_id', 0);
        $photo_id = $this->_getParam('photo_id', 0);

        if(!$photo_id || !$album_id || !$page_id)
            $this->respondWithError('parameter_missing',array('photo_id' => "photo_id or page_id or album_id missing"));

        $photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $sitepage->photo_id = $photo->getIdentity();
            $sitepage->save();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

    }

    

    /**
     * Delete album
     *
     * @return array
     */
    public function deletealbumAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $album_id = $this->_getParam("album_id");
        if (!Engine_Api::_()->authorization()->isAllowed('sitepage_album', $viewer, 'delete'))
                $this->respondWithError('unauthorized');
        
        $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $album->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function editalbumAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $ownerList = $sitepage->getPageOwnerList();

        $album_id = $this->_getParam("album_id");
        $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

        //    //START MANAGE-ADMIN CHECK
        //    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
        //    if (empty($isManageAdmin)) {
        //      return $this->setNoRender();
        //    }

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        if ($this->getRequest()->isGet()) {
            $editForm = array();
            $editForm[] = array(
                'title' => $this->translate("Edit Title"),
                'name' => 'title',
                'value' => $album->title,
                'hasValidator' => true
            );
            // Privacy
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1))
                $ownerTitle = "Page Admins";
            else
                $ownerTitle = "Just Me";

            $user = Engine_Api::_()->user()->getViewer();
            $availableLabels = array(
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'like_member' => 'Who Liked This Page',
            );

            $allowMemberInthisPackage = false;
            $allowMemberInthisPackage = Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember");
            $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
                $availableLabels['member'] = 'Page Members Only';
            } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
                $availableLabels['member'] = 'Page Members Only';
            }

            $availableLabels['owner'] = $ownerTitle;



            $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_album', $user, 'auth_tag');

            $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
            if (count($tagOptions) > 1) {
                $editForm[] = array(
                    'type' => 'select',
                    'name' => 'auth_tag',
                    'label' => 'Tag Post Privacy',
                    'description' => 'Who may tag photos in this album?',
                    'multiOptions' => $tagOptions,
                    'value' => key($tagOptions),
                );
            } else if (count($tagOptions) == 1) {
                $editForm[] = array(
                    'type' => 'select',
                    'name' => 'auth_tag',
                    'label' => 'Tag Post Privacy',
                    'description' => 'Who may tag photos in this album?',
                    'value' => key($tagOptions),
                );
            }
            $editForm[] = array(
                'label' => $this->translate('show this album in search results'),
                'value' => 1,
                'type' => 'checkbox',
                'name' => 'search',
            );
            $editForm[] = array(
                'type' => 'submit',
                'name' => 'submit'
            );
            $response = array();
            $response['form'] = $editForm;
            $this->respondWithSuccess($response, TRUE);
        } elseif ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
            
            // Process
            $db = $album->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                
                // Get form values
                $album->setFromArray($values);
                $album->save();

                // Create suth stuff here
                $auth = Engine_Api::_()->authorization()->context;
                //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if (!empty($sitepagememberEnabled)) {
                    $roles = array('owner', 'member', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                // Rebuild privacy
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                foreach ($actionTable->getActionsByObject($album) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                // Start tag 
                if (!isset($values['auth_tag']) && empty($values['auth_tag'])) {
                    $values['auth_tag'] = key(key($tagOptions));
                    if (empty($values['auth_tag'])) {
                        $values['auth_tag'] = 'registered';
                    }
                }
                $tagMax = array_search($values['auth_tag'], $roles);
                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
                }
                // Commit
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    public function albumfeaturedAction() {
        $album_id = $this->_getParam("album_id");
        $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        $album->featured = !$album->featured;
        $album->save();
        $this->successResponseNoContent('no_content', true);
    }

    /*
    * Adding album of the day
    *
    *
    */
    public function addalbumofdayAction() {

        // Form generation
        $album_id = $this->_getParam('album_id');
        
        // Check post
        if ($this->getRequest()->isPost()) {

            // Get form values
            $values = $this->_getAllParams();
            
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepagealbum')->albumOfDayValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Begin transaction
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            
            try {
                
                // Get item of the day table
                $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

                // Fetch result for resource_id
                $select = $dayItemTime->select()->where('resource_id = ?', $album_id)->where('resource_type = ?', 'sitepage_album');
                
                $row = $dayItemTime->fetchRow($select);

                if (empty($row)) {
                    $row = $dayItemTime->createRow();
                    $row->resource_id = $album_id;
                }
                $row->start_date = $values["startdate"];
                $row->end_date = $values["enddate"];
                $row->resource_type = 'sitepage_album';
                $row->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } else if ($this->getRequest()->isGet()) {
            
            $responseform = array();
            $responseform[] = array(
                'type' => 'Date',
                'name' => 'startdate',
                'title' => $this->translate("Start Date"),
                'description' => $this->translate(" example : 2016-04-27 "),
                'required' => 'true'
            );
            $responseform[] = array(
                'type' => 'Date',
                'name' => 'enddate',
                'title' => $this->translate("End Date"),
                'description' => $this->translate(" example : 2016-04-27 "),
                'required' => 'true'
            );
            $responseform[] = array(
                'type' => "submit",
                'name' => "submit",
            );
            $responseData = array();
            $responseData['form'] = $responseform;
            $this->respondWithSuccess($responseData, true);
            
        }
    }

    /*
    * Returns photo with detail
    *
    */
    public function viewphotoAction() {
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $page_id = $this->_getParam('page_id');
        $album_id = $this->_getParam('album_id');
        $photo_id = $this->_getParam('photo_id');

        // Get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_photo') {
            $photo = Engine_Api::_()->core()->getSubject('sitepage_photo');
        } elseif ($this->_getParam('photo_id')) {
            $photo_id = $this->_getParam('photo_id');
            $photo = Engine_Api::_()->getItem('sitepage_photo', $this->_getParam('photo_id'));
        } else
            $this->respondWithError('validation_fail', "photo_id missing");


        if (!$photo || !$sitepage)
            $this->respondWithError('no_record');

        $photoData = array();
        $photoData = $photo->toArray();
        $filedata = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
        $table = Engine_Api::_()->getDbtable('files', 'storage');
        $file = $table->getFile($photoData['photo_id'])->toArray();
        $photoData =array_merge($photoData , $filedata);
        $editMenu[] = array(
            'label' => $this->translate("Edit Photo"),
            'name' => 'edit',
            'url' => 'sitepage/photos/editphoto/' . $page_id . '/' . $album_id . '/' . $photo_id,
        );
        $editMenu[] = array(
            'label' => $this->translate("Delete Photo"),
            'name' => 'delete',
            'url' => 'sitepage/photos/deletephoto/' . $page_id . '/' . $album_id . '/' . $photo_id,
        );
        $photoData['menu'] = $editMenu;
        $response['photos'] = array($photoData);
        $this->respondWithSuccess($response, true);
    }

    /*
    * Edit title and description of a particular photo
    *
    */
    public function editphotoAction() {

        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_photo') {
            $photo = Engine_Api::_()->core()->getSubject('sitepage_photo');
        } elseif ($this->_getParam('photo_id')) {
            $photo_id = $this->_getParam('photo_id');
            $photo = Engine_Api::_()->getItem('sitepage_photo', $this->_getParam('photo_id'));
        } else
            $this->respondWithError('validation_fail', "photo_id missing");

        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($albumCount) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {

            $editForm = array();
            $editForm[] = array(
                'title' => $this->translate('Title'),
                'name' => 'title',
                'type' => 'text',
            );

            $editForm[] = array(
                'title' => $this->translate('Description'),
                'name' => 'title',
                'type' => 'text',
            );
            
            $editForm[] = array(
                'type' => 'submit',
                'title' => $this->translate('submit'),
                'name' => 'submit'
            );

            $this->respondWithSuccess($editForm, true);
        } elseif ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            $db = $photo->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                if(isset($values['title']) && !empty($values['title']))
                    $photo->title = $values['title'];
                
                if(isset($values['description']) && !empty($values['description']))
                    $photo->description = $values['description'];
                
                $photo->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
    * Deletes a photo
    */
    public function deletephotoAction() {
        
        // Validate request method
        $this->validateRequestMethod('DELETE');

        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_photo') {
            $photo = Engine_Api::_()->core()->getSubject('sitepage_photo');
        } elseif ($this->_getParam('photo_id')) {
            $photo_id = $this->_getParam('photo_id');
            $photo = Engine_Api::_()->getItem('sitepage_photo', $this->_getParam('photo_id'));
        } else
            $this->respondWithError('validation_fail', "photo_id missing");


        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($albumCount) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }
        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $photo->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /*
    * Add photo to album
    *
    */
    public function addphotoAction() {
        
        $this->validateRequestMethod('POST');
        
        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (isset($_FILES) && $this->getRequest()->isPost()) {

            if (empty($viewer_id))
                $this->respondWithError('unauthorized');

            $params = $this->_getAllParams();

            if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
                $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
            } else {
                $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
            }

            foreach ($_FILES as $value) {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitepagealbum')->setPhoto($value, $sitepage, 1, $params);
            }
            $this->successResponseNoContent('no_content', true);
        }
    }
    
    /*
    *   Returns menus of the album
    *
    *
    * @return array
    */
    private function _albumGutterMenus() {
        $album_id = $this->_getParam('album_id', 0);
        $page_id = $this->_getParam('page_id', 0);
        $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        
        $gutterMenus = array();
        
        
        // Delete an album
        // if (Engine_Api::_()->authorization()->isAllowed('sitepage_album', $viewer, 'delete'))
        // {
        //     $gutterMenus[] = array(
        //         'label' => $this->translate("Delete Album"),
        //         'url' => 'sitepage/photos/deletealbum/' . $page_id . '/' . $album_id,
        //         'name' => 'delete'
        //     );
        // }
        
        // edit an album
        // if (Engine_Api::_()->authorization()->isAllowed('sitepage_album', $viewer, 'edit'))
        // {
        //     $gutterMenus[] = array(
        //         'label' => $this->translate("Edit Album"),
        //         'url' => 'sitepage/photos/editalbum/' . $page_id . '/' . $album_id,
        //         'name' => 'edit'
        //     );
        // }
        
        // if($album->featured)
        // {
        //     $gutterMenus[] = array(
        //         'label' => $this->translate("Make Album non Featured"),
        //         'url' => 'sitepage/photos/albumfeatured/' . $page_id . '/' . $album_id,
        //         'name' => 'unfeatured'
        //     );
        // }
        // else
        // {
        //     $gutterMenus[] = array(
        //         'label' => $this->translate("Make Featured"),
        //         'url' => 'sitepage/photos/albumfeatured/' . $page_id . '/' . $album_id,
        //         'name' => 'featured'
        //     );
        // }
        
        // $gutterMenus[] = array(
        //     'label' => $this->translate("Make Album of the Day"),
        //     'url' => 'sitepage/photos/addalbumofday/' . $page_id . '/' . $album_id,
        //     'name' => 'albumofday'
        // );
        $gutterMenus[] = array(
            'label' => $this->translate("Add Photo"),
            'url' => 'sitepage/photos/addphoto/' . $page_id . '/' . $album_id,
            'name' => 'addphoto'
        );
        // $gutterMenus[] = array(
        //     'label' => $this->translate("View Photo"),
        //     'url' => 'sitepage/photos/viewphoto/' . $page_id . '/' . $album_id . '/photo_id',
        //     'name' => 'viewphoto'
        // );

        return $gutterMenus;
        
    }

}