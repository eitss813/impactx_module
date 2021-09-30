<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/3/2016
 * Time: 9:18 AM
 */
class Yndynamicform_AdminEntriesController extends Core_Controller_Action_Admin
{
    public function init() {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_forms');
    }

    public function listAction()
    {
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            $count = 0;
            foreach ($values as $key=>$value) {
                if ($key == 'delete_' . $value)
                {
                    $entry = Engine_Api::_()->getItem('yndynamicform_entry', $value);
                    Engine_Api::_()->getApi('core', 'fields') -> removeItemValues($entry);
                    $count += 1;
                    $entry->delete();
                }
            }
            $form_id = $this -> _getParam('form_id', null);
            if( $form_id && $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id))
            {
                $form->total_entries -= $count;
                if ($form->total_entries < 0)
                    $form->total_entries = 0;
                $form->save();
            }
        }
        // CHECK FOR FORM EXISTENCE
        $id = $this -> _getParam('form_id', null);
        if( !$id || !$form = Engine_Api::_() -> getItem('yndynamicform_form', $id))
        {
            $this -> _helper -> requireSubject()->forward();
            return;
        }

        $page = $this -> _getParam('page', 1);

        $this->view->search_form = $searchForm = new Yndynamicform_Form_Admin_EntrySearch();
        $this->view->hidden_form = new Yndynamicform_Form_Admin_Entries_Delete();


        $params = $this->_getAllParams();

        if(!$searchForm -> isValid($params)) {
            return;
        }

        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');

        $this->view->paginator = $paginator = $entryTable->getEntriesPaginator($params);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        $this -> view -> params = $params;
        $this -> view -> yndform = $form;
    }
}