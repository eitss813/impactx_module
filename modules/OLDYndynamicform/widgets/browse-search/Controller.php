<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 6:32 PM
 */
class Yndynamicform_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this -> view -> form = $form = new Yndynamicform_Form_Search();


        // Process form
        $request = Zend_Controller_Front::getInstance() -> getRequest();
        if ($request -> getModuleName() == 'yndynamicform') {
            if ($request -> getActionName() == 'my-moderated-forms') {
                $form -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'my-moderated-forms'), 'yndynamicform_general', true));
            } else {
                $form -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'list-forms'), 'yndynamicform_general', true));
            }
            $params = $request -> getParams();
        }
        else {
            $params = array();
        }

        $form -> populate($params);
    }
}