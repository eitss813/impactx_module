<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_ProjectPhotosController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
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
        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET PAGINATOR
        $this->view->album = $album = $project->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $this->view->total_images = $total_images = $paginator->getTotalItemCount();
        
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $this->view->allowed_upload_photo = $uploadPhoto = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo") ? 1 : 0;
        } else { 
            $this->view->allowed_upload_photo = $uploadPhoto = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo"); 
        } 

        if (empty($total_images)) {
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
        $this->view->can_edit = $canEdit = $project->authorization()->isAllowed($viewer, "edit");
    }

    public function getChildCount() {
        return $this->_childCount;
    }
}
