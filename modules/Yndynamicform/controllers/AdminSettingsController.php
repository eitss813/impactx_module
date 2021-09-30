<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 4:59 PM
 */
class Yndynamicform_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core')
             -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_settings');
        $this -> view -> setting_form  = $setting_form = new Yndynamicform_Form_Admin_Settings_Global();

        if( $this -> getRequest() -> isPost() && $setting_form -> isValid($this -> _getAllParams()) )
        {
            $values = $setting_form -> getValues();
            foreach ($values as $key => $value){
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $setting_form -> addNotice(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'));
        }
    }

    public function levelAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core')
            -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_level');

        // Get level id
        if( null !== ($id = $this -> _getParam('id')) ) {
            $level = Engine_Api::_() -> getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
        }

        if( !$level instanceof Authorization_Model_Level ) {
            throw new Engine_Exception('missing level');
        }

        $id = $level -> level_id;

        // Make form
        $this -> view -> level_form = $level_form = new Yndynamicform_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level -> type, array('public')) ),
            'moderator' => ( in_array($level -> type, array('admin', 'moderator')) ),
        ));
        $level_form -> level_id -> setValue($id);

        $permisstionKeys = array('view', 'submission', 'comment', 'max');

        if (Engine_Api::_()->hasModuleBootstrap('yncredit') && $id != 5) {
            $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
            $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");

            $creditElements = array('first_amount', 'first_credit', 'credit', 'max_credit', 'period');
            $select = $typeTbl->select()->where('module = ?', 'yndynamicform')->where('action_type = ?', 'yndynamicform_user_submitted')->limit(1);
            $type = $typeTbl -> fetchRow($select);

            if(empty($type)) {
                $type = $typeTbl->createRow();
                $type->module = 'yndynamicform';
                $type->action_type = 'yndynamicform_user_submitted';
                $type->group = 'earn';
                $type->content = 'Submission %s form';
                $type->credit_default = 5;
                $type->link_params = '{"route":"yndynamicform_general"}';
                $type->save();
            }

            $select = $creditTbl->select()
                ->where("level_id = ? ", $id)
                ->where("type_id = ?", $type->type_id)
                ->limit(1);
            $credit = $creditTbl->fetchRow($select);
            if(empty($credit)) {
                $credit = $creditTbl->createRow();
            }
            else {
                foreach ($creditElements as $ele) {
                    $level_form->getElement($ele)->setValue($credit->$ele);
                }
            }
        }

        // Populate values
        $permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
        $level_form -> populate($permissionsTable -> getAllowed('yndynamicform_form', $id, $permisstionKeys
        ));

        // Check post
        if( !$this -> getRequest() -> isPost() ) {
            return;
        }

        // Check validitiy
        if( !$level_form -> isValid($this -> getRequest() -> getPost()) ) {
            return;
        }

        //TODO: Has not yet save value when config yncredit

        // Process

        $values = $level_form -> getValues();

        $db = $permissionsTable -> getAdapter();
        $db -> beginTransaction();

        try
        {
            if (Engine_Api::_() -> hasModuleBootstrap('yncredit') && $id != 5) {
                $permissionValues = array_slice($values, 0, 4);
                $creditValues = array_slice($values, 4, 9);

                $credit->level_id = $id;
                $credit->type_id = $type->type_id;
                foreach ($creditValues as $index=>$value) {
                    $credit->$index = $value;
                }
                $credit->save();
            }
            else {
                $permissionValues = $values;
            }

            // Set permissions
            foreach ($permissionValues as $key => $value) {
                $permissionsTable->setAllowed('yndynamicform_form', $id, $key, $value);
            }

            // Commit
            $db -> commit();
        }

        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }
        $level_form -> addNotice('Your changes have been saved.');
    }
}