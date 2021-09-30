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
class Sitepage_Widget_PhotorecentSitepageController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE PHOTOS IN THE STRIP
  public function indexAction() {  
  		
  	//HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
		$sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
		if (!$sitepagealbumEnabled) {
			return $this->setNoRender();
		}  	
		  	
  	//DON'T RENDER IF SUBJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SITEPAGE SUBJECT
    $this->view->sitepage_subject = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');    
    
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

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }

    //END MANAGE-ADMIN CHECK    
    
    //GET LIMIT
    $this->view->limit = $limit = $this->_getParam('itemCount', 7);
    
    //CHECK REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    
    //IF REQUEST IS AJAX THEN UPDATE PHOTO TABLE
    $phototable = Engine_Api::_()->getDbtable('photos', 'sitepage');
    if (!empty($is_ajax)) {
      $phototable->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
    }
    
  	//SET PAGE PHOTO PARAMS
    $paramsPhoto = array();	    
    $paramsPhoto['page_id'] = $subject->page_id;
    $paramsPhoto['photo_hide'] = 0;
    $paramsPhoto['file_id'] = $subject->photo_id;
    $paramsPhoto['orderby'] = 'creation_date DESC';
    $paramsPhoto['start'] = $limit;
     
    //MAKE PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);   
 
    //SET PAGE PHOTO PARAMS
    $paramsPhoto['photo_hide'] = 1;
    
    //GET PHOTOS COUNT
    $this->view->count =  Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
    
    //IF COUNT IS ZERO THEN NO RENDER
    if (!(count($this->view->paginator) > 0) && !($this->view->count) > 0) {
      return $this->setNoRender();
    }

    //SET PAGE PHOTO PARAMS
    $paramsPhoto['photo_hide'] = 0;
    
    //GET PHOTOS COUNT
    $this->view->row_count =  Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);
    
    //IF COUNT IS ZERO THEN NO RENDER
    if ($this->view->row_count == 0 && $this->view->can_edit == 0) {
      return $this->setNoRender();
    }

    //GETTING THE CURRENT TAB ID
    $this->view->currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}

?>