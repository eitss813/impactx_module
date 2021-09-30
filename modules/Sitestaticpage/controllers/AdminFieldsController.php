<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_AdminFieldsController extends Fields_Controller_AdminAbstract {

    protected $_fieldType = 'sitestaticpage_page';
    protected $_requireProfileType = true;

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_questions');
        include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license2.php';
    }

    // ACTION FOR PROFILE TYPE CREATION
    public function typeCreateAction() {
        include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license2.php';
        //GENERATE FORM
        $this->view->form = $form = new Sitestaticpage_Form_Admin_Type();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            if (!empty($_POST['email'])) {
                $emailArray = explode(',', $_POST['email']);
                $errorMessage = array();
                $validatorEmail = new Zend_Validate_EmailAddress();
                $validatorEmail->getHostnameValidator()->setValidateTld(false);
                foreach ($emailArray as $val) {
                    if (!$validatorEmail->isValid(trim($val))) {
                        $errorMessage = "Please enter valid email address";
                        break;
                    }
                }
                if (!empty($errorMessage)) {
                    $form->addError($errorMessage);
                    return;
                }
            }
            $db = Engine_Db_Table::getDefaultAdapter();
            $option_id = $db->select('option_id')->from('engine4_sitestaticpage_page_fields_options')
                            ->order('option_id DESC')->limit(1)
                            ->query()->fetchColumn();
            $this->updateMail($_POST['email'], $_POST['form_heading'], $_POST['form_description'], $_POST['button_text'], $option_id);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
            ));
        }
    }

    // ACTION FOR PROFILE TYPE EDIT
    public function typeEditAction() {

        $option_id = $this->_getParam('option_id');
        include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license2.php';
        $option_table = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'options');
        //GENERATE FORM
        $this->view->form = $form = new Sitestaticpage_Form_Admin_Type();
        $form->setTitle('Edit Form Meta-data');
        $form->submit->setLabel('Save');
        $db = Engine_Db_Table::getDefaultAdapter();
        $default_email = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact');

        $select = $option_table->select()->from($option_table->info('name'), array('*'))
                ->where('option_id = ?', $option_id);
        $values = $option_table->fetchRow($select);
        $value = $values->toArray();
        if (empty($values['button_text']))
            $values['button_text'] = 'Submit';

        if (!$this->getRequest()->isPost()) {
            $form->form_heading->setValue($values['form_heading']);
            $form->form_description->setValue($values['form_description']);
            
            if(isset($form->recaptcha))
                $form->recaptcha->setValue($values['recaptcha']);
            
            $form->label->setValue($values['label']);
            if (empty($values['email']))
                $form->email->setValue($default_email);
            else
                $form->email->setValue($values['email']);
            $form->button_text->setValue($values['button_text']);
            return;
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            if (!empty($_POST['email'])) {
                $emailArray = explode(',', $_POST['email']);
                $errorMessage = array();
                $validatorEmail = new Zend_Validate_EmailAddress();
                $validatorEmail->getHostnameValidator()->setValidateTld(false);
                foreach ($emailArray as $val) {
                    if (!$validatorEmail->isValid(trim($val))) {
                        $errorMessage = "Please enter valid email address";
                        break;
                    }
                }
                if (!empty($errorMessage)) {
                    $form->addError($errorMessage);
                    return;
                }
            }
            $this->updateMail($_POST['email'], $_POST['form_heading'], $_POST['form_description'], $_POST['button_text'], $option_id);


            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
            ));
        }
    }

    //ACTION FOR PROFILE FIELD CREATION
    public function fieldCreateAction() {
        parent::fieldCreateAction();

        //GENERATE FORM
        $form = $this->view->form;

        if ($form) {
            $form->setTitle('Add Form Question');
            $form->removeElement('search');
            $form->addElement('hidden', 'search', array('value' => 0, 'order' => 1000));
            $form->removeElement('display');
            $form->addElement('hidden', 'display', array('value' => 0, 'order' => 10001));
            $form->removeElement('show');
            $form->addElement('hidden', 'show', array('value' => 0));
            $form->removeElement('error');
            $form->removeElement('style');
        }
        if ($this->getRequest()->getPost()) {
            $formMapTable = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'maps');
            $formMapTable->update(array('field_id' => 1), array('option_id = ?' => 0, 'field_id = ?' => 0, '`order` != ?' => 1));
        }
    }

    //ACTION FOR PROFILE FIELD EDIT
    public function fieldEditAction() {

        parent::fieldEditAction();

        //GENERATE FORM
        $form = $this->view->form;

        if ($form) {
            $form->setTitle('Edit Form Question');
            $form->removeElement('search');
            $form->removeElement('display');
            $form->removeElement('show');
            $form->addElement('hidden', 'show', array('value' => 0));
            $form->removeElement('error');
            $form->removeElement('style');
        }
    }

    //ACTION FOR HEADING CREATION
    public function headingCreateAction() {
        parent::headingCreateAction();

        //GENERATE FORM
        $form = $this->view->form;
        if ($form) {
            $form->removeElement('show');
            $form->addElement('hidden', 'show', array('value' => 0 ,'order' => 986));

            $form->removeElement('display');
            $form->addElement('hidden', 'display', array('value' => 1, 'order' => 987));
        }
    }

    //ACTION FOR HEADING EDIT
    public function headingEditAction() {
        parent::headingEditAction();

        //GENERATE FORM
        $form = $this->view->form;
        if ($form) {
            $form->removeElement('show');
            $form->addElement('hidden', 'show', array('value' => 0, 'order' => '986'));

            $form->removeElement('display');
            $form->addElement('hidden', 'display', array('value' => 1, 'order' => '987'));
        }
    }

    //ACTION FOR PROFILE TYPE DELETE
    public function typeDeleteAction() {

        $option_id = $this->_getParam('option_id');

        if (!empty($option_id)) {

            //DELETE FIELD ENTRIES IF EXISTS
            $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'maps');
            $select = $fieldmapsTable->select()->where('option_id =?', $option_id);
            $metaData = $fieldmapsTable->fetchAll($select)->toArray();
            if (!empty($metaData)) {
                foreach ($metaData as $key => $child_ids) {
                    $child_id = $child_ids['child_id'];

                    //DELETE FIELD ENTRIES IF EXISTS
                    $fieldmetaTable = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'meta');
                    $fieldmetaTable->delete(array(
                        'field_id = ?' => $child_id,
                    ));
                }
            }
            $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'maps');
            $fieldmapsTable->delete(array(
                'option_id = ?' => $option_id,
            ));
        }
        parent::typeDeleteAction();
    }

    public function updateMail($email, $heading, $description, $button_text, $option_id) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->query('UPDATE `engine4_sitestaticpage_page_fields_options` SET `email` = "' . $email . '"  WHERE `option_id` = "' . $option_id . '";');
        $db->query('UPDATE `engine4_sitestaticpage_page_fields_options` SET `form_heading` = "' . $heading . '"  WHERE `option_id` = "' . $option_id . '";');
        $db->query('UPDATE `engine4_sitestaticpage_page_fields_options` SET `form_description` = "' . $description . '"  WHERE `option_id` = "' . $option_id . '";');
        $db->query('UPDATE `engine4_sitestaticpage_page_fields_options` SET `button_text` = "' . $button_text . '"  WHERE `option_id` = "' . $option_id . '";');
        if (isset($_POST['recaptcha'])) {
            $db->query('UPDATE `engine4_sitestaticpage_page_fields_options` SET `recaptcha` = "' . $_POST['recaptcha'] . '"  WHERE `option_id` = "' . $option_id . '";');
        }
    }

    public function displayUserDataAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_questions');

        $form_id = $this->_getParam('form_id');
        $user_table = Engine_Api::_()->getDbtable('users', 'user');

        $table_values = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'values');
        $table_options = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'options');
        $table_meta = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'meta');

        if (isset($_POST['search'])) {
            if (!empty($_POST['user_name'])) {
                $this->view->user_name = $_POST['user_name'];
                $user_id = $user_table->select()->from($user_table->info('name'), 'user_id')
                                ->where('username LIKE ?', $_POST['user_name'])
                                ->query()->fetchColumn();
                if (empty($user_id)) {
                    $this->view->error_message = 'The Entered user does not exist.';
                    return;
                }
            }
        }
        $values_form = $table_values->select()
                ->where('form_id =?', $form_id)
                ->order('member_id ASC');

        if (!empty($user_id))
            $values_form = $values_form->where('member_id =?', $user_id);

        $form_data = Zend_Paginator::factory($values_form);
        $count_form_data = count($form_data);

        if (!empty($user_id) && empty($count_form_data)) {
            $this->view->error_message = 'The user has not submitted any data for this Form.';
        } elseif (empty($count_form_data)) {
            $this->view->error_message = 'No one has submitted any data for this form.';
        }
        $page = $this->_getParam('page', 1);
        $this->view->paginator = $form_data;
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);

        $last_member_id = 0;
        foreach ($form_data as $value) {

            $field_select = $table_meta->select()->from($table_meta->info('name'), array('label', 'type'))
                    ->where('field_id =?', $value->field_id);
            $field = $table_meta->fetchRow($field_select);

            /* Code For Combining all the Fields with Username */

            if (!empty($last_member_id) && $value->member_id != $last_member_id) {
                $content .= $this->_buildLastContents($lastContents, $lastUserName);
                $lastContents = '';
                $user_name = $user_table->select()->from($user_table->info('name'), 'username')
                                ->where('user_id =?', $value->member_id)
                                ->query()->fetchColumn();
                $last_member_id = $value->member_id;
                $lastUserName = $user_name;
            } elseif ($value->member_id != $last_member_id) {
                $user_name = $user_table->select()->from($user_table->info('name'), 'username')
                                ->where('user_id =?', $value->member_id)
                                ->query()->fetchColumn();
                $last_member_id = $value->member_id;
                $lastUserName = $user_name;
            }

            if ($field->type == 'heading') {
                // Heading
                if (!empty($lastContents)) {
                    $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
                    $lastContents = '';
                }
                $lastHeadingTitle = $this->view->translate($field->label);
            } else {
                if (!empty($value->value)) {
                    if ($field->type == 'select' || $field->type == 'multiselect' || $field->type == 'radio' || $field->type == 'multi_checkbox') {

                        $field_value = $table_options->select()->from($table_options->info('name'), 'label')
                                        ->where('option_id =?', $value->value)
                                        ->query()->fetchColumn();
                    } else {
                        $field_value = $value->value;
                    }
                    // Normal fields
                    $label = $this->view->translate($field->label);
                    $lastContents .= <<<EOF

  <li style="list-style-type:none" data-field-id={$value->field_id}>
    <span>
      {$label}
    </span>
      
    <span>
      {$field_value}
    </span>
  </li>
EOF;
                }
            }
        }
        if (!empty($lastContents)) {
            $content .= $this->_buildLastContents($lastContents, $lastUserName);
        }
        $this->view->content = $content;
    }

    protected function _buildLastContents($content, $title, $username = null) {
        if (!$title) {
            return '<ul>' . $content . '</ul>';
        }
        if (isset($username) && !empty($username)) {
            return <<<EOF
        <div class="profile_fields">
          <h4>
            <span class='bold'>User : {$title}</span>
          </h4>
           <br />
          <h3>
            <span>{$title}</span>
          </h3>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
        } else {
            return <<<EOF
        <div class="profile_fields">
          <h4>
            <span class='bold'>User : {$title}</span>
          </h4>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
        }
    }

}
