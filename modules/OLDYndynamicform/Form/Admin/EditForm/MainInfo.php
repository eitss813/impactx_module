<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/1/2016
 * Time: 4:56 PM
 */
class Yndynamicform_Form_Admin_EditForm_MainInfo extends Yndynamicform_Form_Admin_NewForm
{
    public function init()
    {
        parent::init();

        $this->submit->setLabel('Update');
    }

}