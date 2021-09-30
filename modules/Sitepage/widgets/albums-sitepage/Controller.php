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
class Sitepage_Widget_AlbumsSitepageController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR SHOWING THE RANDOM ALBUMS AND PHOTOS BY OTHERS 
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

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

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

//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }
//    //END MANAGE-ADMIN CHECK  
    
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $sitepage->page_id;  

    //GET ALBUMS COUNT
    $this->view->albumcount = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
    
    //SET ALBUMS PARAMS
    $paramsAlbum['orderby'] = 'RAND()'; 
    $paramsAlbum['start'] = 2; 
    $paramsAlbum['getSpecialField'] = 0;
    
    //FETCH ALBUMS
		$this->view->paginator = $fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum, null, array('album_id', 'photo_id', 'title', 'creation_date'));           
    
    //IF COUNT IS ZERO THEN NO RENDER
    if ($this->view->albumcount <= 0) {
      return $this->setNoRender();
    }

    //SET PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['page_id'] = $sitepage->page_id;
    $paramsPhoto['user_id'] = $sitepage->owner_id;
    $paramsPhoto['album_id'] = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($sitepage->page_id)->album_id;;

    //GET TOTAL PHOTOS BY OTHERS
    $this->view->totalphotosothers = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
    
    //SET PHOTO PARAMS
    $paramsPhoto['orderby'] = 'RAND()'; 
    $paramsPhoto['start'] = 4;  
       
    //GET ALL PHOTOS
    $this->view->paginators = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);
    
    //SEND CURRENT TAB ID TO THE TPL
    $this->view->tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $sitepage->page_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0));
  }

}

?>