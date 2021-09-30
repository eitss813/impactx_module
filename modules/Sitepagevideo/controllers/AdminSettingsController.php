<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitepagevideo_Form_Admin_Global') {

        }
        return true;
    }
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
    if ($this->getRequest()->isPost()) {
      $sitepageKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', null);
      if (!empty($sitepageKeyVeri)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.lsettings', trim($sitepageKeyVeri));
      }
      if ($_POST['sitepagevideo_lsettings']) {
        $_POST['sitepagevideo_lsettings'] = trim($_POST['sitepagevideo_lsettings']);
      }
      
      
      $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
      if ( !empty($_POST['video_youtube_apikey']) && $_POST['video_youtube_apikey'] != $currentYouTubeApiKey ) {
        $response = Engine_Api::_()->seaocore()->verifyYotubeApiKey($_POST['video_youtube_apikey']);
        if ( !empty($response['errors']) ) {
          $error_message = array('Invalid API Key');
          foreach ( $response['errors'] as $error ) {
            $error_message[] = "Error Reason (" . $error['reason'] . '): ' . $error['message'];
          }          
          $youTubeError = true;          
          $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_main_settings');
          $this->view->form = $form = new Sitepagevideo_Form_Admin_Global();          
          $navi_auth_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.navi.auth');
          if ( is_array($form->_SHOWELEMENTSBEFOREACTIVATE) ) {
            $formElements = $form->getElements();
            foreach ( $formElements as $elementObj ) {
              $getElementName = $elementObj->getName();
              if ( empty($navi_auth_value) && ($getElementName != 'sitepagevideo_lsettings') && !in_array($getElementName, $form->_SHOWELEMENTSBEFOREACTIVATE) ) {
                $form->removeElement($getElementName);
              } else if ( !empty($navi_auth_value) && $getElementName != 'sitepagevideo_lsettings' && in_array($getElementName, $form->_SHOWELEMENTSBEFOREACTIVATE) ) {
                $form->removeElement($getElementName);
              }
            }
          }                    
          $form->video_youtube_apikey->addErrors($error_message);
          return;
        }
      }
      
    }
    include_once APPLICATION_PATH . '/application/modules/Sitepagevideo/controllers/license/license1.php';
  }

  //ACTION OF SETTING FOR CREATING VIDEO FROM MY COMPUTER
  public function utilityAction() {

    //GET NAVIAGION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_main_utility');

    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitepagevideo_ffmpeg_path;

    $command = "$ffmpeg_path -version 2>&1";
    $this->view->version = $version = @shell_exec($command);

    $command = "$ffmpeg_path -formats 2>&1";
    $this->view->format = $format = @shell_exec($command);
  }

  //ACTION FOR FAQ
  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_main_faq');
  }

  //ACTION FOR WIDGET SETTINGS
  public function widgetAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagevideo_admin_submain', array(), 'sitepagevideo_admin_submain_video_tab');
    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepagevideo', 'type' => 'videos'));
  }  

   //ACTION FOR VIDEO OF THE DAY
  public function manageDayItemsAction() {

		//TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagevideo_admin_main', array(), 'sitepagevideo_admin_widget_settings');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagevideo_admin_submain', array(), 'sitepagevideo_admin_submain_dayitems');
   
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepagevideo_Form_Admin_Manage_Filter();
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

    $this->view->videoOfDaysList = $videoOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->getItemOfDayList($values, 'video_id', 'sitepagevideo_video');
    $videoOfDay->setItemCountPerPage(50);
    $videoOfDay->setCurrentPageNumber($page);
  }

  //ACTION FOR ADDING VIDEO OF THE DAY
  public function addVideoOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepagevideo_Form_Admin_ItemOfDayday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setTitle('Add Video of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Video from the auto-suggest Video field. The selected Video will be displayed as "Video of the Day" for this duration and if more than one videos are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Video Name');

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
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitepagevideo_video');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepagevideo_video';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Video of the Day has been added successfully.'))
              ));
    }
  }

  //ACTION FOR VIDEO SUGGESTION DROP-DOWN
  public function getVideoAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
    $videoName = $videoTable->info('name');
    $data = array();
    $select = $videoTable->select()
													->setIntegrityCheck(false)
													->from($videoName)
                          ->join($pageTableName, $pageTableName . '.page_id = '. $videoName . '.page_id',array('title AS page_title', 'photo_id as page_photo_id'))
													->join($allowName, $allowName . '.resource_id = '. $pageTableName . '.page_id', array('resource_type','role'))
													->where($allowName.'.resource_type = ?', 'sitepage_page')
													->where($allowName.'.role = ?', 'registered')
													->where($allowName.'.action = ?', 'view')
													->where($videoName.'.search = ?', 1)
													->where($videoName.'.title  LIKE ? ', '%' . $title . '%')
													->limit($limit)
													->order($videoName.'.creation_date DESC');
    $select = $select
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $videos = $videoTable->fetchAll($select);

    foreach ($videos as $video) {
      $content_photo = $this->view->itemPhoto($video, 'thumb.normal');
      $data[] = array(
          'id' => $video->video_id,
          'label' => $video->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE VIDEO OF DAY ENTRY
  public function deleteVideoOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }

  //ACTION FOR MULTI DELETE VIDEO ENTRIES
  public function multiDeleteVideoAction() {
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

   // ACTION FOR CHANGE SETTINGS OF TABBED VIDEO WIDZET TAB
  public function editTabAction() {
    //FORM GENERATION
    $this->view->form = $form = new Sitepagevideo_Form_Admin_EditTab();
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

  //ACTION FOR UPDATE ORDER  OF VIDEOS WIDGTS TAB
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
        $this->_helper->redirector->gotoRoute(array('action' => 'widget'));
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
    $this->_redirect('admin/sitepagevideo/settings/widget');
  }

  public function readmeAction() {
    
  }

}
?>