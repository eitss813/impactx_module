<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPhotoController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_AdminPhotoController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_manage');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_submain', array(), 'sitealbum_admin_submain_photo_tab');
//    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitealbum', 'type' => 'photos'));
  }

  public function photoOfDayAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_manage');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_submain', array(), 'sitealbum_admin_submain_photo_day');
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitealbum_Form_Admin_Filter();
    $page = $this->_getParam('page', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $this->view->photoOfDaysList = $photoOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum')->getPhotoOfDayList($values);
    $photoOfDay->setItemCountPerPage(50);
    $photoOfDay->setCurrentPageNumber($page);
  }

  //ACTION FOR ADDING PHOTO OF THE DAY
  public function addPhotoOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
    $viewer = Engine_Api::_()->user()->getViewer();
    //FORM GENERATION
    $form = $this->view->form = new Sitealbum_Form_Admin_ItemOfDayday();
    $form->setTitle('Add a Photo of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Photo Title from the auto-suggest Photo Title field. The selected Photo will be displayed as "Photo of the Day" for this duration and if more than one photos are found to be displayed in the same duration then they will be dispalyed randomly one at a time. NOTE: Below you will not be able to add those Photos as "Photo of the Day" for which the  privacy of the Albums they belong to is not set to "Everyone" or "All Registered Members" as they cannot be highlighted to users.');
    $form->getElement('title')->setLabel('Photo');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum');
        $row = $table->getItem('album_photo', $values["resource_id"]);
        if (empty($row)) {
          $row = $table->createRow();
        }
        $values = array_merge($values, array('resource_type' => 'album_photo'));

        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = strtotime($values['start_date']);
        $end = strtotime($values['end_date']);
        date_default_timezone_set($oldTz);
        $values['start_date'] = date('Y-m-d H:i:s', $start);
        $values['end_date'] = date('Y-m-d H:i:s', $end);
    
        if ($values['start_date'] > $values['end_date'])
          $values['end_date'] = $values['start_date'];

        $row->setFromArray($values);
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Page of the Day has been added successfully.'))
              ));
    }
  }

  //ACTION FOR PHOTO SUGGESTION DROP-DOWN
  public function getPhotoAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $featured = $this->_getParam('featured', 0);
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
    $photoName = $photoTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $data = array();
    $select = $photoTable->select()
            ->setIntegrityCheck(false)
            ->from($photoName);
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array());
    } else {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.collection_id', array());
    }
    $select->join($allowName, $albumName . '.album_id = ' . $allowName . '.resource_id', array('resource_type', 'role'))
            ->where($allowName . '.resource_type = ?', 'album')
            ->where($allowName . '.role = ?', 'registered')
            ->where($allowName . '.action = ?', 'view');
    $select->where('search = ?', true)
            ->where($photoName . '.title  LIKE ? ', '%' . $title . '%')
            ->limit($limit)
            ->order($photoName . '.title')
            ->order($photoName . '.creation_date');

    if (!empty($featured))
      $select->where($photoName . ".featured = ?", 0);

    $photos = $photoTable->fetchAll($select);

    foreach ($photos as $photo) {
      $content_photo = $this->view->itemPhoto($photo, 'thumb.normal');
      $data[] = array(
          'id' => $photo->photo_id,
          'label' => $photo->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE PHOTO OF DAY
  public function deletePhotoOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));

      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-photo/delete.tpl');
  }

  //ACTION FOR MULTI DELETE PHOTO ENTRIES
  public function multiDeletePhotoAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $sitepageitemofthedays = Engine_Api::_()->getItem('sitealbum_itemofthedays', (int) $value);
          if (!empty($sitepageitemofthedays)) {
            $sitepageitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'photo-of-day'));
  }

  public function featuredAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_photo_featured');

    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
    $photoName = $photoTable->info('name');
    $data = array();
    $select = $photoTable->select()
            ->setIntegrityCheck(false)
            ->from($photoName);
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array());
    } else {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.collection_id', array());
    }
    $select->where($photoName . ".featured = ?", 1)
            ->order($photoName . '.creation_date DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(50);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license2.php';
  }

  public function addFeaturedAction() {
    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitealbum_Form_Admin_FeaturedAlbum();
    $form->setTitle('Add an Photo as Featured')
            ->setDescription('Using the auto-suggest field below, choose the photo to be made featured. NOTE: Below you will not be able to add those Photos as "Featured" for which the  privacy of the Albums they belong to is not set to "Everyone" or "All Registered Members" as they cannot be highlighted to users.');
    $form->getElement('title')->setLabel('Photo Title');
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('album_photo', $values['resource_id']);
        $photo->featured = !$photo->featured;
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured photo has been added successfully.'))
              ));
    }
  }

  public function removeFeaturedAction() {

    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('album_photo', $this->_getParam('id'));
        $photo->featured = !$photo->featured;
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-photo/un-featured.tpl');
  }

// ACTION FOR CHANGE SETTINGS OF TABBED ALBUM WIDZET TAB
  public function editTabAction() {
    //FORM GENERATION
    $this->view->form = $form = new Sitealbum_Form_Admin_EditTab();
    $id = $this->_getParam('tab_id');
    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $values = $tab->toarray();
      $form->populate($values);
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $tab->setFromArray($values);
      $tab->save();
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR UPDATE ORDER  OF PHOTOS WIDGTS TAB
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {
          $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
          if (!empty($tab)) {
            $tab->order = $key + 1;
            $tab->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE TAB ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('tab_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    try {
      $tab->enabled = !$tab->enabled;
      $tab->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

}
