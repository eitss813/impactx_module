<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 2:29 PM
 */
class Yndynamicform_AdminManageController extends Core_Controller_Action_Admin
{
    public function init() {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_forms');
    }

    public function getDbTable() {
        return Engine_Api::_() -> getDbTable('forms', 'yndynamicform');
    }

    public function indexAction()
    {
        if ($this -> getRequest() -> isPost()) {
            $values = $this -> getRequest() -> getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $form = Engine_Api::_() -> getItem('yndynamicform_form', $value);
                    if ($form)
                        $form -> delete();
                }
            }
        }

        $params = $this -> _getAllParams();
        $params['valid_form'] = true;
        $table = $this -> getDbTable();
        $this -> view -> paginator = $paginator = $table -> getFormsPaginator($params);

        $this -> view -> paginator -> setItemCountPerPage(10);
        $page = $this -> _getParam('page', 1);
        $this -> view -> paginator -> setCurrentPageNumber($page);
        // Form Search Form
        $this -> view -> form = $form = new Yndynamicform_Form_Admin_Search();

        $form -> populate($params);
        $formValues = $form -> getValues();
        if (isset($params['fieldOrder'])) {
            $formValues['fieldOrder'] = $params['fieldOrder'];
        }
        if (isset($params['direction'])) {
            $formValues['direction'] = $params['direction'];
        }
        $this -> view -> params = $formValues;
    }

    public function multiDeleteConfirmAction()
    {
        $this -> _helper -> layout -> setLayout('default-simple');

        $require_email = $this -> _getParam('require_email');

        if ($this -> getRequest() -> isPost()) {
            $this -> view -> closeSmoothbox = true;
        }

        // Ouput
        $this -> renderScript('admin-manage/multidelete-confirm.tpl');
    }
}