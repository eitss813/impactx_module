<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminSettingsController.php 9802 2012-10-20 16:56:13Z pamela $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Event_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('event_admin_main', array(), 'event_admin_main_settings');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Event_Form_Admin_Global();

    $form->bbcode->setValue($settings->getSetting('event_bbcode', 1));
    $form->html->setValue($settings->getSetting('event_html', 0));
    $form->event_page->setValue($settings->getSetting('event_page', 12));
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      $settings->setSetting('event_bbcode', $values['bbcode']);
      $settings->setSetting('event_html', $values['html']);
      $settings->setSetting('event_allow_unauthorized', $values['event_allow_unauthorized']);
      $settings->setSetting('event_page', $values['event_page']);
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('event_admin_main', array(), 'event_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'event')->fetchAll();
  }

  public function levelAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('event_admin_main', array(), 'event_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Event_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($level_id);

    if (isset($form->coverphoto_dummy)) {
      $eventId = Engine_Api::_()->getItemTable('event')->select()->query()->fetchColumn();
      if (empty($eventId)) {
        $description = '<div class="tip" style="margin-top:-9px"><span>Please create atleast one event first and then set the default cover photo.</div>';
      } else {
        $href = Engine_Api::_()->getItem('event', $eventId)->getHref() . '?uploadDefaultCover=1&level_id='.$level_id;
        $description = sprintf(
          "%1sClick here%2s to upload and set default cover photo for events",
          "<a href='$href' target='_blank'>", "</a>"
        );
      }
      $form->coverphoto_dummy->setDescription($description);
    }

    if(!empty( $eventCover = Engine_Api::_()->getApi("settings", "core")->getSetting("eventcoverphoto.preview.level.id.$id"))) {
      $image = Engine_Api::_()->storage()->get($eventCover, 'thumb.cover')->map();
      $description = sprintf("%1sPreview Default Cover Photo%2s",
        "<a onclick='showPreview();'>",
        "</a><div id='show_default_preview' class='is_hidden'>"
          . "<img src='$image' style='max-height:600px;max-width:600px;'></div>"
      );
      $form->addElement('dummy', 'coverphoto_preview', array(
        'description' => $description,
      ));

      $form->coverphoto_preview->addDecorator(
        'Description',
        ['placement' => 'PREPEND', 'class' => 'description', 'escape' => false]
      );
    }

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('event', $level_id, array_keys($form->getValues())));

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();

    // Form elements with NonBoolean values
    $nonBooleanSettings = $form->nonBooleanFields();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      if( $level->type != 'public' ) {
        // Set permissions
        $values['auth_comment'] = (array) $values['auth_comment'];
        $values['auth_photo'] = (array) $values['auth_photo'];
        $values['auth_view'] = (array) $values['auth_view'];
      }

      // coverphoto work
      unset($values['coverphoto_dummy']);
      unset($values['coverphoto_preview']);
      $permissionsTable->setAllowed('event', $level_id, $values, '', $nonBooleanSettings);

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Event_Form_Admin_Category();
    $form->setAction($this->view->url());

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }

    // Process
    $values = $form->getValues();

    $categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $categoryTable->insert(array(
        'title' => $values['label'],
      ));

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->event_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
    $eventTable = Engine_Api::_()->getDbtable('events', 'event');
    $category = $categoryTable->find($id)->current();

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/delete.tpl');
      return;
    }

    // Process
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->delete();

      $eventTable->update(array(
        'category_id' => 0,
      ), array(
        'category_id = ?' => $category->getIdentity(),
      ));

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->event_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
    $category = $categoryTable->find($id)->current();

    // Generate and assign form
    $form = $this->view->form = new Event_Form_Admin_Category();
    $form->setAction($this->view->url());
    $form->setField($category);

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }

    // Ok, we're good to add field
    $values = $form->getValues();

    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->title = $values['label'];
      $category->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }
}
