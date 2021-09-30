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
class Sitepage_Widget_PhotocommentSitepageController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE MOST COMMENTED PHOTOS ON PAGE PROFILE PAGE 
  public function indexAction() {
  	
  	//HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
		$sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
		if (!$sitepagealbumEnabled) {
			return $this->setNoRender();
		}

    //GET SITEPAGE SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    //PACKAGE WORK START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagealbum")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'spcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE WORK END    
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    //SEARCH PARAMETER
    $params = array();
		$params['page_id'] = $subject->page_id;
		$params['orderby'] = 'comment_count DESC';
		$params['zero_count'] = 'comment_count';
		$params['limit'] = $this->_getParam('itemCount', 4);
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitepage');
		//MAKE PAGINATOR
    $this->view->paginator = $paginator = $photoTable->widgetPhotos($params);    

    $this->view->count =  $photoTable->countTotalPhotos($params);
    
    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
    
    //SHOWS PHOTOS IN THE LIGHTBOX
    //$this->view->showLightBox = Engine_Api::_()->seaocore()->showLightBoxPhoto();
  }

}

?>