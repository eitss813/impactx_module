<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminAlbumController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_AdminAlbumController extends Core_Controller_Action_Admin {

  public function indexAction() { 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_manage');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_submain', array(), 'sitealbum_admin_submain_album_tab');
//    include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license2.php';
  }

  public function albumOfDayAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_manage');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_submain', array(), 'sitealbum_admin_submain_album_day');

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

    $this->view->albumOfDaysList = $albumOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum')->getAlbumOfDayList($values);
    $albumOfDay->setItemCountPerPage(100);
    $albumOfDay->setCurrentPageNumber($page);
  }

  //ACTION FOR ADDING ALBUM OF THE DAY
  public function addAlbumOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitealbum_Form_Admin_ItemOfDayday();
    $form->setTitle('Add an Album of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Album from the auto-suggest Album field. The selected Album will be displayed as "Album of the Day" for this duration and if more than one albums are found to be displayed in the same duration then they will be dispalyed randomly one at a time. NOTE: Below you will not be able to add those Albums as "Album of the Day" whose privacy is not set to "Everyone" or "All Registered Members" as they cannot be highlighted to users.');
    $form->getElement('title')->setLabel('Album Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum');
        $row = $table->getItem('album', $values["resource_id"]);

        if (empty($row)) {
          $row = $table->createRow();
        }
        $values = array_merge($values, array('resource_type' => 'album'));

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
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Album of the Day has been added successfully.'))
              ));
    }
  }

  //ACTION FOR ALBUM SUGGESTION DROP-DOWN
  public function getAlbumAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $featured = $this->_getParam('featured', 0);
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $albumName = $albumTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $data = array();
    $select = $albumTable->select()
            ->setIntegrityCheck(false)
            ->from($albumName)
            ->join($allowName, $albumName . '.album_id = '. $allowName . '.resource_id', array('resource_type','role'))
            ->where($albumName.'.search = ?', true)
            ->where($albumName.'.title  LIKE ? ', '%' . $title . '%')
            ->where($allowName.'.resource_type = ?', 'album')
            ->where($allowName.'.role = ?', 'registered')
            ->where($allowName.'.action = ?', 'view')
            ->limit($limit)
            ->order($albumName.'.creation_date DESC');

    if (!empty($featured))
      $select->where($albumName.".featured = ?", 0);

    $albums = $albumTable->fetchAll($select);

    foreach ($albums as $album) {
      $content_photo = $this->view->itemPhoto($album, 'thumb.normal');
      $data[] = array(
          'id' => $album->album_id,
          'label' => $album->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE ALBUM OF DAY ENTRY
  public function deleteAlbumOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-album/delete.tpl');
  }

  //ACTION FOR MULTI DELETE ALBUM ENTRIES
  public function multiDeleteAlbumAction() {
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
    return $this->_helper->redirector->gotoRoute(array('action' => 'album-of-day'));
  }

  public function featuredAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_album_featured');
    $table = Engine_Api::_()->getItemTable('album');
    $select = $table->select()
            ->where("featured = ?", 1)
            ->order('creation_date DESC');
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
    $form->setTitle('Add an Album as Featured')
            ->setDescription('Using the auto-suggest field below, choose the album to be made featured. NOTE: Below you will not be able to add those Albums as "Featured" whose privacy is not set to "Everyone" or "All Registered Members" as they cannot be highlighted to users.');
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $album = Engine_Api::_()->getItem('album', $values['resource_id']);
        $album->featured = !$album->featured;
        $album->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured album has been added successfully.'))
              ));
    }
  }

  public function removeFeaturedAction() {

    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $album = Engine_Api::_()->getItem('album', $this->_getParam('id'));
        $album->featured = !$album->featured;
        $album->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-album/un-featured.tpl');
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

  //ACTION FOR UPDATE ORDER  OF ALBUMS WIDGTS TAB
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
    $this->_redirect('admin/sitealbum/album');
  }

}
