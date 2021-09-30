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
class Sitealbum_Widget_AlbumViewController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

   if (Engine_Api::_()->seaocore()->isSitemobileApp() && $this->_getParam('ajax', false)) {
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
   
    try {
          if( '' !== ($subject = trim((string) Zend_Controller_Front::getInstance()->getRequest()->getParam('subject'))) ) {
            $subject = Engine_Api::_()->getItemByGuid($subject);
            if( ($subject instanceof Core_Model_Item_Abstract) && $subject->getIdentity() && !Engine_Api::_()->core()->hasSubject('album') ) {
              Engine_Api::_()->core()->setSubject($subject);
            }
          }         
        } catch( Exception $e ) {
          // Silence
          //throw $e;
        }        
      }   
        
    if (!Engine_Api::_()->core()->hasSubject('album')) {
      return $this->setNoRender();
    }

    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    //FOR MOBILE SITE
    $this->view->page = $page = $this->_getParam('page', 1);
    if (!Engine_Api::_()->seaocore()->isSitemobileApp() && (isset($params['page']) && !empty($params['page'])) ) {
      $this->view->page = $page = $params['page'];
    }

    // Prepare params
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', 200);
    $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
    $this->view->marginPhoto = $params['margin_photo'] = $this->_getParam('margin_photo', 2);
    $this->view->photoHeight = $params['photoHeight'] = $this->_getParam('photoHeight', 200);
    $this->view->photoWidth = $params['photoWidth'] = $this->_getParam('photoWidth', 200);
    $this->view->photoTitleTruncation = $params['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 16);
    $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
    $this->view->photoInfo = $params['photoInfo'] = $this->_getParam('photoInfo', array("likeCommentStrip"));
    $this->view->normalPhotoWidth = $coreSettings->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreSettings->getSetting('sitealbum.last.photoid');
    $this->view->showPhotosInJustifiedView = $params['showPhotosInJustifiedView'] = $this->_getParam('showPhotosInJustifiedView', 0); 
    $this->view->maxRowHeight = $params['maxRowHeight'] = $this->_getParam('maxRowHeight',0); 
    $this->view->rowHeight = $params['rowHeight'] = $this->_getParam('rowHeight',205);   
    $this->view->margin = $params['margin'] = $this->_getParam('margin',5);  
    $this->view->lastRow = $params['lastRow'] = $this->_getParam('lastRow', 'nojustify'); 
    $this->view->params = $params;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $front = Zend_Controller_Front::getInstance();
    $this->view->comment_view = $comment_view = $front->getRequest()->getParam('comment', 'false');
    $sitealbum_albumview = Zend_Registry::isRegistered('sitealbum_albumview') ? Zend_Registry::get('sitealbum_albumview') : null;

    if ($comment_view == 'false') {
      // Prepare data
      if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));
        $this->view->totalCount = $paginator->getTotalItemCount();
      } else {
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $this->view->totalCount = $paginator->getTotalItemCount();
      }
      $paginator->setItemCountPerPage($this->view->itemCountPerPage);
      $paginator->setCurrentPageNumber($page);
      $this->view->count = $this->_childCount = $paginator->getTotalItemCount();
    }
    
    // Do other stuff
    $this->view->mine = true;
    
    if(empty($sitealbum_albumview))
      return $this->setNoRender();

    $this->view->canEdit = Engine_Api::_()->authorization()->isAllowed($album, null, 'edit');
    $this->view->canDelete = Engine_Api::_()->authorization()->isAllowed($album, null, 'delete');
    $this->view->allowView = $this->view->canMakeFeatured = false;
    if (!empty($viewer_id) && ($viewer->level_id == 1 || $viewer->level_id == 2)) {
      $this->view->canMakeFeatured = true;
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
    }

    // Get albums
    $albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
            ->from($albumTable, array('album_id', 'title', 'type'))
            ->where('owner_type = ?', 'user')
            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
            ->query()
            ->fetchAll();

    if ($album->type == null && count($myAlbums) > 1) {
      $this->view->movetotheralbum = 1;
    }

    if (!$album->getOwner()->isSelf($viewer)) {
      $album->getTable()->update(array(
          'view_count' => new Zend_Db_Expr('view_count + 1'),
              ), array(
          'album_id = ?' => $album->getIdentity(),
      ));
      $this->view->mine = false;
    }

    if ($album->photos_count != $this->_childCount) {
      $album->getTable()->update(array(
          'photos_count' => $this->_childCount,
              ), array(
          'album_id = ?' => $album->getIdentity(),
      ));
    }

    $this->view->canComment = $canComment = $album->authorization()->isAllowed($viewer, 'comment');
    $countView = Zend_Registry::isRegistered('sitealbum_countview') ? Zend_Registry::get('sitealbum_countview') : null;
    $sitealbumCoreview = $coreSettings->getSetting('sitealbum.coreview', null);
    if (empty($sitealbumCoreview) || empty($countView)) {
      return $this->setNoRender();
    }
    
    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if (Engine_Api::_()->seaocore()->isSitemobileApp())
        $this->view->sitemapPageHeaderTitle = ( '' != trim($this->view->album->getTitle()) ? $this->view->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>');
      else
        $this->view->sitemapPageHeaderTitle = $this->view->translate('%1$s\'s Album: %2$s', $this->view->album->getOwner()->__toString(), ( '' != trim($this->view->album->getTitle()) ? $this->view->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'));

      Zend_Registry::set('sitemapPageHeaderTitle', $this->view->sitemapPageHeaderTitle);
      //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
      if (!Zend_Registry::isRegistered('sitemobileNavigationName')) {
        Zend_Registry::set('sitemobileNavigationName', 'setNoRender');
      }
      //SCROLLING PARAMETERS SEND
      if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
        //SET SCROLLING PARAMETTER FOR AUTO LOADING.
        if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
          Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
        }
      }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->totalPages = ceil(($this->view->totalCount) /$this->view->itemCountPerPage);
   }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}