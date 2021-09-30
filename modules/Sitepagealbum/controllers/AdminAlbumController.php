<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminAlbumController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_AdminAlbumController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_main', array(), 'sitepagealbum_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_submain', array(), 'sitepagealbum_admin_submain_album_tab');
    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepagealbum', 'type' => 'albums'));
  }

    //ACTION FOR ALBUM OF THE DAY
  public function manageDayItemsAction() {

		//TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_main', array(), 'sitepagealbum_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagealbum_admin_submain', array(), 'sitepagealbum_admin_submain_dayitems');
   
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepagealbum_Form_Admin_Manage_Filter();
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

    $this->view->albumOfDaysList = $albumOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->getItemOfDayList($values, 'album_id', 'sitepage_album');
    $albumOfDay->setItemCountPerPage(50);
    $albumOfDay->setCurrentPageNumber($page);
  }

  //ACTION FOR ADDING ALBUM OF THE DAY
  public function addAlbumOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepagealbum_Form_Admin_ItemOfDayday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setTitle('Add an Album of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Album from the auto-suggest Album field. The selected Album will be displayed as "Album of the Day" for this duration and if more than one albums are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Album Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitepage_album');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepage_album';
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
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
    $albumName = $albumTable->info('name');
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $data = array();
    $select = $albumTable->select()
													->setIntegrityCheck(false)
													->from($albumName) 
													->join($pageTableName, $pageTableName . '.page_id = '. $albumName . '.page_id',array('title AS page_title', 'photo_id as page_photo_id'))
													->join($allowName, $allowName . '.resource_id = '. $pageTableName . '.page_id', array('resource_type','role'))
													->where($allowName.'.resource_type = ?', 'sitepage_page')
													->where($allowName.'.role = ?', 'registered')
													->where($allowName.'.action = ?', 'view')
													->where($albumName.'.search = ?', 1)
													->where($albumName.'.title  LIKE ? ', '%' . $title . '%')
													->limit($limit)
													->order($albumName.'.creation_date DESC');
    $select = $select
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
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
      Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
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

          $sitepageitemofthedays = Engine_Api::_()->getItem('sitepage_itemofthedays', (int) $value);
          if (!empty($sitepageitemofthedays)) {
            $sitepageitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-day-items'));
  }

  // ACTION FOR CHANGE SETTINGS OF TABBED ALBUM WIDZET TAB
  public function editTabAction() {
    //FORM GENERATION
    $this->view->form = $form = new Sitepagealbum_Form_Admin_EditTab();
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
    $this->_redirect('admin/sitepagealbum/album');
  }
}

?>
