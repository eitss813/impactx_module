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
class Sitevideo_Widget_ChannelPhotosController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }

        $this->view->itemCount = $this->_getParam('itemCount', 20);
        $this->view->width = $this->_getParam('width', 205);
        $this->view->height = $this->_getParam('height', 205);
        $this->view->showPhotosInJustifiedView = $params['showPhotosInJustifiedView'] = $this->_getParam('showPhotosInJustifiedView', 0);
        $this->view->maxRowHeight = $params['maxRowHeight'] = $this->_getParam('maxRowHeight', 0);
        $this->view->rowHeight = $params['rowHeight'] = $this->_getParam('rowHeight', 205);
        $this->view->margin = $params['margin'] = $this->_getParam('margin', 5);
        $this->view->lastRow = $params['lastRow'] = $this->_getParam('lastRow', 'nojustify');
        $enableSitealbum =  Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
        if(!$enableSitealbum)
            $this->view->showPhotosInJustifiedView = 0;
        //GET CHANNEL SUBJECT
        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET PAGINATOR
        $this->view->album = $album = $channel->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $sitevideoChannelPhotos = Zend_Registry::isRegistered('sitevideoChannelPhotos') ? Zend_Registry::get('sitevideoChannelPhotos') : null;
        $this->view->total_images = $total_images = $paginator->getTotalItemCount();
        $this->view->allowed_upload_photo = $uploadPhoto = Engine_Api::_()->authorization()->isAllowed($channel, $viewer, "photo");
        if (empty($sitevideoChannelPhotos)) {
            return $this->setNoRender();
        }
        if (empty($total_images) && !$uploadPhoto) {
            return $this->setNoRender();
        }

        //ADD COUNT TO TITLE
        if ($this->_getParam('titleCount', false) && $total_images > 0) {
            $this->_childCount = $total_images;
        }
        $params = $this->_getAllParams();
        $this->view->params = $params;
        $this->view->showContent = true;

        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            $this->view->showContent = false;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
                $this->view->showContent = true;
            } else {
                return;
            }
        }

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->view->itemCount);
        $this->view->can_edit = $canEdit = $channel->authorization()->isAllowed($viewer, "edit");
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
