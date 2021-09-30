<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/3/2016
 * Time: 9:21 AM
 */
class Yndynamicform_AdminConfirmationController extends Core_Controller_Action_Admin
{
    public function getDbTable()
    {
        return Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform');
    }

    public function createAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $new_form = $this -> view -> new_form = new Yndynamicform_Form_Admin_EditForm_Confirmation();
        $new_form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $table = $this -> getDbTable();
        $form_id = $this -> _getParam('form_id');

        // Check post
        if ($this -> getRequest() -> isPost() && $new_form -> isValid($this -> getRequest() -> getPost())) {
            // We will add the new form
            $values = $new_form -> getValues();
            // Prepare data
            $params = $this ->_getAllParams();

            $values['conditional_logic'] = json_encode($params['conditional_logic']);
            $values['conditional_show'] = $params['conditional_show'];
            $values['conditional_scope'] = $params['conditional_scope'];

            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $confirmation = $table -> createRow();
                $confirmation -> setFromArray($values);
                $confirmation -> form_id = $form_id;

                $confirmation -> save();
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => Array(Zend_Registry::get('Zend_Translate') -> _('New confirmation has been added.'))
            ));
        }

        // TODO: Implement conditional logic for confirmation create.

        // Output
        $this -> renderScript('admin-confirmation/create.tpl');
    }

    public function editAction()
    {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $edit_form = new Yndynamicform_Form_Admin_EditForm_Confirmation();
        $edit_form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $edit_form -> submit -> setLabel('Save');
        $edit_form -> setTitle('Edit Confirmation');
//        $edit_form  ->

        // Get selected form to edit
        $table = $this -> getDbTable();
        $this -> view -> confirmation = $confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $this -> _getParam('id', 0));

        // Set value of selected form to editForm
        $values = $confirmation -> toArray();
        $edit_form -> populate($values);
        $this -> view -> edit_form = $edit_form;

        // Check post
        if ($this -> getRequest() -> isPost() && $edit_form -> isValid($this -> getRequest() -> getPost())) {
            // We will add the new form
            $values = $edit_form -> getValues();
            $params = $this ->_getAllParams();
            $values['conditional_logic'] = json_encode($params['conditional_logic']);
            $values['conditional_show'] = $params['conditional_show'];
            $values['conditional_scope'] = $params['conditional_scope'];
            // Prepare data

            // Begin transaction
            $db = $table -> getAdapter();
            $db -> beginTransaction();

            try {
                $confirmation -> setFromArray($values);

                $confirmation -> save();
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => Array(Zend_Registry::get('Zend_Translate') -> _('New confirmation has been added.'))
            ));
        }
        // TODO: Implement conditional logic for confirmation edit.

        // Output
        $this -> renderScript('admin-confirmation/edit.tpl');
    }

    public function deleteAction()
    {
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $this -> view -> confirmation_id = $id;

        //Check post
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                $confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $id);

                //delete confirmation in database
                $confirmation -> delete();
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => Array(Zend_Registry::get('Zend_Translate') -> _('Your confirmation entry has been deleted.'))
            ));
        }
        // TODO: Implement conditional logic for confirmation delete.

        //Out put
        $this -> renderScript('admin-confirmation/delete.tpl');
    }

    public function sortAction() {
        $table = $this -> getDbTable();
        $confirmations = $table -> getConfirmations(array('form_id' => $this -> getRequest() -> getParam('form_id')));
        $order = explode(',', $this -> getRequest() -> getParam('order'));
        foreach ($order as $i => $item) {
            $confirmation_id = substr($item, strrpos($item, '_') + 1);
            foreach ($confirmations as $confirmation) {
                if ($confirmation -> confirmation_id == $confirmation_id) {
                    $confirmation -> order = $i;
                    $confirmation -> save();
                }
            }
        }
    }

    public function enableAction()
    {
        //Get params
        $confirmation_id = $this -> _getParam('confirmation_id');
        $enable = $this -> _getParam('enable');

        //Get confirmation need to set enabled or unset enabled
        $confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $confirmation_id);
        $confirmation -> enable = $enable;
        $confirmation -> save();
    }
}