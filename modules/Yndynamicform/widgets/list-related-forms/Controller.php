<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 2:23 PM
 */
class Yndynamicform_Widget_ListRelatedFormsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if( !Engine_Api::_() -> core() -> hasSubject('yndynamicform_form') ) {
            return $this -> setNoRender();
        }

        $form = Engine_Api::_() -> core() -> getSubject();

        $this -> view -> mode_view = $modeView = 'list';

        //Select forms
        $table = Engine_Api::_() -> getItemTable('yndynamicform_form');
        $params['status'] = 1;
        $params['form_id'] = $form -> getIdentity();
        $params['category_id'] = $form -> category_id;
        $params['limit'] = $this -> _getParam('max', 4);
        $params['privacy'] = 1;

        $select = $table -> getFormsSelect($params);

        $this -> view -> forms = $forms = $table -> fetchAll($select);

        if(!count($forms))
        {
            return $this -> setNoRender();
        }
    }
}