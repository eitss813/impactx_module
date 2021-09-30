<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Widget_AlbumContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
  
    //GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();
    $photosorder = $this->_getParam('photosorder', 1);

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $engineApiSitepage = Engine_Api::_()->sitepage();
    //GET ALBUM ID
    $this->view->album_id = $album_id = $request->getParam('album_id');

    //GET ALBUM ITEM
    $this->view->album = $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $request->getParam('tab');

    $getPackageEditPhoto = $engineApiSitepage->getPackageAuthInfo('sitepagealbum');

    //GET PAGE ID
    $page_id = $request->getParam('page_id');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'spcreate');
    if (empty($isManageAdmin)) {
      $this->view->canCreatePhoto = $canCreatePhoto = 0;
    } else {
      $this->view->canCreatePhoto = $canCreatePhoto = 1;
    }
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }
    if (empty($viewer->level_id)) {
      $this->view->level_id = $level_id = 0;
    }
    else {
      $this->view->level_id = $level_id = $viewer->level_id;
    }
    $this->view->allowView = false;
    if (!empty($viewer_id) && $level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
    } 

    //END MANAGE-ADMIN CHECK
    //CHECK THAT USER CAN UPLOAD PHOTO OR NOT
    $this->view->upload_photo = 0;

    if ($canCreatePhoto == 1 && ($engineApiSitepage->isPageOwner($sitepage) || $album->default_value == 1)) {
      $this->view->upload_photo = 1;
    }

    //GET CURRENT PAGE NUMBER
    $currentPageNumbers = $request->getParam('pages', 1);

    //SEND CURRENT PAGE NUMBER TO THE TPL
    $this->view->currentPageNumbers = $currentPageNumbers;

    //SEND PHOTOS PER PAGE TO THE TPL
    $this->view->photos_per_page = $photos_per_page = 10;

    //SET PAGE PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['page_id'] = $page_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
    $paramsPhoto['viewPage'] = 1;
    //FETCHING ALL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
    if (!empty($total_photo)) {
      if (Engine_Api::_()->core()->hasSubject()) {
        Engine_Api::_()->core()->clearSubject();
      }
      Engine_Api::_()->core()->setSubject($album);
    }

    //SET DEFAULT ALBUM VALUE
    $this->view->default_value = $album->default_value;

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $page_id;
    $paramsAlbum['viewPage'] = 1;
    //GET ALBUM COUNT
    $this->view->album_count =  Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);
    
    //MAKING PAGINATION 
    $page_vars = $engineApiSitepage->makePage($total_photo, $photos_per_page, $currentPageNumbers);
    $page_array = array();
    for ($x = 0; $x <= $page_vars[2] - 1; $x++) {
      if ($x + 1 == $page_vars[1]) {
        $link = "1";
      } else {
        $link = "0";
      }
      $page_array[$x] = array('page' => $x + 1, 'link' => $link);
    }
    $this->view->pagearray = $page_array;
    $this->view->maxpage = $page_vars[2];
    $this->view->pstart = 1;

    //GET TOTAL IMAGES
    $this->view->total_images = empty($getPackageEditPhoto) ? null : $total_photo;

    //SET PAGE PHOTO PARAMS
    $paramsPhoto['start'] = $photos_per_page;
    $paramsPhoto['end'] = $page_vars[0];
    $paramsPhoto['viewPage'] = 1;
    $paramsPhoto['photosorder'] = $photosorder;
    $paramsPhoto['widgetName'] = 'Album Content';
    
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $paramsPhoto['albumviewPage'] = 1;
    }
    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $this->view->photos = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);

    //INCREMENT VIEWS
    if ($album && !$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $album->view_count++;
    }

    //SAVE
    $album->save();

    //START: "SUGGEST TO FRIENDS" LINK WORK
    $page_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
		if( !empty($is_suggestion_enabled) ) {
			$is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
			$isSupport = Engine_Api::_()->getApi('suggestion', 'sitepage')->isSupport();
			//HERE WE ARE DELETE THIS ALBUM SUGGESTION IF VIEWER HAVE
			if (!empty($is_moduleEnabled)) {
				Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer_id, 'page_album', $request->getParam('album_id'), 'page_album_suggestion');
			}

			$SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
			$versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
			if( $versionStatus >= 0 ){ 
				$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepagealbum', $request->getParam('album_id'), 1,null);
				if (!empty($modContentObj)) {
					$contentCreatePopup = @COUNT($modContentObj);
				}
			}

			if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1)) {
				if ($sitepage->expiration_date <= date("Y-m-d H:i:s")) {
					$page_flag = 1;
				}
			}
			if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitepage->closed) && !empty($sitepage->approved) && empty($sitepage->declined) && !empty($sitepage->draft) && empty($page_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
				$this->view->albumSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'album_sugg_link');
			}
		}
    //END: "SUGGEST TO FRIENDS" LINK WORK    
  }

}
?>