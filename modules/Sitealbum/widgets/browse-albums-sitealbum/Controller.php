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
class Sitealbum_Widget_browseAlbumsSitealbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    if (isset($params['is_ajax_load']))
      unset($params['is_ajax_load']);

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;

      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {

      if (!$this->_getParam('detactLocation', 1)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
      }
    }

    if (empty($this->view->is_ajax_load)) {
      $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (isset($cookieLocation['location']) && !empty($cookieLocation['location'])) {
        $this->view->is_ajax_load = 1;
      }
    }

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    //GET PARAMS
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', 250);
    $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
    $this->view->limit = $params['limit'] = $this->_getParam('limit', 12);
    $this->view->albumTitleTruncation = $params['albumTitleTruncation'] = $this->_getParam('albumTitleTruncation', 22);
    $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->albumInfo = $params['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName"));
    $this->view->infoOnHover = $params['infoOnHover'] = $this->_getParam('infoOnHover', 1);
    $this->view->photoWidth = $params['photoWidth'] = $this->_getParam('photoWidth', 195);
    $this->view->photoHeight = $params['photoHeight'] = $this->_getParam('photoHeight', 195);
    $this->view->marginPhoto = $params['margin_photo'] = $this->_getParam('margin_photo', 5);
    $this->view->customParams = $params['customParams'] = $this->_getParam('customParams', 5);
    $this->view->enablePhotoRotation = $params['enablePhotoRotation'] = $this->_getParam('enablePhotoRotation', 0);
    $sitealbum_browsealbum = Zend_Registry::isRegistered('sitealbum_browsealbum') ? Zend_Registry::get('sitealbum_browsealbum') : null;

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = $coreSettings->getSetting('sitealbum.location', 1);
    }

    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    if (isset($params['page']) && !empty($params['page']))
      $this->view->page = $page = $params['page'];
    else
      $this->view->page = $page = 1;

    if (!isset($params['category_id']))
      $params['category_id'] = 0;
    if (!isset($params['subcategory_id']))
      $params['subcategory_id'] = 0;
    $this->view->category_id = $params['category_id'];
    $this->view->subcategory_id = $params['subcategory_id'];

    //GET CATEGORYID AND SUBCATEGORYID
    $this->view->categoryName = '';
    if ($this->view->category_id) {
      $this->view->categoryName = Engine_Api::_()->getItem('album_category', $this->view->category_id)->category_name;
      $this->view->categoryObject = Engine_Api::_()->getItem('album_category', $this->view->category_id);
      if ($this->view->subcategory_id) {
        $this->view->categoryName = Engine_Api::_()->getItem('album_category', $this->view->subcategory_id)->category_name;
        $this->view->categoryObject = Engine_Api::_()->getItem('album_category', $this->view->subcategory_id);
      }
    }
    
    if(empty($sitealbum_browsealbum))
      return $this->setNoRender();

    if (empty($params['category_id'])) {
      $this->view->category_id = $params['category_id'] = $this->_getParam('category_id');
      $params['subcategory_id'] = $this->_getParam('subcategory_id');
    }

    $this->view->params = $params;

    if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    if (!$this->view->is_ajax_load)
      return;
    }

    //FORM GENERATION
    $form = new Sitealbum_Form_Search_Search();

    if (!empty($params)) {
      $form->populate($params);
    }

    $this->view->formValues = $form->getValues();

    $params = array_merge($params, $form->getValues());
    // FIND USERS' FRIENDS
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!empty($params['view_view']) && $params['view_view'] == 1) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }
      $params['users'] = $ids;
    }

    $customFieldValues = array();

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($params, $form->getFieldElements());

    $params['orderby'] = $orderBy = $request->getParam('orderby', null);
    if (empty($orderBy)) {
      $orderby = $this->_getParam('orderby', 'creation_date');
      if ($orderby == 'creationDate')
        $params['orderby'] = 'creation_date';
      elseif ($orderby == 'viewCount')
        $params['orderby'] = 'view_count';
      else
        $params['orderby'] = $orderby;
    }
    $this->view->params['orderby'] = $params['orderby'];

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    $this->view->normalPhotoWidth = $coreSettings->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreSettings->getSetting('sitealbum.last.photoid');
    $this->view->is_ajax = $this->_getParam('isajax', '');

    $params['notLocationPage'] = 1;
    $params['paginator'] = 1;

    if (!$this->view->detactLocation && empty($_GET['location']) && isset($params['location'])) {
      unset($params['location']);

      if (empty($_GET['latitude']) && isset($params['latitude'])) {
        unset($params['latitude']);
      }

      if (empty($_GET['longitude']) && isset($params['longitude'])) {
        unset($params['longitude']);
      }

      if (empty($_GET['Latitude']) && isset($params['Latitude'])) {
        unset($params['Latitude']);
      }

      if (empty($_GET['Longitude']) && isset($params['Longitude'])) {
        unset($params['Longitude']);
      }
    }

    if (!$this->view->detactLocation && empty($_GET['location']) && isset($params['location'])) {
      unset($params['location']);
    }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumPaginator($params, $customFieldValues);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($params['limit']);
    $paginator->setCurrentPageNumber($page);

    if ($this->view->enablePhotoRotation) {
      //CHANGING PHOTO ON MOUSEOVER WORK
      $this->view->photoPagination = $photoPagination = $this->_getParam('photoPagination', 0);
      if ($photoPagination) {
        $photo = Engine_Api::_()->getItem('album_photo', $this->_getParam('photo_id'));
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
          $this->view->nextPhoto = $nextPhoto = $photo->getNextPhoto();
          $this->view->photo_url = $nextPhoto->getPhotoUrl();
          $this->view->photo_id = $nextPhoto->getIdentity();
        } else {
          $this->view->nextPhoto = $nextPhoto = $photo->getNextCollectible();
          $this->view->photo_url = $nextPhoto->getPhotoUrl();
          $this->view->photo_id = $nextPhoto->getIdentity();
        }
        echo Zend_Json::encode(array('photo_url' => $this->view->photo_url, 'photo_id' => $this->view->photo_id, 'album_id' => $this->_getParam('album_id', 0)));
        exit();
      }
    }
   
    //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->totalPages = ceil(($this->view->totalCount) /$params['limit']);
    //END - SCROLLING WORK
  }

}