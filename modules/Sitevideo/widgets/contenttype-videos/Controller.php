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
class Sitevideo_Widget_ContenttypeVideosController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        
        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();
        $params = $this->_getAllParams();
        //GET VIDEO SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->moduleName = $moduleName = strtolower($subject->getModuleName());
        $this->view->getShortType = $getShortType = ucfirst($subject->getShortType());
        if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview', 'checked' => 'enabled'))))
                return $this->setNoRender();
        } else {
            if (!(Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName()), 'checked' => 'enabled'))))
                return $this->setNoRender();
        }
        $params['parent_type'] = $subject->getType();
        $params['parent_id'] = $subject->getIdentity();
        $this->view->canEdit = Engine_Api::_()->sitevideo()->isEditPrivacy($subject->getType(), $subject->getIdentity(), $subject);
        $this->view->canDelete = Engine_Api::_()->sitevideo()->canDeletePrivacy($subject->getType(), $subject->getIdentity(), $subject);

        if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore') {
            $this->view->user_layout = $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName . '.layoutcreate', 0);
            $isModuleOwnerAllow = 'is' . $getShortType . 'OwnerAllow';
            $this->_childCount = $this->view->videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');

            //START PACKAGE WORK
            if (Engine_Api::_()->$moduleName()->hasPackageEnable()) {

                if (!Engine_Api::_()->$moduleName()->allowPackageContent($subject->package_id, "modules", $moduleName . 'video')) {
                    return $this->setNoRender();
                }
            } else {
                $isOwnerAllow = Engine_Api::_()->$moduleName()->$isModuleOwnerAllow($subject, 'svcreate');

                if (empty($isOwnerAllow)) {
                    return $this->setNoRender();
                }
            }
            //END PACKAGE WORK

            $this->view->canCreate = $canCreate = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'svcreate');

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'view');
            if (empty($isManageAdmin)) {
                return $this->setNoRender();
            }

            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');

            if (empty($isManageAdmin)) {
                $this->view->canEdit = $canEdit = 0;
            } else {
                $this->view->canEdit = $canEdit = 1;
            }

            if (empty($canCreate) && empty($this->view->videoCount) && empty($canEdit) && !(Engine_Api::_()->$moduleName()->showTabsWithoutContent())) {
                return $this->setNoRender();
            }
        } else if ($moduleName == 'siteevent') {
            $this->view->user_layout = 0;
            $this->view->canEdit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit");
            $this->_childCount = $this->view->videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            $this->view->canCreate = $canCreate = Engine_Api::_()->siteevent()->allowVideo($subject, $viewer, $this->view->videoCount);

            if (empty($canCreate) && empty($this->view->videoCount) && empty($canEdit)) {
                return $this->setNoRender();
            }
        } else if ($moduleName == 'sitereview') {
            $this->view->user_layout = 0;
            //AUTHORIZATION CHECK
            $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');

            $this->_childCount = $this->view->videoCount = $count = $table
                    ->select()
                    ->from($table->info('name'), array('count(*) as count'))
                    ->where("parent_type = ?", 'sitereview_listing_' . $subject->listingtype_id)
                    ->where("parent_id =?", $subject->getIdentity())
                    ->query()
                    ->fetchColumn();

            $this->view->canCreate = $canCreate = Engine_Api::_()->sitereview()->allowVideo($subject, $viewer, $this->view->videoCount);
            $this->view->canEdit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");

            if (empty($canCreate) && empty($this->view->videoCount) && empty($canEdit)) {
                return $this->setNoRender();
            }

            $params['parent_type'] = 'sitereview_listing_' . $subject->listingtype_id;
            $params['parent_id'] = $subject->getIdentity();
       } else if ($moduleName == 'sitecrowdfunding') { 
            $this->view->canEdit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit");
            $this->_childCount = $this->view->videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            $this->view->canCreate = $canCreate = Engine_Api::_()->sitecrowdfunding()->allowVideo($subject, $viewer, $this->view->videoCount);

            if (empty($this->view->videoCount)) {
                return $this->setNoRender();
            }
        } else if ($moduleName == 'sitestoreproduct') { 
            $this->view->canEdit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit");
            $this->_childCount = $this->view->videoCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            $this->view->canCreate = $canCreate = Engine_Api::_()->sitestoreproduct()->allowVideo($subject, $viewer);

            if (empty($canCreate) && empty($this->view->videoCount) && empty($canEdit)) {
                return $this->setNoRender();
            }
        }

        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;
        $this->view->videoWidth = $params['videoWidth'] = $this->_getParam('videoWidth', 150);
        $this->view->videoHeight = $params['videoHeight'] = $this->_getParam('videoHeight', 150);
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', 150);
        $this->view->marginVideo = $params['margin_video'] = $this->_getParam('margin_video', 2);
        $this->view->videoOption = $params['videoOption'] = $this->_getParam('videoOption', array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'location', 'facebook', 'twitter', 'linkedin', 'googleplus'));
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->is_ajax = $params['is_ajax'] = $this->_getParam('is_ajax', false);
        if (empty($this->view->videoOption) || !is_array($this->view->videoOption)) {
            $this->view->videoOption = $params['videoOption'] = array();
        }
        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['videoWidth'] = $this->view->videoViewWidth;

        $thumbnailType = $this->findThumbnailType($videoSize, $this->view->videoViewWidth);
        $this->view->thumbnailType = $params['thumbnailType'] = $thumbnailType;

        $this->view->params = $params;
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($page);
        
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 17);
        //ADD COUNT TO TITLE
        if ($this->view->totalCount > 0) {
            $this->_childCount = $this->view->totalCount;
        }
    }

    function findThumbnailType($videoSize, $vWidth) {
        arsort($videoSize);
        $thumbnailType = 'thumb.normal';
        $count = 0;
        $bool = true;
        foreach ($videoSize as $key => $tSize) {
            $videoSizeDup[] = $key;
            if ($key != 'videoWidth' && $tSize == $vWidth) {
                $bool = false;
                $thumbnailType = $key;
            }
        }
        if ($bool) {
            foreach ($videoSize as $k => $tSize) {
                if ($k == 'videoWidth') {
                    $thumbnailType = isset($videoSizeDup[$count - 1]) ? $videoSizeDup[$count - 1] : $videoSizeDup[$count + 1];
                    break;
                }
                $count++;
            }
        }
        return $thumbnailType;
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
