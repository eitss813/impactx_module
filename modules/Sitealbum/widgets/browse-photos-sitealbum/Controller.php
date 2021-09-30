<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_BrowsePhotosSitealbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
      }
    }
    $param = array();
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 200);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 200);
    $this->view->photoTitleTruncation = $param['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 22);
    $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 50);
    $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("photoTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
    $this->view->showPhotosInJustifiedView = $param['showPhotosInJustifiedView'] = $this->_getParam('showPhotosInJustifiedView', 0); 
    $this->view->maxRowHeight = $param['maxRowHeight'] = $this->_getParam('maxRowHeight',0); 
    $this->view->rowHeight = $param['rowHeight'] = $this->_getParam('rowHeight',205);   
    $this->view->margin = $param['margin'] = $this->_getParam('margin',5);  
    $this->view->lastRow = $param['lastRow'] = $this->_getParam('lastRow', 'nojustify');  
    $this->view->showContent = $param['show_content'] = $this->_getParam('show_content', 2);
     $this->view->itemCountPerPage = $param['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 4);
    $param['category_id'] = $this->_getParam('category_id');
    $param['subcategory_id'] = $this->_getParam('subcategory_id');
    $this->view->page = $param['page'] = $this->_getParam('page', 1);
    $this->view->detactLocation = $param['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreApi->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) {
      $param['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $param['latitude'] = $this->_getParam('latitude', 0);
      $param['longitude'] = $this->_getParam('longitude', 0);
    }
    
    if (!$this->view->is_ajax_load)
      return;

    $param = array_merge($param,$request->getParams());
    //FORM GENERATION
    $form = new Sitealbum_Form_Search_PhotoSearch();
    if (!empty($param)) {
      $form->populate($param);
    }
    $this->view->formValues = $form->getValues();
    $param['orderby'] = $orderBy = $request->getParam('orderby', null);
    if (empty($orderBy)) {
      $orderby = $this->_getParam('orderby', 'creation_date');
      if ($orderby == 'creationDate')
        $param['orderby'] = 'creation_date';
      elseif ($orderby == 'viewCount')
        $param['orderby'] = 'view_count';
      elseif ($orderby == 'takenDate')
        $param['orderby'] = 'taken_date';
      else
        $param['orderby'] = $orderby;
    }
    //$this->view->param= ['orderby'] = $param['orderby'];
    $this->view->params = $param;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySearching($param);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $photoType = $coreApi->getSetting('sitealbum.phototype', null);

    $featuredPhoto = $coreApi->getSetting('sitealbum.featuredalbum', null);
    // Do not render if nothing to show
    if (empty($photoType)) {
      return $this->setNoRender();
    }

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($param['itemCountPerPage']);
    $paginator->setCurrentPageNumber($param['page']);
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');
    
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();

  }

}