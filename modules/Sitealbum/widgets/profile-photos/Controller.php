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
class Sitealbum_Widget_ProfilePhotosController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      $this->getElement()->removeDecorator('Container');
    } else {
      $this->getElement()->removeDecorator('Title');
      $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
    }
    $this->view->is_ajax_load = true;
    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    if ($subject->getType() == 'album') {
      $this->view->subject = $subject = $subject->getOwner();
    }
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

    $param = array();
    $this->view->albumInfo = $param['albumInfo'] = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName"));
    $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("likeCommentStrip"));
    $this->view->showaddphoto = $param['showaddphoto'] = $this->_getParam('showaddphoto', 1);
    $this->view->showviewphotolink = $param['showviewphotolink'] = $this->_getParam('showviewphotolink', 1);
    $this->view->margin_photo = $param['margin_photo'] = $this->_getParam('margin_photo', 5);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 205);
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 205);
    $this->view->infoOnHover = $params['infoOnHover'] = $this->_getParam('infoOnHover', 1);
    $this->view->albumPhotoHeight = $param['albumPhotoHeight'] = $this->_getParam('albumPhotoHeight', 195);
    $this->view->albumPhotoWidth = $param['albumPhotoWidth'] = $this->_getParam('albumPhotoWidth', 195);
    $this->view->albumColumnHeight = $param['albumColumnHeight'] = $this->_getParam('albumColumnHeight', 250);
    $this->view->photoColumnHeight = $param['photoColumnHeight'] = $this->_getParam('photoColumnHeight', 205);
    $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
    $this->view->truncationLocation = $param['truncationLocation'] = $this->_getParam('truncationLocation', 22);
    $this->view->showPhotosInLightbox = $param['showPhotosInLightbox'] = $this->_getParam('showPhotosInLightbox', 1); 
    $this->view->showPhotosInJustifiedView = $param['showPhotosInJustifiedView'] = $this->_getParam('showPhotosInJustifiedView', 0); 
    $this->view->maxRowHeight = $param['maxRowHeight'] = $this->_getParam('maxRowHeight',0); 
    $this->view->rowHeight = $param['rowHeight'] = $this->_getParam('rowHeight',205);   
    $this->view->margin = $param['margin'] = $this->_getParam('margin',5);  
    $this->view->lastRow = $param['lastRow'] = $this->_getParam('lastRow', 'nojustify');  
    $this->view->limit = $param['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 8);
    $param['category_id'] = $this->_getParam('category_id');
    $param['subcategory_id'] = $this->_getParam('subcategory_id');

    $this->view->selectDispalyTabs = $param['selectDispalyTabs'] = $this->_getParam('selectDispalyTabs', array('photosofyou', 'yourphotos', 'albums', 'likesphotos'));

    if (empty($this->view->selectDispalyTabs)) {
      return $this->setNoRender();
    }

    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->view->viewType = $viewtype = 'albums';
    }else{
    $this->view->viewType = $viewtype = $this->_getParam('viewType', '');
    if (empty($viewtype))
      $this->view->viewType = $viewtype = $this->view->selectDispalyTabs[0];
    }

    $front = Zend_Controller_Front::getInstance();

    $this->view->isajax = $this->_getParam('isajax', 0);
    $this->view->page = $this->_getParam('page', 1);

    if ($this->view->is_ajax_load) {
      $param['controllers'] = $this->_getParam('controllers');
      $param['actions'] = $this->_getParam('actions');
    } else {
      $param['controllers'] = $controller = $front->getRequest()->getControllerName();
      $param['actions'] = $action = $front->getRequest()->getActionName();
    }
    $param['defaultAlbumsShow'] = 1;

    $this->view->param = $param;
    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    $albumTable = Engine_Api::_()->getItemTable('album');
    $photoTable = Engine_Api::_()->getDbTable('photos', 'sitealbum');
    $totalPhotosofyouCount = 0;
    $totalYourphotosCount = 0;
    $totalAlbumsCount = 0;
    $totalLikesphotosCount = 0;

    // Get paginator
    if ($viewtype == 'albums') { 
      if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
        $select = $albumTable->getUserAlbums($subject, $param);
      } else {
        $select = Engine_Api::_()->getApi('core', 'album')->getAlbumSelect(array('owner' => $subject, 'search' => 1));
      }

      if ($viewer->getIdentity() != $subject->getIdentity() && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
        $select->where('type IS NULL');
      //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select);
      $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $this->view->totalAlbumsCount = $totalAlbumsCount = $paginator->getTotalItemCount();

      if (in_array('photosofyou', $param['selectDispalyTabs'])) {
        $paginator = $photoTable->getTaggedInOthersPhotos(array_merge($param, array('owner_id' => $subject->getIdentity())));
        $this->view->totalPhotosofyouCount = $totalPhotosofyouCount = $paginator->getTotalItemCount();
      }

      if (in_array('yourphotos', $param['selectDispalyTabs'])) {
        $paginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'ownerObject' => $subject)));
        $this->view->totalYourphotosCount = $totalYourphotosCount = $paginator->getTotalItemCount();
      }

      if (in_array('likesphotos', $param['selectDispalyTabs'])) {
        $paginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'tab' => 'likesphotos', 'ownerObject' => $subject)));
        $this->view->totalLikesphotosCount = $totalLikesphotosCount = $paginator->getTotalItemCount();
      }
    } elseif ($viewtype == 'photosofyou') {
      $this->view->paginator = $paginator = $photoTable->getTaggedInOthersPhotos(array_merge($param, array('owner_id' => $subject->getIdentity())));
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));

      $this->view->totalPhotosofyouCount = $totalPhotosofyouCount = $paginator->getTotalItemCount();

      $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
      if ($this->view->showLightBox) {
        $this->view->params = $params = array('type' => 'tagged', 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'count' => $totalPhotosofyouCount, 'owner_id' => $subject->getIdentity());
      }

      if (in_array('albums', $param['selectDispalyTabs'])) {
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
          $select = $albumTable->getUserAlbums($subject, $param);
        } else {
          $select = Engine_Api::_()->getApi('core', 'album')->getAlbumSelect(array('owner' => $subject, 'search' => 1));
        }
        if ($viewer->getIdentity() != $subject->getIdentity() && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
          $select->where('type IS NULL');
        // $select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        $albumPaginator = Zend_Paginator::factory($select);
        $this->view->totalAlbumsCount = $totalAlbumsCount = $albumPaginator->getTotalItemCount();
      }

      if (in_array('yourphotos', $param['selectDispalyTabs'])) {
        $yourPhotoPaginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'ownerObject' => $subject)));
        $this->view->totalYourphotosCount = $totalYourphotosCount = $yourPhotoPaginator->getTotalItemCount();
      }

      if (in_array('likesphotos', $param['selectDispalyTabs'])) {
        $paginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'tab' => 'likesphotos', 'ownerObject' => $subject)));
        $this->view->totalLikesphotosCount = $totalLikesphotosCount = $paginator->getTotalItemCount();
      }

      if (!$this->view->isajax && empty($totalPhotosofyouCount) && in_array('albums', $param['selectDispalyTabs']) && ($totalAlbumsCount > 0)) {
        $this->view->viewType = 'albums';
        $this->view->paginator = $albumPaginator;
        $albumPaginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
        $albumPaginator->setCurrentPageNumber($this->_getParam('page', 1));
      }
    } elseif ($viewtype == 'yourphotos') {
      $this->view->paginator = $paginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'ownerObject' => $subject)));

      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));

      $this->view->totalYourphotosCount = $totalYourphotosCount = $paginator->getTotalItemCount();
      $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
      if ($this->view->showLightBox) {
        $this->view->params = $params = array('type' => 'yourphotos', 'count' => $totalYourphotosCount, 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'owner_id' => $subject->getIdentity());
      }

      if (in_array('albums', $param['selectDispalyTabs'])) {
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
          $select = $albumTable->getUserAlbums($subject, $param);
        } else {
          $select = Engine_Api::_()->getApi('core', 'album')->getAlbumSelect(array('owner' => $subject, 'search' => 1));
        }

        if($viewer->getIdentity() != $subject->getIdentity()) {
          if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1)) {
            $select->where('type IS NULL');
          }
        }       
        
        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        //echo $select;die;
        $paginator = Zend_Paginator::factory($select);
        $this->view->totalAlbumsCount = $totalAlbumsCount = $paginator->getTotalItemCount();
      }

      if (in_array('photosofyou', $param['selectDispalyTabs'])) {
        $photosofYouPaginator = $photoTable->getTaggedInOthersPhotos(array_merge($param, array('owner_id' => $subject->getIdentity())));
        $this->view->totalPhotosofyouCount = $totalPhotosofyouCount = $photosofYouPaginator->getTotalItemCount();
      }
      if (in_array('likesphotos', $param['selectDispalyTabs'])) {
        $paginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'tab' => 'likesphotos', 'ownerObject' => $subject)));
        $this->view->totalLikesphotosCount = $totalLikesphotosCount = $paginator->getTotalItemCount();
      }
      if (!$this->view->isajax && empty($totalYourphotosCount)) {
        if (in_array('photosofyou', $param['selectDispalyTabs']) && ($totalPhotosofyouCount > 0)) {
          $this->view->viewType = 'photosofyou';
          $this->view->paginator = $photosofYouPaginator;
          $photosofYouPaginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
          $photosofYouPaginator->setCurrentPageNumber($this->_getParam('page', 1));
          if ($this->view->showLightBox) {
            $this->view->params = $params = array('type' => 'tagged', 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'count' => $totalPhotosofyouCount, 'owner_id' => $subject->getIdentity());
          }
        }
      }
    } elseif ($viewtype == 'likesphotos') {
      $this->view->paginator = $paginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'tab' => 'likesphotos', 'ownerObject' => $subject)));
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));

      $this->view->totalLikesphotosCount = $totalLikesphotosCount = $paginator->getTotalItemCount();
      $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
      if ($this->view->showLightBox) {
        $this->view->params = $params = array('type' => 'likesphotos', 'count' => $totalLikesphotosCount, 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'owner_id' => $subject->getIdentity());
      }


      if (in_array('albums', $param['selectDispalyTabs'])) {
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
          $select = $albumTable->getUserAlbums($subject, $param);
        } else {
          $select = Engine_Api::_()->getApi('core', 'album')->getAlbumSelect(array('owner' => $subject, 'search' => 1));
        }
        if ($viewer->getIdentity() != $subject->getIdentity() && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
          $select->where('type IS NULL');
        // $select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        $albumPaginator = Zend_Paginator::factory($select);
        $this->view->totalAlbumsCount = $totalAlbumsCount = $albumPaginator->getTotalItemCount();
      }

      if (in_array('yourphotos', $param['selectDispalyTabs'])) {
        $yourPhotoPaginator = $photoTable->photoBySettings(array_merge($param, array('owner_id' => $subject->getIdentity(), 'ownerObject' => $subject)));
        $this->view->totalYourphotosCount = $totalYourphotosCount = $yourPhotoPaginator->getTotalItemCount();
      }

      if (in_array('photosofyou', $param['selectDispalyTabs'])) {
        $paginator = $photoTable->getTaggedInOthersPhotos(array_merge($param, array('owner_id' => $subject->getIdentity())));
        $this->view->totalPhotosofyouCount = $totalPhotosofyouCount = $paginator->getTotalItemCount();
      }

      if (!$this->view->isajax && empty($totalPhotosofyouCount) && in_array('albums', $param['selectDispalyTabs']) && ($totalAlbumsCount > 0)) {
        $this->view->viewType = 'albums';
        $this->view->paginator = $albumPaginator;
        $albumPaginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
        $albumPaginator->setCurrentPageNumber($this->_getParam('page', 1));
      }
    }

    if (!$this->view->isajax && ($totalPhotosofyouCount + $totalAlbumsCount + $totalYourphotosCount + $totalLikesphotosCount) <= 0) {
      return $this->setNoRender();
    }

    $element = $this->getElement();
    if (empty($totalAlbumsCount) && empty($totalYourphotosCount) && !empty($totalPhotosofyouCount) && empty($totalLikesphotosCount)) {
      if ($viewer->getIdentity() == $subject->getIdentity())
        $element->setTitle(sprintf($this->view->translate('Photo of You')));
      else
        $element->setTitle(sprintf($this->view->translate("Photos of %s", ucfirst($subject->displayname))));
    } elseif (empty($totalAlbumsCount) && !empty($totalYourphotosCount) && empty($totalPhotosofyouCount) && empty($totalLikesphotosCount)) {
      if ($viewer->getIdentity() == $subject->getIdentity())
        $element->setTitle($this->view->translate('Your Photos'));
      else
        $element->setTitle(sprintf($this->view->translate("%s's Photos"), ucfirst($subject->displayname)));
    } elseif (empty($totalAlbumsCount) && empty($totalYourphotosCount) && empty($totalPhotosofyouCount) && !empty($totalLikesphotosCount)) {
      if ($viewer->getIdentity() == $subject->getIdentity())
        $element->setTitle($this->view->translate('Your Liked Photos'));
      else
        $element->setTitle(sprintf($this->view->translate("%s's Liked Photos"), ucfirst($subject->displayname)));
    }

    if ($this->view->is_ajax_load) {
      $this->view->controller = $this->_getParam('controllers');
      $this->view->action = $this->_getParam('actions');
    } else {
      $this->view->controller = $controller;
      $this->view->action = $action;
    }

// Add count to title if configured
    if ($this->_getParam('titleCount', false)) {
     if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if (($totalPhotosofyouCount + $totalYourphotosCount) > 0)
        $this->_childCount = $totalPhotosofyouCount + $totalYourphotosCount;
      elseif (empty($totalPhotosofyouCount) && empty($totalYourphotosCount) && empty($totalAlbumsCount) && $totalLikesphotosCount > 0)
        $this->_childCount = $totalLikesphotosCount;
      else
        $this->_childCount = $totalAlbumsCount;
     }else{
       $this->_childCount = $totalAlbumsCount;
     }
    }

    $this->view->content_map_id = 0;
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
      $this->view->content_map_id = Engine_Api::_()->sitetagcheckin()->getWidgetId('user_profile_index', 'sitetagcheckin.map-sitetagcheckin');
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}