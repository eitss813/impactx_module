<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/26/2016
 * Time: 8:06 PM
 */
class Sitepage_View_Helper_FieldTextEditor extends Fields_View_Helper_FieldAbstract
{
    public function fieldTextEditor($subject, $field, $value)
    {
        return $field->config['body'];
    }
}