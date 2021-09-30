<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_dashboards');

    $this->view->formFilter = $formFilter = new Sesnewsletter_Form_Admin_FilterSubscribers();
    $page = $this->_getParam('page', 1);
    $table = Engine_Api::_()->getDbtable('subscribers', 'sesnewsletter');
    $select = $table->select();

    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams()))
      $values = $formFilter->getValues();

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array('subscriber_id' => 'DESC'), $values);
    $this->view->assign($values);

    if (!empty($values['email']))
      $select->where('email =?', $values['email']);
    if (!empty($values['type_id']))
      $select->where('type_id =?', $values['type_id']);
    if (!empty($values['level_id']))
      $select->where('level_id =?', $values['level_id']);
    $select->group('email');
    $select->order('subscriber_id DESC');

    $valuesCopy = array_filter($values);

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(100);
    $this->view->formValues = $valuesCopy;

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $action = Engine_Api::_()->getItem('sesnewsletter_subscriber', $value)->delete();
          $db->query("DELETE FROM engine4_sesnewsletter_subscribers WHERE subscriber_id = " . $value);
        }
      }
    }
  }

  public function addsubscriberAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id', 0);
    $this->view->form = $form = new Sesnewsletter_Form_Admin_AddSubscriber();
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $db = Engine_Api::_()->getDbtable('subscribers', 'sesnewsletter')->getAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('subscribers', 'sesnewsletter');
        $values = $form->getValues();
        $newsletter_types = $values['newsletter_types'];

        if($values['choose_member'] == 1) {
            if(empty($values['user_id'])){
                $form->addError("Please select member from dropdrown list.");
                return;
            }
            $user = Engine_Api::_()->getItem('user', $values['user_id']);
            $values['email'] = $user->email;
            $values['resource_id'] = $user->user_id;
            $values['resource_type'] = 'user';
            $values['level_id'] = $user->level_id;
            $values['displayname'] = $values['member_name'];
            foreach($values['newsletter_types'] as $newsletter_types) {
                $isExistType = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExistType($values['email'], $newsletter_types);
                if(empty($isExistType)) {
                    $values['type_id'] = $newsletter_types;
                    $row = $table->createRow();
                    $row->setFromArray($values);
                    $row->save();
                }
            }
        } else if($values['choose_member'] == 2) {
            $values['resource_id'] = 0;
            $values['resource_type'] = 'guest';
            $values['level_id'] = '5';

            foreach($values['newsletter_types'] as $newsletter_types) {
                $emails = explode(',', $values['external_emails']);
                foreach($emails as $email) {
                    $isExistType = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExistType($email, $newsletter_types);
                    if(empty($isExistType)) {
                        $values['type_id'] = $newsletter_types;
                        $values['email'] = $email;
                        $row = $table->createRow();
                        $row->setFromArray($values);
                        $row->save();
                    }
                }
            }
        } else if($values['choose_member'] == 3) {
            $values['resource_id'] = 0;
            $values['resource_type'] = 'guest';
            $values['level_id'] = '5';
            try {
                $csvFile = explode(".", $_FILES['csvfile']['name']);

                if (($csvFile[1] != "csv")) {
                    $itemError = Zend_Registry::get('Zend_Translate')->_("Choose only CSV file.");
                    $form->addError($itemError);
                    return;
                }

                $csv_file = $_FILES['csvfile']['tmp_name']; // specify CSV file path

                $csvfile = fopen($csv_file, 'r');
                $theData = fgets($csvfile);
                //$thedata = explode('|',$theData);
                $email_address = $counter = 0;

                foreach($thedata as $data) {

                    //Direct CSV
                    if(trim(strtolower($data)) == '[Email Address]'){
                        $email_address = $counter;
                    }
                    $counter++;
                }

                $i = 0;
                $importedData = array();
                while (!feof($csvfile))
                {
                    $csv_data[] = fgets($csvfile, 1024);
                    //$csv_array = explode("|", $csv_data[$i]);
                    if(!count($csv_array))
                        continue;
                    if(isset($csv_array[$email_address]))
                        $importedData[$i]['email'] = @$csv_array[0]; //$csv_array[$email_address];
                    $i++;
                }
                fclose($csvfile);

                foreach($values['newsletter_types'] as $newsletter_types) {
                    foreach($importedData as $result) {
                        $isExistType = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExistType($result['email'], $newsletter_types);
                        if(empty($isExistType)) {
                            $values['type_id'] = $newsletter_types;
                            $values['email'] = $result['email'];
                            $row = $table->createRow();
                            $row->setFromArray($values);
                            $row->save();
                        }
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Subscriber added successfully.')
      ));
    }
  }

  public function editAction() {

	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_dashboards');

    $this->view->form = $form = new Sesnewsletter_Form_Admin_Edit();

	$id = $this->_getParam('id');
    $this->view->item = $item = Engine_Api::_()->getItem('sesnewsletter_subscriber', $id);

    $data = array();
    foreach(explode(",",$item->resource_id) as $result) {
        $resource = Engine_Api::_()->getItem($item->resource_type, $result);
        $id = $resource->getIdentity();
        $data[$id] = $resource->getTitle();
    }
    $form->resource_id->setMultiOptions($data);
    $form->populate($item->toArray());

    //Check post
    if (!$this->getRequest()->isPost())
      return;

    //Check
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    unset($values['resource_id']);
    unset($values['resource_type']);
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

        if(isset($values['member_levels']))
            $item->member_levels = implode(',',$values['member_levels']);

        unset($values['member_levels']);
        $item->setFromArray($values);
        $item->save();
        $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'sesnewsletter', 'action' => 'index', 'controller' => 'manage'), 'admin_default', true);
  }


  public function enabledAction() {

    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesnewsletter_subscriber', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }
    $this->_redirect('admin/sesnewsletter/manage');
  }

  public function deleteAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesnewsletter_Form_Admin_Delete();
    $form->setTitle('Delete This Entry?');
    $form->setDescription('Are you sure that you want to delete this Entry? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');

    $this->view->item_id = $id = $this->_getParam('id');

    // Check post
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getItem('sesnewsletter_subscriber', $id)->delete();
      $db = Engine_Db_Table::getDefaultAdapter();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Entry Deleted Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }
}
