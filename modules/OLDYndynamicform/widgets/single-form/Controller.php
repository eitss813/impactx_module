<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/1/2016
 * Time: 3:56 PM
 */
class Yndynamicform_Widget_SingleFormController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Check if no form is selected
        $form_id = (int)$this -> _getParam('form_id', 0);
        if (!$form_id) {
            return $this->setNoRender();
        }

        // Check if form is not available
        $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
        if (empty($form)) {
            return $this->setNoRender();
        }

        // Check permission with this form
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity()) {
            if ($form -> privacy == 1) {
                $this -> setNoRender();
            }
        } else {
            if ($form -> privacy == 2) {
                $this -> setNoRender();
            }
        }

        // Check if this form is not enable
        if ($form->enable === 0) {
            return $this->setNoRender();
        }

        if (isset($form->category_id)) {
            $this->view->category = $category = Engine_Api::_()->getItem('yndynamicform_category', $form->category_id);
        }
        $this -> view -> form = $form;
    }
}