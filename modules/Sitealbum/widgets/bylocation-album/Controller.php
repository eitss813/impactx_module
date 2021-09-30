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
class Sitealbum_Widget_BylocationAlbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->albumInfo = $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName"));
    $this->view->photoWidth = $this->_getParam('photoWidth', 195);
    $this->view->photoHeight = $this->_getParam('photoHeight', 195);
    $this->view->locationDetection = $this->_getParam('locationDetection', 1);
    $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);

    $this->view->coreApi = $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $this->view->normalPhotoWidth = $coreApi->getSetting('normal.photo.width', 375);
    $this->view->normalLargePhotoWidth = $coreApi->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $coreApi->getSetting('sitealbum.last.photoid');

    $widgetSettings = array(
        'showAllCategories' => $this->view->showAllCategories,
        'locationDetection' => $this->view->locationDetection,
    );

    $this->view->form = $form = new Sitealbum_Form_AlbumLocationsearch(array('widgetSettings' => $widgetSettings));

    if (!empty($_POST)) {
      $this->view->is_ajax = $_POST['is_ajax'];
    }

    if (empty($_POST['album_location'])) {
      $this->view->locationVariable = '1';
    }

    if (empty($_POST['is_ajax'])) {
      $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
      //$form->isValid($p);
      $values = $form->getValues();
      $customFieldValues = array_intersect_key($values, $form->getFieldElements());
      $this->view->is_ajax = $this->_getParam('is_ajax', 0);
    } else {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $values = $_POST;
      $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    }

    $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!empty($values['view_view']) && $values['view_view'] == 1) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }
      $values['users'] = $ids;
    }

    unset($values['or']);
    $this->view->assign($values);

    $this->view->current_page = $page = $this->_getParam('page', 1);
    $this->view->current_totalpages = $page * 15;

    //check for miles or street.
    if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {
      if (isset($values['album_street']) && !empty($values['album_street'])) {
        $values['album_location'] = $values['album_street'] . ',';
        unset($values['album_street']);
      }

      if (isset($values['album_city']) && !empty($values['album_city'])) {
        $values['album_location'].= $values['album_city'] . ',';
        unset($values['album_city']);
      }

      if (isset($values['album_state']) && !empty($values['album_state'])) {
        $values['album_location'].= $values['album_state'] . ',';
        unset($values['album_state']);
      }

      if (isset($values['album_country']) && !empty($values['album_country'])) {
        $values['album_location'].= $values['album_country'];
        unset($values['album_country']);
      }
    }

    $values['orderby'] = 'creation_date';
    $result = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumSelect($values, $customFieldValues);
    $this->view->paginator = $paginator = Zend_Paginator::factory($result);
    $this->view->totalresults = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage(16);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();

    if (empty($_POST['is_ajax'])) {
      $categories = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'sponsored' => 0, 'cat_depandancy' => 1));
      $categories_slug[0] = "";
      if (count($categories) != 0) {
        foreach ($categories as $category) {
          $categories_slug[$category->category_id] = $category->getCategorySlug();
        }
      }

      $this->view->categories_slug = $categories_slug;
    }
  }

}