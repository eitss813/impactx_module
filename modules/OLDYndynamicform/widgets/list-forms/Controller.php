<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/1/2016
 * Time: 9:50 AM
 */
class Yndynamicform_Widget_ListFormsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $modeView = 'grid';
        $module = $request -> getModuleName();
        $action = $request -> getActionName();

        $form = new Yndynamicform_Form_Search();
        if($form -> isValid($request -> getParams()))
        {
            $params = $form -> getValues();
        }
        else
            $params = array();

        // Search if form is enabled
        $params['status'] = 1;
        $params['privacy'] = 1;

        if($module == 'yndynamicform' && $action == 'list-forms') {
            $modeView = 'list';
        } else if($module == 'yndynamicform' && $action == 'my-moderated-forms') {
            $modeView = 'list';
            $params['moderated_forms'] = 1;
            $params['status'] = null;
            $params['privacy'] = null;
        } else if($module == 'yndynamicform' && $action == 'index') {
             $this->getElement()->setTitle('Lastest Forms');
        }

        $this->view->mode_view = $modeView;

        // Get paginator
        $paginator = Engine_Api::_()->getDbTable('forms', 'yndynamicform')->getFormsPaginator($params);
        $paginator->setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
        $paginator->setCurrentPageNumber($request -> getParam('page', 1));
        $this->view->paginator = $paginator;
    }
}