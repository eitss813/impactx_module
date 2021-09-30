<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 2:48 PM
 */
class Yndynamicform_Widget_FormDetailsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('yndynamicform_form')) {
            $this -> setNoRender();
        }

        // Get subject
        $form = Engine_Api::_() -> core() -> getSubject();
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $this -> view -> form = $form;
        $this -> view -> viewer = $viewer;
        $this -> view -> isModerator = $isModerator = $form -> isModerator($viewer);
    }
}