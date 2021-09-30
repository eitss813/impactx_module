<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/3/2016
 * Time: 1:53 PM
 */
class Yndynamicform_AdminNotificationController extends Core_Controller_Action_Admin
{
    public function getDbTable()
    {
        return Engine_Api::_() -> getDbTable('notifications', 'yndynamicform');
    }

    public function createAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $new_form = $this -> view -> new_form = new Yndynamicform_Form_Admin_EditForm_Notification();
        $new_form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $table = $this -> getDbTable();
        $form_id = $this -> _getParam('form_id');

        // Check post
        if ($this -> getRequest() -> isPost() && $new_form -> isValid($this -> getRequest() -> getPost())) {
            // We will add the new form
            $values = $new_form -> getValues();

            $params = $this ->_getAllParams();

            $values['conditional_logic'] = json_encode($params['conditional_logic']);
            $values['conditional_show'] = $params['conditional_show'];
            $values['conditional_scope'] = $params['conditional_scope'];

            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $notification = $table -> createRow();
                $notification -> setFromArray($values);
                $notification -> form_id = $form_id;

                $notification -> save();
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => Array(Zend_Registry::get('Zend_Translate') -> _('New notification has been added.'))
            ));
        }

        // Output
        $this -> renderScript('admin-notification/create.tpl');
    }

    public function editAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $edit_form = new Yndynamicform_Form_Admin_EditForm_Notification();
        $edit_form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $edit_form -> submit -> setLabel('Save');
        $edit_form -> setTitle('Edit Email Notification');

        // Get selected form to edit
        $table = $this -> getDbTable();
        $this -> view -> notification = $notification = Engine_Api::_() -> getItem('yndynamicform_notification', $this -> _getParam('id', 0));

        // Set value of selected form to editForm
        $values = $notification -> toArray();

        // Populate value for edit form
        $this -> view -> edit_form = $edit_form -> populate($values);

        // Check post
        if ($this -> getRequest() -> isPost() && $edit_form -> isValid($this -> getRequest() -> getPost())) {
            // We will add the new form
            $values = $edit_form -> getValues();
            // Prepare data
            $params = $this ->_getAllParams();
            $values['conditional_logic'] = json_encode($params['conditional_logic']);
            $values['conditional_show'] = $params['conditional_show'];
            $values['conditional_scope'] = $params['conditional_scope'];

            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $notification -> setFromArray($values);
                $notification -> modified_date = date("Y:m:d H:i:s");
                $notification -> save();
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => Array(Zend_Registry::get('Zend_Translate') -> _('New notification has been added.'))
            ));
        }
        // TODO: Implement conditional logic for notification edit.

        // Output
        $this -> renderScript('admin-notification/edit.tpl');
    }

    public function deleteAction()
    {
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $this -> view -> notification_id = $id;

        //Check post
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                $notification = Engine_Api::_() -> getItem('yndynamicform_notification', $id);

                //delete notification in database
                $notification -> delete();
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => Array(Zend_Registry::get('Zend_Translate') -> _('Your notification entry has been deleted.'))
            ));
        }
        // TODO: Implement conditional logic for notification delete.

        //Out put
        $this -> renderScript('admin-notification/delete.tpl');
    }

    public function suggestAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) {
            $data = null;
        } else {
            $data = array();
            $table = Engine_Api::_()->getItemTable('user');


            $select = Engine_Api::_()->getDbtable('users', 'user')->select();

            if( $this->_getParam('includeSelf', false) ) {
                $data[] = array(
                    'type' => 'user',
                    'id' => $viewer->getIdentity(),
                    'guid' => $viewer->getGuid(),
                    'label' => $viewer->getTitle() . ' (you)',
                    'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
                    'url' => $viewer->getHref(),
                );
            }

            if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
                $select->limit($limit);
            }

            if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
                $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
            }

            $ids = array();
            foreach( $select->getTable()->fetchAll($select) as $user ) {
                $data[] = array(
                    'type' => 'user',
                    'id' => $user->getIdentity(),
                    'guid' => $user->getGuid(),
                    'label' => $user->getTitle(),
                    'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
                    'url' => $user->getHref(),
                );
                $ids[] = $user->getIdentity();
                $user_data[$user->getIdentity()] = $user->getTitle();
            }
        }

        if( $this->_getParam('sendNow', true) ) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    public function sortAction() {
        $table = $this -> getDbTable();
        $notifications = $table -> getNotifications(array('form_id' => $this -> getRequest() -> getParam('form_id')));
        $order = explode(',', $this -> getRequest() -> getParam('order'));
        foreach ($order as $i => $item) {
            $notification_id = substr($item, strrpos($item, '_') + 1);
            foreach ($notifications as $notification) {
                if ($notification -> notification_id == $notification_id) {
                    $notification -> order = $i;
                    $notification -> save();
                }
            }
        }
    }

    public function enableAction()
    {
        //Get params
        $notification_id = $this -> _getParam('notification_id');
        $enable = $this -> _getParam('enable');

        //Get notification need to set enabled or unset enabled
        $notification = Engine_Api::_() -> getItem('yndynamicform_notification', $notification_id);
        $notification -> enable = $enable;
        $notification -> save();
    }
}