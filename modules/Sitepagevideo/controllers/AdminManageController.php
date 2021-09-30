<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING THE PAGE VIDEOS
  public function indexAction() {

    //CREATE NAVIGATION TABS
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_main_manage');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepagevideo_Form_Admin_Manage_Filter();

    //USER TABLE NAME
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    //PAGE TABLE NAME
    $tablesitepage = Engine_Api::_()->getItemTable('sitepage_page')->info('name');

    //PAGE-VIDEO TABLE
    $table = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
    $rName = $table->info('name');

    //MAKE QUERY
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName)
                    ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
                    ->joinLeft($tablesitepage, "$rName.page_id = $tablesitepage.page_id", 'title AS sitepage_title');

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'video_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'video_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $page = $this->_getParam('page', 1);
    $this->view->paginator = array();
    include_once APPLICATION_PATH . '/application/modules/Sitepagevideo/controllers/license/license2.php';
  }

  //ACTION FOR MULTI-VIDEO DELETE
  public function deleteSelectedAction() {

    //GET VIDEO IDS
    $this->view->ids = $ids = $this->_getParam('ids', null);

    //COUNT IDS
    $ids_array = explode(",", $ids);
    $this->view->count = count($ids_array);

    //CHECK DELETE CONFIRMATION
    $confirm = $this->_getParam('confirm', false);
    if ($this->getRequest()->isPost() && $confirm == true) {

      foreach ($ids_array as $video_id) {

        //GET PAGE VIDEO OBJECT
        $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

        if ($sitepagevideo) {

          //DELETE RATING DATA
          Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->delete(array('video_id =?' => $video_id));

          //FINALLY DELETE VIDEO OBJECT
          $sitepagevideo->delete();
        }
      }
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //ACTION FOR DELETE THE PAGE-VIDEO
  public function deleteAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET VIDEO ID
    $this->view->video_id = $video_id = $this->_getParam('video_id');

    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET PAGE VIDEO OBJECT
        $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

        //DELETE RATING DATA
        Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->delete(array('video_id =?' => $video_id));

        //FINALLY DELETE VIDEO OBJECT
        $sitepagevideo->delete();

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
    $this->renderScript('admin-manage/delete.tpl');
  }

   public function killAction()
  {
    $video_id = $this->_getParam('video_id', null);
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
        $sitepagevideo->status = 3;
        $sitepagevideo->save();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE VIDEO FEATURED AND REMOVE FEATURED VIDEO 
  public function featuredvideoAction() {

    //GET OFFER ID
    $videoId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $videoId);
      if ($sitepagevideo->featured == 0) {
        $sitepagevideo->featured = 1;
      } else {
        $sitepagevideo->featured = 0;
      }
      $sitepagevideo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagevideo/manage');
  }

   //ACTION FOR MAKE VIDEO FEATURED AND REMOVE FEATURED VIDEO 
  public function highlightedvideoAction() {

    //GET OFFER ID
    $videoId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $videoId);
      if ($sitepagevideo->highlighted == 0) {
        $sitepagevideo->highlighted = 1;
      } else {
        $sitepagevideo->highlighted = 0;
      }
      $sitepagevideo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagevideo/manage');
  }

}
?>