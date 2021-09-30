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
class Sitealbum_Widget_PhotoStripsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
    if (empty($getLightBox)) {
      return $this->setNoRender();
    }

    $param = array();
    // Just remove the title decorator
    $title = $this->getElement()->getTitle();
    if(empty($title)) {
        $this->getElement()->removeDecorator('Title');
    }
    $param['owner_id'] = $owner_id = $subject->getIdentity();
     
    //GET PARAMS
    $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 100);
    $this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 100);
    $param['popularType'] = $this->_getParam('popularType', 'comment');
    $param['featured'] = $this->_getParam('featured', 0);
    $param['category_id'] = $this->_getParam('category_id');
    $param['subcategory_id'] = $this->_getParam('subcategory_id');

    switch ($param['popularType']) {
      case 'view':
        $param['orderby'] = 'view_count';
        break;
      case 'comment':
        $param['orderby'] = 'comment_count';
        break;
      case 'like':
        $param['orderby'] = 'like_count';
        break;
      case 'rating':
        $param['orderby'] = 'rating';
        break;
      case 'creation':
        $param['orderby'] = 'creation_date';
        break;
      case 'modified':
        $param['orderby'] = 'modified_date';
        break;
      case 'random':
        $param['orderby'] = 'random';
        break;
    }

    $this->view->limit = $param['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 7);

    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $photoTableName = $photoTable->info('name');

    //CHECK REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $hide_photo_id = $this->_getParam('hide_photo_id', null);
    if (!empty($is_ajax) && !empty($hide_photo_id)) {
      $photoTable->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
    }

    $this->view->param = $param;
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->getProfileStripPhotos($param);

    $paginator->setItemCountPerPage($param['itemCountPerPage']);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $count = $paginator->getTotalItemCount();
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $this->view->canEdit = $subject->isSelf($viewer);
    if (!$this->view->canEdit) {
      $viewer_id = $viewer->getIdentity();
      if (!empty($viewer_id) && $viewer->level_id == 1)
        $this->view->canEdit = true;
    }
    $this->view->hidePhoto = 0;
    if ($this->view->canEdit) {
      $album_id_array = array();
      foreach ($paginator as $photo) {
        $album_id_array[] = $photo->album_id;
      }
      $select = $photoTable->select()
              ->from($photoTableName, array('photo_id'))
              ->where($photoTableName . '.album_id in (?)', new Zend_Db_Expr("'" . join("', '", array_unique($album_id_array)) . "'"))
              ->where($photoTableName . '.owner_id = ?', $owner_id)
              ->where($photoTableName . '.photo_hide = ?', 1);

      if (!empty($param['featured']))
        $select->where($photoTableName . '.featured = ?', 1);

      $hidePhoto = $select->query()
              ->fetchColumn();

      if (!empty($hidePhoto))
        $hidePhotoCount = 1;
      else
        $hidePhotoCount = 0;

      $this->view->hidePhoto = $hidePhotoCount;
      if (empty($count))
        $count = $hidePhotoCount;
    }
    $this->view->count = $count;
    $this->view->normalPhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.last.photoid');

    if ($this->view->showLightBox) {
      $this->view->params = $params = array('type' => 'strip_' . $param['popularType'], 'count' => $count, 'featured' => $param['featured'], 'category_id' => $param['category_id'], 'subcategory_id' => $param['subcategory_id'], 'owner_id' => $owner_id);
    }
  }

}