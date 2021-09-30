<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 9/5/2016
 * Time: 2:11 PM
 */
class Sitepage_View_Helper_FieldCheckbox extends Fields_View_Helper_FieldAbstract
{
    public function fieldCheckbox($subject, $field, $value)
    {
        $isChecked = $value->getValue() ? 'Yes' : 'No';
        return Zend_Registry::get('Zend_View') -> translate($isChecked);
    }
}