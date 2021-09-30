<?php

class Sitepagevideo_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $this->respondWithError('unauthorized');
        }

        //CHECK SUBJECT IS EXIST OR NOT IF NOT EXIST THEN SET ACCORDING TO THE PAGE ID AND PHOTO ID
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
                    null !== ($video = Engine_Api::_()->getItem('sitepagevideo_video', $video_id))) {
                Engine_Api::_()->core()->setSubject($video);
            } else if (0 !== ($page_id = (int) $this->_getParam('page_id')) &&
                    null !== ($sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id))) {
                Engine_Api::_()->core()->setSubject($sitepage);
            }
        }

        //GET PAGE ID
        $page_id = $this->_getParam('page_id');

        //PACKAGE BASE PRIYACY START    
        if (!empty($page_id)) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
            if (!empty($sitepage)) {
                if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
                        $this->respondWithError('unauthorized');
                    }
                } else {
                    $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                    if (empty($isPageOwnerAllow)) {
                        $this->respondWithError('unauthorized');
                    }
                }
            }
        }
        // PACKAGE BASE PRIYACY END
        else {
            if (Engine_Api::_()->core()->hasSubject() != null) {
                $video = Engine_Api::_()->core()->getSubject();
                $page_id = $video->page_id;
            }
        }
    }

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $page_id = $sitepage->page_id;
        $params = array();
        $params['page_id'] = $page_id;
        if ($this->_getParam('page'))
            $params['page'] = $this->_getParam('page');
        else
            $params['page'] = 1;

        if ($this->_getParam('limit'))
            $params['limit'] = $this->_getParam('limit');
        else
            $params['limit'] = 10;

        if ($this->_getParam('search'))
            $params['search'] = $this->_getParam('search');

        if ($this->_getParam('myvideos'))
            $params['user_id'] = $viewer_id;

        if ($this->_getParam('orderby'))
            $params['orderby'] = $this->_getParam('orderby');

        $pagesobj = $paginator = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getSitepagevideosPaginator($params);
        $response['totalitemcount'] = $paginator->getTotalItemCount();
        // Set the 'side menus' for 'My Pages' 
        $tempResponse = array();
        if ($pagesobj->getTotalItemCount() > 0) {
            foreach ($pagesobj as $pageObj) {
                $value = $pageObj->toArray();
                $ownerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($pageObj, true);
                $videoImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($pageObj, false);
                $tempMenu = array();

                if ($pageObj->authorization()->isAllowed($viewer, 'view')) {
                    $tempMenu[] = array(
                        'label' => $this->translate('View Video'),
                        'name' => 'view',
                        'url' => 'sitepage/video/view/' . $page_id . '/' . $pageObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }

                if ($pageObj->authorization()->isAllowed($viewer, 'edit')) {
                    $tempMenu[] = array(
                        'label' => $this->translate('Edit Video'),
                        'name' => 'edit',
                        'url' => 'sitepage/video/edit/' . $page_id . '/' . $pageObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );

                    $tempMenu[] = array(
                        'label' => $this->translate('Delete Video'),
                        'name' => 'delete',
                        'url' => 'sitepage/video/delete/' . $page_id . '/' . $pageObj->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }


                $tempMenu[] = array(
                    'label' => $this->translate('Highlight Video'),
                    'name' => 'highlight',
                    'url' => 'sitepage/video/highlight/' . $page_id . '/' . $pageObj->getIdentity(),
                    'urlParams' => array(
                    )
                );

                $tempMenu[] = array(
                    'label' => $this->translate('Make Featured'),
                    'name' => 'featured',
                    'url' => 'sitepage/video/featured/' . $page_id . '/' . $pageObj->getIdentity(),
                    'urlParams' => array(
                    )
                );

                $tempMenu[] = array(
                    'label' => $this->translate('Comment on Video'),
                    'name' => 'comment',
                    'url' => 'sitepage/video/comment/' . $page_id . '/' . $pageObj->getIdentity(),
                    'urlParams' => array(
                    )
                );

                $value["menu"] = $tempMenu;

                $value["owner_title"] = $pageObj->getOwner()->getTitle();

                $isAllowedView = $pageObj->authorization()->isAllowed($viewer, 'view');
                $value["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $pageObj->authorization()->isAllowed($viewer, 'edit');
                if (isset($params['is_edit']) && !empty($params['is_edit']))
                    $value["edit"] = empty($isAllowedEdit) ? 0 : 1;

                $isAllowedDelete = $pageObj->authorization()->isAllowed($viewer, 'delete');

                if (isset($params['is_delete']) && !empty($params['is_delete']))
                    $value["delete"] = empty($isAllowedDelete) ? 0 : 1;

                $value = array_merge($value, $ownerImages);
                $value = array_merge($value, $videoImages);

                $tempResponse[] = $value;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }
        $this->respondWithSuccess($response, true);
    }

    public function viewAction() {
        // this view is not opening properly
        // echo "database";
        // die;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
            $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        } else {
            $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');

        if (!$video)
            $this->respondWithError('no_record');

        $response['video'] = $video->toArray();


        // package level checks
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
            if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
                $this->respondWithError('unauthorized');
            }
        } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
            if (empty($isPageOwnerAllow)) {
                $this->respondWithError('unauthorized');
            }
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }

        if ($viewer_id == $video->owner_id)
            $gutterMenus = $this->_gutterMenus();

        $response = array();
        $response['data'] = $video->toArray();
        $rating_count = Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->ratingCount($video->getIdentity());
        $response['data']['rating_count'] = $rating_count;
        $response['gutterMenus'] = $gutterMenus;
        $this->respondWithSuccess($response, true);
    }

    public function _gutterMenus() {
        $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $tempMenu[] = array(
            'label' => $this->translate('View Video'),
            'name' => 'view',
            'url' => 'sitepagevideo/view/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );
        $tempMenu[] = array(
            'label' => $this->translate('Edit Video'),
            'name' => 'edit',
            'url' => 'sitepagevideo/edit/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );

        $tempMenu[] = array(
            'label' => $this->translate('Highlight Video'),
            'name' => 'highlight',
            'url' => 'sitepagevideo/highlight/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );
        $tempMenu[] = array(
            'label' => $this->translate('Make Featured'),
            'name' => 'featured',
            'url' => 'sitepage/featured/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );
        $tempMenu[] = array(
            'label' => $this->translate('Comment on Video'),
            'name' => 'comment',
            'url' => 'sitepagevideo/comment/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );
        $tempMenu[] = array(
            'label' => $this->translate('Delete Video'),
            'name' => 'delete',
            'url' => 'sitepagevideo/delete/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );
        $tempMenu[] = array(
            'label' => $this->translate("Like Video"),
            'name' => 'like',
            'url' => 'sitepagevideo/like/' . $page_id . '/' . $pageObj->getIdentity(),
            'urlParams' => array(
            )
        );

        if (Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->checkRated($video->getIdentity(), $viewer_id)) {
            $tempMenu[] = array(
                'label' => $this->translate("Rate This Video"),
                'name' => 'rating',
                'url' => 'sitepagevideo/like/' . $page_id . '/' . $pageObj->getIdentity(),
                'urlParams' => array(
                    'rating'
                )
            );
        }
        return $tempMenu;
    }

    public function searchFormAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // if (!Engine_Api::_()->authorization()->isAllowed('sitepagevideo_video', $viewer, 'view'))
        //     $this->respondWithError('unauthorized');

        $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitepagevideo')->getVideoBrowseSearchForm();

        $responseData['form'] = $response;

        // this is not working for some reason 
        // first task to do tomorrow
        $this->respondWithSuccess($responseData, true);
    }

}
