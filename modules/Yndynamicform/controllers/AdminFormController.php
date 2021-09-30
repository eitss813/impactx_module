<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/3/2016
 * Time: 9:18 AM
 */
class Yndynamicform_AdminFormController extends Core_Controller_Action_Admin
{
    public function init() {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_forms');
    }

    public function getDbTable()
    {
        return Engine_Api::_()->getDbTable('forms', 'yndynamicform');
    }

    public function createAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $new_form = $this -> view -> form = new Yndynamicform_Form_Admin_NewForm();
        $new_form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $table = $this -> getDbTable();

        // Check post
        if ($this -> getRequest() -> isPost() && $new_form -> isValid($this -> getRequest() -> getPost())) {
            // We will add the new form
            $values = $new_form -> getValues();
            if (strlen($values['title']) > 128) {
                $new_form -> title -> addError('Value must be less than 128 characters.');
                return;
            }
            $user = Engine_Api::_() -> user() -> getViewer();

            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $form = $table -> createRow();

                $form -> setFromArray($values);
                $form -> user_id = $user -> getIdentity();

                $optionId = Engine_Api::_()->getApi('core', 'Yndynamicform')->typeCreate($form->title);
                if (!empty($values['photo'])) {
                    $form->setPhoto($new_form->photo);
                }
                $form->option_id = $optionId;
                $form->privacy = 3; // 3 mean everyone can see this form

                $form -> save();

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                foreach ($roles as $i => $role)
                {
                    $auth->setAllowed($form, $role, 'view', 1);
                    $auth->setAllowed($form, $role, 'comment', 1);
                    $auth->setAllowed($form, $role, 'submission', 1);
                }

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'yndynamicform',
                    'controller' => 'form',
                    'action' => 'settings',
                    'form_id' => $form -> getIdentity()
                ), 'admin_default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('New form has been successfully added.'))
            ));
        }

        // Output
        $this -> renderScript('admin-form/create.tpl');
    }
    public function mainInfoAction()
    {
        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $this -> _getParam('form_id'));

        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                'messages' => Array($this -> view -> message)
            ));
        }

        $this -> view -> form = $form;

        // Get edit form
        $this -> view -> editform = $editform = new Yndynamicform_Form_Admin_EditForm_MainInfo();
        $value = $form -> toArray();
        $editform -> populate($value);

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$editform -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        $value = $editform -> getValues();

        if (strlen($value['title']) > 128) {
            $editform -> title -> addError('Value must be less than 128 characters.');
            return;
        }

        $table = Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
        $db = $table -> getAdapter();

        // Begin transaction
        $db -> beginTransaction();
        try {
            $form -> setFromArray($value);
            if (!empty($value['photo'])) {
                $form->setPhoto($editform->photo);
            }
            $form -> modified_date = date('Y-m-d H:i:s');

            $form -> save();
            $db -> commit();
        } catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        $editform -> addNotice('Your changes have been saved.');
    }
    public function mainInfobackupAction()
    {
        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $this -> _getParam('form_id'));

        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                'messages' => Array($this -> view -> message)
            ));
        }

        $this -> view -> form = $form;

        // Get edit form
        $this -> view -> editform = $editform = new Yndynamicform_Form_Admin_EditForm_MainInfo();
        $value = $form -> toArray();
        $editform -> populate($value);

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$editform -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        $value = $editform -> getValues();

        if (strlen($value['title']) > 128) {
            $editform -> title -> addError('Value must be less than 128 characters.');
            return;
        }

        $table = Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
        $db = $table -> getAdapter();

        // Begin transaction
        $db -> beginTransaction();
        try {
            $form -> setFromArray($value);
            if (!empty($value['photo'])) {
                $form->setPhoto($editform->photo);
            }
            $form -> modified_date = date('Y-m-d H:i:s');

            $form -> save();
            $db -> commit();
        } catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        $editform -> addNotice('Your changes have been saved.');
    }

    public function settingsAction()
    {
        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $this -> _getParam('form_id'));

        // Check if form is still alive
        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        $this -> view -> form = $form;

        // Get edit form
        $this -> view -> editform = $editform = new Yndynamicform_Form_Admin_EditForm_FormSettings(array('form' => $form -> getIdentity()));

        // Populate form
        $values = $form -> toArray();

        $values = array_filter($values, function ($val){
            return !is_null($val);
        });
        $editform -> populate($values);
        $editform -> privacy -> setValue(array(1 & $form -> privacy,2 & $form -> privacy));


        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$editform -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        // Get all values from edit form
        $values = $editform -> getValues();
        $params = $this ->_getAllParams();

        $values['conditional_logic'] = json_encode($params['conditional_logic']);
        $values['conditional_scope'] = $params['conditional_scope'];
        $values['conditional_show'] = $params['conditional_show'];

        // Validate valid form from and to
        if (!empty($values['valid_from_date']) && !empty($values['valid_to_date']) && !$values['unlimited_time']
            && (strtotime($values['valid_from_date']) > strtotime($values['valid_to_date']))) {
            $editform -> valid_to_date -> addError('Value must be after Valid Time From.');
            return;
        }

        if (!empty($values['unlimited_time']) && $values['unlimited_time']) {
            $values['valid_to_date'] = null;
        } else if (empty($values['valid_to_date'])) {
            $editform->valid_to_date->addError('Value is required and can\'t be empty');
            return;
        }
        // TODO: Implement conditional logic for form settings.

        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        try {
            /*
             * If user check both guest and registered user. Privacy is 3
             * else we save first value (guest or registered)
             */
            if (count($values['privacy']) > 1) {
                $values['privacy'] = 3;
            } elseif (!empty($values['privacy'])) {
                $values['privacy'] = $values['privacy'][0];
            }
            $form -> setFromArray($values);

            if (empty($values['valid_from_date'])) {
                $form -> valid_from_date = null;
            }

            if (empty($values['valid_to_date'])) {
                $form -> valid_to_date = null;
            }

            $form -> modified_date = date('Y:m:d H:i:s');

            $form -> save();
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        $this -> view -> message = 'Your changes have been saved.';
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'form', 'action' => 'settings', 'form_id' => $form->getIdentity()), 'admin_default', true),
            'messages' => Array($this -> view -> message)
        ));
    }

    public function confirmationAction()
    {
        $form_id = $this -> _getParam('form_id', 0);

        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

        // Check if form is still alive
        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        $this -> view -> form = $form;

        // Get all of this form confirmations
        $table = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform');
        $this -> view -> confirmations = $table -> getConfirmations(array('form_id' => $form_id));

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $this -> view -> message = 'Your changes have been saved.';
    }

    public function notificationAction()
    {
        $form_id = $this -> _getParam('form_id', 0);

        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

        // Check if form is still alive
        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'manage'), 'admin_default', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        $this -> view -> form = $form;

        // Get all of this form notifications
        $table = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform');
        $this -> view -> notifications = $table -> getNotifications(array('form_id' => $form_id));

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $this -> view -> message = 'Your changes have been saved.';
    }

    public function moderatorsAction()
    {
        $form_id = $this -> _getParam('form_id', 0);

        // Check if selected form is valid
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

        // Check if form is still alive
        if (!$form) {
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The form is not available anymore.');
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'admin-manage' , 'action' => 'manage'), 'default', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        $this -> view -> form = $form;

        // Prepare params to get form's moderators
        $ids = $form -> getAllModeratorsID();
        $this -> view -> toObjects = Engine_Api::_() -> getItemMulti('user', $ids);
        $this -> view -> toValues = implode(',', $ids);

        // Check post/form
        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        // Get new moderator
        $paramToValues = $this -> _getParam('toValues');
        $newToValues = explode(',', $paramToValues);
        $newToValues = array_filter($newToValues);

        // Get table
        $table = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');

        // Add new moderatos
        if (!empty($newToValues)) {
            $newModerator = array_diff($newToValues, $ids);
            $db = $table -> getAdapter();
            foreach ($newModerator as $k => $id)
            {
                try {
                    $newItem = $table -> createRow();
                    $newItem -> form_id = $form_id;
                    $newItem -> moderator_id = $id;

                    $newItem -> save();
                    $db -> commit();
                } catch (Exception $e) {
                    $db -> rollBack();
                    throw $e;
                }

            }
        }

        // Remove removed moderatos if
        $removeModerator = array_diff($ids, $newToValues);
        $db = $table -> getAdapter();
        if (!empty($removeModerator)) {
            foreach ($removeModerator as $id) {
                try {
                    $item = $table->getModerator($id);
                    if ($item) {
                        $item->delete();
                    }
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }

            }
        }

        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.');
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'yndynamicform', 'controller' => 'form' , 'action' => 'moderators', 'form_id' => $form_id), 'admin_default', true),
            'messages' => Array($this -> view -> message)
        ));
    }

    public function deleteAction() {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $this -> view -> form_id = $id;

        // Check post
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $id);
                if ($yndform)
                    $yndform -> delete();

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

            return $this -> _forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The form is deleted successfully.'))
            ));
        }

        // Output
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> renderScript('admin-form/delete.tpl');
    }

    public function cloneAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('form_id');
        $this -> view -> form_id = $id;
        // Check post
        if ($this -> getRequest() -> isPost()) {
            $table = Engine_Api::_()->getDbTable('forms', 'yndynamicform');
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                // Get form will be generated
                $form = Engine_Api::_() -> getItem('yndynamicform_form', $id);
                $option_id = $form->option_id;
                $values = $form -> toArray();
                unset($values['creation_date'], $values['modified_date'],$values['form_id'], $values['total_entries'],$values['view_count'],$values['comment_count'],$values['like_count'],$values['valid_from_date'],$values['valid_to_date'],$values['unlimited_time'],$values['status']);
                // Generate form
                $new_form = $table -> createRow();
                $new_form -> setFromArray($values);
                $new_form -> title = $form -> getTitle().' (' . $this->view->translate("Clone") . ')';
                $new_option_id = Engine_Api::_()->getApi('core', 'Yndynamicform')->typeCreate($new_form->title);
                $new_form->option_id = $new_option_id;
                $new_form -> save();

                // clone fields
                $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
                $count = 1;
                foreach ($fieldMaps as $item)
                {
                    $field = $item->getChild();
                    $values = $field->toArray();
                    unset($values['field_id']);
                    unset($values['config']);
                    $values = array_merge($field->config, $values);
                    $new_field = Engine_Api::_()->fields()->createField('yndynamicform_entry', array_merge(array(
                        'option_id' => $new_option_id,
                    ), $values));
                    // clone options
                    $old_options = $field->getOptions();
                    if (!empty($old_options)) {
                        foreach ($old_options as $option) {
                            // Create new option
                            Engine_Api::_()->fields()->createOption('yndynamicform_entry', $new_field, array(
                                'label' => $option->label,
                            ));
                        }
                    }
                    // update map order
                    $map = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry') -> getRowMatching('child_id', $new_field -> field_id);
                    $map -> order = $count;
                    $count++;
                    $map -> save();
                }

                // clone other data
                $new_form_id = $new_form->getIdentity();
                $modTable = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');
                $notiTable = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform');
                $confTable = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform');

                $preModerators = $modTable->fetchAll($modTable->select()->where('form_id = ?', $id));
                foreach ($preModerators as $item) {
                    $values = $item->toArray();
                    unset($values['id']);
                    $newItem = $modTable -> createRow();
                    $newItem -> setFromArray($values);
                    $newItem -> form_id = $new_form_id;
                    $newItem -> save();
                }

                $preNotifications = $notiTable->fetchAll($notiTable->select()->where('form_id = ?', $id));
                foreach ($preNotifications as $item) {
                    $values = $item->toArray();
                    unset($values['notification_id']);
                    $newItem = $notiTable -> createRow();
                    $newItem -> setFromArray($values);
                    $newItem -> form_id = $new_form_id;
                    $newItem -> save();
                }

                $preConfirmations = $confTable->fetchAll($confTable->select()->where('form_id = ?', $id));
                foreach ($preConfirmations as $item) {
                    $values = $item->toArray();
                    unset($values['confirmation_id']);
                    $newItem = $confTable -> createRow();
                    $newItem -> setFromArray($values);
                    $newItem -> form_id = $new_form_id;
                    $newItem -> save();
                }

                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                foreach ($roles as $i => $role)
                {
                    $auth->setAllowed($new_form, $role, 'view', 1);
                    $auth->setAllowed($new_form, $role, 'comment', 1);
                    $auth->setAllowed($new_form, $role, 'submission', 1);
                }

                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'module' => 'yndynamicform',
                    'controller' => 'form',
                    'action' => 'main-info',
                    'form_id' => $new_form -> getIdentity()
                ), 'admin_default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The form cloned successfully.'))
            ));
        }

        // Output
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> renderScript('admin-form/clone.tpl');
    }
}