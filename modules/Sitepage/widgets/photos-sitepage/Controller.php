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
class Sitepage_Widget_PhotosSitepageController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE ALBUMS AND PHOTOS
  public function indexAction() {

    //HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if (!$sitepagealbumEnabled) {
      return $this->setNoRender();
    }

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
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

    //GET SITEPAGE SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
    	$this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    }
    else {
      $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //START PACKAGE WORK
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
      if (empty($isPageOwnerAllow)) {
        //return $this->setNoRender();
      }
    }
    //END PACKAGE WORK

    //TOTAL ALBUMS
    $albumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
    $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');

//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $canEdit = 0;
    } else {
      $this->view->can_edit = $canEdit = 1;
    }

    if (empty($photoCreate) && empty($albumCount) && empty($canEdit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    //END MANAGE-ADMIN CHECK

    //GET WHICH LAYOUT IS SET BY THE ADMIN
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

    //GET THIRD TYPE LAYOUT IS SET OR NOT
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage->page_id);

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

    //GET TAB ID
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $sitepage->page_id, $layout);

    //GET CURRENT TAB ID
    $this->view->module_tabid = $currenttabid = $zendRequest->getParam('tab', null);

    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);

    //SHOW TOP TITLE
    $this->view->showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);

    //CHECK REQUEST IS AJAX OR NOT OR CURRENT TAB ID OR LAYOUT
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {

      $this->view->identity_temp = $zendRequest->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

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
      $this->view->albums_order = $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1);

      //GET CURRENT PAGE NUMBER OF ALBUM
      $currentAlbumPageNumbers = $this->_getParam('page', 1);

      //SEND CURRENT PAGE NUMBER OF ALBUM TO THE TPL
      $this->view->currentAlbumPageNumbers = $currentAlbumPageNumbers;

      //SET ALBUMS PARAMS
      $paramsAlbum = array();
      $paramsAlbum['page_id'] = $sitepage->page_id;

      //GET ALBUM COUNT
      $this->view->album_count = $album_count = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);

      //START ALBUMS PAGINATION
      $pages_vars = Engine_Api::_()->sitepage()->makePage($album_count, $albums_per_page, $currentAlbumPageNumbers);
      $pages_array = Array();
      for ($y = 0; $y <= $pages_vars[2] - 1; $y++) {
        if ($y + 1 == $pages_vars[1]) {
          $links = "1";
        } else {
          $links = "0";
        }
        $pages_array[$y] = Array('pages' => $y + 1,
            'links' => $links);
      }

      $this->view->pagesarray = $pages_array;
      $this->view->maxpages = $pages_vars[2];
      $this->view->pstarts = 1;
      //END ALBUMS PAGINATION

      //SET ALBUMS PARAMS
      $paramsAlbum['start'] = $albums_per_page;
      $paramsAlbum['end'] = $pages_vars[0];
      if(empty($albums_order)) {
        $paramsAlbum['orderby'] = 'album_id ASC';
      } else {
        $paramsAlbum['orderby'] = 'album_id DESC';
      }
      $paramsAlbum['getSpecialField'] = 0;

      $fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);
      if (!empty($fecthAlbums)) {
        $this->view->album = $this->view->paginator = $fecthAlbums;
      }

      //SET PHOTOS PARAMS
      $paramsPhoto = array();
      $paramsPhoto['page_id'] = $sitepage->page_id;
      $paramsPhoto['user_id'] = $sitepage->owner_id;
      $paramsPhoto['album_id'] = $this->view->default_album_id = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($sitepage->page_id)->album_id;

      //FETCHING ALL PHOTOS
      $this->view->total_images = $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);

      //SEND CURRENT PAGE NUMBER TO THE TPL
      $this->view->currentPageNumbers = $currentPageNumbers = $this->_getParam('pages', 1);

      //SEND PHOTOS PER PAGE TO THE TPL
      $this->view->photos_per_page = $photos_per_page = $zendRequest->getParam('itemCount_photo', 100);

      if(empty($photos_per_page)) {
        $this->view->photos_per_page = $photos_per_page = 100;
      }

      //START PHOTOS PAGINATION
      $page_vars = Engine_Api::_()->sitepage()->makePage($total_photo, $photos_per_page, $currentPageNumbers);
      $paramsPhoto['start'] = $photos_per_page;
      $paramsPhoto['end'] = $page_vars[0];
      $paramsPhoto['widgetName'] = 'Photos By Others';
      if(empty($albums_order)) {
        $paramsPhoto['photosorder'] = 'album_id ASC';
      } else {
        $paramsPhoto['photosorder'] = 'album_id DESC';
      }
      $this->view->paginators = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);
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
      $this->view->pagearray = $page_array;
      $this->view->maxpage = $page_vars[2];
      $this->view->pstart = 1;
      //END PHOTOS PAGINATION
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
    }

    //SET PHOTOS PARAMS
    $paramsPhoto = array();
    $paramsPhoto['page_id'] = $sitepage->page_id;

    //SET COUNT TO THE TITLE
    $this->_childCount = $this->view->locale()->toNumber(Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto));

   // if($fecthAlbums && empty($canEdit) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1)) {
      // return $this->setNoRender();
    //}


    // video hide start
     $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
     $page_id = $sitepage->getIdentity();
     $viewer = Engine_Api::_()->user()->getViewer();
     $viewer_id = $viewer->getIdentity();

    $this->view->videos = $videos = array();
    $this->view->integratedWithVideo = false;

    $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');

    $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage'));

    if ($sitevideoEnabled && $isIntegrated) {
      $params = array();
      $params['parent_type'] = $sitepage->getType();
      $params['parent_id'] = $sitepage->getIdentity();
      $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
      $this->view->integratedWithVideo = true;
    } else {
      if (Engine_Api::_()->sitecrowdfunding()->enableVideoPlugin()) {
        $this->view->videos = $videos = array();
      }
    }

    $this->view->count = count($videos);

    // video hide end



  }

  public function getChildCount() {
    return $this->_childCount;
  }

}

?>
