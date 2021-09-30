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
class Sitealbum_Widget_PinboardBrowseController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->params = $this->_getAllParams();
    $this->view->params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
    if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
      $this->view->params['noOfTimes'] = 1000;

    if ($this->_getParam('autoload', true)) {
      $this->view->autoload = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->autoload = false;
        if ($this->_getParam('contentpage', 1) > 1)
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
        $this->getElement()->removeDecorator('Title');
        //return;
      }
    } else {
      $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
      if ($this->_getParam('contentpage', 1) > 1) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
    }

    $params = $this->view->params;

    //CORE API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //GET PARAMS
    $this->view->userComment = $this->_getParam('userComment', 1);
    $this->view->albumInfo = $this->_getParam('albumInfo', $this->_getParam('albumInfo', array("totalPhotos", "albumTitle", "ownerName")));
    $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
    $this->view->albumTitleTruncation = $this->_getParam('albumTitleTruncation', 22);
    $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
    $this->view->customParams = $params['customParams'] = $this->_getParam('customParams', 5);
    $sitealbum_pinboard_view = Zend_Registry::isRegistered('sitealbum_pinboard_view') ? Zend_Registry::get('sitealbum_pinboard_view') : null;

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0); 
    if ($this->view->detactLocation) { 
      $this->view->detactLocation = $settings->getSetting('sitealbum.location', 1);
    }
    if ($this->view->detactLocation) { 
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $customFieldValues = array();
    $values = array();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $category_id = $request->getParam('category_id');
    if (empty($category_id)) {
      $params['category_id'] = $this->_getParam('category_id');
      $params['subcategory_id'] = $this->_getParam('subcategory_id');
    } else {
      $params['category_id'] = $request->getParam('category_id');
      $params['subcategory_id'] = $request->getParam('subcategory_id');
    }
    //GET VIEWER DETAILS
    $viewer = Engine_Api::_()->user()->getViewer();

    //FORM GENERATION
    $form = new Sitealbum_Form_Search_Search();

    $this->view->params = $params = array_merge($request->getParams(), $params);

    if (!empty($params)) {
      $form->populate($params);
    }

    $this->view->formValues = $form->getValues();

    $values = array_merge($values, $form->getValues());

    if (!empty($params['view_view']) && $params['view_view'] == 1) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }
      $values['users'] = $ids;
    }
    $this->view->assign($values);

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    
    $values['orderby'] = $orderBy = $request->getParam('orderby', null);
    if (empty($orderBy)) { 
      $orderby = $this->_getParam('orderby', 'creation_date');
      if ($orderby == 'creationDate')
        $values['orderby'] = 'creation_date';
      elseif ($orderby == 'viewCount')
        $values['orderby'] = 'view_count';
      else
        $values['orderby'] = $orderby;
    }
    $this->view->params['orderby'] = $values['orderby'];

    $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 10);
    $values['showClosed'] = $this->_getParam('showClosed', 1);

    if ($request->getParam('titleAjax')) {
      $values['search'] = $request->getParam('titleAjax');
    }

    //GET PAGINATOR
    $values['notLocationPage'] = 1;
    $values['paginator'] = 1; 
    
    if (!$this->view->detactLocation && empty($_GET['location']) && isset($values['location'])) {
      unset($values['location']);

      if (empty($_GET['latitude']) && isset($values['latitude'])) {
        unset($values['latitude']);
      }

      if (empty($_GET['longitude']) && isset($values['longitude'])) {
        unset($values['longitude']);
      }

      if (empty($_GET['Latitude']) && isset($values['Latitude'])) {
        unset($values['Latitude']);
      }

      if (empty($_GET['Longitude']) && isset($values['Longitude'])) {
        unset($values['Longitude']);
      }
    }

    if (!$this->view->detactLocation && empty($_GET['location']) && isset($values['location'])) {
      unset($values['location']);
    }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumPaginator($values, $customFieldValues);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
    $paginator->setItemCountPerPage($values['limit']);

    $this->view->search = 0;
    if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
      $this->view->search = 1;
    }
    
    if(empty($sitealbum_pinboard_view))
      return $this->setNoRender();

    //SEND FORM VALUES TO TPL
    $this->view->formValues = $values;
    $this->view->normalPhotoWidth = $settings->getSetting('normal.photo.width', 256);
    $this->view->normalLargePhotoWidth = $settings->getSetting('normallarge.photo.width', 720);
    $this->view->sitealbum_last_photoid = $settings->getSetting('sitealbum.last.photoid');
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

    $this->view->countPage = $paginator->count();
    if ($this->view->params['noOfTimes'] > $this->view->countPage)
      $this->view->params['noOfTimes'] = $this->view->countPage;
    $this->view->show_buttons = $this->_getParam('show_buttons', array("comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
  }

}
