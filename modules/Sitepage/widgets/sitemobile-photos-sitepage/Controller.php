<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_SitemobilePhotosSitepageController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE ALBUMS AND PHOTOS
  public function indexAction() {

    //HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if (!$sitepagealbumEnabled) {
      return $this->setNoRender();
    }

    //GET HOW MANY ALBUMS DO YOU WANT SHOW ON PER PAGE
    $this->view->itemCount = $albums_per_page = $this->_getParam('itemCount', 10);

    //GET HOW MANY PHOTOS DO YOU WANT SHOW ON PER PAGE
    $this->view->itemCount_photo = $this->_getParam('itemCount_photo', 100);

    //ALBUMS ORDER
    $this->view->albums_order = $this->_getParam('albumsorder', 1);

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET PAGE ID
    $page_id = $sitepage->page_id;

    //START PACKAGE WORK
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE WORK

    //TOTAL ALBUMS
    $albumCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepage', 'albums');     
    $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $canEdit = 0;
    } else {
      $this->view->can_edit = $canEdit = 1;
    }
    
    if (empty($photoCreate) && empty($albumCount) && empty($canEdit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
		if ($isManageAdmin || $canEdit) {
			$this->view->allowed_upload_photo = 1;
		} else {
			$this->view->allowed_upload_photo = 0;
		}

    //ALBUMS PER PAGE
		$this->view->albums_per_page = $albums_per_page = $zendRequest->getParam('itemCount', 10);

		if(empty($albums_per_page)) {
			$this->view->albums_per_page = $albums_per_page = 10;
		}

		//ALBUMS ORDER
		$this->view->albums_order = $albums_order = $zendRequest->getParam('albumsorder', 1);

		//GET CURRENT PAGE NUMBER OF ALBUM
		$currentAlbumPageNumbers = $page = $this->_getParam('page', 1);

		//SEND CURRENT PAGE NUMBER OF ALBUM TO THE TPL
		$this->view->currentAlbumPageNumbers = $currentAlbumPageNumbers;

		//SET ALBUMS PARAMS
		$paramsAlbum = array();
		$paramsAlbum['page_id'] = $page_id;

		//GET ALBUM COUNT
		$this->view->album_count = $album_count = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);
		
		//SET ALBUMS PARAMS
		if(empty($albums_order)) {
			$paramsAlbum['orderby'] = 'album_id ASC';
		} else {
			$paramsAlbum['orderby'] = 'album_id DESC';
		}
		$paramsAlbum['getSpecialField'] = 0;

		$fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);

		$album = $this->view->album = $paginator = $this->view->paginator = $fecthAlbums;
		$this->view->paginator = $paginator->setItemCountPerPage($albums_per_page);
		$this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));

		//SET PHOTOS PARAMS
		$paramsPhoto = array();
		$paramsPhoto['page_id'] = $page_id;
		$paramsPhoto['user_id'] = $sitepage->owner_id;
		$paramsPhoto['album_id'] = $this->view->default_album_id = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($page_id)->album_id;
		
		//FETCHING ALL PHOTOS
		$this->view->total_images = $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);

		//SEND PHOTOS PER PAGE TO THE TPL
		$this->view->photos_per_page = $photos_per_page = $zendRequest->getParam('itemCount_photo', 100);

		if(empty($photos_per_page)) {
			$this->view->photos_per_page = $photos_per_page = 100;
		}

		$this->view->paginators = $paginators = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);
		$this->view->paginators = $paginators->setItemCountPerPage($photos_per_page);
		$this->view->paginators->setCurrentPageNumber($this->_getParam('pages', 1));
		//SET PHOTOS PARAMS
		$paramsPhoto = array();
		$paramsPhoto['page_id'] = $sitepage->page_id;

    //SET COUNT TO THE TITLE
    $this->_childCount = $this->view->locale()->toNumber(Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto));

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}